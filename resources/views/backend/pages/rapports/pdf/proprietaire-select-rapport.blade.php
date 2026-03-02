<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport Financier - Tous les Propriétaires</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #333; line-height: 1.4; }

        .header { text-align: center; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 3px solid #0d6efd; }
        .header h1 { font-size: 20px; margin-bottom: 5px; color: #000; }
        .header p { margin: 3px 0; color: #555; }
        .header .date-gen { font-size: 9px; color: #999; }

        .kpi-row table { width: 100%; border-collapse: collapse; margin-bottom: 18px; }
        .kpi-box { width: 25%; text-align: center; padding: 8px 5px; border: 1px solid #ddd; }
        .kpi-label { font-size: 8px; text-transform: uppercase; color: #666; margin-bottom: 2px; }
        .kpi-value { font-size: 14px; font-weight: bold; }
        .kpi-yellow { color: #e6a800; }
        .kpi-cyan { color: #17a2b8; }
        .kpi-green { color: #28a745; }
        .kpi-blue { color: #0d6efd; }

        table.data { width: 100%; border-collapse: collapse; }
        table.data th, table.data td { padding: 4px 6px; border: 1px solid #ddd; font-size: 9px; }
        table.data thead th { background: #333; color: #fff; font-weight: bold; text-align: left; border-bottom: 2px solid #000; }
        table.data .text-end { text-align: right; }
        table.data .text-center { text-align: center; }

        .row-agence { background: #e3f0ff !important; }

        .badge { display: inline-block; padding: 1px 5px; border-radius: 3px; font-size: 8px; font-weight: bold; }
        .badge-primary { background: #cce5ff; color: #004085; }
        .badge-info { background: #d1ecf1; color: #0c5460; }
        .badge-success { background: #d4edda; color: #155724; }
        .badge-warning { background: #fff3cd; color: #856404; }
        .badge-danger { background: #f8d7da; color: #721c24; }
        .badge-secondary { background: #e2e3e5; color: #383d41; }

        .text-danger { color: #dc3545; }
        .text-success { color: #28a745; font-weight: bold; }
        .text-warning { color: #e6a800; }

        .footer { text-align: center; font-size: 9px; color: #999; margin-top: 25px; padding-top: 8px; border-top: 1px solid #ddd; }
    </style>
</head>
<body>
    <!-- En-tête -->
    <div class="header">
        <h1>RAPPORT FINANCIER — TOUS LES PROPRIÉTAIRES</h1>
        @if(isset($dateDebut) && isset($dateFin))
            <p><strong>Période :</strong> {{ $dateDebut->format('d/m/Y') }} au {{ $dateFin->format('d/m/Y') }}</p>
        @endif
        <p class="date-gen">Document généré le {{ now()->format('d/m/Y à H:i') }} — {{ $proprietaires->count() }} propriétaire(s)</p>
    </div>

    <!-- KPI Global -->
    @if(isset($kpiGlobal))
        <div class="kpi-row">
            <table>
                <tr>
                    <td class="kpi-box">
                        <div class="kpi-label">Versements Disponibles</div>
                        <div class="kpi-value kpi-yellow">{{ number_format($kpiGlobal['versements_disponibles'], 0, ',', ' ') }} F</div>
                    </td>
                    <td class="kpi-box">
                        <div class="kpi-label">Versements Partiels</div>
                        <div class="kpi-value kpi-cyan">{{ number_format($kpiGlobal['versements_partiels'], 0, ',', ' ') }} F</div>
                    </td>
                    <td class="kpi-box">
                        <div class="kpi-label">Versements Effectués</div>
                        <div class="kpi-value kpi-green">{{ number_format($kpiGlobal['versements_effectues'], 0, ',', ' ') }} F</div>
                    </td>
                    <td class="kpi-box">
                        <div class="kpi-label">Commission Perçue</div>
                        <div class="kpi-value kpi-blue">{{ number_format($kpiGlobal['total_commission'], 0, ',', ' ') }} F</div>
                    </td>
                </tr>
            </table>
        </div>
    @endif

    <!-- Tableau des propriétaires -->
    <table class="data">
        <thead>
            <tr>
                <th>Propriétaire</th>
                <th class="text-center">Biens</th>
                <th class="text-end">Total Encaissé</th>
                <th class="text-end">Commission</th>
                <th class="text-end">Charges</th>
                <th class="text-end">Net (À Verser)</th>
                <th class="text-end">Déjà Versé</th>
                <th class="text-end">Reste</th>
                <th class="text-center">Statut</th>
            </tr>
        </thead>
        <tbody>
            @php
                $proprietairesTries = $proprietaires->sortByDesc(fn($p) => $p->type_proprietaire === 'agence' ? 1 : 0);
            @endphp
            @foreach($proprietairesTries as $proprietaire)
                @php
                    $rapport = $aperçus[$proprietaire->id] ?? null;
                    $isAgence = $proprietaire->type_proprietaire === 'agence';
                @endphp
                <tr class="{{ $isAgence ? 'row-agence' : '' }}">
                    <td>
                        <strong>{{ $proprietaire->username }}</strong>
                        @if($isAgence)
                            <span class="badge badge-primary">AGENCE</span>
                        @endif
                        <br>
                        <span style="font-size: 8px; color: #888;">{{ $proprietaire->email }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge badge-info">{{ $rapport['nombre_biens'] ?? 0 }}</span>
                    </td>
                    <td class="text-end">
                        <strong>{{ number_format($rapport['total_brut_encaisse'] ?? 0, 0, ',', ' ') }} F</strong>
                    </td>
                    <td class="text-end" style="color: #0d6efd;">
                        {{ number_format($rapport['total_commission_agence'] ?? 0, 0, ',', ' ') }} F
                    </td>
                    <td class="text-end text-warning">
                        {{ number_format($rapport['total_charges'] ?? 0, 0, ',', ' ') }} F
                    </td>
                    <td class="text-end text-success">
                        {{ number_format($rapport['revenue_net'] ?? 0, 0, ',', ' ') }} F
                    </td>
                    <td class="text-end" style="color: #17a2b8;">
                        {{ number_format($rapport['montant_total_verse'] ?? 0, 0, ',', ' ') }} F
                    </td>
                    <td class="text-end" style="color: {{ ($rapport['reste_a_verser'] ?? 0) > 0 ? '#dc3545' : '#888' }}; font-weight: bold;">
                        {{ number_format($rapport['reste_a_verser'] ?? 0, 0, ',', ' ') }} F
                    </td>
                    <td class="text-center">
                        @if($rapport)
                            <span class="badge badge-{{ $rapport['statut_versement']['badge'] }}">
                                {{ $rapport['statut_versement']['label'] }}
                            </span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Pied de page -->
    <div class="footer">
        Document confidentiel — Rapport financier global généré par SagesImmo le {{ now()->format('d/m/Y à H:i') }}
    </div>
</body>
</html>
