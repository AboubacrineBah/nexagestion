<?php

namespace App\Events;

use App\Models\SalesDocument;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InvoiceValidated
{
    use Dispatchable, SerializesModels;

    public function __construct(public SalesDocument $invoice)
    {
        //
    }
}
