@extends('layouts.app')
@section('title', 'Opportunités')
@section('section', 'CRM')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div>
        <div class="text-uppercase small fw-bold text-primary mb-1">Pipeline commercial</div>
        <h1 class="h2 page-heading mb-1">Opportunités</h1>
    </div>
    <a class="btn btn-primary" href="{{ route('opportunities.create') }}">Nouvelle opportunité</a>
</div>

<div class="card p-3">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Client</th>
                    <th>Étape</th>
                    <th>Montant attendu</th>
                    <th>Prochaine action</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($opportunities as $opportunity)
                    <tr>
                        <td>{{ $opportunity->title }}</td>
                        <td>{{ $opportunity->client?->name }}</td>
                        <td><span class="badge text-bg-secondary">{{ $opportunity->stage }}</span></td>
                        <td>{{ number_format((float) $opportunity->expected_amount, 2, ',', ' ') }}</td>
                        <td>{{ $opportunity->next_action_at?->format('d/m/Y') }}</td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('opportunities.edit', $opportunity) }}">Modifier</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">Aucune opportunité.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $opportunities->links() }}</div>
</div>
@endsection