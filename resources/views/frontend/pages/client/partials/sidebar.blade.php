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
        
        @php
            $user = Auth::user();
            $nombreBiensProprio = \App\Models\Annonce::where('proprietaire_id', $user->id)->count();
            $locationActive = \App\Models\Location::where('locataire_id', $user->id)
                ->where('statut', 'actif')
                ->exists();
            $venteActive = \App\Models\Vente::where('client_id', $user->id)
                ->whereIn('statut', ['demande_client', 'fiche_envoyee', 'visite_planifiee', 'offre_acceptee', 'terminee'])
                ->exists();
            
            $estProprietaire = ($user->roles->contains('name', 'proprietaire')) || $nombreBiensProprio > 0;
            $estLocataire = ($user->roles->contains('name', 'locataire')) || $locationActive;
            $estAcheteur = ($user->roles->contains('name', 'acheteur')) || $venteActive;
        @endphp
        
        @if($estProprietaire)
            <a href="{{ route('client.proprietaire') }}" 
               class="nav-link d-flex align-items-center {{ request()->routeIs('client.proprietaire') ? 'active' : '' }}">
                <i class="ri-building-line"></i>
                <span class="flex-grow-1">Espace Propriétaire</span>
                @if($nombreBiensProprio > 0)
                    <span class="badge bg-success rounded-pill">{{ $nombreBiensProprio }}</span>
                @endif
            </a>
        @endif
        
        @if($estLocataire)
            <a href="{{ route('client.locataire') }}" 
               class="nav-link {{ request()->routeIs('client.locataire') ? 'active' : '' }}">
                <i class="ri-home-heart-line"></i>
                <span>Espace Locataire</span>
            </a>
        @endif
        
        @if($estAcheteur)
            <a href="{{ route('client.acheteur') }}" 
               class="nav-link {{ request()->routeIs('client.acheteur*') ? 'active' : '' }}">
                <i class="ri-shopping-bag-3-line"></i>
                <span>Espace Acheteur</span>
            </a>
        @endif
        
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
                $demandesEnCours = \App\Models\Vente::where('client_id', Auth::id())
                    ->whereIn('statut', ['demande_client', 'brouillon', 'fiche_envoyee', 'visite_planifiee', 'offre_acceptee'])
                    ->count() + 
                    \App\Models\Location::where('locataire_id', Auth::id())
                    ->whereIn('statut', ['demande_client', 'brouillon', 'fiche_envoyee', 'visite_planifiee', 'paiement_initial', 'en_attente_echeances'])
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
