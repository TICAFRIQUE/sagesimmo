@extends('backend.layouts.master')
@section('title')
   Modifier la location
@endsection
@section('content')
    @component('backend.components.breadcrumb')
        @slot('li_1')
            <a href="{{ route('backend.locations.index') }}">Locations</a>
        @endslot
        @slot('title')
            Modifier la location #{{ $location->id }}
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Modifier la location</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('backend.locations.update', $location) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Bien <span class="text-danger">*</span></label>
                                <select name="annonce_id" class="form-select @error('annonce_id') is-invalid @enderror" required>
                                    @foreach($annonces as $annonce)
                                        <option value="{{ $annonce->id }}" {{ $location->annonce_id == $annonce->id ? 'selected' : '' }}>
                                            {{ $annonce->titre }} - {{ $annonce->ville }}
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
                                    @foreach($locataires as $locataire)
                                        <option value="{{ $locataire->id }}" {{ $location->locataire_id == $locataire->id ? 'selected' : '' }}>
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
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Loyer mensuel (FCFA) <span class="text-danger">*</span></label>
                                <input type="number" name="loyer_mensuel" class="form-control @error('loyer_mensuel') is-invalid @enderror" 
                                       value="{{ old('loyer_mensuel', $location->loyer_mensuel) }}" required step="0.01">
                                @error('loyer_mensuel')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Caution (FCFA)</label>
                                <input type="number" name="caution" class="form-control @error('caution') is-invalid @enderror" 
                                       value="{{ old('caution', $location->caution) }}" step="0.01">
                                @error('caution')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Jour de paiement <span class="text-danger">*</span></label>
                                <input type="number" name="jour_paiement" class="form-control @error('jour_paiement') is-invalid @enderror" 
                                       value="{{ old('jour_paiement', $location->jour_paiement) }}" required min="1" max="31">
                                @error('jour_paiement')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Date de début <span class="text-danger">*</span></label>
                                <input type="date" name="date_debut" class="form-control @error('date_debut') is-invalid @enderror" 
                                       value="{{ old('date_debut', $location->date_debut->format('Y-m-d')) }}" required>
                                @error('date_debut')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Date de fin</label>
                                <input type="date" name="date_fin" class="form-control @error('date_fin') is-invalid @enderror" 
                                       value="{{ old('date_fin', $location->date_fin ? $location->date_fin->format('Y-m-d') : '') }}">
                                @error('date_fin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Statut <span class="text-danger">*</span></label>
                                <select name="statut" class="form-select @error('statut') is-invalid @enderror" required>
                                    <option value="actif" {{ $location->statut == 'actif' ? 'selected' : '' }}>Actif</option>
                                    <option value="terminé" {{ $location->statut == 'terminé' ? 'selected' : '' }}>Terminé</option>
                                    <option value="résilié" {{ $location->statut == 'résilié' ? 'selected' : '' }}>Résilié</option>
                                </select>
                                @error('statut')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Conditions du contrat</label>
                            <textarea name="conditions" class="form-control @error('conditions') is-invalid @enderror" rows="4">{{ old('conditions', $location->conditions) }}</textarea>
                            @error('conditions')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="text-end">
                            <a href="{{ route('backend.locations.show', $location) }}" class="btn btn-secondary">Annuler</a>
                            <button type="submit" class="btn btn-primary">Mettre à jour</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
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

    // Calculer au chargement
    window.addEventListener('DOMContentLoaded', calculerCaution);
</script>
@endsection
