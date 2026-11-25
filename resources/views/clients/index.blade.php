@extends('layouts.app')
@section('title', 'CRM et contacts')
@section('section', 'CRM')
@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
        <div>
            <div class="text-uppercase small fw-bold text-success mb-1">Relation client</div>
            <h1 class="h2 page-heading mb-1">CRM et contacts</h1>
            <p class="text-secondary mb-0">Centralisez les prospects et les clients de votre entreprise.</p>
        </div>@can('create', App\Models\Client::class)<button class="btn btn-primary" data-bs-toggle="modal"
        data-bs-target="#createClient"><i class="bi bi-plus"></i> Ajouter un contact</button>@endcan
    </div>
    <section class="card">
        <div class="p-3 border-bottom">
            <form class="row g-2" method="get">
                <div class="col-md-7"><label class="visually-hidden" for="q">Rechercher</label><input class="form-control"
                        id="q" name="q" value="{{ request('q') }}" placeholder="Rechercher par nom, e-mail ou téléphone">
                </div>
                <div class="col-md-3"><label class="visually-hidden" for="type">Type de contact</label><select
                        class="form-select" id="type" name="type">
                        <option value="">Tous les contacts</option>
                        <option value="prospect" @selected(request('type') === 'prospect')>Prospects</option>
                        <option value="client" @selected(request('type') === 'client')>Clients</option>
                    </select></div>
                <div class="col-md-2 d-grid"><button class="btn btn-outline-secondary">Filtrer</button></div>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Contact</th>
                        <th>Type</th>
                        <th>Téléphone</th>
                        <th>E-mail</th>
                        <th>Documents</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clients as $client)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $client->name }}</div><small
                                    class="text-secondary">{{ $client->address }}</small>
                            </td>
                            <td><span
                                    class="badge {{ $client->type === 'prospect' ? 'text-bg-warning' : 'badge-soft' }}">{{ $client->type === 'prospect' ? 'Prospect' : 'Client' }}</span>
                            </td>
                            <td>{{ $client->phone ?: '—' }}</td>
                            <td>{{ $client->email ?: '—' }}</td>
                            <td>{{ $client->sales_documents_count }}</td>
                            <td class="text-end text-nowrap">@can('update', $client)<button
                                class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
                                data-bs-target="#editClient{{ $client->id }}" aria-label="Modifier {{ $client->name }}"><i
                            class="bi bi-pencil"></i></button>@endcan @can('delete', $client)
                                            <form class="d-inline" method="post" action="{{ route('clients.destroy', $client) }}"
                                                onsubmit="return confirm('Supprimer ce contact ?')">@csrf @method('DELETE')<button
                                                    class="btn btn-sm btn-outline-danger" aria-label="Supprimer {{ $client->name }}"><i
                                        class="bi bi-trash"></i></button></form>@endcan
                            </td>
                        </tr>
                    @empty<tr>
                        <td colspan="6" class="text-center text-secondary py-5">Aucun contact trouvé. Ajoutez un prospect ou
                            un client pour démarrer votre suivi.</td>
                    </tr>@endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $clients->links() }}</div>
    </section>
    @can('create', App\Models\Client::class)
        <div class="modal fade" id="createClient" tabindex="-1" aria-labelledby="createClientTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form method="post" action="{{ route('clients.store') }}">@csrf<div class="modal-header">
                            <h2 class="modal-title fs-5" id="createClientTitle">Ajouter un contact</h2><button class="btn-close"
                                type="button" data-bs-dismiss="modal" aria-label="Fermer"></button>
                        </div>
                        <div class="modal-body">@include('clients.form-fields', ['client' => null])</div>
                        <div class="modal-footer"><button type="button" class="btn btn-outline-secondary"
                                data-bs-dismiss="modal">Annuler</button><button class="btn btn-primary">Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
    </div>@endcan
    @foreach($clients as $client)@can('update', $client)
        <div class="modal fade" id="editClient{{ $client->id }}" tabindex="-1"
            aria-labelledby="editClientTitle{{ $client->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form method="post" action="{{ route('clients.update', $client) }}">@csrf @method('PUT')
                        <div class="modal-header">
                            <h2 class="modal-title fs-5" id="editClientTitle{{ $client->id }}">Modifier {{ $client->name }}</h2>
                            <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Fermer"></button>
                        </div>
                        <div class="modal-body">@include('clients.form-fields', ['client' => $client])</div>
                        <div class="modal-footer"><button class="btn btn-primary">Enregistrer</button></div>
                    </form>
                </div>
            </div>
    </div>@endcan @endforeach
@endsection
