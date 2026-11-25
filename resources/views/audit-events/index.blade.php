@extends('layouts.app')
@section('title', 'Journal d’audit')
@section('section', 'Administration')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div>
        <div class="text-uppercase small fw-bold text-secondary mb-1">Traçabilité</div>
        <h1 class="h2 page-heading mb-1">Journal d’audit</h1>
    </div>
</div>

<div class="card p-3">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Utilisateur</th>
                    <th>Type</th>
                    <th>Sujet</th>
                    <th>Action</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($events as $event)
                    <tr>
                        <td>{{ $event->created_at?->format('d/m/Y H:i') }}</td>
                        <td>{{ $event->user?->name }}</td>
                        <td>{{ $event->subject_type }}</td>
                        <td>#{{ $event->subject_id }}</td>
                        <td><span class="badge text-bg-secondary">{{ $event->action }}</span></td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('audit-events.show', $event) }}">Voir</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">Aucun événement.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $events->links() }}</div>
</div>
@endsection