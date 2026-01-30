@extends('backend.layouts.master')
@section('title')
   Ventes - Suivi
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
            Ventes
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Liste des ventes</h5>
                    <a href="{{ route('backend.ventes.create') }}" class="btn btn-primary">
                        <i class="ri-add-line align-middle me-1"></i> Nouvelle vente
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="ventesTable" class="table table-bordered table-hover dt-responsive nowrap w-100">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Bien</th>
                                    <th>Client</th>
                                    <th>Prix vente</th>
                                    <th>Montant payé</th>
                                    <th>Reste à payer</th>
                                    <th>Date vente</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ventes as $vente)
                                    <tr>
                                        <td>{{ $vente->id }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($vente->annonce->getFirstMediaUrl('images'))
                                                    <img src="{{ $vente->annonce->getFirstMediaUrl('images') }}" class="bien-image me-2" alt="Bien">
                                                @endif
                                                <div>
                                                    <div class="fw-semibold">{{ $vente->annonce->titre }}</div>
                                                    <small class="text-muted">{{ $vente->annonce->ville }}</small>
                                                    @if($vente->demandeInteret)
                                                        <div><span class="badge bg-info mt-1"><i class="ri-link me-1"></i>Demande #{{ $vente->demandeInteret->id }}</span></div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div>{{ $vente->client->name }}</div>
                                            <small class="text-muted">{{ $vente->client->email }}</small>
                                        </td>
                                        <td>{{ number_format($vente->prix_vente, 0, ',', ' ') }} FCFA</td>
                                        <td class="text-success">{{ number_format($vente->montantTotal(), 0, ',', ' ') }} FCFA</td>
                                        <td class="text-danger">{{ number_format($vente->resteAPayer(), 0, ',', ' ') }} FCFA</td>
                                        <td>{{ $vente->date_vente->format('d/m/Y') }}</td>
                                        <td>
                                            @if($vente->statut == 'completé')
                                                <span class="badge bg-success">Complété</span>
                                            @elseif($vente->statut == 'en_cours')
                                                <span class="badge bg-warning">En cours</span>
                                            @else
                                                <span class="badge bg-danger">Annulé</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('backend.ventes.show', $vente) }}" class="btn btn-sm btn-info" title="Voir détails">
                                                <i class="ri-eye-line"></i>
                                            </a>
                                            <a href="{{ route('backend.ventes.edit', $vente) }}" class="btn btn-sm btn-primary" title="Modifier">
                                                <i class="ri-edit-line"></i>
                                            </a>
                                            <form action="{{ route('backend.ventes.destroy', $vente) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette vente ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Supprimer">
                                                    <i class="ri-delete-bin-line"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">Aucune vente enregistrée</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3">
                        {{ $ventes->links() }}
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
            $('#ventesTable').DataTable({
                responsive: true,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/fr-FR.json'
                }
            });
        });
    </script>
@endsection
