@extends('backend.layouts.master')

@section('title')
    Rapport Acheteurs - Suivi des Paiements
@endsection

@section('content')
    <div class="container-fluid">
        <!-- En-tête impression -->
        <div id="print-header">
            <div style="text-align: center; margin-bottom: 25px; padding-bottom: 15px; border-bottom: 3px solid #198754;">
                <h1 style="font-size: 24px; margin-bottom: 8px; color: #000;">RAPPORT ACHETEURS - SUIVI DES PAIEMENTS</h1>
                @if ($dateDebut && $dateFin)
                    <p style="font-size: 14px; margin: 5px 0; color: #333;">
                        <strong>Période :</strong> {{ $dateDebut->format('d/m/Y') }} au {{ $dateFin->format('d/m/Y') }}
                    </p>
                @else
                    <p style="font-size: 14px; margin: 5px 0; color: #333;">
                        <strong>Toutes les périodes</strong>
                    </p>
                @endif
                <p style="font-size: 11px; margin: 5px 0; color: #999;">
                    Document généré le {{ now()->format('d/m/Y à H:i') }} — {{ $acheteurs->count() }} acheteur(s)
                </p>
            </div>
        </div>

        <!-- Header -->
        <div class="row mb-3 align-items-center">
            <div class="col-md-8">
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-shopping-cart"></i> Rapport Acheteurs
                    @if ($dateDebut && $dateFin)
                        <small class="text-success ms-2">{{ $dateDebut->format('d/m/Y') }} au {{ $dateFin->format('d/m/Y') }}</small>
                    @else
                        <small class="text-muted ms-2">Toutes les périodes</small>
                    @endif
                </h1>
            </div>
            <div class="col-md-4 text-end no-print">
                <a href="{{ route('backend.rapports.acheteur.pdf.global', ['date_debut' => $dateDebut?->format('Y-m-d'), 'date_fin' => $dateFin?->format('Y-m-d')]) }}" class="btn btn-success btn-sm me-2" title="Télécharger en PDF">
                    <i class="fas fa-file-pdf"></i> Télécharger PDF
                </a>
                <button type="button" class="btn btn-primary btn-sm" onclick="window.print()" title="Imprimer">
                    <i class="fas fa-print"></i> Imprimer
                </button>
            </div>
        </div>

        <!-- KPI Global -->
        @if (isset($kpiGlobal))
            <div class="row mb-3">
                <div class="col-md-3 col-sm-6 mb-2">
                    <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #0d6efd !important;">
                        <div class="card-body py-3 d-flex align-items-center">
                            <div class="me-3 text-primary" style="font-size: 2rem; opacity: 0.6;">
                                <i class="fas fa-file-invoice-dollar"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block mb-1" style="font-size: 11px;">Total À Payer</small>
                                <h5 class="mb-0 fw-bold text-primary">{{ number_format($kpiGlobal['total_a_payer'], 0, ',', ' ') }} F</h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-2">
                    <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #28a745 !important;">
                        <div class="card-body py-3 d-flex align-items-center">
                            <div class="me-3 text-success" style="font-size: 2rem; opacity: 0.6;">
                                <i class="fas fa-check-double"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block mb-1" style="font-size: 11px;">Total Payé</small>
                                <h5 class="mb-0 fw-bold text-success">{{ number_format($kpiGlobal['total_paye'], 0, ',', ' ') }} F</h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-2">
                    <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #dc3545 !important;">
                        <div class="card-body py-3 d-flex align-items-center">
                            <div class="me-3 text-danger" style="font-size: 2rem; opacity: 0.6;">
                                <i class="fas fa-exclamation-circle"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block mb-1" style="font-size: 11px;">Reste À Payer</small>
                                <h5 class="mb-0 fw-bold text-danger">{{ number_format($kpiGlobal['total_restant'], 0, ',', ' ') }} F</h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-2">
                    <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #17a2b8 !important;">
                        <div class="card-body py-3 d-flex align-items-center">
                            <div class="me-3 text-info" style="font-size: 2rem; opacity: 0.6;">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block mb-1" style="font-size: 11px;">{{ ($dateDebut && $dateFin) ? 'Payé sur Période' : 'Total Payé' }}</small>
                                <h5 class="mb-0 fw-bold text-info">{{ number_format($kpiGlobal['total_paye_periode'], 0, ',', ' ') }} F</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Filtres -->
        <div class="card mb-3 no-print">
            <div class="card-body py-3">
                <form method="GET" action="{{ route('backend.rapports.acheteur') }}" class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label for="acheteur_filtre" class="form-label mb-1 small">Acheteur</label>
                        <select name="acheteur_filtre" id="acheteur_filtre" class="form-select form-select-sm">
                            <option value="">-- Tous --</option>
                            @foreach ($allAcheteurs as $ach)
                                <option value="{{ $ach->id }}" @selected(request('acheteur_filtre') == $ach->id)>
                                    {{ $ach->username }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="statut_filtre" class="form-label mb-1 small">Statut</label>
                        <select name="statut_filtre" id="statut_filtre" class="form-select form-select-sm">
                            <option value="">-- Tous --</option>
                            <option value="paye" @selected(request('statut_filtre') == 'paye')>Tout payé</option>
                            <option value="en_cours" @selected(request('statut_filtre') == 'en_cours')>Paiement en cours</option>
                            <option value="en_attente" @selected(request('statut_filtre') == 'en_attente')>En attente</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="date_debut" class="form-label mb-1 small">Date début</label>
                        <input type="date" name="date_debut" id="date_debut" class="form-control form-control-sm"
                            value="{{ $dateDebut?->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-2">
                        <label for="date_fin" class="form-label mb-1 small">Date fin</label>
                        <input type="date" name="date_fin" id="date_fin" class="form-control form-control-sm"
                            value="{{ $dateFin?->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                            <i class="fas fa-search"></i> Filtrer
                        </button>
                        <a href="{{ route('backend.rapports.acheteur') }}" class="btn btn-outline-secondary btn-sm" title="Réinitialiser">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tableau des acheteurs -->
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0" style="font-size: 13px;">
                        <thead style="background-color: #198754; color: #fff;">
                            <tr>
                                <th style="width: 30px;">#</th>
                                <th>Acheteur</th>
                                <th class="text-center">Ventes</th>
                                <th class="text-end">Prix Total</th>
                                <th class="text-end">Total Payé</th>
                                <th class="text-end">Reste</th>
                                {{-- <th class="text-center">Progression</th> --}}
                                @if($dateDebut && $dateFin)
                                    <th class="text-end">Payé (Période)</th>
                                @endif
                                <th class="text-center">Statut</th>
                                <th class="text-center no-print">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($acheteurs->sortByDesc(fn($a) => $aperçus[$a->id]['total_restant'] ?? 0) as $acheteur)
                                @php $apercu = $aperçus[$acheteur->id] ?? null; @endphp
                                @if($apercu)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <strong>{{ $acheteur->username }}</strong>
                                            @if($acheteur->email)
                                                <br><small class="text-muted">{{ $acheteur->email }}</small>
                                            @endif
                                            @if($acheteur->telephone)
                                                <br><small class="text-muted"><i class="fas fa-phone fa-xs"></i> {{ $acheteur->telephone }}</small>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary">{{ $apercu['nb_ventes'] }}</span>
                                        </td>
                                        <td class="text-end fw-bold">{{ number_format($apercu['total_a_payer'], 0, ',', ' ') }} F</td>
                                        <td class="text-end text-success fw-bold">{{ number_format($apercu['total_paye'], 0, ',', ' ') }} F</td>
                                        <td class="text-end {{ $apercu['total_restant'] > 0 ? 'text-danger fw-bold' : '' }}">
                                            {{ number_format($apercu['total_restant'], 0, ',', ' ') }} F
                                        </td>
                                        {{-- <td class="text-center">
                                            @php $taux = $apercu['taux_paiement']; @endphp
                                            <div class="progress" style="height: 18px; min-width: 60px;">
                                                <div class="progress-bar {{ $taux >= 100 ? 'bg-success' : ($taux >= 50 ? 'bg-info' : ($taux > 0 ? 'bg-warning' : 'bg-danger')) }}"
                                                    role="progressbar" style="width: {{ max($taux, 5) }}%">
                                                    {{ $taux }}%
                                                </div>
                                            </div>
                                        </td> --}}
                                        @if($dateDebut && $dateFin)
                                            <td class="text-end">{{ number_format($apercu['total_paye_periode'], 0, ',', ' ') }} F</td>
                                        @endif
                                        <td class="text-center">
                                            <span class="badge bg-{{ $apercu['statut_global']['badge'] }}">
                                                {{ $apercu['statut_global']['label'] }}
                                            </span>
                                        </td>
                                        <td class="text-center no-print">
                                            <a href="{{ route('backend.rapports.acheteur', ['acheteur_id' => $acheteur->id, 'date_debut' => $dateDebut?->format('Y-m-d'), 'date_fin' => $dateFin?->format('Y-m-d')]) }}"
                                                class="btn btn-outline-success btn-sm" title="Voir détail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center text-muted py-4">
                                        <i class="fas fa-info-circle me-2"></i> Aucun acheteur avec des ventes actives trouvé.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($acheteurs->count() > 0)
                            <tfoot class="table-light fw-bold">
                                <tr>
                                    <td colspan="3" class="text-end">TOTAUX :</td>
                                    <td class="text-end">{{ number_format($aperçus->sum('total_a_payer'), 0, ',', ' ') }} F</td>
                                    <td class="text-end text-success">{{ number_format($aperçus->sum('total_paye'), 0, ',', ' ') }} F</td>
                                    <td class="text-end text-danger">{{ number_format($aperçus->sum('total_restant'), 0, ',', ' ') }} F</td>
                                    <td colspan="{{ ($dateDebut && $dateFin) ? 4 : 3 }}"></td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>

    <style>
        #print-header { display: none !important; }

        @media print {
            @page { size: A4 landscape; margin: 10mm; }
            body { font-size: 11px !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            .no-print { display: none !important; }
            #print-header { display: block !important; }
            .card { border: none !important; box-shadow: none !important; }
            .container-fluid { padding: 0 !important; }
            .progress { border: 1px solid #ccc; }
            .progress-bar { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            .badge { border: 1px solid #999; -webkit-print-color-adjust: exact !important; }
        }
    </style>
@endsection
