@extends('frontend.pages.client.layout')

@section('title', 'Situation financière')

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
                        <i class="ri-money-dollar-circle-line text-success"></i> Situation financière
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
                    <h5 class="mb-3">{{ $vente->annonce->titre ?? 'Bien' }}</h5>
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <p class="text-muted small mb-1">Référence</p>
                            <p class="mb-0"><strong>{{ $vente->annonce->reference ?? 'N/A' }}</strong></p>
                        </div>
                        <div class="col-md-4 mb-2">
                            <p class="text-muted small mb-1">Type de bien</p>
                            <p class="mb-0">{{ $vente->annonce->typeBien->nom ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-4 mb-2">
                            <p class="text-muted small mb-1">Localisation</p>
                            <p class="mb-0">{{ $vente->annonce->ville ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Résumé financier -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card bg-primary-subtle border-0 h-100">
                <div class="card-body">
                    <h6 class="text-muted mb-2 text-uppercase small">
                        <i class="ri-money-dollar-circle-line me-1"></i> Prix total
                    </h6>
                    <h3 class="text-primary mb-0">{{ number_format($vente->prix_vente, 0, ',', ' ') }}</h3>
                    <p class="text-muted small mb-0 mt-1">FCFA</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success-subtle border-0 h-100">
                <div class="card-body">
                    <h6 class="text-muted mb-2 text-uppercase small">
                        <i class="ri-checkbox-circle-line me-1"></i> Montant payé
                    </h6>
                    <h3 class="text-success mb-0">{{ number_format($vente->montantTotalPaye(), 0, ',', ' ') }}</h3>
                    <p class="text-muted small mb-0 mt-1">FCFA ({{ round($vente->pourcentagePaiement(), 1) }}%)</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-{{ $vente->resteAPayer() > 0 ? 'warning' : 'success' }}-subtle border-0 h-100">
                <div class="card-body">
                    <h6 class="text-muted mb-2 text-uppercase small">
                        <i class="ri-wallet-3-line me-1"></i> Reste à payer
                    </h6>
                    <h3 class="text-{{ $vente->resteAPayer() > 0 ? 'warning' : 'success' }} mb-0">{{ number_format($vente->resteAPayer(), 0, ',', ' ') }}</h3>
                    <p class="text-muted small mb-0 mt-1">FCFA</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info-subtle border-0 h-100">
                <div class="card-body">
                    <h6 class="text-muted mb-2 text-uppercase small">
                        <i class="ri-file-list-3-line me-1"></i> Nombre de paiements
                    </h6>
                    <h3 class="text-info mb-0">{{ $vente->paiements->where('statut', 'paye')->count() }}</h3>
                    <p class="text-muted small mb-0 mt-1">Paiement(s) effectué(s)</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Progression du paiement -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h6 class="mb-0">
                <i class="ri-bar-chart-line me-2"></i>Progression du paiement
            </h6>
        </div>
        <div class="card-body">
            <div class="progress mb-3" style="height: 35px;">
                <div class="progress-bar bg-success" 
                     role="progressbar" 
                     style="width: {{ $vente->pourcentagePaiement() }}%;" 
                     aria-valuenow="{{ $vente->pourcentagePaiement() }}" 
                     aria-valuemin="0" 
                     aria-valuemax="100">
                    <strong class="fs-6">{{ round($vente->pourcentagePaiement(), 1) }}%</strong>
                </div>
            </div>
            <div class="d-flex justify-content-between">
                <div>
                    <small class="text-muted d-block">Départ</small>
                    <strong class="text-muted">0 FCFA</strong>
                </div>
                <div class="text-center">
                    <small class="text-success d-block">Payé</small>
                    <strong class="text-success">{{ number_format($vente->montantTotalPaye(), 0, ',', ' ') }} FCFA</strong>
                </div>
                <div class="text-end">
                    <small class="text-muted d-block">Total</small>
                    <strong class="text-muted">{{ number_format($vente->prix_vente, 0, ',', ' ') }} FCFA</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Statut du paiement -->
    @if($vente->estEntierementPaye())
        <div class="alert alert-success d-flex align-items-center mb-4">
            <i class="ri-checkbox-circle-line fs-3 me-3"></i>
            <div>
                <strong>Paiement complet !</strong>
                <p class="mb-0 small">Vous avez finalisé le paiement de votre bien. Vous pouvez procéder à la récupération des clés.</p>
            </div>
        </div>
    @elseif($vente->montantTotalPaye() > 0)
        <div class="alert alert-info d-flex align-items-center mb-4">
            <i class="ri-information-line fs-3 me-3"></i>
            <div>
                <strong>Paiement en cours</strong>
                <p class="mb-0 small">Il reste {{ number_format($vente->resteAPayer(), 0, ',', ' ') }} FCFA à payer pour finaliser votre achat.</p>
            </div>
        </div>
    @else
        <div class="alert alert-warning d-flex align-items-center mb-4">
            <i class="ri-alert-line fs-3 me-3"></i>
            <div>
                <strong>Aucun paiement enregistré</strong>
                <p class="mb-0 small">Aucun paiement n'a encore été effectué pour ce bien. Veuillez contacter l'agence pour démarrer le processus de paiement.</p>
            </div>
        </div>
    @endif

    <!-- Détails des coûts -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h6 class="mb-0">
                <i class="ri-file-text-line me-2"></i>Détails des coûts
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-borderless mb-0">
                    <tbody>
                        <tr>
                            <td class="text-muted">Prix de vente du bien</td>
                            <td class="text-end"><strong>{{ number_format($vente->prix_vente, 0, ',', ' ') }} FCFA</strong></td>
                        </tr>
                        <tr class="border-top">
                            <td class="text-muted"><strong>Montant total</strong></td>
                            <td class="text-end"><strong class="text-primary fs-5">{{ number_format($vente->prix_vente, 0, ',', ' ') }} FCFA</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Historique des paiements -->
    @if($vente->paiements->where('statut', 'paye')->count() > 0)
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0">
                    <i class="ri-history-line me-2"></i>Historique des paiements
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="min-width: 50px;">#</th>
                                <th style="min-width: 120px;">Date</th>
                                <th style="min-width: 100px;">Type</th>
                                <th class="text-end" style="min-width: 120px;">Montant</th>
                                <th style="min-width: 120px;">Méthode</th>
                                <th class="text-center" style="min-width: 100px;">Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($vente->paiements->where('statut', 'paye')->sortByDesc('date_paiement') as $index => $paiement)
                                <tr>
                                    <td><strong>{{ $index + 1 }}</strong></td>
                                    <td>
                                        <strong>{{ \Carbon\Carbon::parse($paiement->date_paiement)->format('d/m/Y') }}</strong>
                                        <br>
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($paiement->date_paiement)->format('H:i') }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ ucfirst($paiement->type ?? 'Paiement') }}</span>
                                    </td>
                                    <td class="text-end">
                                        <strong class="text-success">{{ number_format($paiement->montant, 0, ',', ' ') }} FCFA</strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            {{ ucfirst(str_replace('_', ' ', $paiement->methode_paiement ?? 'N/A')) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success">
                                            <i class="ri-checkbox-circle-line"></i> Payé
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="3" class="text-end"><strong>Total payé :</strong></td>
                                <td class="text-end">
                                    <strong class="text-success fs-5">{{ number_format($vente->montantTotalPaye(), 0, ',', ' ') }} FCFA</strong>
                                </td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- Informations complémentaires -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h6 class="mb-0">
                <i class="ri-information-line me-2"></i>Informations complémentaires
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm rounded-circle bg-primary-subtle me-3">
                            <span class="avatar-title rounded-circle text-primary">
                                <i class="ri-calendar-line"></i>
                            </span>
                        </div>
                        <div>
                            <p class="text-muted small mb-0">Date de demande</p>
                            <h6 class="mb-0">{{ \Carbon\Carbon::parse($vente->created_at)->format('d/m/Y') }}</h6>
                        </div>
                    </div>
                </div>
                @if($vente->date_vente)
                <div class="col-md-6 mb-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm rounded-circle bg-success-subtle me-3">
                            <span class="avatar-title rounded-circle text-success">
                                <i class="ri-calendar-check-line"></i>
                            </span>
                        </div>
                        <div>
                            <p class="text-muted small mb-0">Date de vente</p>
                            <h6 class="mb-0">{{ \Carbon\Carbon::parse($vente->date_vente)->format('d/m/Y') }}</h6>
                        </div>
                    </div>
                </div>
                @endif
                <div class="col-md-6 mb-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm rounded-circle bg-info-subtle me-3">
                            <span class="avatar-title rounded-circle text-info">
                                <i class="ri-file-list-3-line"></i>
                            </span>
                        </div>
                        <div>
                            <p class="text-muted small mb-0">Statut de la vente</p>
                            <h6 class="mb-0">{!! $vente->statut_badge !!}</h6>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm rounded-circle bg-warning-subtle me-3">
                            <span class="avatar-title rounded-circle text-warning">
                                <i class="ri-pie-chart-line"></i>
                            </span>
                        </div>
                        <div>
                            <p class="text-muted small mb-0">Progression</p>
                            <h6 class="mb-0">{{ round($vente->pourcentagePaiement(), 1) }}% payé</h6>
                        </div>
                    </div>
                </div>
            </div>
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
                <p class="mb-0">Pour toute question concernant votre situation financière ou pour effectuer un paiement, contactez votre gestionnaire de compte.</p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .avatar-sm {
        height: 48px;
        width: 48px;
    }

    .avatar-title {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100%;
        width: 100%;
    }

    .card {
        border: none;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        border-radius: 8px;
    }

    @media (max-width: 768px) {
        .avatar-sm {
            height: 40px;
            width: 40px;
        }
        
        h3 {
            font-size: 1.5rem;
        }
        
        .progress {
            height: 25px !important;
        }
    }
</style>
@endsection
