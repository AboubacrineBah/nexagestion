<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Payment::class);

        $payments = Payment::with(['document.client'])
            ->where('company_id', Auth::user()->company_id)
            ->latest('paid_at')
            ->paginate(15);

        return view('payments.index', compact('payments'));
    }

    public function show(Payment $payment): View
    {
        abort_unless($payment->company_id === Auth::user()->company_id, 404);
        $this->authorize('view', $payment);

        return view('payments.show', compact('payment'));
    }

    public function destroy(Payment $payment): RedirectResponse
    {
        abort_unless($payment->company_id === Auth::user()->company_id, 404);
        $this->authorize('delete', $payment);
        $payment->delete();

        return redirect()->route('payments.index')->with('success', 'Paiement supprimé.');
    }

    public function restore(int $payment): RedirectResponse
    {
        $payment = Payment::withTrashed()->findOrFail($payment);
        abort_unless($payment->company_id === Auth::user()->company_id, 404);
        $this->authorize('restore', $payment);
        $payment->restore();

        return redirect()->route('payments.index')->with('success', 'Paiement restauré.');
    }

    public function forceDelete(int $payment): RedirectResponse
    {
        $payment = Payment::withTrashed()->findOrFail($payment);
        abort_unless($payment->company_id === Auth::user()->company_id, 404);
        $this->authorize('forceDelete', $payment);
        $payment->forceDelete();

        return redirect()->route('payments.index')->with('success', 'Paiement définitivement supprimé.');
    }
}
