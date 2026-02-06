@extends('frontend.pages.client.layout')

@section('title', 'Espace Locataire')

@section('content')
<div class="container-fluid">
<div class="row mb-3">
    <div class="col-12">
        <h4 class="mb-0">
            <i class="ri-home-heart-line text-info"></i> Mon espace Locataire
        </h4>
    </div>
</div>

@if($locationActive)
    <!-- Informations Bien Loué -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h6 class="mb-0">
                <i class="ri-home-4-line me-2"></i>Mon logement actuel
            </h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-12 col-md-4">
                    @if($locationActive->annonce && $locationActive->annonce->hasMedia('images'))
                        <img src="{{ $locationActive->annonce->getFirstMediaUrl('images') }}" 
                             class="img-fluid rounded" 
                             alt="{{ $locationActive->annonce->titre }}"
                             style="height: 200px; width: 100%; object-fit: cover;">
                    @else
                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 200px;">
                            <i class="ri-home-4-line fs-1 text-muted"></i>
                        </div>
                    @endif
                </div>
                <div class="col-12 col-md-8">
                    <h5 class="mb-3">{{ $locationActive->annonce->titre ?? 'Bien non disponible' }}</h5>
                    <div class="row g-2">
                        <div class="col-md-6 mb-3">
                            <p class="text-muted small mb-1">
                                <i class="ri-map-pin-line me-1"></i>Localisation
                            </p>
                            <p class="mb-0">{{ $locationActive->annonce->ville ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <p class="text-muted small mb-1">
                                <i class="ri-money-dollar-circle-line me-1"></i>Loyer mensuel
                            </p>
                            <h6 class="text-success mb-0">{{ number_format($locationActive->montant_mensuel, 0, ',', ' ') }} FCFA</h6>
                        </div>
                        <div class="col-md-6 mb-3">
                            <p class="text-muted small mb-1">
                                <i class="ri-calendar-line me-1"></i>Date début
                            </p>
                            <p class="mb-0">{{ \Carbon\Carbon::parse($locationActive->date_debut)->format('d/m/Y') }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <p class="text-muted small mb-1">
                                <i class="ri-calendar-check-line me-1"></i>Date fin
                            </p>
                            <p class="mb-0">
                                @if($locationActive->date_fin)
                                    {{ \Carbon\Carbon::parse($locationActive->date_fin)->format('d/m/Y') }}
                                @else
                                    <span class="text-muted">Non définie</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Situation Financière -->
    <div class="row g-2 g-md-3 mb-3 mb-md-4">
        @if($prochaineEcheanceLocataire)
            <div class="col-6 col-md-3">
                <div class="card bg-info-subtle border-0 h-100">
                    <div class="card-body py-2 py-md-3 px-2 px-md-3">
                        <h6 class="text-muted mb-2 text-uppercase small">
                            <i class="ri-calendar-check-line me-1"></i> Prochaine échéance
                        </h6>
                        <h5 class="text-info mb-1 fs-6 fs-md-5">{{ \Carbon\Carbon::parse($prochaineEcheanceLocataire->date_echeance)->format('d/m/Y') }}</h5>
                        <p class="text-muted small mb-0">{{ number_format($prochaineEcheanceLocataire->montant_du, 0, ',', ' ') }} FCFA</p>
                    </div>
                </div>
            </div>
        @endif
        
        @if($avanceRestante > 0)
            <div class="col-6 col-md-3">
                <div class="card bg-success-subtle border-0 h-100">
                    <div class="card-body py-2 py-md-3 px-2 px-md-3">
                        <h6 class="text-muted mb-2 text-uppercase small">
                            <i class="ri-wallet-3-line me-1"></i> Avance restante
                        </h6>
                        <h5 class="text-success mb-0 fs-6 fs-md-5">{{ number_format($avanceRestante, 0, ',', ' ') }} FCFA</h5>
                        <p class="text-muted small mb-0 mt-1">Crédit disponible</p>
                    </div>
                </div>
            </div>
        @endif
        
        <div class="col-md-3">
            <div class="card bg-{{ $impayesLocataire > 0 ? 'danger' : 'light' }}-subtle border-0">
                <div class="card-body">
                    <h6 class="text-muted mb-3 text-uppercase small">
                        <i class="ri-alert-line me-1"></i> Impayés
                    </h6>
                    <h5 class="text-{{ $impayesLocataire > 0 ? 'danger' : 'muted' }} mb-0">
                        {{ number_format($impayesLocataire, 0, ',', ' ') }} FCFA
                    </h5>
                    <p class="text-muted small mb-0 mt-1">
                        @if($impayesLocataire > 0)
                            À régulariser rapidement
                        @else
                            Aucun impayé
                        @endif
                    </p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-light border-0">
                <div class="card-body">
                    <h6 class="text-muted mb-3 text-uppercase small">
                        <i class="ri-shield-check-line me-1"></i> Caution
                    </h6>
                    <h6 class="mb-1">{{ number_format($locationActive->caution ?? 0, 0, ',', ' ') }} FCFA</h6>
                    <p class="text-muted small mb-0 mt-1">{{ $cautionStatut }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertes -->
    @if($impayesLocataire > 0)
        <div class="alert alert-danger d-flex align-items-center mb-4">
            <i class="ri-alert-line fs-3 me-3"></i>
            <div>
                <strong>Attention : Vous avez des impayés</strong>
                <p class="mb-0 small">Veuillez régulariser votre situation au plus vite pour éviter les pénalités.</p>
            </div>
        </div>
    @endif

    <!-- Échéances -->
    @if($echeancesLocataire->count() > 0)
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0">
                    <i class="ri-calendar-event-line me-2"></i>Mes échéances
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="min-width: 100px;">Date</th>
                                <th class="d-none d-md-table-cell" style="min-width: 80px;">Type</th>
                                <th class="text-end" style="min-width: 90px;">Montant</th>
                                <th class="text-end d-none d-lg-table-cell" style="min-width: 90px;">Payé</th>
                                <th class="text-center" style="min-width: 90px;">Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($echeancesLocataire->take(12) as $echeance)
                                <tr>
                                    <td>
                                        <strong class="d-md-none">{{ \Carbon\Carbon::parse($echeance->date_echeance)->format('d/m/y') }}</strong>
                                        <strong class="d-none d-md-inline">{{ \Carbon\Carbon::parse($echeance->date_echeance)->format('d/m/Y') }}</strong>
                                    </td>
                                    <td class="d-none d-md-table-cell">
                                        <span class="badge bg-secondary">{{ ucfirst($echeance->type) }}</span>
                                    </td>
                                    <td class="text-end">
                                        <small class="d-md-none text-muted d-block">Dû</small>
                                        {{ number_format($echeance->montant_du, 0, ',', ' ') }}<small class="d-none d-md-inline"> FCFA</small>
                                    </td>
                                    <td class="text-end d-none d-lg-table-cell">{{ number_format($echeance->montant_paye, 0, ',', ' ') }} FCFA</td>
                                    <td class="text-center">
                                        @if($echeance->statut == 'paye')
                                            <span class="badge bg-success">
                                                <i class="ri-checkbox-circle-line d-none d-sm-inline"></i> <span class="d-none d-sm-inline">Payé</span><i class="ri-check-line d-sm-none"></i>
                                            </span>
                                        @elseif($echeance->statut == 'impaye')
                                            <span class="badge bg-danger">
                                                <i class="ri-close-circle-line d-none d-sm-inline"></i> <span class="d-none d-sm-inline">Impayé</span><i class="ri-close-line d-sm-none"></i>
                                            </span>
                                        @else
                                            <span class="badge bg-warning">
                                                <i class="ri-time-line d-none d-sm-inline"></i> <span class="d-none d-sm-inline">À échéance</span><i class="ri-time-line d-sm-none"></i>
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- Historique des Paiements -->
    @if($historiquePaiements->count() > 0)
        <div class="card">
            <div class="card-header bg-light">
                <h6 class="mb-0">
                    <i class="ri-file-list-3-line me-2"></i>Historique des paiements
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date paiement</th>
                                <th>Type</th>
                                <th class="text-end">Montant</th>
                                <th>Méthode</th>
                                <th class="text-center">Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($historiquePaiements as $paiement)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($paiement->date_paiement)->format('d/m/Y') }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ ucfirst($paiement->type) }}</span>
                                    </td>
                                    <td class="text-end">
                                        <strong>{{ number_format($paiement->montant, 0, ',', ' ') }} FCFA</strong>
                                    </td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $paiement->methode_paiement ?? 'N/A')) }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-success">
                                            <i class="ri-checkbox-circle-line"></i> Validé
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
@else
    <!-- Aucune location active -->
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="ri-home-4-line fs-1 text-muted mb-3 d-block"></i>
            <h5 class="text-muted">Aucune location active</h5>
            <p class="text-muted mb-4">Vous n'avez pas encore de bien loué actuellement.</p>
            <a href="{{ route('properties.index') }}" class="btn btn-primary">
                <i class="ri-search-line me-2"></i>Rechercher un bien
            </a>
        </div>
    </div>
