@extends('frontend.layouts.master')

@section('title', 'Connexion - Sage Immo')

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
        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        max-width: 450px;
        margin: 0 auto;
    }
    .auth-card h2 {
        font-size: 2rem;
        margin-bottom: 0.5rem;
    }
    .social-login {
        display: flex;
        gap: 1rem;
        margin: 1.5rem 0;
    }
    .social-btn {
        flex: 1;
        padding: 0.75rem;
        border: 2px solid var(--border-color);
        border-radius: 8px;
        background: white;
        font-weight: 500;
        transition: all 0.3s;
    }
    .social-btn:hover {
        border-color: var(--primary-color);
        background: var(--bg-light);
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
</style>
@endsection

@section('content')
<div class="auth-page">
    <div class="container">
        <div class="auth-card" data-aos="zoom-in">
            <div class="text-center mb-4">
                <h2>Bienvenue !</h2>
                <p class="text-muted">Connectez-vous pour accéder à votre compte</p>
            </div>
            
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            <form action="{{ route('login') }}" method="POST">
                @csrf
                
                <div class="mb-3">
                    <label for="login" class="form-label">Email ou Téléphone</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="ri-mail-line"></i></span>
                        <input type="text" class="form-control @error('login') is-invalid @enderror" 
                               id="login" name="login" value="{{ old('login') }}" 
                               placeholder="votre@email.com ou 070000000" required autofocus>
                    </div>
                    @error('login')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label">Mot de passe</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="ri-lock-line"></i></span>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                               id="password" name="password" 
                               placeholder="••••••••" required>
                        <button class="btn btn-outline-secondary toggle-password" type="button" data-target="password">
                            <i class="ri-eye-line"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="remember" name="remember">
                        <label class="form-check-label" for="remember">
                            Se souvenir de moi
                        </label>
                    </div>
                    <a href="#" class="text-decoration-none">Mot de passe oublié ?</a>
                </div>
                
                <button type="submit" class="btn btn-accent w-100 py-3 mb-3">
                    <i class="ri-login-box-line"></i> Se connecter
                </button>
            </form>
            
            <div class="divider">
                <span>OU</span>
            </div>
            
            <div class="text-center">
                <p class="mb-0">
                    Vous n'avez pas de compte ? 
                    <a href="{{ route('register') }}" class="text-decoration-none fw-bold">
                        Inscrivez-vous
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
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
</script>
@endsection

@section('scripts')
<script>
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
</script>
@endsection
