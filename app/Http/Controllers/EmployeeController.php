<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmployeeRequest;
use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Employee::class);

        $employees = Employee::with(['contracts', 'leaveRequests'])
            ->where('company_id', Auth::user()->company_id)
            ->latest()
            ->get();

        return view('employees.index', compact('employees'));
    }

    public function create(): View
    {
        $this->authorize('create', Employee::class);

        return view('employees.create');
    }

    public function store(StoreEmployeeRequest $request): RedirectResponse
    {
        $employee = Employee::create($request->validated() + [
            'company_id' => Auth::user()->company_id,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('employees.index')->with('success', 'Employé enregistré.');
    }

    public function show(Employee $employee): View
    {
        $this->authorize('view', $employee);

        $employee->load(['contracts', 'leaveRequests']);
        $payslips = $employee->payslips()->latest('pay_period')->limit(6)->get();

        return view('employees.show', compact('employee', 'payslips'));
    }

    public function edit(Employee $employee): View
    {
        $this->authorize('update', $employee);

        return view('employees.edit', compact('employee'));
    }

    public function update(StoreEmployeeRequest $request, Employee $employee): RedirectResponse
    {
        $this->authorize('update', $employee);
        $employee->update($request->validated());

        return redirect()->route('employees.index')->with('success', 'Employé mis à jour.');
    }

    public function destroy(Employee $employee): RedirectResponse
    {
        $this->authorize('delete', $employee);
        $employee->delete();

        return redirect()->route('employees.index')->with('success', 'Employé supprimé.');
    }

    public function restore(int $employee): RedirectResponse
    {
        $employee = Employee::withTrashed()->findOrFail($employee);
        abort_unless($employee->company_id === Auth::user()->company_id, 404);
        $this->authorize('restore', $employee);
        $employee->restore();

        return redirect()->route('employees.index')->with('success', 'Employé restauré.');
    }

    public function forceDelete(int $employee): RedirectResponse
    {
        $employee = Employee::withTrashed()->findOrFail($employee);
        abort_unless($employee->company_id === Auth::user()->company_id, 404);
        $this->authorize('forceDelete', $employee);
        $employee->forceDelete();

        return redirect()->route('employees.index')->with('success', 'Employé définitivement supprimé.');
    }
}
