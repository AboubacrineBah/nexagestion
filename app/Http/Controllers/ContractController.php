<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContractRequest;
use App\Models\Contract;
use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ContractController extends Controller
{
    public function index(Employee $employee): View
    {
        abort_unless($employee->company_id === Auth::user()->company_id, 404);
        $this->authorize('viewAny', Contract::class);

        return view('contracts.index', [
            'employee' => $employee->load('contracts'),
            'contracts' => $employee->contracts()->latest()->get(),
        ]);
    }

    public function create(Employee $employee): View
    {
        abort_unless($employee->company_id === Auth::user()->company_id, 404);
        $this->authorize('create', Contract::class);

        return view('contracts.create', compact('employee'));
    }

    public function store(StoreContractRequest $request, Employee $employee): RedirectResponse
    {
        abort_unless($employee->company_id === Auth::user()->company_id, 404);
        $this->authorize('create', Contract::class);

        $employee->contracts()->create($request->validated());

        return redirect()->route('employees.contracts.index', $employee)->with('success', 'Contrat enregistré.');
    }

    public function edit(Employee $employee, Contract $contract): View
    {
        abort_unless($employee->company_id === Auth::user()->company_id, 404);
        $this->authorize('update', $contract);

        return view('contracts.edit', compact('employee', 'contract'));
    }

    public function update(StoreContractRequest $request, Employee $employee, Contract $contract): RedirectResponse
    {
        abort_unless($employee->company_id === Auth::user()->company_id, 404);
        $this->authorize('update', $contract);

        $contract->update($request->validated());

        return redirect()->route('employees.contracts.index', $employee)->with('success', 'Contrat mis à jour.');
    }

    public function destroy(Employee $employee, Contract $contract): RedirectResponse
    {
        abort_unless($employee->company_id === Auth::user()->company_id, 404);
        $this->authorize('delete', $contract);
        $contract->delete();

        return redirect()->route('employees.contracts.index', $employee)->with('success', 'Contrat supprimé.');
    }

    public function restore(Employee $employee, int $contract): RedirectResponse
    {
        abort_unless($employee->company_id === Auth::user()->company_id, 404);
        $contract = Contract::withTrashed()->findOrFail($contract);
        $this->authorize('restore', $contract);
        $contract->restore();

        return redirect()->route('employees.contracts.index', $employee)->with('success', 'Contrat restauré.');
    }

    public function forceDelete(Employee $employee, int $contract): RedirectResponse
    {
        abort_unless($employee->company_id === Auth::user()->company_id, 404);
        $contract = Contract::withTrashed()->findOrFail($contract);
        $this->authorize('forceDelete', $contract);
        $contract->forceDelete();

        return redirect()->route('employees.contracts.index', $employee)->with('success', 'Contrat définitivement supprimé.');
    }
}
