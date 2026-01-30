@extends('backend.layouts.master')
@section('title')
   Modifier l'équipement
@endsection
@section('content')
    @component('backend.components.breadcrumb')
        @slot('li_1')
            <a href="{{ route('backend.equipements.index') }}">Équipements</a>
        @endslot
        @slot('title')
            Modifier l'équipement
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <form action="{{ route('backend.equipements.update', $equipement->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Informations de l'équipement</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="nom" class="form-label">Nom <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nom') is-invalid @enderror" 
                                   id="nom" name="nom" value="{{ old('nom', $equipement->nom) }}" required>
                            @error('nom')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="icone" class="form-label">Classe d'icône</label>
                            <input type="text" class="form-control @error('icone') is-invalid @enderror" 
                                   id="icone" name="icone" value="{{ old('icone', $equipement->icone) }}" 
                                   placeholder="Ex: ri-wifi-line">
                            @error('icone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                Icônes RemixIcon : <a href="https://remixicon.com/" target="_blank">https://remixicon.com/</a>
                            </small>
                            <div id="icon-preview" class="mt-2"></div>
                        </div>

                        <div class="mb-3">
                            <label for="ordre" class="form-label">Ordre d'affichage</label>
                            <input type="number" class="form-control @error('ordre') is-invalid @enderror" 
                                   id="ordre" name="ordre" value="{{ old('ordre', $equipement->ordre) }}" min="0">
                            @error('ordre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Plus le nombre est petit, plus il apparaîtra en premier</small>
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="actif" 
                                   name="actif" {{ old('actif', $equipement->actif) ? 'checked' : '' }}>
                            <label class="form-check-label" for="actif">
                                Équipement actif
                            </label>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('backend.equipements.index') }}" class="btn btn-light">Annuler</a>
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

@section('script')
    <script>
        $(document).ready(function() {
            // Prévisualisation de l'icône
            $('#icone').on('input', function() {
                const iconClass = $(this).val();
                if (iconClass) {
                    $('#icon-preview').html(`
                        <div class="alert alert-info">
                            <i class="${iconClass} fs-3 me-2"></i>
                            <span>Aperçu de l'icône</span>
                        </div>
                    `);
                } else {
                    $('#icon-preview').empty();
                }
            });

            // Afficher l'aperçu initial si une valeur existe
            if ($('#icone').val()) {
                $('#icone').trigger('input');
            }
        });
    </script>
@endsection
