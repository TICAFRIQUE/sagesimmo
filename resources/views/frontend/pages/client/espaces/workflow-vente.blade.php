@extends('frontend.pages.client.layout')

@section('title', 'Workflow de la vente')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center">
                <a href="{{ route('client.acheteur') }}" class="btn btn-sm btn-outline-secondary me-3">
                    <i class="ri-arrow-left-line"></i> Retour
                </a>
                <div>
                    <h4 class="mb-1">
                        <i class="ri-flow-chart text-primary"></i> Workflow de la vente
                    </h4>
                    <p class="text-muted mb-0 small">{{ $vente->annonce->titre ?? 'Bien' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Informations du bien -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h6 class="mb-0">
                <i class="ri-home-4-line me-2"></i>Informations du bien
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 mb-3">
                    @if($vente->annonce && $vente->annonce->hasMedia('images'))
                        <img src="{{ $vente->annonce->getFirstMediaUrl('images') }}" 
                             class="img-fluid rounded" 
                             alt="{{ $vente->annonce->titre }}"
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
                            <p class="mb-0"><strong>{{ $vente->annonce->reference ?? 'N/A' }}</strong></p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <p class="text-muted small mb-1">Type de bien</p>
                            <p class="mb-0">{{ $vente->annonce->typeBien->nom ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <p class="text-muted small mb-1">Localisation</p>
                            <p class="mb-0">{{ $vente->annonce->ville ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <p class="text-muted small mb-1">Prix de vente</p>
                            <p class="mb-0 text-success"><strong>{{ number_format($vente->prix_vente, 0, ',', ' ') }} FCFA</strong></p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <p class="text-muted small mb-1">Date de demande</p>
                            <p class="mb-0">{{ \Carbon\Carbon::parse($vente->created_at)->format('d/m/Y') }}</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <p class="text-muted small mb-1">Statut actuel</p>
                            <p class="mb-0">{!! $vente->statut_badge !!}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Progression du workflow -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h6 class="mb-0">
                <i class="ri-bar-chart-box-line me-2"></i>Progression du processus
            </h6>
        </div>
        <div class="card-body">
            <div class="progress mb-3" style="height: 25px;">
                <div class="progress-bar bg-primary" 
                     role="progressbar" 
                     style="width: {{ $vente->progression }}%;" 
                     aria-valuenow="{{ $vente->progression }}" 
                     aria-valuemin="0" 
                     aria-valuemax="100">
                    <strong>{{ $vente->progression }}%</strong>
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
                <div class="timeline-item {{ in_array($vente->statut, ['demande_client', 'fiche_envoyee', 'visite_planifiee', 'offre_acceptee', 'terminee']) ? 'completed' : '' }}">
                    <div class="timeline-marker">
                        @if(in_array($vente->statut, ['demande_client', 'fiche_envoyee', 'visite_planifiee', 'offre_acceptee', 'terminee']))
                            <i class="ri-checkbox-circle-fill text-success"></i>
                        @else
                            <i class="ri-record-circle-line text-muted"></i>
                        @endif
                    </div>
                    <div class="timeline-content">
                        <h6 class="mb-1">Demande reçue</h6>
                        <p class="text-muted small mb-0">Votre demande d'achat a été enregistrée</p>
                        @if($vente->created_at)
                            <p class="text-muted small mb-0 mt-1">
                                <i class="ri-calendar-line me-1"></i>{{ \Carbon\Carbon::parse($vente->created_at)->format('d/m/Y à H:i') }}
                            </p>
                        @endif
                    </div>
                </div>

                <!-- Étape 2: Fiche envoyée -->
                <div class="timeline-item {{ in_array($vente->statut, ['fiche_envoyee', 'visite_planifiee', 'offre_acceptee', 'terminee']) ? 'completed' : ($vente->statut == 'demande_client' ? 'active' : '') }}">
                    <div class="timeline-marker">
                        @if(in_array($vente->statut, ['fiche_envoyee', 'visite_planifiee', 'offre_acceptee', 'terminee']))
                            <i class="ri-checkbox-circle-fill text-success"></i>
                        @elseif($vente->statut == 'demande_client')
                            <i class="ri-time-line text-warning"></i>
                        @else
                            <i class="ri-record-circle-line text-muted"></i>
                        @endif
                    </div>
                    <div class="timeline-content">
                        <h6 class="mb-1">Fiche du bien envoyée</h6>
                        <p class="text-muted small mb-0">La fiche détaillée du bien vous a été transmise</p>
                    </div>
                </div>

                <!-- Étape 3: Visite planifiée -->
                <div class="timeline-item {{ in_array($vente->statut, ['visite_planifiee', 'offre_acceptee', 'terminee']) ? 'completed' : ($vente->statut == 'fiche_envoyee' ? 'active' : '') }}">
                    <div class="timeline-marker">
                        @if(in_array($vente->statut, ['visite_planifiee', 'offre_acceptee', 'terminee']))
                            <i class="ri-checkbox-circle-fill text-success"></i>
                        @elseif($vente->statut == 'fiche_envoyee')
                            <i class="ri-time-line text-warning"></i>
                        @else
                            <i class="ri-record-circle-line text-muted"></i>
                        @endif
                    </div>
                    <div class="timeline-content">
                        <h6 class="mb-1">Visite du bien</h6>
                        <p class="text-muted small mb-0">Visite du bien organisée</p>
                        @if($vente->date_visite)
                            <p class="text-muted small mb-0 mt-1">
                                <i class="ri-calendar-line me-1"></i>{{ \Carbon\Carbon::parse($vente->date_visite)->format('d/m/Y à H:i') }}
                            </p>
                        @endif
                        @if($vente->compte_rendu_visite)
                            <div class="alert alert-info alert-sm mt-2 mb-0">
                                <strong>Compte-rendu :</strong> {{ $vente->compte_rendu_visite }}
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Étape 4: Offre acceptée -->
                <div class="timeline-item {{ in_array($vente->statut, ['offre_acceptee', 'terminee']) ? 'completed' : ($vente->statut == 'visite_planifiee' ? 'active' : '') }}">
                    <div class="timeline-marker">
                        @if(in_array($vente->statut, ['offre_acceptee', 'terminee']))
                            <i class="ri-checkbox-circle-fill text-success"></i>
                        @elseif($vente->statut == 'visite_planifiee')
                            <i class="ri-time-line text-warning"></i>
                        @else
                            <i class="ri-record-circle-line text-muted"></i>
                        @endif
                    </div>
                    <div class="timeline-content">
                        <h6 class="mb-1">Offre acceptée</h6>
                        <p class="text-muted small mb-0">Votre offre d'achat a été acceptée</p>
                        @if($vente->date_vente)
                            <p class="text-muted small mb-0 mt-1">
                                <i class="ri-calendar-line me-1"></i>{{ \Carbon\Carbon::parse($vente->date_vente)->format('d/m/Y') }}
                            </p>
                        @endif
                    </div>
                </div>

                <!-- Étape 5: Vente finalisée -->
                <div class="timeline-item {{ $vente->statut == 'terminee' ? 'completed' : ($vente->statut == 'offre_acceptee' ? 'active' : '') }}">
                    <div class="timeline-marker">
                        @if($vente->statut == 'terminee')
                            <i class="ri-checkbox-circle-fill text-success"></i>
                        @elseif($vente->statut == 'offre_acceptee')
                            <i class="ri-time-line text-warning"></i>
                        @else
                            <i class="ri-record-circle-line text-muted"></i>
                        @endif
                    </div>
                    <div class="timeline-content">
                        <h6 class="mb-1">Vente finalisée</h6>
                        <p class="text-muted small mb-0">Le processus d'achat est terminé, vous pouvez récupérer les clés</p>
                        @if($vente->date_finalisation)
                            <p class="text-muted small mb-0 mt-1">
                                <i class="ri-calendar-line me-1"></i>{{ \Carbon\Carbon::parse($vente->date_finalisation)->format('d/m/Y à H:i') }}
                            </p>
                        @endif
                    </div>
                </div>

                <!-- Étape Annulée (si applicable) -->
                @if($vente->statut == 'annulee')
                    <div class="timeline-item cancelled">
                        <div class="timeline-marker">
                            <i class="ri-close-circle-fill text-danger"></i>
                        </div>
                        <div class="timeline-content">
                            <h6 class="mb-1 text-danger">Vente annulée</h6>
                            <p class="text-muted small mb-0">Le processus d'achat a été annulé</p>
                            @if($vente->note_admin)
                                <div class="alert alert-danger alert-sm mt-2 mb-0">
                                    <strong>Motif :</strong> {{ $vente->note_admin }}
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Informations supplémentaires -->
    @if($vente->message_client)
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0">
                    <i class="ri-message-3-line me-2"></i>Votre message
                </h6>
            </div>
            <div class="card-body">
                <p class="mb-0">{{ $vente->message_client }}</p>
            </div>
        </div>
    @endif

    <!-- Bouton vers l'état financier -->
    <div class="card mb-4">
        <div class="card-body text-center">
            <h6 class="mb-3">
                <i class="ri-money-dollar-circle-line me-2"></i>Consultez votre situation financière
            </h6>
            <a href="{{ route('client.acheteur.situation-financiere', $vente->id) }}" class="btn btn-success btn-lg">
                <i class="ri-money-dollar-circle-line me-2"></i>Voir l'état financier
            </a>
            <p class="text-muted small mb-0 mt-2">Suivez vos paiements et l'avancement financier de votre achat</p>
        </div>
    </div>

    <!-- Note informative -->
    <div class="alert alert-light border">
        <div class="d-flex">
            <div class="flex-shrink-0">
                <i class="ri-information-line text-primary fs-4"></i>
            </div>
            <div class="flex-grow-1 ms-3">
                <h6 class="alert-heading">Besoin d'aide ?</h6>
                <p class="mb-0">Pour toute question concernant le processus d'achat, contactez votre gestionnaire de compte.</p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .timeline {
        position: relative;
        padding-left: 40px;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 12px;
        top: 10px;
        bottom: 10px;
        width: 2px;
        background: #e9ecef;
    }

    .timeline-item {
        position: relative;
        margin-bottom: 30px;
    }

    .timeline-item:last-child {
        margin-bottom: 0;
    }

    .timeline-marker {
        position: absolute;
        left: -40px;
        top: 0;
        width: 25px;
        height: 25px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: white;
        border-radius: 50%;
        font-size: 1.5rem;
    }

    .timeline-item.completed .timeline-content h6 {
        color: #198754;
    }

    .timeline-item.active .timeline-content h6 {
        color: #ffc107;
    }

    .timeline-item.cancelled .timeline-content h6 {
        color: #dc3545;
    }

    .timeline-content {
        padding: 10px 15px;
        background: #f8f9fa;
        border-radius: 8px;
        border-left: 3px solid #e9ecef;
    }

    .timeline-item.completed .timeline-content {
        border-left-color: #198754;
    }

    .timeline-item.active .timeline-content {
        border-left-color: #ffc107;
    }

    .timeline-item.cancelled .timeline-content {
        border-left-color: #dc3545;
    }

    .alert-sm {
        padding: 0.5rem;
        font-size: 0.875rem;
    }

    @media (max-width: 768px) {
        .timeline {
            padding-left: 30px;
        }

        .timeline-marker {
            left: -32px;
            width: 20px;
            height: 20px;
            font-size: 1.2rem;
        }

        .timeline::before {
            left: 8px;
        }
    }
</style>
@endsection
