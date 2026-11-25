<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Proposition hébergement VPS Hetzner</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; margin: 40px; }
        h1 { color: #1f3a5f; }
        h2 { color: #2c5282; margin-top: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background-color: #f2f2f2; }
        .total { font-weight: bold; }
    </style>
</head>
<body>
    <h1>Proposition de plan d'hébergement VPS — Hetzner</h1>

    <h2>1. Configuration recommandée</h2>
    <table>
        <tr><th>Composant</th><th>Spécification</th></tr>
        <tr><td>Serveur</td><td>Hetzner Cloud CPX21 (3 vCPU, 4 GB RAM, 80 GB SSD)</td></tr>
        <tr><td>Système</td><td>Ubuntu 24.04 LTS</td></tr>
        <tr><td>Base de données</td><td>MySQL 8 (conteneurisé ou natif)</td></tr>
        <tr><td>Reverse proxy</td><td>Nginx + Let's Encrypt SSL</td></tr>
        <tr><td>Cache/Queue</td><td>Redis (optionnel)</td></tr>
    </table>

    <h2>2. Coûts mensuels (FCFA)</h2>
    <table>
        <tr><th>Poste</th><th>Montant (FCFA)</th></tr>
        <tr><td>VPS CPX21 (Hetzner)</td><td>~ 12 000</td></tr>
        <tr><td>Domaine (.com)</td><td>~ 8 000 / an (≈ 667 / mois)</td></tr>
        <tr><td>Sauvegardes (Hetzner Backup)</td><td>~ 2 400</td></tr>
        <tr><td>Monitoring (optionnel)</td><td>~ 3 000</td></tr>
        <tr class="total"><td>Total mensuel estimé</td><td>~ 18 000 FCFA</td></tr>
    </table>

    <h2>3. Mise en production</h2>
    <ul>
        <li>Déploiement via GitHub Actions (CI/CD) vers le VPS.</li>
        <li>Configuration Nginx, PHP-FPM, MySQL.</li>
        <li>Certificat SSL Let's Encrypt automatique.</li>
        <li>Sauvegardes quotidiennes de la base de données.</li>
    </ul>

    <h2>4. Recommandations</h2>
    <p>Pour une montée en charge, prévoir un VPS CPX31 (4 vCPU, 8 GB RAM) et un load balancer. Les coûts restent très compétitifs par rapport aux offres managées.</p>
</body>
</html>