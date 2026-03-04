@extends('backend.layouts.master')
@section('title')
    Modifier l'administrateur
@endsection
@section('css')
    <style>
        .avatar-preview {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #e2e8f0;
        }
        .role-card {
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        .role-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .role-card.selected {
            border-color: #0d6efd;
            background-color: #e7f1ff;
        }
        .role-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
    </style>
@endsection
@section('content')
    @component('backend.components.breadcrumb')
        @slot('li_1')
            <a href="{{ route('admin-register.index') }}">Administrateurs</a>
        @endslot
        @slot('title')
            Modifier l'administrateur
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <form class="needs-validation" action="{{ route('admin-register.update', $data_admin->id) }}" method="POST" 
                  enctype="multipart/form-data" novalidate>
                @csrf
                @method('POST')

                <!-- Sélection du rôle -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Rôle <span class="text-danger">*</span></h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            @php
                                $userRole = $data_admin->roles->first();
                            @endphp
                            @foreach($data_role as $role)
                                @php
                                    $isSelected = $userRole && $userRole->name == $role->name;
                                @endphp
                                <div class="col-md-3">
                                    <div class="card role-card text-center p-3 {{ $isSelected ? 'selected' : '' }}" data-role="{{ $role->name }}">
                                        <div class="card-body">
                                            @switch($role->name)
                                                @case('admin')
                                                    <i class="ri-admin-line role-icon text-danger"></i>
                                                    @break
                                                @case('superadmin')
                                                    <i class="ri-shield-star-line role-icon text-warning"></i>
                                                    @break
                                                @case('developpeur')
                                                    <i class="ri-code-line role-icon text-info"></i>
                                                    @break
                                                @default
                                                    <i class="ri-user-settings-line role-icon text-secondary"></i>
                                            @endswitch
                                            <h5>{{ ucfirst($role->name) }}</h5>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @php
                            $currentRole = old('role', $userRole->name ?? '');
                        @endphp
                        <input type="hidden" name="role" id="role" value="{{ $currentRole }}" required>
                        @error('role')
                            <div class="text-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Informations personnelles -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Informations personnelles</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="username" class="form-label">Nom complet <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('username') is-invalid @enderror" 
                                           id="username" name="username" value="{{ old('username', $data_admin->username) }}" required>
                                    @error('username')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Téléphone</label>
                                    <input type="number" class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" name="phone" value="{{ old('phone', $data_admin->phone) }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email', $data_admin->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password" class="form-label">Nouveau mot de passe</label>
                                    <div class="position-relative">
                                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                               id="password" name="password">
                                        <button type="button" class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted" 
                                                style="padding: 0.5rem 0.75rem;" onclick="togglePassword('password')">
                                            <i class="ri-eye-off-line" id="password-icon"></i>
                                        </button>
                                    </div>
                                    @error('password')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Laissez vide pour conserver le mot de passe actuel</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label">Confirmer le nouveau mot de passe</label>
                                    <div class="position-relative">
                                        <input type="password" class="form-control" 
                                               id="password_confirmation" name="password_confirmation">
                                        <button type="button" class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted" 
                                                style="padding: 0.5rem 0.75rem;" onclick="togglePassword('password_confirmation')">
                                            <i class="ri-eye-off-line" id="password_confirmation-icon"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="avatar" class="form-label">Photo de profil</label>
                                    <input type="file" class="form-control @error('avatar') is-invalid @enderror" 
                                           id="avatar" name="avatar" accept="image/*">
                                    @error('avatar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="mt-3 text-center">
                                        @if($data_admin->hasMedia('avatar'))
                                            <img id="avatar-preview" class="avatar-preview" 
                                                 src="{{ $data_admin->getFirstMediaUrl('avatar') }}" 
                                                 alt="{{ $data_admin->username }}">
                                        @else
                                            <img id="avatar-preview" class="avatar-preview" src="" alt="Aperçu" style="display: none;">
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Boutons d'action -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin-register.index') }}" class="btn btn-light">
                                <i class="ri-arrow-left-line me-1"></i> Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="ri-save-line me-1"></i> Mettre à jour
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Gestion de la sélection du rôle
            const roleCards = document.querySelectorAll('.role-card');
            const roleInput = document.getElementById('role');

            roleCards.forEach(card => {
                card.addEventListener('click', function() {
                    roleCards.forEach(c => c.classList.remove('selected'));
                    this.classList.add('selected');
                    roleInput.value = this.dataset.role;
                });
            });

            // Prévisualisation de l'avatar
            const avatarInput = document.getElementById('avatar');
            const avatarPreview = document.getElementById('avatar-preview');

            avatarInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        avatarPreview.src = e.target.result;
                        avatarPreview.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                }
            });

            // Validation du formulaire
            const form = document.querySelector('.needs-validation');
            form.addEventListener('submit', function(e) {
                if (!form.checkValidity()) {
                    e.preventDefault();
                    e.stopPropagation();
                }
                
                if (!roleInput.value) {
                    e.preventDefault();
                    alert('Veuillez sélectionner un rôle');
                    return false;
                }
                
                form.classList.add('was-validated');
            }, false);
        });

        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = document.getElementById(fieldId + '-icon');
            
            if (field.type === 'password') {
                field.type = 'text';
                icon.className = 'ri-eye-line';
            } else {
                field.type = 'password';
                icon.className = 'ri-eye-off-line';
            }
        }
    </script>
@endsection
