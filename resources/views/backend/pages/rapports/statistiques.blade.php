@extends('backend.layouts.master')

@section('title', 'Statistiques Générales')

@section('content')
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Rapports</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i class="bx bx-home-alt"></i></a></li>
                <li class="breadcrumb-item active" aria-current="page">Statistiques</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="ri-bar-chart-line me-2"></i>Statistiques Générales</h5>
                    <div>
                        <a href="{{ route('backend.rapports.commissions') }}" class="btn btn-sm btn-outline-primary me-2">
                            <i class="ri-percent-line me-1"></i>Voir les Commissions
                        </a>
                        <button class="btn btn-sm btn-primary" onclick="window.print()">
                            <i class="ri-printer-line me-1"></i>Imprimer
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Filtres -->
                <form method="GET" class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label">Date début</label>
                        <input type="date" name="date_debut" class="form-control" value="{{ $dateDebut }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Date fin</label>
                        <input type="date" name="date_fin" class="form-control" value="{{ $dateFin }}">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="ri-filter-line me-1"></i>Filtrer
                        </button>
                        <a href="{{ route('backend.rapports.statistiques') }}" class="btn btn-secondary">
                            <i class="ri-refresh-line me-1"></i>Réinitialiser
                        </a>
                    </div>
                </form>

                <hr>

                <!-- Vue d'ensemble -->
                <h6 class="mb-3"><i class="ri-dashboard-line me-1"></i>Vue d'Ensemble</h6>
                <div class="row mb-4">
                    <!-- Locations -->
                    <div class="col-md-6">
                        <div class="card border-primary">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0"><i class="ri-home-4-line me-1"></i>Locations</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="mb-3">
                                            <small class="text-muted">Locations actives</small>
                                            <h4 class="mb-0">{{ $locationsActives }}</h4>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="mb-3">
                                            <small class="text-muted">Total loyers perçus</small>
                                            <h5 class="mb-0 text-success">{{ number_format($totalLoyers, 0, ',', ' ') }} FCFA</h5>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <hr>
                                        <div>
                                            <small class="text-muted">Commissions générées</small>
                                            <h4 class="mb-0 text-primary">{{ number_format($commissionsLoyers, 0, ',', ' ') }} FCFA</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Ventes -->
                    <div class="col-md-6">
                        <div class="card border-success">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0"><i class="ri-building-line me-1"></i>Ventes</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="mb-3">
                                            <small class="text-muted">Ventes complètes</small>
                                            <h4 class="mb-0">{{ $ventesCompletes }}</h4>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="mb-3">
                                            <small class="text-muted">Total ventes</small>
                                            <h5 class="mb-0 text-success">{{ number_format($totalVentes, 0, ',', ' ') }} FCFA</h5>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <hr>
                                        <div>
                                            <small class="text-muted">Commissions générées</small>
                                            <h4 class="mb-0 text-success">{{ number_format($commissionsVentes, 0, ',', ' ') }} FCFA</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total des commissions -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card bg-gradient-primary text-white">
                            <div class="card-body text-center">
                                <h5 class="mb-2">TOTAL DES COMMISSIONS (Période sélectionnée)</h5>
                                <h2 class="mb-0">{{ number_format($commissionsLoyers + $commissionsVentes, 0, ',', ' ') }} FCFA</h2>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Évolution mensuelle -->
                @if($evolutionMensuelle->count() > 0)
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="mb-3"><i class="ri-line-chart-line me-1"></i>Évolution Mensuelle des Paiements</h6>
                                <canvas id="chartEvolution" height="80"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Top 5 biens -->
                @if($topBiens->count() > 0)
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="mb-3"><i class="ri-medal-line me-1"></i>Top 5 Biens les Plus Rentables</h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Référence</th>
                                                <th>Bien</th>
                                                <th class="text-end">Total Transactions</th>
                                                <th class="text-center">Nb Transactions</th>
                                                <th class="text-end">Commission Générée</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($topBiens as $index => $bien)
                                            <tr>
                                                <td>
                                                    @if($loop->iteration == 1)
                                                        <i class="ri-trophy-fill text-warning"></i>
                                                    @elseif($loop->iteration == 2)
                                                        <i class="ri-medal-fill text-secondary"></i>
                                                    @elseif($loop->iteration == 3)
                                                        <i class="ri-medal-2-fill" style="color: #cd7f32;"></i>
                                                    @else
                                                        {{ $loop->iteration }}
                                                    @endif
                                                </td>
                                                <td>{{ $bien['reference'] }}</td>
                                                <td>{{ Str::limit($bien['bien'], 40) }}</td>
                                                <td class="text-end">{{ number_format($bien['total'], 0, ',', ' ') }} FCFA</td>
                                                <td class="text-center"><span class="badge bg-info">{{ $bien['nombre_transactions'] }}</span></td>
                                                <td class="text-end"><strong class="text-success">{{ number_format($bien['commission'], 0, ',', ' ') }} FCFA</strong></td>
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
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
@if($evolutionMensuelle->count() > 0)
// Graphique d'évolution mensuelle
const ctx = document.getElementById('chartEvolution');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($evolutionMensuelle->pluck('mois')) !!},
        datasets: [{
            label: 'Montant Total (FCFA)',
            data: {!! json_encode($evolutionMensuelle->pluck('total')) !!},
            backgroundColor: 'rgba(54, 162, 235, 0.5)',
            borderColor: 'rgb(54, 162, 235)',
            borderWidth: 1,
            yAxisID: 'y',
        }, {
            label: 'Nombre de Transactions',
            data: {!! json_encode($evolutionMensuelle->pluck('nombre')) !!},
            backgroundColor: 'rgba(255, 99, 132, 0.5)',
            borderColor: 'rgb(255, 99, 132)',
            borderWidth: 1,
            type: 'line',
            yAxisID: 'y1',
        }]
    },
    options: {
        responsive: true,
        interaction: {
            mode: 'index',
            intersect: false,
        },
        plugins: {
            legend: {
                display: true,
                position: 'top',
            }
        },
        scales: {
            y: {
                type: 'linear',
                display: true,
                position: 'left',
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return value.toLocaleString('fr-FR') + ' FCFA';
                    }
                }
            },
            y1: {
                type: 'linear',
                display: true,
                position: 'right',
                beginAtZero: true,
                grid: {
                    drawOnChartArea: false,
                }
            }
        }
    }
});
@endif
</script>
@endsection
