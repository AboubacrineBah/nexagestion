@extends('layouts.app')
@section('title', 'Paie')
@section('section', 'RH')
@section('content')
<div class="mb-4">
    <div class="text-uppercase small fw-bold text-success mb-1">Ressources humaines</div>
    <h1 class="h2 page-heading mb-1">Fiches de paie</h1>
    <p class="text-secondary mb-0">Consultation des fiches de paie de votre entreprise.</p>
</div>

<section class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Employé</th>
                    <th>Période</th>
                    <th class="text-end">Brut</th>
                    <th class="text-end">Retenues</th>
                    <th class="text-end">Net</th>
                    <th>Statut</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($payslips as $payslip)
                    <tr>
                        <td>{{ $payslip->employee->first_name }} {{ $payslip->employee->last_name }}</td>
                        <td>{{ $payslip->pay_period->format('m/Y') }}</td>
                        <td class="text-end">{{ number_format($payslip->gross_salary_cents / 100, 2, ',', ' ') }} {{ auth()->user()->company->currency }}</td>
                        <td class="text-end">{{ number_format($payslip->deductions_cents / 100, 2, ',', ' ') }} {{ auth()->user()->company->currency }}</td>
                        <td class="text-end fw-semibold">{{ number_format($payslip->net_salary_cents / 100, 2, ',', ' ') }} {{ auth()->user()->company->currency }}</td>
                        <td><span class="badge text-bg-{{ $payslip->status === 'paid' ? 'success' : 'secondary' }}">{{ ['draft' => 'Brouillon', 'paid' => 'Payée'][$payslip->status] ?? ucfirst($payslip->status) }}</span></td>
                        <td class="text-end"><a class="btn btn-sm btn-outline-secondary" href="{{ route('payroll.show', $payslip) }}">Ouvrir</a></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-secondary py-5">Aucune fiche de paie enregistrée.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3">{{ $payslips->links() }}</div>
</section>
<p class="small text-secondary mt-3">Les fiches sont consultables par les responsables RH. Les règles de calcul et obligations légales doivent être validées avant tout usage de production.</p>
@endsection
