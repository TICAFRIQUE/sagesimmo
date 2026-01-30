@extends('frontend.pages.client.layout')

@section('client-content')
<div class="client-content">
    <h2 class="mb-4">
        <i class="ri-user-settings-line"></i> Mon Profil
    </h2>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="ri-check-line"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Informations personnelles -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="ri-user-line"></i> Informations personnelles
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('client.profil.update') }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="username" class="form-label">Nom complet *</label>
                        <input type="text" 
                               class="form-control @error('username') is-invalid @enderror" 
                               id="username" 
                               name="username" 
                               value="{{ old('username', $user->username) }}" 
                               required>
                        @error('username')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email *</label>
                        <input type="email" 
                               class="form-control @error('email') is-invalid @enderror" 
                               id="email" 
                               name="email" 
                               value="{{ old('email', $user->email) }}" 
                               required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="phone" class="form-label">Téléphone</label>
                        <input type="text" 
                               class="form-control @error('phone') is-invalid @enderror" 
                               id="phone" 
                               name="phone" 
                               value="{{ old('phone', $user->phone) }}">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Date d'inscription</label>
                        <input type="text" 
                               class="form-control" 
                               value="{{ $user->created_at->format('d/m/Y') }}" 
                               disabled>
                    </div>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="ri-save-line"></i> Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Changer le mot de passe -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="ri-lock-password-line"></i> Changer le mot de passe
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('client.profil.change-password') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="current_password" class="form-label">Mot de passe actuel *</label>
                        <input type="password" 
                               class="form-control @error('current_password') is-invalid @enderror" 
                               id="current_password" 
                               name="current_password" 
                               required>
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="password" class="form-label">Nouveau mot de passe *</label>
                        <input type="password" 
                               class="form-control @error('password') is-invalid @enderror" 
                               id="password" 
                               name="password" 
                               required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Minimum 8 caractères</small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="password_confirmation" class="form-label">Confirmer le mot de passe *</label>
                        <input type="password" 
                               class="form-control" 
                               id="password_confirmation" 
                               name="password_confirmation" 
                               required>
                    </div>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-warning">
                        <i class="ri-lock-unlock-line"></i> Changer le mot de passe
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistiques du compte -->
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="ri-bar-chart-line"></i> Statistiques de mon compte
            </h5>
        </div>
        <div class="card-body">
            <div class="row text-center">
                <div class="col-md-4">
                    <div class="p-3">
                        <h3 class="text-primary">{{ $user->demandeInterets()->count() }}</h3>
                        <p class="text-muted mb-0">Demandes envoyées</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3">
                        <h3 class="text-success">{{ $user->demandeInterets()->where('statut', 'acceptee')->count() }}</h3>
                        <p class="text-muted mb-0">Demandes acceptées</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3">
                        <h3 class="text-info">{{ $user->demandeInterets()->where('statut', 'visite_proposee')->count() }}</h3>
                        <p class="text-muted mb-0">Visites programmées</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
