@extends('frontend.pages.client.layout')

@section('title', 'Espace Acheteur')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="mb-0">
                <i class="ri-shopping-bag-3-line text-primary"></i> Mon espace Acheteur
            </h4>
            <p class="text-muted small mb-0 mt-1">Gérez vos achats immobiliers</p>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card bg-primary-subtle border-0 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-lg rounded-circle bg-primary me-3">
                            <span class="avatar-title rounded-circle text-white">
                                <i class="ri-home-4-line fs-3"></i>
                            </span>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1 text-uppercase small">Biens achetés</h6>
                            <h3 class="text-primary mb-0">{{ $nombreBiensAchetes }}</h3>
                            <p class="text-muted small mb-0">
                                @if($nombreBiensAchetes > 1)
                                    Propriétés
                                @else
                                    Propriété
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success-subtle border-0 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-lg rounded-circle bg-success me-3">
                            <span class="avatar-title rounded-circle text-white">
                                <i class="ri-money-dollar-circle-line fs-3"></i>
                            </span>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1 text-uppercase small">Total dépensé</h6>
                            <h3 class="text-success mb-0">{{ number_format($totalDepense, 0, ',', ' ') }}</h3>
                            <p class="text-muted small mb-0">FCFA</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-{{ $montantRestantTotal > 0 ? 'warning' : 'info' }}-subtle border-0 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-lg rounded-circle bg-{{ $montantRestantTotal > 0 ? 'warning' : 'info' }} me-3">
                            <span class="avatar-title rounded-circle text-white">
                                <i class="ri-wallet-3-line fs-3"></i>
                            </span>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1 text-uppercase small">Montant restant</h6>
                            <h3 class="text-{{ $montantRestantTotal > 0 ? 'warning' : 'info' }} mb-0">{{ number_format($montantRestantTotal, 0, ',', ' ') }}</h3>
                            <p class="text-muted small mb-0">
                                @if($montantRestantTotal > 0)
                                    À payer
                                @else
                                    Tous les paiements effectués
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alerte si montant restant -->
    @if($montantRestantTotal > 0)
        <div class="alert alert-warning d-flex align-items-center mb-4">
            <i class="ri-alert-line fs-3 me-3"></i>
            <div>
                <strong>Attention !</strong> 
                <span class="ms-2">Il vous reste {{ number_format($montantRestantTotal, 0, ',', ' ') }} FCFA à payer sur vos achats.</span>
            </div>
        </div>
    @else
        <div class="alert alert-success d-flex align-items-center mb-4">
            <i class="ri-checkbox-circle-line fs-3 me-3"></i>
            <div>
                <strong>Félicitations !</strong> 
                <span class="ms-2">Tous vos achats sont entièrement payés.</span>
            </div>
        </div>
    @endif

    <!-- Tableau des biens achetés -->
    <div class="card">
        <div class="card-header bg-light">
            <h6 class="mb-0">
                <i class="ri-list-check me-2"></i>Liste de mes achats ({{ $ventes->count() }})
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="min-width: 60px;" class="text-center">#</th>
                            <th style="min-width: 250px;">Bien immobilier</th>
                            <th class="text-center d-none d-lg-table-cell" style="min-width: 120px;">Prix de vente</th>
                            <th class="text-center d-none d-md-table-cell" style="min-width: 120px;">Montant payé</th>
                            <th class="text-center d-none d-xl-table-cell" style="min-width: 120px;">Reste à payer</th>
                            <th class="text-center d-none d-md-table-cell" style="min-width: 130px;">Statut</th>
                            <th class="text-center" style="min-width: 200px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ventes as $index => $vente)
                            <tr>
                                <td class="text-center">
                                    <strong class="text-primary">#{{ $index + 1 }}</strong>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            @if($vente->annonce && $vente->annonce->hasMedia('images'))
                                                <img src="{{ $vente->annonce->getFirstMediaUrl('images') }}" 
                                                     class="rounded" 
                                                     alt="{{ $vente->annonce->titre }}"
                                                     style="width: 70px; height: 70px; object-fit: cover;">
                                            @else
                                                <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                                     style="width: 70px; height: 70px;">
                                                    <i class="ri-home-4-line fs-3 text-muted"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <h6 class="mb-1">{{ $vente->annonce->titre ?? 'Bien' }}</h6>
                                            <small class="text-muted d-block">
                                                <i class="ri-map-pin-line me-1"></i>{{ $vente->annonce->ville ?? 'N/A' }}
                                            </small>
                                            <small class="text-muted d-block">
                                                <i class="ri-calendar-line me-1"></i>Acheté le {{ \Carbon\Carbon::parse($vente->created_at)->format('d/m/Y') }}
                                            </small>
                                            <!-- Info mobile -->
                                            <small class="text-success d-md-none d-block mt-1">
                                                <strong>{{ number_format($vente->montantTotalPaye(), 0, ',', ' ') }} FCFA</strong> payé
                                            </small>
                                            @if($vente->resteAPayer() > 0)
                                                <small class="text-warning d-md-none d-block">
                                                    <strong>{{ number_format($vente->resteAPayer(), 0, ',', ' ') }} FCFA</strong> restant
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center d-none d-lg-table-cell">
                                    <strong class="text-primary">{{ number_format($vente->prix_vente, 0, ',', ' ') }}</strong>
                                    <small class="text-muted d-block">FCFA</small>
                                </td>
                                <td class="text-center d-none d-md-table-cell">
                                    <strong class="text-success">{{ number_format($vente->montantTotalPaye(), 0, ',', ' ') }}</strong>
                                    <small class="text-muted d-block">
                                        {{ round($vente->pourcentagePaiement(), 1) }}%
                                    </small>
                                </td>
                                <td class="text-center d-none d-xl-table-cell">
                                    @if($vente->resteAPayer() > 0)
                                        <strong class="text-warning">{{ number_format($vente->resteAPayer(), 0, ',', ' ') }}</strong>
                                        <small class="text-muted d-block">FCFA</small>
                                    @else
                                        <span class="badge bg-success">
                                            <i class="ri-checkbox-circle-line"></i> Complet
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center d-none d-md-table-cell">
                                    {!! $vente->statut_badge !!}
                                    <div class="progress mt-2" style="height: 5px;">
                                        <div class="progress-bar bg-success" 
                                             role="progressbar" 
                                             style="width: {{ $vente->pourcentagePaiement() }}%;">
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('client.acheteur.workflow', $vente->id) }}" 
                                           class="btn btn-outline-primary" 
                                           title="Voir le workflow"
                                           data-bs-toggle="tooltip">
                                            <i class="ri-flow-chart me-1"></i>
                                            <span class="d-none d-lg-inline">Workflow</span>
                                        </a>
                                        <a href="{{ route('client.acheteur.situation-financiere', $vente->id) }}" 
                                           class="btn btn-outline-success" 
                                           title="Voir l'état financier"
                                           data-bs-toggle="tooltip">
                                            <i class="ri-money-dollar-circle-line me-1"></i>
                                            <span class="d-none d-lg-inline">Financier</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Note informative -->
    <div class="alert alert-light border mt-4">
        <div class="d-flex">
            <div class="flex-shrink-0">
                <i class="ri-information-line text-primary fs-4"></i>
            </div>
            <div class="flex-grow-1 ms-3">
                <h6 class="alert-heading">Besoin d'aide ?</h6>
                <p class="mb-0">Pour tout renseignement sur vos achats ou vos paiements, contactez votre gestionnaire de compte.</p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .container-fluid {
        max-width: 100%;
        padding-left: 1rem;
        padding-right: 1rem;
    }
    
    .avatar-lg {
        height: 60px;
        width: 60px;
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

    .table > :not(caption) > * > * {
        padding: 1rem 0.75rem;
    }

    .btn-group-sm > .btn {
        padding: 0.4rem 0.8rem;
        font-size: 0.875rem;
    }
    
    @media (max-width: 768px) {
        .container-fluid {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }
        
        .card-body {
            padding: 1rem !important;
        }
        
        h3 {
            font-size: 1.5rem;
        }
        
        h4 {
            font-size: 1.1rem;
        }
        
        h6 {
            font-size: 0.9rem;
        }
        
        .avatar-lg {
            height: 50px;
            width: 50px;
        }

        .table > :not(caption) > * > * {
            padding: 0.75rem 0.5rem;
        }

        .btn-group-sm > .btn {
            padding: 0.35rem 0.6rem;
            font-size: 0.8rem;
        }
    }
</style>
@endsection

@section('scripts')
<script>
    // Initialiser les tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endsection
