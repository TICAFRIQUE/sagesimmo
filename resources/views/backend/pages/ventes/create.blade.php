@extends('backend.layouts.master')
@section('title')
   Créer une vente
@endsection
@section('content')
    @component('backend.components.breadcrumb')
        @slot('li_1')
            <a href="{{ route('backend.ventes.index') }}">Ventes</a>
        @endslot
        @slot('title')
            Créer une vente
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-primary bg-opacity-10">
                    <h5 class="card-title mb-0 text-primary">
                        <i class="ri-add-circle-line me-2"></i>Enregistrer une nouvelle vente
                    </h5>
                    <p class="text-muted mb-0 mt-2">
                        <small><i class="ri-information-line me-1"></i>Créez une vente pour un client. Le workflow sera géré depuis la page de détails.</small>
                    </p>
                </div>
                <div class="card-body">
                    <form action="{{ route('backend.ventes.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Bien à vendre <span class="text-danger">*</span></label>
                                <select name="annonce_id" id="annonce_id" class="form-select @error('annonce_id') is-invalid @enderror" required>
                                    <option value="">Sélectionnez un bien</option>
                                    @foreach($annonces as $annonce)
                                        <option value="{{ $annonce->id }}" 
                                                data-prix="{{ $annonce->prix }}"
                                                {{ old('annonce_id') == $annonce->id ? 'selected' : '' }}>
                                            {{ $annonce->titre }} - {{ $annonce->ville }} ({{ number_format($annonce->prix, 0, ',', ' ') }} FCFA)
                                        </option>
                                    @endforeach
                                </select>
                                @error('annonce_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Client <span class="text-danger">*</span></label>
                                <select name="client_id" class="form-select @error('client_id') is-invalid @enderror" required>
                                    <option value="">Sélectionnez un client</option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                            {{ $client->name }} ({{ $client->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('client_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Prix de vente (FCFA) <span class="text-danger">*</span></label>
                                <input type="text" id="prix_vente_display" class="form-control @error('prix_vente') is-invalid @enderror" 
                                       value="{{ old('prix_vente') ? number_format(old('prix_vente'), 0, ',', ' ') : '' }}" required placeholder="Ex: 5 000 000">
                                <input type="hidden" name="prix_vente" id="prix_vente" value="{{ old('prix_vente') }}">
                                <small class="text-muted">Le prix sera automatiquement rempli depuis le bien sélectionné</small>
                                @error('prix_vente')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Message du client (optionnel)</label>
                                <textarea name="message_client" class="form-control @error('message_client') is-invalid @enderror" 
                                          rows="3" placeholder="Message ou demande spécifique du client...">{{ old('message_client') }}</textarea>
                                @error('message_client')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div> --}}

                        {{-- <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Notes internes (optionnel)</label>
                                <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" 
                                          rows="3" placeholder="Notes internes pour l'équipe...">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div> --}}

                        <div class="alert alert-info">
                            <i class="ri-information-line me-2"></i>
                            <strong>Note:</strong> La vente sera créée avec le statut "Demande client". 
                            Vous pourrez ensuite gérer le workflow complet (envoi fiche, visite, paiement) depuis la page de détails.
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Message / Notes</label>
                            <textarea name="message" class="form-control @error('message') is-invalid @enderror" rows="4" placeholder="Notes ou message du client...">{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="text-end">
                            <a href="{{ route('backend.ventes.index') }}" class="btn btn-secondary">
                                <i class="ri-arrow-left-line me-1"></i>Annuler
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="ri-save-line me-1"></i>Créer la vente
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
        let numStr = value.replace(/[^\d.,]/g, '');
        numStr = numStr.replace(',', '.');
        let parts = numStr.split('.');
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
        return parts.join('.');
    }
    
    // Fonction pour obtenir la valeur numérique
    function getMontantNumeric(value) {
        return parseFloat(value.replace(/\s/g, '').replace(',', '.')) || 0;
    }
    
    // Remplir automatiquement le prix depuis l'annonce sélectionnée
    const prixVenteDisplay = document.getElementById('prix_vente_display');
    const prixVenteInput = document.getElementById('prix_vente');
    
    // Utiliser jQuery et l'événement Select2
    $('#annonce_id').on('select2:select', function(e) {
        const selectedData = e.params.data;
        const selectedOption = selectedData.element;
        const prix = selectedOption.getAttribute('data-prix');
        
        if (prix) {
            prixVenteDisplay.value = formatMontant(prix);
            prixVenteInput.value = prix;
        }
    });
    
    // Fallback pour le cas où Select2 n'est pas initialisé
    document.getElementById('annonce_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const prix = selectedOption.getAttribute('data-prix');
        
        if (prix) {
            prixVenteDisplay.value = formatMontant(prix);
            prixVenteInput.value = prix;
        }
    });
    
    // Formater le prix lors de la saisie
    prixVenteDisplay.addEventListener('input', function(e) {
        let cursorPos = this.selectionStart;
        let oldLength = this.value.length;
        
        let formatted = formatMontant(this.value);
        this.value = formatted;
        
        // Mettre à jour le champ hidden avec la valeur numérique
        prixVenteInput.value = getMontantNumeric(formatted);
        
        let newLength = formatted.length;
        cursorPos += (newLength - oldLength);
        this.setSelectionRange(cursorPos, cursorPos);
    });
    
    // Validation avant soumission
    document.querySelector('form').addEventListener('submit', function(e) {
        const prixNumeric = getMontantNumeric(prixVenteDisplay.value);
        
        if (!prixNumeric || prixNumeric <= 0) {
            e.preventDefault();
            alert('Veuillez saisir un prix de vente valide.');
            return false;
        }
        
        // S'assurer que le champ hidden a la bonne valeur
        prixVenteInput.value = prixNumeric;
    });
</script>
@endsection
