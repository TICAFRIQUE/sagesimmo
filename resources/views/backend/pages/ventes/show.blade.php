@extends('backend.layouts.master')
@section('title')
   Détails de la vente
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
            <a href="{{ route('backend.ventes.index') }}">Ventes</a>
        @endslot
        @slot('title')
            Vente #{{ $vente->id }}
        @endslot
    @endcomponent

    <!-- Progression du workflow -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">Progression du workflow</h5>
                    <div class="progress progress-bar-custom mb-2">
                        <div class="progress-bar bg-success" role="progressbar" 
                             style="width: {{ $vente->progression }}%">
                            {{ $vente->progression }}%
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="badge {{ $vente->statut_badge }}" style="font-size: 1rem;">
                            {{ ucfirst(str_replace('_', ' ', $vente->statut)) }}
                        </span>
                        @if($vente->date_finalisation)
                            <small class="text-muted">
                                <i class="ri-check-line text-success"></i>
                                Finalisée le {{ $vente->date_finalisation->format('d/m/Y') }}
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
                    <h5 class="card-title mb-0"><i class="ri-flow-chart me-2"></i>Workflow de vente</h5>
                </div>
                <div class="card-body">
                    <!-- Message du client -->
                    @if($vente->message_client)
                    <div class="workflow-step completed">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1"><i class="ri-message-2-line me-1"></i>Message initial du client</h6>
                                <p class="mb-0">{{ $vente->message_client }}</p>
                            </div>
                            <i class="ri-checkbox-circle-fill text-success" style="font-size: 24px;"></i>
                        </div>
                    </div>
                    @endif

                    <!-- Étape 1: Envoi de la fiche -->
                    <div class="workflow-step {{ in_array($vente->statut, ['fiche_envoyee', 'visite_planifiee', 'en_attente_paiement', 'paiement_valide']) ? 'completed' : ($vente->statut == 'demande_client' ? 'active' : '') }}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <h6 class="mb-2">
                                    <i class="ri-file-text-line me-1"></i>
                                    1. Envoi de la fiche au client
                                </h6>
                                @if($vente->statut == 'demande_client')
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#envoyerFicheModal">
                                        <i class="ri-send-plane-line me-1"></i>Envoyer la fiche
                                    </button>
                                @else
                                    <span class="badge bg-success"><i class="ri-check-line me-1"></i>Complété</span>
                                @endif
                            </div>
                            @if(in_array($vente->statut, ['fiche_envoyee', 'visite_planifiee', 'en_attente_paiement', 'paiement_valide']))
                                <i class="ri-checkbox-circle-fill text-success" style="font-size: 24px;"></i>
                            @endif
                        </div>
                    </div>

                    <!-- Étape 2: Planification de la visite -->
                    <div class="workflow-step {{ in_array($vente->statut, ['visite_planifiee', 'en_attente_paiement', 'paiement_valide']) ? 'completed' : ($vente->statut == 'fiche_envoyee' ? 'active' : '') }}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <h6 class="mb-2">
                                    <i class="ri-calendar-check-line me-1"></i>
                                    2. Planification de la visite
                                </h6>
                                @if($vente->statut == 'fiche_envoyee')
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#planifierVisiteModal">
                                        <i class="ri-calendar-event-line me-1"></i>Planifier la visite
                                    </button>
                                @elseif($vente->date_visite)
                                    <p class="mb-0">
                                        <i class="ri-time-line me-1"></i>
                                        <strong>Date:</strong> {{ \Carbon\Carbon::parse($vente->date_visite)->format('d/m/Y à H:i') }}
                                    </p>
                                @endif
                            </div>
                            @if(in_array($vente->statut, ['visite_planifiee', 'en_attente_paiement', 'paiement_valide']))
                                <i class="ri-checkbox-circle-fill text-success" style="font-size: 24px;"></i>
                            @endif
                        </div>
                    </div>

                    <!-- Étape 3: Visite effectuée -->
                    <div class="workflow-step {{ in_array($vente->statut, ['en_attente_paiement', 'paiement_valide']) ? 'completed' : ($vente->statut == 'visite_planifiee' ? 'active' : '') }}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <h6 class="mb-2">
                                    <i class="ri-home-smile-line me-1"></i>
                                    3. Visite effectuée
                                </h6>
                                @if($vente->statut == 'visite_planifiee')
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#visiteEffectueeModal">
                                        <i class="ri-checkbox-line me-1"></i>Marquer comme effectuée
                                    </button>
                                @elseif($vente->compte_rendu_visite)
                                    <div class="alert alert-info mt-2 mb-0">
                                        <strong>Compte rendu:</strong><br>
                                        {{ $vente->compte_rendu_visite }}
                                    </div>
                                @endif
                            </div>
                            @if(in_array($vente->statut, ['en_attente_paiement', 'paiement_valide']))
                                <i class="ri-checkbox-circle-fill text-success" style="font-size: 24px;"></i>
                            @endif
                        </div>
                    </div>

                    <!-- Étape 4: Configuration du paiement -->
                    <div class="workflow-step {{ $vente->statut == 'paiement_valide' ? 'completed' : ($vente->statut == 'en_attente_paiement' ? 'active' : '') }}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <h6 class="mb-2">
                                    <i class="ri-money-dollar-circle-line me-1"></i>
                                    4. Configuration du paiement
                                </h6>
                                @if(in_array($vente->statut, ['en_attente_paiement', 'paiement_valide']))
                                    <div class="info-card mt-2">
                                        <div class="row mb-2">
                                            <div class="col-6"><strong>Prix de vente:</strong></div>
                                            <div class="col-6 text-end">{{ number_format($vente->prix_vente, 0, ',', ' ') }} FCFA</div>
                                        </div>
                                        @if($vente->montant_caution)
                                        <div class="row mb-2">
                                            <div class="col-6"><strong>Caution:</strong></div>
                                            <div class="col-6 text-end">{{ number_format($vente->montant_caution, 0, ',', ' ') }} FCFA</div>
                                        </div>
                                        @endif
                                        @if($vente->montant_frais_agence)
                                        <div class="row mb-2">
                                            <div class="col-6"><strong>Frais d'agence:</strong></div>
                                            <div class="col-6 text-end">{{ number_format($vente->montant_frais_agence, 0, ',', ' ') }} FCFA</div>
                                        </div>
                                        @endif
                                        @if($vente->commission_agence)
                                        <div class="row mb-2">
                                            <div class="col-6"><strong>Commission agence:</strong></div>
                                            <div class="col-6 text-end text-info">
                                                @if($vente->type_commission == 'pourcentage')
                                                    {{ $vente->commission_agence }}% = {{ number_format(($vente->prix_vente * $vente->commission_agence) / 100, 0, ',', ' ') }} FCFA
                                                @else
                                                    {{ number_format($vente->commission_agence, 0, ',', ' ') }} FCFA (Fixe)
                                                @endif
                                            </div>
                                        </div>
                                        @endif
                                        <hr>
                                        <div class="row">
                                            <div class="col-6"><strong>TOTAL:</strong></div>
                                            <div class="col-6 text-end"><strong>{{ number_format($vente->prix_vente + ($vente->montant_caution ?? 0) + ($vente->montant_frais_agence ?? 0), 0, ',', ' ') }} FCFA</strong></div>
                                        </div>
                                    </div>
                                    @if($vente->statut == 'en_attente_paiement')
                                        <div class="mt-3 p-3 bg-light rounded">
                                            <h6 class="mb-2">Paiements effectués</h6>
                                            @if($vente->paiements->count() > 0)
                                                @foreach($vente->paiements as $paiement)
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <div>
                                                        <strong>{{ number_format($paiement->montant, 0, ',', ' ') }} FCFA</strong>
                                                        <small class="text-muted d-block">{{ $paiement->date_paiement->format('d/m/Y') }} - {{ ucfirst($paiement->methode_paiement) }}</small>
                                                    </div>
                                                    <span class="badge bg-success">Payé</span>
                                                </div>
                                                @endforeach
                                                <hr>
                                                <div class="d-flex justify-content-between">
                                                    <strong>Total payé:</strong>
                                                    <strong class="text-success">{{ number_format($vente->montantTotal(), 0, ',', ' ') }} FCFA</strong>
                                                </div>
                                                <div class="d-flex justify-content-between text-danger">
                                                    <strong>Reste à payer:</strong>
                                                    <strong>{{ number_format($vente->resteAPayer(), 0, ',', ' ') }} FCFA</strong>
                                                </div>
                                            @else
                                                <p class="text-muted mb-0">Aucun paiement enregistré</p>
                                            @endif
                                            <button class="btn btn-sm btn-primary w-100 mt-3" data-bs-toggle="modal" data-bs-target="#addPaiementModal">
                                                <i class="ri-add-line me-1"></i>Ajouter un paiement
                                            </button>
                                        </div>
                                        @if($vente->resteAPayer() == 0)
                                        <button class="btn btn-sm btn-success mt-2" data-bs-toggle="modal" data-bs-target="#validerPaiementModal">
                                            <i class="ri-check-double-line me-1"></i>Paiement complet - Remettre les clés
                                        </button>
                                        @endif
                                    @endif
                                @else
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#configurerPaiementModal" 
                                            {{ $vente->statut != 'en_attente_paiement' ? 'disabled' : '' }}>
                                        <i class="ri-settings-line me-1"></i>Configurer le paiement
                                    </button>
                                @endif
                            </div>
                            @if($vente->statut == 'paiement_valide')
                                <i class="ri-checkbox-circle-fill text-success" style="font-size: 24px;"></i>
                            @endif
                        </div>
                    </div>

                    <!-- Étape 5: Vente finalisée -->
                    @if($vente->statut == 'paiement_valide')
                    <div class="workflow-step completed">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1"><i class="ri-trophy-line me-1"></i>Vente finalisée</h6>
                                <p class="mb-0">Le bien a été marqué comme vendu. Remise des clés au client.</p>
                            </div>
                            <i class="ri-checkbox-circle-fill text-success" style="font-size: 24px;"></i>
                        </div>
                    </div>
                    @endif

                    <!-- Vente annulée -->
                    @if($vente->statut == 'annule')
                    <div class="alert alert-danger mt-3">
                        <h6><i class="ri-close-circle-line me-1"></i>Vente annulée</h6>
                        @if($vente->note_admin)
                        <p class="mb-0">{{ $vente->note_admin }}</p>
                        @endif
                    </div>
                    @endif

                    <!-- Actions rapides -->
                    @if($vente->statut != 'paiement_valide' && $vente->statut != 'annule')
                    <div class="mt-4 pt-3 border-top">
                        <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#annulerVenteModal">
                            <i class="ri-close-line me-1"></i>Annuler la vente
                        </button>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Note admin -->
            @if($vente->note_admin)
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="ri-sticky-note-line me-2"></i>Notes administrateur</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $vente->note_admin }}</p>
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
                    @if($vente->annonce->getFirstMediaUrl('images'))
                        <img src="{{ $vente->annonce->getFirstMediaUrl('images') }}" class="img-fluid rounded mb-3" alt="Bien">
                    @endif
                    <h6>{{ $vente->annonce->titre }}</h6>
                    <p class="text-muted mb-2">
                        <i class="ri-map-pin-line me-1"></i>
                        {{ $vente->annonce->ville }}, {{ $vente->annonce->quartier }}
                    </p>
                    <div class="info-card">
                        <p class="mb-1"><strong>Type:</strong> {{ $vente->annonce->typeBien->nom ?? 'N/A' }}</p>
                        <p class="mb-1"><strong>Surface:</strong> {{ $vente->annonce->surface }} m²</p>
                        <p class="mb-0"><strong>Prix:</strong> {{ number_format($vente->annonce->prix, 0, ',', ' ') }} FCFA</p>
                    </div>
                    <a href="{{ route('backend.annonces.show', $vente->annonce) }}" class="btn btn-sm btn-outline-primary mt-2">
                        <i class="ri-eye-line me-1"></i>Voir l'annonce
                    </a>
                </div>
            </div>

            <!-- Informations du client -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="ri-user-line me-2"></i>Client</h5>
                </div>
                <div class="card-body">
                    <div class="info-card">
                        <p class="mb-2"><strong>Nom:</strong> {{ $vente->client->username }}</p>
                        <p class="mb-2">
                            <strong>Email:</strong><br>
                            <a href="mailto:{{ $vente->client->email }}">{{ $vente->client->email }}</a>
                        </p>
                        <p class="mb-0">
                            <strong>Téléphone:</strong><br>
                            <a href="tel:{{ $vente->client->phone }}">{{ $vente->client->phone ?? 'N/A' }}</a>
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
                    <a href="{{ route('backend.ventes.edit', $vente) }}" class="btn btn-primary btn-sm w-100 mb-2">
                        <i class="ri-edit-line me-1"></i>Modifier
                    </a>
                    <form action="{{ route('backend.ventes.destroy', $vente) }}" method="POST" 
                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette vente ?');">
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
            <form action="{{ route('backend.ventes.envoyer-fiche', $vente) }}" method="POST" enctype="multipart/form-data">
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
                        <div class="mb-3">
                            <label class="form-label">Note interne (optionnelle)</label>
                            <textarea name="note_admin" class="form-control" rows="2" 
                                      placeholder="Note interne concernant l'envoi de la fiche"></textarea>
                        </div>
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

    <!-- Modal: Planifier la visite -->
    <div class="modal fade" id="planifierVisiteModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('backend.ventes.planifier-visite', $vente) }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Planifier la visite</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Date et heure de la visite <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="date_visite" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Note (optionnelle)</label>
                            <textarea name="note_admin" class="form-control" rows="3" 
                                      placeholder="Instructions pour la visite"></textarea>
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
            <form action="{{ route('backend.ventes.visite-effectuee', $vente) }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Marquer la visite comme effectuée</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Le client est-il intéressé ? <span class="text-danger">*</span></label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="client_interesse" id="interesse_oui" value="1" required>
                                    <label class="form-check-label" for="interesse_oui">Oui</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="client_interesse" id="interesse_non" value="0" required>
                                    <label class="form-check-label" for="interesse_non">Non</label>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Compte rendu de la visite <span class="text-danger">*</span></label>
                            <textarea name="compte_rendu_visite" class="form-control" rows="4" required 
                                      placeholder="Décrivez comment s'est passée la visite..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Note additionnelle (optionnelle)</label>
                            <textarea name="note_admin" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="alert alert-warning">
                            <i class="ri-alert-line me-1"></i>
                            Si le client n'est pas intéressé, la vente sera automatiquement annulée.
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

    <!-- Modal: Configurer le paiement -->
    <div class="modal fade" id="configurerPaiementModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form action="{{ route('backend.ventes.configurer-paiement', $vente) }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Configurer le paiement</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Prix et Caution -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Prix de vente <span class="text-danger">*</span></label>
                                <input type="number" name="prix_vente" id="prix_vente_config" class="form-control" 
                                       value="{{ $vente->prix_vente }}" required step="0.01">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Caution</label>
                                <input type="number" name="montant_caution" id="montant_caution_config" class="form-control" 
                                       value="{{ $vente->montant_caution ?? 0 }}" step="0.01">
                            </div>
                        </div>

                        <!-- Commission (Bloc mis en évidence) -->
                        <div class="card bg-light mb-3">
                            <div class="card-body">
                                <h6 class="card-title mb-3">
                                    <i class="ri-percent-line me-1"></i>Commission de l'agence
                                </h6>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Type de commission <span class="text-danger">*</span></label>
                                        <select name="type_commission" class="form-select" id="type_commission_config" required>
                                            <option value="pourcentage" {{ ($vente->type_commission ?? $vente->annonce->type_commission) == 'pourcentage' ? 'selected' : '' }}>Pourcentage (%)</option>
                                            <option value="montant" {{ ($vente->type_commission ?? $vente->annonce->type_commission) == 'montant' ? 'selected' : '' }}>Montant Fixe (FCFA)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Valeur de la commission <span class="text-danger">*</span></label>
                                        <input type="number" name="commission_agence" id="commission_agence_config" class="form-control" 
                                               value="{{ $vente->commission_agence ?? $vente->annonce->commission ?? 0 }}" step="0.01" min="0" required>
                                        <small class="text-muted" id="commission_hint_config">
                                            @php
                                                $typeComm = $vente->type_commission ?? $vente->annonce->type_commission;
                                                $commValue = $vente->commission_agence ?? $vente->annonce->commission;
                                            @endphp
                                            @if($typeComm == 'pourcentage' && $commValue)
                                                {{ $commValue }}% = {{ number_format(($vente->prix_vente * $commValue) / 100, 0, ',', ' ') }} FCFA
                                            @elseif($typeComm == 'fixe' && $commValue)
                                                {{ number_format($commValue, 0, ',', ' ') }} FCFA
                                            @endif
                                        </small>
                                    </div>
                                </div>
                                <div class="alert alert-info mb-0">
                                    <i class="ri-information-line me-1"></i>
                                    <strong>Commission calculée :</strong> <span id="commission_calculee">0</span> FCFA
                                </div>
                            </div>
                        </div>

                        <!-- Frais d'agence -->
                        <div class="mb-3">
                            <label class="form-label">Frais d'agence additionnels</label>
                            <input type="number" name="montant_frais_agence" id="montant_frais_agence_config" class="form-control" 
                                   value="{{ $vente->montant_frais_agence ?? 0 }}" step="0.01">
                            <small class="text-muted">Frais supplémentaires (dossier, visite, etc.)</small>
                        </div>

                        <!-- Récapitulatif -->
                        <div class="card bg-success bg-opacity-10 border-success mb-3">
                            <div class="card-body">
                                <h6 class="card-title text-success mb-3">
                                    <i class="ri-money-dollar-circle-line me-1"></i>Récapitulatif du montant total
                                </h6>
                                <table class="table table-sm mb-0">
                                    <tbody>
                                        <tr>
                                            <td>Prix de vente :</td>
                                            <td class="text-end"><strong id="recap_prix_vente">{{ number_format($vente->prix_vente, 0, ',', ' ') }}</strong> FCFA</td>
                                        </tr>
                                        <tr>
                                            <td>Commission agence :</td>
                                            <td class="text-end"><strong id="recap_commission">0</strong> FCFA</td>
                                        </tr>
                                        <tr>
                                            <td>Frais d'agence :</td>
                                            <td class="text-end"><strong id="recap_frais">0</strong> FCFA</td>
                                        </tr>
                                        <tr>
                                            <td>Caution :</td>
                                            <td class="text-end"><strong id="recap_caution">0</strong> FCFA</td>
                                        </tr>
                                        <tr class="table-success">
                                            <td><strong>TOTAL À PAYER :</strong></td>
                                            <td class="text-end"><strong class="fs-5 text-success" id="recap_total">{{ number_format($vente->prix_vente, 0, ',', ' ') }}</strong> FCFA</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Conditions et notes -->
                        <div class="mb-3">
                            <label class="form-label">Conditions de paiement (optionnelles)</label>
                            <textarea name="conditions" class="form-control" rows="3" 
                                      placeholder="Ex: Paiement en 2 versements...">{{ $vente->conditions }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Note interne (optionnelle)</label>
                            <textarea name="note_admin" class="form-control" rows="2"></textarea>
                        </div>
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

    <!-- Modal: Valider le paiement -->
    <div class="modal fade" id="validerPaiementModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('backend.ventes.valider-paiement', $vente) }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">Valider le paiement complet et remettre les clés</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        @php
                            $resteAPayer = $vente->resteAPayer();
                        @endphp
                        
                        @if($resteAPayer > 0)
                            <div class="alert alert-danger">
                                <h6 class="alert-heading"><i class="ri-alert-line me-1"></i>Paiement incomplet</h6>
                                <p class="mb-0">Il reste <strong>{{ number_format($resteAPayer, 0, ',', ' ') }} FCFA</strong> à payer.</p>
                                <p class="mb-0 mt-2">Vous devez d'abord ajouter le(s) paiement(s) manquant(s) avant de pouvoir finaliser la vente.</p>
                            </div>
                        @else
                            <div class="alert alert-success">
                                <h6 class="alert-heading"><i class="ri-check-double-line me-1"></i>Paiement complet</h6>
                                <p class="mb-0">Le montant total a été payé. Vous pouvez finaliser la vente et remettre les clés au client.</p>
                            </div>
                        @endif
                        
                        <div class="mb-3">
                            <label class="form-label">Date de signature</label>
                            <input type="date" name="date_signature" class="form-control" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Note finale (optionnelle)</label>
                            <textarea name="note_admin" class="form-control" rows="3" 
                                      placeholder="Remarques finales sur la transaction et la remise des clés"></textarea>
                        </div>
                        <div class="alert alert-info">
                            <i class="ri-information-line me-1"></i>
                            Le bien sera automatiquement marqué comme <strong>vendu</strong> et retiré de la publication. Les clés seront remises au client.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-success" {{ $resteAPayer > 0 ? 'disabled' : '' }}>
                            <i class="ri-check-double-line me-1"></i>Valider et remettre les clés
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Annuler la vente -->
    <div class="modal fade" id="annulerVenteModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('backend.ventes.annuler', $vente) }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Annuler la vente</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="ri-alert-line me-1"></i>
                            Êtes-vous sûr de vouloir annuler cette vente ?
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Raison de l'annulation <span class="text-danger">*</span></label>
                            <textarea name="note_admin" class="form-control" rows="4" required 
                                      placeholder="Expliquez pourquoi la vente est annulée"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Retour</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="ri-close-line me-1"></i>Confirmer l'annulation
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Ajouter un paiement -->
    <div class="modal fade" id="addPaiementModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('backend.ventes.add-paiement', $vente) }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Ajouter un paiement</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <strong>Montant total à payer:</strong> {{ number_format($vente->prix_vente + ($vente->montant_caution ?? 0) + ($vente->montant_frais_agence ?? 0), 0, ',', ' ') }} FCFA<br>
                            <strong>Déjà payé:</strong> {{ number_format($vente->montantTotal(), 0, ',', ' ') }} FCFA<br>
                            <strong class="text-danger">Reste à payer:</strong> {{ number_format($vente->resteAPayer(), 0, ',', ' ') }} FCFA
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Montant du paiement <span class="text-danger">*</span></label>
                            <input type="number" name="montant" class="form-control" required step="0.01" 
                                   max="{{ $vente->resteAPayer() }}" 
                                   placeholder="Montant maximum: {{ number_format($vente->resteAPayer(), 0, ',', ' ') }} FCFA">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Date de paiement <span class="text-danger">*</span></label>
                            <input type="date" name="date_paiement" class="form-control" required value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Méthode de paiement <span class="text-danger">*</span></label>
                            <select name="methode_paiement" class="form-select" required>
                                <option value="virement">Virement</option>
                                <option value="espèces">Espèces</option>
                                <option value="chèque">Chèque</option>
                                <option value="carte_bancaire">Carte bancaire</option>
                                <option value="autre">Autre</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Référence (optionnelle)</label>
                            <input type="text" name="reference" class="form-control" placeholder="N° de transaction, chèque, etc.">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Notes (optionnelles)</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Remarques sur ce paiement"></textarea>
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
@endsection

@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Calcul automatique de la commission et du récapitulatif
        const prixVenteInput = document.getElementById('prix_vente_config');
        const typeCommissionSelect = document.getElementById('type_commission_config');
        const commissionInput = document.getElementById('commission_agence_config');
        const fraisInput = document.getElementById('montant_frais_agence_config');
        const cautionInput = document.getElementById('montant_caution_config');
        
        function calculerRecapitulatif() {
            if (!prixVenteInput || !typeCommissionSelect || !commissionInput) return;
            
            const prixVente = parseFloat(prixVenteInput.value) || 0;
            const typeCommission = typeCommissionSelect.value;
            const commissionValue = parseFloat(commissionInput.value) || 0;
            const frais = parseFloat(fraisInput?.value) || 0;
            const caution = parseFloat(cautionInput?.value) || 0;
            
            let commissionCalculee = 0;
            
            if (typeCommission === 'pourcentage') {
                commissionCalculee = (prixVente * commissionValue) / 100;
                document.getElementById('commission_hint_config').textContent = 
                    commissionValue + '% = ' + commissionCalculee.toLocaleString('fr-FR') + ' FCFA';
            } else if (typeCommission === 'fixe') {
                commissionCalculee = commissionValue;
                document.getElementById('commission_hint_config').textContent = 
                    commissionValue.toLocaleString('fr-FR') + ' FCFA';
            }
            
            // Mise à jour du récapitulatif
            document.getElementById('commission_calculee').textContent = commissionCalculee.toLocaleString('fr-FR');
            document.getElementById('recap_prix_vente').textContent = prixVente.toLocaleString('fr-FR');
            document.getElementById('recap_commission').textContent = commissionCalculee.toLocaleString('fr-FR');
            document.getElementById('recap_frais').textContent = frais.toLocaleString('fr-FR');
            document.getElementById('recap_caution').textContent = caution.toLocaleString('fr-FR');
            
            const total = prixVente + commissionCalculee + frais + caution;
            document.getElementById('recap_total').textContent = total.toLocaleString('fr-FR');
        }
        
        // Écouteurs d'événements pour recalculer automatiquement
        if (prixVenteInput) {
            prixVenteInput.addEventListener('input', calculerRecapitulatif);
        }
        if (typeCommissionSelect) {
            typeCommissionSelect.addEventListener('change', calculerRecapitulatif);
        }
        if (commissionInput) {
            commissionInput.addEventListener('input', calculerRecapitulatif);
        }
        if (fraisInput) {
            fraisInput.addEventListener('input', calculerRecapitulatif);
        }
        if (cautionInput) {
            cautionInput.addEventListener('input', calculerRecapitulatif);
        }
        
        // Calcul initial au chargement
        calculerRecapitulatif();
    });
</script>
@endsection
