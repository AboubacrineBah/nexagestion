<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStockMovementRequest;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StockMovementController extends Controller
{
    public function index(Product $product): View
    {
        abort_unless($product->company_id === Auth::user()->company_id, 404);
        $this->authorize('view', $product);

        return view('stock-movements.index', [
            'product' => $product->load('stockMovements'),
            'movements' => $product->stockMovements()->latest()->get(),
        ]);
    }

    public function create(Product $product): View
    {
        abort_unless($product->company_id === Auth::user()->company_id, 404);
        $this->authorize('update', $product);

        return view('stock-movements.create', compact('product'));
    }

    public function store(StoreStockMovementRequest $request, Product $product): RedirectResponse
    {
        abort_unless($product->company_id === Auth::user()->company_id, 404);
        $this->authorize('update', $product);

        $quantity = (int) $request->validated('quantity');
        $direction = $request->validated('direction');
        $type = $direction === 'increase' ? 'in' : 'out';
        $delta = $direction === 'increase' ? $quantity : -$quantity;

        if ($direction === 'decrease' && $product->quantity_in_stock < $quantity) {
            return back()->withErrors(['quantity' => 'Le stock disponible est insuffisant pour cette sortie.']);
        }

        $product->increment('quantity_in_stock', $delta);

        StockMovement::create([
            'company_id' => $product->company_id,
            'product_id' => $product->id,
            'type' => $type,
            'quantity' => $quantity,
            'unit_price_cents' => $product->sale_price_cents,
            'reference_type' => 'manual_adjustment',
            'reference_id' => 0,
            'notes' => $request->validated('notes') ?? 'Mouvement manuel de stock',
        ]);

        return redirect()->route('products.stock-movements.index', $product)->with('success', 'Mouvement de stock enregistré.');
    }
}
