@extends('backend.layouts.master')

@section('title')
    Rapport Acheteur - {{ $acheteur->username }}
@endsection

@section('content')
    <div class="container-fluid">
        <!-- En-tête impression -->
        <div class="d-none" id="print-header" style="display: none;">
            <div style="text-align: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 3px solid #198754;">
                <h1 style="font-size: 28px; margin-bottom: 10px; color: #000;">RAPPORT FINANCIER ACHETEUR</h1>
                <p style="font-size: 16px; margin: 5px 0; color: #333;">
                    <strong>{{ $acheteur->username }}</strong>
                    @if($acheteur->telephone)
                        — {{ $acheteur->telephone }}
                    @endif
                </p>
                <p style="font-size: 14px; margin: 5px 0; color: #666;">
                    @if($dateDebut && $dateFin)
                        Période : {{ $dateDebut->format('d/m/Y') }} au {{ $dateFin->format('d/m/Y') }}
                    @else
                        Toutes les périodes
                    @endif
                </p>
                <p style="font-size: 12px; margin: 5px 0; color: #999;">
                    Document généré le {{ now()->format('d/m/Y à H:i') }}
                </p>
            </div>
        </div>

        <!-- Header -->
        <div class="row mb-3 align-items-center">
            <div class="col-md-7">
                <h1 class="h3 mb-1 text-gray-800">
                    <i class="fas fa-file-invoice-dollar"></i> Rapport Acheteur
                </h1>
                <h5 class="text-muted mb-0">
                    <i class="fas fa-user-circle me-1"></i> {{ $acheteur->username }}
                    @if($acheteur->email)
                        <small class="ms-2"><i class="fas fa-envelope fa-xs"></i> {{ $acheteur->email }}</small>
                    @endif
                    @if($acheteur->telephone)
                        <small class="ms-2"><i class="fas fa-phone fa-xs"></i> {{ $acheteur->telephone }}</small>
                    @endif
                </h5>
                <small class="text-muted">
                    <i class="fas fa-calendar-alt me-1"></i>
                    @if($dateDebut && $dateFin)
                        {{ $dateDebut->format('d/m/Y') }} au {{ $dateFin->format('d/m/Y') }}
                    @else
                        Toutes les périodes
                    @endif
                </small>
            </div>
            <div class="col-md-5 text-end">
                <a href="{{ route('backend.rapports.acheteur') }}" class="btn btn-outline-secondary btn-sm me-2 no-print">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
                <a href="{{ route('backend.rapports.acheteur.pdf', ['acheteur_id' => $acheteur->id, 'date_debut' => $dateDebut?->format('Y-m-d'), 'date_fin' => $dateFin?->format('Y-m-d')]) }}" class="btn btn-success btn-sm me-2 no-print" title="Télécharger en PDF">
                    <i class="fas fa-file-pdf"></i> Télécharger PDF
                </a>
                <button type="button" class="btn btn-primary btn-sm no-print" onclick="window.print()" title="Imprimer">
                    <i class="fas fa-print"></i> Imprimer
                </button>
            </div>
        </div>

        <!-- Filtres -->
        <div class="card mb-3 no-print">
            <div class="card-body py-3">
                <form method="GET" action="{{ route('backend.rapports.acheteur') }}" class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label mb-1 small">Acheteur</label>
                        <select name="acheteur_id" class="form-select form-select-sm">
                            @foreach ($acheteurs as $ach)
                                <option value="{{ $ach->id }}" @selected($ach->id == $acheteur->id)>{{ $ach->username }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label mb-1 small">Date début</label>
                        <input type="date" name="date_debut" class="form-control form-control-sm" value="{{ $dateDebut?->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label mb-1 small">Date fin</label>
                        <input type="date" name="date_fin" class="form-control form-control-sm" value="{{ $dateFin?->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm flex-grow-1"><i class="fas fa-search"></i> Filtrer</button>
                        <a href="{{ route('backend.rapports.acheteur') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-redo"></i></a>
                    </div>
                </form>
            </div>
        </div>

        <!-- KPI Cards -->
        <div class="row mb-3">
            <div class="col-md-2 col-sm-4 mb-2">
                <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #0d6efd !important;">
                    <div class="card-body py-2 text-center">
                        <small class="text-muted d-block" style="font-size: 10px;">Total À Payer</small>
                        <h6 class="mb-0 fw-bold text-primary">{{ number_format($rapport['total_a_payer'], 0, ',', ' ') }} F</h6>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-sm-4 mb-2">
                <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #28a745 !important;">
                    <div class="card-body py-2 text-center">
                        <small class="text-muted d-block" style="font-size: 10px;">Total Payé</small>
                        <h6 class="mb-0 fw-bold text-success">{{ number_format($rapport['total_paye'], 0, ',', ' ') }} F</h6>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-sm-4 mb-2">
                <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #dc3545 !important;">
                    <div class="card-body py-2 text-center">
                        <small class="text-muted d-block" style="font-size: 10px;">Reste À Payer</small>
                        <h6 class="mb-0 fw-bold text-danger">{{ number_format($rapport['total_restant'], 0, ',', ' ') }} F</h6>
                    </div>
                </div>
            </div>
            @if($rapport['has_periode'])
            <div class="col-md-2 col-sm-4 mb-2">
                <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #17a2b8 !important;">
                    <div class="card-body py-2 text-center">
                        <small class="text-muted d-block" style="font-size: 10px;">Payé (Période)</small>
                        <h6 class="mb-0 fw-bold text-info">{{ number_format($rapport['total_paye_periode'], 0, ',', ' ') }} F</h6>
                    </div>
                </div>
            </div>
            @endif
            <div class="col-md-2 col-sm-4 mb-2">
                <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #ffc107 !important;">
                    <div class="card-body py-2 text-center">
                        <small class="text-muted d-block" style="font-size: 10px;">Taux Paiement</small>
                        <h6 class="mb-0 fw-bold text-warning">{{ $rapport['taux_paiement'] }}%</h6>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-sm-4 mb-2">
                <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #198754 !important;">
                    <div class="card-body py-2 text-center">
                        <small class="text-muted d-block" style="font-size: 10px;">Statut Global</small>
                        <span class="badge bg-{{ $rapport['statut_global']['badge'] }}">{{ $rapport['statut_global']['label'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Détail par vente -->
        @foreach($rapport['ventes'] as $rapportVente)
            <div class="card shadow-sm mb-3">
                <div class="card-header py-2" style="background-color: #d4edda;">
                    <div class="row align-items-center">
                        <div class="col-md-5">
                            <h6 class="mb-0">
                                <i class="fas fa-building me-1 text-success"></i>
                                {{ $rapportVente['adresse'] }}
                                <small class="text-muted ms-2">{{ $rapportVente['type_bien'] }}</small>
                            </h6>
                        </div>
                        <div class="col-md-7 text-end" style="font-size: 12px;">
                            <span class="me-3">
                                <strong>Prix :</strong> {{ number_format($rapportVente['prix_vente'], 0, ',', ' ') }} F
                            </span>
                            @if($rapportVente['date_vente'])
                                <span class="me-3">
                                    <strong>Date vente :</strong> {{ $rapportVente['date_vente']->format('d/m/Y') }}
                                </span>
                            @endif
                            @if($rapportVente['commission_agence'] > 0)
                                <span class="me-3">
                                    <strong>Commission :</strong> {{ number_format($rapportVente['commission_agence'], 0, ',', ' ') }} F
                                </span>
                            @endif
                            <span class="badge bg-{{ $rapportVente['statut_paiement']['badge'] }}">
                                {{ $rapportVente['statut_paiement']['label'] }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <!-- Barre de progression -->
                    <div class="px-3 py-2 border-bottom">
                        <div class="d-flex justify-content-between align-items-center mb-1" style="font-size: 12px;">
                            <span>Progression du paiement</span>
                            <span class="fw-bold">{{ number_format($rapportVente['total_paye'], 0, ',', ' ') }} F / {{ number_format($rapportVente['prix_vente'], 0, ',', ' ') }} F</span>
                        </div>
                        <div class="progress" style="height: 22px;">
                            @php $pct = $rapportVente['pourcentage_paiement']; @endphp
                            <div class="progress-bar {{ $pct >= 100 ? 'bg-success' : ($pct >= 50 ? 'bg-info' : ($pct > 0 ? 'bg-warning' : 'bg-danger')) }}"
                                role="progressbar" style="width: {{ max($pct, 3) }}%">
                                {{ round($pct, 1) }}%
                            </div>
                        </div>
                        @if($rapportVente['reste_a_payer'] > 0)
                            <small class="text-danger mt-1 d-block">
                                <i class="fas fa-exclamation-triangle fa-xs"></i>
                                Reste à payer : <strong>{{ number_format($rapportVente['reste_a_payer'], 0, ',', ' ') }} F</strong>
                            </small>
                        @endif
                    </div>

                    <!-- Mini KPI -->
                    <div class="row g-0 border-bottom" style="font-size: 12px;">
                        <div class="col text-center py-2 border-end">
                            <small class="text-muted d-block">Prix Vente</small>
                            <strong>{{ number_format($rapportVente['prix_vente'], 0, ',', ' ') }} F</strong>
                        </div>
                        <div class="col text-center py-2 border-end">
                            <small class="text-muted d-block">Total Payé</small>
                            <strong class="text-success">{{ number_format($rapportVente['total_paye'], 0, ',', ' ') }} F</strong>
                        </div>
                        <div class="col text-center py-2 border-end">
                            <small class="text-muted d-block">Reste</small>
                            <strong class="text-danger">{{ number_format($rapportVente['reste_a_payer'], 0, ',', ' ') }} F</strong>
                        </div>
                        @if($rapportVente['has_periode'])
                        <div class="col text-center py-2">
                            <small class="text-muted d-block">Payé (Période)</small>
                            <strong class="text-info">{{ number_format($rapportVente['total_paye_periode'], 0, ',', ' ') }} F</strong>
                        </div>
                        @endif
                    </div>

                    @if($rapportVente['has_periode'])
                    {{-- MODE FILTRE ACTIF: paiements période + toggle vers tout --}}
                    <!-- Paiements de la période -->
                    <div class="px-3 py-2 border-bottom d-flex justify-content-between align-items-center">
                        <strong style="font-size: 12px;">
                            <i class="fas fa-filter me-1 text-info"></i> Paiements de la période
                            <span class="badge bg-info ms-1">{{ $rapportVente['paiements_periode']->count() }}</span>
                        </strong>
                        @if($rapportVente['tous_paiements']->count() > $rapportVente['paiements_periode']->count())
                            <button class="btn btn-outline-secondary btn-sm py-0 px-2 btnToggleAll" 
                                    data-target="allPaiements{{ $loop->index }}" 
                                    data-period="periodPaiements{{ $loop->index }}"
                                    data-total="{{ $rapportVente['tous_paiements']->count() }}" style="font-size: 11px;">
                                <i class="fas fa-history"></i> Voir tout ({{ $rapportVente['tous_paiements']->count() }})
                            </button>
                        @endif
                    </div>

                    <!-- Tableau Paiements Période -->
                    <div class="table-responsive" id="periodPaiements{{ $loop->index }}">
                        <table class="table table-sm table-hover mb-0" style="font-size: 12px;">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 30px;">#</th>
                                    <th>Date Paiement</th>
                                    <th>Type</th>
                                    <th class="text-end">Montant</th>
                                    <th>Méthode</th>
                                    <th>Référence</th>
                                    <th class="text-center">Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rapportVente['paiements_periode'] as $idx => $paiement)
                                    @php $datePaiement = \Carbon\Carbon::parse($paiement['date']); @endphp
                                    <tr class="table-info-soft">
                                        <td>{{ $idx + 1 }}</td>
                                        <td><i class="fas fa-calendar fa-xs text-muted me-1"></i> {{ $datePaiement->format('d/m/Y') }}</td>
                                        <td>
                                            @php
                                                $typeLabels = ['prix_achat' => 'Prix achat', 'arrhes' => 'Arrhes', 'frais_agence' => 'Frais agence', 'acompte' => 'Acompte', 'solde' => 'Solde', 'caution' => 'Caution'];
                                            @endphp
                                            {{ $typeLabels[$paiement['type']] ?? ucfirst($paiement['type']) }}
                                        </td>
                                        <td class="text-end fw-bold text-success">{{ number_format($paiement['montant'], 0, ',', ' ') }} F</td>
                                        <td>{{ ucfirst($paiement['methode'] ?? '-') }}</td>
                                        <td><small class="text-muted">{{ $paiement['reference'] ?? '-' }}</small></td>
                                        <td class="text-center"><span class="badge bg-{{ $paiement['statut'] === 'paye' ? 'success' : 'warning' }}">{{ ucfirst($paiement['statut']) }}</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-3">
                                            <i class="fas fa-info-circle me-1"></i> Aucun paiement sur cette période.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if($rapportVente['paiements_periode']->count() > 0)
                                <tfoot class="table-light">
                                    <tr class="fw-bold">
                                        <td colspan="3" class="text-end">Total période :</td>
                                        <td class="text-end text-info">{{ number_format($rapportVente['total_paye_periode'], 0, ',', ' ') }} F</td>
                                        <td colspan="3"></td>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>

                    <!-- Tableau Tous Paiements (caché par défaut, toggle) -->
                    <div class="table-responsive d-none" id="allPaiements{{ $loop->index }}">
                        <table class="table table-sm table-hover mb-0" style="font-size: 12px;">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 30px;">#</th>
                                    <th>Date Paiement</th>
                                    <th>Type</th>
                                    <th class="text-end">Montant</th>
                                    <th>Méthode</th>
                                    <th>Référence</th>
                                    <th class="text-center">Période</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rapportVente['tous_paiements'] as $idx => $paiement)
                                    @php
                                        $datePaiement = \Carbon\Carbon::parse($paiement['date']);
                                        $estDansPeriode = ($dateDebut && $dateFin) ? $datePaiement->between($dateDebut, $dateFin) : true;
                                    @endphp
                                    <tr class="{{ $estDansPeriode ? 'table-info-soft' : '' }}">
                                        <td>{{ $idx + 1 }}</td>
                                        <td><i class="fas fa-calendar fa-xs text-muted me-1"></i> {{ $datePaiement->format('d/m/Y') }}</td>
                                        <td>
                                            @php $typeLabels = ['prix_achat' => 'Prix achat', 'arrhes' => 'Arrhes', 'frais_agence' => 'Frais agence', 'acompte' => 'Acompte', 'solde' => 'Solde', 'caution' => 'Caution']; @endphp
                                            {{ $typeLabels[$paiement['type']] ?? ucfirst($paiement['type']) }}
                                        </td>
                                        <td class="text-end fw-bold text-success">{{ number_format($paiement['montant'], 0, ',', ' ') }} F</td>
                                        <td>{{ ucfirst($paiement['methode'] ?? '-') }}</td>
                                        <td><small class="text-muted">{{ $paiement['reference'] ?? '-' }}</small></td>
                                        <td class="text-center">
                                            @if($estDansPeriode)
                                                <span class="badge bg-info" style="font-size: 9px;">Dans période</span>
                                            @else
                                                <span class="badge bg-secondary" style="font-size: 9px;">Hors période</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            @if($rapportVente['tous_paiements']->count() > 0)
                                <tfoot class="table-light">
                                    <tr class="fw-bold">
                                        <td colspan="3" class="text-end">Total global :</td>
                                        <td class="text-end text-success">{{ number_format($rapportVente['total_paye'], 0, ',', ' ') }} F</td>
                                        <td colspan="3"></td>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>

                    @else
                    {{-- MODE SANS FILTRE: afficher tous les paiements directement --}}
                    <div class="px-3 py-2 border-bottom">
                        <strong style="font-size: 12px;">
                            <i class="fas fa-list me-1"></i> Historique des paiements
                            <span class="badge bg-success ms-1">{{ $rapportVente['tous_paiements']->count() }}</span>
                        </strong>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0" style="font-size: 12px;">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 30px;">#</th>
                                    <th>Date Paiement</th>
                                    <th>Type</th>
                                    <th class="text-end">Montant</th>
                                    <th>Méthode</th>
                                    <th>Référence</th>
                                    <th class="text-center">Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rapportVente['tous_paiements'] as $idx => $paiement)
                                    @php $datePaiement = \Carbon\Carbon::parse($paiement['date']); @endphp
                                    <tr>
                                        <td>{{ $idx + 1 }}</td>
                                        <td><i class="fas fa-calendar fa-xs text-muted me-1"></i> {{ $datePaiement->format('d/m/Y') }}</td>
                                        <td>
                                            @php $typeLabels = ['prix_achat' => 'Prix achat', 'arrhes' => 'Arrhes', 'frais_agence' => 'Frais agence', 'acompte' => 'Acompte', 'solde' => 'Solde', 'caution' => 'Caution']; @endphp
                                            {{ $typeLabels[$paiement['type']] ?? ucfirst($paiement['type']) }}
                                        </td>
                                        <td class="text-end fw-bold text-success">{{ number_format($paiement['montant'], 0, ',', ' ') }} F</td>
                                        <td>{{ ucfirst($paiement['methode'] ?? '-') }}</td>
                                        <td><small class="text-muted">{{ $paiement['reference'] ?? '-' }}</small></td>
                                        <td class="text-center"><span class="badge bg-{{ $paiement['statut'] === 'paye' ? 'success' : 'warning' }}">{{ ucfirst($paiement['statut']) }}</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-3">Aucun paiement enregistré.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if($rapportVente['tous_paiements']->count() > 0)
                                <tfoot class="table-light">
                                    <tr class="fw-bold">
                                        <td colspan="3" class="text-end">Total :</td>
                                        <td class="text-end text-success">{{ number_format($rapportVente['total_paye'], 0, ',', ' ') }} F</td>
                                        <td colspan="3"></td>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
                    @endif
                    </div>
                </div>
            </div>
        @endforeach

        @if($rapport['ventes']->isEmpty())
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i> Aucune vente active trouvée pour cet acheteur.
            </div>
        @endif
    </div>

    <style>
        #print-header { display: none !important; }
        .table-info-soft { background-color: rgba(23, 162, 184, 0.05) !important; }

        @media print {
            @page { size: A4 portrait; margin: 10mm; }
            body { font-size: 11px !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            .no-print { display: none !important; }
            #print-header { display: block !important; }
            .card { border: 1px solid #ddd !important; box-shadow: none !important; break-inside: avoid; }
            .container-fluid { padding: 0 !important; }
            .progress { border: 1px solid #ccc; }
            .progress-bar { -webkit-print-color-adjust: exact !important; }
            .badge { border: 1px solid #999; -webkit-print-color-adjust: exact !important; }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.btnToggleAll').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var targetId = this.getAttribute('data-target');
                    var periodId = this.getAttribute('data-period');
                    var targetEl = document.getElementById(targetId);
                    var periodEl = document.getElementById(periodId);
                    
                    if (targetEl.classList.contains('d-none')) {
                        targetEl.classList.remove('d-none');
                        periodEl.classList.add('d-none');
                        this.innerHTML = '<i class="fas fa-filter"></i> Voir période';
                        this.classList.remove('btn-outline-secondary');
                        this.classList.add('btn-outline-info');
                    } else {
                        targetEl.classList.add('d-none');
                        periodEl.classList.remove('d-none');
                        this.innerHTML = '<i class="fas fa-history"></i> Voir tout (' + this.dataset.total + ')';
                        this.classList.remove('btn-outline-info');
                        this.classList.add('btn-outline-secondary');
                    }
                });
            });
        });
    </script>
@endsection
