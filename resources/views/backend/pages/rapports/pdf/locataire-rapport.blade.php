<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport Locataire - {{ $locataire->username }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #333; line-height: 1.4; }
        .header { text-align: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 3px solid #dc3545; }
        .header h1 { font-size: 20px; color: #000; margin-bottom: 5px; }
        .header p { margin: 3px 0; }
        .kpi-row { width: 100%; margin-bottom: 15px; }
        .kpi-row table { width: 100%; border-collapse: collapse; }
        .kpi-box { text-align: center; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        .kpi-box .label { font-size: 9px; color: #666; text-transform: uppercase; }
        .kpi-box .value { font-size: 14px; font-weight: bold; }
        .text-success { color: #28a745; }
        .text-danger { color: #dc3545; }
        .text-primary { color: #0d6efd; }
        .text-warning { color: #856404; }
        .text-info { color: #17a2b8; }

        .location-header { background-color: #e8f4fd; padding: 8px 12px; border-left: 4px solid #0d6efd; margin-top: 15px; margin-bottom: 5px; }
        .location-header h3 { font-size: 13px; margin: 0; }

        table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; font-size: 10px; }
        table.data-table th { background-color: #f8f9fa; padding: 5px 8px; border: 1px solid #ddd; text-align: left; font-size: 9px; text-transform: uppercase; }
        table.data-table td { padding: 4px 8px; border: 1px solid #eee; }
        table.data-table tr.row-retard { background-color: #fff3cd; }
        table.data-table tr.row-impaye { background-color: #f8d7da; }
        table.data-table tr.row-paye { background-color: #d4edda; }
        table.data-table tfoot td { font-weight: bold; background-color: #f8f9fa; border-top: 2px solid #999; }

        .badge { display: inline-block; padding: 2px 6px; border-radius: 3px; font-size: 9px; font-weight: bold; color: #fff; }
        .badge-success { background-color: #28a745; }
        .badge-danger { background-color: #dc3545; }
        .badge-warning { background-color: #ffc107; color: #333; }
        .badge-info { background-color: #17a2b8; }
        .badge-secondary { background-color: #6c757d; }

        .mini-kpi { width: 100%; border-collapse: collapse; margin-bottom: 5px; }
        .mini-kpi td { text-align: center; padding: 5px; border: 1px solid #eee; font-size: 10px; }
        .mini-kpi .label { font-size: 8px; color: #999; display: block; }

        .alert { padding: 8px 12px; border-radius: 4px; margin-bottom: 10px; font-size: 10px; }
        .alert-info { background-color: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; }

        .footer { text-align: center; margin-top: 20px; padding-top: 10px; border-top: 1px solid #ccc; font-size: 9px; color: #999; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h1>RAPPORT FINANCIER LOCATAIRE</h1>
        <p style="font-size: 14px;"><strong>{{ $locataire->username }}</strong>
            @if($locataire->email) — {{ $locataire->email }} @endif
            @if($locataire->telephone) — {{ $locataire->telephone }} @endif
        </p>
        <p style="font-size: 12px; color: #666;">
            @if($dateDebut && $dateFin)
                Période : {{ $dateDebut->format('d/m/Y') }} au {{ $dateFin->format('d/m/Y') }}
            @else
                Toutes les échéances
            @endif
        </p>
        <p style="font-size: 10px; color: #999;">Généré le {{ now()->format('d/m/Y à H:i') }}</p>
    </div>

    <!-- KPI -->
    <div class="kpi-row">
        <table>
            <tr>
                <td width="16%" style="padding: 3px;">
                    <div class="kpi-box">
                        <div class="label">Total Dû</div>
                        <div class="value text-primary">{{ number_format($rapport['total_du'], 0, ',', ' ') }} F</div>
                    </div>
                </td>
                <td width="16%" style="padding: 3px;">
                    <div class="kpi-box">
                        <div class="label">Total Payé</div>
                        <div class="value text-success">{{ number_format($rapport['total_paye'], 0, ',', ' ') }} F</div>
                    </div>
                </td>
                <td width="16%" style="padding: 3px;">
                    <div class="kpi-box">
                        <div class="label">Restant</div>
                        <div class="value text-danger">{{ number_format($rapport['total_restant'], 0, ',', ' ') }} F</div>
                    </div>
                </td>
                <td width="16%" style="padding: 3px;">
                    <div class="kpi-box">
                        <div class="label">En Retard</div>
                        <div class="value text-warning">{{ $rapport['nb_en_retard'] }} éch.</div>
                    </div>
                </td>
                <td width="16%" style="padding: 3px;">
                    <div class="kpi-box">
                        <div class="label">Impayées</div>
                        <div class="value text-danger">{{ $rapport['nb_impayees'] }} éch.</div>
                    </div>
                </td>
                <td width="16%" style="padding: 3px;">
                    <div class="kpi-box">
                        <div class="label">Taux</div>
                        <div class="value">{{ $rapport['taux_paiement'] }}%</div>
                        <span class="badge badge-{{ $rapport['statut_global']['badge'] }}">{{ $rapport['statut_global']['label'] }}</span>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    @if($rapport['prochaine_echeance'])
        <div class="alert alert-info">
            <strong>Prochaine échéance :</strong>
            {{ $rapport['prochaine_echeance']['date']->format('d/m/Y') }}
            — {{ number_format($rapport['prochaine_echeance']['montant'], 0, ',', ' ') }} F
            — {{ $rapport['prochaine_echeance']['bien'] }}
            (dans {{ $rapport['prochaine_echeance']['jours_restants'] }} jour(s))
        </div>
    @endif

    <!-- Détail par location -->
    @foreach($rapport['locations'] as $rapportLocation)
        <div class="location-header">
            <h3>{{ $rapportLocation['adresse'] }} — {{ $rapportLocation['type_bien'] }}
                | Loyer: {{ number_format($rapportLocation['loyer_mensuel'], 0, ',', ' ') }} F/mois
                | {{ ucfirst($rapportLocation['statut_location']) }}
            </h3>
        </div>

        <table class="mini-kpi">
            <tr>
                <td><span class="label">Dû</span><strong>{{ number_format($rapportLocation['total_du'], 0, ',', ' ') }} F</strong></td>
                <td><span class="label">Payé</span><strong class="text-success">{{ number_format($rapportLocation['total_paye'], 0, ',', ' ') }} F</strong></td>
                <td><span class="label">Restant</span><strong class="text-danger">{{ number_format($rapportLocation['total_restant'], 0, ',', ' ') }} F</strong></td>
                <td><span class="label">Taux</span><strong>{{ $rapportLocation['taux_paiement'] }}%</strong></td>
            </tr>
        </table>

        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date Échéance</th>
                    <th class="text-right">Montant Dû</th>
                    <th class="text-right">Montant Payé</th>
                    <th class="text-right">Reste</th>
                    <th class="text-center">Jrs Retard</th>
                    <th class="text-center">Statut</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rapportLocation['echeances'] as $idx => $echeance)
                    @php
                        $rowClass = '';
                        if ($echeance['statut'] === 'impaye') $rowClass = 'row-impaye';
                        elseif ($echeance['statut'] === 'en_retard') $rowClass = 'row-retard';
                        elseif ($echeance['statut'] === 'paye') $rowClass = 'row-paye';

                        $statutLabels = ['paye' => 'Payé', 'partiel' => 'Partiel', 'en_retard' => 'En retard', 'impaye' => 'Impayé', 'a_echeance' => 'À échéance'];
                        $statutBadges = ['paye' => 'success', 'partiel' => 'info', 'en_retard' => 'warning', 'impaye' => 'danger', 'a_echeance' => 'secondary'];
                    @endphp
                    <tr class="{{ $rowClass }}">
                        <td>{{ $idx + 1 }}</td>
                        <td>{{ $echeance['date_echeance']->format('d/m/Y') }}</td>
                        <td class="text-right">{{ number_format($echeance['montant_du'], 0, ',', ' ') }} F</td>
                        <td class="text-right">{{ number_format($echeance['montant_paye'], 0, ',', ' ') }} F</td>
                        <td class="text-right">{{ number_format($echeance['montant_restant'], 0, ',', ' ') }} F</td>
                        <td class="text-center">{{ $echeance['jours_retard'] > 0 ? $echeance['jours_retard'] . 'j' : '-' }}</td>
                        <td class="text-center">
                            <span class="badge badge-{{ $statutBadges[$echeance['statut']] ?? 'secondary' }}">
                                {{ $statutLabels[$echeance['statut']] ?? $echeance['statut'] }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center" style="color: #999;">Aucune échéance sur cette période.</td></tr>
                @endforelse
            </tbody>
        </table>
    @endforeach

    <div class="footer">
        <p>Document généré automatiquement — {{ config('app.name', 'SagesImmo') }} — {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>
