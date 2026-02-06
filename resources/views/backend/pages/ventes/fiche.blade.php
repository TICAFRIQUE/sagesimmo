<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fiche de Vente - {{ $vente->annonce->titre ?? 'N/A' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Arial', sans-serif;
            padding: 40px;
            background: #fff;
            color: #333;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: #fff;
        }
        .header {
            border-bottom: 3px solid #299cdb;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #299cdb;
            font-size: 28px;
            margin-bottom: 10px;
        }
        .header .info {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
        }
        .header .info div {
            font-size: 12px;
            color: #666;
        }
        .section {
            margin-bottom: 30px;
        }
        .section-title {
            background: #f0f9ff;
            padding: 10px 15px;
            border-left: 4px solid #299cdb;
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 15px;
            color: #299cdb;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }
        .info-item {
            padding: 10px;
            border: 1px solid #e9ecef;
            border-radius: 5px;
        }
        .info-item label {
            display: block;
            font-size: 11px;
            color: #666;
            margin-bottom: 5px;
            text-transform: uppercase;
            font-weight: bold;
        }
        .info-item .value {
            font-size: 14px;
            color: #333;
            font-weight: 500;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        table th {
            background: #f8f9fa;
            padding: 10px;
            text-align: left;
            font-size: 12px;
            border: 1px solid #dee2e6;
            font-weight: bold;
        }
        table td {
            padding: 10px;
            border: 1px solid #dee2e6;
            font-size: 13px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
        }
        .badge-success {
            background: #d1fae5;
            color: #065f46;
        }
        .badge-warning {
            background: #fef3c7;
            color: #92400e;
        }
        .badge-info {
            background: #dbeafe;
            color: #1e40af;
        }
        .summary-box {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }
        .summary-box .row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #dee2e6;
        }
        .summary-box .row:last-child {
            border-bottom: none;
            font-weight: bold;
            font-size: 16px;
            margin-top: 10px;
            padding-top: 15px;
            border-top: 2px solid #299cdb;
        }
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 2px solid #e9ecef;
            text-align: center;
            font-size: 11px;
            color: #666;
        }
        .signature-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            margin-top: 50px;
        }
        .signature-box {
            text-align: center;
        }
        .signature-box .title {
            font-weight: bold;
            margin-bottom: 50px;
        }
        .signature-box .line {
            border-top: 1px solid #333;
            margin-top: 50px;
        }
        @media print {
            body {
                padding: 20px;
            }
            .no-print {
                display: none;
            }
        }
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #299cdb;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        .print-button:hover {
            background: #2280b3;
        }
    </style>
