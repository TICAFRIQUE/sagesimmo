@extends('backend.layouts.master')

@section('title')
   Rapport Propriétaire
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-file-invoice-dollar"></i> Rapport Propriétaire
            </h1>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('rapports.proprietaire') }}" class="btn btn-secondary btn-sm">
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
            <form method="GET" action="{{ route('rapports.proprietaire') }}" class="row g-3">
                @if(Auth::user()->role === 'admin')
                <div class="col-md-4">
                    <label for="proprietaire_id" class="form-label">Propriétaire</label>
                    <select name="proprietaire_id" id="proprietaire_id" class="form-select">
                        <option value="">-- Sélectionner --</option>
                        @foreach($proprietaires as $prop)
                            <option value="{{ $prop->id }}" @selected($prop->id == $proprietaire->id)>
                                {{ $prop->username }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif
                
                <div class="col-md-4">
                    <label for="date_debut" class="form-label">Date début</label>
                    <input type="date" name="date_debut" id="date_debut" class="form-control"
                        value="{{ $dateDebut->format('Y-m-d') }}">
                </div>
                
                <div class="col-md-4">
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

    <!-- Résumé général -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-primary">
                <div class="card-body">
                    <div class="text-primary font-weight-bold text-uppercase mb-1">Total Encaissé</div>
                    <div class="h3 mb-0">
                        {{ number_format($rapport['total_brut_encaisse'], 0, ',', ' ') }} F
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-left-danger">
                <div class="card-body">
                    <div class="text-danger font-weight-bold text-uppercase mb-1">Commission Agence</div>
                    <div class="h3 mb-0">
                        {{ number_format($rapport['total_commission_agence'], 0, ',', ' ') }} F
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-left-warning">
                <div class="card-body">
                    <div class="text-warning font-weight-bold text-uppercase mb-1">Charges</div>
                    <div class="h3 mb-0">
                        {{ number_format($rapport['total_charges'], 0, ',', ' ') }} F
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-left-success">
                <div class="card-body">
                    <div class="text-success font-weight-bold text-uppercase mb-1">Revenu Net</div>
                    <div class="h3 mb-0">
                        {{ number_format($rapport['revenue_net'], 0, ',', ' ') }} F
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Détail par bien -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-home"></i> Détail par Bien ({{ $rapport['nombre_biens'] }} biens)
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr class="table-light">
                            <th>Bien</th>
                            <th>Adresse</th>
                            <th class="text-end">Loyers Encaissés</th>
                            <th class="text-end">Ventes Encaissées</th>
                            <th class="text-end">Total Brut</th>
                            <th class="text-end">Commission</th>
                            <th class="text-end">Charges</th>
                            <th class="text-end">Revenu Net</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rapport['biens'] as $bien)
                        <tr>
                            <td>
                                <strong>{{ $bien['bien']->titre ?? 'N/A' }}</strong><br>
                                <small class="text-muted">{{ $bien['type_bien'] }}</small>
                            </td>
                            <td>{{ $bien['adresse'] }}</td>
                            <td class="text-end">
                                {{ number_format($bien['encaissement_loyers']['total'], 0, ',', ' ') }} F
                            </td>
                            <td class="text-end">
                                {{ number_format($bien['encaissement_ventes']['total'], 0, ',', ' ') }} F
                            </td>
                            <td class="text-end font-weight-bold">
                                {{ number_format($bien['total_brut_encaisse'], 0, ',', ' ') }} F
                            </td>
                            <td class="text-end text-danger">
                                {{ number_format($bien['total_commission_agence'], 0, ',', ' ') }} F
                            </td>
                            <td class="text-end text-warning">
                                {{ number_format($bien['total_charges'], 0, ',', ' ') }} F
                            </td>
                            <td class="text-end text-success font-weight-bold">
                                {{ number_format($bien['revenue_net'], 0, ',', ' ') }} F
                            </td>
                        </tr>
                        @if($bien['charges']->isNotEmpty())
                        <tr class="table-light">
                            <td colspan="8">
                                <small class="text-muted">
                                    <strong>Charges détail :</strong>
                                    @foreach($bien['charges'] as $charge)
                                        <br>
                                        • {{ $charge->type_charge_libelle }}: 
                                        {{ number_format($charge->montant, 0, ',', ' ') }} F
                                        @if($charge->description)
                                            ({{ $charge->description }})
                                        @endif
                                    @endforeach
                                </small>
                            </td>
                        </tr>
                        @endif
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                Aucun bien trouvé pour cette période
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Résumé des charges -->
    @if($rapport['detail_charges']['nombre_charges'] > 0)
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-tools"></i> Résumé des Charges
                ({{ $rapport['detail_charges']['nombre_charges'] }} charges)
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                @foreach($rapport['detail_charges']['par_type'] as $type => $montant)
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="text-muted text-uppercase mb-1">
                                @switch($type)
                                    @case('maintenance')
                                        <i class="fas fa-wrench"></i> Maintenance
                                        @break
                                    @case('reparation')
                                        <i class="fas fa-hammer"></i> Réparation
                                        @break
                                    @case('taxe')
                                        <i class="fas fa-percent"></i> Taxe
                                        @break
                                    @default
                                        <i class="fas fa-ellipsis-h"></i> Autre
                                @endswitch
                            </div>
                            <div class="h4 mb-0">
                                {{ number_format($montant, 0, ',', ' ') }} F
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Section de calcul -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-calculator"></i> Calcul du Revenu Net
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <table class="table table-sm">
                        <tr>
                            <td><strong>Total Encaissé (Loyers + Ventes)</strong></td>
                            <td class="text-end" width="30%">
                                <strong>{{ number_format($rapport['total_brut_encaisse'], 0, ',', ' ') }} F</strong>
                            </td>
                        </tr>
                        <tr class="table-light">
                            <td><strong>- Commission Agence</strong></td>
                            <td class="text-end text-danger">
                                ({{ number_format($rapport['total_commission_agence'], 0, ',', ' ') }} F)
                            </td>
                        </tr>
                        <tr class="table-light">
                            <td><strong>- Total des Charges</strong></td>
                            <td class="text-end text-warning">
                                ({{ number_format($rapport['total_charges'], 0, ',', ' ') }} F)
                            </td>
                        </tr>
                        <tr class="table-success">
                            <td><strong class="h5">= REVENU NET DU PROPRIÉTAIRE</strong></td>
                            <td class="text-end text-success">
                                <strong class="h5">{{ number_format($rapport['revenue_net'], 0, ',', ' ') }} F</strong>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-4">
                    <div class="alert alert-info">
                        <strong>Période :</strong><br>
                        {{ $rapport['periode'] }}
                        <hr>
                        <strong>Propriétaire :</strong><br>
                        {{ $proprietaire->username }}
                        <hr>
                        <strong>Nombre de biens :</strong><br>
                        {{ $rapport['nombre_biens'] }}
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
