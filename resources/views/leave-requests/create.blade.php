@extends('layouts.app')
@section('title', 'Nouvelle demande de congé')
@section('section', 'RH')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div>
        <div class="text-uppercase small fw-bold text-success mb-1">Congé</div>
        <h1 class="h2 page-heading mb-1">Nouvelle demande pour {{ $employee->first_name }} {{ $employee->last_name }}</h1>
    </div>
    <a class="btn btn-outline-secondary" href="{{ route('employees.leave-requests.index', $employee) }}">Retour</a>
</div>

<div class="card p-4">
    <form method="post" action="{{ route('employees.leave-requests.store', $employee) }}">
        @csrf
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label" for="type">Type</label>
                <select class="form-select" id="type" name="type" required>
                    <option value="annual" @selected(old('type') === 'annual')>Congés annuels</option>
                    <option value="sick" @selected(old('type') === 'sick')>Maladie</option>
                    <option value="personal" @selected(old('type') === 'personal')>Personnelle</option>
                    <option value="maternity" @selected(old('type') === 'maternity')>Maternité</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="status">Statut</label>
                <select class="form-select" id="status" name="status" required>
                    <option value="pending" @selected(old('status') === 'pending')>En attente</option>
                    <option value="approved" @selected(old('status') === 'approved')>Approuvé</option>
                    <option value="rejected" @selected(old('status') === 'rejected')>Refusé</option>
                    <option value="cancelled" @selected(old('status') === 'cancelled')>Annulé</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="reason">Motif</label>
                <input class="form-control" id="reason" name="reason" value="{{ old('reason') }}">
            </div>
            <div class="col-md-6">
                <label class="form-label" for="start_date">Date de début</label>
                <input class="form-control" id="start_date" name="start_date" type="date" value="{{ old('start_date') }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="end_date">Date de fin</label>
                <input class="form-control" id="end_date" name="end_date" type="date" value="{{ old('end_date') }}" required>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <a class="btn btn-outline-secondary" href="{{ route('employees.leave-requests.index', $employee) }}">Annuler</a>
            <button class="btn btn-primary" type="submit">Enregistrer</button>
        </div>
    </form>
</div>
@endsection
