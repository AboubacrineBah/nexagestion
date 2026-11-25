@extends('layouts.app')
@section('title', 'Mouvements de stock')
@section('section', 'Stock')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div>
        <div class="text-uppercase small fw-bold text-success mb-1">Historique</div>
        <h1 class="h2 page-heading mb-1">{{ $product->name }}</h1>
        <p class="text-secondary mb-0">Traces complètes des entrées, sorties et ajustements.</p>
    </div>
    @can('update', $product)
        <a class="btn btn-primary" href="{{ route('products.stock-movements.create', $product) }}">Nouveau mouvement</a>
    @endcan
</div>

<section class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Quantité</th>
                    <th>Prix unitaire</th>
                    <th>Référence</th>
                    <th>Notes</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($movements as $movement)
                    <tr>
                        <td><span class="badge text-bg-light">{{ ucfirst($movement->type) }}</span></td>
                        <td>{{ $movement->quantity }}</td>
                        <td>{{ number_format($movement->unit_price_cents / 100, 2, ',', ' ') }} {{ auth()->user()->company->currency }}</td>
                        <td>{{ $movement->reference_type }} #{{ $movement->reference_id ?: 'Manuel' }}</td>
                        <td>{{ Str::limit($movement->notes ?? '—', 30) }}</td>
                        <td>{{ $movement->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-secondary py-5">Aucun mouvement de stock pour ce produit.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
