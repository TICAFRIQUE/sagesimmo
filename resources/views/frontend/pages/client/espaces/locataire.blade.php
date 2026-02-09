@extends('frontend.pages.client.layout')

@section('title', 'Espace Locataire')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <h4 class="mb-0">
                <i class="ri-home-heart-line text-info"></i> Mon espace Locataire
            </h4>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="row g-3 mb-4">
        <!-- Nombre de biens loués -->
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-lg bg-primary-subtle rounded me-3">
                            <i class="ri-home-4-line fs-2 text-primary"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Biens loués</h6>
                            <h3 class="mb-0">{{ $nombreBiensLoues }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total dépensé -->
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-lg bg-success-subtle rounded me-3">
                            <i class="ri-money-dollar-circle-line fs-2 text-success"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Total payé</h6>
                            <h3 class="mb-0">{{ number_format($totalDepense, 0, ',', ' ') }} F</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Montant restant -->
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-lg bg-warning-subtle rounded me-3">
                            <i class="ri-wallet-3-line fs-2 text-warning"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Reste à payer</h6>
                            <h3 class="mb-0">{{ number_format($montantRestantTotal, 0, ',', ' ') }} F</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des locations -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-light border-0">
            <h5 class="mb-0">
                <i class="ri-list-check me-2"></i>Mes locations
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Bien</th>
                            <th>Localisation</th>
                            <th>Loyer mensuel</th>
                            <th>Prochain paiement</th>
                            <th>Statut</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($locations as $location)
                            @php
                                // Trouver la prochaine échéance (non payée et date future ou actuelle)
                                $prochaineEcheance = $location->echeances()
                                    ->where(function($q) {
                                        $q->where('statut', '!=', 'paye')
                                          ->orWhereRaw('montant_paye < montant_du');
                                    })
                                    ->orderBy('date_echeance', 'asc')
                                    ->first();
                            @endphp
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($location->annonce && $location->annonce->hasMedia('images'))
                                            <img src="{{ $location->annonce->getFirstMediaUrl('images') }}" 
                                                 class="rounded me-2" 
                                                 alt="{{ $location->annonce->titre }}"
                                                 style="width: 50px; height: 50px; object-fit: cover;">
                                        @else
                                            <div class="bg-light rounded me-2 d-flex align-items-center justify-content-center" 
                                                 style="width: 50px; height: 50px;">
                                                <i class="ri-home-4-line text-muted"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="fw-semibold">{{ Str::limit($location->annonce->titre ?? 'N/A', 30) }}</div>
                                            <small class="text-muted">
                                                <i class="ri-building-line"></i>
                                                {{ $location->annonce->typeBien->nom ?? 'N/A' }}
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <i class="ri-map-pin-line text-muted me-1"></i>
                                    {{ $location->annonce->ville ?? 'N/A' }}
                                </td>
                                <td>
                                    <span class="fw-semibold text-success">
                                        {{ number_format($location->loyer_mensuel, 0, ',', ' ') }} F
                                    </span>
                                </td>
                                <td>
                                    @if($prochaineEcheance)
                                        @php
                                            $dateEcheance = \Carbon\Carbon::parse($prochaineEcheance->date_echeance);
                                            $maintenant = \Carbon\Carbon::now();
                                            $resteAPayer = $prochaineEcheance->montant_du - $prochaineEcheance->montant_paye;
                                        @endphp
                                        <div>
                                            <small class="text-muted d-block">
                                                <i class="ri-calendar-line me-1"></i>{{ $dateEcheance->format('d/m/Y') }}
                                            </small>
                                            <span class="fw-semibold {{ $dateEcheance->isPast() && $resteAPayer > 0 ? 'text-danger' : 'text-warning' }}">
                                                {{ number_format($resteAPayer, 0, ',', ' ') }} F
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-muted small">Aucune échéance</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $badgeColors = [
                                            'demande_client' => 'primary',
                                            'visite_planifiee' => 'info',
                                            'contrat_signe' => 'success',
                                            'actif' => 'success',
                                            'expire' => 'warning',
                                            'termine' => 'secondary',
                                            'annule' => 'danger'
                                        ];
                                        $badgeColor = $badgeColors[$location->statut] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $badgeColor }}">
                                        {{ ucfirst(str_replace('_', ' ', $location->statut)) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('client.locataire.workflow', $location->id) }}" 
                                           class="btn btn-sm btn-primary"
                                           title="Voir le workflow">
                                            <i class="ri-git-branch-line"></i>
                                            <span class="d-none d-md-inline ms-1">Workflow</span>
                                        </a>
                                        <a href="{{ route('client.locataire.echeances', $location->id) }}" 
                                           class="btn btn-sm btn-info"
                                           title="Voir les échéances">
                                            <i class="ri-calendar-check-line"></i>
                                            <span class="d-none d-md-inline ms-1">Échéances</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <i class="ri-inbox-line fs-2 text-muted d-block mb-2"></i>
                                    <p class="text-muted mb-0">Aucune location trouvée</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Si aucune location trouvée, afficher un message -->
    @if($locations->isEmpty())
        <div class="card mb-4">
            <div class="card-body text-center py-5">
                <i class="ri-inbox-line fs-1 text-muted mb-3 d-block"></i>
                <h5 class="text-muted">Aucune location trouvée</h5>
                <p class="text-muted mb-4">Vous n'avez pas encore de bien loué.</p>
                <a href="{{ route('properties.index') }}" class="btn btn-primary">
                    <i class="ri-search-line me-2"></i>Rechercher un bien
                </a>
            </div>
        </div>
    @endif
</div>

<style>
.avatar-lg {
    width: 64px;
    height: 64px;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
@endsection

