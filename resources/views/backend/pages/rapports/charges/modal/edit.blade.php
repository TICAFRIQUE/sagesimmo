<div class="modal fade" id="editChargeModal{{ $charge->id }}" tabindex="-1" aria-labelledby="editChargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="editChargeForm" action="" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="_form_type" value="edit">
                <input type="hidden" id="e_charge_id" name="_charge_id" value="">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="editChargeModalLabel">
                        <i class="fas fa-edit"></i> Éditer la Charge
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>

                <div class="modal-body">
                    @if ($errors->any() && old('_form_type') === 'edit')
                        <div class="alert alert-danger">
                            <strong>Erreurs :</strong>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="e_annonce_id" class="form-label">
                                    <strong>Bien immobilier</strong> <span class="text-danger">*</span>
                                </label>
                                <select name="annonce_id" id="e_annonce_id" class="form-control" required>
                                    <option value="">-- Sélectionner un bien --</option>
                                    @foreach ($biens as $bien)
                                        <option value="{{ $bien->id }}">
                                            {{ $bien->titre }} - {{ $bien->adresse }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="e_type_charge" class="form-label">
                                    <strong>Type de charge</strong> <span class="text-danger">*</span>
                                </label>
                                <select name="type_charge" id="e_type_charge" class="form-control" required>
                                    <option value="">-- Sélectionner --</option>
                                    <option value="maintenance">Maintenance</option>
                                    <option value="reparation">Réparation</option>
                                    <option value="taxe">Taxe</option>
                                    <option value="autre">Autre</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="e_montant" class="form-label">
                                    <strong>Montant (FCFA)</strong> <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number" name="montant" id="e_montant" class="form-control"
                                        step="1" min="0" required>
                                    <span class="input-group-text">F</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="e_date_charge" class="form-label">
                                    <strong>Date de la charge</strong> <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="date_charge" id="e_date_charge" class="form-control"
                                    required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="e_reference" class="form-label">
                                    <strong>Référence</strong>
                                    <span class="text-muted">(Facture, numéro, etc.)</span>
                                </label>
                                <input type="text" name="reference" id="e_reference" class="form-control"
                                    placeholder="Ex: FAC-2025-001">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="e_description" class="form-label">
                                    <strong>Description</strong>
                                </label>
                                <input type="text" name="description" id="e_description" class="form-control"
                                    placeholder="Détails de la charge">
                            </div>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label for="e_notes" class="form-label">
                            <strong>Notes supplémentaires</strong>
                        </label>
                        <textarea name="notes" id="e_notes" class="form-control" rows="3"
                            placeholder="Remarques additionnelles..."></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Annuler
                    </button>
                    <button type="submit" class="btn btn-warning text-dark fw-bold">
                        <i class="fas fa-save"></i> Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
