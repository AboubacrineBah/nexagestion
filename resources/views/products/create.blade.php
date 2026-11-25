@extends('layouts.app')
@section('title', 'Nouveau produit')
@section('section', 'Stock')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div>
        <div class="text-uppercase small fw-bold text-success mb-1">Catalogue</div>
        <h1 class="h2 page-heading mb-1">Nouveau produit</h1>
    </div>
    <a class="btn btn-outline-secondary" href="{{ route('products.index') }}">Retour</a>
</div>

<div class="card p-4">
    <form method="post" action="{{ route('products.store') }}">
        @csrf
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label" for="name">Nom</label>
                <input class="form-control" id="name" name="name" value="{{ old('name') }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="sku">SKU</label>
                <input class="form-control" id="sku" name="sku" value="{{ old('sku') }}" required>
            </div>
            <div class="col-12">
                <label class="form-label" for="description">Description</label>
                <textarea class="form-control" id="description" name="description" rows="4">{{ old('description') }}</textarea>
            </div>
            <div class="col-md-3">
                <label class="form-label" for="purchase_price_cents">Prix d’achat (centimes)</label>
                <input class="form-control" id="purchase_price_cents" name="purchase_price_cents" type="number" min="0" value="{{ old('purchase_price_cents', 0) }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label" for="sale_price_cents">Prix de vente (centimes)</label>
                <input class="form-control" id="sale_price_cents" name="sale_price_cents" type="number" min="0" value="{{ old('sale_price_cents', 0) }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label" for="quantity_in_stock">Stock actuel</label>
                <input class="form-control" id="quantity_in_stock" name="quantity_in_stock" type="number" min="0" value="{{ old('quantity_in_stock', 0) }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label" for="alert_threshold">Seuil d’alerte</label>
                <input class="form-control" id="alert_threshold" name="alert_threshold" type="number" min="0" value="{{ old('alert_threshold', 0) }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label" for="is_active">Produit actif</label>
                <select class="form-select" id="is_active" name="is_active" required>
                    <option value="1" @selected(old('is_active', true))>Oui</option>
                    <option value="0" @selected(old('is_active') === '0')>Non</option>
                </select>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <a class="btn btn-outline-secondary" href="{{ route('products.index') }}">Annuler</a>
            <button class="btn btn-primary" type="submit">Enregistrer</button>
        </div>
    </form>
</div>
@endsection
