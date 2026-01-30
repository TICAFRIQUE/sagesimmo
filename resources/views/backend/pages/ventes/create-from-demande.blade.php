@extends('backend.layouts.master')
@section('title')
   Créer une vente depuis la demande
@endsection
@section('content')
    @component('backend.components.breadcrumb')
        @slot('li_1')
            <a href="{{ route('backend.demandes.show', $demande->id) }}">Demande #{{ $demande->id }}</a>
        @endslot
        @slot('title')
            Créer le suivi de vente
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0 text-white">Créer le suivi de vente</h5>
                </div>
                <div class="card-body">
                    <!-- Informations de la demande -->
                    <div class="alert alert-info mb-4">
                        <h6><i class="ri-information-line me-2"></i>Informations de la demande</h6>
                        <ul class="mb-0">
                            <li><strong>Bien:</strong> {{ $demande->annonce->titre }}</li>
                            <li><strong>Client:</strong> {{ $demande->user->name }} ({{ $demande->user->email }})</li>
                            <li><strong>Prix du bien:</strong> {{ number_format($demande->annonce->prix, 0, ',', ' ') }} FCFA</li>
                        </ul>
                    </div>

                    <form action="{{ route('backend.ventes.store-from-demande', $demande->id) }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Prix de vente (FCFA) <span class="text-danger">*</span></label>
                                <input type="number" name="prix_vente" id="prix_vente" class="form-control @error('prix_vente') is-invalid @enderror" 
                                       value="{{ old('prix_vente', $demande->annonce->prix) }}" required step="0.01">
                                @error('prix_vente')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Date de vente <span class="text-danger">*</span></label>
                                <input type="date" name="date_vente" class="form-control @error('date_vente') is-invalid @enderror" 
                                       value="{{ old('date_vente', date('Y-m-d')) }}" required>
                                @error('date_vente')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Commission agence</label>
                                <input type="number" name="commission_agence" id="commission_agence" class="form-control @error('commission_agence') is-invalid @enderror" 
                                       value="{{ old('commission_agence', $demande->annonce->commission) }}" step="0.01" min="0">
                                <small class="text-muted" id="commission-info"></small>
                                @error('commission_agence')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Type commission</label>
                                <select name="type_commission" id="type_commission" class="form-select @error('type_commission') is-invalid @enderror">
                                    <option value="pourcentage" {{ old('type_commission', $demande->annonce->type_commission) == 'pourcentage' ? 'selected' : '' }}>Pourcentage (%)</option>
                                    <option value="fixe" {{ old('type_commission', $demande->annonce->type_commission) == 'fixe' ? 'selected' : '' }}>Montant fixe</option>
                                </select>
                                @error('type_commission')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Date de signature du contrat</label>
                            <input type="date" name="date_signature" class="form-control @error('date_signature') is-invalid @enderror" 
                                   value="{{ old('date_signature', $demande->date_signature_contrat ? $demande->date_signature_contrat->format('Y-m-d') : '') }}">
                            @error('date_signature')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="4">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="text-end">
                            <a href="{{ route('backend.demandes.show', $demande->id) }}" class="btn btn-secondary">Annuler</a>
                            <button type="submit" class="btn btn-success">
                                <i class="ri-shopping-bag-line me-2"></i>Créer le suivi de vente
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Informations du bien -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Bien concerné</h5>
                </div>
                <div class="card-body">
                    @if($demande->annonce->getFirstMediaUrl('images'))
                        <img src="{{ $demande->annonce->getFirstMediaUrl('images') }}" class="img-fluid rounded mb-3" alt="Bien">
                    @endif
                    <h6>{{ $demande->annonce->titre }}</h6>
                    <p class="text-muted mb-2">{{ $demande->annonce->ville }}, {{ $demande->annonce->quartier }}</p>
                    <p class="mb-0"><strong>Prix:</strong> {{ number_format($demande->annonce->prix, 0, ',', ' ') }} FCFA</p>
                </div>
            </div>

            <!-- Informations du client -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Client acheteur</h5>
                </div>
                <div class="card-body">
                    <p><strong>Nom:</strong> {{ $demande->user->name }}</p>
                    <p><strong>Email:</strong> {{ $demande->user->email }}</p>
                    <p class="mb-0"><strong>Téléphone:</strong> {{ $demande->user->phone ?? 'Non renseigné' }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
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

    // Calculer au chargement
    window.addEventListener('DOMContentLoaded', calculerCommission);

    // Prévenir la double soumission
    const form = document.querySelector('form');
    const submitBtn = form.querySelector('button[type="submit"]');
    let isSubmitting = false;

    form.addEventListener('submit', function(e) {
        if (isSubmitting) {
            e.preventDefault();
            return false;
        }
        isSubmitting = true;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Création en cours...';
    });
</script>
@endsection
