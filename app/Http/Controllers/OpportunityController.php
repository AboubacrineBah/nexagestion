<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOpportunityRequest;
use App\Models\AuditEvent;
use App\Models\Client;
use App\Models\Opportunity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OpportunityController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Opportunity::class);

        $opportunities = Opportunity::with(['client', 'owner'])
            ->where('company_id', Auth::user()->company_id)
            ->latest()
            ->paginate(12);

        return view('opportunities.index', compact('opportunities'));
    }

    public function create(): View
    {
        $this->authorize('create', Opportunity::class);

        $clients = Client::where('company_id', Auth::user()->company_id)->orderBy('name')->get();

        return view('opportunities.create', compact('clients'));
    }

    public function store(StoreOpportunityRequest $request): RedirectResponse
    {
        $opportunity = Opportunity::create($request->validated() + [
            'company_id' => Auth::user()->company_id,
            'owner_id' => Auth::id(),
        ]);
        AuditEvent::record('opportunity', $opportunity->id, 'created');

        return redirect()->route('opportunities.index')->with('success', 'Opportunité enregistrée.');
    }

    public function show(Opportunity $opportunity): View
    {
        abort_unless($opportunity->company_id === Auth::user()->company_id, 404);
        $this->authorize('view', $opportunity);

        return view('opportunities.show', compact('opportunity'));
    }

    public function edit(Opportunity $opportunity): View
    {
        abort_unless($opportunity->company_id === Auth::user()->company_id, 404);
        $this->authorize('update', $opportunity);

        $clients = Client::where('company_id', Auth::user()->company_id)->orderBy('name')->get();

        return view('opportunities.edit', compact('opportunity', 'clients'));
    }

    public function update(StoreOpportunityRequest $request, Opportunity $opportunity): RedirectResponse
    {
        abort_unless($opportunity->company_id === Auth::user()->company_id, 404);
        $this->authorize('update', $opportunity);
        $opportunity->update($request->validated());
        AuditEvent::record('opportunity', $opportunity->id, 'updated');

        return redirect()->route('opportunities.index')->with('success', 'Opportunité mise à jour.');
    }

    public function destroy(Opportunity $opportunity): RedirectResponse
    {
        abort_unless($opportunity->company_id === Auth::user()->company_id, 404);
        $this->authorize('delete', $opportunity);
        AuditEvent::record('opportunity', $opportunity->id, 'deleted');
        $opportunity->delete();

        return redirect()->route('opportunities.index')->with('success', 'Opportunité supprimée.');
    }

    public function restore(int $opportunity): RedirectResponse
    {
        $opportunity = Opportunity::withTrashed()->findOrFail($opportunity);
        abort_unless($opportunity->company_id === Auth::user()->company_id, 404);
        $this->authorize('restore', $opportunity);
        $opportunity->restore();
        AuditEvent::record('opportunity', $opportunity->id, 'restored');

        return redirect()->route('opportunities.index')->with('success', 'Opportunité restaurée.');
    }

    public function forceDelete(int $opportunity): RedirectResponse
    {
        $opportunity = Opportunity::withTrashed()->findOrFail($opportunity);
        abort_unless($opportunity->company_id === Auth::user()->company_id, 404);
        $this->authorize('forceDelete', $opportunity);
        AuditEvent::record('opportunity', $opportunity->id, 'force_deleted');
        $opportunity->forceDelete();

        return redirect()->route('opportunities.index')->with('success', 'Opportunité définitivement supprimée.');
    }
}
