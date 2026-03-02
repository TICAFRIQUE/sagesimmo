<div class="modal fade" id="createChargeModal" tabindex="-1" aria-labelledby="createChargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('backend.charges.store') }}" method="POST">
                @csrf
                <input type="hidden" name="_form_type" value="create">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white" id="createChargeModalLabel">
                        <i class="fas fa-plus-circle"></i> Ajouter une Charge
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>

                <div class="modal-body">
                    @if($errors->any())
                    <div class="alert alert-danger">
                        <strong>Erreurs :</strong>
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="c_annonce_id" class="form-label">
                                    <strong>Bien immobilier</strong> <span class="text-danger">*</span>
                                </label>
                                <select name="annonce_id" id="c_annonce_id"
                                    class="form-control @error('annonce_id') is-invalid @enderror" required>
                                    <option value="">-- Sélectionner un bien --</option>
                                    @foreach($biens as $bien)
                                        <option value="{{ $bien->id }}" @selected(old('annonce_id') == $bien->id)>
                                            {{ $bien->titre }} - {{ $bien->reference }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('annonce_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="c_type_charge" class="form-label">
                                    <strong>Type de charge</strong> <span class="text-danger">*</span>
                                </label>
                                <select name="type_charge" id="c_type_charge"
                                    class="form-control @error('type_charge') is-invalid @enderror" required>
                                    <option value="">-- Sélectionner --</option>
                                    <option value="maintenance" @selected(old('type_charge') === 'maintenance')>Maintenance</option>
                                    <option value="reparation" @selected(old('type_charge') === 'reparation')>Réparation</option>
                                    <option value="taxe" @selected(old('type_charge') === 'taxe')>Taxe</option>
                                    <option value="autre" @selected(old('type_charge') === 'autre')>Autre</option>
                                </select>
                                @error('type_charge')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="c_montant" class="form-label">
                                    <strong>Montant (FCFA)</strong> <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number" name="montant" id="c_montant"
                                        class="form-control @error('montant') is-invalid @enderror"
                                        step="1" min="0" value="{{ old('montant') }}" required>
                                    <span class="input-group-text">F</span>
                                    @error('montant')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="c_date_charge" class="form-label">
                                    <strong>Date de la charge</strong> <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="date_charge" id="c_date_charge"
                                    class="form-control @error('date_charge') is-invalid @enderror"
                                    value="{{ old('date_charge', today()->format('Y-m-d')) }}" required>
                                @error('date_charge')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="c_reference" class="form-label">
                                    <strong>Référence</strong>
                                    <span class="text-muted">(Facture, numéro, etc.)</span>
                                </label>
                                <input type="text" name="reference" id="c_reference" class="form-control"
                                    value="{{ old('reference') }}" placeholder="Ex: FAC-2025-001">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="c_description" class="form-label">
                                    <strong>Description</strong>
                                </label>
                                <input type="text" name="description" id="c_description" class="form-control"
                                    value="{{ old('description') }}" placeholder="Détails de la charge">
                            </div>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label for="c_notes" class="form-label">
                            <strong>Notes supplémentaires</strong>
                        </label>
                        <textarea name="notes" id="c_notes" class="form-control" rows="3"
                            placeholder="Remarques additionnelles...">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Annuler
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Enregistrer la charge
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>




