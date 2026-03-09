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

            <!-- Biens achetés -->
            @if($user->ventes->count() > 0)
            <div class="card">
                <div class="card-header bg-primary-subtle">
                    <h5 class="card-title mb-0">
                        <i class="ri-shopping-cart-line me-2"></i>Biens achetés
                        <span class="badge bg-primary ms-2">{{ $user->ventes->count() }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    @foreach($user->ventes as $vente)
                        <div class="border rounded p-3 mb-3">
                            <div class="row align-items-center">
                                <div class="col-md-2">
                                    @if($vente->annonce->hasMedia('images'))
                                        <img src="{{ $vente->annonce->getFirstMediaUrl('images') }}" 
                                             class="img-fluid rounded" 
                                             alt="{{ $vente->annonce->titre }}">
                                    @else
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 80px;">
                                            <i class="ri-home-4-line display-5 text-muted"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-7">
                                    <h6 class="mb-1">
                                        <a href="{{ route('backend.ventes.show', $vente) }}" class="text-dark">
                                            {{ $vente->annonce->titre }}
                                        </a>
                                    </h6>
                                    <p class="text-muted mb-1">
                                        <i class="ri-map-pin-line me-1"></i>
                                        {{ $vente->annonce->ville }}, {{ $vente->annonce->quartier }}
                                    </p>
                                    <div>
                                        <span class="badge {{ $vente->statut_badge }}">
                                            {{ ucfirst(str_replace('_', ' ', $vente->statut)) }}
                                        </span>
                                        @if($vente->date_finalisation)
                                            <small class="text-muted ms-2">
                                                <i class="ri-calendar-check-line"></i>
                                                Finalisée le {{ $vente->date_finalisation->format('d/m/Y') }}
                                            </small>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-3 text-end">
                                    <div class="mb-2">
                                        <small class="text-muted d-block">Prix</small>
                                        <strong class="text-primary">
                                            {{ number_format($vente->annonce->prix, 0, ',', ' ') }} FCFA
                                        </strong>
                                    </div>
                                    @if($vente->paiements->count() > 0)
                                        @php
                                            $totalPaye = $vente->paiements->sum('montant');
                                            $pourcentagePaye = ($totalPaye / $vente->annonce->prix) * 100;
                                        @endphp
                                        <div>
                                            <small class="text-muted d-block">Payé</small>
                                            <strong class="text-success">
                                                {{ number_format($totalPaye, 0, ',', ' ') }} FCFA
                                            </strong>
                                            <div class="progress mt-1" style="height: 5px;">
                                                <div class="progress-bar bg-success" 
                                                     style="width: {{ $pourcentagePaye }}%"></div>
                                            </div>
                                        </div>
                                    @endif
                                    <a href="{{ route('backend.ventes.show', $vente) }}" 
                                       class="btn btn-sm btn-outline-primary mt-2">
                                        <i class="ri-eye-line me-1"></i>Détails
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Biens loués -->
            @if($user->locations->count() > 0)
            <div class="card">
                <div class="card-header bg-info-subtle">
                    <h5 class="card-title mb-0">
                        <i class="ri-home-heart-line me-2"></i>Biens loués
                        <span class="badge bg-info ms-2">{{ $user->locations->count() }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    @foreach($user->locations as $location)
                        <div class="border rounded p-3 mb-3">
                            <div class="row align-items-center">
                                <div class="col-md-2">
                                    @if($location->annonce->hasMedia('images'))
                                        <img src="{{ $location->annonce->getFirstMediaUrl('images') }}" 
                                             class="img-fluid rounded" 
                                             alt="{{ $location->annonce->titre }}">
                                    @else
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 80px;">
                                            <i class="ri-home-4-line display-5 text-muted"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-7">
                                    <h6 class="mb-1">
                                        <a href="{{ route('backend.locations.show', $location) }}" class="text-dark">
                                            {{ $location->annonce->titre }}
                                        </a>
                                    </h6>
                                    <p class="text-muted mb-1">
                                        <i class="ri-map-pin-line me-1"></i>
                                        {{ $location->annonce->ville }}, {{ $location->annonce->quartier }}
                                    </p>
                                    <div class="d-flex align-items-center gap-2">
                                         {!! $location->statut_badge !!}
                                        @if($location->date_debut)
                                            <small class="text-muted">
                                                <i class="ri-calendar-line"></i>
                                                Du {{ \Carbon\Carbon::parse($location->date_debut)->format('d/m/Y') }}
                                                @if($location->date_fin)
                                                    au {{ \Carbon\Carbon::parse($location->date_fin)->format('d/m/Y') }}
                                                @endif
                                            </small>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-3 text-end">
                                    <div class="mb-2">
                                        <small class="text-muted d-block">Loyer mensuel</small>
                                        <strong class="text-info">
                                            {{ number_format($location->loyer_mensuel ?? $location->annonce->prix, 0, ',', ' ') }} FCFA
                                        </strong>
                                    </div>
                                    @if($location->echeances->count() > 0)
                                        @php
                                            $echeancesPayees = $location->echeances->where('statut', 'paye')->count();
                                            $totalEcheances = $location->echeances->count();
                                        @endphp
                                        <div class="mb-2">
                                            <small class="text-muted d-block">Échéances payées</small>
                                            <strong class="text-success">{{ $echeancesPayees }}/{{ $totalEcheances }}</strong>
                                            <div class="progress mt-1" style="height: 5px;">
                                                <div class="progress-bar bg-success" 
                                                     style="width: {{ ($echeancesPayees / $totalEcheances) * 100 }}%"></div>
                                            </div>
                                        </div>
                                    @endif
                                    <a href="{{ route('backend.locations.show', $location) }}" 
                                       class="btn btn-sm btn-outline-info mt-2">
                                        <i class="ri-eye-line me-1"></i>Détails
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            @if($user->ventes->count() === 0 && $user->locations->count() === 0)
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Biens achetés/loués</h5>
                </div>
                <div class="card-body">
                    <div class="text-center py-4 text-muted">
                        <i class="ri-home-4-line display-4"></i>
                        <p class="mt-2">Aucun bien acheté ou loué</p>
                        <small>Les biens achetés ou loués par ce client s'afficheront ici</small>
                    </div>
                </div>
            </div>
            @endif

            <!-- Biens du propriétaire -->
            @if($user->annonces->count() > 0)
            <!-- Rapport financier pour les propriétaires -->
            @if($user->hasRole('proprietaire') && $statsFinancieres)
            <div class="card">
                <div class="card-header bg-primary">
                    <h5 class="card-title mb-0 text-white">
                        <i class="ri-bar-chart-box-line me-2"></i>Rapport financier
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Revenus locatifs -->
                        <div class="col-md-6 mb-4">
                            <div class="border-start border-4 border-info ps-3">
                                <h6 class="text-muted mb-2">Revenus locatifs perçus</h6>
                                <h3 class="text-info mb-1">{{ number_format($statsFinancieres['revenus_locatifs_total'], 0, ',', ' ') }} FCFA</h3>
                                <div class="d-flex align-items-center gap-3 mt-2">
                                    <div>
                                        <small class="text-muted d-block">En attente</small>
                                        <strong class="text-warning">{{ number_format($statsFinancieres['revenus_locatifs_attendus'], 0, ',', ' ') }} FCFA</strong>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Locations actives</small>
                                        <strong class="text-success">{{ $statsFinancieres['nombre_locations_actives'] }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Revenus de ventes -->
                        <div class="col-md-6 mb-4">
                            <div class="border-start border-4 border-success ps-3">
                                <h6 class="text-muted mb-2">Revenus de ventes perçus</h6>
                                <h3 class="text-success mb-1">{{ number_format($statsFinancieres['revenus_ventes_total'], 0, ',', ' ') }} FCFA</h3>
                                <div class="mt-2">
                                    <small class="text-muted d-block">Nombre de ventes</small>
                                    <strong class="text-dark">{{ $statsFinancieres['nombre_ventes'] }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- Statistiques des biens -->
                    <div class="row">
                        <div class="col-12 mb-3">
                            <h6 class="text-muted">Répartition des biens</h6>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-3 bg-success-subtle rounded">
                                <i class="ri-checkbox-circle-line display-5 text-success"></i>
                                <h4 class="mt-2 mb-0">{{ $statsFinancieres['biens_disponibles'] }}</h4>
                                <small class="text-muted">Disponibles</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-3 bg-warning-subtle rounded">
                                <i class="ri-home-heart-line display-5 text-warning"></i>
                                <h4 class="mt-2 mb-0">{{ $statsFinancieres['biens_loues'] }}</h4>
                                <small class="text-muted">Loués</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-3 bg-danger-subtle rounded">
                                <i class="ri-shopping-bag-line display-5 text-danger"></i>
                                <h4 class="mt-2 mb-0">{{ $statsFinancieres['biens_vendus'] }}</h4>
                                <small class="text-muted">Vendus</small>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <div class="alert alert-info mb-0">
                            <i class="ri-information-line me-2"></i>
                            <strong>Total des revenus:</strong> 
                            <span class="fs-5">{{ number_format($statsFinancieres['revenus_locatifs_total'] + $statsFinancieres['revenus_ventes_total'], 0, ',', ' ') }} FCFA</span>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Biens loués du propriétaire -->
            @php
                $biensLoues = $user->annonces->filter(function($annonce) {
                    return $annonce->locations->count() > 0;
                });
            @endphp

            @if($user->hasRole('proprietaire') && $biensLoues->count() > 0)
            <div class="card">
                <div class="card-header bg-warning-subtle">
                    <h5 class="card-title mb-0">
                        <i class="ri-home-heart-line me-2"></i>Biens en location
                        <span class="badge bg-warning ms-2">{{ $biensLoues->count() }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    @foreach($biensLoues as $annonce)
                        <div class="border rounded p-3 mb-3 bg-light">
                            <div class="row align-items-center mb-3">
                                <div class="col-md-2">
                                    @if($annonce->hasMedia('images'))
                                        <img src="{{ $annonce->getFirstMediaUrl('images') }}" 
                                             class="img-fluid rounded" 
                                             alt="{{ $annonce->titre }}">
                                    @else
                                        <div class="bg-white rounded d-flex align-items-center justify-content-center" style="height: 80px;">
                                            <i class="ri-home-4-line display-5 text-muted"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-7">
                                    <h6 class="mb-1">
                                        <a href="{{ route('backend.annonces.show', $annonce) }}" class="text-dark">
                                            {{ $annonce->titre }}
                                        </a>
                                    </h6>
                                    <p class="text-muted mb-1">
                                        <i class="ri-map-pin-line me-1"></i>
                                        {{ $annonce->ville }}, {{ $annonce->quartier }}
                                    </p>
                                    <span class="badge bg-info">{{ $annonce->typeBien->nom ?? 'N/A' }}</span>
                                </div>
                                <div class="col-md-3 text-end">
                                    <div class="mb-2">
                                        <small class="text-muted d-block">Prix mensuel</small>
                                        <strong class="text-info">
                                            {{ number_format($annonce->prix, 0, ',', ' ') }} FCFA
                                        </strong>
                                    </div>
                                    <span class="badge bg-success">{{ $annonce->locations->count() }} location(s)</span>
                                </div>
                            </div>

                            <!-- Détails des locations pour ce bien -->
                            <div class="border-top pt-3 mt-2">
                                <h6 class="mb-3"><i class="ri-list-check me-2"></i>Locations de ce bien:</h6>
                                @foreach($annonce->locations as $location)
                                    <div class="bg-white rounded p-3 mb-2 border">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-2">
                                                    <strong class="text-muted">Locataire:</strong>
                                                    <div class="mt-1">
                                                        <i class="ri-user-line me-1"></i>{{ $location->locataire->username }}
                                                        <br>
                                                        <small class="text-muted">
                                                            <i class="ri-mail-line me-1"></i>{{ $location->locataire->email }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-2">
                                                    <strong class="text-muted">Période:</strong>
                                                    <div class="mt-1">
                                                        @if($location->date_debut)
                                                            <small>
                                                                <i class="ri-calendar-line me-1"></i>
                                                                Du {{ \Carbon\Carbon::parse($location->date_debut)->format('d/m/Y') }}
                                                                @if($location->date_fin)
                                                                    au {{ \Carbon\Carbon::parse($location->date_fin)->format('d/m/Y') }}
                                                                @endif
                                                            </small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mt-2">
                                            <div class="col-md-6">
                                                <div class="mb-2">
                                                    <strong class="text-muted">Statut:</strong>
                                                    <span class="badge {{ $location->statut_badge }} ms-2">
                                                        {{ ucfirst(str_replace('_', ' ', $location->statut)) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                @if($location->loyer_mensuel)
                                                    <strong class="text-muted">Loyer:</strong>
                                                    <span class="text-success ms-2">{{ number_format($location->loyer_mensuel, 0, ',', ' ') }} FCFA</span>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Statistiques échéances -->
                                        @if($location->echeances->count() > 0)
                                            @php
                                                $echeancesPayees = $location->echeances->where('statut', 'paye')->count();
                                                $totalEcheances = $location->echeances->count();
                                                $montantPayes = $location->paiements->sum('montant');
                                                $montantAttendus = $location->echeances->where('statut', 'en_attente')->sum('montant');
                                            @endphp
                                            <div class="mt-3 pt-3 border-top">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <small class="text-muted d-block mb-1">Échéances</small>
                                                        <div class="progress" style="height: 20px;">
                                                            <div class="progress-bar bg-success" 
                                                                 role="progressbar" 
                                                                 style="width: {{ $totalEcheances > 0 ? ($echeancesPayees / $totalEcheances) * 100 : 0 }}%">
                                                                {{ $echeancesPayees }}/{{ $totalEcheances }} payées
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="d-flex justify-content-between">
                                                            <div>
                                                                <small class="text-muted d-block">Payé</small>
                                                                <strong class="text-success">{{ number_format($montantPayes, 0, ',', ' ') }} FCFA</strong>
                                                            </div>
                                                            <div>
                                                                <small class="text-muted d-block">En attente</small>
                                                                <strong class="text-warning">{{ number_format($montantAttendus, 0, ',', ' ') }} FCFA</strong>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        <div class="mt-3">
                                            <a href="{{ route('backend.locations.show', $location) }}" 
                                               class="btn btn-sm btn-primary">
                                                <i class="ri-eye-line me-1"></i>Voir le suivi détaillé
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Biens vendus du propriétaire -->
            @php
                $biensVendus = $user->annonces->filter(function($annonce) {
                    return $annonce->ventes->count() > 0;
                });
            @endphp

            @if($user->hasRole('proprietaire') && $biensVendus->count() > 0)
            <div class="card">
                <div class="card-header bg-success-subtle">
                    <h5 class="card-title mb-0">
                        <i class="ri-shopping-cart-line me-2"></i>Biens vendus
                        <span class="badge bg-success ms-2">{{ $biensVendus->count() }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    @foreach($biensVendus as $annonce)
                        <div class="border rounded p-3 mb-3 bg-light">
                            <div class="row align-items-center mb-3">
                                <div class="col-md-2">
                                    @if($annonce->hasMedia('images'))
                                        <img src="{{ $annonce->getFirstMediaUrl('images') }}" 
                                             class="img-fluid rounded" 
                                             alt="{{ $annonce->titre }}">
                                    @else
                                        <div class="bg-white rounded d-flex align-items-center justify-content-center" style="height: 80px;">
                                            <i class="ri-home-4-line display-5 text-muted"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-7">
                                    <h6 class="mb-1">
                                        <a href="{{ route('backend.annonces.show', $annonce) }}" class="text-dark">
                                            {{ $annonce->titre }}
                                        </a>
                                    </h6>
                                    <p class="text-muted mb-1">
                                        <i class="ri-map-pin-line me-1"></i>
                                        {{ $annonce->ville }}, {{ $annonce->quartier }}
                                    </p>
                                    <span class="badge bg-info">{{ $annonce->typeBien->nom ?? 'N/A' }}</span>
                                </div>
                                <div class="col-md-3 text-end">
                                    <div class="mb-2">
                                        <small class="text-muted d-block">Prix de vente</small>
                                        <strong class="text-success">
                                            {{ number_format($annonce->prix, 0, ',', ' ') }} FCFA
                                        </strong>
                                    </div>
                                    <span class="badge bg-primary">{{ $annonce->ventes->count() }} vente(s)</span>
                                </div>
                            </div>

                            <!-- Détails des ventes pour ce bien -->
                            <div class="border-top pt-3 mt-2">
                                <h6 class="mb-3"><i class="ri-list-check me-2"></i>Ventes de ce bien:</h6>
                                @foreach($annonce->ventes as $vente)
                                    <div class="bg-white rounded p-3 mb-2 border">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-2">
                                                    <strong class="text-muted">Acheteur:</strong>
                                                    <div class="mt-1">
                                                        <i class="ri-user-line me-1"></i>{{ $vente->client->username }}
                                                        <br>
                                                        <small class="text-muted">
                                                            <i class="ri-mail-line me-1"></i>{{ $vente->client->email }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-2">
                                                    <strong class="text-muted">Statut:</strong>
                                                    <span class="badge {{ $vente->statut_badge }} ms-2">
                                                        {{ ucfirst(str_replace('_', ' ', $vente->statut)) }}
                                                    </span>
                                                </div>
                                                @if($vente->date_finalisation)
                                                    <div>
                                                        <small class="text-muted">
                                                            <i class="ri-calendar-check-line me-1"></i>
                                                            Finalisée le {{ $vente->date_finalisation->format('d/m/Y') }}
                                                        </small>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Statistiques paiements -->
                                        @if($vente->paiements->count() > 0)
                                            @php
                                                $totalPaye = $vente->paiements->sum('montant');
                                                $pourcentagePaye = $annonce->prix > 0 ? ($totalPaye / $annonce->prix) * 100 : 0;
                                                $montantRestant = $annonce->prix - $totalPaye;
                                            @endphp
                                            <div class="mt-3 pt-3 border-top">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <small class="text-muted d-block mb-1">Paiement</small>
                                                        <div class="progress" style="height: 20px;">
                                                            <div class="progress-bar bg-success" 
                                                                 role="progressbar" 
                                                                 style="width: {{ $pourcentagePaye }}%">
                                                                {{ number_format($pourcentagePaye, 1) }}%
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="d-flex justify-content-between">
                                                            <div>
                                                                <small class="text-muted d-block">Payé</small>
                                                                <strong class="text-success">{{ number_format($totalPaye, 0, ',', ' ') }} FCFA</strong>
                                                            </div>
                                                            <div>
                                                                <small class="text-muted d-block">Restant</small>
                                                                <strong class="text-warning">{{ number_format($montantRestant, 0, ',', ' ') }} FCFA</strong>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        <div class="mt-3">
                                            <a href="{{ route('backend.ventes.show', $vente) }}" 
                                               class="btn btn-sm btn-success">
                                                <i class="ri-eye-line me-1"></i>Voir le suivi détaillé
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="card">
                <div class="card-header bg-success-subtle">
                    <h5 class="card-title mb-0">
                        <i class="ri-building-line me-2"></i>Tous les biens en propriété
                        <span class="badge bg-success ms-2">{{ $user->annonces->count() }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    @foreach($user->annonces as $annonce)
                        <div class="border rounded p-3 mb-3">
                            <div class="row align-items-center">
                                <div class="col-md-2">
                                    @if($annonce->hasMedia('images'))
                                        <img src="{{ $annonce->getFirstMediaUrl('images') }}" 
                                             class="img-fluid rounded" 
                                             alt="{{ $annonce->titre }}">
                                    @else
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 80px;">
                                            <i class="ri-home-4-line display-5 text-muted"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <h6 class="mb-1">
                                        <a href="{{ route('backend.annonces.show', $annonce) }}" class="text-dark">
                                            {{ $annonce->titre }}
                                        </a>
                                    </h6>
                                    <p class="text-muted mb-1">
                                        <i class="ri-map-pin-line me-1"></i>
                                        {{ $annonce->ville }}, {{ $annonce->quartier }}
                                    </p>
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <span class="badge {{ $annonce->statut == 'disponible' ? 'bg-success' : ($annonce->statut == 'loue' ? 'bg-warning' : 'bg-danger') }}">
                                            {{ ucfirst($annonce->statut) }}
                                        </span>
                                        <span class="badge bg-secondary">
                                            {{ $annonce->type_transaction == 'location' ? 'À louer' : 'À vendre' }}
                                        </span>
                                        @if($annonce->typeBien)
                                            <span class="badge bg-info">
                                                {{ $annonce->typeBien->nom }}
                                            </span>
                                        @endif
                                        @if($annonce->locations->count() > 0)
                                            <span class="badge bg-primary">
                                                <i class="ri-home-heart-line me-1"></i>{{ $annonce->locations->count() }} location(s)
                                            </span>
                                        @endif
                                        @if($annonce->ventes->count() > 0)
                                            <span class="badge bg-success">
                                                <i class="ri-shopping-cart-line me-1"></i>{{ $annonce->ventes->count() }} vente(s)
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4 text-end">
                                    <div class="mb-2">
                                        <small class="text-muted d-block">Prix</small>
                                        <strong class="text-success">
                                            {{ number_format($annonce->prix, 0, ',', ' ') }} FCFA
                                            @if($annonce->type_transaction == 'location')
                                                <small>/mois</small>
                                            @endif
                                        </strong>
                                    </div>
                                    <div class="mb-2">
                                        <small class="text-muted d-block">Surface</small>
                                        <strong>{{ $annonce->surface }} m²</strong>
                                    </div>
                                    <div class="d-flex gap-1 justify-content-end flex-wrap">
                                        <a href="{{ route('backend.annonces.show', $annonce) }}" 
                                           class="btn btn-sm btn-outline-success">
                                            <i class="ri-eye-line me-1"></i>Détails
                                        </a>
                                        @if($annonce->statut == 'loue' && $annonce->locations->count() > 0)
                                            @php
                                                $locationActive = $annonce->locations->sortByDesc('created_at')->first();
                                            @endphp
                                            <a href="{{ route('backend.locations.show', $locationActive) }}" 
                                               class="btn btn-sm btn-warning">
                                                <i class="ri-list-check me-1"></i>Suivi
                                            </a>
                                        @endif
                                        @if($annonce->statut == 'vendu' && $annonce->ventes->count() > 0)
                                            @php
                                                $venteActive = $annonce->ventes->sortByDesc('created_at')->first();
                                            @endphp
                                            <a href="{{ route('backend.ventes.show', $venteActive) }}" 
                                               class="btn btn-sm btn-primary">
                                                <i class="ri-list-check me-1"></i>Suivi
                                            </a>
                                        @endif
                                        <button type="button"
                                            class="btn btn-sm btn-outline-danger"
                                            onclick="openChargeModal({{ $annonce->id }}, '{{ addslashes($annonce->titre) }}')"
                                            title="Ajouter une charge">
                                            <i class="ri-money-dollar-circle-line me-1"></i>Charge
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
@endsection

{{-- Modal Créer une Charge depuis la fiche propriétaire --}}
<div class="modal fade" id="chargeProprietaireModal" tabindex="-1" aria-labelledby="chargeProprietaireModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('backend.charges.store') }}" method="POST">
                @csrf
                <input type="hidden" name="_redirect_back" value="1">
                <input type="hidden" name="annonce_id" id="charge_annonce_id">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="chargeProprietaireModalLabel">
                        <i class="ri-money-dollar-circle-line me-1"></i> Ajouter une charge
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-3">Bien : <strong id="charge_bien_titre"></strong></p>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><strong>Type de charge</strong> <span class="text-danger">*</span></label>
                                <select name="type_charge" class="form-control" required>
                                    <option value="">-- Sélectionner --</option>
                                    <option value="maintenance">Maintenance</option>
                                    <option value="reparation">Réparation</option>
                                    <option value="taxe">Taxe</option>
                                    <option value="autre">Autre</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><strong>Montant (FCFA)</strong> <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" name="montant" class="form-control" step="1" min="0" required>
                                    <span class="input-group-text">F</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><strong>Date de la charge</strong> <span class="text-danger">*</span></label>
                                <input type="date" name="date_charge" class="form-control" value="{{ today()->format('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><strong>Référence</strong> <span class="text-muted">(Facture, numéro)</span></label>
                                <input type="text" name="reference" class="form-control" placeholder="Ex: FAC-2026-001">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><strong>Description</strong></label>
                        <input type="text" name="description" class="form-control" placeholder="Détails de la charge">
                    </div>
                    <div class="mb-0">
                        <label class="form-label"><strong>Notes</strong></label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Remarques additionnelles..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="ri-close-line me-1"></i> Annuler
                    </button>
                    <button type="submit" class="btn btn-warning">
                        <i class="ri-save-line me-1"></i> Enregistrer la charge
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('script')
    <script>
        // Ouvrir le modal de création de charge pour un bien donné
        function openChargeModal(annonceId, titreBien) {
            document.getElementById('charge_annonce_id').value = annonceId;
            document.getElementById('charge_bien_titre').textContent = titreBien;
            var modal = new bootstrap.Modal(document.getElementById('chargeProprietaireModal'));
            modal.show();
        }

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
