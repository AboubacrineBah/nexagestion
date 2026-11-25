@extends('layouts.app')
@section('title', 'Fiche employé')
@section('section', 'RH')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div>
        <div class="text-uppercase small fw-bold text-success mb-1">Fiche employé</div>
        <h1 class="h2 page-heading mb-1">{{ $employee->first_name }} {{ $employee->last_name }}</h1>
    </div>
    <div class="d-flex gap-2">
        @can('update', $employee)
            <a class="btn btn-primary" href="{{ route('employees.edit', $employee) }}">Modifier</a>
        @endcan
        <a class="btn btn-outline-secondary" href="{{ route('employees.index') }}">Retour</a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-5">
        <section class="card p-4">
            <h2 class="h5 mb-3">Informations</h2>
            <dl class="row mb-0">
                <dt class="col-sm-5">Poste</dt>
                <dd class="col-sm-7">{{ $employee->position ?: '—' }}</dd>
                <dt class="col-sm-5">Email</dt>
                <dd class="col-sm-7">{{ $employee->email }}</dd>
                <dt class="col-sm-5">Téléphone</dt>
                <dd class="col-sm-7">{{ $employee->phone ?: '—' }}</dd>
                <dt class="col-sm-5">Statut</dt>
                <dd class="col-sm-7"><span class="badge text-bg-light">{{ ucfirst($employee->status) }}</span></dd>
                <dt class="col-sm-5">Date d’embauche</dt>
                <dd class="col-sm-7">{{ $employee->hire_date?->format('d/m/Y') ?? '—' }}</dd>
                <dt class="col-sm-5">Salaire</dt>
                <dd class="col-sm-7">{{ number_format($employee->monthly_salary_cents / 100, 2, ',', ' ') }} {{ auth()->user()->company->currency }}</dd>
            </dl>
            <div class="d-flex gap-2 mt-4">
                @can('create', App\Models\Contract::class)
                    <a class="btn btn-sm btn-outline-primary" href="{{ route('employees.contracts.create', $employee) }}">Nouveau contrat</a>
                @endcan
                @can('create', App\Models\LeaveRequest::class)
                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('employees.leave-requests.create', $employee) }}">Demande congé</a>
                @endcan
            </div>
        </section>
    </div>

    <div class="col-lg-7">
        <section class="card mb-4">
            <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                <h2 class="h5 mb-0">Contrats</h2>
                <a class="btn btn-sm btn-outline-secondary" href="{{ route('employees.contracts.index', $employee) }}">Voir tout</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Début</th>
                            <th>Fin</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employee->contracts as $contract)
                            <tr>
                                <td>{{ ucfirst($contract->type) }}</td>
                                <td>{{ $contract->start_date?->format('d/m/Y') }}</td>
                                <td>{{ $contract->end_date?->format('d/m/Y') ?: '—' }}</td>
                                <td><span class="badge text-bg-light">{{ ucfirst($contract->status) }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-secondary py-4">Aucun contrat enregistré.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="card">
            <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                <h2 class="h5 mb-0">Congés et absences</h2>
                <a class="btn btn-sm btn-outline-secondary" href="{{ route('employees.leave-requests.index', $employee) }}">Voir tout</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Du</th>
                            <th>Au</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employee->leaveRequests as $leave)
                            <tr>
                                <td>{{ ucfirst($leave->type) }}</td>
                                <td>{{ $leave->start_date?->format('d/m/Y') }}</td>
                                <td>{{ $leave->end_date?->format('d/m/Y') }}</td>
                                <td><span class="badge text-bg-light">{{ ucfirst($leave->status) }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-secondary py-4">Aucune demande de congé enregistrée.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="card mt-4">
            <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                <h2 class="h5 mb-0">Fiches de paie récentes</h2>
                <a class="btn btn-sm btn-outline-secondary" href="{{ route('payroll.index') }}">Voir tout</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Période</th>
                            <th>Net</th>
                            <th>Statut</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payslips as $payslip)
                            <tr>
                                <td>{{ $payslip->pay_period->format('m/Y') }}</td>
                                <td>{{ number_format($payslip->net_salary_cents / 100, 2, ',', ' ') }} {{ auth()->user()->company->currency }}</td>
                                <td><span class="badge text-bg-{{ $payslip->status === 'paid' ? 'success' : 'secondary' }}">{{ ['draft' => 'Brouillon', 'paid' => 'Payée'][$payslip->status] ?? ucfirst($payslip->status) }}</span></td>
                                <td class="text-end"><a class="btn btn-sm btn-outline-secondary" href="{{ route('payroll.show', $payslip) }}">Ouvrir</a></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-secondary py-4">Aucune fiche de paie enregistrée.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>
@endsection
