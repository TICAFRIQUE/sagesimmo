@extends('frontend.pages.client.espaces.proprietaire.layout')

@section('title', 'Historique - Espace Propriétaire')

@section('tab-content')
    <!-- Statistiques consolidées -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-start border-4 border-info h-100">
                <div class="card-header bg-info bg-opacity-10">
                    <h6 class="mb-0 text-info">
                        <i class="ri-home-3-line me-2"></i>Revenus Locations
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <h6 class="text-muted small text-uppercase mb-2">Total Brut</h6>
                            <h5 class="text-success mb-0">{{ number_format($totalRevenusLocations / 1000000, 2) }}M</h5>
                            <small class="text-muted">FCFA</small>
                        </div>
                        <div class="col-6">
                            <h6 class="text-muted small text-uppercase mb-2">Commissions</h6>
                            <h5 class="text-warning mb-0">{{ number_format($totalCommissionsLocations / 1000000, 2) }}M</h5>
                            <small class="text-muted">FCFA</small>
                        </div>
                    </div>
                    <hr class="my-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <strong>Revenu Net Total:</strong>
                        <h5 class="text-primary mb-0">{{ number_format(($totalRevenusLocations - $totalCommissionsLocations) / 1000000, 2) }}M</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-start border-4 border-success h-100">
                <div class="card-header bg-success bg-opacity-10">
                    <h6 class="mb-0 text-success">
                        <i class="ri-shopping-bag-line me-2"></i>Revenus Ventes
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <h6 class="text-muted small text-uppercase mb-2">Total Ventes</h6>
                            <h5 class="text-success mb-0">{{ number_format($totalRevenusVentes / 1000000, 2) }}M</h5>
                            <small class="text-muted">FCFA</small>
                        </div>
                        <div class="col-6">
                            <h6 class="text-muted small text-uppercase mb-2">Commissions</h6>
                            <h5 class="text-warning mb-0">{{ number_format($totalCommissionsVentes / 1000000, 2) }}M</h5>
                            <small class="text-muted">FCFA</small>
                        </div>
                    </div>
                    <hr class="my-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <strong>Revenu Net Total:</strong>
                        <h5 class="text-primary mb-0">{{ number_format(($totalRevenusVentes - $totalCommissionsVentes) / 1000000, 2) }}M</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="filter-type" class="form-label">Type d'événement</label>
                    <select id="filter-type" class="form-select">
                        <option value="">Tous les événements</option>
                        <option value="location">Locations uniquement</option>
                        <option value="vente">Ventes uniquement</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="filter-bien" class="form-label">Bien spécifique</label>
                    <select id="filter-bien" class="form-select">
                        <option value="">Tous les biens</option>
                        @foreach($timeline->groupBy('bien.id') as $bienId => $events)
                            <option value="{{ $bienId }}">{{ $events->first()['bien']->titre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="filter-periode" class="form-label">Période</label>
                    <select id="filter-periode" class="form-select">
                        <option value="">Toute la période</option>
                        <option value="30">Derniers 30 jours</option>
                        <option value="90">Derniers 3 mois</option>
                        <option value="180">Derniers 6 mois</option>
                        <option value="365">Dernière année</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Timeline des événements -->
    <div class="card">
        <div class="card-header bg-light">
            <h6 class="mb-0">
                <i class="ri-time-line me-2"></i>Timeline des événements (<span id="event-count">{{ count($timeline) }}</span>)
            </h6>
        </div>
        <div class="card-body">
            @if(count($timeline) > 0)
                <div class="timeline-container">
                    @foreach($timeline as $index => $event)
                        <div class="timeline-item" 
                             data-type="{{ $event['type'] }}" 
                             data-bien="{{ $event['bien']->id }}"
                             data-date="{{ $event['date']->timestamp }}">
                            <div class="row g-3">
                                <!-- Indicateur temporel -->
                                <div class="col-md-2 text-center d-none d-md-block">
                                    <div class="timeline-date">
                                        <div class="badge bg-{{ $event['type'] === 'location' ? 'info' : ($event['type'] === 'vente' ? 'success' : 'secondary') }}">
                                            {{ $event['date']->format('d M Y') }}
                                        </div>
                                        <small class="text-muted d-block mt-1">{{ $event['date']->diffForHumans() }}</small>
                                    </div>
                                    <div class="timeline-icon mt-3">
                                        <div class="avatar-sm mx-auto rounded-circle bg-{{ $event['type'] === 'location' ? 'info' : ($event['type'] === 'vente' ? 'success' : 'secondary') }}-subtle">
                                            <span class="avatar-title rounded-circle text-{{ $event['type'] === 'location' ? 'info' : ($event['type'] === 'vente' ? 'success' : 'secondary') }}">
                                                <i class="{{ $event['type'] === 'location' ? 'ri-home-3-line' : 'ri-shopping-bag-line' }} fs-5"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Contenu de l'événement -->
                                <div class="col-md-10">
                                    <div class="card mb-3 border-start border-4 border-{{ $event['type'] === 'location' ? 'info' : 'success' }}">
                                        <div class="card-body">
                                            <!-- Mobile: Date en haut -->
                                            <div class="d-md-none mb-3">
                                                <span class="badge bg-{{ $event['type'] === 'location' ? 'info' : 'success' }}">
                                                    {{ $event['date']->format('d M Y') }}
                                                </span>
                                                <small class="text-muted ms-2">{{ $event['date']->diffForHumans() }}</small>
                                            </div>

                                            <div class="d-flex align-items-start">
                                                <div class="flex-shrink-0 me-3">
                                                    @if($event['bien']->hasMedia('images'))
                                                        <img src="{{ $event['bien']->getFirstMediaUrl('images') }}" 
                                                             class="rounded" 
                                                             style="width: 80px; height: 80px; object-fit: cover;"
                                                             alt="{{ $event['bien']->titre }}">
                                                    @else
                                                        <div class="rounded bg-light d-flex align-items-center justify-content-center" 
                                                             style="width: 80px; height: 80px;">
                                                            <i class="ri-home-4-line fs-3 text-muted"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <div>
                                                            <h6 class="mb-1">
                                                                <i class="{{ $event['type'] === 'location' ? 'ri-home-3-line text-info' : 'ri-shopping-bag-line text-success' }} me-2"></i>
                                                                {{ $event['titre'] }}
                                                            </h6>
                                                            <p class="text-muted mb-0 small">{{ $event['bien']->titre }}</p>
                                                            <small class="text-muted">
                                                                <i class="ri-map-pin-line"></i> {{ $event['bien']->ville }}
                                                            </small>
                                                        </div>
                                                        <span class="badge bg-{{ $event['type'] === 'location' ? 'info' : 'success' }}">
                                                            {{ ucfirst($event['type']) }}
                                                        </span>
                                                    </div>

                                                    <p class="text-muted mb-3">{{ $event['description'] }}</p>

                                                    <!-- Détails financiers -->
                                                    <div class="row g-2">
                                                        @if(isset($event['montant']))
                                                            <div class="col-6 col-md-3">
                                                                <div class="p-2 bg-success-subtle rounded text-center">
                                                                    <small class="text-muted d-block">{{ $event['type'] === 'location' ? 'Loyer' : 'Prix vente' }}</small>
                                                                    <strong class="text-success">{{ number_format($event['montant'], 0, ',', ' ') }}</strong>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if(isset($event['commission']))
                                                            <div class="col-6 col-md-3">
                                                                <div class="p-2 bg-warning-subtle rounded text-center">
                                                                    <small class="text-muted d-block">Commission</small>
                                                                    <strong class="text-warning">{{ number_format($event['commission'], 0, ',', ' ') }}</strong>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if(isset($event['revenu_net']))
                                                            <div class="col-6 col-md-3">
                                                                <div class="p-2 bg-primary-subtle rounded text-center">
                                                                    <small class="text-muted d-block">Revenu net</small>
                                                                    <strong class="text-primary">{{ number_format($event['revenu_net'], 0, ',', ' ') }}</strong>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if(isset($event['client']))
                                                            <div class="col-6 col-md-3">
                                                                <div class="p-2 bg-secondary-subtle rounded text-center">
                                                                    <small class="text-muted d-block">{{ $event['type'] === 'location' ? 'Locataire' : 'Acheteur' }}</small>
                                                                    <strong class="text-dark small">{{ Str::limit($event['client']->name, 15) }}</strong>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <!-- Statut -->
                                                    @if(isset($event['statut']))
                                                        <div class="mt-2">
                                                            <small class="text-muted">
                                                                <i class="ri-information-line"></i> 
                                                                Statut: <strong>{{ $event['statut'] }}</strong>
                                                            </small>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($index < count($timeline) - 1)
                                <div class="timeline-connector d-none d-md-block"></div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <!-- Message si aucun résultat après filtre -->
                <div id="no-results" class="text-center py-5" style="display: none;">
                    <i class="ri-filter-off-line fs-1 text-muted mb-3 d-block"></i>
                    <h5 class="text-muted">Aucun événement trouvé</h5>
                    <p class="text-muted">Essayez de modifier vos filtres</p>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="ri-time-line fs-1 text-muted mb-3 d-block"></i>
                    <h5 class="text-muted">Aucun historique</h5>
                    <p class="text-muted">Aucune transaction n'a encore été effectuée sur vos biens.</p>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('tab-scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterType = document.getElementById('filter-type');
        const filterBien = document.getElementById('filter-bien');
        const filterPeriode = document.getElementById('filter-periode');
        const timelineItems = document.querySelectorAll('.timeline-item');
        const eventCountElement = document.getElementById('event-count');
        const noResultsElement = document.getElementById('no-results');

        function applyFilters() {
            const selectedType = filterType.value;
            const selectedBien = filterBien.value;
            const selectedPeriode = filterPeriode.value;
            const now = Math.floor(Date.now() / 1000);
            let visibleCount = 0;

            timelineItems.forEach(item => {
                let isVisible = true;

                // Filtre par type
                if (selectedType && item.dataset.type !== selectedType) {
                    isVisible = false;
                }

                // Filtre par bien
                if (selectedBien && item.dataset.bien !== selectedBien) {
                    isVisible = false;
                }

                // Filtre par période
                if (selectedPeriode) {
                    const itemDate = parseInt(item.dataset.date);
                    const periodeDays = parseInt(selectedPeriode);
                    const cutoffDate = now - (periodeDays * 24 * 60 * 60);
                    
                    if (itemDate < cutoffDate) {
                        isVisible = false;
                    }
                }

                // Appliquer la visibilité
                if (isVisible) {
                    item.style.display = 'block';
                    visibleCount++;
                } else {
                    item.style.display = 'none';
                }
            });

            // Mettre à jour le compteur
            eventCountElement.textContent = visibleCount;

            // Afficher le message "Aucun résultat" si nécessaire
            if (visibleCount === 0) {
                noResultsElement.style.display = 'block';
            } else {
                noResultsElement.style.display = 'none';
            }

            // Gérer les connecteurs de timeline
            updateTimelineConnectors();
        }

        function updateTimelineConnectors() {
            const visibleItems = Array.from(timelineItems).filter(item => item.style.display !== 'none');
            
            timelineItems.forEach(item => {
                const connector = item.querySelector('.timeline-connector');
                if (connector) {
                    connector.style.display = 'none';
                }
            });

            visibleItems.forEach((item, index) => {
                if (index < visibleItems.length - 1) {
                    const connector = item.querySelector('.timeline-connector');
                    if (connector) {
                        connector.style.display = 'block';
                    }
                }
            });
        }

        // Écouteurs d'événements
        filterType.addEventListener('change', applyFilters);
        filterBien.addEventListener('change', applyFilters);
        filterPeriode.addEventListener('change', applyFilters);
    });
</script>
@endsection

@section('styles')
@parent
<style>
    .timeline-container {
        position: relative;
    }

    .timeline-item {
        position: relative;
        padding-bottom: 30px;
    }

    .timeline-connector {
        position: absolute;
        left: 50px;
        top: 100px;
        bottom: -30px;
        width: 2px;
        background: linear-gradient(180deg, #e9ecef 0%, transparent 100%);
    }

    .timeline-date {
        position: sticky;
        top: 20px;
    }

    .timeline-icon {
        position: relative;
    }

    .avatar-sm {
        height: 50px;
        width: 50px;
    }

    .avatar-title {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100%;
        width: 100%;
    }

    @media (max-width: 768px) {
        .timeline-connector {
            display: none !important;
        }
    }
</style>
@endsection
