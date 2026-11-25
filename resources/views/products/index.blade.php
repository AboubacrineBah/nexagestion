@extends('layouts.app')
@section('title', 'Gestion des stocks')
@section('section', 'Stock')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div>
        <div class="text-uppercase small fw-bold text-success mb-1">Catalogue et stock</div>
        <h1 class="h2 page-heading mb-1">Produits</h1>
        <p class="text-secondary mb-0">Suivez le stock, les seuils d’alerte et l’activité de vente.</p>
    </div>
    @can('create', App\Models\Product::class)
        <a class="btn btn-primary" href="{{ route('products.create') }}"><i class="bi bi-plus-lg me-1"></i> Ajouter un produit</a>
    @endcan
</div>

<section class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Produit</th>
                    <th>SKU</th>
                    <th>Stock</th>
                    <th>Prix</th>
                    <th>Seuil</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr>
                        <td>
                            <div class="fw-semibold"><a href="{{ route('products.show', $product) }}" class="text-decoration-none">{{ $product->name }}</a></div>
                            <small class="text-secondary">{{ $product->description ? Str::limit($product->description, 55) : 'Aucune description' }}</small>
                        </td>
                        <td>{{ $product->sku }}</td>
                        <td>
                            <span class="badge {{ $product->quantity_in_stock <= $product->alert_threshold ? 'text-bg-warning' : 'text-bg-success' }}">
                                {{ $product->quantity_in_stock }}
                            </span>
                        </td>
                        <td>{{ number_format($product->sale_price_cents / 100, 2, ',', ' ') }} {{ auth()->user()->company->currency }}</td>
                        <td>{{ $product->alert_threshold }}</td>
                        <td class="text-end text-nowrap">
                            @can('view', $product)
                                <a class="btn btn-sm btn-outline-secondary" href="{{ route('products.show', $product) }}" aria-label="Voir {{ $product->name }}"><i class="bi bi-eye"></i></a>
                            @endcan
                            @can('update', $product)
                                <a class="btn btn-sm btn-outline-secondary" href="{{ route('products.edit', $product) }}" aria-label="Modifier {{ $product->name }}"><i class="bi bi-pencil"></i></a>
                            @endcan
                            @can('delete', $product)
                                <form class="d-inline" method="post" action="{{ route('products.destroy', $product) }}" onsubmit="return confirm('Supprimer ce produit ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" aria-label="Supprimer {{ $product->name }}"><i class="bi bi-trash"></i></button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-secondary py-5">Aucun produit déclaré. Ajoutez votre catalogue pour gérer les mouvements de stock.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
