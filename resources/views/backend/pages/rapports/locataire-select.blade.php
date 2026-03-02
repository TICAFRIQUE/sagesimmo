@extends('backend.layouts.master')

@section('title')
    Rapport Locataires - Échéances & Paiements
@endsection

@section('content')
    <div class="container-fluid">
        <!-- En-tête pour l'impression uniquement -->
        <div id="print-header">
            <div style="text-align: center; margin-bottom: 25px; padding-bottom: 15px; border-bottom: 3px solid #dc3545;">
                <h1 style="font-size: 24px; margin-bottom: 8px; color: #000;">RAPPORT LOCATAIRES - ÉCHÉANCES & PAIEMENTS</h1>
                @if (isset($dateDebut) && isset($dateFin))
                    <p style="font-size: 14px; margin: 5px 0; color: #333;">
                        @if($dateDebut && $dateFin)
                            <strong>Période :</strong> {{ $dateDebut->format('d/m/Y') }} au {{ $dateFin->format('d/m/Y') }}
                        @else
                            <strong>Toutes les échéances</strong>
                        @endif
                    </p>
                @endif
                <p style="font-size: 11px; margin: 5px 0; color: #999;">
                    Document généré le {{ now()->format('d/m/Y à H:i') }} — {{ $locataires->count() }} locataire(s)
                </p>
            </div>
        </div>

        <!-- Header -->
        <div class="row mb-3 align-items-center">
            <div class="col-md-8">
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-users"></i> Rapport Locataires
                    @if (isset($dateDebut) && isset($dateFin))
                        @if($dateDebut && $dateFin)
                            <small class="text-danger ms-2">{{ $dateDebut->format('d/m/Y') }} au {{ $dateFin->format('d/m/Y') }}</small>
                        @else
                            <small class="text-muted ms-2">Toutes les échéances</small>
                        @endif
                    @endif
                </h1>
            </div>
            <div class="col-md-4 text-end no-print">
                <a href="{{ route('backend.rapports.locataire.pdf.global', array_filter(['date_debut' => $dateDebut?->format('Y-m-d'), 'date_fin' => $dateFin?->format('Y-m-d')])) }}" class="btn btn-success btn-sm me-2" title="Télécharger en PDF">
                    <i class="fas fa-file-pdf"></i> Télécharger PDF
                </a>
                <button type="button" class="btn btn-primary btn-sm" onclick="window.print()" title="Imprimer le rapport">
                    <i class="fas fa-print"></i> Imprimer
                </button>
            </div>
        </div>

        <!-- KPI Global -->
        @if (isset($kpiGlobal))
            <div class="row mb-3">
                <div class="col-md-2 col-sm-6 mb-2">
                    <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #0d6efd !important;">
                        <div class="card-body py-3 d-flex align-items-center">
                            <div class="me-3 text-primary" style="font-size: 1.8rem; opacity: 0.6;">
                                <i class="fas fa-file-invoice"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block mb-1" style="font-size: 10px;">Total Dû</small>
                                <h6 class="mb-0 fw-bold text-primary">{{ number_format($kpiGlobal['total_du'], 0, ',', ' ') }} F</h6>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-sm-6 mb-2">
                    <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #28a745 !important;">
                        <div class="card-body py-3 d-flex align-items-center">
                            <div class="me-3 text-success" style="font-size: 1.8rem; opacity: 0.6;">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block mb-1" style="font-size: 10px;">Total Payé</small>
                                <h6 class="mb-0 fw-bold text-success">{{ number_format($kpiGlobal['total_paye'], 0, ',', ' ') }} F</h6>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-sm-6 mb-2">
                    <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #ffc107 !important;">
                        <div class="card-body py-3 d-flex align-items-center">
                            <div class="me-3 text-warning" style="font-size: 1.8rem; opacity: 0.6;">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block mb-1" style="font-size: 10px;">Restant</small>
                                <h6 class="mb-0 fw-bold text-warning">{{ number_format($kpiGlobal['total_restant'], 0, ',', ' ') }} F</h6>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-sm-6 mb-2">
                    <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #dc3545 !important;">
                        <div class="card-body py-3 d-flex align-items-center">
                            <div class="me-3 text-danger" style="font-size: 1.8rem; opacity: 0.6;">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block mb-1" style="font-size: 10px;">Montant En Retard</small>
                                <h6 class="mb-0 fw-bold text-danger">{{ number_format($kpiGlobal['total_en_retard'], 0, ',', ' ') }} F</h6>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-sm-6 mb-2">
                    <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #fd7e14 !important;">
                        <div class="card-body py-3 d-flex align-items-center">
                            <div class="me-3" style="font-size: 1.8rem; opacity: 0.6; color: #fd7e14;">
                                <i class="fas fa-alarm-clock fas fa-bell"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block mb-1" style="font-size: 10px;">Éch. En Retard</small>
                                <h6 class="mb-0 fw-bold" style="color: #fd7e14;">{{ $kpiGlobal['nb_en_retard'] }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-sm-6 mb-2">
                    <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #6f42c1 !important;">
                        <div class="card-body py-3 d-flex align-items-center">
                            <div class="me-3" style="font-size: 1.8rem; opacity: 0.6; color: #6f42c1;">
                                <i class="fas fa-ban"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block mb-1" style="font-size: 10px;">Éch. Impayées</small>
                                <h6 class="mb-0 fw-bold" style="color: #6f42c1;">{{ $kpiGlobal['nb_impayees'] }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Filtres -->
        <div class="card mb-3 no-print">
            <div class="card-body py-3">
                <form method="GET" action="{{ route('backend.rapports.locataire') }}" class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label for="locataire_filtre" class="form-label mb-1 small">Locataire</label>
                        <select name="locataire_filtre" id="locataire_filtre" class="form-select form-select-sm">
                            <option value="">-- Tous --</option>
                            @foreach ($allLocataires as $loc)
                                <option value="{{ $loc->id }}" @selected(request('locataire_filtre') == $loc->id)>
                                    {{ $loc->username }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="statut_filtre" class="form-label mb-1 small">Statut</label>
                        <select name="statut_filtre" id="statut_filtre" class="form-select form-select-sm">
                            <option value="">-- Tous --</option>
                            <option value="a_jour" @selected(request('statut_filtre') == 'a_jour')>À jour</option>
                            <option value="en_retard" @selected(request('statut_filtre') == 'en_retard')>En retard</option>
                            <option value="impaye" @selected(request('statut_filtre') == 'impaye')>Impayé</option>
                            <option value="aucun" @selected(request('statut_filtre') == 'aucun')>Aucune échéance</option>
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
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                            <i class="fas fa-search"></i> Filtrer
                        </button>
                        <a href="{{ route('backend.rapports.locataire') }}" class="btn btn-outline-secondary btn-sm" title="Réinitialiser">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tableau des locataires -->
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0" style="font-size: 13px;">
                        <thead class="table-dark">
                            <tr>
                                <th style="width: 30px;">#</th>
                                <th>Locataire</th>
                                <th class="text-center">Locations</th>
                                <th class="text-end">Total Dû</th>
                                <th class="text-end">Total Payé</th>
                                <th class="text-end">Restant</th>
                                <th class="text-center">Taux</th>
                                <th class="text-center">En Retard</th>
                                <th class="text-center">Impayées</th>
                                <th class="text-center">Prochaine Éch.</th>
                                <th class="text-center">Statut</th>
                                <th class="text-center no-print">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($locataires->sortByDesc(fn($l) => ($aperçus[$l->id]['nb_en_retard'] ?? 0) + ($aperçus[$l->id]['nb_impayees'] ?? 0)) as $index => $locataire)
                                @php $apercu = $aperçus[$locataire->id] ?? null; @endphp
                                @if($apercu)
                                    <tr class="{{ ($apercu['statut_global']['code'] ?? '') === 'impaye' ? 'table-danger' : (($apercu['statut_global']['code'] ?? '') === 'en_retard' ? 'table-warning' : '') }}">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <strong>{{ $locataire->username }}</strong>
                                            @if($locataire->email)
                                                <br><small class="text-muted">{{ $locataire->email }}</small>
                                            @endif
                                            @if($locataire->telephone)
                                                <br><small class="text-muted"><i class="fas fa-phone fa-xs"></i> {{ $locataire->telephone }}</small>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary">{{ $apercu['nb_locations'] }}</span>
                                        </td>
                                        <td class="text-end fw-bold">{{ number_format($apercu['total_du'], 0, ',', ' ') }} F</td>
                                        <td class="text-end text-success fw-bold">{{ number_format($apercu['total_paye'], 0, ',', ' ') }} F</td>
                                        <td class="text-end {{ $apercu['total_restant'] > 0 ? 'text-danger fw-bold' : '' }}">
                                            {{ number_format($apercu['total_restant'], 0, ',', ' ') }} F
                                        </td>
                                        <td class="text-center">
                                            @php $taux = $apercu['taux_paiement']; @endphp
                                            <div class="progress" style="height: 18px; min-width: 60px;">
                                                <div class="progress-bar {{ $taux >= 100 ? 'bg-success' : ($taux >= 50 ? 'bg-info' : 'bg-danger') }}"
                                                    role="progressbar" style="width: {{ $taux }}%">
                                                    {{ $taux }}%
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            @if($apercu['nb_en_retard'] > 0)
                                                <span class="badge bg-warning text-dark">{{ $apercu['nb_en_retard'] }}</span>
                                            @else
                                                <span class="text-muted">0</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($apercu['nb_impayees'] > 0)
                                                <span class="badge bg-danger">{{ $apercu['nb_impayees'] }}</span>
                                            @else
                                                <span class="text-muted">0</span>
                                            @endif
                                        </td>
                                        <td class="text-center" style="font-size: 11px;">
                                            @if($apercu['prochaine_echeance'])
                                                {{ $apercu['prochaine_echeance']['date']->format('d/m/Y') }}
                                                <br><small class="text-muted">{{ number_format($apercu['prochaine_echeance']['montant'], 0, ',', ' ') }} F</small>
                                                @if($apercu['prochaine_echeance']['jours_restants'] <= 7 && $apercu['prochaine_echeance']['jours_restants'] >= 0)
                                                    <br><span class="badge bg-warning text-dark" style="font-size: 9px;">dans {{ $apercu['prochaine_echeance']['jours_restants'] }}j</span>
                                                @endif
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-{{ $apercu['statut_global']['badge'] }}">
                                                {{ $apercu['statut_global']['label'] }}
                                            </span>
                                        </td>
                                        <td class="text-center no-print">
                                            <a href="{{ route('backend.rapports.locataire', array_filter(['locataire_id' => $locataire->id, 'date_debut' => $dateDebut?->format('Y-m-d'), 'date_fin' => $dateFin?->format('Y-m-d')])) }}"
                                                class="btn btn-outline-primary btn-sm" title="Voir détail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="12" class="text-center text-muted py-4">
                                        <i class="fas fa-info-circle me-2"></i> Aucun locataire avec des locations actives trouvé.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($locataires->count() > 0)
                            <tfoot class="table-light fw-bold">
                                <tr>
                                    <td colspan="3" class="text-end">TOTAUX :</td>
                                    <td class="text-end">{{ number_format($aperçus->sum('total_du'), 0, ',', ' ') }} F</td>
                                    <td class="text-end text-success">{{ number_format($aperçus->sum('total_paye'), 0, ',', ' ') }} F</td>
                                    <td class="text-end text-danger">{{ number_format($aperçus->sum('total_restant'), 0, ',', ' ') }} F</td>
                                    <td colspan="6"></td>
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
            .table-danger { background-color: #f8d7da !important; -webkit-print-color-adjust: exact !important; }
            .table-warning { background-color: #fff3cd !important; -webkit-print-color-adjust: exact !important; }
        }
    </style>
@endsection
