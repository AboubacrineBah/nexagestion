@extends('layouts.app')
@section('title', 'Contrats RH')
@section('section', 'RH')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div>
        <div class="text-uppercase small fw-bold text-success mb-1">Contrats</div>
        <h1 class="h2 page-heading mb-1">{{ $employee->first_name }} {{ $employee->last_name }}</h1>
        <p class="text-secondary mb-0">Historique des engagements et statuts contractuels.</p>
    </div>
    @can('create', App\Models\Contract::class)
        <a class="btn btn-primary" href="{{ route('employees.contracts.create', $employee) }}">Nouveau contrat</a>
    @endcan
</div>

<section class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Début</th>
                    <th>Fin</th>
                    <th>Salaire</th>
                    <th>Statut</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($contracts as $contract)
                    <tr>
                        <td>{{ ucfirst($contract->type) }}</td>
                        <td>{{ $contract->start_date?->format('d/m/Y') }}</td>
                        <td>{{ $contract->end_date?->format('d/m/Y') ?: '—' }}</td>
                        <td>{{ number_format($contract->salary_cents / 100, 2, ',', ' ') }} {{ auth()->user()->company->currency }}</td>
                        <td><span class="badge text-bg-light">{{ ucfirst($contract->status) }}</span></td>
                        <td class="text-end text-nowrap">
                            @can('update', $contract)
                                <a class="btn btn-sm btn-outline-secondary" href="{{ route('employees.contracts.edit', [$employee, $contract]) }}"><i class="bi bi-pencil"></i></a>
                            @endcan
                            @can('delete', $contract)
                                <form class="d-inline" method="post" action="{{ route('employees.contracts.destroy', [$employee, $contract]) }}" onsubmit="return confirm('Supprimer ce contrat ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-secondary py-5">Aucun contrat pour cet employé.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
