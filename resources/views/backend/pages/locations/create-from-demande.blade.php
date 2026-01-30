@extends('backend.layouts.master')
@section('title')
   Créer une location depuis la demande
@endsection
@section('content')
    @component('backend.components.breadcrumb')
        @slot('li_1')
            <a href="{{ route('backend.demandes.show', $demande->id) }}">Demande #{{ $demande->id }}</a>
        @endslot
        @slot('title')
            Créer le suivi de location
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0 text-white">Créer le suivi de location</h5>
                </div>
                <div class="card-body">
                    <!-- Informations de la demande -->
                    <div class="alert alert-info mb-4">
                        <h6><i class="ri-information-line me-2"></i>Informations de la demande</h6>
                        <ul class="mb-0">
                            <li><strong>Bien:</strong> {{ $demande->annonce->titre }}</li>
                            <li><strong>Locataire:</strong> {{ $demande->user->name }} ({{ $demande->user->email }})</li>
                            <li><strong>Loyer du bien:</strong> {{ number_format($demande->annonce->prix, 0, ',', ' ') }} FCFA/mois</li>
                            @if($demande->montant_caution)
                                <li><strong>Caution payée:</strong> {{ number_format($demande->montant_caution, 0, ',', ' ') }} FCFA</li>
                            @endif
                        </ul>
                    </div>

                    <form action="{{ route('backend.locations.store-from-demande', $demande->id) }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Loyer mensuel (FCFA) <span class="text-danger">*</span></label>
                                <input type="number" name="loyer_mensuel" class="form-control @error('loyer_mensuel') is-invalid @enderror" 
                                       value="{{ old('loyer_mensuel', $demande->annonce->prix) }}" required step="0.01">
                                @error('loyer_mensuel')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Caution (FCFA)</label>
                                <input type="number" name="caution" class="form-control @error('caution') is-invalid @enderror" 
                                       value="{{ old('caution', $demande->montant_caution ?? 0) }}" step="0.01">
                                @error('caution')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
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
                                <small class="text-muted">Laissez vide pour une location indéterminée</small>
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

                        <div class="alert alert-warning">
                            <i class="ri-information-line me-2"></i>
                            <strong>Note:</strong> Les échéances de loyer seront générées automatiquement pour les 12 prochains mois.
                        </div>

                        <div class="text-end">
                            <a href="{{ route('backend.demandes.show', $demande->id) }}" class="btn btn-secondary">Annuler</a>
                            <button type="submit" class="btn btn-success">
                                <i class="ri-key-line me-2"></i>Créer le suivi de location
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
                    <p class="mb-0"><strong>Loyer:</strong> {{ number_format($demande->annonce->prix, 0, ',', ' ') }} FCFA/mois</p>
                </div>
            </div>

            <!-- Informations du locataire -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Locataire</h5>
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
