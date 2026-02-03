@extends('backend.layouts.master')
@section('title')
   Détails de la Demande
@endsection
@section('css')
    <style>
        .info-card {
            border-left: 4px solid #0ab39c;
        }
        .progress-timeline {
            position: relative;
            padding: 30px 0;
        }
        .progress-step {
            text-align: center;
            flex: 1;
            position: relative;
        }
        .progress-step .step-circle {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin: 0 auto 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            background: #e9ecef;
            color: #6c757d;
            border: 3px solid #e9ecef;
        }
        .progress-step.completed .step-circle {
            background: #0ab39c;
            color: white;
            border-color: #0ab39c;
        }
        .progress-step.active .step-circle {
            background: #405189;
            color: white;
            border-color: #405189;
            box-shadow: 0 0 0 4px rgba(64, 81, 137, 0.2);
        }
        .progress-step .step-title {
            font-size: 12px;
            font-weight: 600;
            color: #6c757d;
        }
        .progress-step.completed .step-title,
        .progress-step.active .step-title {
            color: #212529;
        }
        .action-card {
            border: 2px solid;
            border-radius: 8px;
        }
        .action-card.primary { border-color: #405189; background: #f8f9fc; }
        .action-card.success { border-color: #0ab39c; background: #f0fdf9; }
        .action-card.warning { border-color: #f7b84b; background: #fffbf0; }
        .action-card.danger { border-color: #f06548; background: #fff5f5; }
        .action-card.info { border-color: #299cdb; background: #f0f9ff; }
        .bg-purple { background-color: #8b5cf6 !important; }
        .bg-teal { background-color: #14b8a6 !important; }
        .btn-purple { background-color: #8b5cf6; border-color: #8b5cf6; color: white; }
        .btn-teal { background-color: #14b8a6; border-color: #14b8a6; color: white; }
    </style>
@endsection
@section('content')
    @component('backend.components.breadcrumb')
        @slot('li_1')
            <a href="{{ route('backend.demandes.index') }}">Demandes</a>
        @endslot
        @slot('title')
            Détails de la demande
        @endslot
    @endcomponent

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ri-check-line me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Barre de progression du workflow -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title mb-4">Progression du workflow</h5>
            <div class="progress-timeline d-flex justify-content-between">
                @php
                    $steps = [
                        'nouvelle' => ['Nouvelle demande', 'ri-file-add-line'],
                        'visite_planifiee' => ['Visite planifiée', 'ri-calendar-check-line'],
                        'visite_effectuee' => ['Visite effectuée', 'ri-check-line'],
                        'documents_recus' => ['Documents reçus', 'ri-file-list-3-line'],
                        'dossier_valide' => ['Dossier validé', 'ri-shield-check-line'],
                        'contrat_genere' => ['Contrat généré', 'ri-file-text-line'],
                        'paiement_en_attente' => ['Paiement en attente', 'ri-money-dollar-circle-line'],
                        'paiement_valide' => ['Paiement validé', 'ri-checkbox-circle-line'],
                    ];
                    $currentStep = $demande->statut;
                    $stepKeys = array_keys($steps);
                    $currentIndex = array_search($currentStep, $stepKeys);
                @endphp
                
                @foreach($steps as $key => $data)
                    @php
                        $index = array_search($key, $stepKeys);
                        $class = '';
                        if ($index < $currentIndex) $class = 'completed';
                        elseif ($key == $currentStep) $class = 'active';
                    @endphp
                    <div class="progress-step {{ $class }}">
                        <div class="step-circle">
                            <i class="{{ $data[1] }}"></i>
                        </div>
                        <div class="step-title">{{ $data[0] }}</div>
                    </div>
                @endforeach
            </div>
            <div class="mt-3 text-center">
                <span class="badge bg-primary fs-6">Progression: {{ $demande->progression }}%</span>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Informations principales -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="ri-information-line me-2"></i>Informations de la demande
                    </h5>
                    <div>{!! $demande->statut_badge !!}</div>
                </div>
                <div class="card-body">
                    
                    <!-- ÉTAPE 1: Informations générales -->
                    <div class="mb-3 pb-2 border-bottom">
                        <div class="d-flex align-items-center mb-2">
                            <div class="flex-shrink-0">
                                <div class="rounded-circle bg-primary bg-gradient d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                    <span class="text-white fw-bold" style="font-size: 14px;">1</span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-2">
                                <h6 class="mb-0 text-primary" style="font-size: 14px;">Informations générales</h6>
                            </div>
                        </div>
                        <div class="ms-4 ps-2">
                            <div class="row mb-2">
                                <div class="col-md-6 mb-1">
                                    <label class="text-muted small mb-0"><i class="ri-calendar-event-line me-1"></i>Date de demande</label>
                                    <p class="mb-0" style="font-size: 13px;">{{ $demande->created_at->format('d/m/Y à H:i') }}</p>
                                </div>
                                @if($demande->date_finalisation)
                                <div class="col-md-6 mb-1">
                                    <label class="text-muted small mb-0"><i class="ri-check-double-line me-1"></i>Date de finalisation</label>
                                    <p class="mb-0" style="font-size: 13px;">{{ $demande->date_finalisation->format('d/m/Y à H:i') }}</p>
                                </div>
                                @endif
                            </div>
                            <div>
                                <label class="text-muted small mb-1"><i class="ri-message-3-line me-1"></i>Message du client</label>
                                <div class="card info-card mb-0">
                                    <div class="card-body py-2">
                                        <p class="mb-0" style="font-size: 13px;">{{ $demande->message }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ÉTAPE 2: Visite (si applicable) -->
                    @if($demande->date_visite || $demande->compte_rendu_visite)
                    <div class="mb-3 pb-2 border-bottom">
                        <div class="d-flex align-items-center mb-2">
                            <div class="flex-shrink-0">
                                <div class="rounded-circle bg-info bg-gradient d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                    <span class="text-white fw-bold" style="font-size: 14px;">2</span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-2">
                                <h6 class="mb-0 text-info" style="font-size: 14px;">Visite du bien</h6>
                            </div>
                        </div>
                        <div class="ms-4 ps-2">
                            @if($demande->date_visite)
                                <div class="alert alert-info py-2 mb-2">
                                    <i class="ri-calendar-check-line me-2"></i><strong>Visite programmée:</strong> {{ \Carbon\Carbon::parse($demande->date_visite)->format('d/m/Y à H:i') }}
                                </div>
                            @endif
                            @if($demande->compte_rendu_visite)
                                <div class="card bg-light mb-0">
                                    <div class="card-body py-2">
                                        <small class="text-muted"><i class="ri-file-text-line me-1"></i>Compte-rendu</small>
                                        <p class="mb-0 mt-1" style="font-size: 13px;">{{ $demande->compte_rendu_visite }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- ÉTAPE 3: Documents (si applicable) -->
                    @if($demande->pieces_demandees || $demande->getMedia('documents_client')->count() > 0)
                    <div class="mb-3 pb-2 border-bottom">
                        <div class="d-flex align-items-center mb-2">
                            <div class="flex-shrink-0">
                                <div class="rounded-circle bg-warning bg-gradient d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                    <span class="text-white fw-bold" style="font-size: 14px;">3</span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-2">
                                <h6 class="mb-0 text-warning" style="font-size: 14px;">Documents du dossier</h6>
                            </div>
                        </div>
                        <div class="ms-4 ps-2">
                            @if($demande->pieces_demandees)
                                <div class="alert alert-primary py-2 mb-2">
                                    <strong><i class="ri-file-list-line me-1"></i>Pièces demandées:</strong>
                                    <p class="mb-0 mt-1" style="font-size: 13px;">{{ $demande->pieces_demandees }}</p>
                                </div>
                            @endif
                            @if($demande->getMedia('documents_client')->count() > 0)
                                <div>
                                    <small class="text-muted"><i class="ri-folder-open-line me-1"></i>Documents uploadés ({{ $demande->getMedia('documents_client')->count() }})</small>
                                    <div class="list-group mt-1 mb-0">
                                        @foreach($demande->getMedia('documents_client') as $doc)
                                            <a href="{{ $doc->getUrl() }}" target="_blank" class="list-group-item list-group-item-action py-2 d-flex justify-content-between align-items-center" style="font-size: 13px;">
                                                <span><i class="ri-file-line me-2 text-primary"></i>{{ $doc->file_name }}</span>
                                                <span><span class="badge bg-secondary">{{ number_format($doc->size / 1024, 0) }} KB</span></span>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- ÉTAPE 4: Contrat (si applicable) -->
                    @if($demande->contrat_url || $demande->getFirstMedia('contrat'))
                    <div class="mb-3 pb-2 border-bottom">
                        <div class="d-flex align-items-center mb-2">
                            <div class="flex-shrink-0">
                                <div class="rounded-circle bg-success bg-gradient d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                    <span class="text-white fw-bold" style="font-size: 14px;">4</span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-2">
                                <h6 class="mb-0 text-success" style="font-size: 14px;">Contrat</h6>
                            </div>
                        </div>
                        <div class="ms-4 ps-2">
                            @php
                                $contratMedia = $demande->getFirstMedia('contrat');
                                $contratUrl = $contratMedia ? $contratMedia->getUrl() : $demande->contrat_url;
                            @endphp
                            <div class="card mb-0">
                                <div class="card-body py-2">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <i class="ri-file-pdf-line text-success fs-3 me-2"></i>
                                            <div>
                                                <p class="mb-0 fw-semibold" style="font-size: 13px;">Contrat signé</p>
                                                @if($demande->date_signature_contrat)
                                                    <small class="text-muted">{{ $demande->date_signature_contrat->format('d/m/Y') }}</small>
                                                @endif
                                            </div>
                                        </div>
                                        <a href="{{ $contratUrl }}" target="_blank" class="btn btn-success btn-sm py-1">
                                            <i class="ri-download-line"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- ÉTAPE 5: Paiement (si applicable) -->
                    @if($demande->statut == 'paiement_en_attente' || $demande->statut == 'paiement_valide')
                    <div class="mb-3 pb-2 border-bottom">
                        <div class="d-flex align-items-center mb-2">
                            <div class="flex-shrink-0">
                                <div class="rounded-circle bg-purple bg-gradient d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                    <span class="text-white fw-bold" style="font-size: 14px;">5</span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-2">
                                <h6 class="mb-0" style="color: #8b5cf6; font-size: 14px;">Paiement</h6>
                            </div>
                        </div>
                        <div class="ms-4 ps-2">
                            <div class="card mb-0">
                                <div class="card-body py-2">
                                    <!-- Prix du bien / Loyer -->
                                    <div class="alert alert-primary py-2 mb-2" style="font-size: 13px;">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span><i class="ri-price-tag-3-line me-1"></i><strong>{{ $demande->annonce->type_transaction == 'location' ? 'Loyer mensuel' : 'Prix du bien' }}:</strong></span>
                                            <span class="fw-bold">{{ number_format($demande->annonce->prix, 0, ',', ' ') }} FCFA</span>
                                        </div>
                                    </div>

                                    @if($demande->annonce->type_transaction == 'vente' && $demande->commission_agence)
                                        <!-- Commission pour vente (information agence) -->
                                        <div class="alert alert-info py-2 mb-2" style="font-size: 13px;">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <strong><i class="ri-information-line me-1"></i>Commission agence (info interne):</strong>
                                                    <div class="mt-1">
                                                        @if($demande->type_commission === 'pourcentage')
                                                            {{ $demande->commission_agence }}%
                                                            @php
                                                                $montantCommission = ($demande->annonce->prix * $demande->commission_agence) / 100;
                                                            @endphp
                                                            = {{ number_format($montantCommission, 0, ',', ' ') }} FCFA
                                                        @else
                                                            {{ number_format($demande->commission_agence, 0, ',', ' ') }} FCFA (Fixe)
                                                        @endif
                                                    </div>
                                                </div>
                                                <span class="badge bg-light text-dark border">Non inclus dans le paiement client</span>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    @if($demande->details_paiement && is_array($demande->details_paiement))
                                        <div class="border-top pt-2">
                                            <h6 class="mb-2" style="font-size: 13px;"><i class="ri-wallet-3-line me-1"></i>Informations de paiement</h6>
                                            
                                            <table class="table table-sm table-borderless mb-2" style="font-size: 13px;">
                                                @if(isset($demande->details_paiement['montant']))
                                                <tr>
                                                    <td class="text-muted py-1" width="50%"><i class="ri-money-dollar-circle-line me-1"></i>Montant payé:</td>
                                                    <td class="text-end fw-semibold text-success py-1">{{ number_format($demande->details_paiement['montant'], 0, ',', ' ') }} FCFA</td>
                                                </tr>
                                                @endif
                                                @if(isset($demande->details_paiement['date_paiement']))
                                                <tr>
                                                    <td class="text-muted py-1"><i class="ri-calendar-event-line me-1"></i>Date de paiement:</td>
                                                    <td class="text-end fw-semibold py-1">{{ \Carbon\Carbon::parse($demande->details_paiement['date_paiement'])->format('d/m/Y') }}</td>
                                                </tr>
                                                @endif
                                                @if(isset($demande->details_paiement['mode_paiement']))
                                                <tr>
                                                    <td class="text-muted py-1"><i class="ri-bank-card-line me-1"></i>Méthode de paiement:</td>
                                                    <td class="text-end fw-semibold py-1">{{ $demande->details_paiement['mode_paiement'] }}</td>
                                                </tr>
                                                @endif
                                                @if(isset($demande->details_paiement['reference']) && $demande->details_paiement['reference'])
                                                <tr>
                                                    <td class="text-muted py-1"><i class="ri-hashtag me-1"></i>Référence:</td>
                                                    <td class="text-end fw-semibold py-1">{{ $demande->details_paiement['reference'] }}</td>
                                                </tr>
                                                @endif
                                                @if(isset($demande->details_paiement['montant']))
                                                <tr class="border-top">
                                                    <td class="py-1" width="50%">
                                                        <strong class="{{ ($demande->annonce->prix - $demande->details_paiement['montant']) == 0 ? 'text-success' : 'text-danger' }}">
                                                            <i class="ri-alert-line me-1"></i>Reste à payer:
                                                        </strong>
                                                    </td>
                                                    <td class="text-end py-1">
                                                        <strong class="{{ ($demande->annonce->prix - $demande->details_paiement['montant']) == 0 ? 'text-success' : 'text-danger' }}">
                                                            {{ number_format($demande->annonce->prix - $demande->details_paiement['montant'], 0, ',', ' ') }} FCFA
                                                        </strong>
                                                    </td>
                                                </tr>
                                                @endif
                                            </table>
                                            
                                            @if(isset($demande->details_paiement['notes']) && $demande->details_paiement['notes'])
                                                <div class="card bg-light mb-0">
                                                    <div class="card-body py-2">
                                                        <small class="text-muted"><i class="ri-file-text-line me-1"></i>Notes</small>
                                                        <p class="mb-0 mt-1" style="font-size: 13px;">{{ $demande->details_paiement['notes'] }}</p>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                    
                                    @if($demande->statut == 'paiement_valide')
                                        <div class="alert alert-success py-2 mt-2 mb-0" style="font-size: 13px;">
                                            <i class="ri-check-double-line me-1"></i><strong>Paiement validé et finalisé</strong>
                                            @if($demande->date_finalisation)
                                                <div class="mt-1">Le {{ $demande->date_finalisation->format('d/m/Y à H:i') }}</div>
                                            @endif
                                        </div>
                                    @elseif($demande->statut == 'paiement_en_attente')
                                        <div class="alert alert-warning py-2 mt-2 mb-0" style="font-size: 13px;">
                                            <i class="ri-time-line me-1"></i><strong>En attente de validation</strong>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Motif de refus (si applicable) -->
                    @if($demande->motif_refus)
                    <div class="mb-3 pb-2 border-bottom">
                        <div class="d-flex align-items-center mb-2">
                            <div class="flex-shrink-0">
                                <div class="rounded-circle bg-danger bg-gradient d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                    <i class="ri-close-circle-line text-white"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-2">
                                <h6 class="mb-0 text-danger" style="font-size: 14px;">Motif de clôture</h6>
                            </div>
                        </div>
                        <div class="ms-4 ps-2">
                            <div class="alert alert-danger py-2 mb-0">
                                <p class="mb-0" style="font-size: 13px;">{{ $demande->motif_refus }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Notes internes (si applicable) -->
                    @if($demande->note_admin)
                    <div class="mb-0">
                        <div class="d-flex align-items-center mb-2">
                            <div class="flex-shrink-0">
                                <div class="rounded-circle bg-warning bg-gradient d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                    <i class="ri-sticky-note-line text-white"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-2">
                                <h6 class="mb-0 text-warning" style="font-size: 14px;">Notes internes</h6>
                            </div>
                        </div>
                        <div class="ms-4 ps-2">
                            <div class="card bg-warning bg-opacity-10 mb-0">
                                <div class="card-body py-2">
                                    <p class="mb-0" style="font-size: 13px;">{{ $demande->note_admin }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Actions disponibles selon le statut -->
            @if(!$demande->is_cloture)
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0 text-white">Actions disponibles</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            @switch($demande->statut)
                                @case('nouvelle')
                                    <div class="col-md-6">
                                        <div class="action-card info p-3 h-100">
                                            <h6><i class="ri-mail-send-line me-2"></i>Envoyer le contrat</h6>
                                            <p class="text-muted small mb-2">Envoyer les documents du contrat par email</p>
                                            <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#envoyerContratModal">
                                                Envoyer
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="action-card danger p-3 h-100">
                                            <h6><i class="ri-close-line me-2"></i>Clôturer la demande</h6>
                                            <p class="text-muted small mb-2">Refuser ou abandonner</p>
                                            <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#cloturerDemandeModal">
                                                Clôturer
                                            </button>
                                        </div>
                                    </div>
                                @break

                                @case('contrat_envoye')
                                    <div class="col-md-6">
                                        <div class="action-card info p-3 h-100">
                                            <h6><i class="ri-calendar-line me-2"></i>Planifier une visite</h6>
                                            <p class="text-muted small mb-2">Après accord du client (externe)</p>
                                            <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#planifierVisiteModal">
                                                Planifier
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="action-card danger p-3 h-100">
                                            <h6><i class="ri-close-line me-2"></i>Clôturer</h6>
                                            <p class="text-muted small mb-2">Si pas d'accord ou abandon</p>
                                            <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#cloturerDemandeModal">
                                                Clôturer
                                            </button>
                                        </div>
                                    </div>
                                @break

                                @case('visite_planifiee')
                                    <div class="col-md-12">
                                        <div class="action-card success p-3">
                                            <h6><i class="ri-check-line me-2"></i>Marquer la visite comme effectuée</h6>
                                            <p class="text-muted small mb-2">Enregistrer le compte-rendu de visite</p>
                                            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#visiteEffectueeModal">
                                                Visite effectuée
                                            </button>
                                        </div>
                                    </div>
                                @break

                                @case('visite_effectuee')
                                    <div class="col-md-6">
                                        <div class="action-card warning p-3 h-100">
                                            <h6><i class="ri-money-dollar-circle-line me-2"></i>Configurer le paiement</h6>
                                            <p class="text-muted small mb-2">Définir les montants à payer</p>
                                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#configurerPaiementModal">
                                                Configurer
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="action-card danger p-3 h-100">
                                            <h6><i class="ri-close-line me-2"></i>Clôturer</h6>
                                            <p class="text-muted small mb-2">Si abandon</p>
                                            <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#cloturerDemandeModal">
                                                Clôturer
                                            </button>
                                        </div>
                                    </div>
                                @break

                                @case('paiement_en_attente')
                                    <div class="col-md-6">
                                        <div class="action-card success p-3 h-100">
                                            <h6><i class="ri-check-double-line me-2"></i>Valider le paiement</h6>
                                            <p class="text-muted small mb-2">Confirmer réception et remettre les clés</p>
                                            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#validerPaiementModal">
                                                Valider
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="action-card danger p-3 h-100">
                                            <h6><i class="ri-close-line me-2"></i>Clôturer</h6>
                                            <p class="text-muted small mb-2">Si paiement non reçu</p>
                                            <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#cloturerDemandeModal">
                                                Clôturer
                                            </button>
                                        </div>
                                    </div>
                                @break

                                @case('paiement_valide')
                                    <div class="col-md-12">
                                        <div class="alert alert-success">
                                            <i class="ri-check-double-line me-2"></i>Transaction finalisée ! Le bien a été marqué comme {{ $demande->annonce->type_transaction == 'location' ? 'loué' : 'vendu' }}.
                                        </div>
                                    </div>
                                @break
                            @endswitch
                        </div>
                    </div>
                </div>
            @else
                <div class="alert alert-secondary">
                    <i class="ri-information-line me-2"></i>Cette demande est clôturée. Aucune action disponible.
                </div>
            @endif

            <!-- Si la demande est validée mais pas encore convertie en vente/location -->
            @if($demande->statut == 'paiement_valide' && !$demande->is_convertie)
                <div class="card border-success">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title mb-0 text-white">Créer le suivi de {{ $demande->annonce->type_transaction }}</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-3">
                            Le paiement a été validé. Vous pouvez maintenant créer le suivi de cette {{ $demande->annonce->type_transaction }} 
                            pour gérer les paiements et échéances.
                        </p>
                        @if($demande->annonce->type_transaction == 'vente')
                            <a href="{{ route('backend.ventes.create-from-demande', $demande->id) }}" class="btn btn-success w-100">
                                <i class="ri-shopping-bag-line me-2"></i>Créer le suivi de vente
                            </a>
                        @else
                            <a href="{{ route('backend.locations.create-from-demande', $demande->id) }}" class="btn btn-success w-100">
                                <i class="ri-key-line me-2"></i>Créer le suivi de location
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Si la demande est déjà convertie -->
            @if($demande->is_convertie)
                <div class="card border-info">
                    <div class="card-header bg-info text-white">
                        <h5 class="card-title mb-0 text-white">
                            <i class="ri-link me-2"></i>Lié au suivi {{ $demande->annonce->type_transaction }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-3">Cette demande a été convertie en suivi de {{ $demande->annonce->type_transaction }}.</p>
                        @if($demande->vente)
                            <a href="{{ route('backend.ventes.show', $demande->vente->id) }}" class="btn btn-info w-100">
                                <i class="ri-shopping-bag-line me-2"></i>Voir le suivi de vente #{{ $demande->vente->id }}
                            </a>
                        @endif
                        @if($demande->location)
                            <a href="{{ route('backend.locations.show', $demande->location->id) }}" class="btn btn-info w-100">
                                <i class="ri-key-line me-2"></i>Voir le suivi de location #{{ $demande->location->id }}
                            </a>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Informations client -->
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0 text-white">Informations du client</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted mb-1">Nom</label>
                        <p class="fw-semibold">{{ $demande->user->username }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted mb-1">Email</label>
                        <p>{{ $demande->user->email }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted mb-1">Téléphone</label>
                        <p>{{ $demande->user->phone ?? 'Non renseigné' }}</p>
                    </div>
                    <a href="mailto:{{ $demande->user->email }}" class="btn btn-sm btn-outline-primary w-100 mb-2">
                        <i class="ri-mail-line"></i> Envoyer un email
                    </a>
                    @if($demande->user->phone)
                        <a href="tel:{{ $demande->user->phone }}" class="btn btn-sm btn-outline-success w-100">
                            <i class="ri-phone-line"></i> Appeler
                        </a>
                    @endif
                </div>
            </div>

            <!-- Informations du bien -->
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0 text-white">Bien concerné</h5>
                </div>
                <div class="card-body">
                    @if($demande->annonce->hasMedia('image_principale'))
                        <img src="{{ $demande->annonce->getFirstMediaUrl('image_principale') }}" 
                             class="img-fluid rounded mb-3" alt="{{ $demande->annonce->titre }}">
                    @endif
                    <h6>{{ $demande->annonce->titre }}</h6>
                    <p class="text-muted mb-2">Réf: {{ $demande->annonce->reference }}</p>
                    <p class="fs-5 fw-bold text-primary mb-2">{{ number_format($demande->annonce->prix, 0, ',', ' ') }} FCFA</p>
                    <div class="mb-2">
                        <span class="badge bg-info">{{ ucfirst($demande->annonce->type_transaction) }}</span>
                        <span class="badge bg-secondary">{{ $demande->annonce->typeBien->name ?? 'N/A' }}</span>
                    </div>
                    <a href="{{ route('properties.show', $demande->annonce->slug) }}" 
                       class="btn btn-sm btn-outline-primary w-100" target="_blank">
                        <i class="ri-external-link-line"></i> Voir l'annonce
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Modaux -->
    @include('backend.pages.demandes.partials.modals-workflow', ['demande' => $demande])
@endsection

@section('script')
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
    <script>
        // Rouvrir le modal en cas d'erreur de validation
        @if ($errors->any())
            var modalId = '{{ old("_modal") }}';
            if (modalId) {
                var myModal = new bootstrap.Modal(document.getElementById(modalId));
                myModal.show();
            }
        @endif
    </script>
@endsection
