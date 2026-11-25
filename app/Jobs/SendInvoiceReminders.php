<?php

namespace App\Jobs;

use App\Mail\InvoiceReminder;
use App\Models\SalesDocument;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendInvoiceReminders implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        $overdueInvoices = SalesDocument::where('type', 'invoice')
            ->whereIn('status', ['unpaid', 'partial'])
            ->whereNotNull('due_at')
            ->where('due_at', '<', now()->toDateString())
            ->with('client')
            ->get();

        foreach ($overdueInvoices as $invoice) {
            if ($invoice->client?->email) {
                Mail::to($invoice->client->email)->send(new InvoiceReminder($invoice));
            }
        }
    }
}
