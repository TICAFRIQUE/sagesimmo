<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport Acheteur - {{ $acheteur->username }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #333; line-height: 1.4; }
        .header { text-align: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 3px solid #198754; }
        .header h1 { font-size: 20px; color: #000; margin-bottom: 5px; }
        .header p { margin: 3px 0; }
        .kpi-row table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .kpi-box { text-align: center; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        .kpi-box .label { font-size: 9px; color: #666; text-transform: uppercase; }
        .kpi-box .value { font-size: 14px; font-weight: bold; }
        .text-success { color: #28a745; }
        .text-danger { color: #dc3545; }
        .text-primary { color: #0d6efd; }
        .text-info { color: #17a2b8; }
        .text-warning { color: #856404; }

        .vente-header { background-color: #d4edda; padding: 8px 12px; border-left: 4px solid #198754; margin-top: 15px; margin-bottom: 5px; }
        .vente-header h3 { font-size: 13px; margin: 0; }

        .progress-container { margin: 8px 0; padding: 5px 12px; }
        .progress-bar-bg { height: 16px; background-color: #e9ecef; border-radius: 3px; overflow: hidden; }
        .progress-bar-fill { height: 100%; border-radius: 3px; text-align: center; color: #fff; font-size: 9px; line-height: 16px; }

        table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; font-size: 10px; }
        table.data-table th { background-color: #f8f9fa; padding: 5px 8px; border: 1px solid #ddd; font-size: 9px; text-transform: uppercase; }
        table.data-table td { padding: 4px 8px; border: 1px solid #eee; }
        table.data-table tfoot td { font-weight: bold; background-color: #f8f9fa; border-top: 2px solid #999; }

        .mini-kpi { width: 100%; border-collapse: collapse; margin-bottom: 5px; }
        .mini-kpi td { text-align: center; padding: 5px; border: 1px solid #eee; font-size: 10px; }
        .mini-kpi .label { font-size: 8px; color: #999; display: block; }

        .badge { display: inline-block; padding: 2px 6px; border-radius: 3px; font-size: 9px; font-weight: bold; color: #fff; }
        .badge-success { background-color: #28a745; }
        .badge-danger { background-color: #dc3545; }
        .badge-warning { background-color: #ffc107; color: #333; }
        .badge-info { background-color: #17a2b8; }
        .badge-secondary { background-color: #6c757d; }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .footer { text-align: center; margin-top: 20px; padding-top: 10px; border-top: 1px solid #ccc; font-size: 9px; color: #999; }
    </style>
</head>
<body>
    <div class="header">
        <h1>RAPPORT FINANCIER ACHETEUR</h1>
        <p style="font-size: 14px;"><strong>{{ $acheteur->username }}</strong>
            @if($acheteur->email) — {{ $acheteur->email }} @endif
            @if($acheteur->telephone) — {{ $acheteur->telephone }} @endif
        </p>
        <p style="font-size: 12px; color: #666;">Période : {{ $dateDebut?->format('d/m/Y') }} au {{ $dateFin?->format('d/m/Y') }}</p>
        <p style="font-size: 10px; color: #999;">Généré le {{ now()->format('d/m/Y à H:i') }}</p>
    </div>

    <!-- KPI -->
    <div class="kpi-row">
        <table>
            <tr>
                <td width="16%" style="padding: 3px;">
                    <div class="kpi-box"><div class="label">Total À Payer</div><div class="value text-primary">{{ number_format($rapport['total_a_payer'], 0, ',', ' ') }} F</div></div>
                </td>
                <td width="16%" style="padding: 3px;">
                    <div class="kpi-box"><div class="label">Total Payé</div><div class="value text-success">{{ number_format($rapport['total_paye'], 0, ',', ' ') }} F</div></div>
                </td>
                <td width="16%" style="padding: 3px;">
                    <div class="kpi-box"><div class="label">Reste</div><div class="value text-danger">{{ number_format($rapport['total_restant'], 0, ',', ' ') }} F</div></div>
                </td>
                <td width="16%" style="padding: 3px;">
                    <div class="kpi-box"><div class="label">Payé (Période)</div><div class="value text-info">{{ number_format($rapport['total_paye_periode'], 0, ',', ' ') }} F</div></div>
                </td>
                <td width="16%" style="padding: 3px;">
                    <div class="kpi-box"><div class="label">Taux</div><div class="value">{{ $rapport['taux_paiement'] }}%</div></div>
                </td>
                <td width="16%" style="padding: 3px;">
                    <div class="kpi-box">
                        <div class="label">Statut</div>
                        <span class="badge badge-{{ $rapport['statut_global']['badge'] }}">{{ $rapport['statut_global']['label'] }}</span>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Détail par vente -->
    @foreach($rapport['ventes'] as $rapportVente)
        <div class="vente-header">
            <h3>{{ $rapportVente['adresse'] }} — {{ $rapportVente['type_bien'] }}
                | Prix: {{ number_format($rapportVente['prix_vente'], 0, ',', ' ') }} F
                @if($rapportVente['date_vente']) | Vente: {{ $rapportVente['date_vente']->format('d/m/Y') }} @endif
                | <span class="badge badge-{{ $rapportVente['statut_paiement']['badge'] }}">{{ $rapportVente['statut_paiement']['label'] }}</span>
            </h3>
        </div>

        <!-- Barre de progression -->
        <div class="progress-container">
            <div style="font-size: 9px; margin-bottom: 2px;">
                Progression: {{ number_format($rapportVente['total_paye'], 0, ',', ' ') }} F / {{ number_format($rapportVente['prix_vente'], 0, ',', ' ') }} F
                @if($rapportVente['reste_a_payer'] > 0)
                    — <span class="text-danger">Reste: {{ number_format($rapportVente['reste_a_payer'], 0, ',', ' ') }} F</span>
                @endif
            </div>
            @php $pct = $rapportVente['pourcentage_paiement']; @endphp
            <div class="progress-bar-bg">
                <div class="progress-bar-fill" style="width: {{ max($pct, 3) }}%; background-color: {{ $pct >= 100 ? '#28a745' : ($pct >= 50 ? '#17a2b8' : '#ffc107') }};">
                    {{ round($pct, 1) }}%
                </div>
            </div>
        </div>

        <!-- Mini KPI -->
        <table class="mini-kpi">
            <tr>
                <td><span class="label">Prix Vente</span><strong>{{ number_format($rapportVente['prix_vente'], 0, ',', ' ') }} F</strong></td>
                <td><span class="label">Total Payé</span><strong class="text-success">{{ number_format($rapportVente['total_paye'], 0, ',', ' ') }} F</strong></td>
                <td><span class="label">Reste</span><strong class="text-danger">{{ number_format($rapportVente['reste_a_payer'], 0, ',', ' ') }} F</strong></td>
                <td><span class="label">Payé (Période)</span><strong class="text-info">{{ number_format($rapportVente['total_paye_periode'], 0, ',', ' ') }} F</strong></td>
            </tr>
        </table>

        <!-- Tableau paiements -->
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Type</th>
                    <th class="text-right">Montant</th>
                    <th>Méthode</th>
                    <th>Référence</th>
                    <th class="text-center">Statut</th>
                </tr>
            </thead>
            <tbody>
                @php $typeLabels = ['prix_achat' => 'Prix achat', 'arrhes' => 'Arrhes', 'frais_agence' => 'Frais agence']; @endphp
                @forelse($rapportVente['tous_paiements'] as $idx => $paiement)
                    <tr>
                        <td>{{ $idx + 1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($paiement['date'])->format('d/m/Y') }}</td>
                        <td>{{ $typeLabels[$paiement['type']] ?? ucfirst($paiement['type']) }}</td>
                        <td class="text-right text-success" style="font-weight: bold;">{{ number_format($paiement['montant'], 0, ',', ' ') }} F</td>
                        <td>{{ ucfirst($paiement['methode'] ?? '-') }}</td>
                        <td style="font-size: 9px; color: #666;">{{ $paiement['reference'] ?? '-' }}</td>
                        <td class="text-center"><span class="badge badge-{{ $paiement['statut'] === 'paye' ? 'success' : 'warning' }}">{{ ucfirst($paiement['statut']) }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center" style="color: #999;">Aucun paiement enregistré.</td></tr>
                @endforelse
            </tbody>
            @if($rapportVente['tous_paiements']->count() > 0)
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-right">Total :</td>
                        <td class="text-right text-success">{{ number_format($rapportVente['total_paye'], 0, ',', ' ') }} F</td>
                        <td colspan="3"></td>
                    </tr>
                </tfoot>
            @endif
        </table>
    @endforeach

    <div class="footer">
        <p>Document généré automatiquement — {{ config('app.name', 'SagesImmo') }} — {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>
