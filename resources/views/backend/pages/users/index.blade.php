@extends('backend.layouts.master')
@section('title')
    Gestion des Utilisateurs
@endsection
@section('css')
    <!--datatable css-->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <!--datatable responsive css-->
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" rel="stylesheet"
        type="text/css" />
    <link href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
    <style>
        .user-avatar {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 50%;
        }

        .badge-role {
            font-size: 0.75rem;
            padding: 0.35em 0.65em;
        }
    </style>
@endsection
@section('content')
    @component('backend.components.breadcrumb')
        @slot('li_1')
            Gestion
        @endslot
        @slot('title')
            Utilisateurs
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Liste des utilisateurs</h5>
                    <a href="{{ route('backend.users.create') }}" class="btn btn-primary">
                        <i class="ri-user-add-line align-middle me-1"></i> Nouvel utilisateur
                    </a>
                </div>

                @include('backend.components.alertMessage')
                <div class="card-body">
                    <!-- Filtres -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <form method="GET" action="{{ route('backend.users.index') }}">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Type d'utilisateur</label>
                                        <select name="role" class="form-select">
                                            <option value="tous" {{ request('role') == 'tous' ? 'selected' : '' }}>Tous
                                            </option>
                                            <option value="locataire"
                                                {{ request('role') == 'locataire' ? 'selected' : '' }}>Locataire</option>
                                            <option value="proprietaire"
                                                {{ request('role') == 'proprietaire' ? 'selected' : '' }}>Propriétaire
                                            </option>
                                            <option value="acheteur" {{ request('role') == 'acheteur' ? 'selected' : '' }}>
                                                Acheteur</option>
                                            <option value="prospect" {{ request('role') == 'prospect' ? 'selected' : '' }}>
                                                Prospect</option>
                                            {{-- <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Administrateur</option> --}}
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Rechercher</label>
                                        <input type="text" name="search" class="form-control"
                                            placeholder="Nom, email ou téléphone..." value="{{ request('search') }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">&nbsp;</label>
                                        <button type="submit" class="btn btn-success w-100">
                                            <i class="ri-search-line me-1"></i> Filtrer
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle" id="buttons-datatables"
                            style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    {{-- <th>Avatar</th> --}}
                                    <th>Nom</th>
                                    <th>Email</th>
                                    <th>Téléphone</th>
                                    <th>Type</th>
                                    <th>Suivi par</th>
                                    <th>Date création</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $key => $user)
                                    <tr>
                                        <td>{{ ++$key }}</td>
                                        {{-- <td class="text-center">
                                            @if ($user->hasMedia('avatar'))
                                                <img src="{{ $user->getFirstMediaUrl('avatar') }}"
                                                    alt="{{ $user->username }}" class="user-avatar">
                                            @else
                                                <div class="avatar-sm rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center"
                                                    style="width: 40px; height: 40px;">
                                                    <span class="fw-bold">{{ substr($user->username, 0, 1) }}</span>
                                                </div>
                                            @endif
                                        </td> --}}
                                        <td><strong>{{ $user->username }}</strong></td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->phone }}</td>
                                        <td>
                                            @php
                                                $userRole = $user->roles->first();
                                            @endphp
                                            @if ($userRole)
                                                @switch($userRole->name)
                                                    @case('locataire')
                                                        <span class="badge bg-info badge-role">
                                                            <i class="ri-home-heart-line me-1"></i>Locataire
                                                        </span>
                                                    @break

                                                    @case('proprietaire')
                                                        <span class="badge bg-success badge-role">
                                                            <i class="ri-building-line me-1"></i>Propriétaire
                                                        </span>
                                                    @break

                                                    @case('acheteur')
                                                        <span class="badge bg-primary badge-role">
                                                            <i class="ri-shopping-cart-line me-1"></i>Acheteur
                                                        </span>
                                                    @break

                                                    @case('admin')
                                                        <span class="badge bg-danger badge-role">
                                                            <i class="ri-admin-line me-1"></i>Admin
                                                        </span>
                                                    @break

                                                    @default
                                                        <span class="badge bg-secondary badge-role">
                                                            {{ ucfirst($userRole->name) }}
                                                        </span>
                                                @endswitch
                                            @else
                                                <span class="badge bg-secondary badge-role">Aucun rôle</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="#" class="text-capitalize" data-bs-toggle="modal" data-bs-target="#commercialModal{{ $user->id }}">
                                                {{ $user->commercial ? $user->commercial->username : '-' }}
                                            </a>
                                        </td>
                                        <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-soft-secondary" type="button"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="ri-more-fill"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <a class="dropdown-item"
                                                            href="{{ route('backend.users.show', $user->id) }}">
                                                            <i class="ri-eye-line align-bottom me-2 text-muted"></i> Voir
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item"
                                                            href="{{ route('backend.users.edit', $user->id) }}">
                                                            <i class="ri-pencil-line align-bottom me-2 text-muted"></i>
                                                            Modifier
                                                        </a>
                                                    </li>
                                                    <li class="dropdown-divider"></li>

                                                    <li>
                                                        <a href="#" class="dropdown-item remove-item-btn delete"
                                                            data-id="{{ $user->id }}">
                                                            <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i>
                                                            Supprimer
                                                        </a>
                                                       
                                                    </li>

                                                </ul>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Modal Commercial -->
                                    @include('backend.pages.users.partials.commercialModal')
                                @endforeach
                                @if ($users->isEmpty())
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="ri-user-line display-4"></i>
                                                <p class="mt-2">Aucun utilisateur trouvé</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    {{-- <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            Affichage de {{ $users->firstItem() ?? 0 }} à {{ $users->lastItem() ?? 0 }} sur {{ $users->total() }} utilisateurs
                        </div>
                        <div>
                            {{ $users->links() }}
                        </div>
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
        window.routeName = "users";
    </script>
@endsection

{{-- @section('script')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
@endsection --}}
