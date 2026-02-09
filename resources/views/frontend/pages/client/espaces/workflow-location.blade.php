@extends('frontend.pages.client.layout')

@section('title', 'Workflow de la location')

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
                        <i class="ri-flow-chart text-info"></i> Workflow de la location
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
                <div class="col-md-3 mb-3">
                    @if($location->annonce && $location->annonce->hasMedia('images'))
                        <img src="{{ $location->annonce->getFirstMediaUrl('images') }}" 
                             class="img-fluid rounded" 
                             alt="{{ $location->annonce->titre }}"
                             style="height: 150px; width: 100%; object-fit: cover;">
                    @else
                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 150px;">
                            <i class="ri-home-4-line fs-1 text-muted"></i>
                        </div>
                    @endif
                </div>
                <div class="col-md-9">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <p class="text-muted small mb-1">Référence</p>
                            <p class="mb-0"><strong>{{ $location->annonce->reference ?? 'N/A' }}</strong></p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <p class="text-muted small mb-1">Type de bien</p>
                            <p class="mb-0">{{ $location->annonce->typeBien->nom ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <p class="text-muted small mb-1">Localisation</p>
                            <p class="mb-0">{{ $location->annonce->ville ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <p class="text-muted small mb-1">Loyer mensuel</p>
                            <p class="mb-0 text-success"><strong>{{ number_format($location->loyer_mensuel, 0, ',', ' ') }} F</strong></p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <p class="text-muted small mb-1">Date de demande</p>
                            <p class="mb-0">{{ \Carbon\Carbon::parse($location->created_at)->format('d/m/Y') }}</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <p class="text-muted small mb-1">Statut actuel</p>
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

    <!-- Progression du workflow -->
    @php
        $progression = 0;
        switch($location->statut) {
            case 'demande_client':
                $progression = 20;
                break;
            case 'visite_planifiee':
                $progression = 40;
                break;
            case 'contrat_signe':
                $progression = 60;
                break;
            case 'actif':
                $progression = 100;
                break;
            case 'expire':
            case 'termine':
                $progression = 100;
                break;
        }
    @endphp
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h6 class="mb-0">
                <i class="ri-bar-chart-box-line me-2"></i>Progression du processus
            </h6>
        </div>
        <div class="card-body">
            <div class="progress mb-3" style="height: 25px;">
                <div class="progress-bar bg-info" 
                     role="progressbar" 
                     style="width: {{ $progression }}%;" 
                     aria-valuenow="{{ $progression }}" 
                     aria-valuemin="0" 
                     aria-valuemax="100">
                    <strong>{{ $progression }}%</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Chronologie du workflow -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h6 class="mb-0">
                <i class="ri-time-line me-2"></i>Étapes du processus
            </h6>
        </div>
        <div class="card-body">
            <div class="timeline">
                <!-- Étape 1: Demande client -->
                <div class="timeline-item {{ in_array($location->statut, ['demande_client', 'visite_planifiee', 'contrat_signe', 'actif', 'expire', 'termine']) ? 'completed' : '' }}">
                    <div class="timeline-marker">
                        @if(in_array($location->statut, ['demande_client', 'visite_planifiee', 'contrat_signe', 'actif', 'expire', 'termine']))
                            <i class="ri-checkbox-circle-fill text-success"></i>
                        @else
                            <i class="ri-record-circle-line text-muted"></i>
                        @endif
                    </div>
                    <div class="timeline-content">
                        <h6 class="mb-1">Demande reçue</h6>
                        <p class="text-muted small mb-0">Votre demande de location a été enregistrée</p>
                        @if($location->created_at)
                            <p class="text-muted small mb-0 mt-1">
                                <i class="ri-calendar-line me-1"></i>{{ \Carbon\Carbon::parse($location->created_at)->format('d/m/Y à H:i') }}
                            </p>
                        @endif
                    </div>
                </div>

                <!-- Étape 2: Visite planifiée -->
                <div class="timeline-item {{ in_array($location->statut, ['visite_planifiee', 'contrat_signe', 'actif', 'expire', 'termine']) ? 'completed' : ($location->statut == 'demande_client' ? 'active' : '') }}">
                    <div class="timeline-marker">
                        @if(in_array($location->statut, ['visite_planifiee', 'contrat_signe', 'actif', 'expire', 'termine']))
                            <i class="ri-checkbox-circle-fill text-success"></i>
                        @elseif($location->statut == 'demande_client')
                            <i class="ri-time-line text-warning"></i>
                        @else
                            <i class="ri-record-circle-line text-muted"></i>
                        @endif
                    </div>
                    <div class="timeline-content">
                        <h6 class="mb-1">Visite du bien</h6>
                        <p class="text-muted small mb-0">Visite du bien organisée</p>
                        @if($location->date_visite)
                            <p class="text-muted small mb-0 mt-1">
                                <i class="ri-calendar-line me-1"></i>{{ \Carbon\Carbon::parse($location->date_visite)->format('d/m/Y à H:i') }}
                            </p>
                        @endif
                    </div>
                </div>

                <!-- Étape 3: Contrat signé -->
                <div class="timeline-item {{ in_array($location->statut, ['contrat_signe', 'actif', 'expire', 'termine']) ? 'completed' : ($location->statut == 'visite_planifiee' ? 'active' : '') }}">
                    <div class="timeline-marker">
                        @if(in_array($location->statut, ['contrat_signe', 'actif', 'expire', 'termine']))
                            <i class="ri-checkbox-circle-fill text-success"></i>
                        @elseif($location->statut == 'visite_planifiee')
                            <i class="ri-time-line text-warning"></i>
                        @else
                            <i class="ri-record-circle-line text-muted"></i>
                        @endif
                    </div>
                    <div class="timeline-content">
                        <h6 class="mb-1">Contrat de bail signé</h6>
                        <p class="text-muted small mb-0">Le contrat de location a été signé</p>
                        @if($location->date_signature)
                            <p class="text-muted small mb-0 mt-1">
                                <i class="ri-calendar-line me-1"></i>{{ \Carbon\Carbon::parse($location->date_signature)->format('d/m/Y') }}
                            </p>
                        @endif
                    </div>
                </div>

                <!-- Étape 4: Location active -->
                <div class="timeline-item {{ in_array($location->statut, ['actif', 'expire', 'termine']) ? 'completed' : ($location->statut == 'contrat_signe' ? 'active' : '') }}">
                    <div class="timeline-marker">
                        @if(in_array($location->statut, ['actif', 'expire', 'termine']))
                            <i class="ri-checkbox-circle-fill text-success"></i>
                        @elseif($location->statut == 'contrat_signe')
                            <i class="ri-time-line text-warning"></i>
                        @else
                            <i class="ri-record-circle-line text-muted"></i>
                        @endif
                    </div>
                    <div class="timeline-content">
                        <h6 class="mb-1">Location active</h6>
                        <p class="text-muted small mb-0">Vous occupez actuellement le bien</p>
                        @if($location->date_debut)
                            <p class="text-muted small mb-0 mt-1">
                                <i class="ri-calendar-line me-1"></i>Depuis le {{ \Carbon\Carbon::parse($location->date_debut)->format('d/m/Y') }}
                            </p>
                        @endif
                        @if($location->date_fin)
                            <p class="text-muted small mb-0 mt-1">
                                <i class="ri-calendar-line me-1"></i>Jusqu'au {{ \Carbon\Carbon::parse($location->date_fin)->format('d/m/Y') }}
                            </p>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Bouton vers les échéances -->
    <div class="card border-info">
        <div class="card-body text-center py-4">
            <h6 class="mb-3">
                <i class="ri-calendar-check-line text-info me-2"></i>Suivi des paiements
            </h6>
            <p class="text-muted mb-3">Consultez vos échéances de loyer et l'historique de vos paiements</p>
            <a href="{{ route('client.locataire.echeances', $location->id) }}" class="btn btn-info">
                <i class="ri-calendar-event-line me-2"></i>Voir les échéances de paiement
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
                <p class="mb-0">Pour toute question concernant votre location, contactez votre gestionnaire de compte.</p>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 50px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 20px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}

.timeline-item {
    position: relative;
    padding-bottom: 30px;
}

.timeline-item.completed .timeline-marker {
    color: #198754;
}

.timeline-item.active .timeline-marker {
    color: #ffc107;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
        transform: scale(1);
    }
    50% {
        opacity: 0.8;
        transform: scale(1.1);
    }
}

.timeline-marker {
    position: absolute;
    left: -30px;
    top: 0;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: white;
    border-radius: 50%;
    font-size: 1.5rem;
}

.timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border-left: 3px solid #dee2e6;
}

.timeline-item.completed .timeline-content {
    border-left-color: #198754;
}

.timeline-item.active .timeline-content {
    border-left-color: #ffc107;
    background: #fff9e6;
}
</style>
@endsection
