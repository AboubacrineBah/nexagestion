@extends('layouts.app')
@section('title', 'Nouveau contrat')
@section('section', 'RH')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div>
        <div class="text-uppercase small fw-bold text-success mb-1">Contrat</div>
        <h1 class="h2 page-heading mb-1">Nouveau contrat pour {{ $employee->first_name }} {{ $employee->last_name }}</h1>
    </div>
    <a class="btn btn-outline-secondary" href="{{ route('employees.contracts.index', $employee) }}">Retour</a>
</div>

<div class="card p-4">
    <form method="post" action="{{ route('employees.contracts.store', $employee) }}">
        @csrf
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label" for="type">Type</label>
                <select class="form-select" id="type" name="type" required>
                    <option value="permanent" @selected(old('type') === 'permanent')>Permanent</option>
                    <option value="fixed" @selected(old('type') === 'fixed')>CDD</option>
                    <option value="temporary" @selected(old('type') === 'temporary')>Temporaire</option>
                    <option value="internship" @selected(old('type') === 'internship')>Stage</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="status">Statut</label>
                <select class="form-select" id="status" name="status" required>
                    <option value="draft" @selected(old('status') === 'draft')>En préparation</option>
                    <option value="active" @selected(old('status') === 'active')>Actif</option>
                    <option value="ended" @selected(old('status') === 'ended')>Terminé</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="salary_cents">Salaire (centimes)</label>
                <input class="form-control" id="salary_cents" name="salary_cents" type="number" min="0" value="{{ old('salary_cents', 0) }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="start_date">Date de début</label>
                <input class="form-control" id="start_date" name="start_date" type="date" value="{{ old('start_date') }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="end_date">Date de fin</label>
                <input class="form-control" id="end_date" name="end_date" type="date" value="{{ old('end_date') }}">
            </div>
            <div class="col-12">
                <label class="form-label" for="notes">Notes</label>
                <textarea class="form-control" id="notes" name="notes" rows="4">{{ old('notes') }}</textarea>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <a class="btn btn-outline-secondary" href="{{ route('employees.contracts.index', $employee) }}">Annuler</a>
            <button class="btn btn-primary" type="submit">Enregistrer</button>
        </div>
    </form>
</div>
@endsection
