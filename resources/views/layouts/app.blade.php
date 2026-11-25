<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#1f3a5f">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.2/font/bootstrap-icons.min.css">
    <title>@yield('title', 'Espace de travail') · NexaGestion</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>

<body>
    <div class="container-fluid">
        <div class="row min-vh-100">
            <aside class="col-md-3 col-xl-2 app-sidebar p-3 d-flex flex-column">
                <a class="app-brand text-decoration-none d-flex align-items-center py-2 mb-4"
                    href="{{ route('dashboard') }}"><span class="app-brand-mark">N</span><span>NexaGestion<small
                            class="d-block fw-normal text-white-50">par NexaWeb Mali</small></span></a>
                <div class="small text-uppercase text-white-50 fw-bold px-2 mb-2"> <i class="bi bi-person-workspace"></i> Espace de travail</div>
                <nav class="sidebar-nav nav flex-column">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                        href="{{ route('dashboard') }}"><i class="bi bi-speedometer2"></i> Tableau de bord</a>
                    <a class="nav-link {{ request()->routeIs('audit-events.*') ? 'active' : '' }}"
                        href="{{ route('audit-events.index') }}"><i class="bi bi-journal-text"></i> Journal d’audit</a>
                    <a class="nav-link {{ request()->routeIs('clients.*') ? 'active' : '' }}"
                        href="{{ route('clients.index') }}"><i class="bi bi-people"></i> CRM · Contacts</a>
                    <a class="nav-link {{ request()->routeIs('opportunities.*') ? 'active' : '' }}"
                        href="{{ route('opportunities.index') }}"><i class="bi bi-funnel"></i> CRM · Opportunités</a>
                    <a class="nav-link {{ request()->routeIs('sales.*') ? 'active' : '' }}"
                        href="{{ route('sales.index') }}"><i class="bi bi-receipt"></i> Devis et factures</a>
                    <a class="nav-link {{ request()->routeIs('payments.*') ? 'active' : '' }}"
                        href="{{ route('payments.index') }}"><i class="bi bi-cash-coin"></i> Paiements</a>
                    @if(auth()->user()->canAccessModule('hr'))
                        <a class="nav-link {{ request()->routeIs('employees.*') ? 'active' : '' }}"
                            href="{{ route('employees.index') }}"><i class="bi bi-person-badge"></i> RH · Employés</a>
                        <a class="nav-link {{ request()->routeIs('payroll.*') ? 'active' : '' }}"
                            href="{{ route('payroll.index') }}"><i class="bi bi-cash-stack"></i> RH · Paie</a>
                    @endif
                    @if(auth()->user()->canAccessModule('sales'))
                        <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}"
                            href="{{ route('products.index') }}"><i class="bi bi-box-seam"></i> Stock · Produits</a>
                    @endif
                </nav>
                <div class="mt-auto border-top border-light border-opacity-10 pt-3">
                    <div class="text-white fw-semibold small"> <i class="bi bi-building"></i>
                        {{ auth()->user()->company->name }}
                    </div>
                    <div class="text-white-50 small mb-3"> <i class="bi bi-person"></i> {{ auth()->user()->name }} ·
                        {{ ucfirst(auth()->user()->role) }}
                    </div>
                    <form method="post" action="{{ route('logout') }}">@csrf<button
                            class="btn btn-sm btn-outline-light w-100" type="submit"><i
                                class="bi bi-box-arrow-left me-1"></i> Se déconnecter</button></form>
                </div>
            </aside>
            <main class="col-md-9 col-xl-10 app-main px-0">
                <header class="app-topbar d-flex align-items-center justify-content-between px-4 py-3">
                    <div class="text-secondary small"> <i class="bi bi-building"></i>
                        {{ auth()->user()->company->name }} <span class="mx-2">/</span>
                        <strong class="text-dark">@yield('section', 'Vue générale')</strong>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge text-bg-light">
                            {{ auth()->user()->company->currency }}
                        </span>

                        <time id="gmt-clock" class="badge text-bg-light">
                            Chargement...
                        </time>
                    </div>
                </header>
                <div class="content-wrap">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="status">
                            {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"
                                aria-label="Fermer"></button>
                    </div>@endif
                    @if($errors->any())
                        <div class="alert alert-danger" role="alert"><strong>Vérifiez les informations saisies.</strong>
                            <ul class="mb-0 mt-2">@foreach($errors->all() as $error)
                            <li>{{ $error }}</li>@endforeach
                            </ul>
                    </div>@endif
                    @yield('content')
                </div>
            </main>
        </div>
    </div>
</body>

</html>
