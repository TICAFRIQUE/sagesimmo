@extends('frontend.pages.client.espaces.proprietaire.layout')

@section('title', 'Vue d\'ensemble - Espace Propriétaire')

@section('tab-content')
    <!-- Résumé Global -->
    <div class="row g-2 g-md-3 mb-3 mb-md-4">
    <div class="col-6 col-sm-6 col-lg-3">
        <div class="card border-start border-4 border-success h-100">
            <div class="card-body py-2 py-md-3 px-2 px-md-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 d-none d-sm-block">
                        <div class="avatar-sm rounded-circle bg-success-subtle">
                            <span class="avatar-title rounded-circle text-success fs-3">
                                <i class="ri-building-line"></i>
                            </span>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-0 ms-sm-2">
                        <h5 class="mb-0 fs-6 fs-md-5">{{ $nombreBiensProprio }}</h5>
                        <p class="text-muted mb-0 small">Biens confiés</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-sm-6 col-lg-3">
        <div class="card border-start border-4 border-info h-100">
            <div class="card-body py-2 py-md-3 px-2 px-md-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 d-none d-sm-block">
                        <div class="avatar-sm rounded-circle bg-info-subtle">
                            <span class="avatar-title rounded-circle text-info fs-3">
                                <i class="ri-home-smile-line"></i>
                            </span>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-0 ms-sm-2">
                        <h5 class="mb-0 fs-6 fs-md-5">{{ $biensLouesProprio }}</h5>
                        <p class="text-muted mb-0 small">Biens loués</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-sm-6 col-lg-3">
        <div class="card border-start border-4 border-primary h-100">
            <div class="card-body py-2 py-md-3 px-2 px-md-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 d-none d-sm-block">
                        <div class="avatar-sm rounded-circle bg-primary-subtle">
                            <span class="avatar-title rounded-circle text-primary fs-3">
                                <i class="ri-checkbox-circle-line"></i>
                            </span>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-0 ms-sm-2">
                        <h5 class="mb-0 fs-6 fs-md-5">{{ $biensVendusProprio }}</h5>
                        <p class="text-muted mb-0 small">Biens vendus</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-sm-6 col-lg-3">
        <div class="card border-start border-4 border-warning h-100">
            <div class="card-body py-2 py-md-3 px-2 px-md-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 d-none d-sm-block">
                        <div class="avatar-sm rounded-circle bg-warning-subtle">
                            <span class="avatar-title rounded-circle text-warning fs-3">
                                <i class="ri-door-open-line"></i>
                            </span>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-0 ms-sm-2">
                        <h5 class="mb-0 fs-6 fs-md-5">{{ $biensDisponibles }}</h5>
                        <p class="text-muted mb-0 small">Disponibles</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Revenus & Transparence -->
    <div class="row g-2 g-md-3 mb-3 mb-md-4">
        <div class="col-12 col-md-4">
            <div class="card bg-success-subtle border-0 h-100">
                <div class="card-body py-2 py-md-3 px-2 px-md-3">
                <h6 class="text-muted mb-2 text-uppercase small">
                    <i class="ri-money-dollar-circle-line me-1"></i> Revenus Bruts
                </h6>
                    <h4 class="text-success mb-0 fs-5 fs-md-4">{{ number_format($revenusEncaisses, 0, ',', ' ') }}</h4>
                    <p class="text-muted small mb-0 mt-1">Total encaissé</p>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card bg-warning-subtle border-0 h-100">
                <div class="card-body py-2 py-md-3 px-2 px-md-3">
                <h6 class="text-muted mb-2 text-uppercase small">
                    <i class="ri-percent-line me-1"></i> Commission
                </h6>
                    <h4 class="text-warning mb-0 fs-5 fs-md-4">{{ number_format($commissionAgenceTotal, 0, ',', ' ') }}</h4>
                    <p class="text-muted small mb-0 mt-1">
                        {{ $revenusEncaisses > 0 ? round(($commissionAgenceTotal / $revenusEncaisses) * 100, 1) : 0 }}% des revenus
                    </p>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card bg-primary-subtle border-0 h-100">
                <div class="card-body py-2 py-md-3 px-2 px-md-3">
                <h6 class="text-muted mb-2 text-uppercase small">
                    <i class="ri-wallet-3-line me-1"></i> Revenu Net
                </h6>
                    <h4 class="text-primary mb-0 fs-5 fs-md-4">{{ number_format($revenuNetTotal, 0, ',', ' ') }}</h4>
                    <p class="text-muted small mb-0 mt-1">Votre part</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertes -->
    <div class="row g-2 g-md-3 mb-3 mb-md-4">
        @if($loyersImpayes > 0)
            <div class="col-12 col-md-6">
            <div class="alert alert-danger d-flex align-items-center mb-0">
                <i class="ri-alert-line fs-3 me-3"></i>
                <div>
                    <strong>Loyers impayés : {{ number_format($loyersImpayes, 0, ',', ' ') }} FCFA</strong>
                    <p class="mb-0 small">L'agence s'occupe du recouvrement</p>
                    </div>
                </div>
            </div>
        @endif
        @if($prochaineEcheanceProprio)
            <div class="col-12 col-md-6">
            <div class="alert alert-info d-flex align-items-center mb-0">
                <i class="ri-calendar-check-line fs-3 me-3"></i>
                <div>
                    <strong>Prochaine échéance : {{ \Carbon\Carbon::parse($prochaineEcheanceProprio->date_echeance)->format('d/m/Y') }}</strong>
                    <p class="mb-0 small">Montant : {{ number_format($prochaineEcheanceProprio->montant_du, 0, ',', ' ') }} FCFA</p>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Revenus Mensuels -->
    @if(count($revenusMensuels) > 0)
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0">
                    <i class="ri-line-chart-line me-2"></i>Revenus des 6 derniers mois
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-nowrap align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="min-width: 120px;">Mois</th>
                            <th class="text-end" style="min-width: 100px;">Brut</th>
                            <th class="text-end d-none d-md-table-cell" style="min-width: 100px;">Commission</th>
                            <th class="text-end" style="min-width: 100px;">Net</th>
                            <th class="text-center d-none d-sm-table-cell" style="min-width: 90px;">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($revenusMensuels as $revenu)
                            <tr>
                                <td>
                                    <strong class="d-none d-md-inline">{{ \Carbon\Carbon::parse($revenu['mois'] . '-01')->locale('fr')->isoFormat('MMMM YYYY') }}</strong>
                                    <strong class="d-md-none">{{ \Carbon\Carbon::parse($revenu['mois'] . '-01')->locale('fr')->isoFormat('MMM YY') }}</strong>
                                </td>
                                <td class="text-end"><small class="d-md-none text-muted d-block">Brut</small>{{ number_format($revenu['brut'], 0, ',', ' ') }}<small class="d-none d-md-inline"> FCFA</small></td>
                                <td class="text-end text-warning d-none d-md-table-cell">{{ number_format($revenu['commission'], 0, ',', ' ') }} FCFA</td>
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

    <!-- Détails par Bien -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <div class="row align-items-center g-2">
                <div class="col-12 col-md-6">
                    <h6 class="mb-0">
                        <i class="ri-building-4-line me-2"></i>Liste de mes biens
                    </h6>
                </div>
                <div class="col-12 col-md-6">
                    <form method="GET" action="{{ route('client.proprietaire') }}" class="d-flex justify-content-md-end">
                        <select name="filtre" class="form-select form-select-sm" style="max-width: 200px;" onchange="this.form.submit()">
                        <option value="tous" {{ $filtre == 'tous' ? 'selected' : '' }}>Tous les biens</option>
                        <option value="loues" {{ $filtre == 'loues' ? 'selected' : '' }}>Biens loués</option>
                        <option value="vendus" {{ $filtre == 'vendus' ? 'selected' : '' }}>Biens vendus</option>
                        <option value="disponibles" {{ $filtre == 'disponibles' ? 'selected' : '' }}>Biens disponibles</option>
                        </select>
                    </form>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            @if(count($detailsBiensProprio) > 0)
                <!-- Affichage Desktop: Tableau -->
                <div class="table-responsive d-none d-lg-block">
                    <table class="table table-hover table-striped align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="min-width: 200px;">Bien</th>
                                <th class="text-center" style="min-width: 100px;">Statut</th>
                                <th class="text-end" style="min-width: 110px;">Revenus</th>
                                <th class="text-end" style="min-width: 110px;">Commission</th>
                                <th class="text-end" style="min-width: 110px;">Revenu Net</th>
                                <th class="text-center" style="min-width: 120px;">Infos</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($detailsBiensProprio as $detail)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 me-2">
                                                @if($detail['bien']->hasMedia('images'))
                                                    <img src="{{ $detail['bien']->getFirstMediaUrl('images') }}" 
                                                         class="rounded" 
                                                         alt="{{ $detail['bien']->titre }}"
                                                         style="width: 50px; height: 50px; object-fit: cover;">
                                                @else
                                                    <div class="rounded bg-light d-flex align-items-center justify-content-center" 
                                                         style="width: 50px; height: 50px;">
                                                        <i class="ri-home-4-line fs-5 text-muted"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">{{ Str::limit($detail['bien']->titre, 40) }}</h6>
                                                <p class="text-muted small mb-0">
                                                    <i class="ri-map-pin-line"></i> {{ $detail['bien']->ville }}
                                                </p>
                                                <small class="text-muted">Réf: {{ $detail['bien']->reference }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $detail['bien']->statut == 'disponible' ? 'warning' : ($detail['bien']->statut == 'loue' ? 'info' : 'success') }}">
                                            {{ ucfirst($detail['bien']->statut) }}
                                        </span>
                                        <p class="text-muted small mb-0 mt-1">{{ $detail['bien']->typeBien->nom ?? 'N/A' }}</p>
                                    </td>
                                    <td class="text-end">
                                        <strong class="text-success">{{ number_format($detail['loyers_payes'], 0, ',', ' ') }}</strong>
                                        <small class="text-muted d-block">FCFA</small>
                                        @if($detail['vente_finalisee'])
                                            <small class="text-muted d-block mt-1">
                                                <i class="ri-shopping-bag-line"></i> Vente
                                            </small>
                                        @elseif($detail['location_active'])
                                            <small class="text-muted d-block mt-1">
                                                <i class="ri-home-line"></i> Loyers
                                            </small>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <strong class="text-warning">{{ number_format($detail['commission_agence'], 0, ',', ' ') }}</strong>
                                        <small class="text-muted d-block">FCFA</small>
                                    </td>
                                    <td class="text-end">
                                        <strong class="text-primary">{{ number_format($detail['revenu_net'], 0, ',', ' ') }}</strong>
                                        <small class="text-muted d-block">FCFA</small>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1 flex-wrap">
                                            @if($detail['loyers_impayes'] > 0)
                                                <span class="badge bg-danger" data-bs-toggle="tooltip" 
                                                      title="Impayés: {{ number_format($detail['loyers_impayes'], 0, ',', ' ') }} FCFA">
                                                    <i class="ri-alert-line"></i> Impayés
                                                </span>
                                            @endif
                                            @if($detail['location_active'])
                                                <span class="badge bg-info" data-bs-toggle="tooltip" title="Location en cours">
                                                    <i class="ri-home-heart-line"></i> Loué
                                                </span>
                                            @endif
                                            @if($detail['vente_finalisee'])
                                                <span class="badge bg-success" data-bs-toggle="tooltip" 
                                                      data-bs-html="true"
                                                      title="<strong>Vente finalisée</strong><br>Prix: {{ number_format($detail['vente_prix'], 0, ',', ' ') }} FCFA<br>Payé: {{ number_format($detail['vente_montant_paye'], 0, ',', ' ') }} FCFA<br>Date: {{ $detail['vente_date'] ? \Carbon\Carbon::parse($detail['vente_date'])->format('d/m/Y') : 'N/A' }}">
                                                    <i class="ri-check-double-line"></i> Vendu
                                                </span>
                                            @endif
                                            @if(!$detail['location_active'] && !$detail['vente_finalisee'])
                                                <span class="badge bg-secondary">
                                                    <i class="ri-information-line"></i> Aucune
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Affichage Mobile/Tablet: Cartes -->
                <div class="d-lg-none">
                    @foreach($detailsBiensProprio as $detail)
                        <div class="mobile-bien-card p-3 border-bottom">
                            <div class="d-flex align-items-start mb-3">
                                <div class="flex-shrink-0 me-3">
                                    @if($detail['bien']->hasMedia('images'))
                                        <img src="{{ $detail['bien']->getFirstMediaUrl('images') }}" 
                                             class="rounded" 
                                             alt="{{ $detail['bien']->titre }}"
                                             style="width: 70px; height: 70px; object-fit: cover;">
                                    @else
                                        <div class="rounded bg-light d-flex align-items-center justify-content-center" 
                                             style="width: 70px; height: 70px;">
                                            <i class="ri-home-4-line fs-4 text-muted"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ $detail['bien']->titre }}</h6>
                                    <p class="text-muted small mb-1">
                                        <i class="ri-map-pin-line"></i> {{ $detail['bien']->ville }}
                                    </p>
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <span class="badge bg-{{ $detail['bien']->statut == 'disponible' ? 'warning' : ($detail['bien']->statut == 'loue' ? 'info' : 'success') }}">
                                            {{ ucfirst($detail['bien']->statut) }}
                                        </span>
                                        <small class="text-muted">{{ $detail['bien']->typeBien->nom ?? 'N/A' }}</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row g-2 mb-2">
                                <div class="col-4">
                                    <div class="text-center p-2 bg-success-subtle rounded">
                                        <small class="text-muted d-block mb-1">Revenus</small>
                                        <strong class="text-success d-block small">{{ number_format($detail['loyers_payes'], 0, ',', ' ') }}</strong>
                                        <small class="text-muted">FCFA</small>
                                        @if($detail['vente_finalisee'])
                                            <small class="text-muted d-block"><i class="ri-shopping-bag-line"></i> Vente</small>
                                        @elseif($detail['location_active'])
                                            <small class="text-muted d-block"><i class="ri-home-line"></i> Loyers</small>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="text-center p-2 bg-warning-subtle rounded">
                                        <small class="text-muted d-block mb-1">Commission</small>
                                        <strong class="text-warning d-block small">{{ number_format($detail['commission_agence'], 0, ',', ' ') }}</strong>
                                        <small class="text-muted">FCFA</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="text-center p-2 bg-primary-subtle rounded">
                                        <small class="text-muted d-block mb-1">Net</small>
                                        <strong class="text-primary d-block small">{{ number_format($detail['revenu_net'], 0, ',', ' ') }}</strong>
                                        <small class="text-muted">FCFA</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex gap-1 flex-wrap justify-content-center">
                                @if($detail['loyers_impayes'] > 0)
                                    <span class="badge bg-danger">
                                        <i class="ri-alert-line"></i> Impayés: {{ number_format($detail['loyers_impayes'], 0, ',', ' ') }}
                                    </span>
                                @endif
                                @if($detail['location_active'])
                                    <span class="badge bg-info">
                                        <i class="ri-home-heart-line"></i> Location active
                                    </span>
                                @endif
                                @if($detail['vente_finalisee'])
                                    <span class="badge bg-success">
                                        <i class="ri-check-double-line"></i> Vente finalisée
                                    </span>
                                    <div class="mt-2 p-2 bg-light rounded small">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span class="text-muted"><i class="ri-price-tag-3-line"></i> Prix:</span>
                                            <strong class="text-success">{{ number_format($detail['vente_prix'], 0, ',', ' ') }} FCFA</strong>
                                        </div>
                                        <div class="d-flex justify-content-between mb-1">
                                            <span class="text-muted"><i class="ri-money-dollar-circle-line"></i> Encaissé:</span>
                                            <strong class="text-primary">{{ number_format($detail['vente_montant_paye'], 0, ',', ' ') }} FCFA</strong>
                                        </div>
                                        @if($detail['vente_date'])
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted"><i class="ri-calendar-check-line"></i> Date:</span>
                                            <strong>{{ \Carbon\Carbon::parse($detail['vente_date'])->format('d/m/Y') }}</strong>
                                        </div>
                                        @endif
                                    </div>
                                @endif
                                @if(!$detail['location_active'] && !$detail['vente_finalisee'])
                                    <span class="badge bg-secondary">
                                        <i class="ri-information-line"></i> Aucune transaction
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                <div class="card-footer bg-white">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
                        <div class="text-muted small">
                            Affichage de {{ $biensProprio->firstItem() ?? 0 }} à {{ $biensProprio->lastItem() ?? 0 }} sur {{ $biensProprio->total() }} biens
                        </div>
                        <div>
                            {{ $biensProprio->links() }}
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="ri-building-4-line fs-1 text-muted mb-3 d-block"></i>
                    <h5 class="text-muted">Aucun bien trouvé</h5>
                    <p class="text-muted">
                        @if($filtre != 'tous')
                            Aucun bien ne correspond au filtre sélectionné.
                            <a href="{{ route('client.proprietaire') }}" class="btn btn-sm btn-outline-primary mt-2">
                                Voir tous les biens
                            </a>
                        @else
                            Vous n'avez pas encore de bien confié à l'agence.
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>

    <!-- Note informative -->
    <div class="alert alert-light border mt-4 mb-4">
        <div class="d-flex">
            <div class="flex-shrink-0">
                <i class="ri-information-line text-primary fs-4"></i>
            </div>
            <div class="flex-grow-1 ms-3">
                <h6 class="alert-heading">Mode consultation uniquement</h6>
                <p class="mb-0">Vous consultez vos biens confiés à notre agence. Pour toute modification ou question, veuillez contacter votre gestionnaire de compte.</p>
            </div>
        </div>
    </div>
