@extends('backend.layouts.master')

@section('title')
   Rapport Agence
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-chart-line"></i> Rapport Financier Agence
            </h1>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('rapports.agence') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-refresh"></i> Réinitialiser
            </a>
            <button type="button" class="btn btn-primary btn-sm" onclick="window.print()">
                <i class="fas fa-print"></i> Imprimer
            </button>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('rapports.agence') }}" class="row g-3">
                <div class="col-md-5">
                    <label for="date_debut" class="form-label">Date début</label>
                    <input type="date" name="date_debut" id="date_debut" class="form-control"
                        value="{{ $dateDebut->format('Y-m-d') }}">
                </div>
                
                <div class="col-md-5">
                    <label for="date_fin" class="form-label">Date fin</label>
                    <input type="date" name="date_fin" id="date_fin" class="form-control"
                        value="{{ $dateFin->format('Y-m-d') }}">
                </div>
                
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Filtrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- KPIs Principaux -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-primary">
                <div class="card-body">
                    <div class="text-primary font-weight-bold text-uppercase mb-1">Total Encaissé</div>
                    <div class="h3 mb-0">
                        {{ number_format($rapport['total_encaisse'], 0, ',', ' ') }} F
                    </div>
                    <small class="text-muted">Loyers + Ventes</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-left-success">
                <div class="card-body">
                    <div class="text-success font-weight-bold text-uppercase mb-1">Commissions Perçues</div>
                    <div class="h3 mb-0">
                        {{ number_format($rapport['total_commissions'], 0, ',', ' ') }} F
                    </div>
                    <small class="text-muted">Revenu de l'agence</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-left-info">
                <div class="card-body">
                    <div class="text-info font-weight-bold text-uppercase mb-1">Taux Commission Moyen</div>
                    <div class="h3 mb-0">
                        @if($rapport['total_encaisse'] > 0)
                            {{ number_format(($rapport['total_commissions'] / $rapport['total_encaisse']) * 100, 2, ',', ' ') }}%
                        @else
                            0%
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-left-warning">
                <div class="card-body">
                    <div class="text-warning font-weight-bold text-uppercase mb-1">Transactions</div>
                    <div class="h3 mb-0">
                        {{ $rapport['detail_loyers']['nombre_paiements'] + $rapport['detail_ventes']['nombre_paiements'] }}
                    </div>
                    <small class="text-muted">Nombre total</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Encaissements par type -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-home"></i> Loyers Encaissés
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr class="table-light">
                            <td><strong>Nombre de paiements</strong></td>
                            <td class="text-end">{{ $rapport['detail_loyers']['nombre_paiements'] }}</td>
                        </tr>
                        <tr>
                            <td><strong>Total encaissé</strong></td>
                            <td class="text-end font-weight-bold">
                                {{ number_format($rapport['total_loyers_encaisses'], 0, ',', ' ') }} F
                            </td>
                        </tr>
                        <tr class="table-light">
                            <td><strong>Commissions prélevées</strong></td>
                            <td class="text-end text-success font-weight-bold">
                                {{ number_format($rapport['commissions_loyers'], 0, ',', ' ') }} F
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-handshake"></i> Ventes Encaissées
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr class="table-light">
                            <td><strong>Nombre de ventes</strong></td>
                            <td class="text-end">{{ $rapport['detail_ventes']['nombre_paiements'] }}</td>
                        </tr>
                        <tr>
                            <td><strong>Total encaissé</strong></td>
                            <td class="text-end font-weight-bold">
                                {{ number_format($rapport['total_ventes_encaissees'], 0, ',', ' ') }} F
                            </td>
                        </tr>
                        <tr class="table-light">
                            <td><strong>Commissions prélevées</strong></td>
                            <td class="text-end text-success font-weight-bold">
                                {{ number_format($rapport['commissions_ventes'], 0, ',', ' ') }} F
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Biens -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-trophy"></i> Top 10 Biens par Commission
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr class="table-light">
                            <th>Bien</th>
                            <th>Adresse</th>
                            <th class="text-end">Encaissé</th>
                            <th class="text-end">Commission</th>
                            <th class="text-end">Transactions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rapport['detail_par_bien']->take(10) as $bien)
                        <tr>
                            <td>
                                <strong>{{ $bien['annonce']->titre ?? 'N/A' }}</strong><br>
                                <small class="text-muted">{{ $bien['type_bien'] }}</small>
                            </td>
                            <td>{{ $bien['adresse'] }}</td>
                            <td class="text-end">
                                {{ number_format($bien['total_encaisse'], 0, ',', ' ') }} F
                            </td>
                            <td class="text-end font-weight-bold text-success">
                                {{ number_format($bien['total_commission'], 0, ',', ' ') }} F
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary">{{ $bien['nombre_transactions'] }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                Aucune transaction pour cette période
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Top Propriétaires -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-users"></i> Top Propriétaires par Commission
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr class="table-light">
                            <th>Propriétaire</th>
                            <th class="text-end">Total Encaissé</th>
                            <th class="text-end">Commission Agence</th>
                            <th class="text-end">Transactions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rapport['detail_par_proprietaire']->take(10) as $prop)
                        <tr>
                            <td>
                                <strong>{{ $prop['nom'] }}</strong>
                            </td>
                            <td class="text-end">
                                {{ number_format($prop['total_encaisse'], 0, ',', ' ') }} F
                            </td>
                            <td class="text-end font-weight-bold text-success">
                                {{ number_format($prop['total_commission'], 0, ',', ' ') }} F
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info">{{ $prop['nombre_transactions'] }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                Aucun propriétaire pour cette période
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Résumé financier final -->
    <div class="card">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">
                <i class="fas fa-chart-pie"></i> Résumé Financier Global
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <table class="table table-sm">
                        <tr>
                            <td><strong>Total Loyers Encaissés</strong></td>
                            <td class="text-end">
                                {{ number_format($rapport['total_loyers_encaisses'], 0, ',', ' ') }} F
                            </td>
                        </tr>
                        <tr class="table-light">
                            <td><strong>Total Ventes Encaissées</strong></td>
                            <td class="text-end">
                                {{ number_format($rapport['total_ventes_encaissees'], 0, ',', ' ') }} F
                            </td>
                        </tr>
                        <tr class="table-info">
                            <td><strong>Total Encaissé (Loyers + Ventes)</strong></td>
                            <td class="text-end font-weight-bold">
                                {{ number_format($rapport['total_encaisse'], 0, ',', ' ') }} F
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Commission Loyers</strong></td>
                            <td class="text-end">
                                {{ number_format($rapport['commissions_loyers'], 0, ',', ' ') }} F
                            </td>
                        </tr>
                        <tr class="table-light">
                            <td><strong>Commission Ventes</strong></td>
                            <td class="text-end">
                                {{ number_format($rapport['commissions_ventes'], 0, ',', ' ') }} F
                            </td>
                        </tr>
                        <tr class="table-success">
                            <td><strong class="h5">TOTAL COMMISSIONS (REVENU AGENCE)</strong></td>
                            <td class="text-end text-success">
                                <strong class="h5">{{ number_format($rapport['total_commissions'], 0, ',', ' ') }} F</strong>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-4">
                    <div class="alert alert-success">
                        <strong>
                            <i class="fas fa-check-circle"></i> 
                            REVENU TOTAL DE L'AGENCE
                        </strong>
                        <div class="h4 mt-2 text-success">
                            {{ number_format($rapport['total_commissions'], 0, ',', ' ') }} F
                        </div>
                        <hr>
                        <small><strong>Période :</strong> {{ $rapport['periode'] }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        .btn, form, .card-header .d-inline {
            display: none !important;
        }
        .card {
            page-break-inside: avoid;
        }
    }
</style>
@endsection
