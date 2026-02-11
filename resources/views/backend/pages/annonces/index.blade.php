@extends('backend.layouts.master')
@section('title')
   Annonces Immobilières
@endsection
@section('css')
    <!--datatable css-->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <!--datatable responsive css-->
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
    <style>
        .annonce-image {
            width: 80px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
        }
        .badge-status {
            font-size: 0.75rem;
        }
    </style>
@endsection
@section('content')
    @component('backend.components.breadcrumb')
        @slot('li_1')
            Gestion
        @endslot
        @slot('title')
            Annonces Immobilières
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Liste des annonces</h5>
                    <a href="{{ route('backend.annonces.create') }}" class="btn btn-primary">
                        <i class="ri-add-line align-middle me-1"></i> Nouvelle annonce
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="ri-check-line me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Filtres -->
                    <div class="card border mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">
                                <i class="ri-filter-3-line me-2"></i>Filtres de recherche
                            </h6>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="{{ route('backend.annonces.index') }}" id="filterForm">
                                <div class="row g-3">
                                    <!-- Filtre par type de propriétaire -->
                                    <div class="col-md-3">
                                        <label for="type_proprietaire" class="form-label">Type de propriétaire</label>
                                        <select class="form-select" name="type_proprietaire" id="type_proprietaire">
                                            <option value="">Tous</option>
                                            <option value="agence" {{ request('type_proprietaire') == 'agence' ? 'selected' : '' }}>Biens de l'agence</option>
                                            <option value="externe" {{ request('type_proprietaire') == 'externe' ? 'selected' : '' }}>Propriétaires externes</option>
                                        </select>
                                    </div>

                                    <!-- Filtre par propriétaire -->
                                    <div class="col-md-3">
                                        <label for="proprietaire_id" class="form-label">Propriétaire</label>
                                        <select class="form-select" name="proprietaire_id" id="proprietaire_id">
                                            <option value="">Tous les propriétaires</option>
                                            @foreach($proprietaires as $proprietaire)
                                                <option value="{{ $proprietaire->id }}" {{ request('proprietaire_id') == $proprietaire->id ? 'selected' : '' }}>
                                                    {{ $proprietaire->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Filtre par statut -->
                                    <div class="col-md-3">
                                        <label for="statut" class="form-label">Statut</label>
                                        <select class="form-select" name="statut" id="statut">
                                            <option value="">Tous les statuts</option>
                                            <option value="disponible" {{ request('statut') == 'disponible' ? 'selected' : '' }}>Disponible</option>
                                            <option value="loue" {{ request('statut') == 'loue' ? 'selected' : '' }}>Loué</option>
                                            <option value="vendu" {{ request('statut') == 'vendu' ? 'selected' : '' }}>Vendu</option>
                                            <option value="en_attente" {{ request('statut') == 'en_attente' ? 'selected' : '' }}>En attente</option>
                                        </select>
                                    </div>

                                    <!-- Boutons -->
                                    <div class="col-md-3 d-flex align-items-end gap-2">
                                        <button type="submit" class="btn btn-primary flex-grow-1">
                                            <i class="ri-search-line me-1"></i>Filtrer
                                        </button>
                                        <a href="{{ route('backend.annonces.index') }}" class="btn btn-secondary">
                                            <i class="ri-refresh-line"></i>
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Indicateur de résultats -->
                    @if(request()->hasAny(['type_proprietaire', 'proprietaire_id', 'statut']))
                        <div class="alert alert-info d-flex align-items-center justify-content-between mb-3" role="alert">
                            <div>
                                <i class="ri-information-line me-2"></i>
                                <strong>{{ $annonces->count() }}</strong> résultat(s) trouvé(s) avec les filtres actifs :
                                
                                @if(request('type_proprietaire'))
                                    <span class="badge bg-primary ms-2">
                                        Type: {{ request('type_proprietaire') == 'agence' ? 'Biens de l\'agence' : 'Propriétaires externes' }}
                                    </span>
                                @endif
                                
                                @if(request('proprietaire_id'))
                                    @php
                                        $prop = $proprietaires->find(request('proprietaire_id'));
                                    @endphp
                                    @if($prop)
                                        <span class="badge bg-primary ms-2">
                                            Propriétaire: {{ $prop->name }}
                                        </span>
                                    @endif
                                @endif
                                
                                @if(request('statut'))
                                    <span class="badge bg-primary ms-2">
                                        Statut: {{ ucfirst(str_replace('_', ' ', request('statut'))) }}
                                    </span>
                                @endif
                            </div>
                            <a href="{{ route('backend.annonces.index') }}" class="btn btn-sm btn-light">
                                <i class="ri-close-line me-1"></i>Réinitialiser
                            </a>
                        </div>
                    @else
                        <div class="mb-3">
                            <span class="text-muted"><i class="ri-file-list-line me-1"></i><strong>{{ $annonces->count() }}</strong> annonce(s) au total</span>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table id="buttons-datatables" class="display table table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Image</th>
                                    <th>Référence</th>
                                    <th>Titre</th>
                                    <th>Type</th>
                                    <th>Transaction</th>
                                    <th>Prix</th>
                                    <th>Ville</th>
                                    <th>Propriétaire</th>
                                    <th>Statut</th>
                                    <th>Vedette</th>
                                    <th>Date création</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($annonces as $key => $annonce)
                                    <tr id="row_{{ $annonce->id }}">
                                        <td>{{ ++$key }}</td>
                                        <td>
                                            @if($annonce->hasMedia('image_principale'))
                                                <img src="{{ $annonce->getFirstMediaUrl('image_principale') }}" 
                                                     alt="{{ $annonce->titre }}" 
                                                     class="annonce-image">
                                            @else
                                                <img src="{{ asset('build/images/no-image.png') }}" 
                                                     alt="Pas d'image" 
                                                     class="annonce-image">
                                            @endif
                                        </td>
                                        <td><span class="badge bg-secondary">{{ $annonce->reference }}</span></td>
                                        <td>{{ Str::limit($annonce->titre, 30) }}</td>
                                        <td>
                                            <span class="badge bg-info">{{ $annonce->typeBien->nom ?? 'N/A' }}</span>
                                        </td>
                                        <td>
                                            @if($annonce->type_transaction == 'vente')
                                                <span class="badge bg-success">Vente</span>
                                            @else
                                                <span class="badge bg-primary">Location</span>
                                            @endif
                                        </td>
                                        <td><strong>{{ number_format($annonce->prix, 0, ',', ' ') }} FCFA</strong></td>
                                        <td>{{ $annonce->ville }}</td>
                                        <td>
                                            @if($annonce->est_bien_agence)
                                                <span class="badge bg-success" title="Bien appartenant à l'agence">
                                                    <i class="ri-building-line"></i> Agence
                                                </span>
                                            @elseif($annonce->proprietaire)
                                                <span class="badge bg-dark" title="Bien d'un propriétaire externe">
                                                    {{ $annonce->proprietaire->name }}
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @switch($annonce->statut)
                                                @case('disponible')
                                                    <span class="badge bg-success badge-status">Disponible</span>
                                                    @break
                                                @case('loue')
                                                    <span class="badge bg-warning badge-status">Loué</span>
                                                    @break
                                                @case('vendu')
                                                    <span class="badge bg-danger badge-status">Vendu</span>
                                                    @break
                                                @case('en_attente')
                                                    <span class="badge bg-secondary badge-status">En attente</span>
                                                    @break
                                            @endswitch
                                        </td>
                                        <td>
                                            <form action="{{ route('backend.annonces.toggle-vedette', $annonce->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" 
                                                           {{ $annonce->en_vedette ? 'checked' : '' }}
                                                           onchange="this.form.submit()">
                                                </div>
                                            </form>
                                        </td>
                                        <td>{{ $annonce->created_at->format('d/m/Y') }}</td>
                                        <td>
                                            <div class="dropdown d-inline-block">
                                                <button class="btn btn-soft-secondary btn-sm dropdown" type="button"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="ri-more-fill align-middle"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <a href="{{ route('backend.annonces.show', $annonce->id) }}" class="dropdown-item">
                                                            <i class="ri-eye-fill align-bottom me-2 text-muted"></i>
                                                            Voir
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="{{ route('backend.annonces.edit', $annonce->id) }}" class="dropdown-item">
                                                            <i class="ri-pencil-fill align-bottom me-2 text-muted"></i>
                                                            Modifier
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="#" class="dropdown-item remove-item-btn delete"
                                                           data-id="{{ $annonce->id }}">
                                                            <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i>
                                                            Supprimer
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>

    <script src="{{ URL::asset('build/js/pages/datatables.init.js') }}"></script>

    <script src="{{ URL::asset('build/js/app.js') }}"></script>

    <script>
         window.routeName = "annonces";

         // Gestion des filtres
         $(document).ready(function() {
             // Gérer l'activation/désactivation du filtre propriétaire
             $('#type_proprietaire').on('change', function() {
                 const typeProprietaire = $(this).val();
                 const proprietaireSelect = $('#proprietaire_id');
                 
                 if (typeProprietaire === 'agence') {
                     // Désactiver le filtre propriétaire pour les biens de l'agence
                     proprietaireSelect.prop('disabled', true).val('');
                 } else {
                     // Activer le filtre propriétaire
                     proprietaireSelect.prop('disabled', false);
                 }
             });

             // Initialiser l'état au chargement
             $('#type_proprietaire').trigger('change');
         });
     </script>
@endsection

