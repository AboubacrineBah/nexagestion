<?php

namespace App\Http\Controllers;

use App\Models\Payslip;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PayrollController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Payslip::class);

        $companyId = Auth::user()->company_id;
        $payslips = Payslip::query()
            ->with('employee')
            ->whereHas('employee', fn (Builder $query): Builder => $query->where('company_id', $companyId))
            ->latest('pay_period')
            ->paginate(15);

        return view('payroll.index', compact('payslips'));
    }

    public function show(Payslip $payslip): View
    {
        $payslip->loadMissing('employee');
        abort_unless($payslip->employee->company_id === Auth::user()->company_id, 404);
        $this->authorize('view', $payslip);

        return view('payroll.show', compact('payslip'));
    }

    public function exportPdf(Payslip $payslip)
    {
        $payslip->loadMissing('employee');
        abort_unless($payslip->employee->company_id === Auth::user()->company_id, 404);
        $this->authorize('view', $payslip);

        $pdf = Pdf::loadView('pdf.payslip', compact('payslip'));

        return $pdf->download('bulletin-'.$payslip->id.'.pdf');
    }

    public function destroy(Payslip $payslip): RedirectResponse
    {
        $payslip->loadMissing('employee');
        abort_unless($payslip->employee->company_id === Auth::user()->company_id, 404);
        $this->authorize('delete', $payslip);
        $payslip->delete();

        return redirect()->route('payroll.index')->with('success', 'Bulletin de paie supprimé.');
    }

    public function restore(int $payslip): RedirectResponse
    {
        $payslip = Payslip::withTrashed()->findOrFail($payslip);
        $payslip->loadMissing('employee');
        abort_unless($payslip->employee->company_id === Auth::user()->company_id, 404);
        $this->authorize('restore', $payslip);
        $payslip->restore();

        return redirect()->route('payroll.index')->with('success', 'Bulletin de paie restauré.');
    }

    public function forceDelete(int $payslip): RedirectResponse
    {
        $payslip = Payslip::withTrashed()->findOrFail($payslip);
        $payslip->loadMissing('employee');
        abort_unless($payslip->employee->company_id === Auth::user()->company_id, 404);
        $this->authorize('forceDelete', $payslip);
        $payslip->forceDelete();

        return redirect()->route('payroll.index')->with('success', 'Bulletin de paie définitivement supprimé.');
    }
}
