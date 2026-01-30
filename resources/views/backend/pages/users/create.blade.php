@extends('backend.layouts.master')
@section('title')
    Créer un utilisateur
@endsection
@section('css')
    <style>
        .avatar-preview {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #e2e8f0;
            display: none;
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
        .preview-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 15px;
        }
        .preview-item {
            position: relative;
            display: inline-block;
        }
        .preview-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #e2e8f0;
        }
        .preview-file {
            width: 100px;
            height: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8f9fa;
            border-radius: 8px;
            border: 2px solid #e2e8f0;
        }
        .btn-remove-preview {
            position: absolute;
            top: -8px;
            right: -8px;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: #ef4444;
            color: white;
            border: 2px solid white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3);
        }
        .btn-remove-preview:hover {
            background: #dc2626;
            transform: scale(1.1);
        }
        .preview-filename {
            max-width: 100px;
            font-size: 0.75rem;
            text-align: center;
            margin-top: 5px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
    </style>
@endsection
@section('content')
    @component('backend.components.breadcrumb')
        @slot('li_1')
            <a href="{{ route('backend.users.index') }}">Utilisateurs</a>
        @endslot
        @slot('title')
            Créer un utilisateur
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <form class="needs-validation" action="{{ route('backend.users.store') }}" method="POST" 
                  enctype="multipart/form-data" novalidate>
                @csrf

                <!-- Sélection du type d'utilisateur -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Type d'utilisateur <span class="text-danger">*</span></h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            @foreach($roles as $role)
                                <div class="col-md-3">
                                    <div class="card role-card text-center p-3 {{ old('role_id') == $role->id ? 'selected' : '' }}" data-role="{{ $role->id }}">
                                        <div class="card-body">
                                            @switch($role->name)
                                                @case('locataire')
                                                    <i class="ri-home-heart-line role-icon text-info"></i>
                                                    @break
                                                @case('proprietaire')
                                                    <i class="ri-building-line role-icon text-success"></i>
                                                    @break
                                                @case('acheteur')
                                                    <i class="ri-shopping-cart-line role-icon text-primary"></i>
                                                    @break
                                                @case('admin')
                                                    <i class="ri-admin-line role-icon text-danger"></i>
                                                    @break
                                                @default
                                                    <i class="ri-user-line role-icon text-secondary"></i>
                                            @endswitch
                                            <h5>{{ ucfirst($role->name) }}</h5>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <input type="hidden" name="role_id" id="role_id" value="{{ old('role_id') }}" required>
                        @error('role_id')
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
                                           id="username" name="username" value="{{ old('username') }}" required>
                                    @error('username')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Téléphone <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" name="phone" value="{{ old('phone') }}" required>
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
                                           id="email" name="email" value="{{ old('email') }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password" class="form-label">Mot de passe <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                           id="password" name="password" required>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Minimum 6 caractères</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label">Confirmer le mot de passe <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" 
                                           id="password_confirmation" name="password_confirmation" required>
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
                                        <img id="avatar-preview" class="avatar-preview" src="" alt="Aperçu">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Documents -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Documents</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="piece_identite" class="form-label">
                                        <i class="ri-id-card-line me-1"></i>Pièce d'identité
                                    </label>
                                    <input type="file" class="form-control @error('piece_identite.*') is-invalid @enderror" 
                                           id="piece_identite" name="piece_identite[]" 
                                           accept="image/*,.pdf" multiple>
                                    @error('piece_identite.*')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Formats acceptés: JPG, PNG, PDF (Max: 5MB chacun)</small>
                                    <div id="piece_identite_preview" class="preview-container"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="documents" class="form-label">
                                        <i class="ri-file-text-line me-1"></i>Autres documents
                                    </label>
                                    <input type="file" class="form-control @error('documents.*') is-invalid @enderror" 
                                           id="documents" name="documents[]" 
                                           accept="image/*,.pdf,.doc,.docx" multiple>
                                    @error('documents.*')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Formats acceptés: JPG, PNG, PDF, DOC, DOCX (Max: 5MB chacun)</small>
                                    <div id="documents_preview" class="preview-container"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Boutons d'action -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('backend.users.index') }}" class="btn btn-light">
                                <i class="ri-arrow-left-line me-1"></i> Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="ri-save-line me-1"></i> Enregistrer
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
            // Gestion de la sélection du type d'utilisateur
            const roleCards = document.querySelectorAll('.role-card');
            const roleInput = document.getElementById('role_id');

            // Pré-sélection si ancienne valeur existe
            if (roleInput.value) {
                const selectedCard = document.querySelector(`.role-card[data-role="${roleInput.value}"]`);
                if (selectedCard) {
                    selectedCard.classList.add('selected');
                }
            }

            roleCards.forEach(card => {
                card.addEventListener('click', function() {
                    // Retirer la sélection de toutes les cartes
                    roleCards.forEach(c => c.classList.remove('selected'));
                    
                    // Ajouter la sélection à la carte cliquée
                    this.classList.add('selected');
                    
                    // Mettre à jour la valeur du champ caché
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

            // Prévisualisation des pièces d'identité
            handleFilePreview('piece_identite', 'piece_identite_preview');
            
            // Prévisualisation des autres documents
            handleFilePreview('documents', 'documents_preview');

            function handleFilePreview(inputId, previewId) {
                const input = document.getElementById(inputId);
                const previewContainer = document.getElementById(previewId);
                let filesArray = [];

                input.addEventListener('change', function(e) {
                    const newFiles = Array.from(e.target.files);
                    filesArray = [...filesArray, ...newFiles];
                    updatePreview();
                });

                function updatePreview() {
                    previewContainer.innerHTML = '';
                    
                    filesArray.forEach((file, index) => {
                        const previewItem = document.createElement('div');
                        previewItem.className = 'preview-item';

                        if (file.type.startsWith('image/')) {
                            const img = document.createElement('img');
                            img.className = 'preview-image';
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                img.src = e.target.result;
                            };
                            reader.readAsDataURL(file);
                            previewItem.appendChild(img);
                        } else {
                            const fileDiv = document.createElement('div');
                            fileDiv.className = 'preview-file';
                            const icon = document.createElement('i');
                            if (file.type.includes('pdf')) {
                                icon.className = 'ri-file-pdf-line fs-1 text-danger';
                            } else {
                                icon.className = 'ri-file-text-line fs-1 text-primary';
                            }
                            fileDiv.appendChild(icon);
                            previewItem.appendChild(fileDiv);
                        }

                        const removeBtn = document.createElement('button');
                        removeBtn.type = 'button';
                        removeBtn.className = 'btn-remove-preview';
                        removeBtn.innerHTML = '<i class="ri-close-line"></i>';
                        removeBtn.onclick = function() {
                            filesArray.splice(index, 1);
                            updateFileInput();
                            updatePreview();
                        };
                        previewItem.appendChild(removeBtn);

                        const fileName = document.createElement('div');
                        fileName.className = 'preview-filename text-muted';
                        fileName.textContent = file.name;
                        fileName.title = file.name;
                        previewItem.appendChild(fileName);

                        previewContainer.appendChild(previewItem);
                    });
                }

                function updateFileInput() {
                    const dataTransfer = new DataTransfer();
                    filesArray.forEach(file => dataTransfer.items.add(file));
                    input.files = dataTransfer.files;
                }
            }

            // Validation du formulaire
            const form = document.querySelector('.needs-validation');
            form.addEventListener('submit', function(e) {
                if (!form.checkValidity()) {
                    e.preventDefault();
                    e.stopPropagation();
                }
                
                // Vérifier que le rôle est sélectionné
                if (!roleInput.value) {
                    e.preventDefault();
                    alert('Veuillez sélectionner un type d\'utilisateur');
                    return false;
                }
                
                form.classList.add('was-validated');
            }, false);
        });
    </script>
@endsection
