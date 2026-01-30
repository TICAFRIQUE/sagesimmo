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
                <div class="card-header">
                    <h5 class="card-title mb-0">Enregistrer une nouvelle vente</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('backend.ventes.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Bien à vendre <span class="text-danger">*</span></label>
                                <select name="annonce_id" class="form-select @error('annonce_id') is-invalid @enderror" required>
                                    <option value="">Sélectionnez un bien</option>
                                    @foreach($annonces as $annonce)
                                        <option value="{{ $annonce->id }}" {{ old('annonce_id') == $annonce->id ? 'selected' : '' }}>
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
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Prix de vente (FCFA) <span class="text-danger">*</span></label>
                                <input type="number" name="prix_vente" id="prix_vente" class="form-control @error('prix_vente') is-invalid @enderror" 
                                       value="{{ old('prix_vente') }}" required step="0.01">
                                @error('prix_vente')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Commission agence</label>
                                <input type="number" name="commission_agence" id="commission_agence" class="form-control @error('commission_agence') is-invalid @enderror" 
                                       value="{{ old('commission_agence') }}" step="0.01" min="0">
                                <small class="text-muted" id="commission-info"></small>
                                @error('commission_agence')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Type commission</label>
                                <select name="type_commission" id="type_commission" class="form-select @error('type_commission') is-invalid @enderror">
                                    <option value="pourcentage" {{ old('type_commission', 'pourcentage') == 'pourcentage' ? 'selected' : '' }}>Pourcentage (%)</option>
                                    <option value="fixe" {{ old('type_commission') == 'fixe' ? 'selected' : '' }}>Montant fixe</option>
                                </select>
                                @error('type_commission')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Date de vente <span class="text-danger">*</span></label>
                                <input type="date" name="date_vente" class="form-control @error('date_vente') is-invalid @enderror" 
                                       value="{{ old('date_vente', date('Y-m-d')) }}" required>
                                @error('date_vente')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Date de signature</label>
                                <input type="date" name="date_signature" class="form-control @error('date_signature') is-invalid @enderror" 
                                       value="{{ old('date_signature') }}">
                                @error('date_signature')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Statut <span class="text-danger">*</span></label>
                                <select name="statut" class="form-select @error('statut') is-invalid @enderror" required>
                                    <option value="en_cours" {{ old('statut') == 'en_cours' ? 'selected' : '' }}>En cours</option>
                                    <option value="completé" {{ old('statut') == 'completé' ? 'selected' : '' }}>Complété</option>
                                    <option value="annulé" {{ old('statut') == 'annulé' ? 'selected' : '' }}>Annulé</option>
                                </select>
                                @error('statut')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="4">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="text-end">
                            <a href="{{ route('backend.ventes.index') }}" class="btn btn-secondary">Annuler</a>
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
    // Données des annonces avec leurs prix et commissions
    @php
        $annoncesData = $annonces->map(function($annonce) {
            return [
                'id' => $annonce->id, 
                'prix' => $annonce->prix,
                'commission' => $annonce->commission,
                'type_commission' => $annonce->type_commission
            ];
        });
    @endphp
    const annonces = @json($annoncesData);

    // Remplir automatiquement le prix de vente et la commission quand on sélectionne un bien
    document.querySelector('select[name="annonce_id"]').addEventListener('change', function() {
        const annonceId = this.value;
        const annonce = annonces.find(a => a.id == annonceId);
        
        if (annonce) {
            document.getElementById('prix_vente').value = annonce.prix;
            
            if (annonce.commission) {
                document.getElementById('commission_agence').value = annonce.commission;
                document.getElementById('type_commission').value = annonce.type_commission || 'pourcentage';
                calculerCommission();
            }
        }
    });

    // Calculer et afficher la commission
    function calculerCommission() {
        const prix = parseFloat(document.getElementById('prix_vente').value) || 0;
        const commission = parseFloat(document.getElementById('commission_agence').value) || 0;
        const type = document.getElementById('type_commission').value;
        const infoDiv = document.getElementById('commission-info');
        
        if (commission > 0) {
            if (type === 'pourcentage') {
                const montant = (prix * commission) / 100;
                infoDiv.textContent = `${commission}% = ${new Intl.NumberFormat('fr-FR').format(montant)} FCFA`;
            } else {
                infoDiv.textContent = `Montant fixe: ${new Intl.NumberFormat('fr-FR').format(commission)} FCFA`;
            }
        } else {
            infoDiv.textContent = '';
        }
    }

    // Écouter les changements
    document.getElementById('prix_vente').addEventListener('input', calculerCommission);
    document.getElementById('commission_agence').addEventListener('input', calculerCommission);
    document.getElementById('type_commission').addEventListener('change', calculerCommission);

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
