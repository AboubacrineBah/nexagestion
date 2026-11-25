@extends('layouts.app')
@section('title', 'Tableau de bord')
@section('section', 'Tableau de bord')
@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <div class="text-uppercase small fw-bold text-success mb-1">Pilotage de l’activité</div>
            <h1 class="h2 page-heading mb-1">Bonjour {{ Str::before(auth()->user()->name, ' ') }}</h1>
            <p class="text-secondary mb-0">Suivez votre relation client et les opérations commerciales.</p>
        </div><a class="btn btn-primary" href="{{ route('sales.create') }}"><i class="bi bi-plus-lg me-1"></i> Nouveau
            document</a>
    </div>
    <div class="row g-3 mb-4">
        @foreach([['Contacts CRM', $clientCount, 'bi-people', route('clients.index')], ['Prospects à suivre', $prospectCount, 'bi-person', route('clients.index') . '?type=prospect'], ['Devis en cours', $quoteCount, 'bi-file-earmark-text', route('sales.index') . '?type=quote'], ['Factures émises', $invoiceCount, 'bi-receipt', route('sales.index') . '?type=invoice']] as [$label, $value, $icon, $url])
            <div class="col-6 col-xl-3"><a class="card h-100 p-3 text-decoration-none" href="{{ $url }}">
                    <div class="d-flex justify-content-between text-secondary small">{{ $label }}<i
                            class="bi {{ $icon }} text-success fs-5"></i></div>
                    <div class="kpi-value mt-3">{{ number_format($value, 0, ',', ' ') }}</div><span
                        class="small text-secondary">Voir les éléments <i class="bi bi-arrow-right"></i></span>
                </a></div>
        @endforeach
    </div>
    <div class="row g-3">
        <div class="col-lg-8">
            <section class="card">
                <div class="p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="h6 mb-1">Documents récents</h2><span class="small text-secondary">Devis et factures de
                            votre entreprise</span>
                    </div><a class="btn btn-sm btn-outline-secondary" href="{{ route('sales.index') }}">Tout afficher</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Référence</th>
                                <th>Client</th>
                                <th>Type</th>
                                <th>Montant</th>
                                <th>État</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentDocuments as $document)
                                <tr>
                                    <td><a class="fw-semibold text-decoration-none"
                                            href="{{ route('sales.show', $document) }}">{{ $document->reference }}</a></td>
                                    <td>{{ $document->client->name }}</td>
                                    <td>{{ $document->type === 'quote' ? 'Devis' : 'Facture' }}</td>
                                    <td>{{ number_format((float) $document->total, 0, ',', ' ') }} {{ $document->currency }}</td>
                                    <td><span class="badge text-bg-light">{{ ucfirst($document->status) }}</span></td>
                            </tr>@empty<tr>
                                <td colspan="5" class="text-center text-secondary py-4">Aucun document. Créez votre premier
                                    devis ou votre première facture.</td>
                            </tr>@endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
        <div class="col-lg-4">
            <section class="card p-4 h-100">
                <div class="text-success fs-4 mb-2"><i class="bi bi-wallet2"></i></div>
                <h2 class="h6">Montant encaissé</h2>
                <div class="kpi-value">{{ number_format($received, 0, ',', ' ') }} XOF</div>
                <p class="small text-secondary mt-2 mb-0">Somme des paiements enregistrés sur vos factures. Cette synthèse
                    de gestion ne remplace pas une comptabilité réglementaire.</p><a
                    class="btn btn-sm btn-outline-secondary align-self-start mt-auto"
                    href="{{ route('sales.index') }}?type=invoice">Consulter les factures</a>
            </section>
        </div>
    </div>
@endsection
