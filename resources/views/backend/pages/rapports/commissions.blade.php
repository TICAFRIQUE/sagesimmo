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
                                <small class="text-muted">{{ $nombreTransactions }} transactions</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-info">
                            <div class="card-body text-center">
                                <h6 class="text-muted mb-2">Commissions Locations</h6>
                                <h4 class="text-info mb-0">{{ number_format($totalCommissionsLocations, 0, ',', ' ') }} FCFA</h4>
                                <small class="text-muted">{{ $nombreLocations }} loyers</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-success">
                            <div class="card-body text-center">
                                <h6 class="text-muted mb-2">Commissions Ventes</h6>
                                <h4 class="text-success mb-0">{{ number_format($totalCommissionsVentes, 0, ',', ' ') }} FCFA</h4>
                                <small class="text-muted">{{ $nombreVentes }} ventes</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-warning">
                            <div class="card-body text-center">
                                <h6 class="text-muted mb-2">Commission Moyenne</h6>
                                <h4 class="text-warning mb-0">{{ number_format($commissionMoyenne, 0, ',', ' ') }} FCFA</h4>
                                <small class="text-muted">par transaction</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tableau Commissions Locations -->
                @if(in_array($typeTransaction, ['tous', 'location']) && $commissionsLocations->count() > 0)
                <div class="card mb-4">
                    <div class="card-header bg-primary bg-opacity-10">
                        <h6 class="mb-0"><i class="ri-home-4-line me-2"></i>Commissions sur Locations ({{ $commissionsLocations->count() }} loyers)</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Réf. Bien</th>
                                        <th>Bien</th>
                                        <th>Locataire</th>
                                        <th class="text-end">Montant Loyer</th>
                                        <th>Taux</th>
                                        <th class="text-end">Commission</th>
                                        <th>Méthode</th>
                                        <th>Référence</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($commissionsLocations as $commission)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($commission['date'])->format('d/m/Y') }}</td>
                                        <td>
                                            <a href="{{ route('backend.locations.show', $commission['location_id']) }}" class="text-decoration-none" target="_blank">
                                                {{ $commission['reference'] }}
                                                <i class="ri-external-link-line ms-1"></i>
                                            </a>
                                        </td>
                                        <td>{{ Str::limit($commission['bien'], 30) }}</td>
                                        <td>{{ $commission['client'] }}</td>
                                        <td class="text-end">{{ number_format($commission['montant_transaction'], 0, ',', ' ') }} FCFA</td>
                                        <td><span class="badge bg-info">{{ $commission['commission_config'] }}</span></td>
                                        <td class="text-end"><strong class="text-primary">{{ number_format($commission['commission_montant'], 0, ',', ' ') }} FCFA</strong></td>
                                        <td><span class="badge bg-secondary">{{ ucfirst($commission['methode_paiement']) }}</span></td>
                                        <td>{{ $commission['reference_paiement'] ?? '-' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="4" class="text-end">TOTAL LOCATIONS:</th>
                                        <th class="text-end">{{ number_format($totalTransactionsLocations, 0, ',', ' ') }} FCFA</th>
                                        <th></th>
                                        <th class="text-end"><strong class="text-primary">{{ number_format($totalCommissionsLocations, 0, ',', ' ') }} FCFA</strong></th>
                                        <th colspan="2"></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Tableau Commissions Ventes -->
                @if(in_array($typeTransaction, ['tous', 'vente']) && $commissionsVentes->count() > 0)
                <div class="card mb-4">
                    <div class="card-header bg-success bg-opacity-10">
                        <h6 class="mb-0"><i class="ri-building-line me-2"></i>Commissions sur Ventes ({{ $commissionsVentes->count() }} ventes)</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date Paiement</th>
                                        <th>Réf. Bien</th>
                                        <th>Bien</th>
                                        <th>Acheteur</th>
                                        <th class="text-end">Prix Vente</th>
                                        <th>Taux</th>
                                        <th class="text-end">Commission</th>
                                        <th>Méthode</th>
                                        <th>Référence</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($commissionsVentes as $commission)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($commission['date'])->format('d/m/Y') }}</td>
                                        <td>
                                            <a href="{{ route('backend.ventes.show', $commission['vente_id']) }}" class="text-decoration-none" target="_blank">
                                                {{ $commission['reference'] }}
                                                <i class="ri-external-link-line ms-1"></i>
                                            </a>
                                        </td>
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
                                        <th colspan="4" class="text-end">TOTAL VENTES:</th>
                                        <th class="text-end">{{ number_format($totalTransactionsVentes, 0, ',', ' ') }} FCFA</th>
                                        <th></th>
                                        <th class="text-end"><strong class="text-success">{{ number_format($totalCommissionsVentes, 0, ',', ' ') }} FCFA</strong></th>
                                        <th colspan="2"></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Message si aucune commission -->
                @if($commissionsLocations->count() == 0 && $commissionsVentes->count() == 0)
                <div class="alert alert-info">
                    <i class="ri-information-line me-1"></i>Aucune commission trouvée pour la période sélectionnée.
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
