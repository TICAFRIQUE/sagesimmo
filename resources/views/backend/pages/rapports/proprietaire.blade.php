@extends('backend.layouts.master')

@section('title')
    Rapport Propriétaire - {{ $proprietaire->username }}
@endsection

@section('content')
    <div class="container-fluid">
        <!-- En-tête pour l'impression uniquement -->
        <div class="d-none" id="print-header" style="display: none;">
            <div style="text-align: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 3px solid #0d6efd;">
                <h1 style="font-size: 28px; margin-bottom: 10px; color: #000;">RAPPORT FINANCIER PROPRIÉTAIRE</h1>
                <p style="font-size: 16px; margin: 5px 0; color: #333;">
                    <strong>{{ $proprietaire->username }}</strong>
                    @if($proprietaire->type_proprietaire === 'agence')
                        <span style="border: 1px solid #000; padding: 2px 8px; margin-left: 10px;">AGENCE</span>
                    @endif
                </p>
                <p style="font-size: 14px; margin: 5px 0; color: #666;">
                    Période : {{ $dateDebut->format('d/m/Y') }} au {{ $dateFin->format('d/m/Y') }}
                </p>
                <p style="font-size: 12px; margin: 5px 0; color: #999;">
                    Document généré le {{ now()->format('d/m/Y à H:i') }}
                </p>
            </div>
        </div>

        <!-- Header amélioré -->
        <div class="row mb-3 align-items-center">
            <div class="col-md-8">
                <h1 class="h3 mb-1 text-gray-800">
                    <i class="fas fa-file-invoice-dollar"></i> Rapport Financier
                </h1>
                <h5 class="text-muted mb-0">
                    <i class="fas fa-user-circle me-1"></i> {{ $proprietaire->username }}
                    @if($proprietaire->type_proprietaire === 'agence')
                        <span class="badge bg-primary ms-2">AGENCE</span>
                    @endif
                </h5>
                <small class="text-muted">
                    <i class="fas fa-calendar-alt me-1"></i> 
                    {{ $dateDebut->format('d/m/Y') }} au {{ $dateFin->format('d/m/Y') }}
                </small>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('backend.rapports.proprietaire') }}" class="btn btn-outline-secondary btn-sm me-2 no-print">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
                <a href="{{ route('backend.rapports.proprietaire.pdf', ['proprietaire_id' => $proprietaire->id, 'date_debut' => $dateDebut->format('Y-m-d'), 'date_fin' => $dateFin->format('Y-m-d')]) }}" class="btn btn-success btn-sm me-2 no-print" title="Télécharger en PDF">
                    <i class="fas fa-file-pdf"></i> Télécharger PDF
                </a>
                <button type="button" class="btn btn-primary btn-sm no-print" onclick="window.print()" title="Imprimer le rapport financier">
                    <i class="fas fa-print"></i> Imprimer
                </button>
            </div>
        </div>

        <!-- Filtres compacts -->
        <div class="card mb-3 no-print">
            <div class="card-body py-2">
                <form method="GET" action="{{ route('backend.rapports.proprietaire') }}" class="row g-2 align-items-end">
                    <input type="hidden" name="proprietaire_id" value="{{ $proprietaire->id }}">

                    <div class="col-md-3">
                        <label for="date_debut" class="form-label mb-1 small">Date début</label>
                        <input type="date" name="date_debut" id="date_debut" class="form-control form-control-sm"
                            value="{{ $dateDebut->format('Y-m-d') }}">
                    </div>

                    <div class="col-md-3">
                        <label for="date_fin" class="form-label mb-1 small">Date fin</label>
                        <input type="date" name="date_fin" id="date_fin" class="form-control form-control-sm"
                            value="{{ $dateFin->format('Y-m-d') }}">
                    </div>

                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                            <i class="fas fa-search"></i> Filtrer
                        </button>
                        <a href="{{ route('backend.rapports.proprietaire', ['proprietaire_id' => $proprietaire->id]) }}" 
                           class="btn btn-outline-secondary btn-sm" title="Réinitialiser">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- KPI améliorés -->
        <div class="row mb-3">
            <div class="col-md-3 mb-2">
                <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #0d6efd !important;">
                    <div class="card-body py-3 d-flex align-items-center">
                        <div class="me-3 text-primary" style="font-size: 2rem; opacity: 0.6;">
                            <i class="fas fa-coins"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block mb-1" style="font-size: 11px;">Total Encaissé</small>
                            <h5 class="mb-0 fw-bold text-primary">{{ number_format($rapport['total_brut_encaisse'], 0, ',', ' ') }} F</h5>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-2">
                <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #dc3545 !important;">
                    <div class="card-body py-3 d-flex align-items-center">
                        <div class="me-3 text-danger" style="font-size: 2rem; opacity: 0.6;">
                            <i class="fas fa-percentage"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block mb-1" style="font-size: 11px;">Commission Agence</small>
                            <h5 class="mb-0 fw-bold text-danger">{{ number_format($rapport['total_commission_agence'], 0, ',', ' ') }} F</h5>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-2">
                <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #ffc107 !important;">
                    <div class="card-body py-3 d-flex align-items-center">
                        <div class="me-3 text-warning" style="font-size: 2rem; opacity: 0.6;">
                            <i class="fas fa-tools"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block mb-1" style="font-size: 11px;">Charges</small>
                            <h5 class="mb-0 fw-bold text-warning">{{ number_format($rapport['total_charges'], 0, ',', ' ') }} F</h5>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-2">
                <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #28a745 !important;">
                    <div class="card-body py-3 d-flex align-items-center">
                        <div class="me-3 text-success" style="font-size: 2rem; opacity: 0.6;">
                            <i class="fas fa-hand-holding-usd"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block mb-1" style="font-size: 11px;">Revenu Net</small>
                            <h5 class="mb-0 fw-bold text-success">{{ number_format($rapport['revenue_net'], 0, ',', ' ') }} F</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @php
            // Séparer les biens en location et en vente
            $biensLocation = collect($rapport['biens'])->filter(fn($b) => $b['type_transaction'] === 'location' || $b['encaissement_loyers']['total'] > 0);
            $biensVente = collect($rapport['biens'])->filter(fn($b) => $b['type_transaction'] === 'vente' || $b['encaissement_ventes']['total'] > 0);
        @endphp

        <!-- Section Biens en Location -->
        @if($biensLocation->count() > 0)
            <div class="card mb-4">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-home me-2"></i> Biens en Location
                    </h5>
                    <span class="badge bg-white text-info">{{ $biensLocation->count() }} bien(s)</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Bien</th>
                                    <th class="text-end">Loyers Encaissés</th>
                                    <th class="text-end">Commission</th>
                                    <th class="text-end">Charges</th>
                                    <th class="text-end">Revenu Net</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($biensLocation as $bien)
                                    @php
                                        $paiements_loyers = $bien['encaissement_loyers']['paiements'] ?? collect();
                                        $paiements_combined = $paiements_loyers->map(function ($p) {
                                            $clientName = null;
                                            if (!empty($p->payable)) {
                                                $clientName = $p->payable->locataire->name ?? null;
                                            }
                                            return [
                                                'id' => $p->id ?? null,
                                                'date' => optional($p->date_paiement)->format('d/m/Y') ?? ($p->date_paiement ?? null),
                                                'montant' => $p->montant ?? 0,
                                                'methode' => $p->methode_paiement ?? '-',
                                                'reference' => $p->reference ?? '-',
                                                'type' => $p->type_paiement ?? '-',
                                                'client' => $clientName ?? '-',
                                            ];
                                        })->values();
                                    @endphp
                                    <tr>
                                        <td>
                                            <a href="{{ route('backend.annonces.show', $bien['bien']) }}"
                                                class="text-dark text-decoration-none" title="Voir le détail du bien">
                                                <strong>{{ $bien['bien']->titre ?? 'N/A' }}</strong>
                                            </a>
                                            <br>
                                            <small class="text-muted">{{ $bien['type_bien'] }}</small>
                                            <span class="badge bg-info">{{ $bien['reference'] }}</span>
                                        </td>
                                        <td class="text-end">
                                            <strong class="text-info">{{ number_format($bien['encaissement_loyers']['total'], 0, ',', ' ') }} F</strong>
                                            <br>
                                            <small class="text-muted">{{ $bien['encaissement_loyers']['nombre'] }} paiement(s)</small>
                                        </td>
                                        <td class="text-end text-danger">
                                            {{ number_format($bien['total_commission_agence'], 0, ',', ' ') }} F
                                        </td>
                                        <td class="text-end text-warning">
                                            {{ number_format($bien['total_charges'], 0, ',', ' ') }} F
                                        </td>
                                        <td class="text-end">
                                            <strong class="text-success">{{ number_format($bien['revenue_net'], 0, ',', ' ') }} F</strong>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-info btnVoirPaiements"
                                                data-paiements='@json($paiements_combined)'
                                                data-bien-titre="{{ $bien['bien']->titre ?? '' }}">
                                                <i class="fas fa-eye"></i> Détails
                                            </button>
                                        </td>
                                    </tr>
                                    @if ($bien['charges']->isNotEmpty())
                                        <tr class="table-light">
                                            <td colspan="6" class="py-2">
                                                <small class="text-muted">
                                                    <strong><i class="fas fa-tools me-1"></i> Charges :</strong>
                                                    @foreach ($bien['charges'] as $charge)
                                                        <span class="badge bg-warning text-dark me-1">
                                                            {{ $charge->type_charge_libelle }}: {{ number_format($charge->montant, 0, ',', ' ') }} F
                                                        </span>
                                                    @endforeach
                                                </small>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                            <tfoot class="table-info">
                                <tr>
                                    <td><strong>TOTAL LOCATIONS</strong></td>
                                    <td class="text-end"><strong>{{ number_format($biensLocation->sum('encaissement_loyers.total'), 0, ',', ' ') }} F</strong></td>
                                    <td class="text-end"><strong>{{ number_format($biensLocation->sum('total_commission_agence'), 0, ',', ' ') }} F</strong></td>
                                    <td class="text-end"><strong>{{ number_format($biensLocation->sum('total_charges'), 0, ',', ' ') }} F</strong></td>
                                    <td class="text-end"><strong>{{ number_format($biensLocation->sum('revenue_net'), 0, ',', ' ') }} F</strong></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        <!-- Section Biens en Vente -->
        @if($biensVente->count() > 0)
            <div class="card mb-4">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-tags me-2"></i> Biens en Vente
                    </h5>
                    <span class="badge bg-white text-success">{{ $biensVente->count() }} bien(s)</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Bien</th>
                                    <th class="text-end">Prix de Vente</th>
                                    <th class="text-end">Commission</th>
                                    <th class="text-end">Charges</th>
                                    <th class="text-end">Revenu Net</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($biensVente as $bien)
                                    @php
                                        $paiements_ventes = $bien['encaissement_ventes']['paiements'] ?? collect();
                                        $paiements_combined = $paiements_ventes->map(function ($p) {
                                            $clientName = null;
                                            if (!empty($p->payable)) {
                                                $clientName = $p->payable->client->name ?? null;
                                            }
                                            return [
                                                'id' => $p->id ?? null,
                                                'date' => optional($p->date_paiement)->format('d/m/Y') ?? ($p->date_paiement ?? null),
                                                'montant' => $p->montant ?? 0,
                                                'methode' => $p->methode_paiement ?? '-',
                                                'reference' => $p->reference ?? '-',
                                                'type' => $p->type_paiement ?? '-',
                                                'client' => $clientName ?? '-',
                                            ];
                                        })->values();
                                    @endphp
                                    <tr>
                                        <td>
                                            <a href="{{ route('backend.annonces.show', $bien['bien']) }}"
                                                class="text-dark text-decoration-none" title="Voir le détail du bien">
                                                <strong>{{ $bien['bien']->titre ?? 'N/A' }}</strong>
                                            </a>
                                            <br>
                                            <small class="text-muted">{{ $bien['type_bien'] }}</small>
                                            <span class="badge bg-success">{{ $bien['adresse'] }}</span>
                                        </td>
                                        <td class="text-end">
                                            <strong class="text-success">{{ number_format($bien['encaissement_ventes']['total'], 0, ',', ' ') }} F</strong>
                                            <br>
                                            <small class="text-muted">{{ $bien['encaissement_ventes']['nombre'] }} paiement(s)</small>
                                        </td>
                                        <td class="text-end text-danger">
                                            {{ number_format($bien['total_commission_agence'], 0, ',', ' ') }} F
                                        </td>
                                        <td class="text-end text-warning">
                                            {{ number_format($bien['total_charges'], 0, ',', ' ') }} F
                                        </td>
                                        <td class="text-end">
                                            <strong class="text-success">{{ number_format($bien['revenue_net'], 0, ',', ' ') }} F</strong>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-success btnVoirPaiements"
                                                data-paiements='@json($paiements_combined)'
                                                data-bien-titre="{{ $bien['bien']->titre ?? '' }}">
                                                <i class="fas fa-eye"></i> Détails
                                            </button>
                                        </td>
                                    </tr>
                                    @if ($bien['charges']->isNotEmpty())
                                        <tr class="table-light">
                                            <td colspan="6" class="py-2">
                                                <small class="text-muted">
                                                    <strong><i class="fas fa-tools me-1"></i> Charges :</strong>
                                                    @foreach ($bien['charges'] as $charge)
                                                        <span class="badge bg-warning text-dark me-1">
                                                            {{ $charge->type_charge_libelle }}: {{ number_format($charge->montant, 0, ',', ' ') }} F
                                                        </span>
                                                    @endforeach
                                                </small>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                            <tfoot class="table-success">
                                <tr>
                                    <td><strong>TOTAL VENTES</strong></td>
                                    <td class="text-end"><strong>{{ number_format($biensVente->sum('encaissement_ventes.total'), 0, ',', ' ') }} F</strong></td>
                                    <td class="text-end"><strong>{{ number_format($biensVente->sum('total_commission_agence'), 0, ',', ' ') }} F</strong></td>
                                    <td class="text-end"><strong>{{ number_format($biensVente->sum('total_charges'), 0, ',', ' ') }} F</strong></td>
                                    <td class="text-end"><strong>{{ number_format($biensVente->sum('revenue_net'), 0, ',', ' ') }} F</strong></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        @if($biensLocation->count() === 0 && $biensVente->count() === 0)
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                Aucun bien trouvé pour cette période.
            </div>
        @endif

        <!-- Section de calcul -->
        <div class="card mb-4">
            <div class="card-header bg-light">
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
                                    <strong class="h5">{{ number_format($rapport['revenue_net'], 0, ',', ' ') }}
                                        F</strong>
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

        <!-- Section des Versements -->
        <div class="card mt-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-money-check-alt"></i> Versements au Propriétaire
                </h5>
                <div class="btn-group" role="group">
                    {{-- <a href="{{ route('backend.versements.index') }}" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-list"></i> Liste
                </a> --}}
                    @if ($rapport['reste_a_verser'] > 0)
                        <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal"
                            data-bs-target="#modalVersement">
                            <i class="fas fa-plus"></i> Ajouter versement
                        </button>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card border-left-info">
                            <div class="card-body">
                                <div class="text-info font-weight-bold text-uppercase mb-1">Montant à Verser</div>
                                <div class="h3 mb-0">
                                    {{ number_format($rapport['revenue_net'], 0, ',', ' ') }} F
                                </div>
                                <small class="text-muted">
                                    Revenue net de la période
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card border-left-success">
                            <div class="card-body">
                                <div class="text-success font-weight-bold text-uppercase mb-1">Montant Versé</div>
                                <div class="h3 mb-0">
                                    {{ number_format($rapport['montant_total_verse'], 0, ',', ' ') }} F
                                </div>
                                <small class="text-muted">
                                    {{ $rapport['versements']->where('statut', 'effectue')->count() }} versement(s)
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card border-left-primary">
                            <div class="card-body">
                                <div class="text-primary font-weight-bold text-uppercase mb-1">Reste à Verser</div>
                                <div class="h3 mb-0">
                                    {{ number_format($rapport['reste_a_verser'], 0, ',', ' ') }} F
                                </div>
                                <small class="text-muted">
                                    Montant restant à recevoir
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card border-left-{{ $rapport['statut_versement']['badge'] }}">
                            <div class="card-body">
                                <div class="font-weight-bold text-uppercase mb-1">Statut versement</div>
                                <div class="h5 mb-0">
                                    <span class="badge bg-{{ $rapport['statut_versement']['badge'] }}">
                                        {{ $rapport['statut_versement']['label'] }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($rapport['versements']->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead>
                                <tr class="table-light">
                                    <th>Date</th>
                                    <th>Période</th>
                                    <th>Montant</th>
                                    <th>Mode</th>
                                    <th>Référence</th>
                                    <th>Statut</th>
                                    <th>Notes</th>
                                    <th class="text-center" style="width: 150px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rapport['versements'] as $versement)
                                    <tr>
                                        <td>{{ $versement->date_versement->format('d/m/Y') }}</td>
                                        <td>
                                            @if ($versement->date_debut && $versement->date_fin)
                                                {{ $versement->date_debut->format('d/m/Y') }} -
                                                {{ $versement->date_fin->format('d/m/Y') }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td class="text-end">{{ number_format($versement->montant, 0, ',', ' ') }} F</td>
                                        <td>
                                            <small
                                                class="badge bg-light text-dark">{{ $versement->mode_versement }}</small>
                                        </td>
                                        <td><small>{{ $versement->reference ?? '-' }}</small></td>
                                        <td>
                                            @if ($versement->statut === 'effectue')
                                                <span class="badge bg-success">Effectué</span>
                                            @elseif($versement->statut === 'en_attente')
                                                <span class="badge bg-warning">En attente</span>
                                            @elseif($versement->statut === 'annule')
                                                <span class="badge bg-danger">Annulé</span>
                                            @else
                                                <span class="badge bg-secondary">{{ $versement->statut }}</span>
                                            @endif
                                        </td>
                                        <td><small class="text-muted">{{ $versement->notes ?? '-' }}</small></td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm" role="group">
                                                {{-- <a href="{{ route('backend.versements.edit', $versement) }}"
                                                    class="btn btn-sm btn-outline-primary" title="Éditer">
                                                    <i class="fas fa-edit"></i>
                                                </a> --}}
                                                @if ($versement->statut !== 'annule')
                                                    <form action="{{ route('backend.versements.cancel', $versement) }}"
                                                        method="POST" style="display: inline;"
                                                        onsubmit="return confirm('Annuler ce versement ?');">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-sm btn-outline-warning"
                                                            title="Annuler">
                                                            <i class="fas fa-times-circle"></i>
                                                        </button>
                                                    </form>
                                                @endif

                                                @if ($versement->statut === 'annule')
                                                    <form action="{{ route('backend.versements.destroy', $versement) }}"
                                                        method="POST" style="display: inline;"
                                                        onsubmit="return confirm('Supprimer ce versement ?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                            title="Supprimer">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Aucun versement enregistré pour cette période.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal Ajouter Versement -->
    <div class="modal fade" id="modalVersement" tabindex="-1" aria-labelledby="modalVersementLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white" id="modalVersementLabel">
                        <i class="fas fa-money-check-alt"></i> Enregistrer un Versement
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="formVersement" action="{{ route('backend.versements.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <!-- Propriétaire (hidden) -->
                        <input type="hidden" name="proprietaire_id" value="{{ $proprietaire->id }}">

                        <!-- Montant à Verser (hidden + affichage) -->
                        <input type="hidden" id="montantAVerserModal" value="{{ $rapport['reste_a_verser'] }}">
                        <h4 class="py-2">Montant à Verser (F) <span class="text-info">Net</span> :
                            <strong>{{ number_format($rapport['reste_a_verser'], 0, ',', ' ') }}</strong>
                        </h4>

                        <!-- Type de Versement - Boutons Radio -->
                        <div class="mb-3">
                            <label class="form-label">Type de Versement <span class="text-danger">*</span></label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="type_versement" id="typeTotal"
                                    value="total">
                                <label class="btn btn-outline-primary w-50" for="typeTotal">
                                    <i class="fas fa-check-circle me-1"></i> Montant Total
                                </label>

                                <input type="radio" class="btn-check" name="type_versement" id="typePartiel"
                                    value="partiel">
                                <label class="btn btn-outline-primary w-50" for="typePartiel">
                                    <i class="fas fa-minus-circle me-1"></i> Partiel
                                </label>
                            </div>
                        </div>

                        <!-- Montant -->
                        <div class="mb-3">
                            <label for="montantModal" class="form-label">Montant (F) <span
                                    class="text-danger">*</span></label>
                            <input type="number" name="montant" id="montantModal" class="form-control" placeholder="0"
                                step="1" required disabled>
                        </div>

                        <!-- Montant Restant à Payer (read-only) -->
                        <div class="mb-3">
                            <label for="montantRestantModal" class="form-label">Montant Restant à Payer (F)</label>
                            <input type="number" id="montantRestantModal" class="form-control"
                                value="{{ $rapport['revenue_net'] }}" readonly style="background-color: #f8f9fa;">
                        </div>

                        <!-- Date du versement -->
                        <div class="mb-3">
                            <label for="dateVersementModal" class="form-label">Date du versement <span
                                    class="text-danger">*</span></label>
                            <input type="date" name="date_versement" id="dateVersementModal" class="form-control"
                                value="{{ now()->format('Y-m-d') }}" required>
                        </div>

                        <!-- Début de période -->
                        <div class="mb-3">
                            <label for="dateDebutModal" class="form-label">Début de période</label>
                            <input type="date" name="date_debut" id="dateDebutModal" class="form-control"
                                value="{{ $dateDebut->format('Y-m-d') }}">
                        </div>

                        <!-- Fin de période -->
                        <div class="mb-3">
                            <label for="dateFinModal" class="form-label">Fin de période</label>
                            <input type="date" name="date_fin" id="dateFinModal" class="form-control"
                                value="{{ $dateFin->format('Y-m-d') }}">
                        </div>

                        <!-- Mode de versement -->
                        <div class="mb-3">
                            <label for="modeVersementModal" class="form-label">Mode de versement <span
                                    class="text-danger">*</span></label>
                            <select name="mode_versement" id="modeVersementModal" class="form-select" required>
                                <option value="">-- Sélectionner --</option>
                                <option value="virement" selected>Virement</option>
                                <option value="chèque">Chèque</option>
                                <option value="espèces">Espèces</option>
                                <option value="mobile_money">Mobile Money</option>
                                <option value="autre">Autre</option>
                            </select>
                        </div>

                        <!-- Référence -->
                        <div class="mb-3">
                            <label for="referenceModal" class="form-label">Référence</label>
                            <input type="text" name="reference" id="referenceModal" class="form-control"
                                placeholder="N° virement, chèque...">
                        </div>

                        <!-- Statut - Calculé automatiquement -->
                        <input type="hidden" name="statut" value="">

                        <!-- Notes -->
                        <div class="mb-3">
                            <label for="notesModal" class="form-label">Notes</label>
                            <textarea name="notes" id="notesModal" class="form-control" rows="2"
                                placeholder="Notes supplémentaires..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i> Annuler
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Voir Paiements par Bien -->
    <div class="modal fade" id="modalPaiements" tabindex="-1" aria-labelledby="modalPaiementsLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title text-white" id="modalPaiementsLabel">
                        <i class="fas fa-receipt"></i> Paiements
                        <span id="modalPaiementsBien" class="ms-2"></span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-sm" id="tablePaiementsModal">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Client</th>
                                    <th class="text-end">Montant</th>
                                    <th>Méthode</th>
                                    <th>Référence</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Rempli dynamiquement -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Cacher l'en-tête d'impression sur l'écran */
        #print-header {
            display: none !important;
        }

        @media print {
            /* Afficher l'en-tête d'impression */
            #print-header {
                display: block !important;
            }

            /* Cacher les éléments interactifs */
            .btn,
            .btn-group,
            form,
            .modal,
            .no-print {
                display: none !important;
            }

            /* Optimisation de la page */
            @page {
                size: A4;
                margin: 1.5cm 1cm;
            }

            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }

            /* En-tête du document */
            .container-fluid {
                width: 100%;
                max-width: 100%;
                padding: 0;
            }

            /* Header amélioré pour l'impression */
            .row.mb-3.align-items-center {
                border-bottom: 3px solid #0d6efd;
                padding-bottom: 15px;
                margin-bottom: 20px !important;
            }

            .row.mb-3.align-items-center .col-md-4 {
                display: none !important;
            }

            .row.mb-3.align-items-center .col-md-8 {
                width: 100% !important;
                text-align: center;
            }

            .row.mb-3.align-items-center h1.h3 {
                font-size: 24px !important;
                color: #000;
                margin-bottom: 10px !important;
            }

            .row.mb-3.align-items-center h5 {
                font-size: 18px !important;
                color: #333;
            }

            .row.mb-3.align-items-center small {
                font-size: 14px !important;
                color: #666;
            }

            /* Badges */
            .badge {
                border: 1px solid #000;
                background-color: white !important;
                color: #000 !important;
                padding: 3px 8px;
            }

            /* KPI Cards */
            .card {
                page-break-inside: avoid;
                margin-bottom: 15px;
                border: 1px solid #ddd !important;
                box-shadow: none !important;
            }

            .card-body {
                padding: 10px !important;
            }

            .card-header {
                background-color: #f8f9fa !important;
                color: #000 !important;
                border-bottom: 2px solid #000 !important;
                padding: 10px 15px !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .card-header h5 {
                font-size: 16px !important;
                font-weight: bold;
                margin: 0 !important;
            }

            .card-header.bg-info,
            .card-header.bg-success {
                background-color: #e9ecef !important;
            }

            /* Tableaux */
            .table {
                font-size: 11px !important;
                border-collapse: collapse !important;
            }

            .table th,
            .table td {
                padding: 6px 8px !important;
                border: 1px solid #ddd !important;
            }

            .table thead {
                background-color: #e9ecef !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .table thead th {
                font-weight: bold !important;
                border-bottom: 2px solid #000 !important;
            }

            .table-hover tbody tr:hover {
                background-color: transparent !important;
            }

            .table tfoot {
                background-color: #f0f0f0 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                border-top: 2px solid #000 !important;
            }

            .table tfoot td,
            .table tfoot th {
                font-weight: bold !important;
            }

            /* Couleurs de texte pour l'impression */
            .text-primary,
            .text-info {
                color: #000 !important;
            }

            .text-danger {
                color: #333 !important;
            }

            .text-warning {
                color: #555 !important;
            }

            .text-success {
                color: #000 !important;
                font-weight: bold !important;
            }

            /* Lignes de charges */
            .table-light {
                background-color: #f8f9fa !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            /* Section de calcul */
            .table-success {
                background-color: #e8f5e9 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            /* Alert */
            .alert {
                border: 1px solid #ddd !important;
                background-color: #f8f9fa !important;
                color: #000 !important;
                padding: 10px !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            /* Liens */
            a {
                color: #000 !important;
                text-decoration: none !important;
            }

            /* Éviter les coupures dans les sections importantes */
            .card,
            .row.mb-4,
            .table {
                page-break-inside: avoid;
            }

            /* Section des versements - toujours afficher */
            .card.mt-4 {
                margin-top: 20px !important;
                page-break-before: auto;
            }

            /* Ajout d'un pied de page avec la date d'impression */
            .container-fluid::after {
                content: "Document imprimé le {{ now()->format('d/m/Y à H:i') }}";
                display: block;
                text-align: center;
                font-size: 10px;
                color: #666;
                margin-top: 30px;
                padding-top: 10px;
                border-top: 1px solid #ddd;
            }

            /* Masquer les colonnes "Actions" */
            .table th:last-child,
            .table td:last-child {
                display: none !important;
            }

            /* Forcer l'affichage de toutes les sections */
            .row,
            .col-md-3,
            .col-md-4,
            .col-md-8 {
                display: block !important;
                width: 100% !important;
            }

            /* Grid pour KPIs en impression - 2 colonnes */
            .row.mb-3 .col-md-3 {
                width: 49% !important;
                display: inline-block !important;
                margin-right: 1%;
                vertical-align: top;
            }

            .row.mb-3 .col-md-3:nth-child(2n) {
                margin-right: 0;
            }

            /* Icônes en impression */
            .fas,
            .far {
                font-weight: normal;
            }

            /* Optimisation des bordures colorées des KPI */
            .card[style*="border-left"] {
                border-left: 4px solid #000 !important;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const formVersement = document.getElementById('formVersement');
            const modalVersement = document.getElementById('modalVersement');

            // Éléments du formulaire
            const typeTotal = document.getElementById('typeTotal');
            const typePartiel = document.getElementById('typePartiel');
            const montantInput = document.getElementById('montantModal');
            const montantAVerserInput = document.getElementById('montantAVerserModal');
            const montantRestantInput = document.getElementById('montantRestantModal');

            // Gestion du changement de type - Total
            typeTotal.addEventListener('change', function() {
                if (this.checked) {
                    const montantAVerser = parseInt(montantAVerserInput.value) || 0;
                    montantInput.value = montantAVerser;
                    montantInput.disabled = true;
                    montantInput.classList.add('bg-light');
                    montantInput.style.cursor = 'not-allowed';
                    updateMontantRestant();
                }
            });

            // Gestion du changement de type - Partiel
            typePartiel.addEventListener('change', function() {
                if (this.checked) {
                    montantInput.disabled = false;
                    montantInput.classList.remove('bg-light');
                    montantInput.style.cursor = 'auto';
                    montantInput.value = '';
                    updateMontantRestant();
                    setTimeout(() => montantInput.focus(), 100);
                }
            });

            // Gestion de la saisie du montant
            montantInput.addEventListener('input', function() {
                updateMontantRestant();
            });

            // Fonction pour mettre à jour le montant restant
            function updateMontantRestant() {
                const montantAVerser = parseInt(montantAVerserInput.value) || 0;
                const montantSaisi = parseInt(montantInput.value) || 0;
                const montantRestant = montantAVerser - montantSaisi;
                montantRestantInput.value = montantRestant >= 0 ? montantRestant : 0;
            }

            // Réinitialiser le formulaire à l'ouverture du modal
            const modalElement = document.getElementById('modalVersement');
            modalElement.addEventListener('show.bs.modal', function() {
                formVersement.reset();
                typeTotal.checked = false;
                typePartiel.checked = false;
                montantInput.disabled = true;
                montantInput.classList.add('bg-light');
                montantInput.style.cursor = 'not-allowed';
                const montantAVerser = parseInt(montantAVerserInput.value) || 0;
                montantRestantInput.value = montantAVerser;
            });

            // Soumettre le formulaire en AJAX
            formVersement.addEventListener('submit', function(e) {
                e.preventDefault();

                // Vérifier que le type est sélectionné
                if (!typeTotal.checked && !typePartiel.checked) {
                    alert('Veuillez sélectionner un type de versement');
                    return;
                }

                // Vérifier que le montant est saisi
                if (!montantInput.value || parseInt(montantInput.value) <= 0) {
                    alert('Veuillez saisir un montant valide');
                    return;
                }

                // Activer le champ montant temporairement pour la soumission (les champs disabled ne sont pas envoyés)
                montantInput.disabled = false;

                const formData = new FormData(this);
                const modal = bootstrap.Modal.getInstance(modalVersement);

                fetch(formVersement.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Fermer le modal
                            modal.hide();

                            // Afficher un message de succès avec toast
                            const alertDiv = document.createElement('div');
                            alertDiv.className =
                                'alert alert-success alert-dismissible fade show d-flex align-items-center';
                            alertDiv.style.cssText =
                                'position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 350px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);';
                            alertDiv.innerHTML = `
                        <i class="fas fa-check-circle me-3" style="font-size: 24px;"></i>
                        <div>
                            <strong>Succès!</strong>
                            <div>Versement enregistré avec succès</div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `;
                            document.body.appendChild(alertDiv);

                            // Auto-fermer après 4 secondes
                            setTimeout(() => {
                                alertDiv.remove();
                            }, 4000);

                            // Recharger la page après 2 secondes
                            setTimeout(() => {
                                location.reload();
                            }, 2000);
                        } else {
                            alert('Erreur: ' + (data.message || 'Une erreur est survenue'));
                            // Réactiver le disabled si erreur
                            if (typeTotal.checked) {
                                montantInput.disabled = true;
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        alert('Erreur lors de l\'enregistrement');
                        // Réactiver le disabled si erreur
                        if (typeTotal.checked) {
                            montantInput.disabled = true;
                        }
                    });
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Événement pour ouvrir la modal des paiements
            document.querySelectorAll('.btnVoirPaiements').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const paiements = JSON.parse(this.getAttribute('data-paiements') || '[]');
                    const titre = this.getAttribute('data-bien-titre') || '';
                    const modalTitleSpan = document.getElementById('modalPaiementsBien');
                    const tbody = document.querySelector('#tablePaiementsModal tbody');

                    modalTitleSpan.textContent = '- ' + titre;
                    tbody.innerHTML = '';

                    if (paiements.length === 0) {
                        const tr = document.createElement('tr');
                        tr.innerHTML =
                            '<td colspan="5" class="text-center text-muted">Aucun paiement pour cette période</td>';
                        tbody.appendChild(tr);
                    } else {
                        paiements.forEach(function(p) {
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td>${p.date ?? '-'}</td>
                                <td>${(p.type ?? '-').toUpperCase()}</td>
                                <td>${p.client ?? '-'}</td>
                                <td class="text-end">${(p.montant || 0).toLocaleString('fr-FR')} F</td>
                                <td>${p.methode ?? '-'}</td>
                                <td>${p.reference ?? '-'}</td>
                            `;
                            tbody.appendChild(tr);
                        });
                    }

                    const modalEl = document.getElementById('modalPaiements');
                    const modal = new bootstrap.Modal(modalEl);
                    modal.show();
                });
            });
        });
    </script>
@endsection