@endif

<!-- Note informative -->
<div class="alert alert-light border mt-4 mb-4">
    <div class="d-flex">
        <div class="flex-shrink-0">
            <i class="ri-information-line text-primary fs-4"></i>
        </div>
        <div class="flex-grow-1 ms-3">
            <h6 class="alert-heading">Besoin d'aide ?</h6>
            <p class="mb-0">Pour tout renseignement sur votre location ou vos paiements, contactez votre gestionnaire de compte.</p>
        </div>
    </div>
</div>
</div>
@endsection

@section('styles')
<style>
    .container-fluid {
        max-width: 100%;
        padding-left: 1rem;
        padding-right: 1rem;
    }
    
    .card {
        border: none;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        border-radius: 8px;
    }
    
    @media (max-width: 768px) {
        .container-fluid {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }
        
        .card-body {
            padding: 0.75rem !important;
        }
        
        .card-header {
            padding: 0.75rem;
        }
        
        h4 {
            font-size: 1.1rem;
        }
        
        h5 {
            font-size: 1rem;
        }
        
        h6 {
            font-size: 0.9rem;
        }
        
        .fs-6 {
            font-size: 1rem !important;
        }
        
        .small, small {
            font-size: 0.8rem;
        }
        
        .badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }
        
        .table {
            font-size: 0.85rem;
        }
    }
</style>
@endsection
