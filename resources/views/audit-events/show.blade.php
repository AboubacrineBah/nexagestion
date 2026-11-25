@extends('layouts.app')
@section('title', 'Événement d’audit')
@section('section', 'Administration')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div>
        <div class="text-uppercase small fw-bold text-secondary mb-1">Traçabilité</div>
        <h1 class="h2 page-heading mb-1">Événement #{{ $event->id }}</h1>
    </div>
    <a class="btn btn-outline-secondary" href="{{ route('audit-events.index') }}">Retour</a>
</div>

<div class="card p-4">
    <dl class="row mb-0">
        <dt class="col-sm-3">Date</dt>
        <dd class="col-sm-9">{{ $event->created_at?->format('d/m/Y H:i:s') }}</dd>
        <dt class="col-sm-3">Utilisateur</dt>
        <dd class="col-sm-9">{{ $event->user?->name }}</dd>
        <dt class="col-sm-3">Type</dt>
        <dd class="col-sm-9">{{ $event->subject_type }}</dd>
        <dt class="col-sm-3">Sujet</dt>
        <dd class="col-sm-9">#{{ $event->subject_id }}</dd>
        <dt class="col-sm-3">Action</dt>
        <dd class="col-sm-9">{{ $event->action }}</dd>
        <dt class="col-sm-3">Détails</dt>
        <dd class="col-sm-9"><pre class="mb-0">{{ json_encode($event->details, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre></dd>
    </dl>
</div>
@endsection