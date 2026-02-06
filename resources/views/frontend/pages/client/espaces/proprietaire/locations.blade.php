@extends('frontend.pages.client.espaces.proprietaire.layout')

@section('title', 'Locations - Espace Propriétaire')

@section('tab-content')
    <!-- KPI Locations -->
    <div class="row g-2 g-md-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="card border-start border-4 border-success h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="avatar-sm rounded-circle bg-success-subtle">
                                <span class="avatar-title rounded-circle text-success fs-3">
                                    <i class="ri-money-dollar-circle-line"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted text-uppercase mb-1 small">Loyers mois courant</h6>
                            <h4 class="text-success mb-0">{{ number_format($loyersMoisCourant, 0, ',', ' ') }}</h4>
                            <small class="text-muted">FCFA encaissés</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="card border-start border-4 border-info h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3 d-none d-md-block">
                            <div class="avatar-sm rounded-circle bg-info-subtle">
                                <span class="avatar-title rounded-circle text-info fs-3">
                                    <i class="ri-percent-line"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted text-uppercase mb-1 small">Taux d'occupation</h6>
                            <h4 class="text-info mb-0">{{ number_format($tauxOccupation, 1) }}%</h4>
                            <small class="text-muted">de vos biens</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="card border-start border-4 border-{{ $loyersImpayes > 0 ? 'danger' : 'success' }} h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3 d-none d-md-block">
                            <div class="avatar-sm rounded-circle bg-{{ $loyersImpayes > 0 ? 'danger' : 'success' }}-subtle">
                                <span class="avatar-title rounded-circle text-{{ $loyersImpayes > 0 ? 'danger' : 'success' }} fs-3">
                                    <i class="ri-{{ $loyersImpayes > 0 ? 'alert' : 'check-double' }}-line"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted text-uppercase mb-1 small">Loyers impayés</h6>
                            <h4 class="text-{{ $loyersImpayes > 0 ? 'danger' : 'success' }} mb-0">{{ number_format($loyersImpayes, 0, ',', ' ') }}</h4>
                            <small class="text-muted">{{ $loyersImpayes > 0 ? 'En recouvrement' : 'Aucun impayé' }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau biens en location -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h6 class="mb-0">
                <i class="ri-home-smile-line me-2"></i>Biens en location ({{ count($biensLocations) }})
            </h6>
        </div>
        <div class="card-body p-0">
            @if(count($biensLocations) > 0)
                <!-- Version Desktop -->
                <div class="table-responsive d-none d-lg-block">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="min-width: 200px;">Bien</th>
                                <th class="text-center" style="min-width: 150px;">Locataire</th>
                                <th class="text-end" style="min-width: 120px;">Loyer mensuel</th>
                                <th class="text-center" style="min-width: 100px;">Commission</th>
                                <th class="text-end" style="min-width: 120px;">Revenu net</th>
                                <th class="text-center" style="min-width: 130px;">Statut paiement</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($biensLocations as $location)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 me-2">
                                                @if($location['bien']->hasMedia('images'))
                                                    <img src="{{ $location['bien']->getFirstMediaUrl('images') }}" 
                                                         class="rounded" 
                                                         style="width: 50px; height: 50px; object-fit: cover;"
                                                         alt="{{ $location['bien']->titre }}">
                                                @else
                                                    <div class="rounded bg-light d-flex align-items-center justify-content-center" 
                                                         style="width: 50px; height: 50px;">
                                                        <i class="ri-home-4-line fs-5 text-muted"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">{{ Str::limit($location['bien']->titre, 40) }}</h6>
                                                <p class="text-muted small mb-0">
                                                    <i class="ri-map-pin-line"></i> {{ $location['bien']->ville }}
                                                </p>
                                                <small class="text-muted">{{ $location['bien']->typeBien->nom ?? 'N/A' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <strong>{{ $location['locataire']->name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $location['locataire']->phone ?? 'N/A' }}</small>
                                    </td>
                                    <td class="text-end">
                                        <strong class="text-success">{{ number_format($location['loyer_mensuel'], 0, ',', ' ') }}</strong>
                                        <small class="text-muted d-block">FCFA</small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-warning">{{ number_format($location['commission_pct'], 1) }}%</span>
                                        <small class="text-muted d-block">{{ number_format(($location['loyer_mensuel'] * $location['commission_pct']) / 100, 0, ',', ' ') }} FCFA</small>
                                    </td>
                                    <td class="text-end">
                                        <strong class="text-primary">{{ number_format($location['revenu_net_mensuel'], 0, ',', ' ') }}</strong>
                                        <small class="text-muted d-block">FCFA/mois</small>
                                    </td>
                                    <td class="text-center">
                                        @if($location['statut_paiement'] == 'paye')
                                            <span class="badge bg-success">
                                                <i class="ri-check-line"></i> À jour
                                            </span>
                                            @if($location['derniere_echeance'])
                                                <small class="text-muted d-block mt-1">
                                                    {{ \Carbon\Carbon::parse($location['derniere_echeance']->date_echeance)->format('d/m/Y') }}
                                                </small>
                                            @endif
                                        @elseif($location['statut_paiement'] == 'impaye')
                                            <span class="badge bg-danger">
                                                <i class="ri-alert-line"></i> Impayé
                                            </span>
                                            @if($location['derniere_echeance'])
                                                <small class="text-danger d-block mt-1">
                                                    Depuis {{ \Carbon\Carbon::parse($location['derniere_echeance']->date_echeance)->format('d/m/Y') }}
                                                </small>
                                            @endif
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="ri-time-line"></i> En attente
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Version Mobile -->
                <div class="d-lg-none">
                    @foreach($biensLocations as $location)
                        <div class="p-3 border-bottom">
                            <div class="d-flex align-items-start mb-3">
                                <div class="flex-shrink-0 me-3">
                                    @if($location['bien']->hasMedia('images'))
                                        <img src="{{ $location['bien']->getFirstMediaUrl('images') }}" 
                                             class="rounded" 
                                             style="width: 70px; height: 70px; object-fit: cover;"
                                             alt="{{ $location['bien']->titre }}">
                                    @else
                                        <div class="rounded bg-light d-flex align-items-center justify-content-center" 
                                             style="width: 70px; height: 70px;">
                                            <i class="ri-home-4-line fs-4 text-muted"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ $location['bien']->titre }}</h6>
                                    <p class="text-muted small mb-1">
                                        <i class="ri-map-pin-line"></i> {{ $location['bien']->ville }}
                                    </p>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-info">{{ $location['bien']->typeBien->nom ?? 'N/A' }}</span>
                                        @if($location['statut_paiement'] == 'paye')
                                            <span class="badge bg-success">
                                                <i class="ri-check-line"></i> À jour
                                            </span>
                                        @elseif($location['statut_paiement'] == 'impaye')
                                            <span class="badge bg-danger">
                                                <i class="ri-alert-line"></i> Impayé
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-2">
                                <strong>Locataire:</strong> {{ $location['locataire']->name }}
                                <br>
                                <small class="text-muted">{{ $location['locataire']->phone ?? 'N/A' }}</small>
                            </div>
                            
                            <div class="row g-2">
                                <div class="col-4">
                                    <div class="text-center p-2 bg-success-subtle rounded">
                                        <small class="text-muted d-block mb-1">Loyer</small>
                                        <strong class="text-success d-block">{{ number_format($location['loyer_mensuel'], 0, ',', ' ') }}</strong>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="text-center p-2 bg-warning-subtle rounded">
                                        <small class="text-muted d-block mb-1">Commission</small>
                                        <strong class="text-warning d-block">{{ number_format($location['commission_pct'], 1) }}%</strong>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="text-center p-2 bg-primary-subtle rounded">
                                        <small class="text-muted d-block mb-1">Net</small>
                                        <strong class="text-primary d-block">{{ number_format($location['revenu_net_mensuel'], 0, ',', ' ') }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <i class="ri-home-smile-line fs-1 text-muted mb-3 d-block"></i>
                    <h5 class="text-muted">Aucun bien en location</h5>
                    <p class="text-muted">Aucun de vos biens n'est actuellement loué.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Historique mensuel -->
    @if(count($revenusMensuels12Mois) > 0)
        <div class="card">
            <div class="card-header bg-light">
                <h6 class="mb-0">
                    <i class="ri-line-chart-line me-2"></i>Revenus locatifs des 12 derniers mois
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="min-width: 120px;">Mois</th>
                                <th class="text-end" style="min-width: 100px;">Brut</th>
                                <th class="text-end d-none d-md-table-cell" style="min-width: 100px;">Commission</th>
                                <th class="text-end" style="min-width: 100px;">Net</th>
                                <th class="text-center d-none d-sm-table-cell" style="min-width: 80px;">%</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($revenusMensuels12Mois as $revenu)
                                <tr>
                                    <td>
                                        <strong class="d-none d-md-inline">{{ \Carbon\Carbon::parse($revenu['mois'] . '-01')->locale('fr')->isoFormat('MMMM YYYY') }}</strong>
                                        <strong class="d-md-none">{{ \Carbon\Carbon::parse($revenu['mois'] . '-01')->locale('fr')->isoFormat('MMM YY') }}</strong>
                                    </td>
                                    <td class="text-end">
                                        <small class="d-md-none text-muted d-block">Brut</small>
                                        {{ number_format($revenu['brut'], 0, ',', ' ') }}<small class="d-none d-md-inline"> FCFA</small>
                                    </td>
                                    <td class="text-end text-warning d-none d-md-table-cell">
                                        {{ number_format($revenu['commission'], 0, ',', ' ') }} FCFA
                                    </td>
                                    <td class="text-end text-primary">
                                        <small class="d-md-none text-muted d-block">Net</small>
                                        <strong>{{ number_format($revenu['net'], 0, ',', ' ') }}<small class="d-none d-md-inline"> FCFA</small></strong>
                                    </td>
                                    <td class="text-center d-none d-sm-table-cell">
                                        <span class="badge bg-warning">
                                            {{ $revenu['brut'] > 0 ? round(($revenu['commission'] / $revenu['brut']) * 100, 1) : 0 }}%
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
@endsection

@section('styles')
@parent
<style>
    .avatar-sm {
        height: 50px;
        width: 50px;
    }

    .avatar-title {
        display: flex;
        align-items: center;
        justify-content-center;
        height: 100%;
        width: 100%;
    }
    
    @media (max-width: 768px) {
        .avatar-sm {
            height: 40px;
            width: 40px;
        }
    }
</style>
@endsection

@section('tab-scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
    });
</script>
@endsection
