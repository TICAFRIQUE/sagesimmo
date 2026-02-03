@extends('backend.layouts.master')

@section('title', 'Rapport des Commissions')

@section('content')
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Rapports</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i class="bx bx-home-alt"></i></a></li>
                <li class="breadcrumb-item active" aria-current="page">Commissions</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="ri-percent-line me-2"></i>Rapport des Commissions</h5>
                    <button class="btn btn-sm btn-primary" onclick="window.print()">
                        <i class="ri-printer-line me-1"></i>Imprimer
                    </button>
                </div>
            </div>
            <div class="card-body">
                <!-- Filtres -->
                <form method="GET" class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label class="form-label">Date début</label>
                        <input type="date" name="date_debut" class="form-control" value="{{ $dateDebut }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Date fin</label>
                        <input type="date" name="date_fin" class="form-control" value="{{ $dateFin }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Type</label>
                        <select name="type_transaction" class="form-select">
                            <option value="tous" {{ $typeTransaction == 'tous' ? 'selected' : '' }}>Tous</option>
                            <option value="location" {{ $typeTransaction == 'location' ? 'selected' : '' }}>Locations</option>
                            <option value="vente" {{ $typeTransaction == 'vente' ? 'selected' : '' }}>Ventes</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Locataire</label>
                        <select name="locataire" class="form-select">
                            <option value="">Tous</option>
                            @foreach($locataires as $loc)
                                <option value="{{ $loc->id }}" {{ $locataire == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-filter-line me-1"></i>Filtrer
                        </button>
                        <a href="{{ route('backend.rapports.commissions') }}" class="btn btn-secondary">
                            <i class="ri-refresh-line me-1"></i>Réinitialiser
                        </a>
                    </div>
                </form>

                <!-- Statistiques globales -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card border-primary">
                            <div class="card-body text-center">
                                <h6 class="text-muted mb-2">Total Commissions</h6>
                                <h4 class="text-primary mb-0">{{ number_format($totalCommissions, 0, ',', ' ') }} FCFA</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-success">
                            <div class="card-body text-center">
                                <h6 class="text-muted mb-2">Total Transactions</h6>
                                <h4 class="text-success mb-0">{{ number_format($totalTransactions, 0, ',', ' ') }} FCFA</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-info">
                            <div class="card-body text-center">
                                <h6 class="text-muted mb-2">Nombre de Transactions</h6>
                                <h4 class="text-info mb-0">{{ $nombreTransactions }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-warning">
                            <div class="card-body text-center">
                                <h6 class="text-muted mb-2">Commission Moyenne</h6>
                                <h4 class="text-warning mb-0">{{ number_format($commissionMoyenne, 0, ',', ' ') }} FCFA</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Répartition par type -->
                @if($parType->count() > 0)
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="mb-3"><i class="ri-pie-chart-line me-1"></i>Répartition par Type</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Type</th>
                                                <th class="text-end">Nombre</th>
                                                <th class="text-end">Total Commission</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($parType as $type => $data)
                                            <tr>
                                                <td><span class="badge bg-{{ $type == 'Location' ? 'primary' : 'success' }}">{{ $type }}</span></td>
                                                <td class="text-end">{{ $data['count'] }}</td>
                                                <td class="text-end"><strong>{{ number_format($data['total'], 0, ',', ' ') }} FCFA</strong></td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Graphique évolution mensuelle -->
                @if($parMois->count() > 1)
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="mb-3"><i class="ri-line-chart-line me-1"></i>Évolution Mensuelle</h6>
                                <canvas id="chartEvolution" height="100"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Tableau détaillé -->
                <h6 class="mb-3"><i class="ri-list-check me-1"></i>Détail des Commissions ({{ $commissions->count() }} transactions)</h6>
                @if($commissions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Réf. Bien</th>
                                <th>Bien</th>
                                <th>Client</th>
                                <th class="text-end">Montant Transaction</th>
                                <th>Taux</th>
                                <th class="text-end">Commission</th>
                                <th>Méthode</th>
                                <th>Référence</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($commissions as $commission)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($commission['date'])->format('d/m/Y') }}</td>
                                <td><span class="badge bg-{{ $commission['type'] == 'Location' ? 'primary' : 'success' }}">{{ $commission['type'] }}</span></td>
                                <td>{{ $commission['reference'] }}</td>
                                <td>{{ Str::limit($commission['bien'], 30) }}</td>
                                <td>{{ $commission['client'] }}</td>
                                <td class="text-end">{{ number_format($commission['montant_transaction'], 0, ',', ' ') }} FCFA</td>
                                <td><span class="badge bg-info">{{ $commission['commission_config'] }}</span></td>
                                <td class="text-end"><strong class="text-success">{{ number_format($commission['commission_montant'], 0, ',', ' ') }} FCFA</strong></td>
                                <td><span class="badge bg-secondary">{{ ucfirst($commission['methode_paiement']) }}</span></td>
                                <td>{{ $commission['reference_paiement'] ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="5" class="text-end">TOTAL :</th>
                                <th class="text-end">{{ number_format($totalTransactions, 0, ',', ' ') }} FCFA</th>
                                <th></th>
                                <th class="text-end"><strong class="text-primary">{{ number_format($totalCommissions, 0, ',', ' ') }} FCFA</strong></th>
                                <th colspan="2"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @else
                <div class="alert alert-info">
                    <i class="ri-information-line me-1"></i>Aucune commission trouvée pour la période sélectionnée.
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
@if($parMois->count() > 1)
// Graphique d'évolution mensuelle
const ctx = document.getElementById('chartEvolution');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: {!! json_encode($parMois->keys()) !!},
        datasets: [{
            label: 'Commissions (FCFA)',
            data: {!! json_encode($parMois->values()) !!},
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: true,
                position: 'top',
            },
            title: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return value.toLocaleString('fr-FR') + ' FCFA';
                    }
                }
            }
        }
    }
});
@endif
</script>
@endsection
