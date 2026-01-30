@extends('backend.layouts.master')
@section('title')
    Modifier l'annonce
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

        /* Upload Zone Styles */
        .upload-zone {
            display: block;
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            padding: 30px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: #f8fafc;
        }

        .upload-zone:hover {
            border-color: #3b82f6;
            background: #eff6ff;
        }

        .upload-zone.dragover {
            border-color: #3b82f6;
            background: #dbeafe;
            transform: scale(1.02);
        }

        .upload-icon {
            font-size: 48px;
            color: #94a3b8;
            display: block;
            margin-bottom: 10px;
        }

        /* Preview Grid */
        .preview-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 15px;
        }

        .preview-item {
            position: relative;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            aspect-ratio: 4/3;
            background: #f1f5f9;
        }

        .preview-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .preview-item .remove-preview-btn {
            position: absolute;
            top: 8px;
            right: 8px;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: rgba(239, 68, 68, 0.95);
            color: white;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            line-height: 1;
            transition: all 0.2s;
            z-index: 10;
        }

        .preview-item .remove-preview-btn:hover {
            background: rgba(220, 38, 38, 1);
            transform: scale(1.1);
        }

        .preview-item .image-info {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 8px;
            font-size: 11px;
        }

        /* Existing Images */
        .existing-images {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }

        .existing-image {
            position: relative;
            border-radius: 8px;
            overflow: visible !important;
            /* Important pour que le bouton soit au-dessus */
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            aspect-ratio: 4/3;
        }

        .existing-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            user-select: none;
            pointer-events: none !important;
            border-radius: 8px;
        }

        .existing-image .btn-delete-media,
        .document-item .btn-delete-media {
            position: absolute !important;
            top: 8px !important;
            right: 8px !important;
            width: 32px !important;
            height: 32px !important;
            border-radius: 50% !important;
            background: rgba(239, 68, 68, 0.95) !important;
            color: white !important;
            border: 2px solid white !important;
            cursor: pointer !important;
            font-size: 20px !important;
            line-height: 1 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            transition: all 0.2s !important;
            z-index: 99999 !important;
            pointer-events: auto !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3) !important;
        }

        .existing-image .btn-delete-media:hover,
        .document-item .btn-delete-media:hover {
            background: rgba(220, 38, 38, 1) !important;
            transform: scale(1.15) !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4) !important;
        }

        /* Document List */
        .document-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .document-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 16px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .document-item:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
        }

        .document-item .doc-info {
            display: flex;
            align-items: center;
            gap: 12px;
            flex: 1;
        }

        .document-item .doc-icon {
            font-size: 24px;
            color: #ef4444;
        }

        .document-item .doc-details .doc-name {
            font-weight: 500;
            color: #1e293b;
            display: block;
        }

        .document-item .doc-details .doc-size {
            font-size: 12px;
            color: #64748b;
        }

        .document-item .remove-doc-btn {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #fee2e2;
            color: #ef4444;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .document-item .remove-doc-btn:hover {
            background: #ef4444;
            color: white;
        }

        border: none;
        border-radius: 50%;
        width: 25px;
        height: 25px;
        cursor: pointer;
        font-size: 12px;
        }

        .existing-images {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .existing-image {
            position: relative;
            width: 120px;
            height: 120px;
        }

        .existing-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 5px;
            border: 2px solid #ddd;
        }
    </style>
@endsection

