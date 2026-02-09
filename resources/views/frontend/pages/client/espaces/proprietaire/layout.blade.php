@extends('frontend.pages.client.layout')

@section('title', 'Espace Propriétaire')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <h4 class="mb-3">
                <i class="ri-building-line text-success"></i> Mon espace Propriétaire
            </h4>
            
            <!-- Navigation par onglets -->
            <ul class="nav nav-tabs mb-4" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ request()->routeIs('client.proprietaire') ? 'active' : '' }}" 
                       href="{{ route('client.proprietaire') }}"
                       aria-selected="{{ request()->routeIs('client.proprietaire') ? 'true' : 'false' }}">
                        <i class="ri-dashboard-line me-1"></i>
                        <span class="d-none d-sm-inline">Vue d'ensemble</span>
                        <span class="d-sm-none">Accueil</span>
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ request()->routeIs('client.proprietaire.locations') ? 'active' : '' }}" 
                       href="{{ route('client.proprietaire.locations') }}"
                       aria-selected="{{ request()->routeIs('client.proprietaire.locations') ? 'true' : 'false' }}">
                        <i class="ri-home-smile-line me-1"></i>
                        <span class="d-none d-sm-inline">Locations</span>
                        <span class="d-sm-none">Locat.</span>
                        @if(isset($loyersImpayesGlobal) && $loyersImpayesGlobal > 0)
                            <span class="badge bg-danger ms-1">!</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ request()->routeIs('client.proprietaire.ventes') ? 'active' : '' }}" 
                       href="{{ route('client.proprietaire.ventes') }}"
                       aria-selected="{{ request()->routeIs('client.proprietaire.ventes') ? 'true' : 'false' }}">
                        <i class="ri-shopping-bag-line me-1"></i>
                        <span class="d-none d-sm-inline">Ventes</span>
                        <span class="d-sm-none">Ventes</span>
                        @if(isset($ventesEnCoursGlobal) && $ventesEnCoursGlobal > 0)
                            <span class="badge bg-primary ms-1">{{ $ventesEnCoursGlobal }}</span>
                        @endif
                    </a>
                </li>
                {{-- <li class="nav-item" role="presentation">
                    <a class="nav-link {{ request()->routeIs('client.proprietaire.historique') ? 'active' : '' }}" 
                       href="{{ route('client.proprietaire.historique') }}"
                       aria-selected="{{ request()->routeIs('client.proprietaire.historique') ? 'true' : 'false' }}">
                        <i class="ri-history-line me-1"></i>
                        <span class="d-none d-sm-inline">Historique</span>
                        <span class="d-sm-none">Histo.</span>
                    </a>
                </li> --}}
            </ul>
        </div>
    </div>

    <!-- Contenu de l'onglet -->
    @yield('tab-content')
</div>

@endsection

@section('styles')
<style>
    .nav-tabs {
        border-bottom: 2px solid #dee2e6;
        flex-wrap: nowrap;
        overflow-x: auto;
        overflow-y: hidden;
        -webkit-overflow-scrolling: touch;
    }
    
    .nav-tabs::-webkit-scrollbar {
        height: 4px;
    }
    
    .nav-tabs::-webkit-scrollbar-thumb {
        background-color: rgba(0,0,0,0.2);
        border-radius: 4px;
    }
    
    .nav-tabs .nav-link {
        border: none;
        border-bottom: 3px solid transparent;
        color: #64748b;
        font-weight: 500;
        padding: 0.75rem 1rem;
        white-space: nowrap;
        transition: all 0.3s ease;
    }
    
    .nav-tabs .nav-link:hover {
        border-bottom-color: rgba(67, 84, 42, 0.3);
        color: var(--primary-color);
    }
    
    .nav-tabs .nav-link.active {
        border-bottom-color: var(--primary-color);
        color: var(--primary-color);
        background-color: transparent;
    }
    
    .nav-tabs .nav-link i {
        font-size: 1.1rem;
    }
    
    @media (max-width: 576px) {
        .nav-tabs {
            border-bottom: 1px solid #dee2e6;
        }
        
        .nav-tabs .nav-link {
            padding: 0.5rem 0.75rem;
            font-size: 0.85rem;
        }
        
        .nav-tabs .nav-link i {
            font-size: 1rem;
        }
        
        .badge {
            font-size: 0.7rem;
            padding: 0.15rem 0.4rem;
        }
    }
</style>
@endsection

@section('scripts')
@yield('tab-scripts')
@endsection
