@extends('frontend.pages.client.layout')

@section('title', 'Espace Acheteur')

@section('content')
<div class="container-fluid">
<div class="row mb-3">
    <div class="col-12">
        <h4 class="mb-0">
            <i class="ri-shopping-bag-3-line text-primary"></i> Mon espace Acheteur
        </h4>
    </div>
</div>

@if($venteActive)
    <!-- Informations Bien Acheté -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h6 class="mb-0">
                <i class="ri-home-4-line me-2"></i>Mon bien acheté
            </h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-12 col-md-4">
                    @if($venteActive->annonce && $venteActive->annonce->hasMedia('images'))
                        <img src="{{ $venteActive->annonce->getFirstMediaUrl('images') }}" 
                             class="img-fluid rounded" 
                             alt="{{ $venteActive->annonce->titre }}"
                             style="height: 200px; width: 100%; object-fit: cover;">
                    @else
                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 200px;">
                            <i class="ri-home-4-line fs-1 text-muted"></i>
                        </div>
                    @endif
                </div>
                <div class="col-12 col-md-8">
                    <h5 class="mb-3">{{ $venteActive->annonce->titre ?? 'Bien non disponible' }}</h5>
                    <div class="row g-2">
                        <div class="col-md-6 mb-3">
                            <p class="text-muted small mb-1">
                                <i class="ri-map-pin-line me-1"></i>Localisation
                            </p>
                            <p class="mb-0">{{ $venteActive->annonce->ville ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <p class="text-muted small mb-1">
                                <i class="ri-money-dollar-circle-line me-1"></i>Prix total
                            </p>
                            <h6 class="text-success mb-0">{{ number_format($venteActive->montant_total, 0, ',', ' ') }} FCFA</h6>
                        </div>
                        <div class="col-md-6 mb-3">
                            <p class="text-muted small mb-1">
                                <i class="ri-calendar-line me-1"></i>Date d'achat
                            </p>
                            <p class="mb-0">{{ \Carbon\Carbon::parse($venteActive->created_at)->format('d/m/Y') }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <p class="text-muted small mb-1">
                                <i class="ri-file-list-3-line me-1"></i>Référence
                            </p>
                            <p class="mb-0">{{ $venteActive->annonce->reference ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Situation Financière -->
    <div class="row g-2 g-md-3 mb-3 mb-md-4">
        <div class="col-12 col-md-4">
            <div class="card bg-primary-subtle border-0 h-100">
                <div class="card-body py-2 py-md-3 px-2 px-md-3">
                    <h6 class="text-muted mb-2 text-uppercase small">
                        <i class="ri-money-dollar-circle-line me-1"></i> Prix total
                    </h6>
                    <h4 class="text-primary mb-0 fs-5 fs-md-4">{{ number_format($venteActive->montant_total, 0, ',', ' ') }} FCFA</h4>
                    <p class="text-muted small mb-0 mt-1">Montant de l'achat</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success-subtle border-0 h-100">
                <div class="card-body py-2 py-md-3 px-2 px-md-3">
                    <h6 class="text-muted mb-2 text-uppercase small">
                        <i class="ri-checkbox-circle-line me-1"></i> Montant payé
                    </h6>
                    <h4 class="text-success mb-0 fs-5 fs-md-4">{{ number_format($montantPaye, 0, ',', ' ') }} FCFA</h4>
                    <p class="text-muted small mb-0 mt-1">
                        {{ $venteActive->montant_total > 0 ? round(($montantPaye / $venteActive->montant_total) * 100, 1) : 0 }}% du total
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-{{ $montantRestant > 0 ? 'warning' : 'success' }}-subtle border-0">
                <div class="card-body">
                    <h6 class="text-muted mb-3 text-uppercase small">
                        <i class="ri-wallet-3-line me-1"></i> Montant restant
                    </h6>
                    <h4 class="text-{{ $montantRestant > 0 ? 'warning' : 'success' }} mb-0">
                        {{ number_format($montantRestant, 0, ',', ' ') }} FCFA
                    </h4>
                    <p class="text-muted small mb-0 mt-1">
                        @if($montantRestant > 0)
                            À payer
                        @else
                            Paiement complet
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Progression du Paiement -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h6 class="mb-0">
                <i class="ri-bar-chart-line me-2"></i>Progression du paiement
            </h6>
        </div>
        <div class="card-body">
            @php
                $pourcentagePaye = $venteActive->montant_total > 0 ? round(($montantPaye / $venteActive->montant_total) * 100, 1) : 0;
            @endphp
            <div class="progress" style="height: 30px;">
                <div class="progress-bar bg-success" 
                     role="progressbar" 
                     style="width: {{ $pourcentagePaye }}%;" 
                     aria-valuenow="{{ $pourcentagePaye }}" 
                     aria-valuemin="0" 
                     aria-valuemax="100">
                    <strong>{{ $pourcentagePaye }}%</strong>
                </div>
            </div>
            <div class="d-flex justify-content-between mt-2">
                <small class="text-muted">0 FCFA</small>
                <small class="text-success">
                    <strong>{{ number_format($montantPaye, 0, ',', ' ') }} FCFA payé</strong>
                </small>
                <small class="text-muted">{{ number_format($venteActive->montant_total, 0, ',', ' ') }} FCFA</small>
            </div>
        </div>
    </div>

    <!-- Statut de la Vente -->
    @if($remiseCles)
        <div class="alert alert-success d-flex align-items-center mb-4">
            <i class="ri-checkbox-circle-line fs-3 me-3"></i>
            <div>
                <strong>Félicitations ! Votre achat est finalisé</strong>
                <p class="mb-0 small">Vous pouvez récupérer vos clés auprès de l'agence.</p>
            </div>
        </div>
    @elseif($montantRestant > 0)
        <div class="alert alert-warning d-flex align-items-center mb-4">
            <i class="ri-time-line fs-3 me-3"></i>
            <div>
                <strong>Paiement en cours</strong>
                <p class="mb-0 small">Il reste {{ number_format($montantRestant, 0, ',', ' ') }} FCFA à payer pour finaliser votre achat.</p>
            </div>
        </div>
    @endif

    <!-- Historique des Paiements -->
    @if($historiquePaiements->count() > 0)
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0">
                    <i class="ri-file-list-3-line me-2"></i>Historique des paiements
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="min-width: 100px;">Date</th>
                                <th class="d-none d-md-table-cell" style="min-width: 80px;">Type</th>
                                <th class="text-end" style="min-width: 100px;">Montant</th>
                                <th class="d-none d-lg-table-cell" style="min-width: 100px;">Méthode</th>
                                <th class="text-center" style="min-width: 80px;">Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($historiquePaiements as $paiement)
                                <tr>
                                    <td>
                                        <strong class="d-md-none">{{ \Carbon\Carbon::parse($paiement->date_paiement)->format('d/m/y') }}</strong>
                                        <strong class="d-none d-md-inline">{{ \Carbon\Carbon::parse($paiement->date_paiement)->format('d/m/Y') }}</strong>
                                    </td>
                                    <td class="d-none d-md-table-cell">
                                        <span class="badge bg-info">{{ ucfirst($paiement->type) }}</span>
                                    </td>
                                    <td class="text-end">
                                        <strong>{{ number_format($paiement->montant, 0, ',', ' ') }}<small class="d-none d-md-inline"> FCFA</small></strong>
                                    </td>
                                    <td class="d-none d-lg-table-cell">{{ ucfirst(str_replace('_', ' ', $paiement->methode_paiement ?? 'N/A')) }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-success">
                                            <i class="ri-checkbox-circle-line d-none d-sm-inline"></i> <span class="d-none d-sm-inline">Validé</span><i class="ri-check-line d-sm-none"></i>
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="2" class="text-end d-none d-md-table-cell"><strong>Total payé :</strong></td>
                                <td class="text-end d-md-none"><strong>Total :</strong></td>
                                <td class="text-end">
                                    <strong class="text-success">{{ number_format($montantPaye, 0, ',', ' ') }}<small class="d-none d-md-inline"> FCFA</small></strong>
                                </td>
                                <td colspan="2" class="d-none d-md-table-cell"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- Documents -->
    @if(count($documentsVente) > 0)
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0">
                    <i class="ri-folder-line me-2"></i>Documents
                </h6>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    @foreach($documentsVente as $document)
                        <a href="{{ $document->getUrl() }}" 
                           class="list-group-item list-group-item-action d-flex align-items-center" 
                           target="_blank">
                            <i class="ri-file-text-line fs-4 text-primary me-3"></i>
                            <div class="flex-grow-1">
                                <h6 class="mb-0">{{ $document->name }}</h6>
                                <small class="text-muted">{{ $document->human_readable_size }}</small>
                            </div>
                            <i class="ri-download-line fs-4 text-muted"></i>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Informations Complémentaires -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h6 class="mb-0">
                <i class="ri-information-line me-2"></i>Informations complémentaires
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm rounded-circle bg-primary-subtle me-3">
                            <span class="avatar-title rounded-circle text-primary">
                                <i class="ri-percent-line"></i>
                            </span>
                        </div>
                        <div>
                            <p class="text-muted small mb-0">Commission agence</p>
                            <h6 class="mb-0">{{ number_format($venteActive->commission_agence ?? 0, 0, ',', ' ') }} FCFA</h6>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm rounded-circle bg-info-subtle me-3">
                            <span class="avatar-title rounded-circle text-info">
                                <i class="ri-user-line"></i>
                            </span>
                        </div>
                        <div>
                            <p class="text-muted small mb-0">Type de bien</p>
                            <h6 class="mb-0">{{ $venteActive->annonce->typeBien->nom ?? 'N/A' }}</h6>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm rounded-circle bg-success-subtle me-3">
                            <span class="avatar-title rounded-circle text-success">
                                <i class="ri-check-double-line"></i>
                            </span>
                        </div>
                        <div>
                            <p class="text-muted small mb-0">Statut</p>
                            <h6 class="mb-0">{{ ucfirst(str_replace('_', ' ', $venteActive->statut)) }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@else
    <!-- Aucune vente active -->
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="ri-shopping-bag-3-line fs-1 text-muted mb-3 d-block"></i>
            <h5 class="text-muted">Aucun achat finalisé</h5>
            <p class="text-muted mb-4">Vous n'avez pas encore acheté de bien.</p>
            <a href="{{ route('properties.index') }}" class="btn btn-primary">
                <i class="ri-search-line me-2"></i>Rechercher un bien
            </a>
        </div>
    </div>
@endif

<!-- Note informative -->
<div class="alert alert-light border mt-4">
    <div class="d-flex">
        <div class="flex-shrink-0">
            <i class="ri-information-line text-primary fs-4"></i>
        </div>
        <div class="flex-grow-1 ms-3">
            <h6 class="alert-heading">Besoin d'aide ?</h6>
            <p class="mb-0">Pour tout renseignement sur votre achat ou vos paiements, contactez votre gestionnaire de compte.</p>
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
    
    .avatar-sm {
        height: 48px;
        width: 48px;
    }

    .avatar-title {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100%;
        width: 100%;
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
        
        .fs-5 {
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
        
        .avatar-sm {
            height: 40px;
            width: 40px;
        }
    }
</style>
@endsection
