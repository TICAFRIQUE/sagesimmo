<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport Global Acheteurs</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #333; line-height: 1.3; }
        .header { text-align: center; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 3px solid #198754; }
        .header h1 { font-size: 18px; color: #000; margin-bottom: 5px; }
        .header p { margin: 2px 0; }
        .kpi-row table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        .kpi-box { text-align: center; padding: 6px; border: 1px solid #ddd; border-radius: 4px; }
        .kpi-box .label { font-size: 8px; color: #666; text-transform: uppercase; }
        .kpi-box .value { font-size: 12px; font-weight: bold; }
        .text-success { color: #28a745; }
        .text-danger { color: #dc3545; }
        .text-primary { color: #0d6efd; }
        .text-info { color: #17a2b8; }

        table.data-table { width: 100%; border-collapse: collapse; font-size: 9px; }
        table.data-table th { background-color: #198754; color: #fff; padding: 5px 6px; border: 1px solid #157347; text-align: left; font-size: 8px; text-transform: uppercase; }
        table.data-table td { padding: 4px 6px; border: 1px solid #ddd; }
        table.data-table tfoot td { font-weight: bold; background-color: #f8f9fa; border-top: 2px solid #999; }

        .progress-bar-bg { height: 12px; background-color: #e9ecef; border-radius: 2px; overflow: hidden; }
        .progress-bar-fill { height: 100%; border-radius: 2px; text-align: center; color: #fff; font-size: 7px; line-height: 12px; }

        .badge { display: inline-block; padding: 1px 5px; border-radius: 3px; font-size: 8px; font-weight: bold; color: #fff; }
        .badge-success { background-color: #28a745; }
        .badge-danger { background-color: #dc3545; }
        .badge-warning { background-color: #ffc107; color: #333; }
        .badge-info { background-color: #17a2b8; }
        .badge-secondary { background-color: #6c757d; }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .footer { text-align: center; margin-top: 15px; padding-top: 8px; border-top: 1px solid #ccc; font-size: 8px; color: #999; }
    </style>
</head>
<body>
    <div class="header">
        <h1>RAPPORT ACHETEURS - SUIVI DES PAIEMENTS</h1>
        <p style="font-size: 11px;"><strong>Période :</strong> {{ $dateDebut->format('d/m/Y') }} au {{ $dateFin->format('d/m/Y') }}</p>
        <p style="font-size: 9px; color: #999;">Généré le {{ now()->format('d/m/Y à H:i') }} — {{ $acheteurs->count() }} acheteur(s)</p>
    </div>

    <div class="kpi-row">
        <table>
            <tr>
                <td width="25%" style="padding: 2px;">
                    <div class="kpi-box"><div class="label">Total À Payer</div><div class="value text-primary">{{ number_format($kpiGlobal['total_a_payer'], 0, ',', ' ') }} F</div></div>
                </td>
                <td width="25%" style="padding: 2px;">
                    <div class="kpi-box"><div class="label">Total Payé</div><div class="value text-success">{{ number_format($kpiGlobal['total_paye'], 0, ',', ' ') }} F</div></div>
                </td>
                <td width="25%" style="padding: 2px;">
                    <div class="kpi-box"><div class="label">Reste À Payer</div><div class="value text-danger">{{ number_format($kpiGlobal['total_restant'], 0, ',', ' ') }} F</div></div>
                </td>
                <td width="25%" style="padding: 2px;">
                    <div class="kpi-box"><div class="label">Payé (Période)</div><div class="value text-info">{{ number_format($kpiGlobal['total_paye_periode'], 0, ',', ' ') }} F</div></div>
                </td>
            </tr>
        </table>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Acheteur</th>
                <th class="text-center">Ventes</th>
                <th class="text-right">Prix Total</th>
                <th class="text-right">Total Payé</th>
                <th class="text-right">Reste</th>
                <th class="text-center" style="width: 80px;">Progression</th>
                <th class="text-right">Payé (Période)</th>
                <th class="text-center">Statut</th>
            </tr>
        </thead>
        <tbody>
            @php $num = 0; @endphp
            @foreach($acheteurs->sortByDesc(fn($a) => $aperçus[$a->id]['total_restant'] ?? 0) as $acheteur)
                @php
                    $num++;
                    $apercu = $aperçus[$acheteur->id] ?? null;
                @endphp
                @if($apercu)
                    <tr>
                        <td>{{ $num }}</td>
                        <td>
                            <strong>{{ $acheteur->username }}</strong>
                            @if($acheteur->email)<br><span style="font-size: 8px; color: #666;">{{ $acheteur->email }}</span>@endif
                        </td>
                        <td class="text-center">{{ $apercu['nb_ventes'] }}</td>
                        <td class="text-right" style="font-weight: bold;">{{ number_format($apercu['total_a_payer'], 0, ',', ' ') }} F</td>
                        <td class="text-right text-success">{{ number_format($apercu['total_paye'], 0, ',', ' ') }} F</td>
                        <td class="text-right text-danger">{{ number_format($apercu['total_restant'], 0, ',', ' ') }} F</td>
                        <td class="text-center">
                            @php $taux = $apercu['taux_paiement']; @endphp
                            <div class="progress-bar-bg">
                                <div class="progress-bar-fill" style="width: {{ max($taux, 5) }}%; background-color: {{ $taux >= 100 ? '#28a745' : ($taux >= 50 ? '#17a2b8' : '#ffc107') }};">
                                    {{ $taux }}%
                                </div>
                            </div>
                        </td>
                        <td class="text-right">{{ number_format($apercu['total_paye_periode'], 0, ',', ' ') }} F</td>
                        <td class="text-center">
                            <span class="badge badge-{{ $apercu['statut_global']['badge'] }}">{{ $apercu['statut_global']['label'] }}</span>
                        </td>
                    </tr>
                @endif
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="text-right">TOTAUX :</td>
                <td class="text-right">{{ number_format($aperçus->sum('total_a_payer'), 0, ',', ' ') }} F</td>
                <td class="text-right text-success">{{ number_format($aperçus->sum('total_paye'), 0, ',', ' ') }} F</td>
                <td class="text-right text-danger">{{ number_format($aperçus->sum('total_restant'), 0, ',', ' ') }} F</td>
                <td colspan="3"></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Document généré automatiquement — {{ config('app.name', 'SagesImmo') }} — {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>
