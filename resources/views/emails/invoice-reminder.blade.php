<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Rappel de facture</title>
</head>
<body>
    <p>Bonjour {{ $document->client?->name }},</p>
    <p>Nous vous rappelons que la facture <strong>{{ $document->reference }}</strong> d'un montant de {{ number_format((float) $document->total, 2, ',', ' ') }} {{ $document->currency }} est en attente de règlement.</p>
    <p>Date d'échéance : {{ $document->due_at?->format('d/m/Y') }}</p>
    <p>Merci de procéder au règlement dans les meilleurs délais.</p>
    <p>Cordialement,<br>{{ config('app.name') }}</p>
</body>
</html>