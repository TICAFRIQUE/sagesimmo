@extends('backend.layouts.master')

@section('title')
   Modifier un Versement
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-money-check-alt"></i> Modifier un Versement
            </h1>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('backend.versements.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    <!-- Formulaire -->
    <div class="card">
        <div class="card-body">
            <form action="{{ route('backend.versements.update', $versement) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <!-- Propriétaire -->
                    <div class="col-md-6 mb-3">
                        <label for="proprietaire_id" class="form-label">Propriétaire <span class="text-danger">*</span></label>
                        <select name="proprietaire_id" id="proprietaire_id" class="form-select @error('proprietaire_id') is-invalid @enderror" required>
                            <option value="">-- Sélectionner --</option>
                            @foreach($proprietaires as $prop)
                                <option value="{{ $prop->id }}" @selected(old('proprietaire_id', $versement->proprietaire_id) == $prop->id)>
                                    {{ $prop->username }} ({{ $prop->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('proprietaire_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Montant à Verser (lecture seule) -->
                    <div class="col-md-6 mb-3">
                        <label for="montant_a_verser" class="form-label">Montant à Verser <span class="text-danger">*</span></label>
                        <input type="number" id="montant_a_verser" class="form-control" 
                            value="{{ $montantAVerser ?? 0 }}" disabled readonly step="1">
                        <small class="text-muted">Revenue net de la période</small>
                    </div>
                </div>

                <div class="row">
                    <!-- Type de versement -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Type de Versement <span class="text-danger">*</span></label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="type_versement" id="typeTotal" value="total" @checked(old('type_versement', $versement->montant == ($montantAVerser ?? 0) ? 'total' : 'partiel') == 'total')>
                            <label class="btn btn-outline-primary w-50" for="typeTotal">
                                <i class="fas fa-check-circle me-1"></i> Montant Total
                            </label>
                            
                            <input type="radio" class="btn-check" name="type_versement" id="typePartiel" value="partiel" @checked(old('type_versement', $versement->montant != ($montantAVerser ?? 0) ? 'partiel' : 'total') == 'partiel')>
                            <label class="btn btn-outline-primary w-50" for="typePartiel">
                                <i class="fas fa-minus-circle me-1"></i> Partiel
                            </label>
                        </div>
                        @error('type_versement')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Montant -->
                    <div class="col-md-6 mb-3">
                        <label for="montant" class="form-label">Montant (F) <span class="text-danger">*</span></label>
                        <input type="number" name="montant" id="montant" class="form-control @error('montant') is-invalid @enderror" 
                            value="{{ old('montant', $versement->montant) }}" placeholder="0" step="1" required>
                        @error('montant')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted" id="montant_info"></small>
                    </div>
                </div>

                <div class="row">
                    <!-- Montant restant à payer -->
                    <div class="col-md-6 mb-3">
                        <label for="montant_restant" class="form-label">Montant Restant à Payer</label>
                        <input type="number" id="montant_restant" class="form-control bg-light" 
                            value="{{ ($montantAVerser ?? 0) - $versement->montant }}" disabled readonly step="1">
                        <small class="text-success fw-bold">à verser après ce paiement</small>
                    </div>

                    <!-- Date du versement -->
                    <div class="col-md-6 mb-3">
                        <label for="date_versement" class="form-label">Date du versement <span class="text-danger">*</span></label>
                        <input type="date" name="date_versement" id="date_versement" class="form-control @error('date_versement') is-invalid @enderror" 
                            value="{{ old('date_versement', $versement->date_versement->format('Y-m-d')) }}" required>
                        @error('date_versement')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                        <input type="date" name="date_fin" id="date_fin" class="form-control @error('date_fin') is-invalid @enderror" 
                            value="{{ old('date_fin', $versement->date_fin?->format('Y-m-d')) }}">
                        @error('date_fin')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <!-- Mode de versement -->
                    <div class="col-md-4 mb-3">
                        <label for="mode_versement" class="form-label">Mode de versement <span class="text-danger">*</span></label>
                        <select name="mode_versement" id="mode_versement" class="form-select @error('mode_versement') is-invalid @enderror" required>
                            <option value="">-- Sélectionner --</option>
                            <option value="virement" @selected(old('mode_versement', $versement->mode_versement) == 'virement')>Virement</option>
                            <option value="chèque" @selected(old('mode_versement', $versement->mode_versement) == 'chèque')>Chèque</option>
                            <option value="espèces" @selected(old('mode_versement', $versement->mode_versement) == 'espèces')>Espèces</option>
                            <option value="mobile_money" @selected(old('mode_versement', $versement->mode_versement) == 'mobile_money')>Mobile Money</option>
                            <option value="autre" @selected(old('mode_versement', $versement->mode_versement) == 'autre')>Autre</option>
                        </select>
                        @error('mode_versement')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Référence -->
                    <div class="col-md-4 mb-3">
                        <label for="reference" class="form-label">Référence</label>
                        <input type="text" name="reference" id="reference" class="form-control @error('reference') is-invalid @enderror" 
                            value="{{ old('reference', $versement->reference) }}" placeholder="N° virement, chèque...">
                        @error('reference')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Statut - Calculé automatiquement -->
                    <input type="hidden" name="statut" value="{{ $versement->statut }}">
                </div>

                <!-- Notes -->
                <div class="mb-3">
                    <label for="notes" class="form-label">Notes</label>
                    <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" 
                        rows="3" placeholder="Notes supplémentaires...">{{ old('notes', $versement->notes) }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Boutons -->
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Enregistrer
                    </button>
                    <a href="{{ route('backend.versements.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeTotal = document.getElementById('typeTotal');
    const typePartiel = document.getElementById('typePartiel');
    const montantInput = document.getElementById('montant');
    const montantAVerserInput = document.getElementById('montant_a_verser');
    const montantRestantInput = document.getElementById('montant_restant');
    const montantInfo = document.getElementById('montant_info');

    // Changement du type - Total
    typeTotal.addEventListener('change', function() {
        if (this.checked) {
            const montantAVerser = parseInt(montantAVerserInput.value) || 0;
            montantInput.value = montantAVerser;
            montantInput.disabled = true;
            montantInput.classList.add('bg-light');
            montantInfo.textContent = 'Montant auto-rempli (total)';
            montantInfo.classList.add('text-info');
            updateMontantRestant();
        }
    });

    // Changement du type - Partiel
    typePartiel.addEventListener('change', function() {
        if (this.checked) {
            montantInput.disabled = false;
            montantInput.classList.remove('bg-light');
            montantInfo.textContent = 'Saisissez le montant partiel';
            montantInfo.classList.remove('text-info');
            updateMontantRestant();
        }
    });

    // Changement du montant saisi
    montantInput.addEventListener('input', function() {
        updateMontantRestant();
    });

    // Fonction pour mettre à jour le montant restant
    function updateMontantRestant() {
        const montantAVerser = parseInt(montantAVerserInput.value) || 0;
        const montantSaisi = parseInt(montantInput.value) || 0;
        const montantRestant = montantAVerser - montantSaisi;
        montantRestantInput.value = montantRestant >= 0 ? montantRestant : 0;
    }

    // Initialiser le montant restant
    updateMontantRestant();
});
</script>
@endsection
