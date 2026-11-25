@extends('layouts.app')
@section('title', 'Modifier l’opportunité')
@section('section', 'CRM')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div>
        <div class="text-uppercase small fw-bold text-primary mb-1">Pipeline commercial</div>
        <h1 class="h2 page-heading mb-1">Modifier l’opportunité</h1>
    </div>
    <a class="btn btn-outline-secondary" href="{{ route('opportunities.index') }}">Retour</a>
</div>

<div class="card p-4">
    <form method="post" action="{{ route('opportunities.update', $opportunity) }}">
        @csrf
        @method('PUT')
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label required" for="client_id">Client</label>
                <select class="form-select" id="client_id" name="client_id" required>
                    <option value="">— Choisir —</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}" @selected(old('client_id', $opportunity->client_id) == $client->id)>{{ $client->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label required" for="title">Titre</label>
                <input class="form-control" id="title" name="title" value="{{ old('title', $opportunity->title) }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label required" for="stage">Étape</label>
                <select class="form-select" id="stage" name="stage" required>
                    @foreach(['new' => 'Nouveau', 'qualified' => 'Qualifié', 'proposal' => 'Proposition', 'won' => 'Gagné', 'lost' => 'Perdu'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('stage', $opportunity->stage) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label required" for="expected_amount">Montant attendu</label>
                <input class="form-control" id="expected_amount" name="expected_amount" type="number" step="0.01" min="0" value="{{ old('expected_amount', $opportunity->expected_amount) }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="next_action_at">Prochaine action</label>
                <input class="form-control" id="next_action_at" name="next_action_at" type="date" value="{{ old('next_action_at', $opportunity->next_action_at?->format('Y-m-d')) }}">
            </div>
            <div class="col-12">
                <label class="form-label" for="notes">Notes</label>
                <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes', $opportunity->notes) }}</textarea>
            </div>
        </div>
        <div class="d-flex justify-content-end gap-2 mt-4">
            <a class="btn btn-outline-secondary" href="{{ route('opportunities.index') }}">Annuler</a>
            <button class="btn btn-primary" type="submit">Mettre à jour</button>
        </div>
    </form>
</div>
@endsection