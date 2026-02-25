@extends('backend.layouts.master')

@section('title')
   Rapport Propriétaire
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-file-invoice-dollar"></i> Rapport Propriétaire - {{ $proprietaire->username }}
            </h1>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('backend.rapports.proprietaire') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Retour à la Liste des Propriétaires
            </a>
            <button type="button" class="btn btn-primary btn-sm" onclick="window.print()">
                <i class="fas fa-print"></i> Imprimer
            </button>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('backend.rapports.proprietaire') }}" class="row g-3 text-center" >
                <!-- Hidden input pour passer le proprietaire_id -->
                <input type="hidden" name="proprietaire_id" value="{{ $proprietaire->id }}">
                
                {{-- <div class="col-md-4">
                    <label for="proprietaire_id" class="form-label">Propriétaire</label>
                    <select name="proprietaire_id" id="proprietaire_id" class="form-select">
                        <option value="">-- Sélectionner --</option>
                        @foreach($proprietaires as $prop)
                            <option value="{{ $prop->id }}" @selected($prop->id == $proprietaire->id)>
                                {{ $prop->username }}
                            </option>
                        @endforeach
                    </select>
                </div> --}}
                
                <div class="col-md-6">
                    <label for="date_debut" class="form-label">Date début</label>
                    <input type="date" name="date_debut" id="date_debut" class="form-control"
                        value="{{ $dateDebut->format('Y-m-d') }}">
                </div>
                
                <div class="col-md-6">
                    <label for="date_fin" class="form-label">Date fin</label>
                    <input type="date" name="date_fin" id="date_fin" class="form-control"
                        value="{{ $dateFin->format('Y-m-d') }}">
                </div>
                
                <div class="col-12">
                    <button type="submit" class="btn btn-primary w-50 mx-auto">
                         <i class="fas fa-search"></i> Filtrer
                    </button>
                    {{-- <a href="{{ route('backend.rapports.proprietaire') }}" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Réinitialiser
                    </a> --}}
                </div>
            </form>
        </div>
    </div>

    <!-- Résumé général -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-primary">
                <div class="card-body">
                    <div class="text-primary font-weight-bold text-uppercase mb-1">Total Encaissé</div>
                    <div class="h3 mb-0">
                        {{ number_format($rapport['total_brut_encaisse'], 0, ',', ' ') }} F
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-left-danger">
                <div class="card-body">
                    <div class="text-danger font-weight-bold text-uppercase mb-1">Commission Agence</div>
                    <div class="h3 mb-0">
                        {{ number_format($rapport['total_commission_agence'], 0, ',', ' ') }} F
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-left-warning">
                <div class="card-body">
                    <div class="text-warning font-weight-bold text-uppercase mb-1">Charges</div>
                    <div class="h3 mb-0">
                        {{ number_format($rapport['total_charges'], 0, ',', ' ') }} F
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-left-success">
                <div class="card-body">
                    <div class="text-success font-weight-bold text-uppercase mb-1">Revenu Net</div>
                    <div class="h3 mb-0">
                        {{ number_format($rapport['revenue_net'], 0, ',', ' ') }} F
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Détail par bien -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-home"></i> Détail par Bien ({{ $rapport['nombre_biens'] }} biens)
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr class="table-light">
                            <th>Bien</th>
                            <th>Adresse</th>
                            <th class="text-end">Loyers Encaissés</th>
                            <th class="text-end">Ventes Encaissées</th>
                            <th class="text-end">Total Brut</th>
                            <th class="text-end">Commission</th>
                            <th class="text-end">Charges</th>
                            <th class="text-end">Revenu Net</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rapport['biens'] as $bien)
                        <tr>
                            <td>
                                <a href="{{ route('backend.annonces.show', $bien['bien']) }}" class="text-dark text-decoration-none" title="Voir le détail du bien">
                                    <strong>{{ $bien['bien']->titre ?? 'N/A' }}</strong>
                                </a><br>
                                <small class="text-muted">{{ $bien['type_bien'] }}</small>
                                <span class="badge bg-primary">{{ $bien['type_transaction'] }}</span>
                            </td>
                            <td>{{ $bien['adresse'] }}</td>
                            <td class="text-end">
                                {{ number_format($bien['encaissement_loyers']['total'], 0, ',', ' ') }} F
                            </td>
                            <td class="text-end">
                                {{ number_format($bien['encaissement_ventes']['total'], 0, ',', ' ') }} F
                            </td>
                            <td class="text-end font-weight-bold">
                                {{ number_format($bien['total_brut_encaisse'], 0, ',', ' ') }} F
                            </td>
                            <td class="text-end text-danger">
                                {{ number_format($bien['total_commission_agence'], 0, ',', ' ') }} F
                            </td>
                            <td class="text-end text-warning">
                                {{ number_format($bien['total_charges'], 0, ',', ' ') }} F
                            </td>
                            <td class="text-end text-success font-weight-bold">
                                {{ number_format($bien['revenue_net'], 0, ',', ' ') }} F
                            </td>
                        </tr>
                        @if($bien['charges']->isNotEmpty())
                        <tr class="table-light">
                            <td colspan="8">
                                <small class="text-muted">
                                    <strong>Charges détail :</strong>
                                    @foreach($bien['charges'] as $charge)
                                        <br>
                                        • {{ $charge->type_charge_libelle }}: 
                                        {{ number_format($charge->montant, 0, ',', ' ') }} F
                                        @if($charge->description)
                                            ({{ $charge->description }})
                                        @endif
                                    @endforeach
                                </small>
                            </td>
                        </tr>
                        @endif
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                Aucun bien trouvé pour cette période
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Résumé des charges -->
    @if($rapport['detail_charges']['nombre_charges'] > 0)
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-tools"></i> Résumé des Charges
                ({{ $rapport['detail_charges']['nombre_charges'] }} charges)
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                @foreach($rapport['detail_charges']['par_type'] as $type => $montant)
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="text-muted text-uppercase mb-1">
                                @switch($type)
                                    @case('maintenance')
                                        <i class="fas fa-wrench"></i> Maintenance
                                        @break
                                    @case('reparation')
                                        <i class="fas fa-hammer"></i> Réparation
                                        @break
                                    @case('taxe')
                                        <i class="fas fa-percent"></i> Taxe
                                        @break
                                    @default
                                        <i class="fas fa-ellipsis-h"></i> Autre
                                @endswitch
                            </div>
                            <div class="h4 mb-0">
                                {{ number_format($montant, 0, ',', ' ') }} F
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Section de calcul -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-calculator"></i> Calcul du Revenu Net
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <table class="table table-sm">
                        <tr>
                            <td><strong>Total Encaissé (Loyers + Ventes)</strong></td>
                            <td class="text-end" width="30%">
                                <strong>{{ number_format($rapport['total_brut_encaisse'], 0, ',', ' ') }} F</strong>
                            </td>
                        </tr>
                        <tr class="table-light">
                            <td><strong>- Commission Agence</strong></td>
                            <td class="text-end text-danger">
                                ({{ number_format($rapport['total_commission_agence'], 0, ',', ' ') }} F)
                            </td>
                        </tr>
                        <tr class="table-light">
                            <td><strong>- Total des Charges</strong></td>
                            <td class="text-end text-warning">
                                ({{ number_format($rapport['total_charges'], 0, ',', ' ') }} F)
                            </td>
                        </tr>
                        <tr class="table-success">
                            <td><strong class="h5">= REVENU NET DU PROPRIÉTAIRE</strong></td>
                            <td class="text-end text-success">
                                <strong class="h5">{{ number_format($rapport['revenue_net'], 0, ',', ' ') }} F</strong>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-4">
                    <div class="alert alert-info">
                        <strong>Période :</strong><br>
                        {{ $rapport['periode'] }}
                        <hr>
                        <strong>Propriétaire :</strong><br>
                        {{ $proprietaire->username }}
                        <hr>
                        <strong>Nombre de biens :</strong><br>
                        {{ $rapport['nombre_biens'] }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section des Versements -->
    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-money-check-alt"></i> Versements au Propriétaire
            </h5>
            <div class="btn-group" role="group">
                {{-- <a href="{{ route('backend.versements.index') }}" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-list"></i> Liste
                </a> --}}
                <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#modalVersement">
                    <i class="fas fa-plus"></i> Ajouter versement
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card border-left-info">
                        <div class="card-body">
                            <div class="text-info font-weight-bold text-uppercase mb-1">Montant à Verser</div>
                            <div class="h3 mb-0">
                                {{ number_format($rapport['revenue_net'], 0, ',', ' ') }} F
                            </div>
                            <small class="text-muted">
                                Revenue net de la période
                            </small>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card border-left-success">
                        <div class="card-body">
                            <div class="text-success font-weight-bold text-uppercase mb-1">Montant Versé</div>
                            <div class="h3 mb-0">
                                {{ number_format($rapport['montant_total_verse'], 0, ',', ' ') }} F
                            </div>
                            <small class="text-muted">
                                {{ $rapport['versements']->where('statut', 'effectue')->count() }} versement(s)
                            </small>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card border-left-primary">
                        <div class="card-body">
                            <div class="text-primary font-weight-bold text-uppercase mb-1">Reste à Verser</div>
                            <div class="h3 mb-0">
                                {{ number_format($rapport['reste_a_verser'], 0, ',', ' ') }} F
                            </div>
                            <small class="text-muted">
                                Montant restant à recevoir
                            </small>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card border-left-{{ $rapport['statut_versement']['badge'] }}">
                        <div class="card-body">
                            <div class="font-weight-bold text-uppercase mb-1">Statut</div>
                            <div class="h5 mb-0">
                                <span class="badge bg-{{ $rapport['statut_versement']['badge'] }}">
                                    {{ $rapport['statut_versement']['label'] }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($rapport['versements']->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover table-sm">
                    <thead>
                        <tr class="table-light">
                            <th>Date</th>
                            <th>Période</th>
                            <th>Montant</th>
                            <th>Mode</th>
                            <th>Référence</th>
                            <th>Statut</th>
                            <th>Notes</th>
                            <th class="text-center" style="width: 150px;">Actions</th>
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
                            <td class="text-end">{{ number_format($versement->montant, 0, ',', ' ') }} F</td>
                            <td>
                                <small class="badge bg-light text-dark">{{ $versement->mode_versement }}</small>
                            </td>
                            <td><small>{{ $versement->reference ?? '-' }}</small></td>
                            <td>
                                @if($versement->statut === 'effectue')
                                    <span class="badge bg-success">Effectué</span>
                                @elseif($versement->statut === 'en_attente')
                                    <span class="badge bg-warning">En attente</span>
                                @elseif($versement->statut === 'annule')
                                    <span class="badge bg-danger">Annulé</span>
                                @else
                                    <span class="badge bg-secondary">{{ $versement->statut }}</span>
                                @endif
                            </td>
                            <td><small class="text-muted">{{ $versement->notes ?? '-' }}</small></td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('backend.versements.edit', $versement) }}" class="btn btn-sm btn-outline-primary" title="Éditer">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($versement->statut !== 'annule')
                                    <form action="{{ route('backend.versements.cancel', $versement) }}" method="POST" style="display: inline;" onsubmit="return confirm('Annuler ce versement ?');">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-outline-warning" title="Annuler">
                                            <i class="fas fa-times-circle"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                Aucun versement enregistré pour cette période.
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Ajouter Versement -->
<div class="modal fade" id="modalVersement" tabindex="-1" aria-labelledby="modalVersementLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-white" id="modalVersementLabel">
                    <i class="fas fa-money-check-alt"></i> Enregistrer un Versement
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formVersement" action="{{ route('backend.versements.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <!-- Propriétaire (hidden) -->
                    <input type="hidden" name="proprietaire_id" value="{{ $proprietaire->id }}">

                    <!-- Montant à Verser (hidden + affichage) -->
                    <input type="hidden" id="montantAVerserModal" value="{{ $rapport['revenue_net'] }}">
                    <h4 class="py-2">Montant à Verser (F) <span class="text-info">Net</span> : <strong>{{ number_format($rapport['revenue_net'], 0, ',', ' ') }}</strong></h4>

                    <!-- Type de Versement - Boutons Radio -->
                    <div class="mb-3">
                        <label class="form-label">Type de Versement <span class="text-danger">*</span></label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="type_versement" id="typeTotal" value="total">
                            <label class="btn btn-outline-primary w-50" for="typeTotal">
                                <i class="fas fa-check-circle me-1"></i> Montant Total
                            </label>
                            
                            <input type="radio" class="btn-check" name="type_versement" id="typePartiel" value="partiel">
                            <label class="btn btn-outline-primary w-50" for="typePartiel">
                                <i class="fas fa-minus-circle me-1"></i> Partiel
                            </label>
                        </div>
                    </div>

                    <!-- Montant -->
                    <div class="mb-3">
                        <label for="montantModal" class="form-label">Montant (F) <span class="text-danger">*</span></label>
                        <input type="number" name="montant" id="montantModal" class="form-control" 
                            placeholder="0" step="1" required disabled>
                    </div>

                    <!-- Montant Restant à Payer (read-only) -->
                    <div class="mb-3">
                        <label for="montantRestantModal" class="form-label">Montant Restant à Payer (F)</label>
                        <input type="number" id="montantRestantModal" class="form-control" 
                            value="{{ $rapport['revenue_net'] }}" readonly style="background-color: #f8f9fa;">
                    </div>

                    <!-- Date du versement -->
                    <div class="mb-3">
                        <label for="dateVersementModal" class="form-label">Date du versement <span class="text-danger">*</span></label>
                        <input type="date" name="date_versement" id="dateVersementModal" class="form-control" 
                            value="{{ now()->format('Y-m-d') }}" required>
                    </div>

                    <!-- Début de période -->
                    <div class="mb-3">
                        <label for="dateDebutModal" class="form-label">Début de période</label>
                        <input type="date" name="date_debut" id="dateDebutModal" class="form-control" 
                            value="{{ $dateDebut->format('Y-m-d') }}">
                    </div>

                    <!-- Fin de période -->
                    <div class="mb-3">
                        <label for="dateFinModal" class="form-label">Fin de période</label>
                        <input type="date" name="date_fin" id="dateFinModal" class="form-control" 
                            value="{{ $dateFin->format('Y-m-d') }}">
                    </div>

                    <!-- Mode de versement -->
                    <div class="mb-3">
                        <label for="modeVersementModal" class="form-label">Mode de versement <span class="text-danger">*</span></label>
                        <select name="mode_versement" id="modeVersementModal" class="form-select" required>
                            <option value="">-- Sélectionner --</option>
                            <option value="virement" selected>Virement</option>
                            <option value="chèque">Chèque</option>
                            <option value="espèces">Espèces</option>
                            <option value="mobile_money">Mobile Money</option>
                            <option value="autre">Autre</option>
                        </select>
                    </div>

                    <!-- Référence -->
                    <div class="mb-3">
                        <label for="referenceModal" class="form-label">Référence</label>
                        <input type="text" name="reference" id="referenceModal" class="form-control" 
                            placeholder="N° virement, chèque...">
                    </div>

                    <!-- Statut - Calculé automatiquement -->
                    <input type="hidden" name="statut" value="">

                    <!-- Notes -->
                    <div class="mb-3">
                        <label for="notesModal" class="form-label">Notes</label>
                        <textarea name="notes" id="notesModal" class="form-control" 
                            rows="2" placeholder="Notes supplémentaires..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Annuler
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    @media print {
        .btn, form, .card-header .d-inline {
            display: none !important;
        }
        .card {
            page-break-inside: avoid;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const formVersement = document.getElementById('formVersement');
        const modalVersement = document.getElementById('modalVersement');
        
        // Éléments du formulaire
        const typeTotal = document.getElementById('typeTotal');
        const typePartiel = document.getElementById('typePartiel');
        const montantInput = document.getElementById('montantModal');
        const montantAVerserInput = document.getElementById('montantAVerserModal');
        const montantRestantInput = document.getElementById('montantRestantModal');
        
        // Gestion du changement de type - Total
        typeTotal.addEventListener('change', function() {
            if (this.checked) {
                const montantAVerser = parseInt(montantAVerserInput.value) || 0;
                montantInput.value = montantAVerser;
                montantInput.disabled = true;
                montantInput.classList.add('bg-light');
                montantInput.style.cursor = 'not-allowed';
                updateMontantRestant();
            }
        });
        
        // Gestion du changement de type - Partiel
        typePartiel.addEventListener('change', function() {
            if (this.checked) {
                montantInput.disabled = false;
                montantInput.classList.remove('bg-light');
                montantInput.style.cursor = 'auto';
                montantInput.value = '';
                updateMontantRestant();
                setTimeout(() => montantInput.focus(), 100);
            }
        });
        
        // Gestion de la saisie du montant
        montantInput.addEventListener('input', function() {
            updateMontantRestant();
        });
        
        // Fonction pour mettre à jour le montant restant
        function updateMontantRestant() {
            const montantAVerser = parseInt(montantAVerserInput.value) || 0;
            const montantSaisi = parseInt(montantInput.value) || 0;
            const montantRestant = montantAVerser - montantSaisi;
            montantRestantInput.value = montantRestant >= 0 ? montantRestant : 0;
        }
        
        // Réinitialiser le formulaire à l'ouverture du modal
        const modalElement = document.getElementById('modalVersement');
        modalElement.addEventListener('show.bs.modal', function() {
            formVersement.reset();
            typeTotal.checked = false;
            typePartiel.checked = false;
            montantInput.disabled = true;
            montantInput.classList.add('bg-light');
            montantInput.style.cursor = 'not-allowed';
            const montantAVerser = parseInt(montantAVerserInput.value) || 0;
            montantRestantInput.value = montantAVerser;
        });
        
        // Soumettre le formulaire en AJAX
        formVersement.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Vérifier que le type est sélectionné
            if (!typeTotal.checked && !typePartiel.checked) {
                alert('Veuillez sélectionner un type de versement');
                return;
            }
            
            // Vérifier que le montant est saisi
            if (!montantInput.value || parseInt(montantInput.value) <= 0) {
                alert('Veuillez saisir un montant valide');
                return;
            }
            
            // Activer le champ montant temporairement pour la soumission (les champs disabled ne sont pas envoyés)
            montantInput.disabled = false;
            
            const formData = new FormData(this);
            const modal = bootstrap.Modal.getInstance(modalVersement);
            
            fetch(formVersement.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Fermer le modal
                    modal.hide();
                    
                    // Afficher un message de succès avec toast
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-success alert-dismissible fade show d-flex align-items-center';
                    alertDiv.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 350px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);';
                    alertDiv.innerHTML = `
                        <i class="fas fa-check-circle me-3" style="font-size: 24px;"></i>
                        <div>
                            <strong>Succès!</strong>
                            <div>Versement enregistré avec succès</div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `;
                    document.body.appendChild(alertDiv);
                    
                    // Auto-fermer après 4 secondes
                    setTimeout(() => {
                        alertDiv.remove();
                    }, 4000);
                    
                    // Recharger la page après 2 secondes
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                } else {
                    alert('Erreur: ' + (data.message || 'Une erreur est survenue'));
                    // Réactiver le disabled si erreur
                    if (typeTotal.checked) {
                        montantInput.disabled = true;
                    }
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Erreur lors de l\'enregistrement');
                // Réactiver le disabled si erreur
                if (typeTotal.checked) {
                    montantInput.disabled = true;
                }
            });
        });
    });
</script>
@endsection


