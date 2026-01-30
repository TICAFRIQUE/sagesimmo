@extends('frontend.layouts.master')

@section('title', 'Inscription - Sage Immo')

@section('css')
    <style>
        .auth-page {
            min-height: calc(100vh - 80px);
            display: flex;
            align-items: center;
            background: linear-gradient(135deg, #43542A 0%, #2d3a1c 100%);
            padding: 4rem 0;
        }

        .auth-card {
            background: white;
            border-radius: 16px;
            padding: 3rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 700px;
            margin: 0 auto;
        }

        .auth-card h2 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .role-selector {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin: 1.5rem 0;
        }

        .role-option {
            text-align: center;
            padding: 1.5rem 1rem;
            border: 2px solid var(--border-color);
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .role-option:hover {
            border-color: var(--primary-color);
            background: var(--bg-light);
        }

        .role-option.selected {
            border-color: var(--primary-color);
            background: var(--primary-color);
            color: white;
        }

        .role-option i {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            display: block;
        }

        .role-option input[type="radio"] {
            display: none;
        }

        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 1.5rem 0;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid var(--border-color);
        }

        .divider span {
            padding: 0 1rem;
            color: var(--text-light);
            font-size: 0.875rem;
        }

        .toggle-password {
            border-left: 0;
            background: white;
            color: var(--text-light);
            transition: all 0.3s;
        }

        .toggle-password:hover {
            color: var(--primary-color);
            background: var(--bg-light);
        }

        .toggle-password i {
            font-size: 1.1rem;
        }

        .recaptcha-container {
            margin: 1rem 0;
            display: flex;
            justify-content: center;
        }

        .recaptcha-error {
            text-align: center;
            margin-top: 0.5rem;
        }
    </style>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endsection

@section('content')
    <div class="auth-page">
        <div class="container">
            <div class="auth-card" data-aos="zoom-in">
                <div class="text-center mb-4">
                    <h2>Créer un compte</h2>
                    <p class="text-muted">Rejoignez notre plateforme immobilière</p>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form action="{{ route('register') }}" method="POST" id="registerForm">
                    @csrf

                    <!-- Sélection du rôle -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Je suis *</label>
                        <div class="role-selector">
                            @foreach ($roles->whereIn('name', ['locataire', 'acheteur']) as $role)
                                <label class="role-option" data-role="{{ $role->id }}">
                                    <input type="radio" name="role_id" value="{{ $role->id }}" required>
                                    @switch($role->name)
                                        @case('locataire')
                                            <i class="ri-home-heart-line"></i>
                                            <div>Locataire</div>
                                        @break

                                        @case('acheteur')
                                            <i class="ri-shopping-cart-line"></i>
                                            <div>Acheteur</div>
                                        @break

                                        {{-- @case('proprietaire')
                                            <i class="ri-building-line"></i>
                                            <div>Propriétaire</div>
                                        @break --}}
                                    @endswitch
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-12">
                            <label for="username" class="form-label">Nom complet *</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ri-user-line"></i></span>
                                <input type="text" class="form-control @error('username') is-invalid @enderror"
                                    id="username" name="username" value="{{ old('username') }}" placeholder="Jean Dupont"
                                    required>
                            </div>
                            @error('username')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="form-label">Email *</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ri-mail-line"></i></span>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    id="email" name="email" value="{{ old('email') }}"
                                    placeholder="email@exemple.com" required>
                            </div>
                            @error('email')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="phone" class="form-label">Téléphone *</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ri-phone-line"></i></span>
                                <input type="number" min="0"
                                    class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone"
                                    value="{{ old('phone') }}" placeholder="+225 00 00 00 00" required>
                            </div>
                            @error('phone')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="password" class="form-label">Mot de passe *</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ri-lock-line"></i></span>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                    id="password" name="password" placeholder="••••••••" required>
                                <button class="btn btn-outline-secondary toggle-password" type="button"
                                    data-target="password">
                                    <i class="ri-eye-line"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Minimum 6 caractères</small>
                        </div>

                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label">Confirmer mot de passe *</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ri-lock-line"></i></span>
                                <input type="password" class="form-control" id="password_confirmation"
                                    name="password_confirmation" placeholder="••••••••" required>
                                <button class="btn btn-outline-secondary toggle-password" type="button"
                                    data-target="password_confirmation">
                                    <i class="ri-eye-line"></i>
                                </button>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="terms" required>
                                <label class="form-check-label" for="terms">
                                    J'accepte les <a href="#" class="text-decoration-none">conditions
                                        d'utilisation</a>
                                    et la <a href="#" class="text-decoration-none">politique de confidentialité</a>
                                </label>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="recaptcha-container">
                                <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}">
                                </div>
                            </div>
                            @error('g-recaptcha-response')
                                <div class="text-danger small text-center recaptcha-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <button type="submit" class="btn btn-accent w-100 py-3 mt-4 mb-3">
                        <i class="ri-user-add-line"></i> Créer mon compte
                    </button>
                </form>

                <div class="divider">
                    <span>OU</span>
                </div>

                <div class="text-center">
                    <p class="mb-0">
                        Vous avez déjà un compte ?
                        <a href="{{ route('login') }}" class="text-decoration-none fw-bold">
                            Connectez-vous
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Gestion de la sélection du rôle
            const roleOptions = document.querySelectorAll('.role-option');

            roleOptions.forEach(option => {
                option.addEventListener('click', function() {
                    // Retirer la sélection de toutes les options
                    roleOptions.forEach(opt => opt.classList.remove('selected'));

                    // Ajouter la sélection à l'option cliquée
                    this.classList.add('selected');

                    // Cocher le radio bouton
                    this.querySelector('input[type="radio"]').checked = true;
                });
            });

            // Pré-sélection si une valeur existe (après erreur de validation)
            const checkedRadio = document.querySelector('input[name="role_id"]:checked');
            if (checkedRadio) {
                checkedRadio.closest('.role-option').classList.add('selected');
            }

            // Toggle password visibility
            document.querySelectorAll('.toggle-password').forEach(button => {
                button.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-target');
                    const passwordInput = document.getElementById(targetId);
                    const icon = this.querySelector('i');

                    if (passwordInput.type === 'password') {
                        passwordInput.type = 'text';
                        icon.classList.remove('ri-eye-line');
                        icon.classList.add('ri-eye-off-line');
                    } else {
                        passwordInput.type = 'password';
                        icon.classList.remove('ri-eye-off-line');
                        icon.classList.add('ri-eye-line');
                    }
                });
            });
        });
    </script>
@endsection
