<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport Global Locataires</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #333; line-height: 1.3; }
        .header { text-align: center; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 3px solid #dc3545; }
        .header h1 { font-size: 18px; color: #000; margin-bottom: 5px; }
        .header p { margin: 2px 0; }
        .kpi-row table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        .kpi-box { text-align: center; padding: 6px; border: 1px solid #ddd; border-radius: 4px; }
        .kpi-box .label { font-size: 8px; color: #666; text-transform: uppercase; }
        .kpi-box .value { font-size: 12px; font-weight: bold; }
        .text-success { color: #28a745; }
        .text-danger { color: #dc3545; }
        .text-primary { color: #0d6efd; }
        .text-warning { color: #856404; }

        table.data-table { width: 100%; border-collapse: collapse; font-size: 9px; }
        table.data-table th { background-color: #343a40; color: #fff; padding: 5px 6px; border: 1px solid #555; text-align: left; font-size: 8px; text-transform: uppercase; }
        table.data-table td { padding: 4px 6px; border: 1px solid #ddd; }
        table.data-table tr.row-danger { background-color: #f8d7da; }
        table.data-table tr.row-warning { background-color: #fff3cd; }
        table.data-table tfoot td { font-weight: bold; background-color: #f8f9fa; border-top: 2px solid #999; }

        .badge { display: inline-block; padding: 1px 5px; border-radius: 3px; font-size: 8px; font-weight: bold; color: #fff; }
        .badge-success { background-color: #28a745; }
        .badge-danger { background-color: #dc3545; }
        .badge-warning { background-color: #ffc107; color: #333; }
        .badge-secondary { background-color: #6c757d; }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .footer { text-align: center; margin-top: 15px; padding-top: 8px; border-top: 1px solid #ccc; font-size: 8px; color: #999; }
    </style>
</head>
<body>
    <div class="header">
        <h1>RAPPORT LOCATAIRES - ÉCHÉANCES & PAIEMENTS</h1>
        <p style="font-size: 11px;">
            @if($dateDebut && $dateFin)
                <strong>Période :</strong> {{ $dateDebut->format('d/m/Y') }} au {{ $dateFin->format('d/m/Y') }}
            @else
                <strong>Toutes les échéances</strong>
            @endif
        </p>
        <p style="font-size: 9px; color: #999;">Généré le {{ now()->format('d/m/Y à H:i') }} — {{ $locataires->count() }} locataire(s)</p>
    </div>

    <div class="kpi-row">
        <table>
            <tr>
                <td width="16%" style="padding: 2px;">
                    <div class="kpi-box"><div class="label">Total Dû</div><div class="value text-primary">{{ number_format($kpiGlobal['total_du'], 0, ',', ' ') }} F</div></div>
                </td>
                <td width="16%" style="padding: 2px;">
                    <div class="kpi-box"><div class="label">Total Payé</div><div class="value text-success">{{ number_format($kpiGlobal['total_paye'], 0, ',', ' ') }} F</div></div>
                </td>
                <td width="16%" style="padding: 2px;">
                    <div class="kpi-box"><div class="label">Restant</div><div class="value text-danger">{{ number_format($kpiGlobal['total_restant'], 0, ',', ' ') }} F</div></div>
                </td>
                <td width="16%" style="padding: 2px;">
                    <div class="kpi-box"><div class="label">Montant En Retard</div><div class="value text-danger">{{ number_format($kpiGlobal['total_en_retard'], 0, ',', ' ') }} F</div></div>
                </td>
                <td width="16%" style="padding: 2px;">
                    <div class="kpi-box"><div class="label">Éch. En Retard</div><div class="value text-warning">{{ $kpiGlobal['nb_en_retard'] }}</div></div>
                </td>
                <td width="16%" style="padding: 2px;">
                    <div class="kpi-box"><div class="label">Éch. Impayées</div><div class="value text-danger">{{ $kpiGlobal['nb_impayees'] }}</div></div>
                </td>
            </tr>
        </table>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Locataire</th>
                <th class="text-center">Locations</th>
                <th class="text-right">Total Dû</th>
                <th class="text-right">Total Payé</th>
                <th class="text-right">Restant</th>
                <th class="text-center">Taux</th>
                <th class="text-center">En Retard</th>
                <th class="text-center">Impayées</th>
                <th class="text-center">Prochaine Éch.</th>
                <th class="text-center">Statut</th>
            </tr>
        </thead>
        <tbody>
            @php $num = 0; @endphp
            @foreach($locataires->sortByDesc(fn($l) => ($aperçus[$l->id]['nb_en_retard'] ?? 0) + ($aperçus[$l->id]['nb_impayees'] ?? 0)) as $locataire)
                @php
                    $num++;
                    $apercu = $aperçus[$locataire->id] ?? null;
                    $rowClass = '';
                    if ($apercu) {
                        if (($apercu['statut_global']['code'] ?? '') === 'impaye') $rowClass = 'row-danger';
                        elseif (($apercu['statut_global']['code'] ?? '') === 'en_retard') $rowClass = 'row-warning';
                    }
                @endphp
                @if($apercu)
                    <tr class="{{ $rowClass }}">
                        <td>{{ $num }}</td>
                        <td>
                            <strong>{{ $locataire->username }}</strong>
                            @if($locataire->email)<br><span style="font-size: 8px; color: #666;">{{ $locataire->email }}</span>@endif
                        </td>
                        <td class="text-center">{{ $apercu['nb_locations'] }}</td>
                        <td class="text-right">{{ number_format($apercu['total_du'], 0, ',', ' ') }} F</td>
                        <td class="text-right text-success">{{ number_format($apercu['total_paye'], 0, ',', ' ') }} F</td>
                        <td class="text-right text-danger">{{ number_format($apercu['total_restant'], 0, ',', ' ') }} F</td>
                        <td class="text-center">{{ $apercu['taux_paiement'] }}%</td>
                        <td class="text-center">
                            @if($apercu['nb_en_retard'] > 0)
                                <span class="badge badge-warning">{{ $apercu['nb_en_retard'] }}</span>
                            @else 0 @endif
                        </td>
                        <td class="text-center">
                            @if($apercu['nb_impayees'] > 0)
                                <span class="badge badge-danger">{{ $apercu['nb_impayees'] }}</span>
                            @else 0 @endif
                        </td>
                        <td class="text-center" style="font-size: 8px;">
                            @if($apercu['prochaine_echeance'])
                                {{ $apercu['prochaine_echeance']['date']->format('d/m/Y') }}
                                <br>{{ number_format($apercu['prochaine_echeance']['montant'], 0, ',', ' ') }} F
                            @else - @endif
                        </td>
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
                <td class="text-right">{{ number_format($aperçus->sum('total_du'), 0, ',', ' ') }} F</td>
                <td class="text-right text-success">{{ number_format($aperçus->sum('total_paye'), 0, ',', ' ') }} F</td>
                <td class="text-right text-danger">{{ number_format($aperçus->sum('total_restant'), 0, ',', ' ') }} F</td>
                <td colspan="5"></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Document généré automatiquement — {{ config('app.name', 'SagesImmo') }} — {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>
