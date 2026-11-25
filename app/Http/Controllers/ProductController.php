<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Product::class);

        $products = Product::where('company_id', Auth::user()->company_id)
            ->latest()
            ->get();

        return view('products.index', compact('products'));
    }

    public function create(): View
    {
        $this->authorize('create', Product::class);

        return view('products.create');
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $product = Product::create($request->validated() + ['company_id' => Auth::user()->company_id]);

        return redirect()->route('products.index')->with('success', 'Produit enregistré.');
    }

    public function show(Product $product): View
    {
        $this->authorize('view', $product);

        return view('products.show', ['product' => $product->load('stockMovements')]);
    }

    public function edit(Product $product): View
    {
        $this->authorize('update', $product);

        return view('products.edit', compact('product'));
    }

    public function update(StoreProductRequest $request, Product $product): RedirectResponse
    {
        $this->authorize('update', $product);
        $product->update($request->validated());

        return redirect()->route('products.index')->with('success', 'Produit mis à jour.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->authorize('delete', $product);
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Produit supprimé.');
    }

    public function restore(int $product): RedirectResponse
    {
        $product = Product::withTrashed()->findOrFail($product);
        abort_unless($product->company_id === Auth::user()->company_id, 404);
        $this->authorize('restore', $product);
        $product->restore();

        return redirect()->route('products.index')->with('success', 'Produit restauré.');
    }

    public function forceDelete(int $product): RedirectResponse
    {
        $product = Product::withTrashed()->findOrFail($product);
        abort_unless($product->company_id === Auth::user()->company_id, 404);
        $this->authorize('forceDelete', $product);
        $product->forceDelete();

        return redirect()->route('products.index')->with('success', 'Produit définitivement supprimé.');
    }
}
