@extends('frontend.pages.client.layout')

@section('client-content')
    <div class="client-content">
        <h2 class="mb-4">
            <i class="ri-dashboard-line"></i> Tableau de bord
        </h2>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('info'))
            <div class="alert alert-info alert-dismissible fade show">
                {{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Statistiques Générales -->
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

        <!-- Mes Espaces -->
        @if ($estProprietaire || $estLocataire || $estAcheteur)
            <h5 class="mb-3">
                <i class="ri-apps-line"></i> Mes espaces
            </h5>
            <div class="row mb-4">
                @if ($estProprietaire && $nombreBiensProprio > 0)
                    <div class="col-md-4 mb-3">
                        <a href="{{ route('client.proprietaire') }}" class="text-decoration-none">
                            <div class="card h-100 border-0 shadow-sm hover-card">
                                <div class="card-body text-center py-4">
                                    <div class="avatar-lg mx-auto mb-3 bg-success-subtle rounded-circle">
                                        <span class="avatar-title rounded-circle text-success fs-1">
                                            <i class="ri-building-line"></i>
                                        </span>
                                    </div>
                                    <h5 class="text-success mb-2">Espace Propriétaire</h5>
                                    <p class="text-muted small mb-3">Gérez vos biens confiés à l'agence</p>
                                    <div class="d-flex justify-content-around">
                                        <div>
                                            <h4 class="mb-0 text-success">{{ $nombreBiensProprio }}</h4>
                                            <small class="text-muted">Biens</small>
                                        </div>
                                        <div>
                                            <h4 class="mb-0 text-info">{{ $biensLouesProprio }}</h4>
                                            <small class="text-muted">Loués</small>
                                        </div>
                                        <div>
                                            <h4 class="mb-0 text-primary">{{ $biensVendusProprio }}</h4>
                                            <small class="text-muted">Vendus</small>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <span class="btn btn-success btn-sm">
                                            Accéder <i class="ri-arrow-right-line ms-1"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @endif

                @if ($estLocataire && $locationActive)
                    <div class="col-md-4 mb-3">
                        <a href="{{ route('client.locataire') }}" class="text-decoration-none">
                            <div class="card h-100 border-0 shadow-sm hover-card">
                                <div class="card-body text-center py-4">
                                    <div class="avatar-lg mx-auto mb-3 bg-info-subtle rounded-circle">
                                        <span class="avatar-title rounded-circle text-info fs-1">
                                            <i class="ri-home-heart-line"></i>
                                        </span>
                                    </div>
                                    <h5 class="text-info mb-2">Espace Locataire</h5>
                                    <p class="text-muted small mb-3">Consultez votre location actuelle</p>
                                    <div class="mb-3">
                                        <h6 class="text-success mb-1">
                                            {{ number_format($locationActive->montant_mensuel, 0, ',', ' ') }} FCFA</h6>
                                        <small class="text-muted">Loyer mensuel</small>
                                    </div>
                                    @if ($prochaineEcheanceLocataire)
                                        <div>
                                            <small class="text-muted">Prochaine échéance :</small>
                                            <p class="mb-0">
                                                {{ \Carbon\Carbon::parse($prochaineEcheanceLocataire->date_echeance)->format('d/m/Y') }}
                                            </p>
                                        </div>
                                    @endif
                                    <div class="mt-3">
                                        <span class="btn btn-info btn-sm">
                                            Accéder <i class="ri-arrow-right-line ms-1"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @endif

                @if ($estAcheteur && $venteActive)
                    <div class="col-md-4 mb-3">
                        <a href="{{ route('client.acheteur') }}" class="text-decoration-none">
                            <div class="card h-100 border-0 shadow-sm hover-card">
                                <div class="card-body text-center py-4">
                                    <div class="avatar-lg mx-auto mb-3 bg-primary-subtle rounded-circle">
                                        <span class="avatar-title rounded-circle text-primary fs-1">
                                            <i class="ri-shopping-bag-3-line"></i>
                                        </span>
                                    </div>
                                    <h5 class="text-primary mb-2">Espace Acheteur</h5>
                                    <p class="text-muted small mb-3">Suivez votre achat immobilier</p>
                                    <div class="mb-3">
                                        <h6 class="text-success mb-1">{{ number_format($montantPaye, 0, ',', ' ') }} FCFA
                                        </h6>
                                        <small class="text-muted">Montant payé</small>
                                    </div>
                                    @if ($montantRestant > 0)
                                        <div>
                                            <small class="text-muted">Restant :</small>
                                            <p class="mb-0 text-warning">{{ number_format($montantRestant, 0, ',', ' ') }}
                                                FCFA</p>
                                        </div>
                                    @else
                                        <div class="badge bg-success">
                                            <i class="ri-check-double-line"></i> Paiement complet
                                        </div>
                                    @endif
                                    <div class="mt-3">
                                        <span class="btn btn-primary btn-sm">
                                            Accéder <i class="ri-arrow-right-line ms-1"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @endif
            </div>
        @endif

        <!-- Bloc Propriétaire dans Dashboard -->
        @if ($estProprietaire && $nombreBiensProprio > 0)
            <h5 class="mb-3 mt-4">
                <i class="ri-building-line text-success"></i> Aperçu Espace Propriétaire
            </h5>
            <div class="card mb-4 border-success" style="border-left: 4px solid #0ab39c;">
                <div class="card-body">
                    <!-- Résumé statistiques -->
                    <div class="row mb-4">
                        @if ($loyersImpayes > 0)
                            <div class="col-md-6">
                                <div class="alert alert-danger d-flex align-items-center mb-0">
                                    <i class="ri-alert-line fs-3 me-3"></i>
                                    <div>
                                        <strong>Loyers impayés : {{ number_format($loyersImpayes, 0, ',', ' ') }}
                                            FCFA</strong>
                                        <p class="mb-0 small">L'agence s'occupe du recouvrement</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if ($prochaineEcheanceProprio)
                            <div class="col-md-6">
                                <div class="alert alert-info d-flex align-items-center mb-0">
                                    <i class="ri-calendar-check-line fs-3 me-3"></i>
                                    <div>
                                        <strong>Prochaine échéance :
                                            {{ \Carbon\Carbon::parse($prochaineEcheanceProprio->date_echeance)->format('d/m/Y') }}</strong>
                                        <p class="mb-0 small">Montant :
                                            {{ number_format($prochaineEcheanceProprio->montant_du, 0, ',', ' ') }} FCFA
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Revenus Mensuels -->
                    @if (count($revenusMensuels) > 0)
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">
                                    <i class="ri-line-chart-line me-2"></i>Revenus des 6 derniers mois
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover table-nowrap align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Mois</th>
                                                <th class="text-end">Brut</th>
                                                <th class="text-end">Commission</th>
                                                <th class="text-end">Net</th>
                                                <th class="text-center">% Commission</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($revenusMensuels as $revenu)
                                                <tr>
                                                    <td>
                                                        <strong>{{ \Carbon\Carbon::parse($revenu['mois'] . '-01')->locale('fr')->isoFormat('MMMM YYYY') }}</strong>
                                                    </td>
                                                    <td class="text-end">{{ number_format($revenu['brut'], 0, ',', ' ') }}
                                                        FCFA</td>
                                                    <td class="text-end text-warning">
                                                        {{ number_format($revenu['commission'], 0, ',', ' ') }} FCFA</td>
                                                    <td class="text-end text-primary">
                                                        <strong>{{ number_format($revenu['net'], 0, ',', ' ') }}
                                                            FCFA</strong>
                                                    </td>
                                                    <td class="text-center">
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
                    <div class="card">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">
                                <i class="ri-building-4-line me-2"></i>Détails par bien
                            </h6>
                        </div>
                        <div class="card-body p-0">
                            @foreach ($detailsBiensProprio as $detail)
                                <div class="border-bottom p-3">
                                    <div class="row align-items-center">
                                        <div class="col-md-3">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0 me-3">
                                                    @if ($detail['bien']->hasMedia('images'))
                                                        <img src="{{ $detail['bien']->getFirstMediaUrl('images') }}"
                                                            class="rounded" alt="{{ $detail['bien']->titre }}"
                                                            style="width: 70px; height: 70px; object-fit: cover;">
                                                    @else
                                                        <div class="rounded bg-light d-flex align-items-center justify-content-center"
                                                            style="width: 70px; height: 70px;">
                                                            <i class="ri-home-4-line fs-3 text-muted"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1">{{ Str::limit($detail['bien']->titre, 35) }}</h6>
                                                    <p class="text-muted small mb-0">
                                                        <i class="ri-map-pin-line"></i> {{ $detail['bien']->ville }}
                                                    </p>
                                                    <small class="text-muted">Réf:
                                                        {{ $detail['bien']->reference }}</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2 text-center">
                                            <span
                                                class="badge fs-6 bg-{{ $detail['bien']->statut == 'disponible' ? 'warning' : ($detail['bien']->statut == 'loue' ? 'info' : 'success') }}">
                                                {{ ucfirst($detail['bien']->statut) }}
                                            </span>
                                            <p class="text-muted small mb-0 mt-1">
                                                {{ $detail['bien']->typeBien->nom ?? 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-2 text-center">
                                            <p class="text-muted small mb-1">Loyers encaissés</p>
                                            <h6 class="mb-0 text-success">
                                                {{ number_format($detail['loyers_payes'], 0, ',', ' ') }}</h6>
                                        </div>
                                        <div class="col-md-2 text-center">
                                            <p class="text-muted small mb-1">Commission</p>
                                            <h6 class="mb-0 text-warning">
                                                {{ number_format($detail['commission_agence'], 0, ',', ' ') }}</h6>
                                        </div>
                                        <div class="col-md-2 text-center">
                                            <p class="text-muted small mb-1">Revenu Net</p>
                                            <h6 class="mb-0 text-primary">
                                                {{ number_format($detail['revenu_net'], 0, ',', ' ') }}</h6>
                                        </div>
                                        <div class="col-md-1 text-center">
                                            @if ($detail['loyers_impayes'] > 0)
                                                <span class="badge bg-danger" data-bs-toggle="tooltip"
                                                    title="Impayés: {{ number_format($detail['loyers_impayes'], 0, ',', ' ') }} FCFA">
                                                    <i class="ri-alert-line"></i>
                                                </span>
                                            @endif
                                            @if ($detail['location_active'])
                                                <span class="badge bg-info mt-1" data-bs-toggle="tooltip"
                                                    title="Location en cours">
                                                    <i class="ri-home-heart-line"></i>
                                                </span>
                                            @endif
                                            @if ($detail['vente_finalisee'])
                                                <span class="badge bg-success mt-1" data-bs-toggle="tooltip"
                                                    title="Vendu">
                                                    <i class="ri-check-double-line"></i>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Note informative -->
                    <div class="alert alert-light border mt-4">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <i class="ri-information-line text-primary fs-4"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="alert-heading">Mode consultation uniquement</h6>
                                <p class="mb-0">Vous consultez vos biens confiés à notre agence. Pour toute modification
                                    ou question, veuillez contacter votre gestionnaire de compte.</p>
                            </div>
                        </div>
                    </div>
                </div>
        @endif

        <!-- Prochaines visites -->
        @if ($prochainesVisites->count() > 0)
            <div class="mb-4">
                <h4 class="mb-3">
                    <i class="ri-calendar-event-line"></i> Prochaines visites
                </h4>
                @foreach ($prochainesVisites as $visite)
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
                                    <a href="{{ route('client.demandes.show', $visite->id) }}"
                                        class="btn btn-sm btn-outline-primary">
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

        @if ($dernieresDemandes->count() > 0)
            <div class="row">
                @foreach ($dernieresDemandes as $demande)
                    <div class="col-12 mb-3">
                        <div class="card shadow-sm hover-lift">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-2">
                                        @if ($demande->annonce->hasMedia('image_principale'))
                                            <img src="{{ $demande->annonce->getFirstMediaUrl('image_principale') }}"
                                                class="img-fluid rounded"
                                                alt="{{ $demande->annonce->titre }}"
                                                style="height: 80px; object-fit: cover; width: 100%;">
                                        @else
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                                style="height: 80px;">
                                                <i class="ri-image-line fs-2 text-muted"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-md-5">
                                        <h6 class="mb-1 fw-bold">{{ $demande->annonce->titre }}</h6>
                                        <div class="d-flex flex-wrap gap-2 mb-2">
                                            <small class="text-muted">
                                                <i class="ri-file-line"></i>
                                                {{ $demande->annonce->reference }}
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
                                            <div class="progress-bar bg-success" role="progressbar"
                                                style="width: {{ $demande->progression }}%"
                                                aria-valuenow="{{ $demande->progression }}" aria-valuemin="0"
                                                aria-valuemax="100"></div>
                                        </div>
                                        <small class="text-muted">{{ $demande->progression }}%
                                            complété</small>
                                    </div>
                                    <div class="col-md-3 text-end">
                                        <a href="{{ route('client.demandes.show', $demande->id) }}"
                                            class="btn btn-sm btn-primary w-100 mb-2">
                                            <i class="ri-eye-line"></i> Voir le suivi détaillé
                                        </a>
                                        @if ($demande->date_visite && $demande->statut == 'visite_planifiee')
                                            <div class="alert alert-info py-2 px-2 mb-0 small">
                                                <i class="ri-calendar-event-line"></i>
                                                Visite:
                                                {{ \Carbon\Carbon::parse($demande->date_visite)->format('d/m/Y H:i') }}
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
                <a href="{{ route('properties.index') }}" class="alert-link fw-bold">Parcourez nos biens</a>
                pour commencer votre recherche !
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

                .hover-card {
                    transition: all 0.3s ease;
                }

                .hover-card:hover {
                    transform: translateY(-5px);
                    box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.2) !important;
                }

                .stat-card {
                    background: white;
                    border-radius: 10px;
                    padding: 20px;
                    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
                    transition: all 0.3s ease;
                }

                .stat-card:hover {
                    transform: translateY(-3px);
                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
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
                    color: #405189;
                    margin-bottom: 5px;
                }

                .stat-label {
                    font-size: 14px;
                    color: #878a99;
                    font-weight: 500;
                }

                .avatar-lg {
                    height: 64px;
                    width: 64px;
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
            </style>
        @endsection