</head>
<body>
    <button class="print-button no-print" onclick="window.print()">
        🖨️ Imprimer
    </button>

    <div class="container">
        <!-- En-tête -->
        <div class="header">
            <h1>📄 FICHE DE VENTE</h1>
            <div class="info">
                <div>
                    <strong>Référence:</strong> VENTE-{{ str_pad($vente->id, 6, '0', STR_PAD_LEFT) }}<br>
                    <strong>Date:</strong> {{ now()->format('d/m/Y') }}
                </div>
                <div style="text-align: right;">
                    <strong>Statut:</strong> 
                    @if($vente->statut === 'terminee')
                        <span class="badge badge-success">FINALISÉ</span>
                    @elseif($vente->statut === 'offre_acceptee')
                        <span class="badge badge-warning">OFFRE ACCEPTÉE</span>
                    @else
                        <span class="badge badge-info">{{ strtoupper(str_replace('_', ' ', $vente->statut)) }}</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Informations du bien -->
        <div class="section">
            <div class="section-title">🏠 INFORMATIONS DU BIEN</div>
            <div class="info-grid">
                <div class="info-item">
                    <label>Titre du bien</label>
                    <div class="value">{{ $vente->annonce->titre ?? 'N/A' }}</div>
                </div>
                <div class="info-item">
                    <label>Type de bien</label>
                    <div class="value">{{ $vente->annonce->typeBien->name ?? 'N/A' }}</div>
                </div>
                <div class="info-item">
                    <label>Adresse</label>
                    <div class="value">{{ $vente->annonce->adresse ?? 'N/A' }}</div>
                </div>
                <div class="info-item">
                    <label>Superficie</label>
                    <div class="value">{{ $vente->annonce->superficie ?? 'N/A' }} m²</div>
                </div>
                <div class="info-item">
                    <label>Nombre de pièces</label>
                    <div class="value">{{ $vente->annonce->nombre_pieces ?? 'N/A' }}</div>
                </div>
                <div class="info-item">
                    <label>Référence annonce</label>
                    <div class="value">{{ $vente->annonce->reference ?? 'N/A' }}</div>
                </div>
            </div>
        </div>

        <!-- Informations du client -->
        <div class="section">
            <div class="section-title">👤 INFORMATIONS DE L'ACHETEUR</div>
            <div class="info-grid">
                <div class="info-item">
                    <label>Nom complet</label>
                    <div class="value">{{ $vente->client->name ?? 'N/A' }}</div>
                </div>
                <div class="info-item">
                    <label>Email</label>
                    <div class="value">{{ $vente->client->email ?? 'N/A' }}</div>
                </div>
                <div class="info-item">
                    <label>Téléphone</label>
                    <div class="value">{{ $vente->client->phone ?? 'N/A' }}</div>
                </div>
                <div class="info-item">
                    <label>Date de la demande</label>
                    <div class="value">{{ $vente->created_at->format('d/m/Y') }}</div>
                </div>
            </div>
        </div>

        <!-- Détails financiers -->
        <div class="section">
            <div class="section-title">💰 DÉTAILS FINANCIERS</div>
            <div class="info-grid">
                <div class="info-item">
                    <label>Prix de vente</label>
                    <div class="value" style="color: #299cdb; font-size: 18px;">{{ number_format($vente->prix_vente, 0, ',', ' ') }} FCFA</div>
                </div>
                @if($vente->commission_agence)
                <div class="info-item">
                    <label>Commission d'agence</label>
                    <div class="value" style="color: #0ab39c;">
                        {{ number_format($vente->calculerCommission(), 0, ',', ' ') }} FCFA
                        @if($vente->type_commission === 'pourcentage')
                            ({{ $vente->commission_agence }}%)
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Historique des paiements -->
        <div class="section">
            <div class="section-title">💳 HISTORIQUE DES PAIEMENTS</div>
            @if($vente->paiements->count() > 0)
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Méthode</th>
                            <th>Référence</th>
                            <th class="text-right">Montant</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($vente->paiements as $paiement)
                        <tr>
                            <td>{{ $paiement->date_paiement->format('d/m/Y') }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $paiement->methode_paiement)) }}</td>
                            <td>{{ $paiement->reference ?? '-' }}</td>
                            <td class="text-right"><strong>{{ number_format($paiement->montant, 0, ',', ' ') }} FCFA</strong></td>
                        </tr>
                        @if($paiement->notes)
                        <tr>
                            <td colspan="4" style="background: #f8f9fa; font-size: 11px; color: #666;">
                                <em>Note: {{ $paiement->notes }}</em>
                            </td>
                        </tr>
                        @endif
                        @endforeach
                    </tbody>
                </table>

                <!-- Récapitulatif -->
                <div class="summary-box">
                    <div class="row">
                        <span>Prix de vente:</span>
                        <span>{{ number_format($vente->prix_vente, 0, ',', ' ') }} FCFA</span>
                    </div>
                    <div class="row">
                        <span>Total payé:</span>
                        <span style="color: #0ab39c;">{{ number_format($vente->montantTotalPaye(), 0, ',', ' ') }} FCFA</span>
                    </div>
                    <div class="row">
                        <span>Reste à payer:</span>
                        <span style="color: {{ $vente->resteAPayer() > 0 ? '#f97316' : '#0ab39c' }};">
                            {{ number_format($vente->resteAPayer(), 0, ',', ' ') }} FCFA
                        </span>
                    </div>
                </div>
            @else
                <p style="text-align: center; padding: 30px; color: #666;">Aucun paiement enregistré</p>
            @endif
        </div>

        <!-- Dates importantes -->
        <div class="section">
            <div class="section-title">📅 DATES IMPORTANTES</div>
            <div class="info-grid">
                @if($vente->date_visite)
                <div class="info-item">
                    <label>Date de visite</label>
                    <div class="value">{{ $vente->date_visite->format('d/m/Y à H:i') }}</div>
                </div>
                @endif
                @if($vente->date_signature)
                <div class="info-item">
                    <label>Date de signature</label>
                    <div class="value">{{ $vente->date_signature->format('d/m/Y') }}</div>
                </div>
                @endif
                @if($vente->date_vente)
                <div class="info-item">
                    <label>Date de vente</label>
                    <div class="value">{{ $vente->date_vente->format('d/m/Y') }}</div>
                </div>
                @endif
                @if($vente->date_finalisation)
                <div class="info-item">
                    <label>Date de finalisation</label>
                    <div class="value">{{ $vente->date_finalisation->format('d/m/Y à H:i') }}</div>
                </div>
                @endif
            </div>
        </div>

        <!-- Notes -->
        @if($vente->notes || $vente->note_admin)
        <div class="section">
            <div class="section-title">📝 NOTES</div>
            @if($vente->notes)
            <div style="padding: 15px; background: #f8f9fa; border-radius: 5px; margin-bottom: 10px;">
                <strong>Notes générales:</strong><br>
                {{ $vente->notes }}
            </div>
            @endif
            @if($vente->note_admin)
            <div style="padding: 15px; background: #fef3c7; border-radius: 5px;">
                <strong>Notes administratives:</strong><br>
                {{ $vente->note_admin }}
            </div>
            @endif
        </div>
        @endif

        <!-- Signatures -->
        <div class="signature-section">
            <div class="signature-box">
                <div class="title">L'AGENCE</div>
                <div class="line"></div>
                <div style="margin-top: 10px; font-size: 12px;">Signature et cachet</div>
            </div>
            <div class="signature-box">
                <div class="title">LE CLIENT</div>
                <div class="line"></div>
                <div style="margin-top: 10px; font-size: 12px;">Signature</div>
            </div>
        </div>

        <!-- Pied de page -->
        <div class="footer">
            <p>Ce document a été généré automatiquement le {{ now()->format('d/m/Y à H:i') }}</p>
            <p style="margin-top: 5px;">Référence: VENTE-{{ str_pad($vente->id, 6, '0', STR_PAD_LEFT) }}</p>
        </div>
    </div>

    <script>
        // Imprimer automatiquement si le paramètre print est présent
        if (window.location.search.includes('print=1')) {
            window.onload = function() {
                window.print();
            };
        }
    </script>
</body>
</html>
