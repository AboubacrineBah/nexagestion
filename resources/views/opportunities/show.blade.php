@extends('layouts.app')
@section('title', 'Opportunité')
@section('section', 'CRM')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div>
        <div class="text-uppercase small fw-bold text-primary mb-1">Pipeline commercial</div>
        <h1 class="h2 page-heading mb-1">{{ $opportunity->title }}</h1>
    </div>
    <div class="d-flex gap-2">
        <a class="btn btn-outline-secondary" href="{{ route('opportunities.edit', $opportunity) }}">Modifier</a>
        <a class="btn btn-outline-secondary" href="{{ route('opportunities.index') }}">Retour</a>
    </div>
</div>

<div class="card p-4">
    <dl class="row mb-0">
        <dt class="col-sm-3">Client</dt>
        <dd class="col-sm-9">{{ $opportunity->client?->name }}</dd>
        <dt class="col-sm-3">Étape</dt>
        <dd class="col-sm-9">{{ $opportunity->stage }}</dd>
        <dt class="col-sm-3">Montant attendu</dt>
        <dd class="col-sm-9">{{ number_format((float) $opportunity->expected_amount, 2, ',', ' ') }}</dd>
        <dt class="col-sm-3">Prochaine action</dt>
        <dd class="col-sm-9">{{ $opportunity->next_action_at?->format('d/m/Y') }}</dd>
        <dt class="col-sm-3">Notes</dt>
        <dd class="col-sm-9">{{ $opportunity->notes }}</dd>
    </dl>
</div>
@endsection