@extends('backend.layouts.master')
@section('title')
    Créer une annonce
@endsection
@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .preview-images {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }

        .preview-item {
            position: relative;
            display: inline-block;
        }

        .preview-images img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #e2e8f0;
        }

        .btn-remove-preview {
            position: absolute;
            top: 5px;
            right: 5px;
            width: 25px;
            height: 25px;
            border-radius: 50%;
            background: #ef4444;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 18px;
            line-height: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
        }

        .btn-remove-preview:hover {
            background: #dc2626;
        }
    </style>
@endsection
@section('content')
    @component('backend.components.breadcrumb')
        @slot('li_1')
            <a href="{{ route('backend.annonces.index') }}">Annonces</a>
        @endslot
        @slot('title')
            Créer une annonce
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <form class="needs-validation" action="{{ route('backend.annonces.store') }}" method="POST" enctype="multipart/form-data" novalidate>
                @csrf

                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Informations générales</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="titre" class="form-label">Titre de l'annonce <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('titre') is-invalid @enderror"
                                        id="titre" name="titre" value="{{ old('titre') }}" required>
                                    @error('titre')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="prix" class="form-label">Prix (FCFA) <span
                                            class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('prix') is-invalid @enderror"
                                        id="prix" name="prix" value="{{ old('prix') }}" step="0.01" required>
                                    @error('prix')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="type_transaction" class="form-label">Type de transaction <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('type_transaction') is-invalid @enderror"
                                        id="type_transaction" name="type_transaction" required>
                                        <option value="">Sélectionner...</option>
                                        <option value="vente" {{ old('type_transaction') == 'vente' ? 'selected' : '' }}>
                                            Vente</option>
                                        <option value="location"
                                            {{ old('type_transaction') == 'location' ? 'selected' : '' }}>Location</option>
                                    </select>
                                    @error('type_transaction')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="type_bien_id" class="form-label">Type de bien <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('type_bien_id') is-invalid @enderror"
                                        id="type_bien_id" name="type_bien_id" required>
                                        <option value="">Sélectionner...</option>
                                        @foreach ($typeBiens as $typeBien)
                                            <option value="{{ $typeBien->id }}"
                                                {{ old('type_bien_id') == $typeBien->id ? 'selected' : '' }}>
                                                {{ $typeBien->nom }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('type_bien_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="proprietaire_id" class="form-label">Propriétaire du bien <span
                                    class="text-danger">*</span></label>
                            <select class="form-select @error('proprietaire_id') is-invalid @enderror"
                                id="proprietaire_id" name="proprietaire_id" required>
                                <option value="">Sélectionner le propriétaire...</option>
                                @foreach ($proprietaires as $proprietaire)
                                    <option value="{{ $proprietaire->id }}"
                                        {{ old('proprietaire_id') == $proprietaire->id ? 'selected' : '' }}>
                                        {{ $proprietaire->name }} - {{ $proprietaire->email }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">La personne qui possède réellement ce bien immobilier</small>
                            @error('proprietaire_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description <span
                                    class="text-danger">*</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                rows="5" required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Caractéristiques</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="surface" class="form-label">Surface (m²)</label>
                                    <input type="number" class="form-control" id="surface" name="surface"
                                        value="{{ old('surface') }}" step="0.01">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="nombre_pieces" class="form-label">Nombre de pièces</label>
                                    <input type="number" class="form-control" id="nombre_pieces" name="nombre_pieces"
                                        value="{{ old('nombre_pieces') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="nombre_chambres" class="form-label">Nombre de chambres</label>
                                    <input type="number" class="form-control" id="nombre_chambres" name="nombre_chambres"
                                        value="{{ old('nombre_chambres') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="nombre_salles_bain" class="form-label">Salles de bain</label>
                                    <input type="number" class="form-control" id="nombre_salles_bain"
                                        name="nombre_salles_bain" value="{{ old('nombre_salles_bain') }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="etage" class="form-label">Étage</label>
                                    <input type="number" class="form-control" id="etage" name="etage"
                                        value="{{ old('etage') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="annee_construction" class="form-label">Année de construction</label>
                                    <input type="number" class="form-control" id="annee_construction"
                                        name="annee_construction" value="{{ old('annee_construction') }}" min="1800"
                                        max="{{ date('Y') + 5 }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <label class="form-label">Équipements</label>
                                <div class="row">
                                    @foreach ($equipements as $equipement)
                                        <div class="col-md-3">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox"
                                                    id="equipement_{{ $equipement->id }}" name="equipements[]"
                                                    value="{{ $equipement->id }}"
                                                    {{ in_array($equipement->id, old('equipements', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="equipement_{{ $equipement->id }}">
                                                    @if ($equipement->icone)
                                                        <i class="{{ $equipement->icone }}"></i>
                                                    @endif
                                                    {{ $equipement->nom }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="my-4">
                            <label for="caracteristiques_supplementaires" class="form-label">Caractéristiques
                                supplémentaires</label>
                            <textarea class="form-control" id="caracteristiques_supplementaires" name="caracteristiques_supplementaires"
                                rows="3">{{ old('caracteristiques_supplementaires') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Localisation</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="ville" class="form-label">Ville <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('ville') is-invalid @enderror" id="ville"
                                        name="ville" required>
                                        <option value="">Sélectionner une ville...</option>
                                        @foreach (array_keys($villes) as $ville)
                                            <option value="{{ $ville }}"
                                                {{ old('ville') == $ville ? 'selected' : '' }}>
                                                {{ $ville }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('ville')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="commune" class="form-label">Commune</label>
                                    <select class="form-select" id="commune" name="commune"
                                        data-current-commune="{{ old('commune') }}" disabled>
                                        <option value="">Sélectionner une commune...</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="quartier" class="form-label">Quartier</label>
                                    <input type="text" class="form-control" id="quartier" name="quartier"
                                        value="{{ old('quartier') }}">
                                </div>
                            </div>
                        </div>


                           <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="adresse" class="form-label">Adresse complète avec precisions  <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('adresse') is-invalid @enderror"
                                        id="adresse" name="adresse" value="{{ old('adresse') }}" required>
                                    @error('adresse')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                      
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="ri-image-line me-2"></i>Images et Documents</h5>
                    </div>
                    <div class="card-body">
                        <!-- Alertes pour les erreurs -->
                        <div id="alert-container"></div>

                        <!-- Image principale -->
                        <div class="mb-4">
                            <label for="image_principale" class="form-label fw-bold">Image principale <span
                                    class="text-muted">(Max: 1MB)</span></label>
                            <input type="file" class="form-control @error('image_principale') is-invalid @enderror"
                                id="image_principale" name="image_principale" accept="image/*">
                            @error('image_principale')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div id="preview-principale" class="preview-images"></div>
                        </div>

                        <hr>

                        <!-- Images supplémentaires -->
                        <div class="mb-4">
                            <label for="images" class="form-label fw-bold">Images supplémentaires <span
                                    class="text-muted">(Max: 1MB par image, 10 images max)</span></label>
                            <input type="file" class="form-control @error('images.*') is-invalid @enderror"
                                id="images" name="images[]" accept="image/*" multiple>
                            @error('images.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div id="preview-images" class="preview-images"></div>
                        </div>

                        <hr>

                        <!-- Documents -->
                        <div class="mb-3">
                            <label for="documents" class="form-label fw-bold">Documents <span class="text-muted">(PDF,
                                    DOC, DOCX - Max: 5MB)</span></label>
                            <input type="file" class="form-control" id="documents" name="documents[]"
                                accept=".pdf,.doc,.docx" multiple>
                            <div id="preview-documents" class="mt-2"></div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="ri-money-dollar-circle-line me-2"></i>Gestion de commission</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="type_commission" class="form-label">Type de commission</label>
                                    <select class="form-select @error('type_commission') is-invalid @enderror"
                                        id="type_commission" name="type_commission">
                                        <option value="montant" {{ old('type_commission') == 'montant' ? 'selected' : '' }}>
                                            Montant fixe (FCFA)</option>
                                        <option value="pourcentage" {{ old('type_commission') == 'pourcentage' ? 'selected' : '' }}>
                                            Pourcentage (%)</option>
                                    </select>
                                    @error('type_commission')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="commission" class="form-label">Commission</label>
                                    <input type="number" class="form-control @error('commission') is-invalid @enderror"
                                        id="commission" name="commission" value="{{ old('commission') }}" step="0.01">
                                    @error('commission')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted" id="commission-help"></small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Commission calculée</label>
                                    <div class="form-control bg-light" id="commission-calculee" style="font-weight: 600; color: #0d6efd;">-</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Paramètres</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="statut" class="form-label">Statut <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('statut') is-invalid @enderror" id="statut"
                                        name="statut" required>
                                        <option value="disponible" {{ old('statut') == 'disponible' ? 'selected' : '' }}>
                                            Disponible</option>
                                        <option value="en_attente" {{ old('statut') == 'en_attente' ? 'selected' : '' }}>
                                            En attente</option>
                                        <option value="loue" {{ old('statut') == 'loue' ? 'selected' : '' }}>Loué
                                        </option>
                                        <option value="vendu" {{ old('statut') == 'vendu' ? 'selected' : '' }}>Vendu
                                        </option>
                                    </select>
                                    @error('statut')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="date_disponibilite" class="form-label">Date de disponibilité</label>
                                    <input type="date" class="form-control" id="date_disponibilite"
                                        name="date_disponibilite" value="{{ old('date_disponibilite') }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="en_vedette" name="en_vedette"
                                {{ old('en_vedette') ? 'checked' : '' }}>
                            <label class="form-check-label" for="en_vedette">
                                Mettre en vedette (sera affichée en priorité sur le site)
                            </label>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('backend.annonces.index') }}" class="btn btn-light">Annuler</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="ri-save-line me-1"></i> Enregistrer
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            // Données ville-commune
            const villesCommunesData = @json($villes);

            // Constantes de validation
            const MAX_IMAGE_SIZE = 1 * 1024 * 1024; // 1 MB
            const MAX_DOCUMENT_SIZE = 5 * 1024 * 1024; // 5 MB
            const MAX_IMAGES = 10;

            // Fonction pour afficher les alertes
            function afficherAlerte(message, type = 'danger') {
                const alertHtml = `
                    <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                        <i class="ri-error-warning-line me-2"></i>${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `;
                $('#alert-container').prepend(alertHtml);

                // Auto-fermeture après 5 secondes
                setTimeout(() => {
                    $('#alert-container .alert').first().fadeOut(500, function() {
                        $(this).remove();
                    });
                }, 5000);
            }

            // Fonction pour formater la taille
            function formatBytes(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
            }

            // Gestion ville-commune
            const villeSelect = document.getElementById('ville');
            const communeSelect = document.getElementById('commune');

            if (villeSelect && communeSelect) {
                villeSelect.addEventListener('change', function() {
                    communeSelect.innerHTML = '<option value="">Sélectionner une commune...</option>';

                    if (this.value && villesCommunesData[this.value]) {
                        const communes = villesCommunesData[this.value];
                        communeSelect.disabled = false;

                        communes.forEach(commune => {
                            const option = document.createElement('option');
                            option.value = commune;
                            option.textContent = commune;
                            communeSelect.appendChild(option);
                        });
                    } else {
                        communeSelect.disabled = true;
                    }
                });

                if (villeSelect.value) {
                    villeSelect.dispatchEvent(new Event('change'));
                }
            }

            // Variables pour stocker les fichiers
            let filesImagePrincipale = null;
            let filesImagesSupp = [];
            let filesDocuments = [];

            // Preview image principale avec validation
            $('#image_principale').on('change', function() {
                const preview = $('#preview-principale');
                const file = this.files[0];

                if (file) {
                    // Vérifier la taille
                    if (file.size > MAX_IMAGE_SIZE) {
                        afficherAlerte(
                            `L'image principale est trop volumineuse (${formatBytes(file.size)}). Taille maximale: 1 MB`
                            );
                        this.value = '';
                        preview.empty();
                        filesImagePrincipale = null;
                        return;
                    }

                    filesImagePrincipale = file;
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.html(`
                            <div class="preview-item">
                                <img src="${e.target.result}" alt="Preview">
                                <button type="button" class="btn-remove-preview" onclick="supprimerImagePrincipale()">×</button>
                                <small class="d-block text-center mt-1">${formatBytes(file.size)}</small>
                            </div>
                        `);
                    }
                    reader.readAsDataURL(file);
                } else {
                    preview.empty();
                    filesImagePrincipale = null;
                }
            });

            window.supprimerImagePrincipale = function() {
                $('#image_principale').val('');
                $('#preview-principale').empty();
                filesImagePrincipale = null;
            };

            // Preview images supplémentaires avec validation
            $('#images').on('change', function() {
                const newFiles = Array.from(this.files);
                let hasError = false;

                newFiles.forEach(file => {
                    // Vérifier la taille
                    if (file.size > MAX_IMAGE_SIZE) {
                        afficherAlerte(
                            `L'image "${file.name}" est trop volumineuse (${formatBytes(file.size)}). Max: 1 MB`
                            );
                        hasError = true;
                        return;
                    }

                    // Vérifier les doublons
                    const isDuplicate = filesImagesSupp.some(f =>
                        f.name === file.name && f.size === file.size
                    );

                    if (isDuplicate) {
                        afficherAlerte(`L'image "${file.name}" est déjà ajoutée.`, 'warning');
                        hasError = true;
                        return;
                    }

                    // Vérifier le nombre maximum
                    if (filesImagesSupp.length >= MAX_IMAGES) {
                        afficherAlerte(`Nombre maximum d'images atteint (${MAX_IMAGES} images).`,
                            'warning');
                        hasError = true;
                        return;
                    }

                    filesImagesSupp.push(file);
                });

                this.value = '';
                afficherPreviewImages();

                if (!hasError && newFiles.length > 0) {
                    afficherAlerte(`${newFiles.length} image(s) ajoutée(s) avec succès.`, 'success');
                }
            });

            function afficherPreviewImages() {
                const preview = $('#preview-images');
                preview.empty();

                filesImagesSupp.forEach((file, index) => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.append(`
                            <div class="preview-item">
                                <img src="${e.target.result}" alt="Preview">
                                <button type="button" class="btn-remove-preview" onclick="supprimerImage(${index})">×</button>
                                <small class="d-block text-center mt-1">${formatBytes(file.size)}</small>
                            </div>
                        `);
                    }
                    reader.readAsDataURL(file);
                });

                updateInputFiles('images', filesImagesSupp);
            }

            window.supprimerImage = function(index) {
                filesImagesSupp.splice(index, 1);
                afficherPreviewImages();
            };

            // Preview documents avec validation
            $('#documents').on('change', function() {
                const newFiles = Array.from(this.files);
                let hasError = false;

                newFiles.forEach(file => {
                    // Vérifier la taille
                    if (file.size > MAX_DOCUMENT_SIZE) {
                        afficherAlerte(
                            `Le document "${file.name}" est trop volumineux (${formatBytes(file.size)}). Max: 5 MB`
                            );
                        hasError = true;
                        return;
                    }

                    // Vérifier les doublons
                    const isDuplicate = filesDocuments.some(f =>
                        f.name === file.name && f.size === file.size
                    );

                    if (isDuplicate) {
                        afficherAlerte(`Le document "${file.name}" est déjà ajouté.`, 'warning');
                        hasError = true;
                        return;
                    }

                    filesDocuments.push(file);
                });

                this.value = '';
                afficherPreviewDocuments();

                if (!hasError && newFiles.length > 0) {
                    afficherAlerte(`${newFiles.length} document(s) ajouté(s) avec succès.`, 'success');
                }
            });

            function afficherPreviewDocuments() {
                const preview = $('#preview-documents');
                preview.empty();

                if (filesDocuments.length > 0) {
                    let html = '<ul class="list-group">';
                    filesDocuments.forEach((file, index) => {
                        html += `
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>
                                    <i class="ri-file-text-line me-2"></i>${file.name}
                                    <small class="text-muted ms-2">(${formatBytes(file.size)})</small>
                                </span>
                                <button type="button" class="btn btn-sm btn-danger" onclick="supprimerDocument(${index})">
                                    <i class="ri-close-line"></i>
                                </button>
                            </li>
                        `;
                    });
                    html += '</ul>';
                    preview.html(html);

                    updateInputFiles('documents', filesDocuments);
                }
            }

            window.supprimerDocument = function(index) {
                filesDocuments.splice(index, 1);
                afficherPreviewDocuments();
                
                // Si le tableau est vide, vider l'input
                if (filesDocuments.length === 0) {
                    document.getElementById('documents').value = '';
                }
            };

            function updateInputFiles(inputId, filesList) {
                const input = document.getElementById(inputId);
                const dataTransfer = new DataTransfer();

                filesList.forEach(file => {
                    dataTransfer.items.add(file);
                });

                input.files = dataTransfer.files;
            }

            // Gestion du calcul de commission
            function calculerCommission() {
                const prix = parseFloat($('#prix').val()) || 0;
                const typeCommission = $('#type_commission').val();
                const commission = parseFloat($('#commission').val()) || 0;
                
                if (typeCommission === 'pourcentage' && commission > 0 && prix > 0) {
                    const montantCommission = (prix * commission) / 100;
                    $('#commission-calculee').html(`${montantCommission.toLocaleString('fr-FR', {minimumFractionDigits: 0, maximumFractionDigits: 0})} FCFA`);
                    $('#commission-help').text(`${commission}% du prix`);
                } else if (typeCommission === 'montant' && commission > 0) {
                    $('#commission-calculee').html(`${commission.toLocaleString('fr-FR', {minimumFractionDigits: 0, maximumFractionDigits: 0})} FCFA`);
                    $('#commission-help').text('Montant fixe');
                } else {
                    $('#commission-calculee').html('-');
                    $('#commission-help').text('');
                }
            }

            // Événements pour le calcul de commission
            $('#prix, #commission, #type_commission').on('input change', calculerCommission);

            // Calcul initial si des valeurs existent
            calculerCommission();
        });
    </script>
@endsection
