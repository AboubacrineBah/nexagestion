<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Présentation NexaGestion</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; margin: 40px; }
        h1 { color: #1f3a5f; }
        h2 { color: #2c5282; margin-top: 20px; }
        .section { margin-bottom: 20px; }
        ul { margin-left: 20px; }
    </style>
</head>
<body>
    <h1>NexaGestion — Application de gestion d'entreprise</h1>

    <div class="section">
        <h2>1. Présentation générale</h2>
        <p>NexaGestion est une application web de gestion d'entreprise (ERP/CRM) développée avec Laravel 13 et Bootstrap 5. Elle permet de gérer les contacts, les opportunités commerciales, les devis et factures, les paiements, les employés, les contrats, les congés, la paie, le stock et l'audit.</p>
    </div>

    <div class="section">
        <h2>2. Fonctionnalités principales</h2>
        <ul>
            <li><strong>CRM</strong> : gestion des contacts (prospects/clients), opportunités commerciales.</li>
            <li><strong>Ventes</strong> : devis, factures, paiements, conversion de devis en facture.</li>
            <li><strong>RH</strong> : employés, contrats, congés, bulletins de paie.</li>
            <li><strong>Stock</strong> : produits, mouvements de stock, alertes de seuil.</li>
            <li><strong>Audit</strong> : journal d'audit complet avec traçabilité.</li>
            <li><strong>Notifications</strong> : relances automatiques par e-mail pour factures impayées.</li>
            <li><strong>Export</strong> : PDF pour devis, factures et bulletins de paie.</li>
        </ul>
    </div>

    <div class="section">
        <h2>3. Architecture technique</h2>
        <ul>
            <li><strong>Backend</strong> : PHP 8.3+, Laravel 13, MySQL 8.</li>
            <li><strong>Frontend</strong> : Blade, Bootstrap 5, Alpine.js, Vite.</li>
            <li><strong>Qualité</strong> : PHPUnit, Laravel Pint, GitHub Actions CI/CD.</li>
            <li><strong>Sécurité</strong> : RBAC, policies, CSRF, IDOR protection, SoftDeletes.</li>
        </ul>
    </div>

    <div class="section">
        <h2>4. Modules et rôles</h2>
        <p>Les rôles (owner, manager, commercial, finance, reader) contrôlent l'accès aux modules CRM, Ventes et RH. Chaque module est protégé par des middlewares et des policies.</p>
    </div>

    <div class="section">
        <h2>5. Conclusion</h2>
        <p>NexaGestion est une solution complète et sécurisée pour la gestion d'entreprise, prête pour la production.</p>
    </div>
</body>
</html>