@extends('backend.layouts.master')
@section('title')
    Charges
@endsection
@section('css')
    <!--datatable css-->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <!--datatable responsive css-->
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" rel="stylesheet"
        type="text/css" />
    <link href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
    <!--select2 css-->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" type="text/css" />
@endsection
@section('content')
    @component('backend.components.breadcrumb')
        @slot('li_1')
            Liste
        @endslot
        @slot('title')
            Charges
        @endslot
    @endcomponent



    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h5 class="card-title mb-0">Liste des charges</h5>
                    <a href="{{ route('backend.charges.create') }}" class="btn btn-primary ">Créer
                        une charge</a>
                </div>
                @include('backend.components.alertMessage')
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="buttons-datatables" class="display table table-bordered" style="width:100%">
                            <thead>
                                <tr class="table-light">
                                    <th class="text-center">Référence</th>
                                    <th>Date</th>
                                    <th>Proprietaire</th>
                                    <th>Bien</th>
                                    <th>Type</th>
                                    <th>Description</th>
                                    <th>Montant</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($charges as $key => $charge)
                                    <tr id="row_{{ $charge->id }}">
                                        <td class="text-center"> {{ $charge->reference }} </td>
                                        <td> {{ $charge->date_charge }} </td>
                                        <td><a
                                                href="{{ route('backend.rapports.proprietaire', ['proprietaire_id' => $charge->annonce->proprietaire->id]) }}">{{ $charge->annonce->proprietaire->username }}</a>
                                        </td>
                                        <td> {{ $charge->annonce->titre }} - <span
                                                class="badge bg-info">{{ $charge->annonce->reference }}</span></td>
                                        <td> {{ $charge->type_charge }} </td>
                                        <td> {{ $charge->description }} </td>
                                        <td class="text-end"> {{ number_format($charge->montant, 0, ',', ' ') }} </td>
                                        <td class="text-center">
                                            <div class="dropdown d-inline-block">
                                                <button class="btn btn-soft-secondary btn-sm dropdown" type="button"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="ri-more-fill align-middle"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    {{-- <li><a href="#!" class="dropdown-item"><i
                                                                class="ri-eye-fill align-bottom me-2 text-muted"></i>
                                                            View</a>
                                                    </li> --}}
                                                    <li><a class="dropdown-item"
                                                            href="{{ route('backend.charges.edit', $charge->id) }}"><i
                                                                class="ri-pencil-fill align-bottom me-2 text-muted"></i>
                                                            Modifier</a></li>
                                                    <li>
                                                        <a href="#" class="dropdown-item remove-item-btn delete"
                                                            data-id={{ $charge->id }}>
                                                            <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i>
                                                            Supprimer
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    {{-- <!-- MODAL : ÉDITER UNE CHARGE -->
                                    @include('backend.pages.rapports.charges.modal.edit', [
                                        'charge' => $charge,
                                    ]) --}}
                                @endforeach


                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end row-->


    <!-- MODAL : CRÉER UNE CHARGE -->
    {{-- @include('backend.pages.rapports.charges.modal.create') --}}
@endsection
@section('script')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>

    <script src="{{ URL::asset('build/js/pages/datatables.init.js') }}"></script>

    <script src="{{ URL::asset('build/js/app.js') }}"></script>

    <!--select2 cdn-->

    <script>
        window.routeName = "charges";
    </script>
@endsection
