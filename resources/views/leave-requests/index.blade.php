@extends('layouts.app')
@section('title', 'Congés et absences')
@section('section', 'RH')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div>
        <div class="text-uppercase small fw-bold text-success mb-1">Congés</div>
        <h1 class="h2 page-heading mb-1">{{ $employee->first_name }} {{ $employee->last_name }}</h1>
        <p class="text-secondary mb-0">Suivi des demandes de congé et d’absence.</p>
    </div>
    @can('create', App\Models\LeaveRequest::class)
        <a class="btn btn-primary" href="{{ route('employees.leave-requests.create', $employee) }}">Nouvelle demande</a>
    @endcan
</div>

<section class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Du</th>
                    <th>Au</th>
                    <th>Motif</th>
                    <th>Statut</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($leaveRequests as $leaveRequest)
                    <tr>
                        <td>{{ ucfirst($leaveRequest->type) }}</td>
                        <td>{{ $leaveRequest->start_date?->format('d/m/Y') }}</td>
                        <td>{{ $leaveRequest->end_date?->format('d/m/Y') }}</td>
                        <td>{{ Str::limit($leaveRequest->reason ?? '—', 32) }}</td>
                        <td><span class="badge text-bg-light">{{ ucfirst($leaveRequest->status) }}</span></td>
                        <td class="text-end text-nowrap">
                            @can('update', $leaveRequest)
                                <a class="btn btn-sm btn-outline-secondary" href="{{ route('employees.leave-requests.edit', [$employee, $leaveRequest]) }}"><i class="bi bi-pencil"></i></a>
                            @endcan
                            @can('delete', $leaveRequest)
                                <form class="d-inline" method="post" action="{{ route('employees.leave-requests.destroy', [$employee, $leaveRequest]) }}" onsubmit="return confirm('Supprimer cette demande ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-secondary py-5">Aucune demande enregistrée.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