@endsection

@section('styles')
@parent
<style>
    .avatar-sm {
        height: 40px;
        width: 40px;
    }

    .avatar-title {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100%;
        width: 100%;
    }
    
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
    
    .card-body {
        padding: 1rem;
    }
    
    .card-header {
        border-bottom: 1px solid #e9ecef;
        padding: 1rem;
        background-color: #f8f9fa;
    }
    
    .table-responsive {
        margin: 0;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    
    .table {
        margin-bottom: 0;
        white-space: nowrap;
    }
    
    .table td,
    .table th {
        padding: 0.75rem 0.5rem;
        vertical-align: middle;
    }
    
    .table-hover tbody tr {
        transition: background-color 0.2s ease;
    }
    
    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    .badge {
        font-weight: 500;
        padding: 0.35rem 0.65rem;
        font-size: 0.875rem;
    }
    
    .form-select-sm {
        border-radius: 6px;
        border: 1px solid #ced4da;
    }
    
    .pagination {
        margin-bottom: 0;
    }
    
    h4, h5, h6 {
        margin-bottom: 0.5rem;
    }
    
    .alert {
        padding: 1rem;
    }
    
    .mobile-bien-card {
        background-color: #fff;
        transition: background-color 0.2s ease;
    }
    
    .mobile-bien-card:hover {
        background-color: #f8f9fa;
    }
    
    .mobile-bien-card:last-child {
        border-bottom: none !important;
    }
    
    @media (max-width: 768px) {
        .container-fluid {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }
        
        .card-body {
            padding: 0.75rem;
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
        
        .alert {
            padding: 0.75rem;
            font-size: 0.9rem;
        }
    }
    
    @media (max-width: 576px) {
        .table-responsive {
            font-size: 0.85rem;
        }
        
        .mobile-bien-card {
            padding: 0.75rem !important;
        }
    }
</style>
@endsection

@section('tab-scripts')
<script>
    // Initialiser les tooltips Bootstrap
    document.addEventListener('DOMContentLoaded', function() {
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
    });
</script>
@endsection