@section('content')
    @component('backend.components.breadcrumb')
        @slot('li_1')
            <a href="{{ route('backend.annonces.index') }}">Annonces</a>
        @endslot
        @slot('title')
            Modifier l'annonce
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <form class="needs-validation" action="{{ route('backend.annonces.update', $annonce->id) }}" method="POST"
                enctype="multipart/form-data" novalidate>
                @csrf
                @method('PUT')

                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Informations générales</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <strong>Référence:</strong> {{ $annonce->reference }}
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="titre" class="form-label">Titre de l'annonce <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('titre') is-invalid @enderror"
                                        id="titre" name="titre" value="{{ old('titre', $annonce->titre) }}" required>
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
                                        id="prix" name="prix" value="{{ old('prix', $annonce->prix) }}"
                                        step="0.01" required>
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
                                        <option value="vente"
                                            {{ old('type_transaction', $annonce->type_transaction) == 'vente' ? 'selected' : '' }}>
                                            Vente</option>
                                        <option value="location"
                                            {{ old('type_transaction', $annonce->type_transaction) == 'location' ? 'selected' : '' }}>
                                            Location</option>
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
                                                {{ old('type_bien_id', $annonce->type_bien_id) == $typeBien->id ? 'selected' : '' }}>
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
                                        {{ old('proprietaire_id', $annonce->proprietaire_id) == $proprietaire->id ? 'selected' : '' }}>
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
                                rows="5" required>{{ old('description', $annonce->description) }}</textarea>
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
                                        value="{{ old('surface', $annonce->surface) }}" step="0.01">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="nombre_pieces" class="form-label">Nombre de pièces</label>
                                    <input type="number" class="form-control" id="nombre_pieces" name="nombre_pieces"
                                        value="{{ old('nombre_pieces', $annonce->nombre_pieces) }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="nombre_chambres" class="form-label">Nombre de chambres</label>
                                    <input type="number" class="form-control" id="nombre_chambres"
                                        name="nombre_chambres"
                                        value="{{ old('nombre_chambres', $annonce->nombre_chambres) }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="nombre_salles_bain" class="form-label">Salles de bain</label>
                                    <input type="number" class="form-control" id="nombre_salles_bain"
                                        name="nombre_salles_bain"
                                        value="{{ old('nombre_salles_bain', $annonce->nombre_salles_bain) }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="etage" class="form-label">Étage</label>
                                    <input type="number" class="form-control" id="etage" name="etage"
                                        value="{{ old('etage', $annonce->etage) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="annee_construction" class="form-label">Année de construction</label>
                                    <input type="number" class="form-control" id="annee_construction"
                                        name="annee_construction"
                                        value="{{ old('annee_construction', $annonce->annee_construction) }}"
                                        min="1800" max="{{ date('Y') + 5 }}">
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
                                                    {{ in_array($equipement->id, old('equipements', $annonce->equipements->pluck('id')->toArray())) ? 'checked' : '' }}>
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

                        <div class="mb-3">
                            <label for="caracteristiques_supplementaires" class="form-label">Caractéristiques
                                supplémentaires</label>
                            <textarea class="form-control" id="caracteristiques_supplementaires" name="caracteristiques_supplementaires"
                                rows="3">{{ old('caracteristiques_supplementaires', $annonce->caracteristiques_supplementaires) }}</textarea>
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
                                                {{ old('ville', $annonce->ville) == $ville ? 'selected' : '' }}>
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
                                        data-current-commune="{{ old('commune', $annonce->commune) }}" disabled>
                                        <option value="">Sélectionner une commune...</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="quartier" class="form-label">Quartier</label>
                                    <input type="text" class="form-control" id="quartier" name="quartier"
                                        value="{{ old('quartier', $annonce->quartier) }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="adresse" class="form-label">Adresse complète avec precisions <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('adresse') is-invalid @enderror"
                                        id="adresse" name="adresse" value="{{ old('adresse', $annonce->adresse) }}"
                                        required>
                                    @error('adresse')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0"><i class="ri-image-line me-2"></i>Images et Documents</h5>
                    </div>
                    <div class="card-body">
                        <!-- Alertes pour les erreurs -->
                        <div id="alert-container"></div>

                        <!-- Image principale existante -->
                        @if ($annonce->hasMedia('image_principale'))
                            <div class="mb-3">
                                <label class="form-label fw-bold">Image principale actuelle</label>
                                <div class="preview-images">
                                    @foreach ($annonce->getMedia('image_principale') as $media)
                                        <div class="preview-item">
                                            <img src="{{ $media->getUrl() }}" alt="Image principale"
                                                style="width: 150px; height: 150px; object-fit: cover; border-radius: 8px; border: 2px solid #e2e8f0;">
                                            <button type="button" class="btn-remove-preview"
                                                onclick="supprimerMedia({{ $media->id }}, this); return false;">×</button>
                                            <small
                                                class="d-block text-center mt-1">{{ number_format($media->size / 1024, 2) }}
                                                KB</small>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="mb-4">
                            <label for="image_principale" class="form-label fw-bold">
                                {{ $annonce->hasMedia('image_principale') ? 'Remplacer l\'image principale' : 'Nouvelle image principale' }}
                                <span class="text-muted">(Max: 1MB)</span>
                            </label>
                            <input type="file" class="form-control @error('image_principale') is-invalid @enderror"
                                id="image_principale" name="image_principale" accept="image/*">
                            @error('image_principale')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div id="preview-principale" class="preview-images"></div>
                        </div>

                        <hr>

                        <!-- Images supplémentaires existantes -->
                        @if ($annonce->hasMedia('images'))
                            <div class="mb-3">
                                <label class="form-label fw-bold">Images supplémentaires actuelles
                                    ({{ $annonce->getMedia('images')->count() }})</label>
                                <div class="preview-images">
                                    @foreach ($annonce->getMedia('images') as $media)
                                        <div class="preview-item">
                                            <img src="{{ $media->getUrl() }}" alt="Image"
                                                style="width: 150px; height: 150px; object-fit: cover; border-radius: 8px; border: 2px solid #e2e8f0;">
                                            <button type="button" class="btn-remove-preview"
                                                onclick="supprimerMedia({{ $media->id }}, this); return false;">
                                                ×
                                            </button>
                                            <small
                                                class="d-block text-center mt-1">{{ number_format($media->size / 1024, 2) }}
                                                KB</small>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="mb-4">
                            <label for="images" class="form-label fw-bold">Ajouter des images supplémentaires <span
                                    class="text-muted">(Max: 1MB par image, 10 images max)</span></label>
                            <input type="file" class="form-control @error('images.*') is-invalid @enderror"
                                id="images" name="images[]" accept="image/*" multiple>
                            @error('images.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div id="preview-images" class="preview-images"></div>
                        </div>

                        <hr>

                        <!-- Documents existants -->
                        @if ($annonce->hasMedia('documents'))
                            <div class="mb-3">
                                <label class="form-label fw-bold">Documents actuels
                                    ({{ $annonce->getMedia('documents')->count() }})</label>
                                <ul class="list-group">
                                    @foreach ($annonce->getMedia('documents') as $media)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>
                                                <i class="ri-file-text-line me-2"></i>{{ $media->file_name }}
                                                <small
                                                    class="text-muted ms-2">({{ number_format($media->size / 1024, 2) }}
                                                    KB)</small>
                                            </span>
                                            <button type="button" class="btn btn-sm btn-danger"
                                                onclick="supprimerMedia({{ $media->id }}, this); return false;">
                                                <i class="ri-close-line"></i>
                                            </button>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label for="documents" class="form-label fw-bold">Ajouter des documents <span
                                    class="text-muted">(PDF, DOC, DOCX - Max: 5MB)</span></label>
                            <input type="file" class="form-control" id="documents" name="documents[]"
                                accept=".pdf,.doc,.docx" multiple>
                            <div id="preview-documents" class="mt-2"></div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="ri-money-dollar-circle-line me-2"></i>Gestion de commission
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="type_commission" class="form-label">Type de commission</label>
                                    <select class="form-select @error('type_commission') is-invalid @enderror"
                                        id="type_commission" name="type_commission">
                                        <option value="montant"
                                            {{ old('type_commission', $annonce->type_commission) == 'montant' ? 'selected' : '' }}>
                                            Montant fixe (FCFA)</option>
                                        <option value="pourcentage"
                                            {{ old('type_commission', $annonce->type_commission) == 'pourcentage' ? 'selected' : '' }}>
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
                                        id="commission" name="commission"
                                        value="{{ old('commission', $annonce->commission) }}" step="0.01">
                                    @error('commission')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted" id="commission-help"></small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Commission calculée</label>
                                    <div class="form-control bg-light" id="commission-calculee"
                                        style="font-weight: 600; color: #0d6efd;">-</div>
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
                                        <option value="disponible"
                                            {{ old('statut', $annonce->statut) == 'disponible' ? 'selected' : '' }}>
                                            Disponible</option>
                                        <option value="en_attente"
                                            {{ old('statut', $annonce->statut) == 'en_attente' ? 'selected' : '' }}>En
                                            attente</option>
                                        <option value="loue"
                                            {{ old('statut', $annonce->statut) == 'loue' ? 'selected' : '' }}>Loué
                                        </option>
                                        <option value="vendu"
                                            {{ old('statut', $annonce->statut) == 'vendu' ? 'selected' : '' }}>
                                            Vendu</option>
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
                                        name="date_disponibilite"
                                        value="{{ old('date_disponibilite', $annonce->date_disponibilite ? $annonce->date_disponibilite->format('Y-m-d') : '') }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="en_vedette" name="en_vedette"
                                {{ old('en_vedette', $annonce->en_vedette) ? 'checked' : '' }}>
                            <label class="form-check-label" for="en_vedette">
                                Mettre en vedette (sera affichée en priorité sur le site)
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Boutons d'action -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('backend.annonces.index') }}" class="btn btn-light">
                                <i class="ri-arrow-left-line me-1"></i> Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="ri-save-line me-1"></i> Mettre à jour
                            </button>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>
    {{-- </div>
    </div> --}}





@endsection

@section('script')
    @if (!isset($jqueryLoaded))
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @endif
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {

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

            // Fonction globale pour supprimer les médias existants
            window.supprimerMedia = function(mediaId, button) {
                if (!mediaId) {
                    afficherAlerte('Erreur: ID du média introuvable');
                    return false;
                }

                if (!confirm('Voulez-vous vraiment supprimer ce fichier ?')) {
                    return false;
                }

                $.ajax({
                    url: '{{ route('backend.annonces.delete-image') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        media_id: mediaId
                    },
                    success: function(response) {
                        if (response.success) {
                            $(button).closest('.preview-item, li').fadeOut(300, function() {
                                $(this).remove();
                            });
                            afficherAlerte('Fichier supprimé avec succès', 'success');
                        } else {
                            afficherAlerte('Erreur: ' + response.message);
                        }
                    },
                    error: function(xhr) {
                        afficherAlerte('Erreur lors de la suppression');
                    }
                });

                return false;
            };

            // Variables pour stocker les fichiers
            let filesImagePrincipale = null;
            let filesImagesSupp = [];
            let filesDocuments = [];

            // Preview image principale avec validation
            $('#image_principale').on('change', function() {
                const preview = $('#preview-principale');
                const file = this.files[0];

                if (file) {
                    if (file.size > MAX_IMAGE_SIZE) {
                        afficherAlerte(
                            `L'image principale est trop volumineuse (${formatBytes(file.size)}). Max: 1 MB`
                        );
                        this.value = '';
                        preview.empty();
                        return;
                    }

                    filesImagePrincipale = file;
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.html(`
                        <div class="preview-item">
                            <img src="${e.target.result}" alt="Preview" style="width: 150px; height: 150px; object-fit: cover; border-radius: 8px; border: 2px solid #e2e8f0;">
                            <button type="button" class="btn-remove-preview" onclick="supprimerNouvelleImage('principale')">×</button>
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

            window.supprimerNouvelleImage = function(type) {
                if (type === 'principale') {
                    $('#image_principale').val('');
                    $('#preview-principale').empty();
                    filesImagePrincipale = null;
                }
            };

            // Preview images supplémentaires avec validation
            $('#images').on('change', function() {
                const newFiles = Array.from(this.files);
                let hasError = false;

                newFiles.forEach(file => {
                    if (file.size > MAX_IMAGE_SIZE) {
                        afficherAlerte(
                            `L'image "${file.name}" est trop volumineuse (${formatBytes(file.size)}). Max: 1 MB`
                        );
                        hasError = true;
                        return;
                    }

                    const isDuplicate = filesImagesSupp.some(f =>
                        f.name === file.name && f.size === file.size
                    );

                    if (isDuplicate) {
                        afficherAlerte(`L'image "${file.name}" est déjà ajoutée.`, 'warning');
                        hasError = true;
                        return;
                    }

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
                            <img src="${e.target.result}" alt="Preview" style="width: 150px; height: 150px; object-fit: cover; border-radius: 8px; border: 2px solid #e2e8f0;">
                            <button type="button" class="btn-remove-preview" onclick="supprimerImageNouvelle(${index})">×</button>
                            <small class="d-block text-center mt-1">${formatBytes(file.size)}</small>
                        </div>
                    `);
                    }
                    reader.readAsDataURL(file);
                });

                updateInputFiles('images', filesImagesSupp);
            }

            window.supprimerImageNouvelle = function(index) {
                filesImagesSupp.splice(index, 1);
                afficherPreviewImages();
            };

            // Preview documents avec validation
            $('#documents').on('change', function() {
                const newFiles = Array.from(this.files);
                let hasError = false;

                newFiles.forEach(file => {
                    if (file.size > MAX_DOCUMENT_SIZE) {
                        afficherAlerte(
                            `Le document "${file.name}" est trop volumineux (${formatBytes(file.size)}). Max: 5 MB`
                        );
                        hasError = true;
                        return;
                    }

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
                            <button type="button" class="btn btn-sm btn-danger" onclick="supprimerDocumentNouveau(${index})">
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

            window.supprimerDocumentNouveau = function(index) {
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

            // Données ville-commune
            const villesCommunesData = @json($villes);

            function chargerCommunes(villeSelectionnee, communeActuelle = null) {
                const communeSelect = document.getElementById('commune');

                if (!communeSelect) return;

                communeSelect.innerHTML = '<option value="">Sélectionner une commune...</option>';

                if (villeSelectionnee && villesCommunesData[villeSelectionnee]) {
                    const communes = villesCommunesData[villeSelectionnee];

                    if (communes.length > 0) {
                        communeSelect.disabled = false;

                        communes.forEach(commune => {
                            const option = document.createElement('option');
                            option.value = commune;
                            option.textContent = commune;

                            if (communeActuelle && commune === communeActuelle) {
                                option.selected = true;
                            }

                            communeSelect.appendChild(option);
                        });
                    } else {
                        communeSelect.disabled = true;
                    }
                } else {
                    communeSelect.disabled = true;
                }
            }

            // Initialiser ville et commune immédiatement
            const villeSelect = document.getElementById('ville');
            const communeSelect = document.getElementById('commune');

            if (villeSelect && communeSelect) {
                const villeInitiale = villeSelect.value;
                const communeInitiale = communeSelect.dataset.currentCommune || null;

                if (villeInitiale) {
                    chargerCommunes(villeInitiale, communeInitiale);
                }

                villeSelect.addEventListener('change', function() {
                    chargerCommunes(this.value);
                });
            }

            // Fonction utilitaire pour formater la taille des fichiers
            function formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
            }

            // Configuration du drag & drop pour l'image principale
            const dropZonePrincipale = $('#drop-zone-principale');
            const inputPrincipale = $('#image_principale');

            dropZonePrincipale.on('click', function(e) {
                e.preventDefault();
                inputPrincipale[0].click();
            });

            dropZonePrincipale.on('dragover', function(e) {
                e.preventDefault();
                $(this).addClass('dragover');
            });

            dropZonePrincipale.on('dragleave', function(e) {
                e.preventDefault();
                $(this).removeClass('dragover');
            });

            dropZonePrincipale.on('drop', function(e) {
                e.preventDefault();
                $(this).removeClass('dragover');
                const files = e.originalEvent.dataTransfer.files;
                if (files.length > 0) {
                    inputPrincipale.prop('files', files);
                    inputPrincipale.trigger('change');
                }
            });

            // Preview image principale - géré par le handler plus haut avec validation

            // Configuration du drag & drop pour images supplémentaires
            const dropZoneImages = $('#drop-zone-images');
            const inputImages = $('#images');

            dropZoneImages.on('click', function(e) {
                e.preventDefault();
                inputImages[0].click();
            });

            dropZoneImages.on('dragover', function(e) {
                e.preventDefault();
                $(this).addClass('dragover');
            });

            dropZoneImages.on('dragleave', function(e) {
                e.preventDefault();
                $(this).removeClass('dragover');
            });

            dropZoneImages.on('drop', function(e) {
                e.preventDefault();
                $(this).removeClass('dragover');
                const files = e.originalEvent.dataTransfer.files;
                if (files.length > 0) {
                    inputImages.prop('files', files);
                    inputImages.trigger('change');
                }
            });

            // Preview images multiples - géré par afficherPreviewImages() plus haut

            // Configuration du drag & drop pour documents
            const dropZoneDocuments = $('#drop-zone-documents');
            const inputDocuments = $('#documents');

            dropZoneDocuments.on('click', function(e) {
                e.preventDefault();
                inputDocuments[0].click();
            });

            dropZoneDocuments.on('dragover', function(e) {
                e.preventDefault();
                $(this).addClass('dragover');
            });

            dropZoneDocuments.on('dragleave', function(e) {
                e.preventDefault();
                $(this).removeClass('dragover');
            });

            dropZoneDocuments.on('drop', function(e) {
                e.preventDefault();
                $(this).removeClass('dragover');
                const files = e.originalEvent.dataTransfer.files;
                if (files.length > 0) {
                    inputDocuments.prop('files', files);
                    inputDocuments.trigger('change');
                }
            });

            // Preview documents
            inputDocuments.change(function() {
                let preview = $('#preview-documents');
                preview.empty();

                if (this.files) {
                    $.each(this.files, function(i, file) {
                        const extension = file.name.split('.').pop().toLowerCase();
                        let iconClass = 'ri-file-text-line';

                        if (extension === 'pdf') iconClass = 'ri-file-pdf-line';
                        else if (extension === 'doc' || extension === 'docx') iconClass =
                            'ri-file-word-line';

                        preview.append(`
                        <div class="document-item">
                            <div class="doc-info">
                                <i class="${iconClass} doc-icon"></i>
                                <div class="doc-details">
                                    <span class="doc-name">${file.name}</span>
                                    <span class="doc-size">${formatFileSize(file.size)}</span>
                                </div>
                            </div>
                            <button type="button" class="remove-doc-btn" data-index="${i}">
                                <i class="ri-close-line"></i>
                            </button>
                        </div>
                    `);
                    });
                }
            });

            // Supprimer les previews
            $(document).on('click', '.remove-preview-btn', function() {
                const target = $(this).data('target');
                if (target === 'principale') {
                    inputPrincipale.val('');
                    $('#preview-principale').empty();
                }
            });

            $(document).on('click', '.remove-doc-btn', function() {
                const index = $(this).data('index');
                supprimerDocumentNouveau(index);
            });

            // Note: La suppression des médias existants est gérée par la fonction onclick inline supprimerMedia()
            // pour éviter les conflits avec les labels des zones de drag & drop

            // Code commenté - ne pas utiliser de gestionnaire jQuery ici
            // car il entre en conflit avec les événements onclick inline
            /*
            $(document).on('click', '.delete-media', function(e) {
                ...
            */

            // Fonction de nettoyage si besoin de déboguer
            window.debugMediaButtons = function() {
                console.log('Boutons de suppression:', $('.btn-delete-media').length);
                $('.btn-delete-media').each(function(i, btn) {
                    console.log('Bouton', i, '- Media ID:', $(btn).data('media-id'));
                });
            };

            // Vérifier les boutons au chargement de la page
            console.log('Boutons de suppression trouvés:', $('.btn-delete-media').length);

            // Gestion du calcul de commission
            function calculerCommission() {
                const prix = parseFloat($('#prix').val()) || 0;
                const typeCommission = $('#type_commission').val();
                const commission = parseFloat($('#commission').val()) || 0;

                if (typeCommission === 'pourcentage' && commission > 0 && prix > 0) {
                    const montantCommission = (prix * commission) / 100;
                    $('#commission-calculee').html(
                        `${montantCommission.toLocaleString('fr-FR', {minimumFractionDigits: 0, maximumFractionDigits: 0})} FCFA`
                        );
                    $('#commission-help').text(`${commission}% du prix`);
                } else if (typeCommission === 'montant' && commission > 0) {
                    $('#commission-calculee').html(
                        `${commission.toLocaleString('fr-FR', {minimumFractionDigits: 0, maximumFractionDigits: 0})} FCFA`
                        );
                    $('#commission-help').text('Montant fixe');
                } else {
                    $('#commission-calculee').html('-');
                    $('#commission-help').text('');
                }
            }

            // Événements pour le calcul de commission
            $('#prix, #commission, #type_commission').on('input change', calculerCommission);

            // Calcul initial au chargement de la page
            calculerCommission();

        }); // Fin de $(document).ready()
    </script>
@endsection
