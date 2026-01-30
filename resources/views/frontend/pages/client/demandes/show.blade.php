@extends('frontend.pages.client.layout')

@section('client-content')
<div class="client-content">
    <div class="mb-4">
        <a href="{{ route('client.demandes') }}" class="btn btn-sm btn-outline-secondary">
            <i class="ri-arrow-left-line"></i> Retour à mes demandes
        </a>
    </div>

    <h2 class="mb-4">
        <i class="ri-file-list-line"></i> Détails de la demande
    </h2>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="ri-check-line"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Barre de progression -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="mb-3">Progression de votre demande</h5>
            <div class="progress" style="height: 30px;">
                <div class="progress-bar bg-success" role="progressbar" 
                     style="width: {{ $demande->progression }}%"
                     aria-valuenow="{{ $demande->progression }}" 
                     aria-valuemin="0" aria-valuemax="100">
                    {{ $demande->progression }}%
                </div>
            </div>
            <div class="mt-3">
                <strong>Statut actuel:</strong> {!! $demande->statut_badge !!}
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Informations principales -->
        <div class="col-lg-8">
            <!-- Étapes du processus -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="ri-route-line"></i> Suivi du processus
                    </h5>
                </div>
                <div class="card-body">
                    <div class="timeline-workflow">
                        <!-- Nouvelle demande -->
                        <div class="timeline-item {{ $demande->statut == 'nouvelle' ? 'active' : ($demande->progression > 10 ? 'completed' : '') }}">
                            <div class="timeline-marker">
                                <i class="ri-file-add-line"></i>
                            </div>
                            <div class="timeline-content">
                                <h6>Demande envoyée</h6>
                                <p>{{ $demande->created_at->format('d/m/Y à H:i') }}</p>
                            </div>
                        </div>

                        <!-- Visite planifiée -->
                        @if($demande->progression >= 20)
                            <div class="timeline-item {{ $demande->statut == 'visite_planifiee' ? 'active' : ($demande->progression > 20 ? 'completed' : '') }}">
                                <div class="timeline-marker">
                                    <i class="ri-calendar-check-line"></i>
                                </div>
                                <div class="timeline-content">
                                    <h6>Visite planifiée</h6>
                                    @if($demande->date_visite)
                                        <p>{{ \Carbon\Carbon::parse($demande->date_visite)->format('d/m/Y à H:i') }}</p>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Visite effectuée -->
                        @if($demande->progression >= 35)
                            <div class="timeline-item {{ $demande->statut == 'visite_effectuee' ? 'active' : ($demande->progression > 35 ? 'completed' : '') }}">
                                <div class="timeline-marker">
                                    <i class="ri-home-smile-line"></i>
                                </div>
                                <div class="timeline-content">
                                    <h6>Visite effectuée</h6>
                                    @if($demande->compte_rendu_visite)
                                        <p>{{ Str::limit($demande->compte_rendu_visite, 100) }}</p>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Documents reçus -->
                        @if($demande->progression >= 50)
                            <div class="timeline-item {{ $demande->statut == 'documents_recus' ? 'active' : ($demande->progression > 50 ? 'completed' : '') }}">
                                <div class="timeline-marker">
                                    <i class="ri-file-upload-line"></i>
                                </div>
                                <div class="timeline-content">
                                    <h6>Documents reçus</h6>
                                    <p>Vos documents sont en cours de vérification</p>
                                </div>
                            </div>
                        @endif

                        <!-- Dossier validé -->
                        @if($demande->progression >= 65)
                            <div class="timeline-item {{ $demande->statut == 'dossier_valide' ? 'active' : ($demande->progression > 65 ? 'completed' : '') }}">
                                <div class="timeline-marker">
                                    <i class="ri-checkbox-circle-line"></i>
                                </div>
                                <div class="timeline-content">
                                    <h6>Dossier validé</h6>
                                    <p>Votre dossier a été accepté</p>
                                </div>
                            </div>
                        @endif

                        <!-- Contrat généré -->
                        @if($demande->progression >= 80)
                            <div class="timeline-item {{ $demande->statut == 'contrat_genere' ? 'active' : ($demande->progression > 80 ? 'completed' : '') }}">
                                <div class="timeline-marker">
                                    <i class="ri-file-text-line"></i>
                                </div>
                                <div class="timeline-content">
                                    <h6>Contrat généré</h6>
                                    @if($demande->hasMedia('contrat'))
                                        <a href="{{ $demande->getFirstMediaUrl('contrat') }}" target="_blank" class="btn btn-sm btn-primary">
                                            <i class="ri-download-line"></i> Télécharger le contrat
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Paiement -->
                        @if($demande->progression >= 90)
                            <div class="timeline-item {{ in_array($demande->statut, ['paiement_en_attente', 'paiement_valide']) ? 'active' : '' }}">
                                <div class="timeline-marker">
                                    <i class="ri-money-dollar-circle-line"></i>
                                </div>
                                <div class="timeline-content">
                                    <h6>Paiement</h6>
                                    @if($demande->montant_total_paiement)
                                        <p><strong>Montant total:</strong> {{ number_format($demande->montant_total_paiement, 0, ',', ' ') }} FCFA</p>
                                        @if($demande->statut_paiement == 'complet')
                                            <span class="badge bg-success">Paiement validé</span>
                                        @else
                                            <span class="badge bg-warning">En attente de paiement</span>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Finalisation -->
                        @if($demande->statut == 'paiement_valide')
                            <div class="timeline-item completed">
                                <div class="timeline-marker">
                                    <i class="ri-check-double-line"></i>
                                </div>
                                <div class="timeline-content">
                                    <h6>Processus finalisé</h6>
                                    @if($demande->date_finalisation)
                                        <p>{{ $demande->date_finalisation->format('d/m/Y') }}</p>
                                    @endif
                                    <span class="badge bg-success">Félicitations ! Le bien est à vous !</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Zone d'upload de documents -->
            @if($demande->pieces_demandees && $demande->statut == 'visite_effectuee')
                <div class="card mb-4 border-warning">
                    <div class="card-header bg-warning text-white">
                        <h5 class="mb-0">
                            <i class="ri-file-upload-line"></i> Documents demandés
                        </h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Pièces à fournir:</strong></p>
                        <p>{{ $demande->pieces_demandees }}</p>

                        <hr>

                        <form action="{{ route('client.demandes.upload-documents', $demande->id) }}" 
                              method="POST" 
                              enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="documents" class="form-label">Uploader vos documents *</label>
                                <input type="file" 
                                       class="form-control" 
                                       id="documents" 
                                       name="documents[]" 
                                       multiple 
                                       accept=".pdf,.jpg,.jpeg,.png"
                                       required>
                                <small class="text-muted">
                                    Formats acceptés: PDF, JPG, PNG. Taille max: 5 MB par fichier.
                                </small>
                            </div>
                            <button type="submit" class="btn btn-warning">
                                <i class="ri-upload-2-line"></i> Envoyer les documents
                            </button>
                        </form>

                        @if($demande->hasMedia('documents_client'))
                            <hr>
                            <h6>Documents déjà envoyés:</h6>
                            <div class="list-group">
                                @foreach($demande->getMedia('documents_client') as $media)
                                    <a href="{{ $media->getUrl() }}" 
                                       class="list-group-item list-group-item-action"
                                       target="_blank">
                                        <i class="ri-file-line"></i> {{ $media->file_name }}
                                        <small class="text-muted">({{ $media->human_readable_size }})</small>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Détails paiement -->
            @if($demande->statut_paiement)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="ri-money-dollar-box-line"></i> Détails du paiement
                        </h5>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <tr>
                                <td>Caution:</td>
                                <td class="text-end"><strong>{{ number_format($demande->montant_caution ?? 0, 0, ',', ' ') }} FCFA</strong></td>
                            </tr>
                            <tr>
                                <td>Premier loyer/acompte:</td>
                                <td class="text-end"><strong>{{ number_format($demande->montant_loyer_premier ?? 0, 0, ',', ' ') }} FCFA</strong></td>
                            </tr>
                            <tr>
                                <td>Frais d'agence:</td>
                                <td class="text-end"><strong>{{ number_format($demande->montant_frais_agence ?? 0, 0, ',', ' ') }} FCFA</strong></td>
                            </tr>
                            <tr class="table-primary">
                                <td><strong>TOTAL:</strong></td>
                                <td class="text-end"><strong class="fs-5">{{ number_format($demande->montant_total_paiement ?? 0, 0, ',', ' ') }} FCFA</strong></td>
                            </tr>
                        </table>
                        
                        @if($demande->statut_paiement == 'complet')
                            <div class="alert alert-success mb-0">
                                <i class="ri-check-double-line"></i> Paiement complet reçu et validé !
                            </div>
                        @else
                            <div class="alert alert-warning mb-0">
                                <i class="ri-information-line"></i> En attente de votre paiement. Veuillez contacter l'agence pour les modalités.
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Message initial -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="ri-message-3-line"></i> Votre message initial
                    </h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $demande->message }}</p>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Informations du bien -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="ri-home-4-line"></i> Le bien
                    </h5>
                </div>
                <div class="card-body">
                    @if($demande->annonce->hasMedia('image_principale'))
                        <img src="{{ $demande->annonce->getFirstMediaUrl('image_principale') }}" 
                             class="img-fluid rounded mb-3" 
                             alt="{{ $demande->annonce->titre }}">
                    @endif
                    <h5>{{ $demande->annonce->titre }}</h5>
                    <p class="text-muted mb-2">
                        <i class="ri-file-line"></i> Réf: {{ $demande->annonce->reference }}
                    </p>
                    <p class="text-muted mb-2">
                        <i class="ri-map-pin-line"></i> 
                        {{ $demande->annonce->quartier }}, {{ $demande->annonce->ville }}
                    </p>
                    <p class="fs-4 fw-bold text-primary mb-3">
                        {{ number_format($demande->annonce->prix, 0, ',', ' ') }} FCFA
                        @if($demande->annonce->type_transaction == 'location')
                            <small class="text-muted">/mois</small>
                        @endif
                    </p>
                    <a href="{{ route('properties.show', $demande->annonce->slug) }}" 
                       class="btn btn-outline-primary w-100" 
                       target="_blank">
                        <i class="ri-external-link-line"></i> Voir l'annonce complète
                    </a>
                </div>
            </div>

            <!-- Actions -->
            @if($demande->is_en_cours && $demande->statut == 'nouvelle')
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="ri-tools-line"></i> Actions
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('client.demandes.cancel', $demande->id) }}" 
                              method="POST" 
                              onsubmit="return confirm('Voulez-vous vraiment annuler cette demande ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="ri-close-line"></i> Annuler la demande
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            <!-- Contact -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="ri-customer-service-2-line"></i> Besoin d'aide ?
                    </h5>
                </div>
                <div class="card-body">
                    <p class="mb-3">Notre équipe est à votre disposition pour toute question.</p>
                    @if($data_parametre && $data_parametre->email)
                        <a href="mailto:{{ $data_parametre->email }}" class="btn btn-sm btn-outline-primary w-100 mb-2">
                            <i class="ri-mail-line"></i> Envoyer un email
                        </a>
                    @endif
                    @if($data_parametre && $data_parametre->telephone)
                        <a href="tel:{{ $data_parametre->telephone }}" class="btn btn-sm btn-outline-success w-100">
                            <i class="ri-phone-line"></i> Appeler
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .timeline-workflow {
        position: relative;
        padding: 20px 0;
    }
    
    .timeline-workflow::before {
        content: '';
        position: absolute;
        left: 30px;
        top: 0;
        bottom: 0;
        width: 3px;
        background: #e9ecef;
    }
    
    .timeline-item {
        position: relative;
        padding-left: 70px;
        padding-bottom: 30px;
    }
    
    .timeline-marker {
        position: absolute;
        left: 0;
        top: 0;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: #e9ecef;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: #6c757d;
        border: 3px solid #fff;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .timeline-item.active .timeline-marker {
        background: #0ab39c;
        color: white;
        animation: pulse 2s infinite;
    }
    
    .timeline-item.completed .timeline-marker {
        background: #0ab39c;
        color: white;
    }
    
    .timeline-content h6 {
        margin-bottom: 5px;
        font-weight: 600;
    }
    
    .timeline-content p {
        margin-bottom: 0;
        color: #6c757d;
    }
    
    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(10, 179, 156, 0.7);
        }
        70% {
            box-shadow: 0 0 0 10px rgba(10, 179, 156, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(10, 179, 156, 0);
        }
    }
</style>
@endsection
