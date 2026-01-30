@extends('backend.layouts.master')
@section('title')
   Demandes d'Intérêt
@endsection
@section('css')
    <!--datatable css-->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <!--datatable responsive css-->
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
@endsection
@section('content')
    @component('backend.components.breadcrumb')
        @slot('li_1')
            Gestion
        @endslot
        @slot('title')
            Demandes d'Intérêt
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Liste des demandes d'intérêt</h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="ri-check-line me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Filtres -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <form action="{{ route('backend.demandes.index') }}" method="GET" class="row g-2">
                                <div class="col-md-4">
                                    <select name="statut" class="form-select">
                                        <option value="">Tous les statuts</option>
                                        <option value="nouvelle" {{ request('statut') == 'nouvelle' ? 'selected' : '' }}>Nouvelle</option>
                                        <option value="visite_planifiee" {{ request('statut') == 'visite_planifiee' ? 'selected' : '' }}>Visite planifiée</option>
                                        <option value="visite_effectuee" {{ request('statut') == 'visite_effectuee' ? 'selected' : '' }}>Visite effectuée</option>
                                        <option value="documents_recus" {{ request('statut') == 'documents_recus' ? 'selected' : '' }}>Documents reçus</option>
                                        <option value="dossier_valide" {{ request('statut') == 'dossier_valide' ? 'selected' : '' }}>Dossier validé</option>
                                        <option value="contrat_genere" {{ request('statut') == 'contrat_genere' ? 'selected' : '' }}>Contrat généré</option>
                                        <option value="paiement_en_attente" {{ request('statut') == 'paiement_en_attente' ? 'selected' : '' }}>Paiement en attente</option>
                                        <option value="paiement_valide" {{ request('statut') == 'paiement_valide' ? 'selected' : '' }}>Paiement validé</option>
                                        <option value="cloture_refus" {{ request('statut') == 'cloture_refus' ? 'selected' : '' }}>Clôturée - Refusée</option>
                                        <option value="cloture_non_interesse" {{ request('statut') == 'cloture_non_interesse' ? 'selected' : '' }}>Clôturée - Non intéressé</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <select name="annonce_id" class="form-select">
                                        <option value="">Toutes les annonces</option>
                                        @foreach($annonces as $annonce)
                                            <option value="{{ $annonce->id }}" {{ request('annonce_id') == $annonce->id ? 'selected' : '' }}>
                                                {{ $annonce->reference }} - {{ $annonce->titre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ri-filter-line"></i> Filtrer
                                    </button>
                                    <a href="{{ route('backend.demandes.index') }}" class="btn btn-secondary">
                                        <i class="ri-refresh-line"></i> Réinitialiser
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="buttons-datatables" class="display table table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Client</th>
                                    <th>Email</th>
                                    <th>Téléphone</th>
                                    <th>Bien</th>
                                    <th>Message</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($demandes as $key => $demande)
                                    <tr>
                                        <td>{{ $demandes->firstItem() + $key }}</td>
                                        <td>{{ $demande->created_at->format('d/m/Y H:i') }}</td>
                                        <td>{{ $demande->user->username }}</td>
                                        <td>{{ $demande->user->email }}</td>
                                        <td>{{ $demande->user->phone ?? 'N/A' }}</td>
                                        <td>
                                            <a href="{{ route('properties.show', $demande->annonce->slug) }}" target="_blank">
                                                {{ $demande->annonce->reference }}
                                            </a>
                                        </td>
                                        <td>
                                            <span data-bs-toggle="tooltip" title="{{ $demande->message }}">
                                                {{ Str::limit($demande->message, 50) }}
                                            </span>
                                        </td>
                                        <td>{!! $demande->statut_badge !!}</td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-light dropdown-toggle" type="button" 
                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="ri-more-2-fill"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('backend.demandes.show', $demande->id) }}">
                                                            <i class="ri-eye-line me-2"></i> Voir détails
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    @if(!$demande->is_cloture)
                                                        <li>
                                                            <a class="dropdown-item text-primary" href="{{ route('backend.demandes.show', $demande->id) }}">
                                                                <i class="ri-settings-3-line me-2"></i> Gérer le workflow
                                                            </a>
                                                        </li>
                                                        <li><hr class="dropdown-divider"></li>
                                                    @endif
                                                    <li>
                                                        <form action="{{ route('backend.demandes.destroy', $demande->id) }}" 
                                                              method="POST" 
                                                              onsubmit="return confirm('Voulez-vous vraiment supprimer cette demande ?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger">
                                                                <i class="ri-delete-bin-line me-2"></i> Supprimer
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-3">
                        {{ $demandes->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#buttons-datatables').DataTable({
                dom: 'Bfrtip',
                buttons: ['copy', 'csv', 'excel', 'print', 'pdf'],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/fr-FR.json'
                }
            });

            // Initialiser les tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
@endsection
