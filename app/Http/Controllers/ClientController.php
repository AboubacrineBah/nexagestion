<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Models\AuditEvent;
use App\Models\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Client::class);
        $clients = Client::where('company_id', Auth::user()->company_id)
            ->when($request->filled('q'), fn ($query) => $query->where(function ($sub) use ($request): void {
                $term = '%'.$request->input('q').'%';
                $sub->where('name', 'like', $term)->orWhere('email', 'like', $term)->orWhere('phone', 'like', $term);
            }))
            ->when(in_array($request->query('type'), ['prospect', 'client'], true), fn ($query) => $query->where('type', $request->query('type')))
            ->withCount('salesDocuments')->latest()->paginate(12)->withQueryString();

        return view('clients.index', compact('clients'));
    }

    public function store(StoreClientRequest $request): RedirectResponse
    {
        $client = Client::create($request->validated() + ['company_id' => Auth::user()->company_id, 'created_by' => Auth::id()]);
        AuditEvent::record('client', $client->id, 'created');

        return redirect()->route('clients.index')->with('success', 'Contact enregistré.');
    }

    public function update(StoreClientRequest $request, Client $client): RedirectResponse
    {
        abort_unless($client->company_id === Auth::user()->company_id, 404);
        $this->authorize('update', $client);
        $client->update($request->validated());
        AuditEvent::record('client', $client->id, 'updated');

        return redirect()->route('clients.index')->with('success', 'Contact mis à jour.');
    }

    public function destroy(Client $client): RedirectResponse
    {
        abort_unless($client->company_id === Auth::user()->company_id, 404);
        $this->authorize('delete', $client);
        abort_if($client->salesDocuments()->exists(), 422, 'Un contact lié à une pièce commerciale ne peut pas être supprimé.');
        AuditEvent::record('client', $client->id, 'deleted');
        $client->delete();

        return redirect()->route('clients.index')->with('success', 'Contact supprimé.');
    }

    public function restore(int $client): RedirectResponse
    {
        $client = Client::withTrashed()->findOrFail($client);
        abort_unless($client->company_id === Auth::user()->company_id, 404);
        $this->authorize('restore', $client);
        $client->restore();
        AuditEvent::record('client', $client->id, 'restored');

        return redirect()->route('clients.index')->with('success', 'Contact restauré.');
    }

    public function forceDelete(int $client): RedirectResponse
    {
        $client = Client::withTrashed()->findOrFail($client);
        abort_unless($client->company_id === Auth::user()->company_id, 404);
        $this->authorize('forceDelete', $client);
        AuditEvent::record('client', $client->id, 'force_deleted');
        $client->forceDelete();

        return redirect()->route('clients.index')->with('success', 'Contact définitivement supprimé.');
    }
}
