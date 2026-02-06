@extends('backend.layouts.master')
@section('title')
    Locations - Suivi
@endsection
@section('css')
    <!--datatable css-->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <!--datatable responsive css-->
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" rel="stylesheet"
        type="text/css" />
    <link href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
    <style>
        .bien-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
        }
    </style>
@endsection
@section('content')
    @component('backend.components.breadcrumb')
        @slot('li_1')
            Suivi Location/Vente
        @endslot
        @slot('title')
            Locations
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Liste des locations</h5>
                    <a href="{{ route('backend.locations.create') }}" class="btn btn-primary">
                        <i class="ri-add-line align-middle me-1"></i> Nouvelle location
                    </a>
                </div>
                <div class="card-body">
                    <!-- Filtres -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <form action="{{ route('backend.locations.index') }}" method="GET" class="row g-2">
                                <div class="col-md-3">
                                    <label class="form-label">Statut</label>
                                    <select name="statut" class="form-select">
                                        <option value="">Tous les statuts</option>
                                        <option value="demande_client"
                                            {{ request('statut') == 'demande_client' ? 'selected' : '' }}>Demande client
                                        </option>
                                        <option value="brouillon" {{ request('statut') == 'brouillon' ? 'selected' : '' }}>
                                            Brouillon</option>
                                        <option value="fiche_envoyee"
                                            {{ request('statut') == 'fiche_envoyee' ? 'selected' : '' }}>Fiche envoyée
                                        </option>
                                        <option value="visite_planifiee"
                                            {{ request('statut') == 'visite_planifiee' ? 'selected' : '' }}>Visite planifiée
                                        </option>
                                        <option value="en_attente_paiement"
                                            {{ request('statut') == 'en_attente_paiement' ? 'selected' : '' }}>En attente
                                            paiement</option>
                                        <option value="actif" {{ request('statut') == 'actif' ? 'selected' : '' }}>Actif
                                        </option>
                                        <option value="resilie" {{ request('statut') == 'resilie' ? 'selected' : '' }}>
                                            Résilié</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Locataire</label>
                                    <select name="locataire" class="form-select">
                                        <option value="">Tous les locataires</option>
                                        @foreach ($locataires as $locataire)
                                            <option value="{{ $locataire->id }}"
                                                {{ request('locataire') == $locataire->id ? 'selected' : '' }}>
                                                {{ $locataire->username }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Date début</label>
                                    <input type="date" name="date_debut" class="form-control"
                                        value="{{ request('date_debut') }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Date fin</label>
                                    <input type="date" name="date_fin" class="form-control"
                                        value="{{ request('date_fin') }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">&nbsp;</label>
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="ri-search-line me-1"></i>Filtrer
                                        </button>
                                        <a href="{{ route('backend.locations.index') }}" class="btn btn-secondary">
                                            <i class="ri-refresh-line"></i>
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="buttons-datatables" class="table table-bordered table-hover dt-responsive nowrap w-100"
                            style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Bien</th>
                                    <th>Locataire</th>
                                    <th>Loyer mensuel</th>
                                    <th>Date début</th>
                                    <th>Prochaine échéance</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($locations as $key => $location)
                                    @php
                                        $prochaineEcheance = $location
                                            ->echeances()
                                            ->where('statut', '!=', 'payé')
                                            ->orderBy('date_echeance', 'asc')
                                            ->first();
                                    @endphp
                                    <tr id="row_{{ $location->id }}">
                                        <td>{{ ++$key }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if ($location->annonce->getFirstMediaUrl('images'))
                                                    <img src="{{ $location->annonce->getFirstMediaUrl('images') }}"
                                                        class="bien-image me-2" alt="Bien">
                                                @endif
                                                <div>
                                                    <div class="fw-semibold">{{ $location->annonce->titre }}</div>
                                                    <small class="text-muted">{{ $location->annonce->ville }}</small>
                                                </div>
                                            </div>
                                        </td>

                                        <td>
                                            <div>{{ $location->locataire->name }}</div>
                                            <small class="text-muted">{{ $location->locataire->email }}</small>
                                        </td>
                                        <td>{{ $location->loyer_mensuel ? number_format($location->loyer_mensuel, 0, ',', ' ') . ' FCFA' : 'Non configuré' }}
                                        </td>
                                        <td>{{ $location->date_debut ? $location->date_debut->format('d/m/Y') : 'Non définie' }}
                                        </td>
                                        <td>
                                            @if ($prochaineEcheance)
                                                <div>{{ $prochaineEcheance->date_echeance->format('d/m/Y') }}</div>
                                                @if ($prochaineEcheance->statut == 'en_retard')
                                                    <span class="badge bg-danger">En retard</span>
                                                @elseif($prochaineEcheance->statut == 'impayé')
                                                    <span class="badge bg-warning">Impayé</span>
                                                @elseif($prochaineEcheance->statut == 'partiel')
                                                    <span class="badge bg-info">Partiel</span>
                                                @else
                                                    <span class="badge bg-secondary">En attente</span>
                                                @endif
                                            @else
                                                <span class="text-muted">Aucune</span>
                                            @endif
                                        </td>
                                        <td>
                                            {!! $location->statut_badge !!}
                                        </td>
                                        <td>
                                            <a href="{{ route('backend.locations.show', $location) }}"
                                                class="btn btn-sm btn-info" title="Voir détails">
                                                <i class="ri-eye-line"></i>
                                            </a>
                                            <a href="{{ route('backend.locations.edit', $location) }}"
                                                class="btn btn-sm btn-primary" title="Modifier">
                                                <i class="ri-edit-line"></i>
                                            </a>

                                            <a href="{{ route('backend.locations.destroy', $location) }}"
                                                data-confirm-delete="true" class="btn btn-sm btn-danger" title="Supprimer">
                                                <i class="ri-delete-bin-line"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">Aucune location enregistrée</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- <div class="mt-3">
                        {{ $locations->links() }}
                    </div> --}}
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
        window.routeName = "locations";
    </script>
@endsection
