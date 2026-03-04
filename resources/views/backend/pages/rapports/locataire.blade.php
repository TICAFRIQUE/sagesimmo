@extends('backend.layouts.master')

@section('title')
    Rapport Locataire - {{ $locataire->username }}
@endsection

@section('css')
    <style>
        .table-success-soft {
            background-color: rgba(40, 167, 69, 0.05) !important;
        }

        #print-header {
            display: none !important;
        }

        .echeance-detail-row {
            display: none;
        }

        .echeance-detail-row.show {
            display: table-row;
        }

        .echeance-detail-cell {
            background-color: #f8f9fa;
            padding: 15px !important;
        }

        .btn-expand {
            cursor: pointer;
            transition: transform 0.2s;
        }

        .btn-expand.rotated {
            transform: rotate(90deg);
        }

        @media print {
            @page {
                size: A4 portrait;
                margin: 10mm;
            }

            body {
                font-size: 11px !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .no-print {
                display: none !important;
            }

            #print-header {
                display: block !important;
            }

            .card {
                border: 1px solid #ddd !important;
                box-shadow: none !important;
                break-inside: avoid;
            }

            .container-fluid {
                padding: 0 !important;
            }

            .badge {
                border: 1px solid #999;
                -webkit-print-color-adjust: exact !important;
            }

            .table-danger {
                background-color: #f8d7da !important;
                -webkit-print-color-adjust: exact !important;
            }

            .table-warning {
                background-color: #fff3cd !important;
                -webkit-print-color-adjust: exact !important;
            }

            .echeance-detail-row {
                display: none !important;
            }
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <!-- En-tête pour l'impression -->
        <div class="d-none" id="print-header" style="display: none;">
            <div style="text-align: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 3px solid #dc3545;">
                <h1 style="font-size: 28px; margin-bottom: 10px; color: #000;">RAPPORT FINANCIER LOCATAIRE</h1>
                <p style="font-size: 16px; margin: 5px 0; color: #333;">
                    <strong>{{ $locataire->username }}</strong>
                    @if ($locataire->telephone)
                        — {{ $locataire->telephone }}
                    @endif
                </p>
                <p style="font-size: 14px; margin: 5px 0; color: #666;">
                    @if ($dateDebut && $dateFin)
                        Période : {{ $dateDebut->format('d/m/Y') }} au {{ $dateFin->format('d/m/Y') }}
                    @else
                        Toutes les échéances
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
                    <i class="fas fa-file-invoice-dollar"></i> Rapport Locataire
                </h1>
                <h5 class="text-muted mb-0">
                    <i class="fas fa-user-circle me-1"></i> {{ $locataire->username }}
                    @if ($locataire->email)
                        <small class="ms-2"><i class="fas fa-envelope fa-xs"></i> {{ $locataire->email }}</small>
                    @endif
                    @if ($locataire->phone)
                        <small class="ms-2"><i class="fas fa-phone fa-xs"></i> {{ $locataire->phone }}</small>
                    @endif
                </h5>
                <small class="text-muted">
                    <i class="fas fa-calendar-alt me-1"></i>
                    @if ($dateDebut && $dateFin)
                        {{ $dateDebut->format('d/m/Y') }} au {{ $dateFin->format('d/m/Y') }}
                    @else
                        Toutes les échéances
                    @endif
                </small>
                <!--commercial info-->
                @if ($locataire->commercial)
                    <div class="mt-2"> <i class="fas fa-user-tie me-1"></i>
                        <span class="text-muted">Commercial(e) : {{ $locataire->commercial->username }}</span>
                    </div>
                @endif
            </div>
            <div class="col-md-5 text-end">
                <a href="{{ route('backend.rapports.locataire') }}" class="btn btn-outline-secondary btn-sm me-2 no-print">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
                <a href="{{ route('backend.rapports.locataire.pdf', array_filter(['locataire_id' => $locataire->id, 'date_debut' => $dateDebut?->format('Y-m-d'), 'date_fin' => $dateFin?->format('Y-m-d')])) }}"
                    class="btn btn-success btn-sm me-2 no-print" title="Télécharger en PDF">
                    <i class="fas fa-file-pdf"></i> Télécharger PDF & Imprimer PDF
                </a>
                {{-- <button type="button" class="btn btn-primary btn-sm no-print" onclick="window.print()" title="Imprimer">
                    <i class="fas fa-print"></i> Imprimer
                </button> --}}
            </div>
        </div>

        <!-- Filtres -->
        <div class="card mb-3 no-print">
            <div class="card-body py-3">
                <form method="GET" action="{{ route('backend.rapports.locataire') }}" class="row g-2 align-items-end">
                    <input type="hidden" name="locataire_id" value="{{ $locataire->id }}">
                    <div class="col-md-3">
                        <label class="form-label mb-1 small">Locataire</label>
                        <select name="locataire_id" class="form-select form-select-sm" disabled>
                            @foreach ($locataires as $loc)
                                <option value="{{ $loc->id }}" @selected($loc->id == $locataire->id)>{{ $loc->username }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label mb-1 small">Date début</label>
                        <input type="date" name="date_debut" class="form-control form-control"
                            value="{{ $dateDebut?->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label mb-1 small">Date fin</label>
                        <input type="date" name="date_fin" class="form-control form-control"
                            value="{{ $dateFin?->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm flex-grow-1 py-2"><i class="fas fa-search"></i>
                            Filtrer</button>
                        <a href="{{ route('backend.rapports.locataire') }}" class="btn btn-outline-secondary btn-sm"><i
                                class="fas fa-redo"></i></a>
                    </div>
                </form>
            </div>
        </div>

        <!-- KPI Cards -->
        <div class="row mb-3">
            <div class="col-md-2 col-sm-4 mb-2">
                <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #0d6efd !important;">
                    <div class="card-body py-2 text-center">
                        <small class="text-muted d-block" style="font-size: 10px;">Total Dû</small>
                        <h6 class="mb-0 fw-bold text-primary">{{ number_format($rapport['total_du'], 0, ',', ' ') }} F</h6>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-sm-4 mb-2">
                <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #28a745 !important;">
                    <div class="card-body py-2 text-center">
                        <small class="text-muted d-block" style="font-size: 10px;">Total Payé</small>
                        <h6 class="mb-0 fw-bold text-success">{{ number_format($rapport['total_paye'], 0, ',', ' ') }} F
                        </h6>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-sm-4 mb-2">
                <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #dc3545 !important;">
                    <div class="card-body py-2 text-center">
                        <small class="text-muted d-block" style="font-size: 10px;">Restant</small>
                        <h6 class="mb-0 fw-bold text-danger">{{ number_format($rapport['total_restant'], 0, ',', ' ') }} F
                        </h6>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-sm-4 mb-2">
                <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #ffc107 !important;">
                    <div class="card-body py-2 text-center">
                        <small class="text-muted d-block" style="font-size: 10px;">En Retard</small>
                        <h6 class="mb-0 fw-bold text-warning">{{ $rapport['nb_en_retard'] }} éch.</h6>
                        <small class="text-danger"
                            style="font-size: 10px;">{{ number_format($rapport['montant_en_retard'], 0, ',', ' ') }}
                            F</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-sm-4 mb-2">
                <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #6f42c1 !important;">
                    <div class="card-body py-2 text-center">
                        <small class="text-muted d-block" style="font-size: 10px;">Impayées</small>
                        <h6 class="mb-0 fw-bold" style="color: #6f42c1;">{{ $rapport['nb_impayees'] }} éch.</h6>
                        <small class="text-danger"
                            style="font-size: 10px;">{{ number_format($rapport['montant_impaye'], 0, ',', ' ') }}
                            F</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-sm-4 mb-2">
                <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #17a2b8 !important;">
                    <div class="card-body py-2 text-center">
                        <small class="text-muted d-block" style="font-size: 10px;">Statut Global</small>
                        <span
                            class="badge bg-{{ $rapport['statut_global']['badge'] }}">{{ $rapport['statut_global']['label'] }}</span>
                        <br><small class="text-muted" style="font-size: 10px;">Taux:
                            {{ $rapport['taux_paiement'] }}%</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Prochaine échéance alert -->
        @if ($rapport['prochaine_echeance'])
            <div class="alert alert-info py-2 mb-3" style="font-size: 13px;">
                <i class="fas fa-calendar-check me-2"></i>
                <strong>Prochaine échéance :</strong>
                {{ $rapport['prochaine_echeance']['date']->format('d/m/Y') }}
                — <strong>{{ number_format($rapport['prochaine_echeance']['montant'], 0, ',', ' ') }} F</strong>
                {{-- — {{ $rapport['prochaine_echeance']['bien'] }} --}}
                @if ($rapport['prochaine_echeance']['jours_restants'] >= 0)
                    <span
                        class="badge bg-{{ $rapport['prochaine_echeance']['jours_restants'] <= 7 ? 'warning text-dark' : 'info' }} ms-2">
                        dans {{ round($rapport['prochaine_echeance']['jours_restants']) }} jour(s)
                    </span>
                @endif
            </div>
        @endif

        <!-- Détail par location  & Echeances -->
        @foreach ($rapport['locations'] as $rapportLocation)
            @php $location = $rapportLocation['location']; @endphp
            <div class="card shadow-sm mb-3">
                <div class="card-header py-2" style="background-color: #e8f4fd;">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h6 class="mb-0">
                                <i class="fas fa-home me-1 text-primary"></i>
                                <a href="{{ route('backend.locations.show', $location) }}">
                                    {{ $rapportLocation['bien']['titre'] }}</a>
                                <small class="text-muted ms-2">{{ $rapportLocation['type_bien'] }}</small>
                            </h6>
                        </div>
                        <div class="col-md-4 text-end" style="font-size: 12px;">
                            <span class="me-2">
                                <strong>Loyer :</strong>
                                {{ number_format($rapportLocation['loyer_mensuel'], 0, ',', ' ') }} F/mois
                            </span>
                            <span class="me-2">
                                <strong>Début :</strong>
                                {{ $rapportLocation['date_debut'] ? $rapportLocation['date_debut']->format('d/m/Y') : '-' }}
                            </span>
                            @if ($rapportLocation['date_fin'])
                                <span class="me-2">
                                    <strong>Fin :</strong> {{ $rapportLocation['date_fin']->format('d/m/Y') }}
                                </span>
                            @endif
                            <span
                                class="badge bg-{{ $rapportLocation['statut_location'] === 'actif' ? 'success' : ($rapportLocation['statut_location'] === 'resilie' ? 'danger' : 'warning') }}">
                                {{ ucfirst($rapportLocation['statut_location']) }}
                            </span>
                        </div>
                        <div class="col-md-2 text-end no-print">
                            <a href="{{ route('backend.locations.show', $location) }}"
                                class="btn btn-outline-primary btn-sm" title="Voir la location">
                                <i class="ri-eye-line"></i> Détails de la location
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body p-2">
                    {{-- Partial réutilisable : gestion des échéances --}}
                    @include(
                        'backend.partials.location.echeances-gestion',
                        [
                            'location' => $location,
                            'suffix' => $location->id,
                        ] + ($dateDebut && $dateFin ? ['dateDebut' => $dateDebut, 'dateFin' => $dateFin] : []))
                </div>
            </div>
        @endforeach

        @if ($rapport['locations']->isEmpty())
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i> Aucune location active trouvée pour ce locataire.
            </div>
        @endif
    </div>
@endsection
