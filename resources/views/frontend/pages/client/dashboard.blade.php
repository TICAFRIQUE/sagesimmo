@extends('frontend.pages.client.layout')

@section('client-content')
<div class="client-content">
    <h2 class="mb-4">
        <i class="ri-dashboard-line"></i> Tableau de bord
    </h2>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="ri-file-list-line"></i>
                </div>
                <div class="stat-value">{{ $totalDemandes }}</div>
                <div class="stat-label">Total demandes</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card warning">
                <div class="stat-icon">
                    <i class="ri-loader-line"></i>
                </div>
                <div class="stat-value">{{ $demandesEnCours }}</div>
                <div class="stat-label">En cours</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card success">
                <div class="stat-icon">
                    <i class="ri-check-double-line"></i>
                </div>
                <div class="stat-value">{{ $demandesFinalisees }}</div>
                <div class="stat-label">Finalisées</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card info">
                <div class="stat-icon">
                    <i class="ri-calendar-line"></i>
                </div>
                <div class="stat-value">{{ $demandesVisites }}</div>
                <div class="stat-label">Visites programmées</div>
            </div>
        </div>
    </div>

    <!-- Prochaines visites -->
    @if($prochainesVisites->count() > 0)
        <div class="mb-4">
            <h4 class="mb-3">
                <i class="ri-calendar-event-line"></i> Prochaines visites
            </h4>
            @foreach($prochainesVisites as $visite)
                <div class="visite-card">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h5 class="mb-2">{{ $visite->annonce->titre }}</h5>
                            <p class="text-muted mb-2">
                                <i class="ri-map-pin-line"></i> 
                                {{ $visite->annonce->quartier }}, {{ $visite->annonce->ville }}
                            </p>
                            <p class="mb-0">
                                <i class="ri-calendar-line"></i> 
                                <strong>{{ \Carbon\Carbon::parse($visite->date_visite)->format('d/m/Y à H:i') }}</strong>
                            </p>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-info badge-lg">Visite programmée</span>
                            <div class="mt-2">
                                <a href="{{ route('client.demandes.show', $visite->id) }}" class="btn btn-sm btn-outline-primary">
                                    Voir détails
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Dernières demandes -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">
                <i class="ri-message-3-line"></i> Mes dernières demandes
            </h4>
            <a href="{{ route('client.demandes') }}" class="btn btn-sm btn-outline-primary">
                <i class="ri-eye-line"></i> Voir toutes mes demandes
            </a>
        </div>

        @if($dernieresDemandes->count() > 0)
            <div class="row">
                @foreach($dernieresDemandes as $demande)
                    <div class="col-12 mb-3">
                        <div class="card shadow-sm hover-lift">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-2">
                                        @if($demande->annonce->hasMedia('image_principale'))
                                            <img src="{{ $demande->annonce->getFirstMediaUrl('image_principale') }}" 
                                                 class="img-fluid rounded" 
                                                 alt="{{ $demande->annonce->titre }}"
                                                 style="height: 80px; object-fit: cover; width: 100%;">
                                        @else
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 80px;">
                                                <i class="ri-image-line fs-2 text-muted"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-md-5">
                                        <h6 class="mb-1 fw-bold">{{ $demande->annonce->titre }}</h6>
                                        <div class="d-flex flex-wrap gap-2 mb-2">
                                            <small class="text-muted">
                                                <i class="ri-file-line"></i> {{ $demande->annonce->reference }}
                                            </small>
                                            <small class="text-muted">
                                                <i class="ri-map-pin-line"></i> {{ $demande->annonce->ville }}
                                            </small>
                                        </div>
                                        <small class="text-muted">
                                            <i class="ri-calendar-line"></i> 
                                            Demandé le {{ $demande->created_at->format('d/m/Y à H:i') }}
                                        </small>
                                    </div>
                                    <div class="col-md-2 text-center">
                                        {!! $demande->statut_badge !!}
                                        <div class="progress mt-2" style="height: 8px;">
                                            <div class="progress-bar bg-success" 
                                                 role="progressbar" 
                                                 style="width: {{ $demande->progression }}%"
                                                 aria-valuenow="{{ $demande->progression }}" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="100"></div>
                                        </div>
                                        <small class="text-muted">{{ $demande->progression }}% complété</small>
                                    </div>
                                    <div class="col-md-3 text-end">
                                        <a href="{{ route('client.demandes.show', $demande->id) }}" 
                                           class="btn btn-sm btn-primary w-100 mb-2">
                                            <i class="ri-eye-line"></i> Voir le suivi détaillé
                                        </a>
                                        @if($demande->date_visite && $demande->statut == 'visite_planifiee')
                                            <div class="alert alert-info py-2 px-2 mb-0 small">
                                                <i class="ri-calendar-event-line"></i>
                                                Visite: {{ \Carbon\Carbon::parse($demande->date_visite)->format('d/m/Y H:i') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="alert alert-info">
                <i class="ri-information-line"></i> 
                Vous n'avez pas encore de demandes. 
                <a href="{{ route('properties.index') }}" class="alert-link fw-bold">Parcourez nos biens</a> pour commencer votre recherche !
            </div>
        @endif
    </div>

    <!-- Actions rapides -->
    <div class="card border-primary">
        <div class="card-body text-center">
            <h5 class="mb-3">
                <i class="ri-lightbulb-line"></i> Prêt à trouver votre bien idéal ?
            </h5>
            <a href="{{ route('properties.index') }}" class="btn btn-primary">
                <i class="ri-search-line"></i> Parcourir les biens disponibles
            </a>
        </div>
    </div>
</div>

<style>
    .hover-lift {
        transition: all 0.3s ease;
    }
    
    .hover-lift:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
    
    .stat-card {
        background: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.12);
    }
    
    .stat-card.warning {
        border-left: 4px solid #f1b44c;
    }
    
    .stat-card.success {
        border-left: 4px solid #0ab39c;
    }
    
    .stat-card.info {
        border-left: 4px solid #299cdb;
    }
    
    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: #f3f6f9;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin-bottom: 15px;
        color: #405189;
    }
    
    .stat-card.warning .stat-icon {
        background: #fff4e4;
        color: #f1b44c;
    }
    
    .stat-card.success .stat-icon {
        background: #e3f9f5;
        color: #0ab39c;
    }
    
    .stat-card.info .stat-icon {
        background: #e3f4fb;
        color: #299cdb;
    }
    
    .stat-value {
        font-size: 28px;
        font-weight: 700;
        color: #212529;
        margin-bottom: 5px;
    }
    
    .stat-label {
        color: #878a99;
        font-size: 14px;
    }
    
    .visite-card {
        background: #fff;
        border-left: 4px solid #299cdb;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 15px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
    }
    
    .visite-card:hover {
        transform: translateX(5px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.12);
    }
    
    .badge-lg {
        font-size: 14px;
        padding: 8px 12px;
    }
</style>
@endsection
