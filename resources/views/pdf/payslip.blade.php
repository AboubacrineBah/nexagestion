<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Bulletin de paie</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Bulletin de paie</h1>
        <p>Employé : {{ $payslip->employee?->first_name }} {{ $payslip->employee?->last_name }}</p>
        <p>Période : {{ $payslip->pay_period?->format('m/Y') }}</p>
    </div>
    <table>
        <tr>
            <th>Salaire brut</th>
            <td>{{ number_format((float) $payslip->gross_salary_cents / 100, 2, ',', ' ') }}</td>
        </tr>
        <tr>
            <th>Déductions</th>
            <td>{{ number_format((float) $payslip->deductions_cents / 100, 2, ',', ' ') }}</td>
        </tr>
        <tr>
            <th>Salaire net</th>
            <td>{{ number_format((float) $payslip->net_salary_cents / 100, 2, ',', ' ') }}</td>
        </tr>
        <tr>
            <th>Statut</th>
            <td>{{ $payslip->status }}</td>
        </tr>
    </table>
</body>
</html>