@extends('backend.layouts.master')

@section('title')
    Rapport Financier Propriétaires
@endsection

@section('content')
    <div class="container-fluid">
        <!-- En-tête pour l'impression uniquement -->
        <div id="print-header">
            <div style="text-align: center; margin-bottom: 25px; padding-bottom: 15px; border-bottom: 3px solid #0d6efd;">
                <h1 style="font-size: 24px; margin-bottom: 8px; color: #000;">RAPPORT FINANCIER - TOUS LES PROPRIÉTAIRES</h1>
                @if (isset($dateDebut) && isset($dateFin))
                    <p style="font-size: 14px; margin: 5px 0; color: #333;">
                        <strong>Période :</strong> {{ $dateDebut->format('d/m/Y') }} au {{ $dateFin->format('d/m/Y') }}
                    </p>
                @endif
                <p style="font-size: 11px; margin: 5px 0; color: #999;">
                    Document généré le {{ now()->format('d/m/Y à H:i') }} — {{ $proprietaires->count() }} propriétaire(s)
                </p>
            </div>
        </div>

        <!-- Header avec période -->
        <div class="row mb-3 align-items-center">
            <div class="col-md-8">
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-file-invoice-dollar"></i> Rapport Financier Propriétaires
                    @if (isset($dateDebut) && isset($dateFin))
                        <small class="text-primary ms-2">{{ $dateDebut->format('d/m/Y') }} au {{ $dateFin->format('d/m/Y') }}</small>
                    @endif
                </h1>
            </div>
            <div class="col-md-4 text-end no-print">
                <a href="{{ route('backend.rapports.proprietaire.pdf.global', ['date_debut' => $dateDebut->format('Y-m-d'), 'date_fin' => $dateFin->format('Y-m-d')]) }}" class="btn btn-success btn-sm me-2" title="Télécharger en PDF">
                    <i class="fas fa-file-pdf"></i> Télécharger PDF
                </a>
                <button type="button" class="btn btn-primary btn-sm" onclick="window.print()" title="Imprimer le rapport global">
                    <i class="fas fa-print"></i> Imprimer
                </button>
            </div>
        </div>

        <!-- KPI Global -->
        @if (isset($kpiGlobal))
            <div class="row mb-3">
                <div class="col-md-3 col-sm-6 mb-2">
                    <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #ffc107 !important;">
                        <div class="card-body py-3 d-flex align-items-center">
                            <div class="me-3 text-warning" style="font-size: 2rem; opacity: 0.6;">
                                <i class="fas fa-hand-holding-usd"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block mb-1" style="font-size: 11px;">Versements Disponibles</small>
                                <h5 class="mb-0 fw-bold text-warning">{{ number_format($kpiGlobal['versements_disponibles'], 0, ',', ' ') }} F</h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-2">
                    <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #17a2b8 !important;">
                        <div class="card-body py-3 d-flex align-items-center">
                            <div class="me-3 text-info" style="font-size: 2rem; opacity: 0.6;">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block mb-1" style="font-size: 11px;">Versements Partiels</small>
                                <h5 class="mb-0 fw-bold text-info">{{ number_format($kpiGlobal['versements_partiels'], 0, ',', ' ') }} F</h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-2">
                    <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #28a745 !important;">
                        <div class="card-body py-3 d-flex align-items-center">
                            <div class="me-3 text-success" style="font-size: 2rem; opacity: 0.6;">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block mb-1" style="font-size: 11px;">Versements Effectués</small>
                                <h5 class="mb-0 fw-bold text-success">{{ number_format($kpiGlobal['versements_effectues'], 0, ',', ' ') }} F</h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-2">
                    <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #0d6efd !important;">
                        <div class="card-body py-3 d-flex align-items-center">
                            <div class="me-3 text-primary" style="font-size: 2rem; opacity: 0.6;">
                                <i class="fas fa-percentage"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block mb-1" style="font-size: 11px;">Commission Perçue</small>
                                <h5 class="mb-0 fw-bold text-primary">{{ number_format($kpiGlobal['total_commission'], 0, ',', ' ') }} F</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Filtres compacts sur une ligne -->
        <div class="card mb-3 no-print">
            <div class="card-body py-3">
                <form method="GET" action="{{ route('backend.rapports.proprietaire') }}" class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label for="proprietaire_filtre" class="form-label mb-1 small">Propriétaire</label>
                        <select name="proprietaire_filtre" id="proprietaire_filtre" class="form-select form-select-sm">
                            <option value="">-- Tous --</option>
                            @foreach ($allProprietaires as $prop)
                                <option value="{{ $prop->id }}" @selected(request('proprietaire_filtre') == $prop->id)>
                                    {{ $prop->username }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="statut_filtre" class="form-label mb-1 small">Statut Versement</label>
                        <select name="statut_filtre" id="statut_filtre" class="form-select form-select-sm">
                            <option value="">-- Tous --</option>
                            <option value="en_attente" @selected(request('statut_filtre') == 'en_attente')>Disponible</option>
                            <option value="partiel" @selected(request('statut_filtre') == 'partiel')>Partiel</option>
                            <option value="effectue" @selected(request('statut_filtre') == 'effectue')>Effectué</option>
                            <option value="aucun" @selected(request('statut_filtre') == 'aucun')>Aucun</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="date_debut" class="form-label mb-1 small">Date début</label>
                        <input type="date" name="date_debut" id="date_debut" class="form-control form-control-sm"
                            value="{{ isset($dateDebut) ? $dateDebut->format('Y-m-d') : now()->startOfMonth()->format('Y-m-d') }}">
                    </div>

                    <div class="col-md-2">
                        <label for="date_fin" class="form-label mb-1 small">Date fin</label>
                        <input type="date" name="date_fin" id="date_fin" class="form-control form-control-sm"
                            value="{{ isset($dateFin) ? $dateFin->format('Y-m-d') : now()->endOfMonth()->format('Y-m-d') }}">
                    </div>

                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                            <i class="fas fa-search"></i> Filtrer
                        </button>
                        <a href="{{ route('backend.rapports.proprietaire') }}" class="btn btn-outline-secondary btn-sm" title="Réinitialiser">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Liste des propriétaires en tableau -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-users me-2"></i>Liste des Propriétaires
                </h5>
                <span class="badge bg-dark">{{ $proprietaires->count() }} propriétaire(s)</span>
            </div>
            <div class="card-body p-0">
                @if ($proprietaires->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th style="width: 40px;"></th>
                                    <th>Propriétaire</th>
                                    <th>Email / Téléphone</th>
                                    <th class="text-center">Biens</th>
                                    <th class="text-end">Total Encaissé</th>
                                    <th class="text-end">Commission</th>
                                    <th class="text-end">Charges</th>
                                    <th class="text-end">Net (À Verser)</th>
                                    <th class="text-end">Déjà Versé</th>
                                    <th class="text-end">Reste</th>
                                    <th class="text-center">Statut</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    // Trier : propriétaires agence en premier
                                    $proprietairesTries = $proprietaires->sortByDesc(function($p) {
                                        return $p->type_proprietaire === 'agence' ? 1 : 0;
                                    });
                                @endphp
                                @foreach ($proprietairesTries as $proprietaire)
                                    @php
                                        $rapport = $aperçus[$proprietaire->id] ?? null;
                                        $isAgence = $proprietaire->type_proprietaire === 'agence';
                                    @endphp
                                    <tr class="{{ $isAgence ? 'table-primary' : '' }}">
                                        <td class="text-center">
                                            @if ($proprietaire->hasMedia('avatar'))
                                                <img src="{{ $proprietaire->getFirstMediaUrl('avatar') }}"
                                                    alt="{{ $proprietaire->username }}" class="rounded-circle"
                                                    width="32" height="32">
                                            @else
                                                <div class="rounded-circle {{ $isAgence ? 'bg-primary' : 'bg-secondary' }} text-white d-flex align-items-center justify-content-center mx-auto"
                                                    style="width: 32px; height: 32px; font-size: 0.75rem;">
                                                    <i class="fas {{ $isAgence ? 'fa-building' : 'fa-user' }}"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $proprietaire->username }}</strong>
                                            @if ($isAgence)
                                                <span class="badge bg-primary ms-1" style="font-size: 10px;">AGENCE</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small class="d-block">{{ $proprietaire->email }}</small>
                                            @if ($proprietaire->phone)
                                                <small class="text-muted">{{ $proprietaire->phone }}</small>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-info">{{ $rapport['nombre_biens'] ?? 0 }}</span>
                                        </td>
                                        <td class="text-end">
                                            <strong>{{ number_format($rapport['total_brut_encaisse'] ?? 0, 0, ',', ' ') }} F</strong>
                                        </td>
                                        <td class="text-end text-primary">
                                            {{ number_format($rapport['total_commission_agence'] ?? 0, 0, ',', ' ') }} F
                                        </td>
                                        <td class="text-end text-warning">
                                            {{ number_format($rapport['total_charges'] ?? 0, 0, ',', ' ') }} F
                                        </td>
                                        <td class="text-end fw-bold text-success">
                                            {{ number_format($rapport['revenue_net'] ?? 0, 0, ',', ' ') }} F
                                        </td>
                                        <td class="text-end text-info">
                                            {{ number_format($rapport['montant_total_verse'] ?? 0, 0, ',', ' ') }} F
                                        </td>
                                        <td class="text-end fw-bold {{ ($rapport['reste_a_verser'] ?? 0) > 0 ? 'text-danger' : 'text-muted' }}">
                                            {{ number_format($rapport['reste_a_verser'] ?? 0, 0, ',', ' ') }} F
                                        </td>
                                        <td class="text-center">
                                            @if ($rapport)
                                                <span class="badge bg-{{ $rapport['statut_versement']['badge'] }}">
                                                    {{ $rapport['statut_versement']['label'] }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('backend.rapports.proprietaire', ['proprietaire_id' => $proprietaire->id]) }}"
                                                class="btn btn-sm btn-primary" title="Voir le détail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info m-3">
                        <i class="fas fa-info-circle me-2"></i>
                        Aucun propriétaire trouvé.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        /* Cacher l'en-tête d'impression à l'écran */
        #print-header {
            display: none !important;
        }

        @media print {
            /* Afficher l'en-tête d'impression */
            #print-header {
                display: block !important;
            }

            /* Cacher éléments interactifs */
            .no-print,
            .btn,
            .btn-group,
            form,
            .modal {
                display: none !important;
            }

            /* Format de page */
            @page {
                size: A4 landscape;
                margin: 1cm;
            }

            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
                font-size: 11px;
            }

            .container-fluid {
                width: 100%;
                max-width: 100%;
                padding: 0;
            }

            /* Masquer le header écran */
            .row.mb-3.align-items-center .col-md-4 {
                display: none !important;
            }

            /* KPI Cards - en ligne sur 4 colonnes */
            .row.mb-3 .col-md-3 {
                width: 24% !important;
                display: inline-block !important;
                margin-right: 1%;
                vertical-align: top;
            }

            .card {
                page-break-inside: avoid;
                border: 1px solid #ccc !important;
                box-shadow: none !important;
                margin-bottom: 10px;
            }

            .card-body {
                padding: 8px !important;
            }

            .card-header {
                background-color: #e9ecef !important;
                color: #000 !important;
                border-bottom: 2px solid #000 !important;
                padding: 8px 12px !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .card-header.d-flex {
                display: flex !important;
            }

            .card-header h5 {
                font-size: 14px !important;
            }

            /* KPI styles */
            .card[style*="border-left"] {
                border-left: 4px solid #666 !important;
            }

            .card-body h5.fw-bold {
                font-size: 14px !important;
                color: #000 !important;
            }

            /* Tableau */
            .table {
                font-size: 10px !important;
                border-collapse: collapse !important;
            }

            .table th,
            .table td {
                padding: 4px 6px !important;
                border: 1px solid #ccc !important;
            }

            .table thead.table-dark {
                background-color: #333 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .table thead.table-dark th {
                color: #fff !important;
                background-color: #333 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                border-bottom: 2px solid #000 !important;
            }

            /* Masquer colonne Action */
            .table th:last-child,
            .table td:last-child {
                display: none !important;
            }

            /* Ligne agence */
            .table-primary {
                background-color: #e3f0ff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            /* Badges */
            .badge {
                border: 1px solid #666;
                padding: 2px 6px;
                font-size: 9px !important;
            }

            .badge.bg-dark {
                background-color: #333 !important;
                color: #fff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            /* Avatars - simplifier */
            .rounded-circle {
                width: 20px !important;
                height: 20px !important;
                font-size: 9px !important;
            }

            img.rounded-circle {
                width: 20px !important;
                height: 20px !important;
            }

            /* Couleurs texte pour impression */
            .text-success { color: #000 !important; font-weight: bold !important; }
            .text-danger { color: #333 !important; font-weight: bold !important; }
            .text-warning { color: #555 !important; }
            .text-info { color: #444 !important; }
            .text-primary { color: #222 !important; }

            /* Liens */
            a {
                color: #000 !important;
                text-decoration: none !important;
            }

            /* Pied de page */
            .container-fluid::after {
                content: "";
                display: block;
                margin-top: 20px;
                padding-top: 8px;
                border-top: 1px solid #ccc;
                text-align: center;
                font-size: 9px;
                color: #999;
            }
        }
    </style>
@endsection
