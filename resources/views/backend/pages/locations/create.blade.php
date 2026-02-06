@extends('backend.layouts.master')
@section('title')
   Créer une location
@endsection
@section('content')
    @component('backend.components.breadcrumb')
        @slot('li_1')
            <a href="{{ route('backend.locations.index') }}">Locations</a>
        @endslot
        @slot('title')
            Créer une location
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-primary bg-opacity-10">
                    <h5 class="card-title mb-0 text-primary">
                        <i class="ri-add-circle-line me-2"></i>Enregistrer une nouvelle location
                    </h5>
                    <p class="text-muted mb-0 mt-2">
                        <small><i class="ri-information-line me-1"></i>Créez une location pour un locataire. Le workflow sera géré depuis la page de détails.</small>
                    </p>
                </div>
                <div class="card-body">
                    <form action="{{ route('backend.locations.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Bien à louer <span class="text-danger">*</span></label>
                                <select name="annonce_id" id="annonce_id" class="form-select @error('annonce_id') is-invalid @enderror" required>
                                    <option value="">Sélectionnez un bien</option>
                                    @foreach($annonces as $annonce)
                                        <option value="{{ $annonce->id }}" 
                                                data-prix="{{ $annonce->prix }}"
                                                {{ old('annonce_id') == $annonce->id ? 'selected' : '' }}>
                                            {{ $annonce->titre }} - {{ $annonce->ville }} ({{ number_format($annonce->prix, 0, ',', ' ') }} FCFA/mois)
                                        </option>
                                    @endforeach
                                </select>
                                @error('annonce_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Locataire <span class="text-danger">*</span></label>
                                <select name="locataire_id" class="form-select @error('locataire_id') is-invalid @enderror" required>
                                    <option value="">Sélectionnez un locataire</option>
                                    @foreach($locataires as $locataire)
                                        <option value="{{ $locataire->id }}" {{ old('locataire_id') == $locataire->id ? 'selected' : '' }}>
                                            {{ $locataire->name }} ({{ $locataire->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('locataire_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Loyer mensuel (FCFA) <span class="text-danger">*</span></label>
                                <input type="text" id="loyer_mensuel_display" class="form-control @error('loyer_mensuel') is-invalid @enderror" 
                                       value="{{ old('loyer_mensuel') ? number_format(old('loyer_mensuel'), 0, ',', ' ') : '' }}" required placeholder="Ex: 150 000">
                                <input type="hidden" name="loyer_mensuel" id="loyer_mensuel" value="{{ old('loyer_mensuel') }}">
                                <small class="text-muted">Le loyer sera automatiquement rempli depuis le bien sélectionné</small>
                                @error('loyer_mensuel')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="ri-information-line me-2"></i>
                            <strong>Note:</strong> La location sera créée avec le statut "Demande client". 
                            Vous configurerez ensuite les détails financiers (loyer, caution, jour de paiement) dans le workflow "Configuration paiement".
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Message / Notes</label>
                            <textarea name="message" class="form-control @error('message') is-invalid @enderror" rows="4" placeholder="Notes ou message du client...">{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="text-end">
                            <a href="{{ route('backend.locations.index') }}" class="btn btn-secondary">
                                <i class="ri-arrow-left-line me-1"></i>Annuler
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="ri-save-line me-1"></i>Créer la location
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
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
    
    const loyerDisplay = document.getElementById('loyer_mensuel_display');
    const loyerInput = document.getElementById('loyer_mensuel');
    
    // Remplir automatiquement le loyer depuis l'annonce sélectionnée
    // Utiliser jQuery et l'événement Select2
    $('#annonce_id').on('select2:select', function(e) {
        const selectedData = e.params.data;
        const selectedOption = selectedData.element;
        const prix = selectedOption.getAttribute('data-prix');
        
        if (prix) {
            loyerDisplay.value = formatMontant(prix);
            loyerInput.value = prix;
        }
    });
    
    // Fallback pour le cas où Select2 n'est pas initialisé
    document.getElementById('annonce_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const prix = selectedOption.getAttribute('data-prix');
        
        if (prix) {
            loyerDisplay.value = formatMontant(prix);
            loyerInput.value = prix;
        }
    });
    
    // Formater le loyer lors de la saisie
    loyerDisplay.addEventListener('input', function(e) {
        let cursorPos = this.selectionStart;
        let oldLength = this.value.length;
        
        let formatted = formatMontant(this.value);
        this.value = formatted;
        
        loyerInput.value = getMontantNumeric(formatted);
        
        let newLength = formatted.length;
        cursorPos += (newLength - oldLength);
        this.setSelectionRange(cursorPos, cursorPos);
    });
    
    // Prévenir la double soumission et validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const loyerNumeric = getMontantNumeric(loyerDisplay.value);
        
        if (!loyerNumeric || loyerNumeric <= 0) {
            e.preventDefault();
            alert('Veuillez saisir un loyer mensuel valide.');
            return false;
        }
        
        loyerInput.value = loyerNumeric;
        
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Création...';
    });
</script>
@endsection
