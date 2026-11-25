@extends('layouts.app')
@section('title', 'Mouvement de stock')
@section('section', 'Stock')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div>
        <div class="text-uppercase small fw-bold text-success mb-1">Stock</div>
        <h1 class="h2 page-heading mb-1">Mouvement pour {{ $product->name }}</h1>
    </div>
    <a class="btn btn-outline-secondary" href="{{ route('products.stock-movements.index', $product) }}">Retour</a>
</div>

<div class="card p-4">
    <form method="post" action="{{ route('products.stock-movements.store', $product) }}">
        @csrf
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label" for="direction">Direction</label>
                <select class="form-select" id="direction" name="direction" required>
                    <option value="increase" @selected(old('direction') === 'increase')>Entrée</option>
                    <option value="decrease" @selected(old('direction') === 'decrease')>Sortie</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="quantity">Quantité</label>
                <input class="form-control" id="quantity" name="quantity" type="number" min="1" value="{{ old('quantity', 1) }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="notes">Note</label>
                <input class="form-control" id="notes" name="notes" value="{{ old('notes') }}" placeholder="Ajustement journal">
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <a class="btn btn-outline-secondary" href="{{ route('products.stock-movements.index', $product) }}">Annuler</a>
            <button class="btn btn-primary" type="submit">Valider</button>
        </div>
    </form>
</div>
@endsection
