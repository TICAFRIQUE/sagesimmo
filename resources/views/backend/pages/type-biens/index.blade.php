@extends('backend.layouts.master')
@section('title')
    Types de biens
@endsection

@section('css')
    <!--datatable css-->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <!--datatable responsive css-->
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" rel="stylesheet"
        type="text/css" />
    <link href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
@endsection
@section('content')
    @component('backend.components.breadcrumb')
        @slot('li_1')
            Gestion immobilière
        @endslot
        @slot('title')
            Types de biens
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                @include('backend.components.alertMessage')

                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Liste des types de biens</h5>
                    <a href="{{ route('backend.type-biens.create') }}" class="btn btn-primary">
                        <i class="ri-add-line me-1"></i> Nouveau type
                    </a>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped" id="buttons-datatables">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nom</th>
                                <th>Slug</th>
                                <th>Ordre</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($typeBiens as $typeBien)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><strong>{{ $typeBien->nom }}</strong></td>
                                    <td><code>{{ $typeBien->slug }}</code></td>
                                    <td>{{ $typeBien->ordre }}</td>
                                    <td>
                                        @if ($typeBien->actif)
                                            <span class="badge bg-success">Actif</span>
                                        @else
                                            <span class="badge bg-danger">Inactif</span>
                                        @endif
                                    </td>

                                    <td>
                                        <div class="dropdown d-inline-block">
                                            <button class="btn btn-soft-secondary btn-sm dropdown" type="button"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ri-more-fill align-middle"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">

                                                <li>
                                                    <a href="{{ route('backend.type-biens.edit', $typeBien->id) }}"
                                                        class="dropdown-item">
                                                        <i class="ri-pencil-fill align-bottom me-2 text-muted"></i>
                                                        Modifier
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#" class="dropdown-item remove-item-btn delete"
                                                        data-id="{{ $typeBien->id }}">
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
        window.routeName = "type-biens";
    </script>
@endsection
