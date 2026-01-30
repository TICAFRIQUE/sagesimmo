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
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Prix de vente (FCFA) <span class="text-danger">*</span></label>
                                <input type="number" name="prix_vente" id="prix_vente" class="form-control @error('prix_vente') is-invalid @enderror" 
                                       value="{{ old('prix_vente', $vente->prix_vente) }}" required step="0.01">
                                @error('prix_vente')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Commission agence</label>
                                <input type="number" name="commission_agence" id="commission_agence" class="form-control @error('commission_agence') is-invalid @enderror" 
                                       value="{{ old('commission_agence', $vente->commission_agence) }}" step="0.01" min="0">
                                <small class="text-muted" id="commission-info"></small>
                                @error('commission_agence')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Type commission</label>
                                <select name="type_commission" id="type_commission" class="form-select @error('type_commission') is-invalid @enderror">
                                    <option value="pourcentage" {{ old('type_commission', $vente->type_commission) == 'pourcentage' ? 'selected' : '' }}>Pourcentage (%)</option>
                                    <option value="fixe" {{ old('type_commission', $vente->type_commission) == 'fixe' ? 'selected' : '' }}>Montant fixe</option>
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
                                       value="{{ old('date_vente', $vente->date_vente->format('Y-m-d')) }}" required>
                                @error('date_vente')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Date de signature</label>
                                <input type="date" name="date_signature" class="form-control @error('date_signature') is-invalid @enderror" 
                                       value="{{ old('date_signature', $vente->date_signature ? $vente->date_signature->format('Y-m-d') : '') }}">
                                @error('date_signature')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Statut <span class="text-danger">*</span></label>
                                <select name="statut" class="form-select @error('statut') is-invalid @enderror" required>
                                    <option value="en_cours" {{ $vente->statut == 'en_cours' ? 'selected' : '' }}>En cours</option>
                                    <option value="completé" {{ $vente->statut == 'completé' ? 'selected' : '' }}>Complété</option>
                                    <option value="annulé" {{ $vente->statut == 'annulé' ? 'selected' : '' }}>Annulé</option>
                                </select>
                                @error('statut')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="4">{{ old('notes', $vente->notes) }}</textarea>
                            @error('notes')
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
</script>
@endsection
