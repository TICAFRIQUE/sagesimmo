@extends('backend.layouts.master')
@section('title')
    Parametre
@endsection
@section('content')
    {{-- <div class="position-relative mx-n4 mt-n4">
        <div class="profile-wid-bg profile-setting-img">
            <img src="{{ URL::asset('build/images/profile-bg.jpg') }}" class="profile-wid-img" alt="">
            <div class="overlay-content">
                <div class="text-end p-3">
                    <div class="p-0 ms-auto rounded-circle profile-photo-edit">
                        <input id="profile-foreground-img-file-input" type="file"
                            class="profile-foreground-img-file-input">
                        <label for="profile-foreground-img-file-input" class="profile-photo-edit btn btn-light">
                            <i class="ri-image-edit-line align-bottom me-1"></i> Change Cover
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}

    <style>
        .settings-container {
            background-color: #f8f9fa;
            min-height: 100vh;
        }

        .nav-tabs-custom .nav-link {
            border-radius: 10px 10px 0 0;
            transition: all 0.3s ease;
        }

        .nav-tabs-custom .nav-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
        }

        .form-control {
            border-radius: 8px;
            border: 1.5px solid #e3e6f0;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #5a67d8;
            box-shadow: 0 0 0 0.2rem rgba(90, 103, 216, 0.25);
        }

        .image-preview-container {
            position: relative;
            display: inline-block;
        }

        .image-preview {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 12px;
            border: 3px solid #e3e6f0;
            transition: all 0.3s ease;
        }

        .image-preview:hover {
            border-color: #5a67d8;
            transform: scale(1.05);
        }

        .social-input-group {
            background: white;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 15px;
            border: 1px solid #e3e6f0;
            transition: all 0.3s ease;
        }

        .social-input-group:hover {
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border-color: #5a67d8;
        }

        .settings-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .settings-table th,
        .settings-table td {
            padding: 15px 20px;
            text-align: left;
            border-bottom: 1px solid #e3e6f0;
        }

        .settings-table th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: 600;
        }

        .settings-table tbody tr:hover {
            background-color: #f8f9fa;
            transform: scale(1.02);
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 8px;
            padding: 12px 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        .card {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border: none;
        }

        .card-header {
            background: transparent;
            border-bottom: 1px solid #e3e6f0;
        }

        .maintenance-toggle {
            background: white;
            border-radius: 12px;
            padding: 20px;
            border: 1px solid #e3e6f0;
            margin-bottom: 20px;
        }

        .error-message {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 5px;
            display: none;
        }

        .form-group.error .form-control {
            border-color: #dc3545;
        }

        .form-group.error .error-message {
            display: block;
        }

        @media (max-width: 768px) {
            .image-preview {
                width: 80px;
                height: 80px;
            }
            
            .social-input-group {
                padding: 10px;
            }
            
            .settings-table th,
            .settings-table td {
                padding: 10px;
                font-size: 0.875rem;
            }
        }
    </style>

    <div class="row">
        <div class="col-xxl-12  mt-5">
            <div class="card mt-xxl-n5">
                <div class="card-header">
                    <ul class="nav nav-tabs-custom rounded card-header-tabs border-bottom-0" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#personalDetails" role="tab">
                                <i class="fas fa-home"></i> Informations du site
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#privacy" role="tab">
                                <i class="far fa-envelope"></i> Application
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#backup" role="tab">
                                <i class="far fa-envelope"></i> Backups
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body p-4">
                    <div class="tab-content">
                        <div class="tab-pane active" id="personalDetails" role="tabpanel">
                            <form action="{{ route('parametre.store') }}" method="post" enctype="multipart/form-data">
                                @csrf

                                <div class="row">

                                    <!-- ========== Start Section Images ========== -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <h5 class="text-primary mb-3">
                                                <i class="ri-image-line me-2"></i>
                                                Gestion des Images
                                            </h5>
                                        </div>
                                        
                                        <div class="col-lg-4 mb-3">
                                            <div class="form-group">
                                                <label for="background-image" class="form-label">
                                                    <i class="ri-landscape-line me-1"></i>
                                                    Image d'arrière-plan
                                                </label>
                                                <input type="file" id="background-image" name="cover" class="form-control"
                                                    accept="image/jpeg,image/png,image/jpg,image/gif" 
                                                    data-max-size="5120">
                                                <small class="text-muted">Formats acceptés: JPG, PNG, GIF (Max: 5MB)</small>
                                                <div class="error-message">Veuillez sélectionner une image valide</div>
                                                <div class="image-preview-container mt-3 text-center">
                                                    <img id="background-preview"
                                                        src="{{ $data_parametre ? URL::asset($data_parametre->getFirstMediaUrl('cover')) : URL::asset('images/camera-icon.png') }}"
                                                        class="image-preview"
                                                        alt="Aperçu de l'arrière-plan">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-4 mb-3">
                                            <div class="form-group">
                                                <label for="logo-header" class="form-label">
                                                    <i class="ri-header-line me-1"></i>
                                                    Logo d'en-tête
                                                </label>
                                                <input type="file" id="logo-header" name="logo_header" class="form-control"
                                                    accept="image/jpeg,image/png,image/jpg,image/gif"
                                                    data-max-size="2048">
                                                <small class="text-muted">Formats acceptés: JPG, PNG, GIF (Max: 2MB)</small>
                                                <div class="error-message">Veuillez sélectionner une image valide</div>
                                                <div class="image-preview-container mt-3 text-center">
                                                    <img id="header-preview"
                                                        src="{{ $data_parametre ? URL::asset($data_parametre->getFirstMediaUrl('logo_header')) : URL::asset('images/camera-icon.png') }}"
                                                        class="image-preview"
                                                        alt="Aperçu du logo d'en-tête">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-4 mb-3">
                                            <div class="form-group">
                                                <label for="logo-footer" class="form-label">
                                                    <i class="ri-footer-line me-1"></i>
                                                    Logo de pied de page
                                                </label>
                                                <input type="file" id="logo-footer" name="logo_footer" class="form-control"
                                                    accept="image/jpeg,image/png,image/jpg,image/gif"
                                                    data-max-size="2048">
                                                <small class="text-muted">Formats acceptés: JPG, PNG, GIF (Max: 2MB)</small>
                                                <div class="error-message">Veuillez sélectionner une image valide</div>
                                                <div class="image-preview-container mt-3 text-center">
                                                    <img id="footer-preview"
                                                        src="{{ $data_parametre ? URL::asset($data_parametre->getFirstMediaUrl('logo_footer')) : URL::asset('images/camera-icon.png') }}"
                                                        class="image-preview"
                                                        alt="Aperçu du logo de pied de page">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <script>
                                        // Configuration des validations
                                        const validationConfig = {
                                            nom_projet: { required: true, minLength: 3, maxLength: 100 },
                                            description_projet: { maxLength: 500 },
                                            contact_principal: { pattern: /^[0-9+\-\s\(\)]+$/ },
                                            contact_secondaire: { pattern: /^[0-9+\-\s\(\)]+$/ },
                                            contact_whatsapp: { pattern: /^[0-9+\-\s\(\)]+$/ },
                                            email_principal: { pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/ },
                                            email_secondaire: { pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/ },
                                            siege_social: { maxLength: 200 },
                                            localisation: { maxLength: 200 },
                                            google_maps: { pattern: /^https?:\/\/.+/ },
                                            lien_facebook: { pattern: /^https?:\/\/(www\.)?(facebook|fb)\.com\/.+/ },
                                            lien_instagram: { pattern: /^https?:\/\/(www\.)?instagram\.com\/.+/ },
                                            lien_twitter: { pattern: /^https?:\/\/(www\.)?(twitter|x)\.com\/.+/ },
                                            lien_linkedin: { pattern: /^https?:\/\/(www\.)?linkedin\.com\/.+/ },
                                            lien_tiktok: { pattern: /^https?:\/\/(www\.)?tiktok\.com\/.+/ }
                                        };

                                        // Validation des images
                                        function validateImage(file, maxSizeKB) {
                                            if (!file) return { valid: true };
                                            
                                            const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
                                            const maxSize = maxSizeKB * 1024;
                                            
                                            if (!allowedTypes.includes(file.type)) {
                                                return { valid: false, message: 'Format de fichier non supporté' };
                                            }
                                            
                                            if (file.size > maxSize) {
                                                return { valid: false, message: `Fichier trop volumineux (max: ${maxSizeKB/1024}MB)` };
                                            }
                                            
                                            return { valid: true };
                                        }

                                        // Prévisualisation d'image améliorée
                                        function previewImage(input, previewId) {
                                            const file = input.files?.[0];
                                            const preview = document.getElementById(previewId);
                                            const maxSize = parseInt(input.dataset.maxSize) || 5120;
                                            const formGroup = input.closest('.form-group');
                                            
                                            if (!file) return;
                                            
                                            const validation = validateImage(file, maxSize);
                                            
                                            if (!validation.valid) {
                                                showError(formGroup, validation.message);
                                                input.value = '';
                                                return;
                                            }
                                            
                                            clearError(formGroup);
                                            
                                            const reader = new FileReader();
                                            reader.onload = function(e) {
                                                preview.src = e.target.result;
                                                preview.style.transform = 'scale(1.05)';
                                                setTimeout(() => {
                                                    preview.style.transform = 'scale(1)';
                                                }, 300);
                                            };
                                            reader.readAsDataURL(file);
                                        }

                                        // Validation des champs
                                        function validateField(field) {
                                            const config = validationConfig[field.name];
                                            if (!config) return true;
                                            
                                            const value = field.value.trim();
                                            const formGroup = field.closest('.form-group');
                                            
                                            // Champ requis
                                            if (config.required && !value) {
                                                showError(formGroup, 'Ce champ est requis');
                                                return false;
                                            }
                                            
                                            // Longueur minimum
                                            if (config.minLength && value.length > 0 && value.length < config.minLength) {
                                                showError(formGroup, `Minimum ${config.minLength} caractères`);
                                                return false;
                                            }
                                            
                                            // Longueur maximum
                                            if (config.maxLength && value.length > config.maxLength) {
                                                showError(formGroup, `Maximum ${config.maxLength} caractères`);
                                                return false;
                                            }
                                            
                                            // Pattern de validation
                                            if (config.pattern && value && !config.pattern.test(value)) {
                                                showError(formGroup, 'Format invalide');
                                                return false;
                                            }
                                            
                                            clearError(formGroup);
                                            return true;
                                        }

                                        // Afficher erreur
                                        function showError(formGroup, message) {
                                            formGroup.classList.add('error');
                                            const errorElement = formGroup.querySelector('.error-message');
                                            if (errorElement) {
                                                errorElement.textContent = message;
                                            }
                                        }

                                        // Effacer erreur
                                        function clearError(formGroup) {
                                            formGroup.classList.remove('error');
                                        }

                                        // Initialisation des événements
                                        document.addEventListener('DOMContentLoaded', function() {
                                            // Prévisualisation des images
                                            const imageInputs = [
                                                { input: 'background-image', preview: 'background-preview' },
                                                { input: 'logo-header', preview: 'header-preview' },
                                                { input: 'logo-footer', preview: 'footer-preview' }
                                            ];
                                            
                                            imageInputs.forEach(({ input, preview }) => {
                                                const inputElement = document.getElementById(input);
                                                if (inputElement) {
                                                    inputElement.addEventListener('change', function() {
                                                        previewImage(this, preview);
                                                    });
                                                }
                                            });
                                            
                                            // Validation en temps réel
                                            const fieldsToValidate = Object.keys(validationConfig);
                                            fieldsToValidate.forEach(fieldName => {
                                                const field = document.getElementById(fieldName);
                                                if (field) {
                                                    field.addEventListener('blur', function() {
                                                        validateField(this);
                                                    });
                                                    
                                                    field.addEventListener('input', function() {
                                                        if (this.closest('.form-group').classList.contains('error')) {
                                                            validateField(this);
                                                        }
                                                    });
                                                }
                                            });
                                            
                                            // Validation du formulaire
                                            const form = document.querySelector('form');
                                            if (form) {
                                                form.addEventListener('submit', function(e) {
                                                    let isValid = true;
                                                    
                                                    fieldsToValidate.forEach(fieldName => {
                                                        const field = document.getElementById(fieldName);
                                                        if (field && !validateField(field)) {
                                                            isValid = false;
                                                        }
                                                    });
                                                    
                                                    if (!isValid) {
                                                        e.preventDefault();
                                                        Swal.fire({
                                                            icon: 'error',
                                                            title: 'Erreurs de validation',
                                                            text: 'Veuillez corriger les erreurs dans le formulaire',
                                                            confirmButtonText: 'Compris'
                                                        });
                                                    }
                                                });
                                            }
                                        });
                                    </script>
                                    <!-- ========== End Section ========== -->
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-12">
                                        <h5 class="text-primary mb-3">
                                            <i class="ri-information-line me-2"></i>
                                            Informations Générales
                                        </h5>
                                    </div>
                                    
                                    <div class="col-lg-5">
                                        <div class="form-group mb-3">
                                            <label for="nom_projet" class="form-label">
                                                <i class="ri-bookmark-line me-1"></i>
                                                Nom du projet *
                                            </label>
                                            <input type="text" name="nom_projet" class="form-control" id="nom_projet"
                                                value="{{ $data_parametre['nom_projet'] ?? '' }}" 
                                                required
                                                minlength="3"
                                                maxlength="100"
                                                placeholder="Entrez le nom du projet">
                                            <div class="error-message">Le nom du projet est requis (3-100 caractères)</div>
                                        </div>
                                    </div>

                                    <div class="col-lg-7">
                                        <div class="form-group mb-3">
                                            <label for="description_projet" class="form-label">
                                                <i class="ri-file-text-line me-1"></i>
                                                Description du projet
                                            </label>
                                            <textarea name="description_projet" class="form-control" id="description_projet" 
                                                rows="3"
                                                maxlength="500"
                                                placeholder="Décrivez brièvement votre projet...">{{ $data_parametre['description_projet'] ?? '' }}</textarea>
                                            <small class="text-muted">Maximum 500 caractères</small>
                                            <div class="error-message">Description trop longue (max 500 caractères)</div>
                                        </div>
                                    </div>
                                    <!--end col-->

                                    <div class="col-12 mt-4">
                                        <h5 class="text-primary mb-3">
                                            <i class="ri-phone-line me-2"></i>
                                            Informations de Contact
                                        </h5>
                                    </div>
                                    
                                    <div class="col-lg-4">
                                        <div class="form-group mb-3">
                                            <label for="contact1" class="form-label">
                                                <i class="ri-phone-fill me-1"></i>
                                                Téléphone Principal
                                            </label>
                                            <input type="tel" name="contact_principal" class="form-control" id="contact_principal"
                                                value="{{ $data_parametre['contact_principal'] ?? '' }}"
                                                pattern="[0-9+\-\s\(\)]+"
                                                placeholder="+33 1 23 45 67 89">
                                            <div class="error-message">Numéro de téléphone invalide</div>
                                        </div>
                                    </div>

                                    <div class="col-lg-4">
                                        <div class="form-group mb-3">
                                            <label for="contact_secondaire" class="form-label">
                                                <i class="ri-phone-line me-1"></i>
                                                Téléphone Secondaire
                                            </label>
                                            <input type="tel" name="contact_secondaire" class="form-control" id="contact_secondaire"
                                                value="{{ $data_parametre['contact_secondaire'] ?? '' }}"
                                                pattern="[0-9+\-\s\(\)]+"
                                                placeholder="+33 1 23 45 67 89">
                                            <div class="error-message">Numéro de téléphone invalide</div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-4">
                                        <div class="form-group mb-3">
                                            <label for="contact_whatsapp" class="form-label">
                                                <i class="ri-smartphone-line me-1"></i>
                                              Contact whatsapp
                                            </label>
                                            <input type="tel" name="contact_whatsapp" class="form-control" id="contact_whatsapp"
                                                value="{{ $data_parametre['contact_whatsapp'] ?? '' }}"
                                                pattern="[0-9+\-\s\(\)]+"
                                                placeholder="+33 6 12 34 56 78">
                                            <div class="error-message">Numéro de téléphone invalide</div>
                                        </div>
                                    </div>
                                    <!--end col-->
                                    
                                    <div class="col-lg-6">
                                        <div class="form-group mb-3">
                                            <label for="email_principal" class="form-label">
                                                <i class="ri-mail-line me-1"></i>
                                                Email Principal
                                            </label>
                                            <input type="email" name="email_principal" class="form-control" id="email_principal"
                                                value="{{ $data_parametre['email_principal'] ?? '' }}"
                                                placeholder="contact@exemple.com">
                                            <div class="error-message">Adresse email invalide</div>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="form-group mb-3">
                                            <label for="email_secondaire" class="form-label">
                                                <i class="ri-mail-send-line me-1"></i>
                                                Email Secondaire
                                            </label>
                                            <input type="email" name="email_secondaire" class="form-control" id="email_secondaire"
                                                value="{{ $data_parametre['email_secondaire'] ?? '' }}"
                                                placeholder="support@exemple.com">
                                            <div class="error-message">Adresse email invalide</div>
                                        </div>
                                    </div>
                                    <!--end col-->


                                    <div class="col-12 mt-4">
                                        <h5 class="text-primary mb-3">
                                            <i class="ri-map-pin-line me-2"></i>
                                            Localisation
                                        </h5>
                                    </div>
                                    
                                    <div class="col-lg-6">
                                        <div class="form-group mb-3">
                                            <label for="siege_social" class="form-label">
                                                <i class="ri-building-line me-1"></i>
                                                Siège Social
                                            </label>
                                            <textarea name="siege_social" class="form-control" id="siege_social" 
                                                rows="3"
                                                maxlength="200"
                                                placeholder="Adresse complète du siège social">{{ $data_parametre['siege_social'] ?? '' }}</textarea>
                                            <small class="text-muted">Maximum 200 caractères</small>
                                            <div class="error-message">Adresse trop longue (max 200 caractères)</div>
                                        </div>
                                    </div>
                                    <!--end col-->

                                    <div class="col-lg-6">
                                        <div class="form-group mb-3">
                                            <label for="localisation" class="form-label">
                                                <i class="ri-map-pin-2-line me-1"></i>
                                                Localisation
                                            </label>
                                            <textarea name="localisation" class="form-control" id="localisation" 
                                                rows="3"
                                                maxlength="200"
                                                placeholder="Adresse de localisation principale">{{ $data_parametre['localisation'] ?? '' }}</textarea>
                                            <small class="text-muted">Maximum 200 caractères</small>
                                            <div class="error-message">Localisation trop longue (max 200 caractères)</div>
                                        </div>
                                    </div>

                                    <div class="col-lg-12">
                                        <div class="form-group mb-3">
                                            <label for="google_maps" class="form-label">
                                                <i class="ri-map-2-line me-1"></i>
                                                Lien Google Maps
                                            </label>
                                            <input type="url" name="google_maps" class="form-control" id="google_maps"
                                                value="{{ $data_parametre['google_maps'] ?? '' }}"
                                                placeholder="https://maps.google.com/...">
                                            <small class="text-muted">URL complète vers votre localisation Google Maps</small>
                                            <div class="error-message">URL Google Maps invalide</div>
                                        </div>
                                    </div>

                                    <!--end col-->




                                    <!-- ========== Start social network ========== -->
                                    <div class="row mt-4">
                                        <div class="col-12">
                                            <h5 class="text-primary mb-3">
                                                <i class="ri-share-line me-2"></i>
                                                Réseaux Sociaux
                                            </h5>
                                        </div>
                                        
                                        <div class="col-lg-6">
                                            <div class="social-input-group form-group">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-xs d-block flex-shrink-0 me-3">
                                                        <span class="avatar-title rounded-circle fs-16" style="background: #1877f2; color: white;">
                                                            <i class="ri-facebook-fill"></i>
                                                        </span>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <label class="form-label mb-1 fw-bold">Facebook</label>
                                                        <input type="url" name="lien_facebook" class="form-control"
                                                            value="{{ $data_parametre['lien_facebook'] ?? '' }}"
                                                            placeholder="https://facebook.com/votre-page">
                                                        <div class="error-message">URL Facebook invalide</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-lg-6">
                                            <div class="social-input-group form-group">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-xs d-block flex-shrink-0 me-3">
                                                        <span class="avatar-title rounded-circle fs-16" style="background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888); color: white;">
                                                            <i class="ri-instagram-fill"></i>
                                                        </span>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <label class="form-label mb-1 fw-bold">Instagram</label>
                                                        <input type="url" name="lien_instagram" class="form-control"
                                                            value="{{ $data_parametre['lien_instagram'] ?? '' }}"
                                                            placeholder="https://instagram.com/votre-compte">
                                                        <div class="error-message">URL Instagram invalide</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-6">
                                            <div class="social-input-group form-group">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-xs d-block flex-shrink-0 me-3">
                                                        <span class="avatar-title rounded-circle fs-16" style="background: #000; color: white;">
                                                            <i class="ri-twitter-x-fill"></i>
                                                        </span>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <label class="form-label mb-1 fw-bold">Twitter/X</label>
                                                        <input type="url" name="lien_twitter" class="form-control"
                                                            value="{{ $data_parametre['lien_twitter'] ?? '' }}"
                                                            placeholder="https://twitter.com/votre-compte">
                                                        <div class="error-message">URL Twitter invalide</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-lg-6">
                                            <div class="social-input-group form-group">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-xs d-block flex-shrink-0 me-3">
                                                        <span class="avatar-title rounded-circle fs-16" style="background: #0077b5; color: white;">
                                                            <i class="ri-linkedin-fill"></i>
                                                        </span>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <label class="form-label mb-1 fw-bold">LinkedIn</label>
                                                        <input type="url" name="lien_linkedin" class="form-control"
                                                            value="{{ $data_parametre['lien_linkedin'] ?? '' }}"
                                                            placeholder="https://linkedin.com/company/votre-entreprise">
                                                        <div class="error-message">URL LinkedIn invalide</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-6">
                                            <div class="social-input-group form-group">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-xs d-block flex-shrink-0 me-3">
                                                        <span class="avatar-title rounded-circle fs-16" style="background: #000; color: white;">
                                                            <i class="ri-tiktok-fill"></i>
                                                        </span>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <label class="form-label mb-1 fw-bold">TikTok</label>
                                                        <input type="url" name="lien_tiktok" class="form-control"
                                                            value="{{ $data_parametre['lien_tiktok'] ?? '' }}"
                                                            placeholder="https://tiktok.com/@votre-compte">
                                                        <div class="error-message">URL TikTok invalide</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- ========== End social network ========== -->


                                    <div class="col-lg-12">
                                        <div class="hstack mt-3">
                                            <button type="submit" class="btn btn-primary w-100">
                                                <i class="ri-refresh-line align-bottom me-1"></i>
                                                Mettre à jour</button>
                                        </div>
                                    </div>
                                    <!--end col-->
                                </div>

                            </form>
                        </div>
                        <!--end tab-pane-->


                        <div class="tab-pane" id="privacy" role="tabpanel">
                            <div class="mb-4">
                                <h5 class="text-primary mb-4">
                                    <i class="ri-settings-3-line me-2"></i>
                                    Gestion de l'Application
                                </h5>

                                <div class="maintenance-toggle">
                                    <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center">
                                        <div class="flex-grow-1 mb-3 mb-sm-0">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="ri-tools-line text-primary me-2"></i>
                                                <h6 class="mb-0 fw-bold">Cache Système</h6>
                                            </div>
                                            <p class="text-muted mb-0">Vider le cache améliore les performances en supprimant les fichiers temporaires stockés en mémoire</p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <button type="button" class="btn btn-outline-primary btn-clear">
                                                <i class="ri-delete-bin-line me-1"></i>
                                                Vider le Cache
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="maintenance-toggle">
                                    <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center">
                                        <div class="flex-grow-1 mb-3 mb-sm-0">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="ri-shield-line text-primary me-2"></i>
                                                <h6 class="mb-0 fw-bold">Mode Maintenance</h6>
                                            </div>
                                            <p class="text-muted mb-0">Activez le mode maintenance pour effectuer des mises à jour en toute sécurité</p>
                                            @if (!empty($data_parametre->mode_maintenance) && $data_parametre->mode_maintenance != 'up')
                                                <span class="badge bg-warning mt-1">
                                                    <i class="ri-error-warning-line me-1"></i>
                                                    Application en maintenance
                                                </span>
                                            @endif
                                        </div>
                                        <div class="flex-shrink-0">
                                            @if (empty($data_parametre->mode_maintenance) || $data_parametre->mode_maintenance == 'up')
                                                <button type="button" class="btn btn-warning btn-mode-down">
                                                    <i class="ri-shield-cross-line me-1"></i>
                                                    Activer Maintenance
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-success btn-mode-up">
                                                    <i class="ri-shield-check-line me-1"></i>
                                                    Désactiver Maintenance
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end tab-pane-->

                        <div class="tab-pane" id="backup" role="tabpanel">
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h5 class="text-primary mb-0">
                                        <i class="ri-database-2-line me-2"></i>
                                        Sauvegardes Disponibles
                                    </h5>
                                    <span class="badge bg-info">
                                        {{ count($backup) }} sauvegarde(s)
                                    </span>
                                </div>

                                @if(count($backup) > 0)
                                    <div class="table-responsive">
                                        <table class="settings-table">
                                            <thead>
                                                <tr>
                                                    <th>
                                                        <i class="ri-file-line me-1"></i>
                                                        Nom du fichier
                                                    </th>
                                                    <th>
                                                        <i class="ri-calendar-line me-1"></i>
                                                        Date de création
                                                    </th>
                                                    <th class="text-center">
                                                        <i class="ri-tools-line me-1"></i>
                                                        Actions
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($backup as $file)
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <i class="ri-file-zip-line text-primary me-2"></i>
                                                                <span class="fw-bold">{{ basename($file) }}</span>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <span class="text-muted">
                                                                {{ date('d/m/Y H:i:s', filectime($file)) }}
                                                            </span>
                                                        </td>
                                                        <td class="text-center">
                                                            <a href="{{ route('setting.download-backup', basename($file)) }}" 
                                                               class="btn btn-sm btn-outline-primary"
                                                               title="Télécharger cette sauvegarde">
                                                                <i class="ri-download-cloud-line me-1"></i>
                                                                Télécharger
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-5">
                                        <i class="ri-inbox-line display-4 text-muted"></i>
                                        <h5 class="text-muted mt-3">Aucune sauvegarde disponible</h5>
                                        <p class="text-muted">Les sauvegardes apparaîtront ici une fois créées</p>
                                    </div>
                                @endif
                            </div>
                        </div>

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

    <script>
        $(document).ready(function() {

            //cache clear
            $('.btn-clear').click(function(e) {
                e.preventDefault();
                $.ajax({
                    type: "get",
                    url: "{{ route('parametre.optimize-clear') }}",
                    // data: "data",
                    dataType: "json",
                    success: function(response) {
                        if (response.status == 200) {
                            let timerInterval;
                            Swal.fire({
                                title: "Traitement en cour!",
                                html: "Se termine dans <b></b> milliseconds.",
                                timer: 6000,
                                timerProgressBar: true,
                                didOpen: () => {
                                    Swal.showLoading();
                                    const timer = Swal.getPopup().querySelector(
                                        "b");
                                    timerInterval = setInterval(() => {
                                        timer.textContent =
                                            `${Swal.getTimerLeft()}`;
                                    }, 100);
                                },
                                willClose: () => {
                                    clearInterval(timerInterval);
                                }
                            }).then((result) => {
                                /* Read more about handling dismissals below */
                                if (result.dismiss === Swal.DismissReason.timer) {
                                    Swal.fire({
                                        position: "top-end",
                                        icon: "success",
                                        title: "Application optimisé avec succès",
                                        showConfirmButton: false,
                                        timer: 1500
                                    });
                                }
                            });
                        }
                    }
                });
            });

            // maintenance mode down
            $('.btn-mode-down').click(function(e) {
                e.preventDefault();
                $.ajax({
                    type: "get",
                    url: "{{ route('parametre.maintenance-down') }}",
                    // data: "data",
                    dataType: "json",
                    success: function(response) {
                        if (response.status == 200) {
                            Swal.fire({
                                position: "top-end",
                                icon: "success",
                                title: "Mode maintenance activé",
                                showConfirmButton: false,
                                timer: 1500
                            });

                            $('btn-mode-up').html('désactivé');
                            location.reload(true);
                        }
                    }
                });
            });

            // maintenance mode up
            $('.btn-mode-up').click(function(e) {
                e.preventDefault();
                $.ajax({
                    type: "get",
                    url: "{{ route('parametre.maintenance-up') }}",
                    // data: "data",
                    dataType: "json",
                    success: function(response) {
                        if (response.status == 200) {
                            Swal.fire({
                                position: "top-end",
                                icon: "success",
                                title: "Mode maintenance desactivé",
                                showConfirmButton: false,
                                timer: 1500
                            });

                            location.reload(true);
                        }
                    }
                });
            });


        });
    </script>
@endsection
