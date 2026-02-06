@extends('backend.layouts.master')
@section('title')
    Ventes - Suivi
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
                    <!-- Filtres -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <form action="{{ route('backend.ventes.index') }}" method="GET" class="row g-2">
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
                                        <option value="offre_acceptee"
                                            {{ request('statut') == 'offre_acceptee' ? 'selected' : '' }}>Offre acceptée
                                        </option>
                                        <option value="terminee" {{ request('statut') == 'terminee' ? 'selected' : '' }}>
                                            Terminée</option>
                                        <option value="annulee" {{ request('statut') == 'annulee' ? 'selected' : '' }}>
                                            Annulée</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Client</label>
                                    <select name="client" class="form-select">
                                        <option value="">Tous les clients</option>
                                        @foreach ($clients as $client)
                                            <option value="{{ $client->id }}"
                                                {{ request('client') == $client->id ? 'selected' : '' }}>
                                                {{ $client->username }}
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
                                        <a href="{{ route('backend.ventes.index') }}" class="btn btn-secondary">
                                            <i class="ri-refresh-line"></i>
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="buttons-datatables" class="table table-bordered table-hover dt-responsive nowrap w-100">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Bien</th>
                                    <th>Client</th>
                                    <th>Prix vente</th>
                                    <th>Montant payé</th>
                                    <th>Reste à payer</th>
                                    <th>Commission</th>
                                    <th>Date vente</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ventes as $key => $vente)
                                    <tr id="row_{{$vente->id}}">
                                        <td>{{ ++$key }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if ($vente->annonce->getFirstMediaUrl('images'))
                                                    <img src="{{ $vente->annonce->getFirstMediaUrl('images') }}"
                                                        class="bien-image me-2" alt="Bien">
                                                @endif
                                                <div>
                                                    <div class="fw-semibold">{{ $vente->annonce->titre }}</div>
                                                    <small class="text-muted">{{ $vente->annonce->ville }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div>{{ $vente->client->name }}</div>
                                            <small class="text-muted">{{ $vente->client->email }}</small>
                                        </td>
                                        <td>{{ number_format($vente->prix_vente, 0, ',', ' ') }} FCFA</td>
                                        <td class="text-success">{{ number_format($vente->montantTotal(), 0, ',', ' ') }}
                                            FCFA</td>
                                        <td class="text-danger">{{ number_format($vente->resteAPayer(), 0, ',', ' ') }}
                                            FCFA</td>
                                        <td class="text-info">
                                            @if ($vente->commission_agence)
                                                <div>
                                                    <strong>{{ number_format($vente->calculerCommission(), 0, ',', ' ') }}</strong>
                                                    FCFA
                                                </div>
                                                <small class="text-muted">
                                                    @if ($vente->type_commission === 'pourcentage')
                                                        ({{ $vente->commission_agence }}%)
                                                    @endif
                                                    @if (in_array($vente->statut, ['terminee']))
                                                        <span class="text-success">✓ Perçue</span>
                                                    @else
                                                        <span class="text-warning">En attente</span>
                                                    @endif
                                                </small>
                                            @else
                                                <span class="text-muted">Non configurée</span>
                                            @endif
                                        </td>
                                        <td>{{ $vente->date_vente ? $vente->date_vente->format('d/m/Y') : '-' }}</td>
                                        <td>
                                            {!! $vente->statut_badge !!}
                                        </td>
                                        <td>
                                            <a href="{{ route('backend.ventes.show', $vente) }}"
                                                class="btn btn-sm btn-info" title="Voir détails">
                                                <i class="ri-eye-line"></i>
                                            </a>
                                            <a href="{{ route('backend.ventes.edit', $vente) }}"
                                                class="btn btn-sm btn-primary" title="Modifier">
                                                <i class="ri-edit-line"></i>
                                            </a>
                                            {{-- <form action="{{ route('backend.ventes.destroy', $vente) }}" method="POST"
                                                class="d-inline"
                                                onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette vente ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Supprimer">
                                                    <i class="ri-delete-bin-line"></i>
                                                </button>
                                            </form> --}}

                                             <a href="{{ route('backend.ventes.destroy', $vente) }}"
                                                class="btn btn-sm btn-danger" title="Supprimer" data-confirm-delete="true">
                                                <i class="ri-delete-bin-line"></i>
                                            </a>
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
        window.routeName = "ventes";
    </script>
@endsection


{{-- @section('script')
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
@endsection --}}
