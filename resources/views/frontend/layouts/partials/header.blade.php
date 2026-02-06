<header class="site-header">
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <!--logo image-->
                <img src="{{ $data_parametre ? URL::asset($data_parametre?->getFirstMediaUrl('logo_header')) : URL::asset('images/camera-icon.png') }}"
                    alt="Sage Immo Logo" width="auto" height="70">
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                            <i class="ri-home-4-line"></i> Accueil
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('properties.index') ? 'active' : '' }}"
                            href="{{ route('properties.index', ['type_annonce' => 'location']) }}">
                            <i class="ri-key-line"></i> Location
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('properties.index') ? 'active' : '' }}"
                            href="{{ route('properties.index', ['type_annonce' => 'vente']) }}">
                            <i class="ri-shopping-bag-line"></i> Vente
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('properties.index') }}">
                            <i class="ri-building-line"></i> Tous les biens
                        </a>
                    </li>
                </ul>

                <div class="d-flex gap-2 align-items-center">
                    @auth
                        <div class="dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="ri-user-line"></i> {{ Auth::user()->username }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                @if (Auth::user()->hasRole('admin'))
                                    <li>
                                        <a class="dropdown-item" href="{{ route('dashboard.index') }}">
                                            <i class="ri-dashboard-line"></i> Dashboard Admin
                                        </a>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                @else
                                    <li>
                                        <a class="dropdown-item" href="{{ route('client.dashboard') }}">
                                            <i class="ri-dashboard-line"></i> Mon Espace
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('client.profil') }}">
                                            <i class="ri-user-settings-line"></i> Mon Profil
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('client.demandes') }}">
                                            <i class="ri-message-3-line"></i> Mes Demandes
                                        </a>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                @endif
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="ri-logout-box-line"></i> Déconnexion
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-primary">
                            <i class="ri-login-box-line"></i> Connexion
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-accent">
                            <i class="ri-user-add-line"></i> Inscription
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show m-0 rounded-0 alert-front" role="alert">
            <div class="container">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show m-0 rounded-0 alert-front" role="alert">
            <div class="container">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    <!--script to auto-hide alerts after 5 seconds-->
    <script>
        // faire disparaitre les alertes apres 5 secondes
        window.setTimeout(function() {
            const alerts = document.querySelectorAll('.alert-front');
            alerts.forEach(function(alert) {
                // Fade out avec transition
                alert.style.transition = 'opacity 0.5s ease-in-out';
                alert.style.opacity = '0';

                // Slide up et suppression après le fade
                setTimeout(function() {
                    alert.style.transition = 'all 0.5s ease-in-out';
                    alert.style.maxHeight = '0';
                    alert.style.padding = '0';
                    alert.style.margin = '0';
                    alert.style.overflow = 'hidden';

                    // Supprimer complètement l'élément après l'animation
                    setTimeout(function() {
                        alert.remove();
                    }, 500);
                }, 500);
            });
        }, 5000);
    </script>

    {{-- @include('backend.components.alertMessage') --}}
</header>
