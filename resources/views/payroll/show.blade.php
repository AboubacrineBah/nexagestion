@extends('layouts.app')
@section('title', 'Fiche de paie')
@section('section', 'RH')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div>
        <div class="text-uppercase small fw-bold text-success mb-1">Fiche de paie · {{ $payslip->pay_period->format('m/Y') }}</div>
        <h1 class="h2 page-heading mb-1">{{ $payslip->employee->first_name }} {{ $payslip->employee->last_name }}</h1>
    </div>
    <div class="d-flex gap-2">
        <a class="btn btn-outline-secondary" href="{{ route('employees.show', $payslip->employee) }}">Fiche employé</a>
        <a class="btn btn-outline-secondary" href="{{ route('payroll.index') }}">Retour</a>
    </div>
</div>

<section class="card p-4">
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <div class="small text-secondary">Période de paie</div>
            <div class="fw-semibold">{{ $payslip->pay_period->format('m/Y') }}</div>
        </div>
        <span class="badge text-bg-{{ $payslip->status === 'paid' ? 'success' : 'secondary' }}">{{ ['draft' => 'Brouillon', 'paid' => 'Payée'][$payslip->status] ?? ucfirst($payslip->status) }}</span>
    </div>
    <dl class="row mb-0">
        <dt class="col-sm-4">Salaire brut</dt>
        <dd class="col-sm-8">{{ number_format($payslip->gross_salary_cents / 100, 2, ',', ' ') }} {{ auth()->user()->company->currency }}</dd>
        <dt class="col-sm-4">Retenues</dt>
        <dd class="col-sm-8">{{ number_format($payslip->deductions_cents / 100, 2, ',', ' ') }} {{ auth()->user()->company->currency }}</dd>
        <dt class="col-sm-4">Salaire net</dt>
        <dd class="col-sm-8 fw-bold">{{ number_format($payslip->net_salary_cents / 100, 2, ',', ' ') }} {{ auth()->user()->company->currency }}</dd>
        <dt class="col-sm-4">Date de paiement</dt>
        <dd class="col-sm-8">{{ $payslip->paid_at?->format('d/m/Y') ?? 'Non réglée' }}</dd>
        @if($payslip->notes)
            <dt class="col-sm-4">Note</dt>
            <dd class="col-sm-8">{{ $payslip->notes }}</dd>
        @endif
    </dl>
</section>
<p class="small text-secondary mt-3">Donnée RH confidentielle. Les montants affichés ne constituent pas une validation réglementaire de la paie.</p>
@endsection
