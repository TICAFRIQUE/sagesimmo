<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Reçu de Paiement #{{ $paiement->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 9px;
            color: #333;
            line-height: 1.3;
            padding: 15px;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #0d6efd;
        }
        .header h1 {
            color: #0d6efd;
            font-size: 20px;
            margin-bottom: 3px;
        }
        .header p {
            color: #666;
            font-size: 8px;
        }
        .recu-number {
            text-align: right;
            margin-bottom: 12px;
            font-size: 10px;
        }
        .recu-number strong {
            color: #0d6efd;
        }
        .info-section {
            margin-bottom: 12px;
        }
        .info-section h3 {
            background-color: #f8f9fa;
            padding: 4px 8px;
            margin-bottom: 6px;
            font-size: 10px;
            color: #0d6efd;
            border-left: 3px solid #0d6efd;
        }
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }
        .info-row {
            display: table-row;
        }
        .info-label {
            display: table-cell;
            padding: 3px 6px;
            font-weight: bold;
            width: 40%;
            background-color: #f8f9fa;
            font-size: 8px;
        }
        .info-value {
            display: table-cell;
            padding: 3px 6px;
            border-bottom: 1px solid #dee2e6;
            font-size: 8px;
        }
        .payment-details {
            background-color: #e7f3ff;
            padding: 10px;
            margin: 12px 0;
            border-radius: 4px;
            border: 2px solid #0d6efd;
        }
        .payment-details .amount {
            font-size: 18px;
            font-weight: bold;
            color: #0d6efd;
            text-align: center;
            margin: 6px 0;
        }
        .payment-details .amount-label {
            text-align: center;
            color: #666;
            margin-bottom: 3px;
            font-size: 8px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0;
        }
        table th {
            background-color: #0d6efd;
            color: white;
            padding: 5px 6px;
            text-align: left;
            font-size: 8px;
        }
        table td {
            padding: 4px 6px;
            border-bottom: 1px solid #dee2e6;
            font-size: 8px;
        }
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #dee2e6;
            text-align: center;
            font-size: 7px;
            color: #666;
        }
        .signature-section {
            margin-top: 25px;
            display: table;
            width: 100%;
        }
        .signature-box {
            display: table-cell;
            width: 50%;
            text-align: center;
            padding: 8px;
            font-size: 8px;
        }
        .signature-box p {
            margin-top: 30px;
            border-top: 1px solid #333;
            padding-top: 3px;
            display: inline-block;
            width: 150px;
            font-size: 7px;
        }
        .badge {
            display: inline-block;
            padding: 2px 5px;
            border-radius: 2px;
            font-size: 7px;
            font-weight: bold;
        }
        .badge-success {
            background-color: #d4edda;
            color: #155724;
        }
        .badge-info {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 80px;
            color: rgba(13, 110, 253, 0.04);
            z-index: -1;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="watermark">PAYÉ</div>
    
    <!-- En-tête -->
    <div class="header">
        <h1>🏠 SAGE IMMO</h1>
        <p>Agence Immobilière - Gestion de locations et ventes</p>
        <p>Email: contact@sageimmo.com | Téléphone: +226 XX XX XX XX</p>
    </div>

    <!-- Numéro de reçu -->
    <div class="recu-number">
        <strong>REÇU DE PAIEMENT N° {{ str_pad($paiement->id, 6, '0', STR_PAD_LEFT) }}</strong><br>
        <span style="color: #666;">Émis le {{ \Carbon\Carbon::now()->format('d/m/Y à H:i') }}</span>
    </div>

    <!-- Informations du locataire -->
    <div class="info-section">
        <h3>📋 Informations du locataire</h3>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Nom complet</div>
                <div class="info-value">{{ $locataire->username }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Email</div>
                <div class="info-value">{{ $locataire->email }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Téléphone</div>
                <div class="info-value">{{ $locataire->phone ?? 'N/A' }}</div>
            </div>
        </div>
    </div>

    <!-- Informations du bien -->
    <div class="info-section">
        <h3>🏡 Informations du bien loué</h3>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Désignation</div>
                <div class="info-value">{{ $bien->titre }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Localisation</div>
                <div class="info-value">{{ $bien->quartier }}, {{ $bien->ville }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Type de bien</div>
                <div class="info-value">{{ $bien->typeBien->nom ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Loyer mensuel</div>
                <div class="info-value">{{ number_format($location->loyer_mensuel, 0, ',', ' ') }} FCFA</div>
            </div>
        </div>
    </div>

    <!-- Détails du paiement -->
    <div class="payment-details">
        <div class="payment-details-label" style="text-align: center; margin-bottom: 10px;">
            <strong>💰 DÉTAILS DU PAIEMENT</strong>
        </div>
        
        <table style="margin: 8px 0; border: none;">
            <tr>
                <td style="border: none; padding: 3px;"><strong>Date du paiement :</strong></td>
                <td style="border: none; padding: 3px;">{{ \Carbon\Carbon::parse($paiement->date_paiement)->format('d/m/Y à H:i') }}</td>
            </tr>
            @if($echeance)
            <tr>
                <td style="border: none; padding: 3px;"><strong>Période concernée :</strong></td>
                <td style="border: none; padding: 3px;">{{ \Carbon\Carbon::parse($echeance->date_echeance)->format('F Y') }}</td>
            </tr>
            @endif
            <tr>
                <td style="border: none; padding: 3px;"><strong>Type de paiement :</strong></td>
                <td style="border: none; padding: 3px;">
                    <span class="badge badge-info">{{ ucfirst(str_replace('_', ' ', $paiement->type_paiement)) }}</span>
                </td>
            </tr>
            <tr>
                <td style="border: none; padding: 3px;"><strong>Méthode de paiement :</strong></td>
                <td style="border: none; padding: 3px;">
                    <span class="badge badge-info">{{ ucfirst($paiement->methode_paiement) }}</span>
                </td>
            </tr>
            @if($paiement->reference)
            <tr>
                <td style="border: none; padding: 3px;"><strong>Référence :</strong></td>
                <td style="border: none; padding: 3px;">{{ $paiement->reference }}</td>
            </tr>
            @endif
        </table>

        <div class="amount">
            {{ number_format($paiement->montant, 0, ',', ' ') }} FCFA
        </div>
        <div class="payment-details-label" style="text-align: center; color: #28a745; font-weight: bold;">
            ✓ PAIEMENT REÇU ET ENREGISTRÉ
        </div>
    </div>

    <!-- Détails supplémentaires -->
    @if($paiement->commission_agence)
    <div class="info-section">
        <h3>📊 Détails financiers</h3>
        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th style="text-align: right;">Montant</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Montant payé par le locataire</td>
                    <td style="text-align: right;">{{ number_format($paiement->montant, 0, ',', ' ') }} FCFA</td>
                </tr>
                <tr>
                    <td>Commission agence ({{ $paiement->type_commission == 'pourcentage' ? $paiement->commission_agence . '%' : 'Montant fixe' }})</td>
                    <td style="text-align: right;">
                        @if($paiement->type_commission == 'pourcentage')
                            {{ number_format(($paiement->montant * $paiement->commission_agence / 100), 0, ',', ' ') }} FCFA
                        @else
                            {{ number_format($paiement->commission_agence, 0, ',', ' ') }} FCFA
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    @endif

    @if($paiement->notes)
    <div class="info-section">
        <h3>📝 Notes</h3>
        <p style="padding: 6px; background-color: #f8f9fa; border-left: 2px solid #0d6efd; font-size: 8px;">
            {{ $paiement->notes }}
        </p>
    </div>
    @endif

    <!-- Signatures -->
    <div class="signature-section">
        <div class="signature-box">
            <strong>Le locataire</strong>
            <p>Signature</p>
        </div>
        <div class="signature-box">
            <strong>L'agence</strong>
            <p>Signature et cachet</p>
        </div>
    </div>

    <!-- Pied de page -->
    <div class="footer">
        <p><strong>SAGE IMMO</strong> - Votre partenaire immobilier de confiance</p>
        <p>Ce reçu fait foi de paiement et doit être conservé précieusement.</p>
        <p style="margin-top: 5px; font-size: 6px;">
            Document généré automatiquement le {{ \Carbon\Carbon::now()->format('d/m/Y à H:i') }}
        </p>
    </div>
</body>
</html>
