@extends('backend.layouts.master')

@section('title')
   Éditer la Charge
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    {{-- <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-edit"></i> Éditer la Charge
            </h1>
        </div>
    </div> --}}

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Erreurs :</strong>
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Formulaire -->
    <div class="card">
        <div class="card-body">
            <form action="{{ route('backend.charges.update', $charge) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="annonce_id" class="form-label">
                                <strong>Bien immobilier</strong> <span class="text-danger">*</span>
                            </label>
                            <select name="annonce_id" id="annonce_id" class="form-control @error('annonce_id') is-invalid @enderror"
                                required>
                                <option value="">-- Sélectionner un bien --</option>
                                @foreach($biens as $bien)
                                    <option value="{{ $bien->id }}" @selected(old('annonce_id', $charge->annonce_id) == $bien->id)>
                                        {{ $bien->titre }} * {{ $bien->reference }}
                                    </option>
                                @endforeach
                            </select>
                            @error('annonce_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="type_charge" class="form-label">
                                <strong>Type de charge</strong> <span class="text-danger">*</span>
                            </label>
                            <select name="type_charge" id="type_charge" class="form-select @error('type_charge') is-invalid @enderror"
                                required>
                                <option value="">-- Sélectionner --</option>
                                <option value="maintenance" @selected(old('type_charge', $charge->type_charge) === 'maintenance')>
                                    <i class="fas fa-wrench"></i> Maintenance
                                </option>
                                <option value="reparation" @selected(old('type_charge', $charge->type_charge) === 'reparation')>
                                    <i class="fas fa-hammer"></i> Réparation
                                </option>
                                <option value="taxe" @selected(old('type_charge', $charge->type_charge) === 'taxe')>
                                    <i class="fas fa-percent"></i> Taxe
                                </option>
                                <option value="autre" @selected(old('type_charge', $charge->type_charge) === 'autre')>
                                    <i class="fas fa-ellipsis-h"></i> Autre
                                </option>
                            </select>
                            @error('type_charge')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="montant" class="form-label">
                                <strong>Montant (FCFA)</strong> <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="number" name="montant" id="montant" class="form-control @error('montant') is-invalid @enderror"
                                    step="0.01" min="0" value="{{ old('montant', $charge->montant) }}" required>
                                <span class="input-group-text">F</span>
                                @error('montant')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="date_charge" class="form-label">
                                <strong>Date de la charge</strong> <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="date_charge" id="date_charge" class="form-control @error('date_charge') is-invalid @enderror"
                                value="{{ old('date_charge', $charge->date_charge->format('Y-m-d')) }}" required>
                            @error('date_charge')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="reference" class="form-label">
                                <strong>Référence</strong> <span class="text-muted">(Facture, numéro, etc.)</span>
                            </label>
                            <input type="text" name="reference" id="reference" class="form-control"
                                value="{{ old('reference', $charge->reference) }}" placeholder="Ex: FAC-2025-001">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="description" class="form-label">
                                <strong>Description</strong>
                            </label>
                            <input type="text" name="description" id="description" class="form-control"
                                value="{{ old('description', $charge->description) }}" placeholder="Détails de la charge">
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="notes" class="form-label">
                        <strong>Notes supplémentaires</strong>
                    </label>
                    <textarea name="notes" id="notes" class="form-control" rows="4"
                        placeholder="Remarques additionnelles...">{{ old('notes', $charge->notes) }}</textarea>
                </div>

                <!-- Boutons -->
                <div class="mb-0">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Mettre à jour
                    </button>
                    <a href="{{ route('backend.charges.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Informations -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card bg-light">
                <div class="card-body">
                    <h6 class="card-title">
                        <i class="fas fa-info-circle"></i> Informations
                    </h6>
                    <p class="mb-0">
                        <strong>Créé :</strong> {{ $charge->created_at->format('d/m/Y à H:i') }}<br>
                        <strong>Modifié :</strong> {{ $charge->updated_at->format('d/m/Y à H:i') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
