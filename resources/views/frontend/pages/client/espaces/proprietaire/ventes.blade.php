@extends('frontend.pages.client.espaces.proprietaire.layout')

@section('title', 'Ventes - Espace Propriétaire')

@section('tab-content')
    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="filter-mois" class="form-label">Mois</label>
                    <select id="filter-mois" class="form-select">
                        <option value="">Tous les mois</option>
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ now()->month == $i ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create(null, $i)->locale('fr')->monthName }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filter-annee" class="form-label">Année</label>
                    <select id="filter-annee" class="form-select">
                        <option value="">Toutes les années</option>
                        @for($year = now()->year; $year >= now()->year - 5; $year--)
                            <option value="{{ $year }}" {{ now()->year == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="button" class="btn btn-primary w-100" id="btn-filtrer">
                        <i class="ri-filter-line me-1"></i>Filtrer
                    </button>
                </div>
                <div class="col-md-3">
                    <button type="button" class="btn btn-secondary w-100" id="btn-reinitialiser">
                        <i class="ri-refresh-line me-1"></i>Réinitialiser
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- KPI Ventes -->
    <div class="row g-2 g-md-3 mb-4">
        <div class="col-6 col-md-4">
            <div class="card border-start border-4 border-success h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3 d-none d-md-block">
                            <div class="avatar-sm rounded-circle bg-success-subtle">
                                <span class="avatar-title rounded-circle text-success fs-3">
                                    <i class="ri-check-double-line"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted text-uppercase mb-1 small">Ventes finalisées</h6>
                            <h4 class="text-success mb-0" id="kpi-ventes-count">{{ $ventesFinalisesAnnee }}</h4>
                            <small class="text-muted" id="kpi-ventes-label">Cette année</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="card border-start border-4 border-primary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3 d-none d-md-block">
                            <div class="avatar-sm rounded-circle bg-primary-subtle">
                                <span class="avatar-title rounded-circle text-primary fs-3">
                                    <i class="ri-money-dollar-circle-line"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted text-uppercase mb-1 small">Revenu net perçu</h6>
                            <h4 class="text-primary mb-0" id="kpi-montant-encaisse">{{ number_format($montantTotalNet, 0, ',', ' ') }}</h4>
                            <small class="text-muted">FCFA (après commission)</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card border-start border-4 border-warning h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3 d-none d-md-block">
                            <div class="avatar-sm rounded-circle bg-warning-subtle">
                                <span class="avatar-title rounded-circle text-warning fs-3">
                                    <i class="ri-time-line"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted text-uppercase mb-1 small">Ventes en cours</h6>
                            <h4 class="text-warning mb-0">{{ $ventesEnCoursCount }}</h4>
                            <small class="text-muted">{{ $ventesEnCoursCount > 1 ? 'Transactions actives' : 'Transaction active' }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Ventes en cours -->
    @if(count($ventesEnCoursDetails) > 0)
        <div class="card mb-4">
            <div class="card-header bg-warning bg-opacity-10 border-warning">
                <h6 class="mb-0 text-warning">
                    <i class="ri-time-line me-2"></i>Ventes en cours ({{ count($ventesEnCoursDetails) }})
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="min-width: 200px;">Bien</th>
                                <th class="text-end" style="min-width: 120px;">Prix demandé</th>
                                <th class="text-center" style="min-width: 150px;">Statut</th>
                                <th class="text-center d-none d-md-table-cell" style="min-width: 120px;">Progression</th>
                                <th class="text-center d-none d-lg-table-cell" style="min-width: 140px;">Dernière action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ventesEnCoursDetails as $vente)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 me-2">
                                                @if($vente['bien']->hasMedia('images'))
                                                    <img src="{{ $vente['bien']->getFirstMediaUrl('images') }}" 
                                                         class="rounded" 
                                                         style="width: 50px; height: 50px; object-fit: cover;"
                                                         alt="{{ $vente['bien']->titre }}">
                                                @else
                                                    <div class="rounded bg-light d-flex align-items-center justify-content-center" 
                                                         style="width: 50px; height: 50px;">
                                                        <i class="ri-home-4-line fs-5 text-muted"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">{{ Str::limit($vente['bien']->titre, 40) }}</h6>
                                                <p class="text-muted small mb-0">
                                                    <i class="ri-map-pin-line"></i> {{ $vente['bien']->ville}}
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <strong class="text-success">{{ number_format($vente['prix_demande'], 0, ',', ' ') }}</strong>
                                        <small class="text-muted d-block">FCFA</small>
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $statutBadges = [
                                                'demande_client' => 'secondary',
                                                'fiche_envoyee' => 'info',
                                                'visite_planifiee' => 'primary',
                                                'offre_acceptee' => 'success',
                                            ];
                                            $badgeColor = $statutBadges[$vente['statut']] ?? 'secondary';
                                        @endphp
                                        <span class="badge bg-{{ $badgeColor }}">
                                            {{ ucfirst(str_replace('_', ' ', $vente['statut'])) }}
                                        </span>
                                        @if($vente['client_interesse'])
                                            <small class="text-muted d-block mt-1">{{ $vente['client_interesse']->name }}</small>
                                        @endif
                                    </td>
                                    <td class="text-center d-none d-md-table-cell">
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-{{ $badgeColor }}" 
                                                 role="progressbar" 
                                                 style="width: {{ $vente['progression'] }}%">
                                                {{ $vente['progression'] }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center d-none d-lg-table-cell">
                                        <small class="text-muted">
                                            <i class="ri-time-line"></i>
                                            {{ $vente['date_derniere_action']->diffForHumans() }}
                                        </small>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- Biens vendus (finalisés) -->
    <div class="card">
        <div class="card-header bg-light">
            <h6 class="mb-0">
                <i class="ri-shopping-bag-line me-2"></i>Biens vendus ({{ count($biensVendusDetails) }})
            </h6>
        </div>
        <div class="card-body p-0">
            @if(count($biensVendusDetails) > 0)
                <!-- Version Desktop -->
                <div class="table-responsive d-none d-lg-block">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="min-width: 200px;">Bien</th>
                                <th class="text-end" style="min-width: 120px;">Prix de vente</th>
                                <th class="text-center" style="min-width: 120px;">Date finalisation</th>
                                <th class="text-end" style="min-width: 120px;">Commission</th>
                                <th class="text-end" style="min-width: 120px;">Revenu net</th>
                                <th class="text-center" style="min-width: 150px;">Acheteur</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($biensVendusDetails as $vente)
                                <tr class="vente-row" 
                                    data-mois="{{ $vente['date_finalisation'] ? $vente['date_finalisation']->month : '' }}" 
                                    data-annee="{{ $vente['date_finalisation'] ? $vente['date_finalisation']->year : '' }}"
                                    data-prix="{{ $vente['prix_vente'] }}"
                                    data-commission="{{ $vente['commission_agence'] }}">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 me-2">
                                                @if($vente['bien']->hasMedia('images'))
                                                    <img src="{{ $vente['bien']->getFirstMediaUrl('images') }}" 
                                                         class="rounded" 
                                                         style="width: 50px; height: 50px; object-fit: cover;"
                                                         alt="{{ $vente['bien']->titre }}">
                                                @else
                                                    <div class="rounded bg-light d-flex align-items-center justify-content-center" 
                                                         style="width: 50px; height: 50px;">
                                                        <i class="ri-home-4-line fs-5 text-muted"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">{{ Str::limit($vente['bien']->titre, 40) }}</h6>
                                                <p class="text-muted small mb-0">
                                                    <i class="ri-map-pin-line"></i> {{ $vente['bien']->ville }}
                                                </p>
                                                <small class="text-muted">{{ $vente['bien']->typeBien->nom ?? 'N/A' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <strong class="text-success">{{ number_format($vente['prix_vente'], 0, ',', ' ') }}</strong>
                                        <small class="text-muted d-block">FCFA</small>
                                    </td>
                                    <td class="text-center">
                                        @if($vente['date_finalisation'])
                                            <span class="badge bg-success">
                                                <i class="ri-calendar-check-line"></i>
                                                {{ $vente['date_finalisation']->format('d/m/Y') }}
                                            </span>
                                            <small class="text-muted d-block mt-1">
                                                {{ $vente['date_finalisation']->diffForHumans() }}
                                            </small>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <strong class="text-warning">{{ number_format($vente['commission_agence'], 0, ',', ' ') }}</strong>
                                        <small class="text-muted d-block">FCFA ({{ number_format($vente['commission_pct'], 1) }}%)</small>
                                    </td>
                                    <td class="text-end">
                                        <strong class="text-primary">{{ number_format($vente['revenu_net'], 0, ',', ' ') }}</strong>
                                        <small class="text-muted d-block">FCFA</small>
                                    </td>
                                    <td class="text-center">
                                        <strong>{{ $vente['acheteur']->name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $vente['acheteur']->phone ?? 'N/A' }}</small>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Version Mobile -->
                <div class="d-lg-none">
                    @foreach($biensVendusDetails as $vente)
                        <div class="p-3 border-bottom vente-row" 
                             data-mois="{{ $vente['date_finalisation'] ? $vente['date_finalisation']->month : '' }}" 
                             data-annee="{{ $vente['date_finalisation'] ? $vente['date_finalisation']->year : '' }}"
                             data-prix="{{ $vente['prix_vente'] }}"
                             data-commission="{{ $vente['commission_agence'] }}">
                            <div class="d-flex align-items-start mb-3">
                                <div class="flex-shrink-0 me-3">
                                    @if($vente['bien']->hasMedia('images'))
                                        <img src="{{ $vente['bien']->getFirstMediaUrl('images') }}" 
                                             class="rounded" 
                                             style="width: 70px; height: 70px; object-fit: cover;"
                                             alt="{{ $vente['bien']->titre }}">
                                    @else
                                        <div class="rounded bg-light d-flex align-items-center justify-content-center" 
                                             style="width: 70px; height: 70px;">
                                            <i class="ri-home-4-line fs-4 text-muted"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ $vente['bien']->titre }}</h6>
                                    <p class="text-muted small mb-1">
                                        <i class="ri-map-pin-line"></i> {{ $vente['bien']->ville }}
                                    </p>
                                    @if($vente['date_finalisation'])
                                        <span class="badge bg-success">
                                            <i class="ri-check-double-line"></i> 
                                            Vendu {{ $vente['date_finalisation']->diffForHumans() }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="mb-2">
                                <strong>Acheteur:</strong> {{ $vente['acheteur']->name }}
                                <br>
                                <small class="text-muted">{{ $vente['acheteur']->phone ?? 'N/A' }}</small>
                            </div>
                            
                            <div class="row g-2">
                                <div class="col-4">
                                    <div class="text-center p-2 bg-success-subtle rounded">
                                        <small class="text-muted d-block mb-1">Prix vente</small>
                                        <strong class="text-success d-block small">{{ number_format($vente['prix_vente'], 0, ',', ' ') }}</strong>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="text-center p-2 bg-warning-subtle rounded">
                                        <small class="text-muted d-block mb-1">Commission ({{ number_format($vente['commission_pct'], 1) }}%)</small>
                                        <strong class="text-warning d-block small">{{ number_format($vente['commission_agence'], 0, ',', ' ') }}</strong>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="text-center p-2 bg-primary-subtle rounded">
                                        <small class="text-muted d-block mb-1">Revenu net</small>
                                        <strong class="text-primary d-block small">{{ number_format($vente['revenu_net'], 0, ',', ' ') }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <i class="ri-shopping-bag-line fs-1 text-muted mb-3 d-block"></i>
                    <h5 class="text-muted">Aucune vente finalisée</h5>
                    <p class="text-muted">Vous n'avez pas encore vendu de bien.</p>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('tab-scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterMois = document.getElementById('filter-mois');
        const filterAnnee = document.getElementById('filter-annee');
        const btnFiltrer = document.getElementById('btn-filtrer');
        const btnReinitialiser = document.getElementById('btn-reinitialiser');
        const venteRows = document.querySelectorAll('.vente-row');
        
        // Valeurs initiales
        const initialVentesCount = {{ $ventesFinalisesAnnee }};
        const initialMontantTotal = {{ $montantTotalNet }};
        
        function formatNumber(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
        }
        
        function mettreAJourKPI(visibleCount, montantTotal) {
            // Mettre à jour le compteur de ventes
            const kpiCountElement = document.getElementById('kpi-ventes-count');
            const kpiLabelElement = document.getElementById('kpi-ventes-label');
            const kpiMontantElement = document.getElementById('kpi-montant-encaisse');
            
            if (kpiCountElement) {
                kpiCountElement.textContent = visibleCount;
            }
            
            if (kpiLabelElement) {
                const moisSelectionne = filterMois.value;
                const anneeSelectionnee = filterAnnee.value;
                
                if (moisSelectionne && anneeSelectionnee) {
                    const moisNom = filterMois.options[filterMois.selectedIndex].text;
                    kpiLabelElement.textContent = moisNom + ' ' + anneeSelectionnee;
                } else if (anneeSelectionnee) {
                    kpiLabelElement.textContent = 'Année ' + anneeSelectionnee;
                } else if (moisSelectionne) {
                    const moisNom = filterMois.options[filterMois.selectedIndex].text;
                    kpiLabelElement.textContent = moisNom;
                } else {
                    kpiLabelElement.textContent = 'Cette année';
                }
            }
            
            if (kpiMontantElement) {
                kpiMontantElement.textContent = formatNumber(montantTotal);
            }
        }
        
        function appliquerFiltres() {
            const moisSelectionne = filterMois.value;
            const anneeSelectionnee = filterAnnee.value;
            let visibleCount = 0;
            let montantTotal = 0;
            
            venteRows.forEach(row => {
                const moisRow = row.dataset.mois;
                const anneeRow = row.dataset.annee;
                let afficher = true;
                
                // Filtrer par mois si sélectionné
                if (moisSelectionne && moisRow !== moisSelectionne) {
                    afficher = false;
                }
                
                // Filtrer par année si sélectionnée
                if (anneeSelectionnee && anneeRow !== anneeSelectionnee) {
                    afficher = false;
                }
                
                // Appliquer l'affichage
                if (afficher) {
                    row.style.display = '';
                    visibleCount++;
                    
                    // Calculer le montant NET total des ventes visibles (prix - commission)
                    const prix = parseFloat(row.dataset.prix) || 0;
                    const commission = parseFloat(row.dataset.commission) || 0;
                    montantTotal += (prix - commission);
                } else {
                    row.style.display = 'none';
                }
            });
            
            // Mettre à jour les KPI
            mettreAJourKPI(visibleCount, montantTotal);
            
            // Afficher un message si aucun résultat
            const noResultsMsg = document.getElementById('no-results-ventes');
            if (visibleCount === 0 && !noResultsMsg) {
                const container = document.querySelector('.card-body.p-0');
                if (container) {
                    const msgDiv = document.createElement('div');
                    msgDiv.id = 'no-results-ventes';
                    msgDiv.className = 'text-center py-5';
                    msgDiv.innerHTML = `
                        <i class="ri-filter-off-line fs-1 text-muted mb-3 d-block"></i>
                        <h5 class="text-muted">Aucune vente trouvée</h5>
                        <p class="text-muted">Essayez de modifier vos filtres</p>
                    `;
                    container.appendChild(msgDiv);
                }
            } else if (visibleCount > 0 && noResultsMsg) {
                noResultsMsg.remove();
            }
        }
        
        function reinitialiserFiltres() {
            filterMois.value = '';
            filterAnnee.value = '';
            
            venteRows.forEach(row => {
                row.style.display = '';
            });
            
            // Réinitialiser les KPI aux valeurs initiales
            mettreAJourKPI(initialVentesCount, initialMontantTotal);
            
            const noResultsMsg = document.getElementById('no-results-ventes');
            if (noResultsMsg) {
                noResultsMsg.remove();
            }
        }
        
        // Écouteurs d'événements
        btnFiltrer.addEventListener('click', appliquerFiltres);
        btnReinitialiser.addEventListener('click', reinitialiserFiltres);
        
        // Filtrage automatique lors du changement
        filterMois.addEventListener('change', appliquerFiltres);
        filterAnnee.addEventListener('change', appliquerFiltres);
    });
</script>
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
        align-items-center;
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
