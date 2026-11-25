<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>{{ $document->reference }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background-color: #f2f2f2; }
        .total { font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $document->type === 'quote' ? 'Devis' : 'Facture' }} {{ $document->reference }}</h1>
        <p>Client : {{ $document->client?->name }}</p>
        <p>Date : {{ $document->issued_at?->format('d/m/Y') }}</p>
    </div>
    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th>Quantité</th>
                <th>Prix unitaire</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($document->lines as $line)
                <tr>
                    <td>{{ $line->description }}</td>
                    <td>{{ $line->quantity }}</td>
                    <td>{{ number_format((float) $line->unit_price, 2, ',', ' ') }}</td>
                    <td>{{ number_format((float) $line->line_total, 2, ',', ' ') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="total">Total</td>
                <td class="total">{{ number_format((float) $document->total, 2, ',', ' ') }} {{ $document->currency }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>