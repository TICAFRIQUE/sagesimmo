@extends('backend.layouts.master')
@section('title')
   Locations - Suivi
@endsection
@section('css')
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
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
                    <div class="table-responsive">
                        <table id="locationsTable" class="table table-bordered table-hover dt-responsive nowrap w-100">
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
                                @forelse($locations as $location)
                                    @php
                                        $prochaineEcheance = $location->echeances()
                                            ->where('statut', '!=', 'payé')
                                            ->orderBy('date_echeance', 'asc')
                                            ->first();
                                    @endphp
                                    <tr>
                                        <td>{{ $location->id }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($location->annonce->getFirstMediaUrl('images'))
                                                    <img src="{{ $location->annonce->getFirstMediaUrl('images') }}" class="bien-image me-2" alt="Bien">
                                                @endif
                                                <div>
                                                    <div class="fw-semibold">{{ $location->annonce->titre }}</div>
                                                    <small class="text-muted">{{ $location->annonce->ville }}</small>
                                                    @if($location->demandeInteret)
                                                        <div><span class="badge bg-info mt-1"><i class="ri-link me-1"></i>Demande #{{ $location->demandeInteret->id }}</span></div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div>{{ $location->locataire->name }}</div>
                                            <small class="text-muted">{{ $location->locataire->email }}</small>
                                        </td>
                                        <td>{{ number_format($location->loyer_mensuel, 0, ',', ' ') }} FCFA</td>
                                        <td>{{ $location->date_debut->format('d/m/Y') }}</td>
                                        <td>
                                            @if($prochaineEcheance)
                                                <div>{{ $prochaineEcheance->date_echeance->format('d/m/Y') }}</div>
                                                @if($prochaineEcheance->statut == 'en_retard')
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
                                            <a href="{{ route('backend.locations.show', $location) }}" class="btn btn-sm btn-info" title="Voir détails">
                                                <i class="ri-eye-line"></i>
                                            </a>
                                            <a href="{{ route('backend.locations.edit', $location) }}" class="btn btn-sm btn-primary" title="Modifier">
                                                <i class="ri-edit-line"></i>
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
                    
                    <div class="mt-3">
                        {{ $locations->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#locationsTable').DataTable({
                responsive: true,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/fr-FR.json'
                }
            });
        });
    </script>
@endsection
