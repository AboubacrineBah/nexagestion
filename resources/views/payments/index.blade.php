@extends('layouts.app')
@section('title', 'Paiements')
@section('section', 'Ventes')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div>
        <div class="text-uppercase small fw-bold text-success mb-1">Encaissements</div>
        <h1 class="h2 page-heading mb-1">Paiements</h1>
    </div>
</div>

<div class="card p-3">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Document</th>
                    <th>Client</th>
                    <th>Montant</th>
                    <th>Méthode</th>
                    <th>Référence</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                    <tr>
                        <td>{{ $payment->paid_at?->format('d/m/Y') }}</td>
                        <td>{{ $payment->document?->reference }}</td>
                        <td>{{ $payment->document?->client?->name }}</td>
                        <td>{{ number_format((float) $payment->amount, 2, ',', ' ') }}</td>
                        <td>{{ $payment->method }}</td>
                        <td>{{ $payment->reference }}</td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('payments.show', $payment) }}">Voir</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">Aucun paiement.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $payments->links() }}</div>
</div>
@endsection