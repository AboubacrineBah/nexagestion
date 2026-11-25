<?php

namespace App\Http\Controllers;

use App\Events\InvoiceValidated;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\StoreSalesDocumentRequest;
use App\Models\AuditEvent;
use App\Models\Client;
use App\Models\Payment;
use App\Models\SalesDocument;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SalesDocumentController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', SalesDocument::class);
        $documents = SalesDocument::with('client')
            ->where('company_id', Auth::user()->company_id)
            ->when(in_array($request->query('type'), ['quote', 'invoice'], true), fn ($query) => $query->where('type', $request->query('type')))
            ->when($request->filled('q'), fn ($query) => $query->where(function ($sub) use ($request): void {
                $term = '%'.$request->input('q').'%';
                $sub->where('reference', 'like', $term)->orWhereHas('client', fn ($client) => $client->where('name', 'like', $term));
            }))
            ->latest('issued_at')->paginate(12)->withQueryString();

        return view('sales.index', compact('documents'));
    }

    public function create(): View
    {
        $this->authorize('create', SalesDocument::class);
        $clients = Client::where('company_id', Auth::user()->company_id)->orderBy('name')->get();

        return view('sales.create', compact('clients'));
    }

    public function store(StoreSalesDocumentRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $companyId = Auth::user()->company_id;
        $client = Client::where('company_id', $companyId)->findOrFail($data['client_id']);
        $document = DB::transaction(function () use ($data, $companyId, $client): SalesDocument {
            $subtotal = collect($data['lines'])->sum(fn ($line) => round((float) $line['quantity'] * (float) $line['unit_price'], 2));
            $prefix = $data['type'] === 'quote' ? 'DEV' : 'FAC';
            $sequence = SalesDocument::where('company_id', $companyId)->where('type', $data['type'])->whereYear('created_at', now()->year)->lockForUpdate()->count() + 1;
            $document = SalesDocument::create([
                'company_id' => $companyId,
                'client_id' => $client->id,
                'created_by' => Auth::id(),
                'reference' => sprintf('%s-%s-%05d', $prefix, now()->format('Y'), $sequence),
                'type' => $data['type'], 'status' => $data['type'] === 'quote' ? 'draft' : 'unpaid',
                'issued_at' => $data['issued_at'], 'due_at' => $data['due_at'] ?? null,
                'currency' => Auth::user()->company->currency,
                'subtotal' => $subtotal, 'total' => $subtotal, 'notes' => $data['notes'] ?? null,
            ]);
            foreach ($data['lines'] as $line) {
                $document->lines()->create([
                    'product_id' => $line['product_id'] ?? null,
                    'description' => $line['description'],
                    'quantity' => $line['quantity'],
                    'unit_price' => $line['unit_price'],
                    'line_total' => round((float) $line['quantity'] * (float) $line['unit_price'], 2),
                ]);
            }
            if ($data['type'] === 'invoice' && $client->type === 'prospect') {
                $client->update(['type' => 'client']);
            }

            return $document;
        });
        AuditEvent::record('sales_document', $document->id, 'created', ['type' => $document->type, 'reference' => $document->reference]);

        if ($document->type === 'invoice' && $document->status === 'unpaid') {
            event(new InvoiceValidated($document));
        }

        return redirect()->route('sales.show', $document)->with('success', $document->type === 'quote' ? 'Devis enregistré en brouillon.' : 'Facture enregistrée.');
    }

    public function show(SalesDocument $document): View
    {
        abort_unless($document->company_id === Auth::user()->company_id, 404);
        $this->authorize('view', $document);
        $document->load(['client', 'lines', 'payments', 'sourceDocument']);
        $events = AuditEvent::with('user')->where('company_id', Auth::user()->company_id)
            ->where('subject_type', 'sales_document')->where('subject_id', $document->id)->latest()->limit(10)->get();

        return view('sales.show', compact('document', 'events'));
    }

    public function exportPdf(SalesDocument $document)
    {
        abort_unless($document->company_id === Auth::user()->company_id, 404);
        $this->authorize('view', $document);
        $document->load(['client', 'lines']);

        $pdf = Pdf::loadView('pdf.sales-document', compact('document'));

        return $pdf->download($document->reference.'.pdf');
    }

    public function markSent(SalesDocument $document): RedirectResponse
    {
        abort_unless($document->company_id === Auth::user()->company_id, 404);
        $this->authorize('update', $document);
        abort_unless($document->type === 'quote', 422);
        $document->update(['status' => 'sent']);
        AuditEvent::record('sales_document', $document->id, 'sent');

        return back()->with('success', 'Devis marqué comme envoyé.');
    }

    public function accept(SalesDocument $document): RedirectResponse
    {
        abort_unless($document->company_id === Auth::user()->company_id, 404);
        $this->authorize('update', $document);
        abort_unless($document->type === 'quote' && $document->status === 'sent', 422);
        DB::transaction(function () use ($document): void {
            $document->update(['status' => 'accepted']);
            $document->client()->where('company_id', Auth::user()->company_id)->update(['type' => 'client']);
        });
        AuditEvent::record('sales_document', $document->id, 'accepted');

        return back()->with('success', 'Devis accepté. Vous pouvez le convertir en facture.');
    }

    public function convertToInvoice(SalesDocument $document): RedirectResponse
    {
        abort_unless($document->company_id === Auth::user()->company_id, 404);
        $this->authorize('create', SalesDocument::class);
        abort_unless($document->type === 'quote' && $document->status === 'accepted', 422);
        $invoice = DB::transaction(function () use ($document): SalesDocument {
            $locked = SalesDocument::where('company_id', Auth::user()->company_id)->lockForUpdate()->findOrFail($document->id);
            abort_if(SalesDocument::where('source_document_id', $locked->id)->exists(), 422, 'Ce devis a déjà été converti.');
            $sequence = SalesDocument::where('company_id', $locked->company_id)->where('type', 'invoice')->whereYear('created_at', now()->year)->lockForUpdate()->count() + 1;
            $invoice = SalesDocument::create([
                'company_id' => $locked->company_id, 'client_id' => $locked->client_id,
                'created_by' => Auth::id(), 'source_document_id' => $locked->id,
                'reference' => sprintf('FAC-%s-%05d', now()->format('Y'), $sequence),
                'type' => 'invoice', 'status' => 'unpaid', 'issued_at' => today(),
                'due_at' => $locked->due_at, 'currency' => $locked->currency,
                'subtotal' => $locked->subtotal, 'total' => $locked->total, 'notes' => $locked->notes,
            ]);
            foreach ($locked->lines as $line) {
                $invoice->lines()->create([
                    'product_id' => $line->product_id,
                    'description' => $line->description,
                    'quantity' => $line->quantity,
                    'unit_price' => $line->unit_price,
                    'line_total' => $line->line_total,
                ]);
            }
            $locked->update(['status' => 'converted']);

            return $invoice;
        });
        AuditEvent::record('sales_document', $invoice->id, 'created_from_quote', ['quote_id' => $document->id]);

        return redirect()->route('sales.show', $invoice)->with('success', 'Le devis a été converti en facture.');
    }

    public function storePayment(StorePaymentRequest $request, SalesDocument $document): RedirectResponse
    {
        abort_unless($document->company_id === Auth::user()->company_id, 404);
        $this->authorize('update', $document);
        abort_unless($document->type === 'invoice', 422);
        DB::transaction(function () use ($request, $document): void {
            $locked = SalesDocument::where('company_id', Auth::user()->company_id)->lockForUpdate()->findOrFail($document->id);
            $alreadyPaid = (float) $locked->payments()->sum('amount');
            $amount = (float) $request->validated('amount');
            if ($amount > (float) $locked->total - $alreadyPaid) {
                throw ValidationException::withMessages(['amount' => 'Le règlement dépasse le solde restant.']);
            }
            Payment::create($request->validated() + [
                'company_id' => $locked->company_id,
                'sales_document_id' => $locked->id,
                'created_by' => Auth::id(),
            ]);
            AuditEvent::record('sales_document', $locked->id, 'payment_recorded', ['amount' => $amount]);
            $newBalance = (float) $locked->total - $alreadyPaid - $amount;
            $locked->update(['status' => $newBalance <= 0 ? 'paid' : 'partial']);
        });

        return back()->with('success', 'Règlement enregistré.');
    }

    public function destroy(SalesDocument $document): RedirectResponse
    {
        abort_unless($document->company_id === Auth::user()->company_id, 404);
        $this->authorize('delete', $document);
        $document->delete();

        return redirect()->route('sales.index')->with('success', 'Document supprimé.');
    }

    public function restore(int $document): RedirectResponse
    {
        $document = SalesDocument::withTrashed()->findOrFail($document);
        abort_unless($document->company_id === Auth::user()->company_id, 404);
        $this->authorize('restore', $document);
        $document->restore();

        return redirect()->route('sales.index')->with('success', 'Document restauré.');
    }

    public function forceDelete(int $document): RedirectResponse
    {
        $document = SalesDocument::withTrashed()->findOrFail($document);
        abort_unless($document->company_id === Auth::user()->company_id, 404);
        $this->authorize('forceDelete', $document);
        $document->forceDelete();

        return redirect()->route('sales.index')->with('success', 'Document définitivement supprimé.');
    }
}
