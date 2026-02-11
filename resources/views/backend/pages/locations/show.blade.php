@extends('backend.layouts.master')
@section('title')
    Détails de la location
@endsection
@section('css')
    <style>
        .info-card {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
        }

        .workflow-step {
            padding: 15px;
            border-left: 4px solid #e9ecef;
            margin-bottom: 15px;
            border-radius: 4px;
            background: #fff;
        }

        .workflow-step.active {
            border-left-color: #0ab39c;
            background: #f0fdf4;
        }

        .workflow-step.completed {
            border-left-color: #299cdb;
            background: #f0f9ff;
        }

        .progress-bar-custom {
            height: 25px;
            font-size: 14px;
        }
    </style>
@endsection
@section('content')
    @component('backend.components.breadcrumb')
        @slot('li_1')
            <a href="{{ route('backend.locations.index') }}">Locations</a>
        @endslot
        @slot('title')
            Location #{{ $location->id }}
        @endslot
    @endcomponent

    <!-- Progression du workflow -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">Progression du workflow</h5>
                    <div class="progress progress-bar-custom mb-2">
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $location->progression }}%">
                            {{ $location->progression }}%
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="badge {{ $location->statut_badge }}" style="font-size: 1rem;">
                            {{ ucfirst(str_replace('_', ' ', $location->statut)) }}
                        </span>
                        @if ($location->date_finalisation)
                            <small class="text-muted">
                                <i class="ri-check-line text-success"></i>
                                Activée le {{ $location->date_finalisation->format('d/m/Y') }}
                            </small>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Colonne gauche: Workflow et Actions -->
        <div class="col-lg-8">
            <!-- Workflow actuel -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="ri-flow-chart me-2"></i>Workflow de location</h5>
                </div>
                <div class="card-body">
                    <!-- Message du client -->
                    @if ($location->message_client)
                        <div class="workflow-step completed">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1"><i class="ri-message-2-line me-1"></i>Message initial du client</h6>
                                    <p class="mb-0">{{ $location->message_client }}</p>
                                </div>
                                <i class="ri-checkbox-circle-fill text-success" style="font-size: 24px;"></i>
                            </div>
                        </div>
                    @endif

                    <!-- Étape 1: Envoi de la fiche -->
                    <div
                        class="workflow-step {{ in_array($location->statut, ['retour_prospect', 'fiche_envoyee', 'visite_planifiee', 'en_attente_paiement', 'actif']) ? 'completed' : (in_array($location->statut, ['demande_client', 'brouillon']) ? 'active' : '') }}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <h6 class="mb-2">
                                    <i class="ri-file-text-line me-1"></i>
                                    1. Envoi de la fiche au client
                                </h6>
                                @if (in_array($location->statut, ['demande_client', 'brouillon']))
                                    @if ($location->statut == 'brouillon')
                                        <div class="alert alert-info mb-2">
                                            <i class="ri-information-line me-1"></i>
                                            <strong>Location en brouillon.</strong> Vous pouvez soit envoyer la fiche au
                                            client, soit passer directement à l'étape suivante si la fiche a déjà été
                                            envoyée manuellement.
                                        </div>
                                        <button class="btn btn-sm btn-primary me-2" data-bs-toggle="modal"
                                            data-bs-target="#envoyerFicheModal">
                                            <i class="ri-send-plane-line me-1"></i>Envoyer la fiche par email
                                        </button>
                                        <button class="btn btn-sm btn-success" onclick="marquerFicheEnvoyee()">
                                            <i class="ri-check-line me-1"></i>Marquer comme "Fiche envoyée"
                                        </button>
                                    @else
                                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#envoyerFicheModal">
                                            <i class="ri-send-plane-line me-1"></i>Envoyer la fiche
                                        </button>
                                    @endif
                                @else
                                    <span class="badge bg-success"><i class="ri-check-line me-1"></i>
                                        Fiche envoyée</span>
                                @endif
                            </div>
                            @if (in_array($location->statut, [
                                    'retour_prospect',
                                    'fiche_envoyee',
                                    'visite_planifiee',
                                    'en_attente_paiement',
                                    'actif',
                                ]))
                                <i class="ri-checkbox-circle-fill text-success" style="font-size: 24px;"></i>
                            @endif
                        </div>
                    </div>

                    <!-- Étape 2: Attente retour du prospect -->
                    <div
                        class="workflow-step {{ in_array($location->statut, ['fiche_envoyee', 'visite_planifiee', 'en_attente_paiement', 'actif']) ? 'completed' : ($location->statut == 'retour_prospect' ? 'active' : '') }}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <h6 class="mb-2">
                                    <i class="ri-time-line me-1"></i>
                                    2. Attente retour du prospect
                                </h6>
                                @if ($location->statut == 'retour_prospect')
                                    <p class="text-muted mb-2">En attente de la confirmation d'intérêt du client.</p>
                                    <button class="btn btn-sm btn-success" data-bs-toggle="modal"
                                        data-bs-target="#confirmerRetourProspectModal">
                                        <i class="ri-checkbox-circle-line me-1"></i>Confirmer l'intérêt
                                    </button>
                                    <button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                        data-bs-target="#refuserRetourProspectModal">
                                        <i class="ri-close-line me-1"></i>Client non intéressé
                                    </button>
                                @else
                                    <span class="badge bg-success"><i class="ri-check-line me-1"></i>
                                        {{ [
                                            1 => 'Client intéressé',
                                            0 => 'Client non intéressé',
                                        ][$location->client_interesse_retour] ?? 'En attente' }}
                                    </span>
                                @endif
                            </div>
                            @if (in_array($location->statut, ['fiche_envoyee', 'visite_planifiee', 'en_attente_paiement', 'actif']))
                                <i class="ri-checkbox-circle-fill text-success" style="font-size: 24px;"></i>
                            @endif
                        </div>
                    </div>

                    <!-- Étape 3: Planification de la visite -->
                    <div
                        class="workflow-step {{ in_array($location->statut, ['visite_planifiee', 'en_attente_paiement', 'actif']) ? 'completed' : ($location->statut == 'fiche_envoyee' ? 'active' : '') }}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <h6 class="mb-2">
                                    <i class="ri-calendar-check-line me-1"></i>
                                    3. Planification de la visite
                                </h6>
                                @if ($location->statut == 'fiche_envoyee')
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#planifierVisiteModal">
                                        <i class="ri-calendar-event-line me-1"></i>Planifier la visite
                                    </button>
                                @elseif($location->date_visite)
                                    <p class="mb-0">
                                        <i class="ri-time-line me-1"></i>
                                        <strong>Date:</strong>
                                        {{ \Carbon\Carbon::parse($location->date_visite)->format('d/m/Y à H:i') }}
                                    </p>
                                @endif
                            </div>
                            @if (in_array($location->statut, ['visite_planifiee', 'en_attente_paiement', 'actif']))
                                <i class="ri-checkbox-circle-fill text-success" style="font-size: 24px;"></i>
                            @endif
                        </div>
                    </div>

                    <!-- Étape 3: Visite effectuée -->
                    <div
                        class="workflow-step {{ in_array($location->statut, ['en_attente_paiement', 'actif']) ? 'completed' : ($location->statut == 'visite_planifiee' ? 'active' : '') }}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <h6 class="mb-2">
                                    <i class="ri-home-smile-line me-1"></i>
                                    4. Visite effectuée
                                </h6>
                                @if ($location->statut == 'visite_planifiee')
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#visiteEffectueeModal">
                                        <i class="ri-checkbox-line me-1"></i>Retour de la visite
                                    </button>
                                @elseif($location->client_interesse_visite)
                                    <div class="alert alert-info mt-2 mb-0">
                                        <strong>Compte rendu:</strong><br>
                                        {{ $location->client_interesse_visite == 1 ? 'Client intéressé apres la visite' : ($location->client_interesse_visite == 0 ? 'Client non intéressé apres la visite' : 'En attente') }}

                                    </div>
                                @endif

                            </div>
                            @if (in_array($location->statut, ['en_attente_paiement', 'actif']))
                                <i class="ri-checkbox-circle-fill text-success" style="font-size: 24px;"></i>
                            @endif
                        </div>
                    </div>

                    <!-- Étape 4: Gestion des paiements -->
                    <div
                        class="workflow-step {{ $location->statut == 'actif' ? 'completed' : ($location->statut == 'en_attente_paiement' ? 'active' : '') }}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <h6 class="mb-2">
                                    <i class="ri-money-dollar-circle-line me-1"></i>
                                    5. Gestion des paiements
                                </h6>
                                @if ($location->statut == 'en_attente_paiement')

                                    <!-- Configuration du paiement -->
                                    @if ($location->loyer_mensuel && $location->jour_paiement)
                                        <!-- Configuration complète -->
                                        <div class="info-card mt-2">
                                            <h6 class="mb-2"><i class="ri-settings-line me-1"></i>Configuration</h6>
                                            <div class="row mb-2">
                                                <div class="col-6"><strong>Loyer mensuel:</strong></div>
                                                <div class="col-6 text-end">
                                                    {{ number_format($location->loyer_mensuel, 0, ',', ' ') }} FCFA</div>
                                            </div>
                                            @if ($location->caution)
                                                <div class="row mb-2">
                                                    <div class="col-6"><strong>Caution ({{ $location->nombre_cautions }}
                                                            mois):</strong></div>
                                                    <div class="col-6 text-end">
                                                        {{ number_format($location->caution, 0, ',', ' ') }} FCFA</div>
                                                </div>
                                            @endif
                                            @if ($location->avance_sur_loyer)
                                                <div class="row mb-2">
                                                    <div class="col-6"><strong>Avance sur loyer:</strong></div>
                                                    <div class="col-6 text-end">
                                                        {{ $location->avance_sur_loyer }} mois
                                                        ({{ number_format($location->montant_avance, 0, ',', ' ') }} FCFA)
                                                    </div>
                                                </div>
                                            @endif
                                            @if ($location->montant_frais_agence)
                                                <div class="row mb-2">
                                                    <div class="col-6"><strong>Frais d'agence:</strong></div>
                                                    <div class="col-6 text-end">
                                                        {{ number_format($location->montant_frais_agence, 0, ',', ' ') }}
                                                        FCFA</div>
                                                </div>
                                            @endif
                                            @if ($location->commission_agence)
                                                <div class="row mb-2">
                                                    <div class="col-6"><strong>Commission agence (suggestion):</strong>
                                                    </div>
                                                    <div class="col-6 text-end">
                                                        {{ number_format($location->commission_agence, 0, ',', ' ') }}
                                                        @if ($location->type_commission == 'pourcentage')
                                                            % du loyer mensuel
                                                        @else
                                                            FCFA par mois
                                                        @endif
                                                        <button class="btn btn-sm btn-link p-0 ms-2"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#modifierCommissionModal"
                                                            title="Modifier la commission">
                                                            <i class="ri-edit-line"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endif
                                            <hr>
                                            <div class="row">
                                                <div class="col-6"><strong>Premier paiement requis:</strong></div>
                                                <div class="col-6 text-end">
                                                    <strong>{{ number_format($location->montant_premier_paiement, 0, ',', ' ') }}
                                                        FCFA</strong>
                                                </div>
                                            </div>
                                            <div class="row mt-2">
                                                <div class="col-6">Période:</div>
                                                <div class="col-6 text-end">
                                                    {{ \Carbon\Carbon::parse($location->date_debut)->format('d/m/Y') }}
                                                    @if ($location->date_fin)
                                                        au
                                                        {{ \Carbon\Carbon::parse($location->date_fin)->format('d/m/Y') }}
                                                    @else
                                                        (Indéterminée)
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="row mt-2">
                                                <div class="col-6">Jour de paiement:</div>
                                                <div class="col-6 text-end">Chaque {{ $location->jour_paiement }} du mois
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Bouton pour modifier la configuration -->
                                        <button class="btn btn-sm btn-outline-primary mt-3" data-bs-toggle="modal"
                                            data-bs-target="#configurerPaiementModal">
                                            <i class="ri-settings-line me-1"></i>Modifier la configuration
                                        </button>

                                        <!-- Section des paiements initiaux -->
                                        <div class="mt-3">
                                            <h6 class="mb-2"><i class="ri-wallet-line me-1"></i>Premier paiement</h6>
                                            @php
                                                $paiementsInitiaux = $location->paiements->whereIn('type_paiement', [
                                                    'caution',
                                                    'avance',
                                                    'frais_agence',
                                                ]);
                                                $paiementCaution = $paiementsInitiaux
                                                    ->where('type_paiement', 'caution')
                                                    ->first();
                                                $paiementAvance = $paiementsInitiaux
                                                    ->where('type_paiement', 'avance')
                                                    ->first();
                                                $paiementFrais = $paiementsInitiaux
                                                    ->where('type_paiement', 'frais_agence')
                                                    ->first();
                                                $totalPaye = $paiementsInitiaux->sum('montant');
                                                $montantPremierPaiement = $location->montant_premier_paiement;
                                                $resteAPayer = $montantPremierPaiement - $totalPaye;
                                            @endphp

                                            @if ($paiementsInitiaux->count() > 0)
                                                <div class="table-responsive mb-3">
                                                    <table class="table table-sm table-bordered">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>Type</th>
                                                                <th>Montant attendu</th>
                                                                <th>Montant payé</th>
                                                                <th>Date</th>
                                                                <th>Méthode</th>
                                                                <th>Statut</th>
                                                                <th width="100">Actions</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr class="{{ $paiementCaution ? 'table-success' : '' }}">
                                                                <td><strong>Caution</strong></td>
                                                                <td>{{ number_format($location->caution ?? 0, 0, ',', ' ') }}
                                                                    FCFA</td>
                                                                <td><strong>{{ $paiementCaution ? number_format($paiementCaution->montant, 0, ',', ' ') . ' FCFA' : '-' }}</strong>
                                                                </td>
                                                                <td>{{ $paiementCaution ? \Carbon\Carbon::parse($paiementCaution->date_paiement)->format('d/m/Y') : '-' }}
                                                                </td>
                                                                <td>{{ $paiementCaution ? ucfirst($paiementCaution->methode_paiement) : '-' }}
                                                                </td>
                                                                <td>
                                                                    @if ($paiementCaution)
                                                                        <span class="badge bg-success">Payé</span>
                                                                    @else
                                                                        <span class="badge bg-warning">En attente</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if ($paiementCaution)
                                                                        <a href="{{ route('backend.locations.recu-paiement', $paiementCaution) }}"
                                                                            class="btn btn-sm btn-outline-primary"
                                                                            title="Télécharger le reçu">
                                                                            <i class="ri-file-download-line"></i>
                                                                        </a>
                                                                    @else
                                                                        -
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            @if ($location->avance_sur_loyer > 0)
                                                                <tr class="{{ $paiementAvance ? 'table-success' : '' }}">
                                                                    <td><strong>Avance sur loyer</strong>
                                                                        ({{ $location->avance_sur_loyer }} mois)</td>
                                                                    <td>{{ number_format($location->montant_avance, 0, ',', ' ') }}
                                                                        FCFA</td>
                                                                    <td><strong>{{ $paiementAvance ? number_format($paiementAvance->montant, 0, ',', ' ') . ' FCFA' : '-' }}</strong>
                                                                    </td>
                                                                    <td>{{ $paiementAvance ? \Carbon\Carbon::parse($paiementAvance->date_paiement)->format('d/m/Y') : '-' }}
                                                                    </td>
                                                                    <td>{{ $paiementAvance ? ucfirst($paiementAvance->methode_paiement) : '-' }}
                                                                    </td>
                                                                    <td>
                                                                        @if ($paiementAvance)
                                                                            <span class="badge bg-success">Payé</span>
                                                                        @else
                                                                            <span class="badge bg-warning">En
                                                                                attente</span>
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        @if ($paiementAvance)
                                                                            <a href="{{ route('backend.locations.recu-paiement', $paiementAvance) }}"
                                                                                class="btn btn-sm btn-outline-primary"
                                                                                title="Télécharger le reçu">
                                                                                <i class="ri-file-download-line"></i>
                                                                            </a>
                                                                        @else
                                                                            -
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endif
                                                            @if ($location->montant_frais_agence > 0)
                                                                <tr class="{{ $paiementFrais ? 'table-success' : '' }}">
                                                                    <td><strong>Frais d'agence</strong></td>
                                                                    <td>{{ number_format($location->montant_frais_agence, 0, ',', ' ') }}
                                                                        FCFA</td>
                                                                    <td><strong>{{ $paiementFrais ? number_format($paiementFrais->montant, 0, ',', ' ') . ' FCFA' : '-' }}</strong>
                                                                    </td>
                                                                    <td>{{ $paiementFrais ? \Carbon\Carbon::parse($paiementFrais->date_paiement)->format('d/m/Y') : '-' }}
                                                                    </td>
                                                                    <td>{{ $paiementFrais ? ucfirst($paiementFrais->methode_paiement) : '-' }}
                                                                    </td>
                                                                    <td>
                                                                        @if ($paiementFrais)
                                                                            <span class="badge bg-success">Payé</span>
                                                                        @else
                                                                            <span class="badge bg-warning">En
                                                                                attente</span>
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        @if ($paiementFrais)
                                                                            <a href="{{ route('backend.locations.recu-paiement', $paiementFrais) }}"
                                                                                class="btn btn-sm btn-outline-primary"
                                                                                title="Télécharger le reçu">
                                                                                <i class="ri-file-download-line"></i>
                                                                            </a>
                                                                        @else
                                                                            -
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endif
                                                        </tbody>
                                                        <tfoot class="table-light">
                                                            <tr>
                                                                <th colspan="2">Total payé</th>
                                                                <th colspan="5">
                                                                    <strong
                                                                        class="text-success">{{ number_format($totalPaye, 0, ',', ' ') }}
                                                                        FCFA</strong>
                                                                    /
                                                                    {{ number_format($montantPremierPaiement, 0, ',', ' ') }}
                                                                    FCFA
                                                                </th>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            @else
                                                <div class="alert alert-info">
                                                    <i class="ri-information-line me-1"></i>
                                                    Aucun paiement enregistré pour l'instant.
                                                </div>
                                            @endif

                                            <!-- Boutons d'action -->
                                            <div class="d-flex gap-2">
                                                @if (!$location->premier_paiement_valide)
                                                    @php
                                                        $paiementComplet = $location->premierPaiementComplet();
                                                    @endphp

                                                    @if (!$paiementComplet)
                                                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                            data-bs-target="#enregistrerPremierPaiementModal">
                                                            <i class="ri-add-line me-1"></i>Enregistrer un paiement
                                                        </button>
                                                    @endif

                                                    @if ($paiementComplet)
                                                        <button class="btn btn-sm btn-success" data-bs-toggle="modal"
                                                            data-bs-target="#validerPremierPaiementModal">
                                                            <i class="ri-check-double-line me-1"></i>Valider et activer la
                                                            location
                                                        </button>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <!-- Configuration non complète -->
                                        <div class="alert alert-warning mt-3">
                                            <i class="ri-alert-line me-1"></i>
                                            <strong>Configuration requise :</strong> Vous devez d'abord configurer les
                                            paramètres de paiement (loyer mensuel, caution, avance sur loyer , jour de
                                            paiement) avant de
                                            pouvoir enregistrer des paiements.
                                        </div>
                                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#configurerPaiementModal">
                                            <i class="ri-settings-line me-1"></i>Configurer le paiement
                                        </button>
                                    @endif
                                @elseif($location->statut == 'actif')
                                    <!-- Location active - afficher les infos -->
                                    <div class="info-card mt-2">
                                        <div class="row mb-2">
                                            <div class="col-6"><strong>Loyer mensuel:</strong></div>
                                            <div class="col-6 text-end">
                                                {{ number_format($location->loyer_mensuel, 0, ',', ' ') }} FCFA</div>
                                        </div>
                                        @if ($location->caution)
                                            <div class="row mb-2">
                                                <div class="col-6"><strong>Caution ({{ $location->nombre_cautions }}
                                                        mois):</strong></div>
                                                <div class="col-6 text-end">
                                                    {{ number_format($location->caution, 0, ',', ' ') }} FCFA</div>
                                            </div>
                                        @endif
                                        @if ($location->commission_agence)
                                            <div class="row mb-2">
                                                <div class="col-6"><strong>Commission agence:</strong></div>
                                                <div class="col-6 text-end">
                                                    {{ number_format($location->commission_agence, 0, ',', ' ') }}
                                                    @if ($location->type_commission == 'pourcentage')
                                                        %
                                                    @else
                                                        FCFA
                                                    @endif
                                                    <button class="btn btn-sm btn-link p-0 ms-2" data-bs-toggle="modal"
                                                        data-bs-target="#modifierCommissionModal"
                                                        title="Modifier la commission">
                                                        <i class="ri-edit-line"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                        <div class="row mt-2">
                                            <div class="col-6">Jour de paiement:</div>
                                            <div class="col-6 text-end">Chaque {{ $location->jour_paiement }} du mois
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Bouton pour modifier la configuration même en mode actif -->
                                    <button class="btn btn-sm btn-outline-primary mt-2" data-bs-toggle="modal"
                                        data-bs-target="#configurerPaiementModal">
                                        <i class="ri-settings-line me-1"></i>Modifier la configuration
                                    </button>
                                @endif
                            </div>
                            @if ($location->statut == 'actif')
                                <i class="ri-checkbox-circle-fill text-success" style="font-size: 24px;"></i>
                            @endif
                        </div>
                    </div>

                    <!-- Étape 5: Location active -->
                    @if ($location->statut == 'actif')
                        <div class="workflow-step completed">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="w-100">
                                    <h6 class="mb-1"><i class="ri-trophy-line me-1"></i>Location active</h6>
                                    <p class="mb-0">Le bien a été marqué comme loué. Les échéances ont été générées.</p>

                                    @if ($location->doitGenererNouvellesEcheances())
                                        <div class="alert alert-warning mt-2 mb-2">
                                            <i class="ri-alert-line me-1"></i>
                                            <strong>Attention!</strong> Il reste moins de 3 mois d'échéances à venir.
                                            Pensez à générer de nouvelles échéances.
                                        </div>
                                    @endif

                                    @php
                                        $echeances = $location->echeances()->orderBy('date_echeance')->get();
                                        $nombreMoisPayes = $echeances->where('statut', 'paye')->count();
                                        $totalPaye = $echeances->sum('montant_paye');
                                        
                                        // Calcul correct des impayés et retards basé sur les dates et montants
                                        $echeancesImpayees = $echeances->filter(function($e) {
                                            return $e->date_echeance->isPast() && 
                                                   $e->montant_paye < $e->montant_du && 
                                                   $e->date_echeance->diffInDays(now()) > 30;
                                        })->count();
                                        
                                        $echeancesEnRetard = $echeances->filter(function($e) {
                                            $joursRetard = $e->date_echeance->isPast() ? $e->date_echeance->diffInDays(now()) : 0;
                                            return $e->date_echeance->isPast() && 
                                                   $e->montant_paye < $e->montant_du && 
                                                   $joursRetard > 0 && $joursRetard <= 30;
                                        })->count();
                                        
                                        $montantImpaye = $echeances->filter(function($e) {
                                            return $e->date_echeance->isPast() && $e->montant_paye < $e->montant_du;
                                        })->sum(function ($e) {
                                            return $e->montant_du - $e->montant_paye;
                                        });
                                        $prochaineEcheance = $echeances
                                            ->where('statut', '!=', 'paye')
                                            ->where('date_echeance', '>=', now())
                                            ->sortBy('date_echeance')
                                            ->first();
                                    @endphp

                                    <!-- Statistiques -->
                                    <div class="row mt-3 mb-3">
                                        <div class="col-md-3">
                                            <div class="card border-info mb-0">
                                                <div class="card-body text-center py-2">
                                                    <small class="text-muted d-block"><i
                                                            class="ri-calendar-check-line"></i> Mois payés</small>
                                                    <h5 class="text-info mb-0">{{ $nombreMoisPayes }}<small
                                                            class="text-muted">/{{ $echeances->count() }}</small></h5>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card border-success mb-0">
                                                <div class="card-body text-center py-2">
                                                    <small class="text-muted d-block"><i
                                                            class="ri-money-dollar-circle-line"></i> Total payé</small>
                                                    <h5 class="text-success mb-0">
                                                        {{ number_format($totalPaye, 0, ',', ' ') }}</h5>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card border-warning mb-0">
                                                <div class="card-body text-center py-2">
                                                    <small class="text-muted d-block"><i
                                                            class="ri-alarm-warning-line"></i> En retard</small>
                                                    <h5 class="text-warning mb-0">{{ $echeancesEnRetard }}</h5>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card border-danger mb-0">
                                                <div class="card-body text-center py-2">
                                                    <small class="text-muted d-block"><i class="ri-alert-line"></i>
                                                        Impayés</small>
                                                    <h5 class="text-danger mb-0">{{ $echeancesImpayees }}</h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Prochaine échéance -->
                                    @if ($prochaineEcheance)
                                        <div class="alert alert-info mb-3">
                                            <div class="d-flex align-items-center">
                                                <i class="ri-calendar-event-line me-2" style="font-size: 20px;"></i>
                                                <div>
                                                    <strong>Prochaine échéance :</strong>
                                                    {{ \Carbon\Carbon::parse($prochaineEcheance->date_echeance)->format('d/m/Y') }}
                                                    <span class="ms-2">
                                                        <strong>Montant :</strong>
                                                        {{ number_format($prochaineEcheance->montant_du, 0, ',', ' ') }}
                                                        FCFA
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Boutons d'action -->
                                    <div class="row g-2 mt-2">
                                        <div class="col-md-4">
                                            <button class="btn btn-primary w-100" data-bs-toggle="modal"
                                                data-bs-target="#echeancesModal">
                                                <i class="ri-calendar-line me-1"></i>Voir échéances & paiements
                                            </button>
                                        </div>
                                        <div class="col-md-4">
                                            <button class="btn btn-success w-100" data-bs-toggle="modal"
                                                data-bs-target="#genererEcheancesModal">
                                                <i class="ri-add-line me-1"></i>Générer nouvelles échéances
                                            </button>
                                        </div>
                                        <div class="col-md-4">
                                            <button class="btn btn-warning w-100" data-bs-toggle="modal"
                                                data-bs-target="#resilierModal">
                                                <i class="ri-close-line me-1"></i>Résilier la location
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <i class="ri-checkbox-circle-fill text-success ms-3" style="font-size: 24px;"></i>
                            </div>
                        </div>
                    @endif

                    <!-- Location résiliée -->
                    @if ($location->statut == 'resilie')
                        <div class="alert alert-warning mt-3">
                            <h6><i class="ri-alert-line me-1"></i>Location résiliée</h6>
                            @if ($location->note_admin)
                                <p class="mb-0">{{ $location->note_admin }}</p>
                            @endif
                        </div>
                    @endif

                    <!-- Location terminée -->
                    @if ($location->statut == 'termine')
                        <div class="alert alert-success mt-3">
                            <h6><i class="ri-check-line me-1"></i>Location terminée</h6>
                            <p class="mb-0">La location s'est terminée normalement.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Note admin -->
            @if ($location->note_admin)
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="ri-sticky-note-line me-2"></i>Notes administrateur</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{{ $location->note_admin }}</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Colonne droite: Informations -->
        <div class="col-lg-4">
            <!-- Informations du bien -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="ri-home-4-line me-2"></i>Bien immobilier</h5>
                </div>
                <div class="card-body">
                    @if ($location->annonce->getFirstMediaUrl('images'))
                        <img src="{{ $location->annonce->getFirstMediaUrl('images') }}" class="img-fluid rounded mb-3"
                            alt="Bien">
                    @endif
                    <h6>{{ $location->annonce->titre }}</h6>
                    <p class="text-muted mb-2">
                        <i class="ri-map-pin-line me-1"></i>
                        {{ $location->annonce->ville }}, {{ $location->annonce->quartier }}
                    </p>
                    <div class="info-card">
                        <p class="mb-1"><strong>Type:</strong> {{ $location->annonce->typeBien->nom ?? 'N/A' }}</p>
                        <p class="mb-1"><strong>Surface:</strong> {{ $location->annonce->surface }} m²</p>
                        <p class="mb-0"><strong>Prix:</strong>
                            {{ number_format($location->annonce->prix, 0, ',', ' ') }} FCFA/mois</p>
                    </div>
                    <a href="{{ route('backend.annonces.show', $location->annonce) }}"
                        class="btn btn-sm btn-outline-primary mt-2">
                        <i class="ri-eye-line me-1"></i>Voir l'annonce
                    </a>
                </div>
            </div>

            <!-- Informations du client -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="ri-user-line me-2"></i>Locataire</h5>
                </div>
                <div class="card-body">
                    <div class="info-card">
                        <p class="mb-2"><strong>Nom:</strong> {{ $location->locataire->username }}</p>
                        <p class="mb-2">
                            <strong>Email:</strong><br>
                            <a href="mailto:{{ $location->locataire->email }}">{{ $location->locataire->email }}</a>
                        </p>
                        <p class="mb-0">
                            <strong>Téléphone:</strong><br>
                            <a
                                href="tel:{{ $location->locataire->phone }}">{{ $location->locataire->phone ?? 'N/A' }}</a>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="ri-tools-line me-2"></i>Actions</h5>
                </div>
                <div class="card-body">
                    @if ($location->paiements->count() == 0)
                        <a href="{{ route('backend.locations.edit', $location) }}"
                            class="btn btn-primary btn-sm w-100 mb-2">
                            <i class="ri-edit-line me-1"></i>Modifier
                        </a>
                    @else
                        <button class="btn btn-secondary btn-sm w-100 mb-2" disabled
                            title="Modification impossible : des paiements ont été enregistrés">
                            <i class="ri-lock-line me-1"></i>Modification verrouillée
                        </button>
                    @endif
                    <form action="{{ route('backend.locations.destroy', $location) }}" method="POST"
                        onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette location ?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm w-100">
                            <i class="ri-delete-bin-line me-1"></i>Supprimer
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- MODALS -->

    <!-- Modal: Envoyer la fiche -->
    <div class="modal fade" id="envoyerFicheModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('backend.locations.envoyer-fiche', $location) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Envoyer la fiche au client</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Documents à joindre (optionnel)</label>
                            <input type="file" name="documents[]" class="form-control" multiple
                                accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                            <small class="text-muted">
                                Formats acceptés : PDF, Word, Images. Vous pouvez sélectionner plusieurs fichiers.
                            </small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Message au client (optionnel)</label>
                            <textarea name="message_email" class="form-control" rows="3"
                                placeholder="Message qui sera inclus dans l'email au client"></textarea>
                        </div>
                        {{-- <div class="mb-3">
                            <label class="form-label">Note interne (optionnelle)</label>
                            <textarea name="note_admin" class="form-control" rows="2"
                                placeholder="Note interne concernant l'envoi de la fiche"></textarea>
                        </div> --}}
                        <div class="alert alert-info">
                            <i class="ri-information-line me-1"></i>
                            Un email sera envoyé au client avec la fiche à remplir et les documents joints.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-send-plane-line me-1"></i>Envoyer
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Confirmer retour prospect - Intéressé -->
    <div class="modal fade" id="confirmerRetourProspectModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('backend.locations.confirmer-retour-prospect', $location) }}" method="POST">
                @csrf
                <input type="hidden" name="client_interesse" value="1">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">Confirmer l'intérêt du client</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-success">
                            <i class="ri-checkbox-circle-line me-1"></i>
                            <strong>Le client a confirmé son intérêt pour le bien</strong>
                        </div>
                        <p>En confirmant, vous indiquez que le client est intéressé par le bien après réception de la fiche.
                        </p>
                        <p>Vous pourrez ensuite planifier une visite du bien.</p>
                        <div class="mb-3">
                            <label class="form-label">Note (optionnelle)</label>
                            <textarea name="note_admin" class="form-control" rows="3"
                                placeholder="Remarques concernant le retour du client"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-success">
                            <i class="ri-checkbox-circle-line me-1"></i>Confirmer l'intérêt
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Refuser retour prospect - Non intéressé -->
    <div class="modal fade" id="refuserRetourProspectModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('backend.locations.confirmer-retour-prospect', $location) }}" method="POST">
                @csrf
                <input type="hidden" name="client_interesse" value="0">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Client non intéressé</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="ri-alert-line me-1"></i>
                            <strong>Le client n'est pas intéressé par le bien</strong>
                        </div>
                        <p>En confirmant, la location sera automatiquement résiliée.</p>
                        <div class="mb-3">
                            <label class="form-label">Raison (optionnelle)</label>
                            <textarea name="note_admin" class="form-control" rows="3" placeholder="Motif du refus ou remarques"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="ri-close-line me-1"></i>Confirmer l'annulation
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Planifier la visite -->
    <div class="modal fade" id="planifierVisiteModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('backend.locations.planifier-visite', $location) }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Planifier la visite</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Date et heure de la visite <span
                                    class="text-danger">*</span></label>
                            <input type="datetime-local" name="date_visite" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Note (optionnelle)</label>
                            <textarea name="note_admin" class="form-control" rows="3" placeholder="Instructions pour la visite"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-calendar-event-line me-1"></i>Planifier
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Visite effectuée -->
    <div class="modal fade" id="visiteEffectueeModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('backend.locations.visite-effectuee', $location) }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Marquer la visite comme effectuée</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Le client est-il intéressé ? <span
                                    class="text-danger">*</span></label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="client_interesse"
                                        id="interesse_oui" value="1" required>
                                    <label class="form-check-label" for="interesse_oui">Oui</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="client_interesse"
                                        id="interesse_non" value="0" required>
                                    <label class="form-check-label" for="interesse_non">Non</label>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Compte rendu de la visite </label>
                            <textarea name="compte_rendu_visite" class="form-control" rows="4"
                                placeholder="Décrivez comment s'est passée la visite..."></textarea>
                        </div>
                        {{-- <div class="mb-3">
                            <label class="form-label">Note additionnelle (optionnelle)</label>
                            <textarea name="note_admin" class="form-control" rows="2"></textarea>
                        </div> --}}
                        <div class="alert alert-warning">
                            <i class="ri-alert-line me-1"></i>
                            Si le client n'est pas intéressé, la location sera automatiquement résiliée.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-checkbox-line me-1"></i>Valider
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Enregistrer premier paiement -->
    <div class="modal fade" id="enregistrerPremierPaiementModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form action="{{ route('backend.locations.enregistrer-premier-paiement', $location) }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title"><i class="ri-wallet-line me-1"></i>Enregistrer le premier paiement</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info mb-3">
                            <h6 class="alert-heading mb-2">
                                <i class="ri-information-line me-1"></i>Informations importantes
                            </h6>
                            <p class="mb-0">Les montants de la caution, avance et frais sont préremplis selon la
                                configuration. Vous pouvez les ajuster si le client paie partiellement.</p>
                        </div>

                        <div class="row">
                            <div class="mb-3 col-md-4">
                                <label class="form-label">Caution <span class="text-danger">*</span></label>
                                <input type="number" name="montant_caution" class="form-control"
                                    value="{{ $location->caution ?? 0 }}"
                                    placeholder="{{ number_format($location->caution ?? 0, 0, ',', ' ') }}" required
                                    step="1" min="0">
                                <small class="text-muted">Requis:
                                    {{ number_format($location->caution ?? 0, 0, ',', ' ') }} FCFA</small>
                            </div>

                            <div class="mb-3 col-md-4">
                                <label class="form-label">Avance sur loyer</label>
                                <input type="number" name="montant_avance" class="form-control"
                                    value="{{ $location->montant_avance ?? 0 }}"
                                    placeholder="{{ number_format($location->montant_avance ?? 0, 0, ',', ' ') }}"
                                    step="1" min="0">
                                <small class="text-muted">Requis:
                                    {{ number_format($location->montant_avance ?? 0, 0, ',', ' ') }} FCFA</small>
                            </div>

                            <div class="mb-3 col-md-4">
                                <label class="form-label">Frais d'agence</label>
                                <input type="number" name="montant_frais" class="form-control"
                                    value="{{ $location->montant_frais_agence ?? 0 }}"
                                    placeholder="{{ number_format($location->montant_frais_agence ?? 0, 0, ',', ' ') }}"
                                    step="1" min="0">
                                <small class="text-muted">Requis:
                                    {{ number_format($location->montant_frais_agence ?? 0, 0, ',', ' ') }} FCFA</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label class="form-label">Date du paiement <span class="text-danger">*</span></label>
                                <input type="date" name="date_paiement" class="form-control"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>

                            <div class="mb-3 col-md-6">
                                <label class="form-label">Méthode de paiement <span class="text-danger">*</span></label>
                                <select name="methode_paiement" class="form-select" required>
                                    <option value="">Sélectionner une méthode</option>
                                    <option value="espèces">Espèces</option>
                                    <option value="virement">Virement bancaire</option>
                                    <option value="chèque">Chèque</option>
                                    <option value="carte_bancaire">Carte bancaire</option>
                                    <option value="mobile_money">Mobile Money</option>
                                    <option value="autre">Autre</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Référence du paiement</label>
                            <input type="text" name="reference" class="form-control"
                                placeholder="Ex: N° de transaction, chèque, etc.">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Notes supplémentaires sur ce paiement"></textarea>
                        </div>

                        <div class="alert alert-warning">
                            <i class="ri-information-line me-1"></i>
                            <strong>Note :</strong> La commission de l'agence ne sera calculée que sur les paiements
                            mensuels de loyer, pas sur les paiements initiaux.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-save-line me-1"></i>Enregistrer le paiement
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Configurer le paiement -->
    <div class="modal fade" id="configurerPaiementModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form action="{{ route('backend.locations.configurer-paiement', $location) }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Configurer le paiement</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Loyer mensuel <span class="text-danger">*</span></label>
                                <input type="number" name="loyer_mensuel" id="loyer_mensuel" class="form-control"
                                    value="{{ $location->loyer_mensuel ?? $location->annonce->prix }}" required
                                    step="1" min="0">

                                <small class="text-muted">Prix de l'annonce :
                                    {{ number_format($location->annonce->prix, 0, ',', ' ') }} FCFA (négociable)</small>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Nombre de cautions <span class="text-danger">*</span></label>
                                <input type="number" name="nombre_cautions" id="nombre_cautions" class="form-control"
                                    value="{{ $location->nombre_cautions ?? 2 }}" required min="0"
                                    max="12">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Montant caution</label>
                                <input type="number" name="caution" id="caution" class="form-control"
                                    value="{{ $location->caution }}" readonly style="background-color: #e9ecef;">
                                <small class="text-muted">Calculé automatiquement</small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Avance sur loyer (en mois)</label>
                                <input type="number" name="avance_sur_loyer" id="avance_sur_loyer" class="form-control"
                                    value="{{ $location->avance_sur_loyer ?? 0 }}" min="0" max="12">
                                <small class="text-muted">0 à 12 mois. L'avance sera déduite des premières
                                    échéances</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Montant de l'avance</label>
                                <input type="number" name="montant_avance" id="montant_avance" class="form-control"
                                    value="{{ $location->montant_avance }}" readonly style="background-color: #e9ecef;">
                                <small class="text-muted">Calculé automatiquement</small>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Frais d'agence (optionnels)</label>
                            <input type="number" name="montant_frais_agence" class="form-control"
                                value="{{ $location->montant_frais_agence }}" step="1" min="0">
                        </div>

                        <hr>
                        <h6 class="mb-3"><i class="ri-percent-line me-1"></i>Commission de l'agence sur les loyers</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Type de commission</label>
                                <select name="type_commission" id="type_commission_config" class="form-select">
                                    <option value="pourcentage"
                                        {{ $location->type_commission == 'pourcentage' ? 'selected' : '' }}>Pourcentage
                                        (%)</option>
                                    <option value="montant"
                                        {{ $location->type_commission == 'montant' ? 'selected' : '' }}>Montant fixe
                                        (FCFA)</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Commission agence</label>
                                <input type="number" name="commission_agence" class="form-control"
                                    value="{{ $location->commission_agence }}" step="1" min="0">
                                <small class="text-muted">Appliquée sur chaque paiement de loyer (pas sur la
                                    caution)</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Date de début <span class="text-danger">*</span></label>
                                <input type="date" name="date_debut" class="form-control"
                                    value="{{ $location->date_debut ? $location->date_debut->format('Y-m-d') : '' }}"
                                    required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Date de fin (optionnelle)</label>
                                <input type="date" name="date_fin" class="form-control"
                                    value="{{ $location->date_fin ? $location->date_fin->format('Y-m-d') : '' }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Jour de paiement (1-31 du mois) <span
                                        class="text-danger">*</span></label>
                                <input type="number" name="jour_paiement" class="form-control"
                                    value="{{ $location->jour_paiement }}" required min="1" max="31">
                            </div>
                        </div>
                        {{-- <div class="mb-3">
                            <label class="form-label">Conditions de paiement (optionnelles)</label>
                            <textarea name="conditions" class="form-control" rows="3" placeholder="Ex: Paiement mensuel...">{{ $location->conditions }}</textarea>
                        </div> --}}
                        {{-- <div class="mb-3">
                            <label class="form-label">Note interne (optionnelle)</label>
                            <textarea name="note_admin" class="form-control" rows="2"></textarea>
                        </div> --}}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-save-line me-1"></i>Enregistrer
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Valider le premier paiement -->
    <div class="modal fade" id="validerPremierPaiementModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('backend.locations.valider-premier-paiement', $location) }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">Valider le paiement et activer la location</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-success">
                            <h6 class="alert-heading"><i class="ri-check-double-line me-1"></i>Confirmation de paiement
                            </h6>
                            <p class="mb-0">Le paiement a bien été reçu et la location sera activée.</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Note finale (optionnelle)</label>
                            <textarea name="note_admin" class="form-control" rows="3" placeholder="Remarques finales sur la transaction"></textarea>
                        </div>
                        <div class="alert alert-info">
                            <i class="ri-information-line me-1"></i>
                            Le bien sera automatiquement marqué comme <strong>loué</strong>, retiré de la publication,
                            et les échéances de paiement seront générées.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-success">
                            <i class="ri-check-double-line me-1"></i>Valider et activer
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Modifier la commission rapidement -->
    <div class="modal fade" id="modifierCommissionModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('backend.locations.configurer-paiement', $location) }}" method="POST">
                @csrf
                <input type="hidden" name="loyer_mensuel" value="{{ $location->loyer_mensuel }}">
                <input type="hidden" name="nombre_cautions" value="{{ $location->nombre_cautions }}">
                <input type="hidden" name="avance_sur_loyer" value="{{ $location->avance_sur_loyer }}">
                <input type="hidden" name="montant_frais_agence" value="{{ $location->montant_frais_agence }}">
                <input type="hidden" name="date_debut" value="{{ $location->date_debut?->format('Y-m-d') }}">
                <input type="hidden" name="date_fin" value="{{ $location->date_fin?->format('Y-m-d') }}">
                <input type="hidden" name="jour_paiement" value="{{ $location->jour_paiement }}">
                <input type="hidden" name="conditions" value="{{ $location->conditions }}">

                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title"><i class="ri-percent-line me-1"></i>Modifier la commission</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="ri-information-line me-1"></i>
                            La commission est appliquée sur chaque paiement de loyer mensuel (pas sur la caution ni
                            l'avance).
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Type de commission <span class="text-danger">*</span></label>
                            <select name="type_commission" id="type_commission_modal" class="form-select" required>
                                <option value="pourcentage"
                                    {{ $location->type_commission == 'pourcentage' ? 'selected' : '' }}>Pourcentage (%) du
                                    loyer</option>
                                <option value="montant" {{ $location->type_commission == 'montant' ? 'selected' : '' }}>
                                    Montant fixe (FCFA)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Montant de la commission <span class="text-danger">*</span></label>
                            <input type="number" name="commission_agence" id="commission_agence_modal"
                                class="form-control" value="{{ $location->commission_agence ?? 0 }}" required
                                step="1" min="0">
                            <small class="text-muted" id="commission_hint">
                                @if ($location->type_commission == 'pourcentage')
                                    Ex: 10 pour 10% du loyer mensuel
                                @else
                                    Ex: 5000 pour 5000 FCFA par mois
                                @endif
                            </small>
                        </div>

                        @if ($location->loyer_mensuel)
                            <div class="alert alert-secondary">
                                <strong>Aperçu du calcul :</strong><br>
                                Loyer mensuel : {{ number_format($location->loyer_mensuel, 0, ',', ' ') }} FCFA<br>
                                <span id="commission_preview">
                                    @if ($location->type_commission == 'pourcentage' && $location->commission_agence)
                                        Commission :
                                        {{ number_format(($location->loyer_mensuel * $location->commission_agence) / 100, 0, ',', ' ') }}
                                        FCFA
                                        ({{ $location->commission_agence }}%)
                                    @elseif($location->commission_agence)
                                        Commission : {{ number_format($location->commission_agence, 0, ',', ' ') }} FCFA
                                    @endif
                                </span>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-save-line me-1"></i>Enregistrer
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Résilier la location -->
    <!-- Modal Générer nouvelles échéances -->
    <div class="modal fade" id="genererEcheancesModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('backend.locations.generer-nouvelles-echeances', $location) }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title"><i class="ri-add-line me-1"></i>Générer nouvelles échéances</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="ri-information-line me-1"></i>
                            Cette action va générer de nouvelles échéances mensuelles à partir de la dernière échéance
                            existante.
                        </div>

                        @php
                            $derniereEcheance = $location->echeances()->orderBy('date_echeance', 'desc')->first();
                            $echeancesFutures = $location->echeances()->where('date_echeance', '>', now())->count();
                        @endphp

                        @if ($derniereEcheance)
                            <div class="mb-3 bg-light p-3 rounded">
                                <p class="mb-2"><strong>Dernière échéance :</strong>
                                    {{ $derniereEcheance->date_echeance->format('d/m/Y') }}</p>
                                <p class="mb-2">
                                    <strong>Échéances déjà programmées :</strong>
                                    <span
                                        class="badge {{ $echeancesFutures < 3 ? 'bg-danger' : ($echeancesFutures < 6 ? 'bg-warning text-dark' : 'bg-success') }}">
                                        {{ $echeancesFutures }} mois à venir
                                    </span>
                                </p>
                                <p class="mb-0"><strong>Montant mensuel :</strong>
                                    {{ number_format($location->loyer_mensuel, 0, ',', ' ') }} FCFA</p>

                                @if ($echeancesFutures >= 6)
                                    <div class="alert alert-success mt-2 mb-0">
                                        <i class="ri-information-line me-1"></i>
                                        Vous avez déjà {{ $echeancesFutures }} mois d'échéances programmées. Vous pouvez
                                        quand même en générer plus si nécessaire.
                                    </div>
                                @endif
                            </div>
                        @endif

                        <div class="mb-3">
                            <label class="form-label">Nombre de mois à générer <span class="text-danger">*</span></label>
                            <select name="nombre_mois" class="form-select" required>
                                <option value="">Choisir...</option>
                                <option value="3">3 mois</option>
                                <option value="6" selected>6 mois</option>
                                <option value="12">12 mois (1 an)</option>
                                <option value="24">24 mois (2 ans)</option>
                            </select>
                            <small class="text-muted">Recommandé : 6 ou 12 mois</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-success">
                            <i class="ri-add-line me-1"></i>Générer les échéances
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Résilier location -->
    <div class="modal fade" id="resilierModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('backend.locations.resilier', $location) }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title">Résilier la location</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="ri-alert-line me-1"></i>
                            Êtes-vous sûr de vouloir résilier cette location ?
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Raison de la résiliation <span class="text-danger">*</span></label>
                            <textarea name="note_admin" class="form-control" rows="4" required
                                placeholder="Expliquez pourquoi la location est résiliée"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-warning">
                            <i class="ri-close-line me-1"></i>Confirmer la résiliation
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Section Échéances: affichée uniquement quand la location est active -->
    @if ($location->statut == 'actif')
        <div class="modal fade" id="echeancesModal" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title text-white"><i class="ri-calendar-line me-1"></i>Échéances et Historique
                            des Paiements</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        @php
                            $echeances = $location->echeances()->orderBy('date_echeance')->get();
                            $totalDu = $echeances->sum('montant_du');
                            $totalPaye = $echeances->sum('montant_paye');

                            // Statistiques demandées
                            $nombreMoisPayes = $echeances->where('statut', 'paye')->count();
                            
                            // Calcul correct des impayés et retards basé sur les dates et montants
                            $echeancesImpayees = $echeances->filter(function($e) {
                                return $e->date_echeance->isPast() && 
                                       $e->montant_paye < $e->montant_du && 
                                       $e->date_echeance->diffInDays(now()) > 30;
                            })->count();
                            
                            $echeancesEnRetard = $echeances->filter(function($e) {
                                $joursRetard = $e->date_echeance->isPast() ? $e->date_echeance->diffInDays(now()) : 0;
                                return $e->date_echeance->isPast() && 
                                       $e->montant_paye < $e->montant_du && 
                                       $joursRetard > 0 && $joursRetard <= 30;
                            })->count();
                            
                            $montantImpaye = $echeances->filter(function($e) {
                                return $e->date_echeance->isPast() && $e->montant_paye < $e->montant_du;
                            })->sum(function ($e) {
                                return $e->montant_du - $e->montant_paye;
                            });

                            // Prochaine échéance
                            $prochaineEcheance = $echeances
                                ->where('statut', '!=', 'paye')
                                ->where('date_echeance', '>=', now())
                                ->sortBy('date_echeance')
                                ->first();
                        @endphp

                        @if ($echeances->count() > 0)
                            <!-- Résumé global amélioré -->
                            <div class="row mb-4">
                                <div class="col-md-3">
                                    <div class="card border-info">
                                        <div class="card-body text-center">
                                            <h6 class="text-muted mb-2"><i class="ri-calendar-check-line"></i> Mois payés
                                            </h6>
                                            <h4 class="text-info mb-0">{{ $nombreMoisPayes }}</h4>
                                            <small class="text-muted">sur {{ $echeances->count() }} échéances</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border-success">
                                        <div class="card-body text-center">
                                            <h6 class="text-muted mb-2"><i class="ri-money-dollar-circle-line"></i> Total
                                                payé</h6>
                                            <h4 class="text-success mb-0">{{ number_format($totalPaye, 0, ',', ' ') }}
                                            </h4>
                                            <small class="text-muted">FCFA</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border-warning">
                                        <div class="card-body text-center">
                                            <h6 class="text-muted mb-2"><i class="ri-alarm-warning-line"></i> En retard
                                            </h6>
                                            <h4 class="text-warning mb-0">{{ $echeancesEnRetard }}</h4>
                                            <small class="text-muted">échéance(s)</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border-danger">
                                        <div class="card-body text-center">
                                            <h6 class="text-muted mb-2"><i class="ri-alert-line"></i> Impayés</h6>
                                            <h4 class="text-danger mb-0">{{ $echeancesImpayees }}</h4>
                                            <small class="text-muted">{{ number_format($montantImpaye, 0, ',', ' ') }}
                                                FCFA</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Prochaine échéance -->
                            @if ($prochaineEcheance)
                                <div class="alert alert-info mb-4">
                                    <div class="d-flex align-items-center">
                                        <i class="ri-calendar-event-line me-3" style="font-size: 24px;"></i>
                                        <div>
                                            <strong>Prochaine échéance :</strong>
                                            {{ \Carbon\Carbon::parse($prochaineEcheance->date_echeance)->format('d/m/Y') }}
                                            <span class="ms-3">
                                                <strong>Montant :</strong>
                                                {{ number_format($prochaineEcheance->montant_du, 0, ',', ' ') }} FCFA
                                            </span>
                                            @if ($prochaineEcheance->montant_paye > 0)
                                                <span class="ms-3 text-success">
                                                    ({{ number_format($prochaineEcheance->montant_paye, 0, ',', ' ') }}
                                                    FCFA déjà payés)
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Tableau des échéances avec historique -->
                            <div class="accordion" id="accordionEcheances">
                                @foreach ($echeances as $index => $echeance)
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="heading{{ $echeance->id }}">
                                            <button class="accordion-button {{ $index > 0 ? 'collapsed' : '' }}"
                                                type="button" data-bs-toggle="collapse"
                                                data-bs-target="#collapse{{ $echeance->id }}"
                                                aria-expanded="{{ $index == 0 ? 'true' : 'false' }}">
                                                <div class="d-flex justify-content-between align-items-center w-100 me-3">
                                                    <div>
                                                        <strong>Échéance du
                                                            {{ \Carbon\Carbon::parse($echeance->date_echeance)->format('d/m/Y') }}</strong>
                                                        @php
                                                            // Calcul dynamique du statut réel
                                                            $estComplet = $echeance->montant_paye >= $echeance->montant_du;
                                                            $dateDepassee = $echeance->date_echeance->isPast();
                                                            $joursRetard = $dateDepassee ? (int) $echeance->date_echeance->diffInDays(now()) : 0;
                                                            
                                                            if ($estComplet) {
                                                                $statutAffichage = 'Payé';
                                                                $badgeClass = 'bg-success';
                                                            } elseif ($dateDepassee && $joursRetard > 30) {
                                                                $statutAffichage = 'Impayé (' . $joursRetard . 'j)';
                                                                $badgeClass = 'bg-danger';
                                                            } elseif ($dateDepassee && $joursRetard > 0) {
                                                                $statutAffichage = 'En retard (' . $joursRetard . 'j)';
                                                                $badgeClass = 'bg-danger';
                                                            } elseif ($echeance->montant_paye > 0) {
                                                                $statutAffichage = 'Partiel';
                                                                $badgeClass = 'bg-info';
                                                            } else {
                                                                $statutAffichage = 'À échéance';
                                                                $badgeClass = 'bg-secondary';
                                                            }
                                                        @endphp
                                                        <span class="badge {{ $badgeClass }} ms-2">
                                                            {{ $statutAffichage }}
                                                        </span>
                                                    </div>
                                                    <div class="text-end">
                                                        <strong>{{ number_format($echeance->montant_paye, 0, ',', ' ') }}</strong>
                                                        /
                                                        {{ number_format($echeance->montant_du, 0, ',', ' ') }} FCFA
                                                    </div>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="collapse{{ $echeance->id }}"
                                            class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}"
                                            data-bs-parent="#accordionEcheances">
                                            <div class="accordion-body">
                                                @php
                                                    $paiementsEcheance = $echeance->paiements;
                                                @endphp

                                                @if ($paiementsEcheance->count() > 0)
                                                    <h6 class="mb-3"><i class="ri-history-line me-1"></i>Historique des
                                                        paiements</h6>
                                                    <div class="table-responsive">
                                                        <table class="table table-sm table-bordered">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th>Date</th>
                                                                    <th>Montant</th>
                                                                    <th>Commission</th>
                                                                    <th>Méthode</th>
                                                                    <th>Référence</th>
                                                                    <th>Notes</th>
                                                                    <th width="120">Actions</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($paiementsEcheance as $paiement)
                                                                    <tr>
                                                                        <td>{{ \Carbon\Carbon::parse($paiement->date_paiement)->format('d/m/Y H:i') }}
                                                                        </td>
                                                                        <td><strong
                                                                                class="text-success">{{ number_format($paiement->montant, 0, ',', ' ') }}
                                                                                FCFA</strong></td>
                                                                        <td>
                                                                            @if ($paiement->commission_agence)
                                                                                <span class="badge bg-warning">
                                                                                    {{ number_format($paiement->commission_agence, 0, ',', ' ') }}
                                                                                    {{ $paiement->type_commission == 'pourcentage' ? '%' : 'FCFA' }}
                                                                                </span>
                                                                            @else
                                                                                -
                                                                            @endif
                                                                        </td>
                                                                        <td>
                                                                            <span
                                                                                class="badge bg-info">{{ ucfirst($paiement->methode_paiement) }}</span>
                                                                        </td>
                                                                        <td>{{ $paiement->reference ?? '-' }}</td>
                                                                        <td>{{ $paiement->notes ?? '-' }}</td>
                                                                        <td>
                                                                            <a href="{{ route('backend.locations.recu-paiement', $paiement) }}"
                                                                                class="btn btn-sm btn-outline-primary"
                                                                                title="Télécharger le reçu">
                                                                                <i
                                                                                    class="ri-file-download-line me-1"></i>Reçu
                                                                            </a>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @else
                                                    <div class="alert alert-info">
                                                        <i class="ri-information-line me-1"></i>Aucun paiement enregistré
                                                        pour cette échéance.
                                                    </div>
                                                @endif

                                                @if ($echeance->statut != 'paye')
                                                    <button class="btn btn-sm btn-primary mt-2" data-bs-toggle="modal"
                                                        data-bs-target="#payerEcheanceModal{{ $echeance->id }}">
                                                        <i class="ri-wallet-line me-1"></i>Enregistrer un paiement
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="ri-information-line me-1"></i>Aucune échéance générée pour cette location.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Modals: Payer les échéances -->
        @foreach ($location->echeances ?? [] as $echeance)
            <div class="modal fade" id="payerEcheanceModal{{ $echeance->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <form action="{{ route('backend.locations.enregistrer-paiement-loyer', $echeance) }}"
                        method="POST">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title">Enregistrer paiement loyer</h5>
                                <button type="button" class="btn-close btn-close-white"
                                    data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="alert alert-info mb-3">
                                    <strong>Échéance :</strong>
                                    {{ \Carbon\Carbon::parse($echeance->date_echeance)->format('d/m/Y') }}<br>
                                    <strong>Montant dû :</strong> {{ number_format($echeance->montant_du, 0, ',', ' ') }}
                                    FCFA<br>
                                    <strong>Déjà payé :</strong> {{ number_format($echeance->montant_paye, 0, ',', ' ') }}
                                    FCFA<br>
                                    <strong>Reste à payer :</strong>
                                    {{ number_format($echeance->montant_du - $echeance->montant_paye, 0, ',', ' ') }} FCFA
                                </div>

                                <div class="mb-3">
                                    <label class="form-label d-block">Type de paiement</label>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input type-paiement-radio" type="radio"
                                            name="type_paiement_{{ $echeance->id }}"
                                            id="paiement_total_{{ $echeance->id }}" value="total" checked>
                                        <label class="form-check-label" for="paiement_total_{{ $echeance->id }}">
                                            <i class="ri-money-dollar-circle-line me-1"></i>Montant total
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input type-paiement-radio" type="radio"
                                            name="type_paiement_{{ $echeance->id }}"
                                            id="paiement_partiel_{{ $echeance->id }}" value="partiel">
                                        <label class="form-check-label" for="paiement_partiel_{{ $echeance->id }}">
                                            <i class="ri-percent-line me-1"></i>Montant partiel
                                        </label>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Montant du paiement <span
                                            class="text-danger">*</span></label>
                                    <input type="number" name="montant" class="form-control montant-paiement-input"
                                        id="montant_input_{{ $echeance->id }}"
                                        data-montant-total="{{ $echeance->montant_du - $echeance->montant_paye }}"
                                        value="{{ $echeance->montant_du - $echeance->montant_paye }}" required
                                        step="1" min="1"
                                        max="{{ $echeance->montant_du - $echeance->montant_paye }}" readonly>
                                    <small class="text-muted">Maximum:
                                        {{ number_format($echeance->montant_du - $echeance->montant_paye, 0, ',', ' ') }}
                                        FCFA</small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Date du paiement <span
                                            class="text-danger">*</span></label>
                                    <input type="date" name="date_paiement" class="form-control"
                                        value="{{ date('Y-m-d') }}" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Méthode de paiement <span
                                            class="text-danger">*</span></label>
                                    <select name="methode_paiement" class="form-select" required>
                                        <option value="">Sélectionner une méthode</option>
                                        <option value="espèces">Espèces</option>
                                        <option value="virement">Virement bancaire</option>
                                        <option value="chèque">Chèque</option>
                                        <option value="carte_bancaire">Carte bancaire</option>
                                        <option value="mobile_money">Mobile Money</option>
                                        <option value="autre">Autre</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Référence du paiement</label>
                                    <input type="text" name="reference" class="form-control"
                                        placeholder="Ex: N° de transaction, chèque, etc.">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Notes</label>
                                    <textarea name="notes" class="form-control" rows="2"></textarea>
                                </div>

                                <div class="alert alert-info">
                                    <i class="ri-information-line me-1"></i>
                                    La commission de l'agence sera automatiquement calculée selon la configuration
                                    ({{ $location->commission_agence }}
                                    {{ $location->type_commission == 'pourcentage' ? '%' : 'FCFA' }})
                                    .
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary"
                                    data-bs-dismiss="modal">Annuler</button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="ri-save-line me-1"></i>Enregistrer
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach
    @endif
@endsection

@section('script')
    <script>
        // Fonction pour formater le montant avec des espaces
        function formatMontant(value) {
            let numStr = value.replace(/[^\d]/g, '');
            return numStr.replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
        }

        // Fonction pour obtenir la valeur numérique
        function getMontantNumeric(value) {
            return parseFloat(value.replace(/\s/g, '')) || 0;
        }

        // Appliquer le formatage à tous les champs de montant
        document.addEventListener('DOMContentLoaded', function() {
            // Gestion des radio buttons pour paiement total/partiel
            document.querySelectorAll('.type-paiement-radio').forEach(radio => {
                radio.addEventListener('change', function() {
                    const echeanceId = this.name.split('_').pop();
                    const montantInput = document.getElementById('montant_input_' + echeanceId);
                    const montantTotal = montantInput.dataset.montantTotal;

                    if (this.value === 'total') {
                        montantInput.value = montantTotal;
                        montantInput.setAttribute('readonly', 'readonly');
                    } else {
                        montantInput.value = '';
                        montantInput.removeAttribute('readonly');
                        montantInput.focus();
                    }
                });
            });

            // Sélectionner uniquement les champs dans le modal de configuration
            const modalConfiguration = document.getElementById('configurerPaiementModal');

            if (modalConfiguration) {
                // NE PAS appliquer de formatage - laisser les champs en type="number" natif
                // Cela évite les problèmes de conversion et de multiplication
            }

            // Calcul automatique de la caution et de l'avance
            const loyerInput = document.getElementById('loyer_mensuel');
            const nombreCautionsInput = document.getElementById('nombre_cautions');
            const cautionInput = document.getElementById('caution');
            const avanceSurLoyerInput = document.getElementById('avance_sur_loyer');
            const montantAvanceInput = document.getElementById('montant_avance');

            function calculerCaution() {
                if (loyerInput && nombreCautionsInput && cautionInput) {
                    const loyer = parseFloat(loyerInput.value.replace(/\s/g, '')) || 0;
                    const nombreCautions = parseFloat(nombreCautionsInput.value) || 0;
                    const caution = loyer * nombreCautions;
                    cautionInput.value = Math.round(caution).toLocaleString('fr-FR').replace(/,/g, ' ');
                }
            }

            function calculerAvance() {
                if (loyerInput && avanceSurLoyerInput && montantAvanceInput) {
                    const loyer = parseFloat(loyerInput.value.replace(/\s/g, '')) || 0;
                    const avanceMois = parseFloat(avanceSurLoyerInput.value) || 0;
                    const montantAvance = loyer * avanceMois;
                    montantAvanceInput.value = Math.round(montantAvance).toLocaleString('fr-FR').replace(/,/g, ' ');
                }
            }

            if (loyerInput && nombreCautionsInput) {
                loyerInput.addEventListener('input', function() {
                    calculerCaution();
                    calculerAvance();
                });
                nombreCautionsInput.addEventListener('input', calculerCaution);

                if (avanceSurLoyerInput) {
                    avanceSurLoyerInput.addEventListener('input', calculerAvance);
                }

                // Calcul initial au chargement du modal
                calculerCaution();
                calculerAvance();

                // Recalculer quand le modal s'ouvre (les champs sont déjà reformatés par l'événement sur modalConfiguration)
                const modal = document.getElementById('configurerPaiementModal');
                if (modal) {
                    modal.addEventListener('shown.bs.modal', function() {
                        calculerCaution();
                        calculerAvance();
                    });
                }
            }

            // Gestion du modal de modification de commission
            const typeCommissionModal = document.getElementById('type_commission_modal');
            const commissionAgenceModal = document.getElementById('commission_agence_modal');
            const commissionHint = document.getElementById('commission_hint');
            const commissionPreview = document.getElementById('commission_preview');
            const loyerMensuel = {{ $location->loyer_mensuel ?? 0 }};

            function updateCommissionPreview() {
                if (!commissionAgenceModal || !typeCommissionModal || !commissionPreview) return;

                const type = typeCommissionModal.value;
                const montant = parseFloat(commissionAgenceModal.value) || 0;

                // Mettre à jour le hint
                if (commissionHint) {
                    if (type === 'pourcentage') {
                        commissionHint.textContent = 'Ex: 10 pour 10% du loyer mensuel';
                    } else {
                        commissionHint.textContent = 'Ex: 5 000 pour 5 000 FCFA par mois';
                    }
                }

                // Mettre à jour l'aperçu
                if (loyerMensuel > 0) {
                    if (type === 'pourcentage') {
                        const commission = (loyerMensuel * montant / 100);
                        commissionPreview.innerHTML =
                            `Commission : ${Math.round(commission).toLocaleString('fr-FR')} FCFA (${montant}%)`;
                    } else {
                        commissionPreview.innerHTML =
                            `Commission : ${Math.round(montant).toLocaleString('fr-FR')} FCFA`;
                    }
                }
            }

            if (typeCommissionModal && commissionAgenceModal) {
                typeCommissionModal.addEventListener('change', updateCommissionPreview);
                commissionAgenceModal.addEventListener('input', updateCommissionPreview);

                // Mettre à jour l'aperçu quand le modal s'ouvre
                const modalCommission = document.getElementById('modifierCommissionModal');
                if (modalCommission) {
                    modalCommission.addEventListener('shown.bs.modal', updateCommissionPreview);
                }
            }
        });

        // Fonction pour marquer la fiche comme envoyée
        function marquerFicheEnvoyee() {
            if (confirm('Confirmez-vous que la fiche a déjà été envoyée au client (par email ou autre moyen) ?')) {
                // Créer un formulaire et le soumettre
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route('backend.locations.marquer-fiche-envoyee', $location) }}';

                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);

                document.body.appendChild(form);
                form.submit();
            }
        }

        // Formater les champs de montant avec des séparateurs de milliers
        document.addEventListener('DOMContentLoaded', function() {
            // Sélectionner tous les inputs de type number pour les montants
            const montantInputs = document.querySelectorAll(
                'input[type="number"][name*="montant"], input[type="number"][name*="loyer"], input[type="number"][name*="caution"], input[type="number"][name*="commission"], input[type="number"][name*="frais"]'
            );

            montantInputs.forEach(input => {
                // Convertir en type text pour permettre le formatage
                input.setAttribute('type', 'text');
                input.setAttribute('inputmode', 'numeric');
                input.removeAttribute('pattern'); // Retirer le pattern pour éviter la validation

                // Formater la valeur initiale
                if (input.value) {
                    const numValue = input.value.replace(/\s/g, '');
                    if (numValue && !isNaN(numValue)) {
                        input.value = parseInt(numValue).toLocaleString('fr-FR').replace(/,/g, ' ');
                    }
                }

                // Formater pendant la saisie
                input.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\s/g, '');

                    // Garder seulement les chiffres
                    value = value.replace(/[^\d]/g, '');

                    // Formater avec des espaces
                    if (value) {
                        e.target.value = parseInt(value).toLocaleString('fr-FR').replace(/,/g, ' ');
                    } else {
                        e.target.value = '';
                    }
                });
            });

            // Nettoyer tous les champs formatés avant soumission de n'importe quel formulaire
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    // Retirer les espaces de tous les champs formatés dans ce formulaire
                    form.querySelectorAll('input[type="text"][inputmode="numeric"]').forEach(
                        input => {
                            input.value = input.value.replace(/\s/g, '');
                        });
                });
            });
        });
    </script>
@endsection
