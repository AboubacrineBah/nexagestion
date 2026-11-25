@extends('layouts.app')
@section('title', 'Paiement')
@section('section', 'Ventes')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div>
        <div class="text-uppercase small fw-bold text-success mb-1">Encaissement</div>
        <h1 class="h2 page-heading mb-1">Paiement #{{ $payment->id }}</h1>
    </div>
    <a class="btn btn-outline-secondary" href="{{ route('payments.index') }}">Retour</a>
</div>

<div class="card p-4">
    <dl class="row mb-0">
        <dt class="col-sm-3">Document</dt>
        <dd class="col-sm-9">{{ $payment->document?->reference }}</dd>
        <dt class="col-sm-3">Client</dt>
        <dd class="col-sm-9">{{ $payment->document?->client?->name }}</dd>
        <dt class="col-sm-3">Montant</dt>
        <dd class="col-sm-9">{{ number_format((float) $payment->amount, 2, ',', ' ') }}</dd>
        <dt class="col-sm-3">Date</dt>
        <dd class="col-sm-9">{{ $payment->paid_at?->format('d/m/Y') }}</dd>
        <dt class="col-sm-3">Méthode</dt>
        <dd class="col-sm-9">{{ $payment->method }}</dd>
        <dt class="col-sm-3">Référence</dt>
        <dd class="col-sm-9">{{ $payment->reference }}</dd>
        <dt class="col-sm-3">Notes</dt>
        <dd class="col-sm-9">{{ $payment->notes }}</dd>
    </dl>
</div>
@endsection