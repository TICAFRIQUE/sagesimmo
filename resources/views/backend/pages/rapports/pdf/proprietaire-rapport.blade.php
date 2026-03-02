<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport Financier - {{ $proprietaire->username }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #333; line-height: 1.4; }

        .header { text-align: center; margin-bottom: 25px; padding-bottom: 15px; border-bottom: 3px solid #0d6efd; }
        .header h1 { font-size: 22px; margin-bottom: 5px; color: #000; }
        .header p { margin: 3px 0; color: #555; }
        .header .periode { font-size: 13px; font-weight: bold; }
        .header .date-gen { font-size: 9px; color: #999; }
        .badge-agence { border: 1px solid #0d6efd; background: #e7f1ff; padding: 2px 8px; font-size: 10px; color: #0d6efd; }

        .kpi-row { width: 100%; margin-bottom: 20px; }
        .kpi-row table { width: 100%; border-collapse: collapse; }
        .kpi-box { width: 25%; text-align: center; padding: 10px 5px; }
        .kpi-label { font-size: 9px; text-transform: uppercase; color: #666; margin-bottom: 3px; }
        .kpi-value { font-size: 16px; font-weight: bold; }
        .kpi-blue { color: #0d6efd; }
        .kpi-red { color: #dc3545; }
        .kpi-orange { color: #e6a800; }
        .kpi-green { color: #28a745; }

        .section { margin-bottom: 20px; }
        .section-title { font-size: 14px; font-weight: bold; padding: 8px 12px; margin-bottom: 0; }
        .section-title-location { background: #17a2b8; color: #fff; }
        .section-title-vente { background: #28a745; color: #fff; }
        .section-title-calcul { background: #e9ecef; color: #333; border-bottom: 2px solid #333; }
        .section-title-versement { background: #6c757d; color: #fff; }

        table.data { width: 100%; border-collapse: collapse; margin-bottom: 5px; }
        table.data th, table.data td { padding: 5px 8px; border: 1px solid #ddd; font-size: 10px; }
        table.data thead th { background: #f0f0f0; font-weight: bold; text-align: left; border-bottom: 2px solid #999; }
        table.data .text-end { text-align: right; }
        table.data .text-center { text-align: center; }
        table.data .fw-bold { font-weight: bold; }
        table.data tfoot td { background: #f8f9fa; font-weight: bold; border-top: 2px solid #333; }

        .charges-row td { background: #fafafa; font-size: 9px; color: #666; padding: 3px 8px; }
        .charge-badge { background: #fff3cd; color: #856404; padding: 1px 5px; border-radius: 3px; font-size: 9px; margin-right: 3px; }

        .calcul-table { width: 65%; }
        .calcul-table td { padding: 6px 10px; font-size: 11px; }
        .calcul-total { background: #d4edda !important; }
        .calcul-total td { font-size: 13px; font-weight: bold; }

        .info-box { float: right; width: 30%; background: #e8f4fd; border: 1px solid #bee5eb; padding: 10px; font-size: 10px; margin-top: -10px; }
        .info-box strong { display: block; margin-bottom: 3px; }
        .info-box hr { border: none; border-top: 1px solid #bee5eb; margin: 5px 0; }

        .versement-kpi { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .versement-kpi td { width: 25%; text-align: center; padding: 8px; border: 1px solid #ddd; }
        .versement-kpi .v-label { font-size: 9px; text-transform: uppercase; color: #666; }
        .versement-kpi .v-value { font-size: 14px; font-weight: bold; }

        .badge { display: inline-block; padding: 2px 6px; border-radius: 3px; font-size: 9px; font-weight: bold; }
        .badge-success { background: #d4edda; color: #155724; }
        .badge-warning { background: #fff3cd; color: #856404; }
        .badge-danger { background: #f8d7da; color: #721c24; }
        .badge-secondary { background: #e2e3e5; color: #383d41; }
        .badge-info { background: #d1ecf1; color: #0c5460; }

        .footer { text-align: center; font-size: 9px; color: #999; margin-top: 30px; padding-top: 10px; border-top: 1px solid #ddd; }

        .clearfix::after { content: ""; display: table; clear: both; }
        .page-break { page-break-before: always; }
    </style>
</head>
<body>
    <!-- En-tête -->
    <div class="header">
        <h1>RAPPORT FINANCIER PROPRIÉTAIRE</h1>
        <p class="periode">
            {{ $proprietaire->username }}
            @if($proprietaire->type_proprietaire === 'agence')
                <span class="badge-agence">AGENCE</span>
            @endif
        </p>
        <p>Période : {{ $dateDebut->format('d/m/Y') }} au {{ $dateFin->format('d/m/Y') }}</p>
        <p class="date-gen">Document généré le {{ now()->format('d/m/Y à H:i') }}</p>
    </div>

    <!-- KPIs -->
    <div class="kpi-row">
        <table>
            <tr>
                <td class="kpi-box">
                    <div class="kpi-label">Total Encaissé</div>
                    <div class="kpi-value kpi-blue">{{ number_format($rapport['total_brut_encaisse'], 0, ',', ' ') }} F</div>
                </td>
                <td class="kpi-box">
                    <div class="kpi-label">Commission Agence</div>
                    <div class="kpi-value kpi-red">{{ number_format($rapport['total_commission_agence'], 0, ',', ' ') }} F</div>
                </td>
                <td class="kpi-box">
                    <div class="kpi-label">Charges</div>
                    <div class="kpi-value kpi-orange">{{ number_format($rapport['total_charges'], 0, ',', ' ') }} F</div>
                </td>
                <td class="kpi-box">
                    <div class="kpi-label">Revenu Net</div>
                    <div class="kpi-value kpi-green">{{ number_format($rapport['revenue_net'], 0, ',', ' ') }} F</div>
                </td>
            </tr>
        </table>
    </div>

    @php
        $biensLocation = collect($rapport['biens'])->filter(fn($b) => $b['type_transaction'] === 'location' || $b['encaissement_loyers']['total'] > 0);
        $biensVente = collect($rapport['biens'])->filter(fn($b) => $b['type_transaction'] === 'vente' || $b['encaissement_ventes']['total'] > 0);
    @endphp

    <!-- Biens en Location -->
    @if($biensLocation->count() > 0)
        <div class="section">
            <div class="section-title section-title-location">
                Biens en Location ({{ $biensLocation->count() }} bien(s))
            </div>
            <table class="data">
                <thead>
                    <tr>
                        <th>Bien</th>
                        <th class="text-end">Loyers Encaissés</th>
                        <th class="text-end">Commission</th>
                        <th class="text-end">Charges</th>
                        <th class="text-end">Revenu Net</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($biensLocation as $bien)
                        <tr>
                            <td>
                                <strong>{{ $bien['bien']->titre ?? 'N/A' }}</strong><br>
                                <span style="font-size: 9px; color: #888;">{{ $bien['type_bien'] }} — {{ $bien['adresse'] }}</span>
                            </td>
                            <td class="text-end">
                                <strong>{{ number_format($bien['encaissement_loyers']['total'], 0, ',', ' ') }} F</strong><br>
                                <span style="font-size: 9px; color: #888;">{{ $bien['encaissement_loyers']['nombre'] }} paiement(s)</span>
                            </td>
                            <td class="text-end" style="color: #dc3545;">{{ number_format($bien['total_commission_agence'], 0, ',', ' ') }} F</td>
                            <td class="text-end" style="color: #e6a800;">{{ number_format($bien['total_charges'], 0, ',', ' ') }} F</td>
                            <td class="text-end"><strong style="color: #28a745;">{{ number_format($bien['revenue_net'], 0, ',', ' ') }} F</strong></td>
                        </tr>
                        @if($bien['charges']->isNotEmpty())
                            <tr class="charges-row">
                                <td colspan="5">
                                    <strong>Charges :</strong>
                                    @foreach($bien['charges'] as $charge)
                                        <span class="charge-badge">{{ $charge->type_charge_libelle }}: {{ number_format($charge->montant, 0, ',', ' ') }} F</span>
                                    @endforeach
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td><strong>TOTAL LOCATIONS</strong></td>
                        <td class="text-end">{{ number_format($biensLocation->sum('encaissement_loyers.total'), 0, ',', ' ') }} F</td>
                        <td class="text-end">{{ number_format($biensLocation->sum('total_commission_agence'), 0, ',', ' ') }} F</td>
                        <td class="text-end">{{ number_format($biensLocation->sum('total_charges'), 0, ',', ' ') }} F</td>
                        <td class="text-end">{{ number_format($biensLocation->sum('revenue_net'), 0, ',', ' ') }} F</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @endif

    <!-- Biens en Vente -->
    @if($biensVente->count() > 0)
        <div class="section">
            <div class="section-title section-title-vente">
                Biens en Vente ({{ $biensVente->count() }} bien(s))
            </div>
            <table class="data">
                <thead>
                    <tr>
                        <th>Bien</th>
                        <th class="text-end">Prix de Vente</th>
                        <th class="text-end">Commission</th>
                        <th class="text-end">Charges</th>
                        <th class="text-end">Revenu Net</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($biensVente as $bien)
                        <tr>
                            <td>
                                <strong>{{ $bien['bien']->titre ?? 'N/A' }}</strong><br>
                                <span style="font-size: 9px; color: #888;">{{ $bien['type_bien'] }} — {{ $bien['adresse'] }}</span>
                            </td>
                            <td class="text-end">
                                <strong>{{ number_format($bien['encaissement_ventes']['total'], 0, ',', ' ') }} F</strong><br>
                                <span style="font-size: 9px; color: #888;">{{ $bien['encaissement_ventes']['nombre'] }} paiement(s)</span>
                            </td>
                            <td class="text-end" style="color: #dc3545;">{{ number_format($bien['total_commission_agence'], 0, ',', ' ') }} F</td>
                            <td class="text-end" style="color: #e6a800;">{{ number_format($bien['total_charges'], 0, ',', ' ') }} F</td>
                            <td class="text-end"><strong style="color: #28a745;">{{ number_format($bien['revenue_net'], 0, ',', ' ') }} F</strong></td>
                        </tr>
                        @if($bien['charges']->isNotEmpty())
                            <tr class="charges-row">
                                <td colspan="5">
                                    <strong>Charges :</strong>
                                    @foreach($bien['charges'] as $charge)
                                        <span class="charge-badge">{{ $charge->type_charge_libelle }}: {{ number_format($charge->montant, 0, ',', ' ') }} F</span>
                                    @endforeach
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td><strong>TOTAL VENTES</strong></td>
                        <td class="text-end">{{ number_format($biensVente->sum('encaissement_ventes.total'), 0, ',', ' ') }} F</td>
                        <td class="text-end">{{ number_format($biensVente->sum('total_commission_agence'), 0, ',', ' ') }} F</td>
                        <td class="text-end">{{ number_format($biensVente->sum('total_charges'), 0, ',', ' ') }} F</td>
                        <td class="text-end">{{ number_format($biensVente->sum('revenue_net'), 0, ',', ' ') }} F</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @endif

    @if($biensLocation->count() === 0 && $biensVente->count() === 0)
        <div style="text-align: center; padding: 20px; color: #666; background: #f8f9fa; border: 1px solid #ddd;">
            Aucun bien trouvé pour cette période.
        </div>
    @endif

    <!-- Calcul du Revenu Net -->
    <div class="section clearfix">
        <div class="section-title section-title-calcul">Calcul du Revenu Net</div>
        <br>
        <div class="info-box">
            <strong>Période :</strong> {{ $rapport['periode'] }}
            <hr>
            <strong>Propriétaire :</strong> {{ $proprietaire->username }}
            <hr>
            <strong>Nombre de biens :</strong> {{ $rapport['nombre_biens'] }}
        </div>
        <table class="calcul-table">
            <tr>
                <td><strong>Total Encaissé (Loyers + Ventes)</strong></td>
                <td class="text-end"><strong>{{ number_format($rapport['total_brut_encaisse'], 0, ',', ' ') }} F</strong></td>
            </tr>
            <tr style="background: #f8f9fa;">
                <td><strong>- Commission Agence</strong></td>
                <td class="text-end" style="color: #dc3545;">({{ number_format($rapport['total_commission_agence'], 0, ',', ' ') }} F)</td>
            </tr>
            <tr style="background: #f8f9fa;">
                <td><strong>- Total des Charges</strong></td>
                <td class="text-end" style="color: #e6a800;">({{ number_format($rapport['total_charges'], 0, ',', ' ') }} F)</td>
            </tr>
            <tr class="calcul-total">
                <td>= REVENU NET DU PROPRIÉTAIRE</td>
                <td class="text-end" style="color: #28a745;">{{ number_format($rapport['revenue_net'], 0, ',', ' ') }} F</td>
            </tr>
        </table>
    </div>

    <!-- Versements au Propriétaire -->
    <div class="section" style="margin-top: 25px;">
        <div class="section-title section-title-versement">Versements au Propriétaire</div>
        <br>

        <!-- KPI Versements -->
        <table class="versement-kpi">
            <tr>
                <td>
                    <div class="v-label">Montant à Verser</div>
                    <div class="v-value" style="color: #17a2b8;">{{ number_format($rapport['revenue_net'], 0, ',', ' ') }} F</div>
                </td>
                <td>
                    <div class="v-label">Montant Versé</div>
                    <div class="v-value" style="color: #28a745;">{{ number_format($rapport['montant_total_verse'], 0, ',', ' ') }} F</div>
                </td>
                <td>
                    <div class="v-label">Reste à Verser</div>
                    <div class="v-value" style="color: #0d6efd;">{{ number_format($rapport['reste_a_verser'], 0, ',', ' ') }} F</div>
                </td>
                <td>
                    <div class="v-label">Statut</div>
                    <div class="v-value">
                        <span class="badge badge-{{ $rapport['statut_versement']['badge'] }}">{{ $rapport['statut_versement']['label'] }}</span>
                    </div>
                </td>
            </tr>
        </table>

        @if($rapport['versements']->count() > 0)
            <table class="data">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Période</th>
                        <th class="text-end">Montant</th>
                        <th>Mode</th>
                        <th>Référence</th>
                        <th class="text-center">Statut</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rapport['versements'] as $versement)
                        <tr>
                            <td>{{ $versement->date_versement->format('d/m/Y') }}</td>
                            <td>
                                @if($versement->date_debut && $versement->date_fin)
                                    {{ $versement->date_debut->format('d/m/Y') }} - {{ $versement->date_fin->format('d/m/Y') }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td class="text-end fw-bold">{{ number_format($versement->montant, 0, ',', ' ') }} F</td>
                            <td>{{ $versement->mode_versement }}</td>
                            <td>{{ $versement->reference ?? '-' }}</td>
                            <td class="text-center">
                                @if($versement->statut === 'effectue')
                                    <span class="badge badge-success">Effectué</span>
                                @elseif($versement->statut === 'en_attente')
                                    <span class="badge badge-warning">En attente</span>
                                @elseif($versement->statut === 'annule')
                                    <span class="badge badge-danger">Annulé</span>
                                @else
                                    <span class="badge badge-secondary">{{ $versement->statut }}</span>
                                @endif
                            </td>
                            <td style="font-size: 9px;">{{ $versement->notes ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div style="text-align: center; padding: 15px; color: #666; background: #f8f9fa; border: 1px solid #ddd;">
                Aucun versement enregistré pour cette période.
            </div>
        @endif
    </div>

    <!-- Pied de page -->
    <div class="footer">
        Document confidentiel — Rapport financier propriétaire généré par SagesImmo le {{ now()->format('d/m/Y à H:i') }}
    </div>
</body>
</html>
