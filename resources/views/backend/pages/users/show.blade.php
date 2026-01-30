@extends('backend.layouts.master')
@section('title')
    Détails de l'utilisateur
@endsection
@section('css')
    <style>
        .user-avatar-large {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            border: 5px solid #e2e8f0;
        }
        .info-label {
            font-weight: 600;
            color: #64748b;
            margin-bottom: 0.25rem;
        }
        .info-value {
            font-size: 1.1rem;
            color: #1e293b;
        }
        .document-item {
            position: relative;
            display: inline-block;
            margin: 10px;
        }
        .document-thumbnail {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #e2e8f0;
        }
        .btn-delete-document {
            position: absolute;
            top: -8px;
            right: -8px;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #ef4444;
            color: white;
            border: 2px solid white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3);
        }
        .btn-delete-document:hover {
            background: #dc2626;
            transform: scale(1.1);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.5);
        }
        .btn-delete-document i {
            font-size: 18px;
        }
    </style>
@endsection
@section('content')
    @component('backend.components.breadcrumb')
        @slot('li_1')
            <a href="{{ route('backend.users.index') }}">Utilisateurs</a>
        @endslot
        @slot('title')
            Détails de l'utilisateur
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body text-center">
                    @if($user->hasMedia('avatar'))
                        <img src="{{ $user->getFirstMediaUrl('avatar') }}" 
                             alt="{{ $user->username }}" 
                             class="user-avatar-large mb-3">
                    @else
                        <div class="avatar-lg rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center mb-3" 
                             style="width: 150px; height: 150px;">
                            <span class="fs-1 fw-bold">{{ substr($user->username, 0, 1) }}</span>
                        </div>
                    @endif
                    
                    <h4 class="mb-1">{{ $user->username }}</h4>
                    
                    @php
                        $userRole = $user->roles->first();
                    @endphp
                    @if($userRole)
                        @switch($userRole->name)
                            @case('locataire')
                                <span class="badge bg-info fs-6 mb-3">
                                    <i class="ri-home-heart-line me-1"></i>Locataire
                                </span>
                                @break
                            @case('proprietaire')
                                <span class="badge bg-success fs-6 mb-3">
                                    <i class="ri-building-line me-1"></i>Propriétaire
                                </span>
                                @break
                            @case('acheteur')
                                <span class="badge bg-primary fs-6 mb-3">
                                    <i class="ri-shopping-cart-line me-1"></i>Acheteur
                                </span>
                                @break
                            @case('admin')
                                <span class="badge bg-danger fs-6 mb-3">
                                    <i class="ri-admin-line me-1"></i>Administrateur
                                </span>
                                @break
                            @default
                                <span class="badge bg-secondary fs-6 mb-3">
                                    {{ ucfirst($userRole->name) }}
                                </span>
                        @endswitch
                    @else
                        <span class="badge bg-secondary fs-6 mb-3">Aucun rôle</span>
                    @endif

                    <div class="d-flex gap-2 justify-content-center mt-4">
                        <a href="{{ route('backend.users.edit', $user->id) }}" class="btn btn-primary">
                            <i class="ri-pencil-line me-1"></i> Modifier
                        </a>
                        <a href="{{ route('backend.users.index') }}" class="btn btn-light">
                            <i class="ri-arrow-left-line me-1"></i> Retour
                        </a>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Statistiques</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="info-label">Date d'inscription</div>
                        <div class="info-value">{{ $user->created_at->format('d/m/Y à H:i') }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="info-label">Dernière mise à jour</div>
                        <div class="info-value">{{ $user->updated_at->format('d/m/Y à H:i') }}</div>
                    </div>
                    @if($user->deleted_at)
                    <div class="mb-0">
                        <div class="info-label">Supprimé le</div>
                        <div class="info-value text-danger">{{ $user->deleted_at->format('d/m/Y à H:i') }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informations détaillées</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="info-label">ID</div>
                            <div class="info-value">
                                <span class="badge bg-secondary">{{ $user->id }}</span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="info-label">Email</div>
                            <div class="info-value">
                                <i class="ri-mail-line me-2 text-primary"></i>{{ $user->email }}
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="info-label">Téléphone</div>
                            <div class="info-value">
                                <i class="ri-phone-line me-2 text-success"></i>{{ $user->phone }}
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="info-label">Email vérifié</div>
                            <div class="info-value">
                                @if($user->email_verified_at)
                                    <span class="badge bg-success">
                                        <i class="ri-checkbox-circle-line me-1"></i>Oui
                                    </span>
                                    <small class="text-muted d-block mt-1">
                                        {{ $user->email_verified_at->format('d/m/Y') }}
                                    </small>
                                @else
                                    <span class="badge bg-warning">
                                        <i class="ri-close-circle-line me-1"></i>Non
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-12">
                            <h6 class="mb-3">Informations supplémentaires</h6>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="info-label">Type de compte</div>
                            <div class="info-value">
                                @php
                                    $userRole = $user->roles->first();
                                @endphp
                                @if($userRole)
                                    @switch($userRole->name)
                                        @case('locataire')
                                            <div class="alert alert-info mb-0 p-2">
                                                <i class="ri-home-heart-line me-2"></i>
                                                <strong>Locataire</strong> - Recherche des biens à louer
                                            </div>
                                            @break
                                        @case('proprietaire')
                                            <div class="alert alert-success mb-0 p-2">
                                                <i class="ri-building-line me-2"></i>
                                                <strong>Propriétaire</strong> - Propriétaire de biens immobiliers
                                            </div>
                                            @break
                                        @case('acheteur')
                                            <div class="alert alert-primary mb-0 p-2">
                                                <i class="ri-shopping-cart-line me-2"></i>
                                                <strong>Acheteur</strong> - Recherche des biens à acheter
                                            </div>
                                            @break
                                        @case('admin')
                                            <div class="alert alert-danger mb-0 p-2">
                                                <i class="ri-admin-line me-2"></i>
                                                <strong>Administrateur</strong> - Accès complet
                                            </div>
                                            @break
                                        @default
                                            <div class="alert alert-secondary mb-0 p-2">
                                                <i class="ri-user-line me-2"></i>
                                                <strong>{{ ucfirst($userRole->name) }}</strong>
                                            </div>
                                    @endswitch
                                @else
                                    <div class="alert alert-secondary mb-0 p-2">
                                        <i class="ri-user-line me-2"></i>
                                        <strong>Aucun rôle assigné</strong>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="info-label">Statut du compte</div>
                            <div class="info-value">
                                @if($user->deleted_at)
                                    <span class="badge bg-danger">
                                        <i class="ri-close-circle-line me-1"></i>Désactivé
                                    </span>
                                @else
                                    <span class="badge bg-success">
                                        <i class="ri-checkbox-circle-line me-1"></i>Actif
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section pour les activités futures -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Documents</h5>
                </div>
                <div class="card-body">
                    <!-- Pièces d'identité -->
                    @if($user->hasMedia('piece_identite'))
                        <div class="mb-4">
                            <h6 class="mb-3"><i class="ri-id-card-line me-2 text-primary"></i>Pièces d'identité</h6>
                            <div class="d-flex flex-wrap">
                                @foreach($user->getMedia('piece_identite') as $media)
                                    <div class="document-item">
                                        @if(str_contains($media->mime_type, 'image'))
                                            <a href="{{ $media->getUrl() }}" target="_blank">
                                                <img src="{{ $media->getUrl() }}" class="document-thumbnail" alt="Pièce d'identité">
                                            </a>
                                        @else
                                            <a href="{{ $media->getUrl() }}" target="_blank" class="text-decoration-none">
                                                <div class="document-thumbnail d-flex align-items-center justify-content-center bg-light">
                                                    <i class="ri-file-pdf-line display-4 text-danger"></i>
                                                </div>
                                            </a>
                                        @endif
                                        <button type="button" class="btn-delete-document" onclick="deleteDocument({{ $media->id }})"
                                                title="Supprimer">
                                            <i class="ri-close-line"></i>
                                        </button>
                                        <div class="text-center mt-2">
                                            <small class="text-muted">{{ Str::limit($media->file_name, 15) }}</small>
                                            <br>
                                            <small class="text-muted">{{ number_format($media->size / 1024, 2) }} KB</small>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Autres documents -->
                    @if($user->hasMedia('documents'))
                        <div class="mb-4">
                            <h6 class="mb-3"><i class="ri-file-text-line me-2 text-success"></i>Autres documents</h6>
                            <div class="d-flex flex-wrap">
                                @foreach($user->getMedia('documents') as $media)
                                    <div class="document-item">
                                        @if(str_contains($media->mime_type, 'image'))
                                            <a href="{{ $media->getUrl() }}" target="_blank">
                                                <img src="{{ $media->getUrl() }}" class="document-thumbnail" alt="Document">
                                            </a>
                                        @else
                                            <a href="{{ $media->getUrl() }}" target="_blank" class="text-decoration-none">
                                                <div class="document-thumbnail d-flex align-items-center justify-content-center bg-light">
                                                    @if(str_contains($media->mime_type, 'pdf'))
                                                        <i class="ri-file-pdf-line display-4 text-danger"></i>
                                                    @else
                                                        <i class="ri-file-word-line display-4 text-primary"></i>
                                                    @endif
                                                </div>
                                            </a>
                                        @endif
                                        <button type="button" class="btn-delete-document" onclick="deleteDocument({{ $media->id }})"
                                                title="Supprimer">
                                            <i class="ri-close-line"></i>
                                        </button>
                                        <div class="text-center mt-2">
                                            <small class="text-muted">{{ Str::limit($media->file_name, 15) }}</small>
                                            <br>
                                            <small class="text-muted">{{ number_format($media->size / 1024, 2) }} KB</small>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if(!$user->hasMedia('piece_identite') && !$user->hasMedia('documents'))
                        <div class="text-center py-4 text-muted">
                            <i class="ri-file-line display-4"></i>
                            <p class="mt-2">Aucun document</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Activités récentes</h5>
                </div>
                <div class="card-body">
                    <div class="text-center py-4 text-muted">
                        <i class="ri-time-line display-4"></i>
                        <p class="mt-2">Aucune activité pour le moment</p>
                        <small>Les activités de l'utilisateur s'afficheront ici</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        // Fonction pour supprimer un document
        function deleteDocument(mediaId) {
            if (!confirm('Êtes-vous sûr de vouloir supprimer ce document ?')) {
                return;
            }

            fetch('{{ route("backend.users.delete-media") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ media_id: mediaId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Erreur lors de la suppression du document');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Erreur lors de la suppression du document');
            });
        }
    </script>
@endsection
