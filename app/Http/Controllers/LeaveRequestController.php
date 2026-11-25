<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLeaveRequestRequest;
use App\Models\Employee;
use App\Models\LeaveRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LeaveRequestController extends Controller
{
    public function index(Employee $employee): View
    {
        abort_unless($employee->company_id === Auth::user()->company_id, 404);
        $this->authorize('viewAny', LeaveRequest::class);

        return view('leave-requests.index', [
            'employee' => $employee->load('leaveRequests'),
            'leaveRequests' => $employee->leaveRequests()->latest()->get(),
        ]);
    }

    public function create(Employee $employee): View
    {
        abort_unless($employee->company_id === Auth::user()->company_id, 404);
        $this->authorize('create', LeaveRequest::class);

        return view('leave-requests.create', compact('employee'));
    }

    public function store(StoreLeaveRequestRequest $request, Employee $employee): RedirectResponse
    {
        abort_unless($employee->company_id === Auth::user()->company_id, 404);
        $this->authorize('create', LeaveRequest::class);

        $employee->leaveRequests()->create($request->validated());

        return redirect()->route('employees.leave-requests.index', $employee)->with('success', 'Demande de congé enregistrée.');
    }

    public function edit(Employee $employee, LeaveRequest $leaveRequest): View
    {
        abort_unless($employee->company_id === Auth::user()->company_id, 404);
        $this->authorize('update', $leaveRequest);

        return view('leave-requests.edit', compact('employee', 'leaveRequest'));
    }

    public function update(StoreLeaveRequestRequest $request, Employee $employee, LeaveRequest $leaveRequest): RedirectResponse
    {
        abort_unless($employee->company_id === Auth::user()->company_id, 404);
        $this->authorize('update', $leaveRequest);

        $leaveRequest->update($request->validated());

        return redirect()->route('employees.leave-requests.index', $employee)->with('success', 'Demande de congé mise à jour.');
    }

    public function destroy(Employee $employee, LeaveRequest $leaveRequest): RedirectResponse
    {
        abort_unless($employee->company_id === Auth::user()->company_id, 404);
        $this->authorize('delete', $leaveRequest);
        $leaveRequest->delete();

        return redirect()->route('employees.leave-requests.index', $employee)->with('success', 'Demande de congé supprimée.');
    }

    public function restore(Employee $employee, int $leaveRequest): RedirectResponse
    {
        abort_unless($employee->company_id === Auth::user()->company_id, 404);
        $leaveRequest = LeaveRequest::withTrashed()->findOrFail($leaveRequest);
        $this->authorize('restore', $leaveRequest);
        $leaveRequest->restore();

        return redirect()->route('employees.leave-requests.index', $employee)->with('success', 'Demande de congé restaurée.');
    }

    public function forceDelete(Employee $employee, int $leaveRequest): RedirectResponse
    {
        abort_unless($employee->company_id === Auth::user()->company_id, 404);
        $leaveRequest = LeaveRequest::withTrashed()->findOrFail($leaveRequest);
        $this->authorize('forceDelete', $leaveRequest);
        $leaveRequest->forceDelete();

        return redirect()->route('employees.leave-requests.index', $employee)->with('success', 'Demande de congé définitivement supprimée.');
    }
}
