@extends('backend.layouts.master')
@section('title')
   Modifier la vente
@endsection
@section('content')
    @component('backend.components.breadcrumb')
        @slot('li_1')
            <a href="{{ route('backend.ventes.index') }}">Ventes</a>
        @endslot
        @slot('title')
            Modifier la vente #{{ $vente->id }}
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Modifier la vente</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('backend.ventes.update', $vente) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Bien <span class="text-danger">*</span></label>
                                <select name="annonce_id" class="form-select @error('annonce_id') is-invalid @enderror" required>
                                    @foreach($annonces as $annonce)
                                        <option value="{{ $annonce->id }}" {{ $vente->annonce_id == $annonce->id ? 'selected' : '' }}>
                                            {{ $annonce->titre }} - {{ $annonce->ville }}
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
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}" {{ $vente->client_id == $client->id ? 'selected' : '' }}>
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
                                       value="{{ old('prix_vente') ? number_format(old('prix_vente'), 0, ',', ' ') : number_format($vente->prix_vente, 0, ',', ' ') }}" required placeholder="Ex: 5 000 000">
                                <input type="hidden" name="prix_vente" id="prix_vente" value="{{ old('prix_vente', $vente->prix_vente) }}">
                                @error('prix_vente')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="ri-information-line me-2"></i>
                            <strong>Note:</strong> La commission est configurée lors du premier paiement. 
                            Le statut est géré automatiquement selon le workflow (demande, fiche, visite, paiement).
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Message / Notes</label>
                            <textarea name="message" class="form-control @error('message') is-invalid @enderror" rows="4" placeholder="Notes ou message du client...">{{ old('message', $vente->message) }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="text-end">
                            <a href="{{ route('backend.ventes.show', $vente) }}" class="btn btn-secondary">Annuler</a>
                            <button type="submit" class="btn btn-primary">Mettre à jour</button>
                        </div>
                    </form>

                    <!-- Formulaire de suppression -->
                    <div class="mt-4 pt-4 border-top">
                        <form action="{{ route('backend.ventes.destroy', $vente) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette vente ? Cette action est irréversible.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="ri-delete-bin-line me-1"></i>Supprimer la vente
                            </button>
                        </form>
                    </div>
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
    
    const prixVenteDisplay = document.getElementById('prix_vente_display');
    const prixVenteInput = document.getElementById('prix_vente');
    
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
