@extends('layouts.app')
@section('title', 'Fiche produit')
@section('section', 'Stock')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div>
        <div class="text-uppercase small fw-bold text-success mb-1">Produit</div>
        <h1 class="h2 page-heading mb-1">{{ $product->name }}</h1>
    </div>
    <div class="d-flex gap-2">
        @can('update', $product)
            <a class="btn btn-primary" href="{{ route('products.edit', $product) }}">Modifier</a>
        @endcan
        @can('update', $product)
            <a class="btn btn-outline-primary" href="{{ route('products.stock-movements.create', $product) }}">Mouvement de stock</a>
        @endcan
        <a class="btn btn-outline-secondary" href="{{ route('products.index') }}">Retour</a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-5">
        <section class="card p-4">
            <h2 class="h5 mb-3">Informations</h2>
            <dl class="row mb-0">
                <dt class="col-sm-5">SKU</dt>
                <dd class="col-sm-7">{{ $product->sku }}</dd>
                <dt class="col-sm-5">Prix achat</dt>
                <dd class="col-sm-7">{{ number_format($product->purchase_price_cents / 100, 2, ',', ' ') }} {{ auth()->user()->company->currency }}</dd>
                <dt class="col-sm-5">Prix vente</dt>
                <dd class="col-sm-7">{{ number_format($product->sale_price_cents / 100, 2, ',', ' ') }} {{ auth()->user()->company->currency }}</dd>
                <dt class="col-sm-5">Stock</dt>
                <dd class="col-sm-7">{{ $product->quantity_in_stock }}</dd>
                <dt class="col-sm-5">Seuil</dt>
                <dd class="col-sm-7">{{ $product->alert_threshold }}</dd>
                <dt class="col-sm-5">Actif</dt>
                <dd class="col-sm-7">{{ $product->is_active ? 'Oui' : 'Non' }}</dd>
            </dl>
        </section>
    </div>

    <div class="col-lg-7">
        <section class="card">
            <div class="p-3 border-bottom">
                <h2 class="h5 mb-0">Historique du stock</h2>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Quantité</th>
                            <th>Référence</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($product->stockMovements as $movement)
                            <tr>
                                <td><span class="badge text-bg-light">{{ ucfirst($movement->type) }}</span></td>
                                <td>{{ $movement->quantity }}</td>
                                <td>{{ $movement->reference_type }} #{{ $movement->reference_id }}</td>
                                <td>{{ $movement->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-secondary py-4">Aucun mouvement de stock enregistré.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>
@endsection
