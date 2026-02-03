<div class="client-sidebar">
    <div class="text-center mb-4">
        <div class="avatar-lg mx-auto mb-3">
            <div class="avatar-title bg-soft-primary text-primary rounded-circle fs-1">
                {{ strtoupper(substr(Auth::user()->username, 0, 1)) }}
            </div>
        </div>
        <h5 class="mb-1">{{ Auth::user()->username }}</h5>
        <p class="text-muted mb-0">{{ Auth::user()->email }}</p>
    </div>

    <hr class="my-3">

    <nav class="nav flex-column">
        <a href="{{ route('client.dashboard') }}" 
           class="nav-link {{ request()->routeIs('client.dashboard') ? 'active' : '' }}">
            <i class="ri-dashboard-line"></i>
            <span>Tableau de bord</span>
        </a>
        <a href="{{ route('client.profil') }}" 
           class="nav-link {{ request()->routeIs('client.profil') ? 'active' : '' }}">
            <i class="ri-user-settings-line"></i>
            <span>Mon Profil</span>
        </a>
        <a href="{{ route('client.demandes') }}" 
           class="nav-link d-flex align-items-center {{ request()->routeIs('client.demandes*') ? 'active' : '' }}">
            <i class="ri-message-3-line"></i>
            <span class="flex-grow-1">Mes Demandes</span>
            @php
                $demandesEnCours = \App\Models\DemandeInteret::where('user_id', Auth::id())
                    ->whereNotIn('statut', ['cloture', 'paiement_valide'])
                    ->count();
            @endphp
            @if($demandesEnCours > 0)
                <span class="badge bg-danger rounded-pill">{{ $demandesEnCours }}</span>
            @endif
        </a>
        <a href="{{ route('properties.index') }}" class="nav-link">
            <i class="ri-search-line"></i>
            <span>Rechercher un bien</span>
        </a>
    </nav>

    <hr class="my-3">

    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="nav-link text-danger border-0 bg-transparent w-100 text-start">
            <i class="ri-logout-box-line"></i>
            <span>Déconnexion</span>
        </button>
    </form>
</div>
