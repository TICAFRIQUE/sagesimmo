<!-- ========== App Menu ========== -->
<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        @if ($data_parametre != null)
            <a href="#" class="logo logo-light">
                <span class="logo-lg">
                    <img src="{{ $data_parametre ? URL::asset($data_parametre?->getFirstMediaUrl('logo_header')) : URL::asset('images/camera-icon.png') }}"
                        alt="logo" width="auto" class="rounded-circle" height="60">
                </span>
            </a>
        @endif

        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover"
            id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>



    <div id="scrollbar">
        <div class="container-fluid">

            <div id="two-column-menu">
            </div>
            {{-- @if (Auth::user()->role == 'superadmin' || Auth::user()->role == 'developpeur')
            @endif --}}
            <ul class="navbar-nav" id="navbar-nav">

                @can('voir-tableau de bord')
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ Route::is('dashboard.*') ? 'active' : '' }} "
                            href="{{ route('dashboard.index') }}">
                            <i class="ri-dashboard-2-line"></i> <span>TABLEAU DE BORD</span>
                        </a>
                    </li>
                @endcan

                <!-- Alertes et Retards -->
                <li class="nav-item">
                    <a class="nav-link menu-link {{ Route::is('backend.alertes.*') ? 'active' : '' }}" 
                        href="{{ route('backend.alertes.index') }}">
                        <i class="ri-alarm-warning-line"></i> 
                        <span>Alertes et Retards</span>
                        @php
                            $nbAlertes = \App\Models\Echeance::enRetard()->count();
                        @endphp
                        @if($nbAlertes > 0)
                            <span class="badge bg-danger rounded-pill ms-1">{{ $nbAlertes }}</span>
                        @endif
                    </a>
                </li>

                <!-- Configuration Immobilière -->
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarImmobilier" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="sidebarImmobilier">
                        <i class="ri-building-4-line me-2"></i> <span>Configuration</span>
                    </a>
                    <div class="collapse menu-dropdown {{ Route::is('backend.type-biens.*') || Route::is('backend.equipements.*') ? 'show' : '' }}"
                        id="sidebarImmobilier">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ route('backend.type-biens.index') }}"
                                    class="nav-link {{ Route::is('backend.type-biens.*') ? 'active' : '' }}">
                                    <i class="ri-home-3-line me-2"></i> Types de biens
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('backend.equipements.index') }}"
                                    class="nav-link {{ Route::is('backend.equipements.*') ? 'active' : '' }}">
                                    <i class="ri-tools-line me-2"></i> Équipements
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>


                <!-- Annonces -->
                {{-- @can('voir-annonce') --}}
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ Route::is('backend.annonces.*') ? 'active' : '' }} "
                            href="{{ route('backend.annonces.index') }}">
                            <i class="ri-home-4-line"></i> <span>Annonces</span>
                        </a>
                    </li>
                {{-- @endcan --}}

                <!-- Utilisateurs -->
                <li class="nav-item">
                    <a class="nav-link menu-link {{ Route::is('backend.users.*') ? 'active' : '' }} "
                        href="{{ route('backend.users.index') }}">
                        <i class="ri-group-line"></i> <span>Clients</span>
                    </a>
                </li>

                <!-- Notifications -->
                {{-- <li class="nav-item">
                    <a class="nav-link menu-link {{ Route::is('backend.notifications.*') ? 'active' : '' }}" 
                        href="{{ route('backend.notifications.index') }}">
                        <i class="ri-notification-3-line"></i> 
                        <span>Notifications</span>
                        @if(auth()->user()->unreadNotifications->count() > 0)
                            <span class="badge rounded-pill bg-danger ms-auto">{{ auth()->user()->unreadNotifications->count() }}</span>
                        @endif
                    </a>
                </li> --}}

                <!-- Suivi Location/Vente -->
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarSuivi" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="sidebarSuivi">
                        <i class="ri-line-chart-line me-2"></i> 
                        <span>Demandes Location/Vente</span>
                        @php
                            $nouvellesDemandes = \App\Models\Vente::where('statut', 'demande_client')->count() + 
                                                 \App\Models\Location::where('statut', 'demande_client')->count();
                        @endphp
                        @if($nouvellesDemandes > 0)
                            <span class="badge rounded-pill bg-danger ms-auto">{{ $nouvellesDemandes }}</span>
                        @endif
                    </a>
                    <div class="collapse menu-dropdown {{ Route::is('backend.ventes.*') || Route::is('backend.locations.*') ? 'show' : '' }}"
                        id="sidebarSuivi">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ route('backend.ventes.index') }}"
                                    class="nav-link {{ Route::is('backend.ventes.*') ? 'active' : '' }}">
                                    <i class="ri-shopping-bag-line me-2"></i> Ventes
                                    @php
                                        $nouvellesVentes = \App\Models\Vente::where('statut', 'demande_client')->count();
                                    @endphp
                                    @if($nouvellesVentes > 0)
                                        <span class="badge rounded-pill bg-danger ms-2">{{ $nouvellesVentes }}</span>
                                    @endif
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('backend.locations.index') }}"
                                    class="nav-link {{ Route::is('backend.locations.*') ? 'active' : '' }}">
                                    <i class="ri-key-line me-2"></i> Locations
                                    @php
                                        $nouvellesLocations = \App\Models\Location::where('statut', 'demande_client')->count();
                                    @endphp
                                    @if($nouvellesLocations > 0)
                                        <span class="badge rounded-pill bg-danger ms-2">{{ $nouvellesLocations }}</span>
                                    @endif
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- Rapports & Statistiques -->
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarRapports" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="sidebarRapports">
                        <i class="ri-bar-chart-box-line me-2"></i> <span>Rapports & Historiques</span>
                    </a>
                    <div class="collapse menu-dropdown {{ Route::is('backend.versements.*') ||Route::is('backend.rapports.*') || Route::is('rapports.*') || Route::is('backend.charges.*') ? 'show' : '' }}"
                        id="sidebarRapports">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ route('backend.rapports.commissions') }}"
                                    class="nav-link {{ Route::is('backend.rapports.commissions') ? 'active' : '' }}">
                                    <i class="ri-percent-line me-2"></i> Commissions
                                </a>
                            </li>
                            {{-- <li class="nav-item">
                                <a href="{{ route('backend.rapports.statistiques') }}"
                                    class="nav-link {{ Route::is('backend.rapports.statistiques') ? 'active' : '' }}">
                                    <i class="ri-line-chart-line me-2"></i> Statistiques
                                </a>
                            </li> --}}
                            <li class="nav-item">
                                <a href="{{ route('backend.rapports.proprietaire') }}"
                                    class="nav-link {{ Route::is('backend.rapports.proprietaire') ? 'active' : '' }}">
                                    <i class="ri-home-heart-line me-2"></i> Rapport Propriétaire
                                </a>
                            </li>
                            {{-- <li class="nav-item">
                                <a href="{{ route('backend.rapports.agence') }}"
                                    class="nav-link {{ Route::is('backend.rapports.agence') ? 'active' : '' }}">
                                    <i class="ri-building-2-line me-2"></i> Rapport Agence
                                </a>
                            </li> --}}
                            <li class="nav-item">
                                <a href="{{ route('backend.charges.index') }}"
                                    class="nav-link {{ Route::is('backend.charges.*') ? 'active' : '' }}">
                                    <i class="ri-tools-line me-2"></i> Gestion des Charges
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('backend.versements.index') }}"
                                    class="nav-link {{ Route::is('backend.versements.*') ? 'active' : '' }}">
                                    <i class="ri-money-dollar-circle-line me-2"></i> Gestion des Versements
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>




                @if (Auth::user()->role == 'superadmin' || Auth::user()->role == 'developpeur' || Auth::user()->can('voir-parametre'))
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarAuth" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="sidebarAuth">
                            <i class="ri-settings-2-fill me-2"></i> <span>Paramètres</span>
                        </a>
                        <div class="collapse menu-dropdown {{ Route::is('role.*') || Route::is('parametre.*') || Route::is('module.*') || Route::is('permission.*') || Route::is('admin-register.*') ? 'show' : '' }}"
                            id="sidebarAuth">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="{{ route('parametre.index') }}"
                                        class="nav-link {{ Route::is('parametre.*') ? 'active' : '' }}">
                                        <i class="ri-information-line me-2"></i> Informations
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin-register.index') }}"
                                        class="nav-link {{ Route::is('admin-register.*') ? 'active' : '' }}">
                                        <i class="ri-user-settings-line me-2"></i> Utilisateurs
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('module.index') }}"
                                        class="nav-link {{ Route::is('module.*') ? 'active' : '' }}">
                                        <i class="ri-apps-2-line me-2"></i> Modules
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('role.index') }}"
                                        class="nav-link {{ Route::is('role.*') ? 'active' : '' }}">
                                        <i class="ri-user-star-line me-2"></i> Rôles
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('permission.index') }}"
                                        class="nav-link {{ Route::is('permission.*') ? 'active' : '' }}">
                                        <i class="ri-key-2-line me-2"></i> Permissions / Rôles
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endif

            </ul>
        </div>
        <!-- Sidebar -->
    </div>
    <div class="sidebar-background"></div>
</div>
<!-- Left Sidebar End -->
<!-- Vertical Overlay-->
<div class="vertical-overlay"></div>
