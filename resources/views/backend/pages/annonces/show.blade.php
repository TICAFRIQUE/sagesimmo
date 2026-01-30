@extends('backend.layouts.master')
@section('title')
   Détails de l'annonce
@endsection
@section('css')
    <style>
        .property-image {
            width: 100%;
            height: 450px;
            object-fit: cover;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }
        
        .gallery-item {
            position: relative;
            border-radius: 8px;
            overflow: hidden;
            aspect-ratio: 4/3;
            cursor: pointer;
            transition: transform 0.3s;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .gallery-item:hover {
            transform: scale(1.05);
        }
        
        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .gallery-item .overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s;
        }
        
        .gallery-item:hover .overlay {
            opacity: 1;
        }
        
        .gallery-item .overlay i {
            color: white;
            font-size: 32px;
        }
        
        .info-card {
            border-left: 3px solid #007bff;
        }
        
        .feature-badge {
            background: #f8f9fa;
            padding: 8px 12px;
            border-radius: 5px;
            margin: 5px;
            display: inline-block;
        }
        
        .document-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        
        .document-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            transition: all 0.2s;
        }
        
        .document-item:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        
        .document-item .doc-info {
            display: flex;
            align-items: center;
            gap: 12px;
            flex: 1;
        }
        
        .document-item .doc-icon {
            font-size: 32px;
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
        
        .document-item .doc-actions {
            display: flex;
            gap: 8px;
        }
        
        /* Modal Image */
        .modal-image-container {
            max-height: 80vh;
            overflow: hidden;
        }
        
        .modal-image-container img {
            width: 100%;
            height: auto;
            object-fit: contain;
        }
    </style>
@endsection
@section('content')
    @component('backend.components.breadcrumb')
        @slot('li_1')
            <a href="{{ route('backend.annonces.index') }}">Annonces</a>
        @endslot
        @slot('title')
            Détails de l'annonce
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-8">
            <!-- Images -->
            <div class="card">
                <div class="card-body">
                    @if($annonce->hasMedia('image_principale'))
                        <img src="{{ $annonce->getFirstMediaUrl('image_principale') }}" 
                             alt="{{ $annonce->titre }}" 
                             class="property-image mb-3"
                             data-bs-toggle="modal" 
                             data-bs-target="#imageModal"
                             data-image="{{ $annonce->getFirstMediaUrl('image_principale') }}"
                             style="cursor: pointer;">
                    @else
                        <div class="property-image mb-3 d-flex align-items-center justify-content-center" style="background: #f1f5f9;">
                            <div class="text-center text-muted">
                                <i class="ri-image-line" style="font-size: 72px;"></i>
                                <p class="mt-2">Aucune image disponible</p>
                            </div>
                        </div>
                    @endif

                    @if($annonce->hasMedia('images'))
                        <h6 class="mb-3 mt-4"><i class="ri-gallery-line me-2"></i>Galerie d'images ({{ $annonce->getMedia('images')->count() }})</h6>
                        <div class="gallery-grid">
                            @foreach($annonce->getMedia('images') as $media)
                                <div class="gallery-item"
                                     data-bs-toggle="modal" 
                                     data-bs-target="#imageModal"
                                     data-image="{{ $media->getUrl() }}">
                                    <img src="{{ $media->getUrl() }}" alt="Image">
                                    <div class="overlay">
                                        <i class="ri-eye-line"></i>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Description -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="ri-file-text-line me-2"></i>Description</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">{{ $annonce->description }}</p>
                </div>
            </div>

            <!-- Caractéristiques -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="ri-list-check me-2"></i>Caractéristiques</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if($annonce->surface)
                            <div class="col-md-6 mb-3">
                                <div class="feature-badge w-100">
                                    <i class="ri-ruler-line me-2"></i>
                                    <strong>Surface:</strong> {{ $annonce->surface }} m²
                                </div>
                            </div>
                        @endif
                        @if($annonce->nombre_pieces)
                            <div class="col-md-6 mb-3">
                                <div class="feature-badge w-100">
                                    <i class="ri-home-4-line me-2"></i>
                                    <strong>Pièces:</strong> {{ $annonce->nombre_pieces }}
                                </div>
                            </div>
                        @endif
                        @if($annonce->nombre_chambres)
                            <div class="col-md-6 mb-3">
                                <div class="feature-badge w-100">
                                    <i class="ri-hotel-bed-line me-2"></i>
                                    <strong>Chambres:</strong> {{ $annonce->nombre_chambres }}
                                </div>
                            </div>
                        @endif
                        @if($annonce->nombre_salles_bain)
                            <div class="col-md-6 mb-3">
                                <div class="feature-badge w-100">
                                    <i class="ri-drop-line me-2"></i>
                                    <strong>Salles de bain:</strong> {{ $annonce->nombre_salles_bain }}
                                </div>
                            </div>
                        @endif
                        @if($annonce->etage !== null)
                            <div class="col-md-6 mb-3">
                                <div class="feature-badge w-100">
                                    <i class="ri-building-line me-2"></i>
                                    <strong>Étage:</strong> {{ $annonce->etage }}
                                </div>
                            </div>
                        @endif
                        @if($annonce->annee_construction)
                            <div class="col-md-6 mb-3">
                                <div class="feature-badge w-100">
                                    <i class="ri-calendar-line me-2"></i>
                                    <strong>Année:</strong> {{ $annonce->annee_construction }}
                                </div>
                            </div>
                        @endif
                    </div>

                    <h6 class="mt-3 mb-2">Équipements</h6>
                    <div class="d-flex flex-wrap">
                        @forelse($annonce->equipements as $equipement)
                            <span class="badge bg-primary me-2 mb-2">
                                @if($equipement->icone)
                                    <i class="{{ $equipement->icone }}"></i>
                                @endif
                                {{ $equipement->nom }}
                            </span>
                        @empty
                            <span class="text-muted">Aucun équipement spécifié</span>
                        @endforelse
                    </div>

                    @if($annonce->caracteristiques_supplementaires)
                        <h6 class="mt-3 mb-2">Caractéristiques supplémentaires</h6>
                        <p class="text-muted">{{ $annonce->caracteristiques_supplementaires }}</p>
                    @endif
                </div>
            </div>

            <!-- Localisation -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Localisation</h5>
                </div>
                <div class="card-body">
                    <p><strong>Adresse:</strong> {{ $annonce->adresse }}</p>
                    <p><strong>Ville:</strong> {{ $annonce->ville }}</p>
                    @if($annonce->commune)
                        <p><strong>Commune:</strong> {{ $annonce->commune }}</p>
                    @endif
                    @if($annonce->quartier)
                        <p><strong>Quartier:</strong> {{ $annonce->quartier }}</p>
                    @endif
                    @if($annonce->code_postal)
                        <p><strong>Code postal:</strong> {{ $annonce->code_postal }}</p>
                    @endif
                    
                    @if($annonce->latitude && $annonce->longitude)
                        <div class="mt-3">
                            <p><strong>Coordonnées GPS:</strong></p>
                            <p>Latitude: {{ $annonce->latitude }} | Longitude: {{ $annonce->longitude }}</p>
                            <a href="https://www.google.com/maps?q={{ $annonce->latitude }},{{ $annonce->longitude }}" 
                               target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="ri-map-pin-line me-1"></i> Voir sur Google Maps
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Documents -->
            @if($annonce->hasMedia('documents'))
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="ri-folder-line me-2"></i>Documents ({{ $annonce->getMedia('documents')->count() }})</h5>
                    </div>
                    <div class="card-body">
                        <div class="document-list">
                            @foreach($annonce->getMedia('documents') as $media)
                                <div class="document-item">
                                    <div class="doc-info">
                                        @php
                                            $extension = pathinfo($media->file_name, PATHINFO_EXTENSION);
                                            $iconClass = 'ri-file-text-line';
                                            if ($extension === 'pdf') {
                                                $iconClass = 'ri-file-pdf-line';
                                            } elseif (in_array($extension, ['doc', 'docx'])) {
                                                $iconClass = 'ri-file-word-line';
                                            }
                                        @endphp
                                        <i class="{{ $iconClass }} doc-icon"></i>
                                        <div class="doc-details">
                                            <span class="doc-name">{{ $media->file_name }}</span>
                                            <span class="doc-size">{{ number_format($media->size / 1024, 2) }} KB</span>
                                        </div>
                                    </div>
                                    <div class="doc-actions">
                                        <a href="{{ $media->getUrl() }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="ri-eye-line"></i> Voir
                                        </a>
                                        <a href="{{ $media->getUrl() }}" download class="btn btn-sm btn-outline-success">
                                            <i class="ri-download-line"></i> Télécharger
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <!-- Informations principales -->
            <div class="card info-card">
                <div class="card-body">
                    <h4 class="card-title mb-3">{{ $annonce->titre }}</h4>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="badge bg-secondary">{{ $annonce->reference }}</span>
                        @if($annonce->en_vedette)
                            <span class="badge bg-warning">
                                <i class="ri-star-fill"></i> En vedette
                            </span>
                        @endif
                    </div>

                    <h3 class="text-primary mb-3">{{ number_format($annonce->prix, 0, ',', ' ') }} FCFA</h3>

                    <div class="mb-3">
                        <span class="badge bg-info me-2">{{ $annonce->typeBien->nom ?? 'N/A' }}</span>
                        @if($annonce->type_transaction == 'vente')
                            <span class="badge bg-success">Vente</span>
                        @else
                            <span class="badge bg-primary">Location</span>
                        @endif
                    </div>

                    <div class="mb-3">
                        <strong>Statut:</strong>
                        @switch($annonce->statut)
                            @case('disponible')
                                <span class="badge bg-success">Disponible</span>
                                @break
                            @case('loue')
                                <span class="badge bg-warning">Loué</span>
                                @break
                            @case('vendu')
                                <span class="badge bg-danger">Vendu</span>
                                @break
                            @case('en_attente')
                                <span class="badge bg-secondary">En attente</span>
                                @break
                        @endswitch
                    </div>

                    @if($annonce->date_disponibilite)
                        <p><strong>Disponible le:</strong> {{ $annonce->date_disponibilite->format('d/m/Y') }}</p>
                    @endif

                    <hr>

                    <div class="d-grid gap-2">
                        <a href="{{ route('backend.annonces.edit', $annonce->id) }}" class="btn btn-primary">
                            <i class="ri-pencil-line me-1"></i> Modifier
                        </a>
                        <button type="button" class="btn btn-danger delete-annonce" data-id="{{ $annonce->id }}">
                            <i class="ri-delete-bin-line me-1"></i> Supprimer
                        </button>
                        <a href="{{ route('backend.annonces.index') }}" class="btn btn-light">
                            <i class="ri-arrow-left-line me-1"></i> Retour à la liste
                        </a>
                    </div>
                </div>
            </div>

            <!-- Informations de création -->
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Informations</h6>
                </div>
                <div class="card-body">
                    @if($annonce->proprietaire)
                        <div class="mb-3">
                            <strong>Propriétaire du bien:</strong>
                            <div class="mt-1">
                                <span class="badge bg-primary">{{ $annonce->proprietaire->name }}</span>
                                <br>
                                <small class="text-muted">{{ $annonce->proprietaire->email }}</small>
                            </div>
                        </div>
                    @endif
                    
                    @if($annonce->createdBy)
                        <div class="mb-3">
                            <strong>Annonce créée par:</strong>
                            <div class="mt-1">
                                <span class="badge bg-info">{{ $annonce->createdBy->name }}</span>
                                <br>
                                <small class="text-muted">{{ $annonce->createdBy->email }}</small>
                            </div>
                        </div>
                    @endif
                    
                    <p><strong>Date de création:</strong> {{ $annonce->created_at->format('d/m/Y à H:i') }}</p>
                    <p><strong>Dernière modification:</strong> {{ $annonce->updated_at->format('d/m/Y à H:i') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal pour afficher les images en grand -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalImage" src="" alt="Image" style="width: 100%; height: auto;">
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Afficher l'image dans le modal
        $(document).on('click', '.gallery-item, .property-image', function() {
            let imageSrc = $(this).data('image');
            $('#modalImage').attr('src', imageSrc);
        });

        // Suppression de l'annonce
        $('.delete-annonce').on('click', function() {
            let id = $(this).data('id');
            
            Swal.fire({
                title: 'Êtes-vous sûr ?',
                text: "Cette action est irréversible !",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Oui, supprimer !',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/admin/annonces/' + id,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire({
                                title: 'Supprimé !',
                                text: 'L\'annonce a été supprimée.',
                                icon: 'success'
                            }).then(() => {
                                window.location.href = '{{ route("backend.annonces.index") }}';
                            });
                        },
                        error: function(xhr) {
                            Swal.fire('Erreur !', 'Une erreur est survenue.', 'error');
                        }
                    });
                }
            });
        });
    </script>
@endsection
