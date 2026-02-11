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
                        <a class="nav-link {{ request()->routeIs('properties.index') && request('type_annonce') == 'location' ? 'active' : '' }}"
                            href="{{ route('properties.index', ['type_annonce' => 'location']) }}">
                            <i class="ri-key-line"></i> Location
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('properties.index') && request('type_annonce') == 'vente' ? 'active' : '' }}"
                            href="{{ route('properties.index', ['type_annonce' => 'vente']) }}">
                            <i class="ri-shopping-bag-line"></i> Vente
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('properties.index') && !request('type_annonce') ? 'active' : '' }}" 
                            href="{{ route('properties.index') }}">
                            <i class="ri-building-line"></i> Tous les biens
                        </a>
                    </li>
                </ul>

                <div class="d-flex gap-3 align-items-center">
                    <!-- Contact Button -->
                    {{-- <a href="mailto:{{$data_parametre?->email_principal}}" class="btn btn-outline-primary d-none d-lg-flex align-items-center gap-2"
                        style="border-radius: 25px; padding: 8px 20px;">
                        <i class="ri-mail-line"></i>
                        <span>Contact</span>
                    </a> --}}

                    @auth
                        <div class="dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" 
                                href="#" role="button" data-bs-toggle="dropdown"
                                style="background: linear-gradient(135deg, #43542A 0%, #2d3a1c 100%); 
                                       color: white; padding: 8px 20px; border-radius: 25px;">
                                <i class="ri-user-line"></i> 
                                <span>{{ Auth::user()->username }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow-lg" style="border-radius: 12px; border: none;">
                                @if (Auth::user()->hasRole('admin'))
                                    <li>
                                        <a class="dropdown-item py-2" href="{{ route('dashboard.index') }}">
                                            <i class="ri-dashboard-line me-2"></i> Dashboard Admin
                                        </a>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                @else
                                    <li>
                                        <a class="dropdown-item py-2" href="{{ route('client.dashboard') }}">
                                            <i class="ri-dashboard-line me-2"></i> Mon Espace
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item py-2" href="{{ route('client.profil') }}">
                                            <i class="ri-user-settings-line me-2"></i> Mon Profil
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item py-2" href="{{ route('client.demandes') }}">
                                            <i class="ri-message-3-line me-2"></i> Mes Demandes
                                        </a>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                @endif
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item py-2 text-danger">
                                            <i class="ri-logout-box-line me-2"></i> Déconnexion
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        {{-- <a href="{{ route('login') }}" class="btn btn-outline-primary">
                            <i class="ri-login-box-line"></i> Connexion
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-accent">
                            <i class="ri-user-add-line"></i> Inscription
                        </a> --}}
                        
                        <!-- Mobile: Show icon only -->
                        <a href="tel:{{$data_parametre?->contact_principal}}" class="btn btn-accent d-lg-none" 
                            style="border-radius: 50%; width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;">
                            <i class="ri-phone-line fs-5"></i>
                        </a>
                        
                        <!-- Desktop: Show full button -->
                        <a href="tel:{{$data_parametre?->contact_principal}}" class="btn btn-accent d-none d-lg-flex align-items-center gap-2" 
                            style="border-radius: 25px; padding: 10px 25px; font-weight: 500;">
                            <i class="ri-phone-line"></i>
                            <span>Appelez-nous</span>
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
