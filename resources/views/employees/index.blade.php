@extends('layouts.app')
@section('title', 'Ressources humaines')
@section('section', 'RH')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div>
        <div class="text-uppercase small fw-bold text-success mb-1">Gestion des talents</div>
        <h1 class="h2 page-heading mb-1">Employés</h1>
        <p class="text-secondary mb-0">Suivez les collaborateurs, leurs contrats et leur statut RH.</p>
    </div>
    @can('create', App\Models\Employee::class)
        <a class="btn btn-primary" href="{{ route('employees.create') }}"><i class="bi bi-person-plus me-1"></i> Ajouter un employé</a>
    @endcan
</div>

<section class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Employé</th>
                    <th>Poste</th>
                    <th>Email</th>
                    <th>Statut</th>
                    <th>Contrat</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($employees as $employee)
                    <tr>
                        <td>
                            <div class="fw-semibold"><a href="{{ route('employees.show', $employee) }}" class="text-decoration-none">{{ $employee->first_name }} {{ $employee->last_name }}</a></div>
                            <small class="text-secondary">{{ $employee->phone ?: 'Téléphone non renseigné' }}</small>
                        </td>
                        <td>{{ $employee->position ?: '—' }}</td>
                        <td>{{ $employee->email }}</td>
                        <td><span class="badge text-bg-light">{{ ucfirst($employee->status) }}</span></td>
                        <td>{{ $employee->contracts->count() }}</td>
                        <td class="text-end text-nowrap">
                            @can('view', $employee)
                                <a class="btn btn-sm btn-outline-secondary" href="{{ route('employees.show', $employee) }}" aria-label="Voir {{ $employee->first_name }} {{ $employee->last_name }}"><i class="bi bi-eye"></i></a>
                            @endcan
                            @can('update', $employee)
                                <a class="btn btn-sm btn-outline-secondary" href="{{ route('employees.edit', $employee) }}" aria-label="Modifier {{ $employee->first_name }} {{ $employee->last_name }}"><i class="bi bi-pencil"></i></a>
                            @endcan
                            @can('delete', $employee)
                                <form class="d-inline" method="post" action="{{ route('employees.destroy', $employee) }}" onsubmit="return confirm('Supprimer cet employé ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" aria-label="Supprimer {{ $employee->first_name }} {{ $employee->last_name }}"><i class="bi bi-trash"></i></button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-secondary py-5">Aucun employé trouvé. Créez votre première fiche RH.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
