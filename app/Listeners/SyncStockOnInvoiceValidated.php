<?php

namespace App\Listeners;

use App\Events\InvoiceValidated;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Contracts\Queue\ShouldQueue;

class SyncStockOnInvoiceValidated implements ShouldQueue
{
    public function handle(InvoiceValidated $event): void
    {
        $invoice = $event->invoice;

        if ($invoice->type !== 'invoice') {
            return;
        }

        foreach ($invoice->lines as $line) {
            $product = Product::where('company_id', $invoice->company_id)
                ->whereKey($line->product_id ?? null)
                ->first();

            if (! $product) {
                continue;
            }

            $quantityToDecrease = (int) $line->quantity;

            if ($quantityToDecrease <= 0) {
                continue;
            }

            $product->decrement('quantity_in_stock', $quantityToDecrease);

            StockMovement::create([
                'company_id' => $invoice->company_id,
                'product_id' => $product->id,
                'type' => 'out',
                'quantity' => $quantityToDecrease,
                'unit_price_cents' => (int) round((float) $line->unit_price * 100),
                'reference_type' => 'sales_document',
                'reference_id' => $invoice->id,
                'notes' => 'Sortie stock liée à la facture '.$invoice->reference,
            ]);
        }
    }
}
