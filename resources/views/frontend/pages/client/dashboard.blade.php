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
                <i class="ri-message-3-line"></i> Dernières demandes
            </h4>
            <a href="{{ route('client.demandes') }}" class="btn btn-sm btn-outline-primary">
                Voir toutes
            </a>
        </div>

        @if($dernieresDemandes->count() > 0)
            @foreach($dernieresDemandes as $demande)
                <div class="demande-card">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h6 class="mb-1">{{ $demande->annonce->titre }}</h6>
                            <small class="text-muted">
                                Réf: {{ $demande->annonce->reference }} | 
                                {{ $demande->created_at->format('d/m/Y') }}
                            </small>
                        </div>
                        <div class="col-md-3 text-center">
                            {!! $demande->statut_badge !!}
                        </div>
                        <div class="col-md-3 text-end">
                            <a href="{{ route('client.demandes.show', $demande->id) }}" 
                               class="btn btn-sm btn-outline-primary">
                                <i class="ri-eye-line"></i> Détails
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="alert alert-info">
                <i class="ri-information-line"></i> 
                Vous n'avez pas encore de demandes. 
                <a href="{{ route('properties.index') }}">Parcourez nos biens</a> pour commencer.
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
@endsection
