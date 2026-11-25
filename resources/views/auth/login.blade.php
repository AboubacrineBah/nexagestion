<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion · NexaGestion</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>

<body class="d-flex align-items-center min-vh-100">
    <main class="container">
        <div class="row justify-content-center">
            <div class="col-sm-10 col-md-6 col-lg-4">
                <section class="card p-4 p-md-5">
                    <div class="app-brand text-dark mb-4">
                        <span class="app-brand-mark">N</span>NexaGestion<small
                            class="d-block fw-normal text-secondary mt-1">par NexaWeb Mali</small>
                    </div>
                    <h1 class="h4 page-heading">Connexion</h1>
                    <p class="text-secondary mb-4">Accédez à l’espace de gestion de votre entreprise.</p>
                    @if($errors->any())
                        <div class="alert alert-danger" role="alert">{{ $errors->first() }}</div>
                    @endif
                    @if(session('status'))
                        <div class="alert alert-success" role="status">{{ session('status') }}</div>
                    @endif
                    <form method="post" action="{{ route('login.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label" for="email">Adresse e-mail</label>
                            <input class="form-control" id="email" name="email" type="email" value="{{ old('email') }}"
                                autocomplete="username" required autofocus>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="password">Mot de passe</label>
                            <input class="form-control" id="password" name="password" type="password"
                                autocomplete="current-password" required>
                        </div>
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" name="remember" value="1" id="remember">
                            <label class="form-check-label" for="remember">Rester
                                connecté</label>
                        </div>
                        <button class="btn btn-primary w-100" type="submit">Se
                            connecter</button>
                        <div class="text-start mt-3 mb-3">
                            <a class="small" href="{{ route('password.request') }}">Mot de passe
                                oublié ?</a>
                        </div>
                    </form>
                </section>
                <p class="text-center small text-secondary mt-3">Les accès sont créés par l’administrateur de
                    l’entreprise.</p>
            </div>
        </div>
    </main>
</body>

</html>
