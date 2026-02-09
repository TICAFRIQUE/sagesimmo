@extends('frontend.pages.client.layout')

@section('title', 'Échéances de paiement')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center">
                <a href="{{ route('client.locataire') }}" class="btn btn-sm btn-outline-secondary me-3">
                    <i class="ri-arrow-left-line"></i> Retour
                </a>
                <div>
                    <h4 class="mb-1">
                        <i class="ri-calendar-check-line text-info"></i> Échéances de paiement
                    </h4>
                    <p class="text-muted mb-0 small">{{ $location->annonce->titre ?? 'Bien' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Informations du bien -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h6 class="mb-0">
                <i class="ri-home-4-line me-2"></i>Informations du bien loué
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    @if($location->annonce && $location->annonce->hasMedia('images'))
                        <img src="{{ $location->annonce->getFirstMediaUrl('images') }}" 
                             class="img-fluid rounded" 
                             alt="{{ $location->annonce->titre }}"
                             style="height: 120px; width: 100%; object-fit: cover;">
                    @else
                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 120px;">
                            <i class="ri-home-4-line fs-1 text-muted"></i>
                        </div>
                    @endif
                </div>
                <div class="col-md-9">
                    <div class="row">
                        <div class="col-md-3">
                            <p class="text-muted small mb-1">Référence</p>
                            <p class="mb-0"><strong>{{ $location->annonce->reference ?? 'N/A' }}</strong></p>
                        </div>
                        <div class="col-md-3">
                            <p class="text-muted small mb-1">Localisation</p>
                            <p class="mb-0">{{ $location->annonce->ville ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-3">
                            <p class="text-muted small mb-1">Loyer mensuel</p>
                            <p class="mb-0 text-success"><strong>{{ number_format($location->loyer_mensuel, 0, ',', ' ') }} F</strong></p>
                        </div>
                        <div class="col-md-3">
                            <p class="text-muted small mb-1">Statut</p>
                            <p class="mb-0">
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
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Résumé financier -->
    @php
        $totalPaye = $location->paiements()->where('statut', 'valide')->sum('montant');
        
        // Calcul correct des impayés : uniquement les échéances passées et non payées
        $aujourdhui = \Carbon\Carbon::now();
        $echeancesImpayees = $location->echeances
            ->filter(function($e) use ($aujourdhui) {
                $dateEcheance = \Carbon\Carbon::parse($e->date_echeance);
                $reste = $e->montant_du - $e->montant_paye;
                return $dateEcheance->lt($aujourdhui) && $reste > 0;
            })
            ->sum(function($e) {
                return $e->montant_du - $e->montant_paye;
            });
        
        $totalDu = $location->echeances()->sum('montant_du');
        $pourcentagePaiement = $totalDu > 0 ? round(($totalPaye / $totalDu) * 100) : 0;
    @endphp

    <div class="row g-3 mb-4">
        <!-- Total payé -->
        <div class="col-12 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-lg bg-success-subtle rounded me-3">
                            <i class="ri-money-dollar-circle-line fs-2 text-success"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Total payé</h6>
                            <h3 class="mb-0">{{ number_format($totalPaye, 0, ',', ' ') }} F</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total dû -->
        <div class="col-12 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-lg bg-info-subtle rounded me-3">
                            <i class="ri-file-list-3-line fs-2 text-info"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Total dû</h6>
                            <h3 class="mb-0">{{ number_format($totalDu, 0, ',', ' ') }} F</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Impayés -->
        <div class="col-12 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-lg bg-{{ $echeancesImpayees > 0 ? 'danger' : 'light' }}-subtle rounded me-3">
                            <i class="ri-alert-line fs-2 text-{{ $echeancesImpayees > 0 ? 'danger' : 'muted' }}"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Impayés</h6>
                            <h3 class="mb-0">{{ number_format($echeancesImpayees, 0, ',', ' ') }} F</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pourcentage -->
        <div class="col-12 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-lg bg-primary-subtle rounded me-3">
                            <i class="ri-percent-line fs-2 text-primary"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Progression</h6>
                            <h3 class="mb-0">{{ $pourcentagePaiement }}%</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Barre de progression -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Paiements effectués</span>
                <span class="fw-semibold">{{ $pourcentagePaiement }}%</span>
            </div>
            <div class="progress" style="height: 20px;">
                <div class="progress-bar bg-success" 
                     role="progressbar" 
                     style="width: {{ $pourcentagePaiement }}%"
                     aria-valuenow="{{ $pourcentagePaiement }}" 
                     aria-valuemin="0" 
                     aria-valuemax="100">
                    {{ $pourcentagePaiement }}%
                </div>
            </div>
        </div>
    </div>

    <!-- Alerte si impayés -->
    @if($echeancesImpayees > 0)
        <div class="alert alert-danger d-flex align-items-center mb-4">
            <i class="ri-alert-line fs-3 me-3"></i>
            <div>
                <strong>Attention : Vous avez des impayés</strong>
                <p class="mb-0 small">Veuillez régulariser votre situation au plus vite pour éviter les pénalités.</p>
            </div>
        </div>
    @endif

    <!-- Tableau des échéances -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h6 class="mb-0">
                <i class="ri-calendar-event-line me-2"></i>Calendrier des échéances
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date échéance</th>
                            <th>Type</th>
                            <th class="text-end">Montant dû</th>
                            <th class="text-end">Montant payé</th>
                            <th class="text-end">Reste à payer</th>
                            <th class="text-center">Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($location->echeances->sortBy('date_echeance') as $echeance)
                            @php
                                $dateEcheance = \Carbon\Carbon::parse($echeance->date_echeance);
                                $maintenant = \Carbon\Carbon::now();
                                $reste = $echeance->montant_du - $echeance->montant_paye;
                                
                                // Déterminer le vrai statut selon la date et les montants
                                if ($reste <= 0) {
                                    $statutReel = 'paye';
                                    $cssClass = '';
                                } elseif ($dateEcheance->lt($maintenant)) {
                                    $statutReel = 'impaye';
                                    $cssClass = 'table-danger';
                                } else {
                                    $statutReel = 'a_venir';
                                    $cssClass = '';
                                }
                            @endphp
                            <tr class="{{ $cssClass }}">
                                <td>
                                    <i class="ri-calendar-line text-muted me-1"></i>
                                    {{ $dateEcheance->format('d/m/Y') }}
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ ucfirst($echeance->type) }}</span>
                                </td>
                                <td class="text-end">
                                    {{ number_format($echeance->montant_du, 0, ',', ' ') }} F
                                </td>
                                <td class="text-end text-success">
                                    {{ number_format($echeance->montant_paye, 0, ',', ' ') }} F
                                </td>
                                <td class="text-end">
                                    <span class="{{ $reste > 0 ? 'text-danger' : 'text-muted' }}">
                                        {{ number_format($reste, 0, ',', ' ') }} F
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($statutReel == 'paye')
                                        <span class="badge bg-success">
                                            <i class="ri-checkbox-circle-line"></i> Payé
                                        </span>
                                    @elseif($statutReel == 'impaye')
                                        <span class="badge bg-danger">
                                            <i class="ri-close-circle-line"></i> Impayé
                                        </span>
                                    @else
                                        <span class="badge bg-primary">
                                            <i class="ri-calendar-todo-line"></i> À venir
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <i class="ri-inbox-line fs-2 text-muted d-block mb-2"></i>
                                    <p class="text-muted mb-0">Aucune échéance trouvée</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Historique des paiements -->
    @if($location->paiements->count() > 0)
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0">
                    <i class="ri-file-list-3-line me-2"></i>Historique des paiements
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date paiement</th>
                                <th>Type</th>
                                <th class="text-end">Montant</th>
                                <th>Méthode</th>
                                <th class="text-center">Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($location->paiements->sortByDesc('date_paiement') as $paiement)
                                <tr>
                                    <td>
                                        <i class="ri-calendar-line text-muted me-1"></i>
                                        {{ \Carbon\Carbon::parse($paiement->date_paiement)->format('d/m/Y') }}
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ ucfirst($paiement->type) }}</span>
                                    </td>
                                    <td class="text-end">
                                        <strong class="text-success">{{ number_format($paiement->montant, 0, ',', ' ') }} F</strong>
                                    </td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $paiement->methode_paiement ?? 'N/A')) }}</td>
                                    <td class="text-center">
                                        @if($paiement->statut == 'valide')
                                            <span class="badge bg-success">
                                                <i class="ri-checkbox-circle-line"></i> Validé
                                            </span>
                                        @elseif($paiement->statut == 'en_attente')
                                            <span class="badge bg-warning">
                                                <i class="ri-time-line"></i> En attente
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                <i class="ri-close-circle-line"></i> Rejeté
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- Bouton vers workflow -->
    <div class="card border-info">
        <div class="card-body text-center py-4">
            <h6 class="mb-3">
                <i class="ri-git-branch-line text-info me-2"></i>Processus de location
            </h6>
            <p class="text-muted mb-3">Consultez l'avancement de votre processus de location</p>
            <a href="{{ route('client.locataire.workflow', $location->id) }}" class="btn btn-info">
                <i class="ri-flow-chart me-2"></i>Voir le workflow
            </a>
        </div>
    </div>

    <!-- Note informative -->
    <div class="alert alert-light border mt-4">
        <div class="d-flex">
            <div class="flex-shrink-0">
                <i class="ri-information-line text-info fs-4"></i>
            </div>
            <div class="flex-grow-1 ms-3">
                <h6 class="alert-heading">Information</h6>
                <p class="mb-0">Pour toute question concernant vos paiements, contactez votre gestionnaire de compte.</p>
            </div>
        </div>
    </div>
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
