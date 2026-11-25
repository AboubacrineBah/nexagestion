@extends('layouts.app')
@section('title', 'Modifier un employé')
@section('section', 'RH')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div>
        <div class="text-uppercase small fw-bold text-success mb-1">Ressources humaines</div>
        <h1 class="h2 page-heading mb-1">Modifier {{ $employee->first_name }} {{ $employee->last_name }}</h1>
    </div>
    <a class="btn btn-outline-secondary" href="{{ route('employees.index') }}">Retour</a>
</div>

<div class="card p-4">
    <form method="post" action="{{ route('employees.update', $employee) }}">
        @csrf
        @method('PUT')
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label" for="first_name">Prénom</label>
                <input class="form-control" id="first_name" name="first_name" value="{{ old('first_name', $employee->first_name) }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="last_name">Nom</label>
                <input class="form-control" id="last_name" name="last_name" value="{{ old('last_name', $employee->last_name) }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="position">Poste</label>
                <input class="form-control" id="position" name="position" value="{{ old('position', $employee->position) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label" for="email">Email</label>
                <input class="form-control" id="email" name="email" type="email" value="{{ old('email', $employee->email) }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="phone">Téléphone</label>
                <input class="form-control" id="phone" name="phone" value="{{ old('phone', $employee->phone) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label" for="status">Statut</label>
                <select class="form-select" id="status" name="status" required>
                    <option value="active" @selected(old('status', $employee->status) === 'active')>Actif</option>
                    <option value="paused" @selected(old('status', $employee->status) === 'paused')>En pause</option>
                    <option value="terminated" @selected(old('status', $employee->status) === 'terminated')>Terminé</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="hire_date">Date d’embauche</label>
                <input class="form-control" id="hire_date" name="hire_date" type="date" value="{{ old('hire_date', $employee->hire_date?->format('Y-m-d')) }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="monthly_salary_cents">Salaire mensuel (centimes)</label>
                <input class="form-control" id="monthly_salary_cents" name="monthly_salary_cents" type="number" min="0" value="{{ old('monthly_salary_cents', $employee->monthly_salary_cents) }}" required>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <a class="btn btn-outline-secondary" href="{{ route('employees.index') }}">Annuler</a>
            <button class="btn btn-primary" type="submit">Enregistrer</button>
        </div>
    </form>
</div>
@endsection
