@extends('frontend.pages.client.espaces.proprietaire.layout')

@section('title', 'Locations - Espace Propriétaire')

@section('tab-content')
    <!-- Filtre par période -->
    <div class="card mb-3">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('client.proprietaire.locations') }}" class="row g-2 align-items-end">
                <div class="col-12 col-md-5">
                    <label class="form-label small mb-1">Mois</label>
                    <select name="mois" class="form-select form-select-sm">
                        <option value="">Tous les mois</option>
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ request('mois') == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create(null, $m, 1)->locale('fr')->isoFormat('MMMM') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-12 col-md-5">
                    <label class="form-label small mb-1">Année</label>
                    <select name="annee" class="form-select form-select-sm">
                        <option value="">Toutes les années</option>
                        @for($y = now()->year; $y >= now()->year - 5; $y--)
                            <option value="{{ $y }}" {{ request('annee') == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="ri-filter-line"></i> Filtrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- KPI Locations -->
    <div class="row g-2 g-md-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card border-start border-4 border-success h-100">
                <div class="card-body py-2 py-md-3 px-2 px-md-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-2 d-none d-md-block">
                            <div class="avatar-sm rounded-circle bg-success-subtle">
                                <span class="avatar-title rounded-circle text-success fs-3">
                                    <i class="ri-money-dollar-circle-line"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted text-uppercase mb-1 small">Loyers encaissés</h6>
                            <h4 class="text-success mb-0 fs-5 fs-md-4">{{ number_format($loyersEncaisses, 0, ',', ' ') }}</h4>
                            <small class="text-muted">FCFA</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-start border-4 border-danger h-100">
                <div class="card-body py-2 py-md-3 px-2 px-md-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-2 d-none d-md-block">
                            <div class="avatar-sm rounded-circle bg-danger-subtle">
                                <span class="avatar-title rounded-circle text-danger fs-3">
                                    <i class="ri-alert-line"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted text-uppercase mb-1 small">Loyers en retard</h6>
                            <h4 class="text-danger mb-0 fs-5 fs-md-4">{{ number_format($loyersImpayes, 0, ',', ' ') }}</h4>
                            <small class="text-muted">FCFA</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-start border-4 border-warning h-100">
                <div class="card-body py-2 py-md-3 px-2 px-md-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-2 d-none d-md-block">
                            <div class="avatar-sm rounded-circle bg-warning-subtle">
                                <span class="avatar-title rounded-circle text-warning fs-3">
                                    <i class="ri-percent-line"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted text-uppercase mb-1 small">Commission agence</h6>
                            <h4 class="text-warning mb-0 fs-5 fs-md-4">{{ number_format($commissionAgence, 0, ',', ' ') }}</h4>
                            <small class="text-muted">FCFA</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-start border-4 border-primary h-100">
                <div class="card-body py-2 py-md-3 px-2 px-md-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-2 d-none d-md-block">
                            <div class="avatar-sm rounded-circle bg-primary-subtle">
                                <span class="avatar-title rounded-circle text-primary fs-3">
                                    <i class="ri-wallet-3-line"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted text-uppercase mb-1 small">Revenus nets</h6>
                            <h4 class="text-primary mb-0 fs-5 fs-md-4">{{ number_format($revenusNets, 0, ',', ' ') }}</h4>
                            <small class="text-muted">FCFA</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Alerte prochaine échéance -->
    @if($prochaineEcheance)
        <div class="alert alert-info d-flex align-items-center mb-4">
            <i class="ri-calendar-check-line fs-3 me-3"></i>
            <div>
                <strong>Prochaine échéance : {{ \Carbon\Carbon::parse($prochaineEcheance->date_echeance)->format('d/m/Y') }}</strong>
                <p class="mb-0 small">Bien: {{ $prochaineEcheance->location->annonce->titre }} - Montant : {{ number_format($prochaineEcheance->montant_du, 0, ',', ' ') }} FCFA</p>
            </div>
        </div>
    @endif

    <!-- Tableau biens en location -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <div class="row align-items-center">
                <div class="col-12">
                    <h6 class="mb-0">
                        <i class="ri-home-smile-line me-2"></i>Détails des locations ({{ count($biensLocations) }} biens)
                        @if(request('mois') || request('annee'))
                            <span class="badge bg-primary ms-2 small">
                                @if(request('mois') && request('annee'))
                                    {{ \Carbon\Carbon::create(request('annee'), request('mois'), 1)->locale('fr')->isoFormat('MMMM YYYY') }}
                                @elseif(request('annee'))
                                    Année {{ request('annee') }}
                                @endif
                            </span>
                        @endif
                    </h6>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            @if(count($biensLocations) > 0)
                <!-- Version Desktop -->
                <div class="table-responsive d-none d-lg-block">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="min-width: 200px;">Bien</th>
                                <th class="text-end" style="min-width: 110px;">Loyer</th>
                                <th class="text-end" style="min-width: 110px;">Encaissé</th>
                                <th class="text-end" style="min-width: 90px;">Retard</th>
                                <th class="text-end" style="min-width: 110px;">Commission</th>
                                <th class="text-end" style="min-width: 110px;">Net</th>
                                <th class="text-center" style="min-width: 130px;">Prochaine échéance</th>
                                <th class="text-center" style="min-width: 100px;">Actions</th>
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
                                    <td class="text-end">
                                        <strong class="text-dark">{{ number_format($location['loyer_mensuel'], 0, ',', ' ') }}</strong>
                                        <small class="text-muted d-block">FCFA</small>
                                    </td>
                                    <td class="text-end">
                                        <strong class="text-success">{{ number_format($location['encaisse'], 0, ',', ' ') }}</strong>
                                        <small class="text-muted d-block">FCFA</small>
                                    </td>
                                    <td class="text-end">
                                        <strong class="text-{{ $location['retard'] > 0 ? 'danger' : 'success' }}">{{ number_format($location['retard'], 0, ',', ' ') }}</strong>
                                        <small class="text-muted d-block">FCFA</small>
                                    </td>
                                    <td class="text-end">
                                        <span class="badge bg-warning mb-1">{{ number_format($location['commission_pct'], 1) }}%</span>
                                        <strong class="text-warning d-block">{{ number_format($location['commission_reelle'], 0, ',', ' ') }}</strong>
                                        <small class="text-muted d-block">FCFA</small>
                                    </td>
                                    <td class="text-end">
                                        <strong class="text-primary">{{ number_format($location['net'], 0, ',', ' ') }}</strong>
                                        <small class="text-muted d-block">FCFA</small>
                                    </td>
                                    <td class="text-center">
                                        @if($location['prochaine_echeance'])
                                            <strong class="d-block">{{ \Carbon\Carbon::parse($location['prochaine_echeance']->date_echeance)->format('d/m/Y') }}</strong>
                                            <small class="text-muted">{{ number_format($location['prochaine_echeance']->montant_du, 0, ',', ' ') }} FCFA</small>
                                            <br>
                                            @if($location['prochaine_echeance']->statut == 'paye')
                                                <span class="badge bg-success mt-1">
                                                    <i class="ri-check-line"></i> Payée
                                                </span>
                                            @elseif($location['prochaine_echeance']->statut == 'impaye' && $location['prochaine_echeance']->date_echeance < now())
                                                <span class="badge bg-danger mt-1">
                                                    <i class="ri-alert-line"></i> Retard
                                                </span>
                                            @elseif($location['prochaine_echeance']->statut == 'impaye')
                                                <span class="badge bg-warning mt-1">
                                                    <i class="ri-time-line"></i> Impayée
                                                </span>
                                            @else
                                                <span class="badge bg-info mt-1">
                                                    <i class="ri-time-line"></i> En attente
                                                </span>
                                            @endif
                                        @else
                                            <span class="text-muted">Aucune</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalEcheances{{ $location['location_active']->id }}">
                                            <i class="ri-calendar-line"></i> Échéances
                                        </button>
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
                            
                            <div class="row g-2 mb-2">
                                <div class="col-3">
                                    <div class="text-center p-2 bg-light rounded">
                                        <small class="text-muted d-block mb-1">Loyer</small>
                                        <strong class="text-dark d-block small">{{ number_format($location['loyer_mensuel'], 0, ',', ' ') }}</strong>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="text-center p-2 bg-success-subtle rounded">
                                        <small class="text-muted d-block mb-1">Encaissé</small>
                                        <strong class="text-success d-block small">{{ number_format($location['encaisse'], 0, ',', ' ') }}</strong>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="text-center p-2 bg-{{ $location['retard'] > 0 ? 'danger' : 'success' }}-subtle rounded">
                                        <small class="text-muted d-block mb-1">Retard</small>
                                        <strong class="text-{{ $location['retard'] > 0 ? 'danger' : 'success' }} d-block small">{{ number_format($location['retard'], 0, ',', ' ') }}</strong>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="text-center p-2 bg-warning-subtle rounded">
                                        <small class="text-muted d-block mb-1">Commission</small>
                                        <span class="badge bg-warning mb-1">{{ number_format($location['commission_pct'], 1) }}%</span>
                                        <strong class="text-warning d-block small">{{ number_format($location['commission_reelle'], 0, ',', ' ') }}</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    @if($location['prochaine_echeance'])
                                        <small class="text-muted">Prochaine échéance:</small>
                                        <strong class="d-block">{{ \Carbon\Carbon::parse($location['prochaine_echeance']->date_echeance)->format('d/m/Y') }}</strong>
                                    @else
                                        <small class="text-muted">Aucune échéance</small>
                                    @endif
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalEcheances{{ $location['location_active']->id }}">
                                    <i class="ri-calendar-line"></i> Échéances
                                </button>
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

    <!-- Modaux pour les échéances de chaque location -->
    @foreach($biensLocations as $location)
        <div class="modal fade" id="modalEcheances{{ $location['location_active']->id }}" tabindex="-1" aria-labelledby="modalEcheancesLabel{{ $location['location_active']->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title" id="modalEcheancesLabel{{ $location['location_active']->id }}">
                            <i class="ri-calendar-line me-2"></i>Échéances - {{ Str::limit($location['bien']->titre, 40) }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0">
                        @php
                            $echeances = $location['location_active']->echeances()->orderBy('date_echeance', 'asc')->get();
                        @endphp
                        
                        @if($echeances->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Date échéance</th>
                                            <th class="text-end">Montant dû</th>
                                            <th class="text-end">Montant payé</th>
                                            <th class="text-center">Statut</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($echeances as $echeance)
                                            <tr>
                                                <td>
                                                    <strong>{{ \Carbon\Carbon::parse($echeance->date_echeance)->format('d/m/Y') }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ \Carbon\Carbon::parse($echeance->date_echeance)->locale('fr')->isoFormat('MMMM YYYY') }}</small>
                                                </td>
                                                <td class="text-end">
                                                    <strong>{{ number_format($echeance->montant_du, 0, ',', ' ') }}</strong>
                                                    <small class="text-muted d-block">FCFA</small>
                                                </td>
                                                <td class="text-end">
                                                    <strong class="text-{{ $echeance->montant_paye >= $echeance->montant_du ? 'success' : 'warning' }}">
                                                        {{ number_format($echeance->montant_paye, 0, ',', ' ') }}
                                                    </strong>
                                                    <small class="text-muted d-block">FCFA</small>
                                                </td>
                                                <td class="text-center">
                                                    @if($echeance->statut == 'paye')
                                                        <span class="badge bg-success">
                                                            <i class="ri-check-line"></i> Payée
                                                        </span>
                                                    @elseif($echeance->statut == 'impaye' && $echeance->date_echeance < now())
                                                        <span class="badge bg-danger">
                                                            <i class="ri-alert-line"></i> Retard
                                                        </span>
                                                        <small class="text-danger d-block mt-1">
                                                            {{ \Carbon\Carbon::parse($echeance->date_echeance)->diffForHumans() }}
                                                        </small>
                                                    @elseif($echeance->statut == 'impaye')
                                                        <span class="badge bg-warning">
                                                            <i class="ri-time-line"></i> Impayée
                                                        </span>
                                                    @elseif($echeance->statut == 'partiel')
                                                        <span class="badge bg-info">
                                                            <i class="ri-information-line"></i> Partiel
                                                        </span>
                                                        <small class="text-muted d-block mt-1">
                                                            Reste: {{ number_format($echeance->montant_du - $echeance->montant_paye, 0, ',', ' ') }} FCFA
                                                        </small>
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
                        @else
                            <div class="text-center py-5">
                                <i class="ri-calendar-line fs-1 text-muted mb-3 d-block"></i>
                                <h5 class="text-muted">Aucune échéance</h5>
                                <p class="text-muted">Aucune échéance n'a été générée pour cette location.</p>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer bg-light">
                        <div class="d-flex justify-content-between align-items-center w-100">
                            <div>
                                <strong>Total échéances:</strong> {{ $echeances->count() }}
                                <span class="mx-2">|</span>
                                <span class="text-success">Payées: {{ $echeances->where('statut', 'paye')->count() }}</span>
                                <span class="mx-2">|</span>
                                <span class="text-danger">En retard: {{ $echeances->where('statut', 'impaye')->filter(fn($e) => $e->date_echeance < now())->count() }}</span>
                            </div>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
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
