<!-- Modal Planifier Visite -->
<div class="modal fade" id="planifierVisiteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('backend.demandes.planifier-visite', $demande->id) }}" method="POST">
                @csrf
                <input type="hidden" name="_modal" value="planifierVisiteModal">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">Planifier une visite</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="mb-3">
                        <label class="form-label">Date et heure de la visite *</label>
                        <input type="datetime-local" name="date_visite" class="form-control @error('date_visite') is-invalid @enderror" value="{{ old('date_visite') }}" required>
                        @error('date_visite')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Note interne (optionnel)</label>
                        <textarea name="note_admin" class="form-control" rows="2">{{ old('note_admin', $demande->note_admin) }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-info">
                        <i class="ri-calendar-line"></i> Planifier
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Visite Effectuée -->
<div class="modal fade" id="visiteEffectueeModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('backend.demandes.visite-effectuee', $demande->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Compte-rendu de visite</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Compte-rendu de la visite *</label>
                        <textarea name="compte_rendu_visite" class="form-control" rows="4" required placeholder="Décrivez comment s'est passée la visite..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Le client est-il intéressé ? *</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="client_interesse_apres_visite" value="1" id="interesse_oui" required>
                            <label class="form-check-label" for="interesse_oui">
                                Oui, intéressé
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="client_interesse_apres_visite" value="0" id="interesse_non" required>
                            <label class="form-check-label" for="interesse_non">
                                Non, pas intéressé
                            </label>
                        </div>
                        <small class="text-muted">Si "Non", la demande sera automatiquement clôturée.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Note interne (optionnel)</label>
                        <textarea name="note_admin" class="form-control" rows="2">{{ $demande->note_admin }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">
                        <i class="ri-check-line"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Demander Pièces -->
<div class="modal fade" id="demanderPiecesModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('backend.demandes.demander-pieces', $demande->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Demander des pièces au client</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Pièces à demander *</label>
                        <div class="form-check">
                            <input class="form-check-input piece-checkbox" type="checkbox" value="CNI ou Passeport" id="cni">
                            <label class="form-check-label" for="cni">CNI ou Passeport</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input piece-checkbox" type="checkbox" value="Bulletins de salaire (3 derniers mois)" id="salaire">
                            <label class="form-check-label" for="salaire">Bulletins de salaire (3 derniers mois)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input piece-checkbox" type="checkbox" value="Attestation de travail" id="travail">
                            <label class="form-check-label" for="travail">Attestation de travail</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input piece-checkbox" type="checkbox" value="Justificatif de domicile" id="domicile">
                            <label class="form-check-label" for="domicile">Justificatif de domicile</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input piece-checkbox" type="checkbox" value="RIB" id="rib">
                            <label class="form-check-label" for="rib">RIB</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Autres pièces</label>
                        <textarea class="form-control" id="autresPieces" rows="2" placeholder="Précisez d'autres pièces..."></textarea>
                    </div>
                    <input type="hidden" name="pieces_demandees" id="piecesDemandeesInput">
                    <div class="mb-3">
                        <label class="form-label">Note interne (optionnel)</label>
                        <textarea name="note_admin" class="form-control" rows="2">{{ $demande->note_admin }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary" onclick="collectPieces()">
                        <i class="ri-file-list-line"></i> Demander
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Documents Reçus -->
<div class="modal fade" id="documentsRecusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('backend.demandes.documents-recus', $demande->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-purple text-white">
                    <h5 class="modal-title">Confirmer réception des documents</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Confirmez que vous avez bien reçu tous les documents demandés au client.</p>
                    <div class="mb-3">
                        <label class="form-label">Note interne (optionnel)</label>
                        <textarea name="note_admin" class="form-control" rows="2">{{ $demande->note_admin }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-purple">
                        <i class="ri-check-line"></i> Confirmer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Valider Dossier -->
<div class="modal fade" id="validerDossierModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('backend.demandes.valider-dossier', $demande->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Valider le dossier</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Le dossier client a été vérifié et est complet ?</p>
                    <div class="mb-3">
                        <label class="form-label">Note interne (optionnel)</label>
                        <textarea name="note_admin" class="form-control" rows="2">{{ $demande->note_admin }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">
                        <i class="ri-check-double-line"></i> Valider
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Refuser Dossier -->
<div class="modal fade" id="refuserDossierModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('backend.demandes.refuser-dossier', $demande->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Refuser le dossier</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Raison du refus *</label>
                        <textarea name="raison_refus_dossier" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Note interne (optionnel)</label>
                        <textarea name="note_admin" class="form-control" rows="2">{{ $demande->note_admin }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="ri-close-line"></i> Refuser
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Générer Contrat -->
<div class="modal fade" id="genererContratModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('backend.demandes.generer-contrat', $demande->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-teal text-white">
                    <h5 class="modal-title">Générer et uploader le contrat</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Fichier contrat (PDF) *</label>
                        <input type="file" name="contrat" class="form-control" accept=".pdf" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Note interne (optionnel)</label>
                        <textarea name="note_admin" class="form-control" rows="2">{{ $demande->note_admin }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-teal">
                        <i class="ri-file-upload-line"></i> Générer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Configurer Paiement -->
<div class="modal fade" id="configurerPaiementModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('backend.demandes.configurer-paiement', $demande->id) }}" method="POST" id="formPaiement">
                @csrf
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title">Configurer le paiement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Informations du bien -->
                    <div class="alert alert-info">
                        <strong><i class="ri-information-line me-1"></i>Bien concerné:</strong> {{ $demande->annonce->titre }}<br>
                        <strong>Prix:</strong> {{ number_format($demande->annonce->prix, 0, ',', ' ') }} FCFA
                        @if($demande->annonce->type_transaction == 'location')
                            <span class="text-muted">(loyer {{ $demande->annonce->frequence_loyer }})</span>
                        @endif
                    </div>

                    @if($demande->annonce->type_transaction == 'vente')
                        <!-- Section Commission (pour vente uniquement) -->
                        <div class="card mb-3 border-info">
                            <div class="card-header bg-info-subtle">
                                <h6 class="mb-0"><i class="ri-information-line me-1"></i>Commission de l'agence (Information interne)</h6>
                                <small class="text-muted">Cette commission n'est pas ajoutée au montant payé par le client</small>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Commission agence</label>
                                        <input type="number" name="commission_agence" id="commission_agence_config" 
                                               class="form-control @error('commission_agence') is-invalid @enderror" 
                                               value="{{ old('commission_agence', $demande->annonce->commission ?? '') }}" 
                                               step="0.01" min="0">
                                        <small class="text-muted" id="commission-info-config"></small>
                                        @error('commission_agence')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Type commission</label>
                                        <select name="type_commission" id="type_commission_config" class="form-select @error('type_commission') is-invalid @enderror">
                                            <option value="pourcentage" {{ old('type_commission', $demande->annonce->type_commission ?? 'pourcentage') == 'pourcentage' ? 'selected' : '' }}>Pourcentage (%)</option>
                                            <option value="fixe" {{ old('type_commission', $demande->annonce->type_commission) == 'fixe' ? 'selected' : '' }}>Montant fixe</option>
                                        </select>
                                        @error('type_commission')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="alert alert-light border mb-0">
                                    <small><i class="ri-lightbulb-line me-1"></i><strong>Note:</strong> Le client paie uniquement le prix du bien ({{ number_format($demande->annonce->prix, 0, ',', ' ') }} FCFA). La commission représente ce que l'agence reçoit sur cette vente.</small>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Section Montants -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="ri-money-dollar-circle-line me-1"></i>Détails du paiement</h6>
                        </div>
                        <div class="card-body">
                            <!-- Total récapitulatif -->
                            <div class="mb-3 pb-3 border-bottom">
                                @if($demande->annonce->type_transaction == 'vente')
                                <div class="row mb-2">
                                    <div class="col-12">
                                        <div class="alert alert-info mb-0">
                                            <strong><i class="ri-shopping-bag-line me-1"></i>Prix du bien:</strong> {{ number_format($demande->annonce->prix, 0, ',', ' ') }} FCFA
                                        </div>
                                    </div>
                                </div>
                                @else
                                <div class="row mb-2">
                                    <div class="col-12">
                                        <div class="alert alert-info mb-0">
                                            <strong><i class="ri-home-line me-1"></i>Loyer {{ $demande->annonce->frequence_loyer }}:</strong> {{ number_format($demande->annonce->prix, 0, ',', ' ') }} FCFA
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>

                            <!-- Informations de paiement -->
                            <div class="border rounded p-3 bg-light">
                                <h6 class="mb-3"><i class="ri-cash-line me-1"></i>Informations de paiement</h6>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Montant payé <span class="text-danger">*</span></label>
                                        <input type="number" name="montant_paiement" id="montant_paiement_config" 
                                               class="form-control @error('montant_paiement') is-invalid @enderror" 
                                               value="{{ old('montant_paiement', $demande->annonce->prix) }}" 
                                               min="0" step="0.01" required>
                                        @error('montant_paiement')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Date de paiement <span class="text-danger">*</span></label>
                                        <input type="date" name="date_paiement" 
                                               class="form-control @error('date_paiement') is-invalid @enderror" 
                                               value="{{ old('date_paiement', date('Y-m-d')) }}" 
                                               required>
                                        @error('date_paiement')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Méthode de paiement <span class="text-danger">*</span></label>
                                        <select name="methode_paiement" class="form-select @error('methode_paiement') is-invalid @enderror" required>
                                            <option value="">Sélectionnez</option>
                                            <option value="Espèces" {{ old('methode_paiement') == 'Espèces' ? 'selected' : '' }}>Espèces</option>
                                            <option value="Virement bancaire" {{ old('methode_paiement') == 'Virement bancaire' ? 'selected' : '' }}>Virement bancaire</option>
                                            <option value="Chèque" {{ old('methode_paiement') == 'Chèque' ? 'selected' : '' }}>Chèque</option>
                                            <option value="Mobile Money" {{ old('methode_paiement') == 'Mobile Money' ? 'selected' : '' }}>Mobile Money</option>
                                            <option value="Carte bancaire" {{ old('methode_paiement') == 'Carte bancaire' ? 'selected' : '' }}>Carte bancaire</option>
                                            <option value="Autre" {{ old('methode_paiement') == 'Autre' ? 'selected' : '' }}>Autre</option>
                                        </select>
                                        @error('methode_paiement')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Référence</label>
                                        <input type="text" name="reference_paiement" 
                                               class="form-control @error('reference_paiement') is-invalid @enderror" 
                                               value="{{ old('reference_paiement') }}" 
                                               placeholder="N° transaction, chèque, etc.">
                                        @error('reference_paiement')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label">Notes</label>
                                        <textarea name="notes_paiement" class="form-control" rows="2" placeholder="Informations complémentaires sur le paiement">{{ old('notes_paiement') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($demande->annonce->type_transaction == 'location')
                    <!-- Section Configuration du suivi de location -->
                    <div class="card mb-3 border-primary">
                        <div class="card-header bg-primary-subtle">
                            <h6 class="mb-0"><i class="ri-calendar-check-line me-1"></i>Paramètres du suivi de location</h6>
                            <small class="text-muted">Ces informations seront utilisées pour créer le suivi de location automatiquement</small>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Loyer mensuel <span class="text-danger">*</span></label>
                                    <input type="number" name="loyer_mensuel" 
                                           class="form-control @error('loyer_mensuel') is-invalid @enderror" 
                                           value="{{ old('loyer_mensuel', $demande->annonce->prix) }}" 
                                           min="0" step="0.01" required>
                                    @error('loyer_mensuel')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Nombre de cautions <span class="text-danger">*</span></label>
                                    <input type="number" name="nombre_cautions" 
                                           class="form-control @error('nombre_cautions') is-invalid @enderror" 
                                           value="{{ old('nombre_cautions', 2) }}" 
                                           min="0" required>
                                    @error('nombre_cautions')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Montant de la caution</label>
                                    <input type="number" name="caution" 
                                           class="form-control @error('caution') is-invalid @enderror" 
                                           value="{{ old('caution', $demande->annonce->prix * 2) }}" 
                                           min="0" step="0.01">
                                    <small class="text-muted">Par défaut: 2 mois de loyer</small>
                                    @error('caution')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Date de début <span class="text-danger">*</span></label>
                                    <input type="date" name="date_debut" 
                                           class="form-control @error('date_debut') is-invalid @enderror" 
                                           value="{{ old('date_debut', date('Y-m-d')) }}" 
                                           required>
                                    @error('date_debut')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Date de fin</label>
                                    <input type="date" name="date_fin" 
                                           class="form-control @error('date_fin') is-invalid @enderror" 
                                           value="{{ old('date_fin', date('Y-m-d', strtotime('+1 year'))) }}">
                                    <small class="text-muted">Par défaut: 1 an</small>
                                    @error('date_fin')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Jour de paiement <span class="text-danger">*</span></label>
                                    <input type="number" name="jour_paiement" 
                                           class="form-control @error('jour_paiement') is-invalid @enderror" 
                                           value="{{ old('jour_paiement', 5) }}" 
                                           min="1" max="31" required>
                                    <small class="text-muted">Jour du mois (1-31)</small>
                                    @error('jour_paiement')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Conditions particulières</label>
                                    <textarea name="conditions" class="form-control" rows="2" placeholder="Conditions spécifiques du contrat de location">{{ old('conditions') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label">Note interne (optionnel)</label>
                        <textarea name="note_admin" class="form-control" rows="2">{{ old('note_admin', $demande->note_admin) }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-warning">
                        @if($demande->annonce->type_transaction == 'location')
                            <i class="ri-calendar-check-line"></i> Créer le suivi et enregistrer le paiement
                        @else
                            <i class="ri-money-dollar-circle-line"></i> Finaliser la vente
                        @endif
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Script pour calculer la commission dans le modal de configuration
    document.addEventListener('DOMContentLoaded', function() {
        const formPaiement = document.getElementById('formPaiement');
        if (formPaiement) {
            const prixBien = {{ $demande->annonce->prix }};
            const typeTransaction = '{{ $demande->annonce->type_transaction }}';
            
            function calculerCommission() {
                const commissionInput = document.getElementById('commission_agence_config');
                const typeCommission = document.getElementById('type_commission_config');
                const infoDiv = document.getElementById('commission-info-config');
                
                if (commissionInput && typeCommission && infoDiv) {
                    const commission = parseFloat(commissionInput.value) || 0;
                    const type = typeCommission.value;
                    
                    if (commission > 0) {
                        if (type === 'pourcentage') {
                            const montant = (prixBien * commission) / 100;
                            infoDiv.textContent = `${commission}% = ${new Intl.NumberFormat('fr-FR').format(montant)} FCFA`;
                        } else {
                            infoDiv.textContent = `Montant fixe: ${new Intl.NumberFormat('fr-FR').format(commission)} FCFA`;
                        }
                    } else {
                        infoDiv.textContent = '';
                    }
                }
            }
            
            if (typeTransaction === 'vente') {
                document.getElementById('commission_agence_config')?.addEventListener('input', calculerCommission);
                document.getElementById('type_commission_config')?.addEventListener('change', calculerCommission);
                // Calculer à l'ouverture du modal
                calculerCommission();
            }
        }
    });
</script>

<!-- Modal Refuser Demande (nouvelle demande) -->
<div class="modal fade" id="refuserDemandeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('backend.demandes.changer-statut', $demande->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Refuser la demande</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="statut" value="cloture_refus">
                    <div class="mb-3">
                        <label class="form-label">Motif du refus *</label>
                        <textarea name="motif_refus" class="form-control" rows="3" required placeholder="Expliquez pourquoi cette demande est refusée..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Note interne (optionnel)</label>
                        <textarea name="note_admin" class="form-control" rows="2">{{ $demande->note_admin }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="ri-close-line"></i> Refuser
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Clôturer Non Intéressé -->
<div class="modal fade" id="cloturerNonInteresseModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('backend.demandes.changer-statut', $demande->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Clôturer - Client non intéressé</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="statut" value="cloture_non_interesse">
                    <div class="mb-3">
                        <label class="form-label">Motif de clôture *</label>
                        <textarea name="motif_refus" class="form-control" rows="3" required placeholder="Expliquez pourquoi le client n'est plus intéressé..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Note interne (optionnel)</label>
                        <textarea name="note_admin" class="form-control" rows="2">{{ $demande->note_admin }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="ri-close-line"></i> Clôturer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function collectPieces() {
    let pieces = [];
    document.querySelectorAll('.piece-checkbox:checked').forEach(function(checkbox) {
        pieces.push(checkbox.value);
    });
    
    let autres = document.getElementById('autresPieces').value;
    if (autres) {
        pieces.push(autres);
    }
    
    document.getElementById('piecesDemandeesInput').value = pieces.join(', ');
}
</script>
