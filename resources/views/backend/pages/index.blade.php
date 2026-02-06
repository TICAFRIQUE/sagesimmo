@extends('backend.layouts.master')
@section('title')
    Tableau de bord
@endsection
@section('css')
    <style>
        .stat-card {
            border-radius: 10px;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
        }
    </style>
@endsection
@section('content')
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between flex-wrap">
                <div>
                    <h4 class="fs-20 mb-1">Bonjour, {{ Auth::user()->username }} 👋</h4>
                    <p class="text-muted mb-0">Voici un aperçu de votre activité immobilière</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('backend.annonces.create') }}" class="btn btn-primary">
                        <i class="ri-add-line me-1"></i>Nouvelle annonce
                    </a>
                    <a href="{{ route('backend.locations.create') }}" class="btn btn-info">
                        <i class="ri-home-heart-line me-1"></i>Nouvelle location
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- KPIs Principaux -->
    <div class="row">
        <!-- Total des biens -->
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-primary-subtle text-primary me-3">
                            <i class="ri-building-line"></i>
                        </div>
                        <div class="flex-grow-1">
                            <p class="text-muted mb-1">Total des biens</p>
                            <h4 class="mb-0">{{ $totalBiens }}</h4>
                            <small class="text-muted">
                                <span class="text-success">{{ $biensDisponibles }}</span> disponibles,
                                <span class="text-warning">{{ $biensLoues }}</span> loués,
                                <span class="text-danger">{{ $biensVendus }}</span> vendus
                            </small>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('backend.annonces.index') }}" class="btn btn-sm btn-outline-primary w-100">
                            <i class="ri-eye-line me-1"></i>Voir les annonces
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenus du mois -->
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-success-subtle text-success me-3">
                            <i class="ri-money-dollar-circle-line"></i>
                        </div>
                        <div class="flex-grow-1">
                            <p class="text-muted mb-1">Revenus du mois</p>
                            <h4 class="mb-0">{{ number_format($revenusTotalMois, 0, ',', ' ') }}</h4>
                            <small>
                                @if($evolutionRevenus > 0)
                                    <span class="text-success"><i class="ri-arrow-up-line"></i> +{{ number_format($evolutionRevenus, 1) }}%</span>
                                @elseif($evolutionRevenus < 0)
                                    <span class="text-danger"><i class="ri-arrow-down-line"></i> {{ number_format($evolutionRevenus, 1) }}%</span>
                                @else
                                    <span class="text-muted">= 0%</span>
                                @endif
                                vs mois précédent
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Taux d'occupation -->
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-info-subtle text-info me-3">
                            <i class="ri-pie-chart-line"></i>
                        </div>
                        <div class="flex-grow-1">
                            <p class="text-muted mb-1">Taux d'occupation</p>
                            <h4 class="mb-0">{{ number_format($tauxOccupation, 1) }}%</h4>
                            <div class="progress mt-2" style="height: 6px;">
                                <div class="progress-bar bg-info" style="width: {{ $tauxOccupation }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Impayés -->
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-danger-subtle text-danger me-3">
                            <i class="ri-alert-line"></i>
                        </div>
                        <div class="flex-grow-1">
                            <p class="text-muted mb-1">Impayés</p>
                            <h4 class="mb-0 text-danger">{{ number_format($montantImpaye, 0, ',', ' ') }}</h4>
                            <small class="text-muted">{{ $nombreEcheancesEnRetard }} échéance(s)</small>
                        </div>
                    </div>
                    @if($nombreEcheancesEnRetard > 0)
                    <div class="mt-3">
                        <a href="{{ route('backend.locations.index') }}?statut=actif" class="btn btn-sm btn-outline-danger w-100">
                            <i class="ri-alert-line me-1"></i>Voir les retards
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Section Locations -->
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header bg-info-subtle">
                    <h5 class="card-title mb-0">
                        <i class="ri-home-heart-line me-2"></i>Locations
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="border rounded p-3 text-center">
                                <h3 class="text-info mb-1">{{ $locationsActives }}</h3>
                                <small class="text-muted">Locations actives</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-3 text-center">
                                <h3 class="text-primary mb-1">{{ number_format($loyersMensuels, 0, ',', ' ') }}</h3>
                                <small class="text-muted">Loyers mensuels (FCFA)</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-3 text-center">
                                <h3 class="text-success mb-1">{{ $echeancesMoisPayees }}/{{ $echeancesMoisTotal }}</h3>
                                <small class="text-muted">Échéances du mois</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-3 text-center">
                                <h3 class="text-warning mb-1">{{ $nouvellesDemandesLocation }}</h3>
                                <small class="text-muted">Nouvelles demandes</small>
                            </div>
                        </div>
                    </div>

                    @if($locationsExpiration > 0)
                    <div class="alert alert-warning mt-3 mb-0">
                        <i class="ri-time-line me-1"></i>
                        <strong>{{ $locationsExpiration }}</strong> location(s) arrivent à expiration dans 30 jours
                    </div>
                    @endif

                    <div class="mt-3 d-flex gap-2">
                        <a href="{{ route('backend.locations.index') }}" class="btn btn-info btn-sm flex-fill">
                            <i class="ri-eye-line me-1"></i>Voir toutes
                        </a>
                        @if($nouvellesDemandesLocation > 0)
                        <a href="{{ route('backend.locations.index') }}?statut=demande_client" class="btn btn-warning btn-sm flex-fill">
                            <i class="ri-notification-line me-1"></i>Traiter demandes
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Section Ventes -->
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header bg-primary-subtle">
                    <h5 class="card-title mb-0">
                        <i class="ri-shopping-cart-line me-2"></i>Ventes
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="border rounded p-3 text-center">
                                <h3 class="text-primary mb-1">{{ $ventesEnCours }}</h3>
                                <small class="text-muted">Ventes en cours</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-3 text-center">
                                <h3 class="text-success mb-1">{{ $ventesFinaliseesMois }}</h3>
                                <small class="text-muted">Finalisées ce mois</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-3 text-center">
                                <h3 class="text-warning mb-1">{{ $nouvellesDemandesVente }}</h3>
                                <small class="text-muted">Nouvelles demandes</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-3 text-center">
                                <h3 class="text-info mb-1">{{ number_format($valeurVentesEnCours / 1000000, 1) }}M</h3>
                                <small class="text-muted">Valeur en cours (FCFA)</small>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 d-flex gap-2">
                        <a href="{{ route('backend.ventes.index') }}" class="btn btn-primary btn-sm flex-fill">
                            <i class="ri-eye-line me-1"></i>Voir toutes
                        </a>
                        @if($nouvellesDemandesVente > 0)
                        <a href="{{ route('backend.ventes.index') }}?statut=demande_client" class="btn btn-warning btn-sm flex-fill">
                            <i class="ri-notification-line me-1"></i>Traiter demandes
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenus détaillés et Clients -->
    <div class="row">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ri-bar-chart-line me-2"></i>Détails des revenus du mois
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center p-3 border-end">
                                <h5 class="text-success mb-1">{{ number_format($loyersPercus, 0, ',', ' ') }}</h5>
                                <small class="text-muted">Loyers perçus</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3 border-end">
                                <h5 class="text-info mb-1">{{ number_format($cautionsPercues, 0, ',', ' ') }}</h5>
                                <small class="text-muted">Cautions</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3 border-end">
                                <h5 class="text-primary mb-1">{{ number_format($ventesRealisees, 0, ',', ' ') }}</h5>
                                <small class="text-muted">Ventes</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3">
                                <h5 class="text-warning mb-1">{{ number_format($commissionsAgence, 0, ',', ' ') }}</h5>
                                <small class="text-muted">Commissions</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ri-user-line me-2"></i>Clients
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Total clients</span>
                            <strong>{{ $totalClients }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Propriétaires</span>
                            <strong>{{ $totalProprietaires }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Nouveaux ce mois</span>
                            <span class="badge bg-success">+{{ $nouveauxClientsMois }}</span>
                        </div>
                    </div>
                    <a href="{{ route('backend.users.index') }}" class="btn btn-outline-primary btn-sm w-100">
                        <i class="ri-eye-line me-1"></i>Voir tous les clients
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Activités récentes et Alertes -->
    <div class="row">
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header bg-warning-subtle">
                    <h5 class="card-title mb-0">
                        <i class="ri-notification-line me-2"></i>Alertes & Activités
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <div>
                            <i class="ri-calendar-event-line text-info me-2"></i>
                            <span>Visites cette semaine</span>
                        </div>
                        <span class="badge bg-info">{{ $visitesTotal }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <div>
                            <i class="ri-notification-line text-warning me-2"></i>
                            <span>Notifications</span>
                        </div>
                        <span class="badge bg-warning">{{ $notificationsNonLues }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <i class="ri-alert-line text-danger me-2"></i>
                            <span>Retards de paiement</span>
                        </div>
                        <span class="badge bg-danger">{{ $nombreEcheancesEnRetard }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ri-time-line me-2"></i>Prochaines échéances (7 jours)
                    </h5>
                </div>
                <div class="card-body">
                    @if($prochainesEcheances->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Locataire</th>
                                        <th>Bien</th>
                                        <th>Montant</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($prochainesEcheances as $echeance)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($echeance->date_echeance)->format('d/m/Y') }}</td>
                                        <td>{{ $echeance->location->locataire->username }}</td>
                                        <td>{{ Str::limit($echeance->location->annonce->titre, 30) }}</td>
                                        <td>{{ number_format($echeance->montant_du, 0, ',', ' ') }} FCFA</td>
                                        <td>
                                            <a href="{{ route('backend.locations.show', $echeance->location) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="ri-eye-line"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="ri-calendar-line display-4"></i>
                            <p class="mt-2">Aucune échéance à venir dans les 7 prochains jours</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Dernières locations et ventes -->
    <div class="row">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ri-home-heart-line me-2"></i>Dernières locations
                    </h5>
                </div>
                <div class="card-body">
                    @if($dernieresLocations->count() > 0)
                        @foreach($dernieresLocations as $location)
                        <div class="d-flex align-items-center mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">
                                    <a href="{{ route('backend.locations.show', $location) }}" class="text-dark">
                                        {{ Str::limit($location->annonce->titre, 40) }}
                                    </a>
                                </h6>
                                <small class="text-muted">
                                    <i class="ri-user-line"></i> {{ $location->locataire->username }}
                                </small>
                            </div>
                            <div class="text-end">
                                @if($location->statut == 'actif')
                                    <span class="badge bg-success">Actif</span>
                                @elseif($location->statut == 'demande_client')
                                    <span class="badge bg-warning">Demande</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($location->statut) }}</span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                        <a href="{{ route('backend.locations.index') }}" class="btn btn-sm btn-outline-info w-100">
                            Voir toutes les locations
                        </a>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="ri-home-heart-line display-4"></i>
                            <p class="mt-2">Aucune location active</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ri-shopping-cart-line me-2"></i>Dernières ventes
                    </h5>
                </div>
                <div class="card-body">
                    @if($dernieresVentes->count() > 0)
                        @foreach($dernieresVentes as $vente)
                        <div class="d-flex align-items-center mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">
                                    <a href="{{ route('backend.ventes.show', $vente) }}" class="text-dark">
                                        {{ Str::limit($vente->annonce->titre, 40) }}
                                    </a>
                                </h6>
                                <small class="text-muted">
                                    <i class="ri-user-line"></i> {{ $vente->client->username }}
                                </small>
                            </div>
                            <div class="text-end">
                                @if($vente->statut == 'en_cours')
                                    <span class="badge bg-primary">En cours</span>
                                @elseif($vente->statut == 'finalise')
                                    <span class="badge bg-success">Finalisé</span>
                                @elseif($vente->statut == 'demande_client')
                                    <span class="badge bg-warning">Demande</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($vente->statut) }}</span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                        <a href="{{ route('backend.ventes.index') }}" class="btn btn-sm btn-outline-primary w-100">
                            Voir toutes les ventes
                        </a>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="ri-shopping-cart-line display-4"></i>
                            <p class="mt-2">Aucune vente en cours</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
