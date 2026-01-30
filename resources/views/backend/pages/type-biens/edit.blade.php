@extends('backend.layouts.master')
@section('title')
   Modifier le type de bien
@endsection
@section('content')
    @component('backend.components.breadcrumb')
        @slot('li_1')
            <a href="{{ route('backend.type-biens.index') }}">Types de biens</a>
        @endslot
        @slot('title')
            Modifier le type de bien
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <form action="{{ route('backend.type-biens.update', $typeBien->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Informations du type de bien</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="nom" class="form-label">Nom <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nom') is-invalid @enderror" 
                                   id="nom" name="nom" value="{{ old('nom', $typeBien->nom) }}" required>
                            @error('nom')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="ordre" class="form-label">Ordre d'affichage</label>
                            <input type="number" class="form-control @error('ordre') is-invalid @enderror" 
                                   id="ordre" name="ordre" value="{{ old('ordre', $typeBien->ordre) }}" min="0">
                            @error('ordre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Plus le nombre est petit, plus il apparaîtra en premier</small>
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="actif" 
                                   name="actif" {{ old('actif', $typeBien->actif) ? 'checked' : '' }}>
                            <label class="form-check-label" for="actif">
                                Type de bien actif
                            </label>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('backend.type-biens.index') }}" class="btn btn-light">Annuler</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="ri-save-line me-1"></i> Mettre à jour
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
