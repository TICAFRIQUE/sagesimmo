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
                <div class="card-header">
                    <h5 class="card-title mb-0">Enregistrer une nouvelle location</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('backend.locations.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Bien à louer <span class="text-danger">*</span></label>
                                <select name="annonce_id" class="form-select @error('annonce_id') is-invalid @enderror" required>
                                    <option value="">Sélectionnez un bien</option>
                                    @foreach($annonces as $annonce)
                                        <option value="{{ $annonce->id }}" {{ old('annonce_id') == $annonce->id ? 'selected' : '' }}>
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
                                <label class="form-label">Point de départ du workflow <span class="text-danger">*</span></label>
                                <select name="statut_initial" class="form-select @error('statut_initial') is-invalid @enderror" required>
                                    <option value="brouillon" {{ old('statut_initial', 'brouillon') == 'brouillon' ? 'selected' : '' }}>
                                        🔵 Brouillon - Je configurerai les détails et enverrai la fiche plus tard
                                    </option>
                                    <option value="fiche_envoyee" {{ old('statut_initial') == 'fiche_envoyee' ? 'selected' : '' }}>
                                        📄 Fiche déjà envoyée - Je peux directement planifier une visite
                                    </option>
                                    <option value="demande_client" {{ old('statut_initial') == 'demande_client' ? 'selected' : '' }}>
                                        💬 Demande client - Le client a fait une demande, je dois lui envoyer la fiche
                                    </option>
                                </select>
                                <small class="text-muted">
                                    <i class="ri-information-line"></i> 
                                    <strong>Recommandé:</strong> Choisissez "Brouillon" pour une création manuelle depuis l'admin.
                                    Choisissez "Demande client" uniquement si le client a fait une demande depuis le site.
                                </small>
                                @error('statut_initial')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Loyer mensuel (FCFA) <span class="text-danger">*</span></label>
                                <input type="number" name="loyer_mensuel" id="loyer_mensuel" class="form-control @error('loyer_mensuel') is-invalid @enderror" 
                                       value="{{ old('loyer_mensuel') }}" required step="0.01">
                                @error('loyer_mensuel')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">Nombre de cautions <span class="text-danger">*</span></label>
                                <input type="number" name="nombre_cautions" id="nombre_cautions" class="form-control @error('nombre_cautions') is-invalid @enderror" 
                                       value="{{ old('nombre_cautions', 2) }}" required min="0" step="1">
                                <small class="text-muted">Multiplicateur pour la caution</small>
                                @error('nombre_cautions')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">Caution (FCFA)</label>
                                <input type="number" name="caution" id="caution" class="form-control @error('caution') is-invalid @enderror" 
                                       value="{{ old('caution') }}" step="0.01" readonly>
                                <small class="text-muted">Calculée automatiquement</small>
                                @error('caution')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">Jour de paiement <span class="text-danger">*</span></label>
                                <input type="number" name="jour_paiement" class="form-control @error('jour_paiement') is-invalid @enderror" 
                                       value="{{ old('jour_paiement', 1) }}" required min="1" max="31">
                                <small class="text-muted">Jour du mois (1-31)</small>
                                @error('jour_paiement')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Date de début <span class="text-danger">*</span></label>
                                <input type="date" name="date_debut" class="form-control @error('date_debut') is-invalid @enderror" 
                                       value="{{ old('date_debut', date('Y-m-d')) }}" required>
                                @error('date_debut')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Date de fin</label>
                                <input type="date" name="date_fin" class="form-control @error('date_fin') is-invalid @enderror" 
                                       value="{{ old('date_fin') }}">
                                <small class="text-muted">Laissez vide pour une location sans date de fin</small>
                                @error('date_fin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Conditions du contrat</label>
                            <textarea name="conditions" class="form-control @error('conditions') is-invalid @enderror" rows="4">{{ old('conditions') }}</textarea>
                            @error('conditions')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="text-end">
                            <a href="{{ route('backend.locations.index') }}" class="btn btn-secondary">Annuler</a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
    // Données des annonces avec leurs prix
    const annonces = @json($annonces->map(function($annonce) {
        return ['id' => $annonce->id, 'prix' => $annonce->prix];
    }));

    // Remplir automatiquement le loyer mensuel quand on sélectionne un bien
    document.querySelector('select[name="annonce_id"]').addEventListener('change', function() {
        const annonceId = this.value;
        const annonce = annonces.find(a => a.id == annonceId);
        
        if (annonce) {
            document.getElementById('loyer_mensuel').value = annonce.prix;
            calculerCaution();
        }
    });

    // Calculer la caution automatiquement
    function calculerCaution() {
        const loyer = parseFloat(document.getElementById('loyer_mensuel').value) || 0;
        const nombreCautions = parseFloat(document.getElementById('nombre_cautions').value) || 0;
        const caution = loyer * nombreCautions;
        
        document.getElementById('caution').value = caution;
    }

    // Écouter les changements sur le loyer et le nombre de cautions
    document.getElementById('loyer_mensuel').addEventListener('input', calculerCaution);
    document.getElementById('nombre_cautions').addEventListener('input', calculerCaution);

    // Calculer au chargement si des valeurs existent
    window.addEventListener('DOMContentLoaded', calculerCaution);

    // Prévenir la double soumission
    const form = document.querySelector('form');
    const submitBtn = document.getElementById('submitBtn');
    let isSubmitting = false;

    form.addEventListener('submit', function(e) {
        if (isSubmitting) {
            e.preventDefault();
            return false;
        }
        isSubmitting = true;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Enregistrement...';
    });
</script>
@endsection
