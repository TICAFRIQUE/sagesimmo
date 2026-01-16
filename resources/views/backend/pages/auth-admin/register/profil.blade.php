@extends('backend.layouts.master')
@section('title')
    Profil Administrateur
@endsection
@section('content')
    <div class="row">
        <div class="col-xxl-12 ">
            <div class="card mt-xxl-n5">
                <div class="card-header mt-5">
                    <ul class="nav nav-tabs-custom rounded card-header-tabs border-bottom-0" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#personalDetails" role="tab">
                                <i class="fas fa-home"></i> Mes informations
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#privacy" role="tab">
                                <i class="far fa-envelope"></i> Modifier mon mot de passe
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body p-4">
                    <div class="tab-content">
                        <div class="tab-pane active" id="personalDetails" role="tabpanel">
                            <form class="needs-validation" novalidate method="POST"
                                action="{{ route('admin-register.update', Auth::user()->id) }}"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3">
                                    <label for="username" class="form-label">Nom utilisateur</label>
                                    <input type="text" value="{{ $data_admin['username'] }}" name="username"
                                        class="form-control" id="username" required>
                                </div>


                                <div class="mb-3">
                                    <label for="username" class="form-label">Email</label>
                                    <input type="email" value="{{ $data_admin['email'] }}" name="email"
                                        class="form-control" id="username" required>
                                </div>

                                <div class="mb-3">
                                    <label for="username" class="form-label">Telephone</label>
                                    <input type="number" value="{{ $data_admin['phone'] }}" name="phone"
                                        class="form-control" id="username">
                                </div>
                                <input type="text" name="role" value="{{$data_admin['roles'][0]['name']}}" hidden>

                                {{-- <div class="mb-3">
                                    <label for="username" class="form-label">Mot de passe</label>
                                    <input type="password" name="password" class="form-control" id="username" required>
                                </div> --}}

                                {{-- <div class="mb-3">
                                    <label for="username" class="form-label">Role</label>
                                    <select class="form-control" name="role" id="" required>
                                        <option disabled selected value>Selectionner...</option>
                                        @foreach ($data_role as $role)
                                            <option value="{{ $role['name'] }}"
                                                {{ $role['id'] == $data_admin['roles'][0]['id'] ? 'selected' : '' }}>
                                                {{ $role['name'] }} </option>
                                        @endforeach
                                    </select>
                                </div> --}}

                                <div class="mt-3">
                                    <button class="btn btn-success w-100" type="submit">Modifier</button>
                                </div>


                            </form>

                        </div>
                        <!--end tab-pane-->


                        <div class="tab-pane" id="privacy" role="tabpanel">
                            <div class="mb-4 pb-2">
                                <form class="needs-validation" novalidate method="POST"
                                    action="{{ route('admin-register.new-password') }}"
                                    enctype="multipart/form-data">
                                    @csrf

                                    <div class="mb-3">
                                        <label for="username" class="form-label">Ancien mot de passe</label>
                                        <input type="password" name="old_password" class="form-control" id="username" required>
                                    </div>


                                     <div class="mb-3">
                                        <label for="username" class="form-label">Nouveau mot de passe</label>
                                        <input type="password" name="new_password" class="form-control" id="username" required>
                                    </div>



                                    <div class="mt-3">
                                        <button class="btn btn-success w-100" type="submit">Modifier</button>
                                    </div>


                                </form>

                            </div>
                        </div>
                        <!--end tab-pane-->
                    </div>
                </div>
            </div>
        </div>
        <!--end col-->
    </div>
    <!--end row-->
@endsection
@section('script')
    <script src="{{ URL::asset('build/js/pages/profile-setting.init.js') }}"></script>
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
