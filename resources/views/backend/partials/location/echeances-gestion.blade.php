{{--
    Partial: Gestion complète des échéances d'une location
    
    Usage:
    @include('backend.partials.location.echeances-gestion', [
        'location' => $location,           // Required: Location model (avec echeances.paiements chargés)
        'dateDebut' => $dateDebut ?? null,  // Optional: Carbon - filtre début période
        'dateFin' => $dateFin ?? null,      // Optional: Carbon - filtre fin période
        'suffix' => $location->id,          // Optional: suffixe unique pour IDs modals
    ])
--}}

@php
// Suffixe unique pour éviter les conflits d'ID dans les modals
    $_suffix = $suffix ?? $location->id;
    
    // Récupérer les échéances triées
    $_echeances = $location->echeances->sortBy('date_echeance');
    
    // Filtrer par période si dates fournies
    if (!empty($dateDebut) && !empty($dateFin)) {
        $_echeances = $_echeances->filter(function($e) use ($dateDebut, $dateFin) {
            return $e->date_echeance->between($dateDebut, $dateFin);
        });
    }
    
    // Statistiques
    $_totalDu = $_echeances->sum('montant_du');
    $_totalPaye = $_echeances->sum('montant_paye');
    $_totalRestant = $_totalDu - $_totalPaye;
    $_tauxPaiement = $_totalDu > 0 ? round(($_totalPaye / $_totalDu) * 100, 1) : 0;
    $_nbMoisPayes = $_echeances->filter(fn($e) => $e->montant_paye >= $e->montant_du)->count();
    
    $_nbEnRetard = $_echeances->filter(function($e) {
        $joursRetard = $e->date_echeance->isPast() ? (int)$e->date_echeance->diffInDays(now()) : 0;
        return $e->date_echeance->isPast() && $e->montant_paye < $e->montant_du && $joursRetard > 0 && $joursRetard <= 30;
    })->count();
    
    $_nbImpayees = $_echeances->filter(function($e) {
        return $e->date_echeance->isPast() && $e->montant_paye < $e->montant_du && $e->date_echeance->diffInDays(now()) > 30;
    })->count();
    
    $_montantImpaye = $_echeances->filter(function($e) {
        return $e->date_echeance->isPast() && $e->montant_paye < $e->montant_du;
    })->sum(function($e) {
        return $e->montant_du - $e->montant_paye;
    });
    
    $_prochaineEcheance = $_echeances
        ->filter(fn($e) => $e->montant_paye < $e->montant_du && $e->date_echeance->gte(now()))
        ->sortBy('date_echeance')
        ->first();
@endphp

{{-- Alerte génération nécessaire --}}
@if($location->statut === 'actif' && $location->doitGenererNouvellesEcheances())
    <div class="alert alert-warning py-2 mb-2" style="font-size: 12px;">
        <i class="ri-alert-line me-1"></i>
        <strong>Attention!</strong> Il reste moins de 3 mois d'échéances à venir.
        <button class="btn btn-sm btn-warning ms-2 no-print" data-bs-toggle="modal" data-bs-target="#genererEcheancesModal_{{ $_suffix }}">
            Générer maintenant
        </button>
    </div>
@endif

{{-- Alerte résiliation --}}
@if($location->statut === 'resilie')
    <div class="alert alert-danger py-2 mb-2" style="font-size: 12px;">
        <i class="ri-close-circle-line me-1"></i>
        <strong>Contrat résilié.</strong> Les paiements ne sont plus autorisés.
        @if($location->note_admin)
            — <em>{{ $location->note_admin }}</em>
        @endif
    </div>
@endif

{{-- Statistiques --}}
<div class="row mt-2 mb-2">
    <div class="col-md-3 col-6 mb-2">
        <div class="card border-info mb-0">
            <div class="card-body text-center py-2">
                <small class="text-muted d-block"><i class="ri-calendar-check-line"></i> Mois payés</small>
                <h5 class="text-info mb-0">{{ $_nbMoisPayes }}<small class="text-muted">/{{ $_echeances->count() }}</small></h5>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6 mb-2">
        <div class="card border-success mb-0">
            <div class="card-body text-center py-2">
                <small class="text-muted d-block"><i class="ri-money-dollar-circle-line"></i> Total payé</small>
                <h5 class="text-success mb-0">{{ number_format($_totalPaye, 0, ',', ' ') }}</h5>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6 mb-2">
        <div class="card border-warning mb-0">
            <div class="card-body text-center py-2">
                <small class="text-muted d-block"><i class="ri-alarm-warning-line"></i> En retard</small>
                <h5 class="text-warning mb-0">{{ $_nbEnRetard }}</h5>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6 mb-2">
        <div class="card border-danger mb-0">
            <div class="card-body text-center py-2">
                <small class="text-muted d-block"><i class="ri-alert-line"></i> Impayés</small>
                <h5 class="text-danger mb-0">{{ $_nbImpayees }}</h5>
            </div>
        </div>
    </div>
</div>

{{-- Prochaine échéance --}}
@if($_prochaineEcheance)
    <div class="alert alert-info py-2 mb-2">
        <div class="d-flex align-items-center">
            <i class="ri-calendar-event-line me-2" style="font-size: 20px;"></i>
            <div>
                <strong>Prochaine échéance :</strong>
                {{ $_prochaineEcheance->date_echeance->format('d/m/Y') }}
                <span class="ms-2">
                    <strong>Montant :</strong> {{ number_format($_prochaineEcheance->montant_du, 0, ',', ' ') }} FCFA
                </span>
                @if($_prochaineEcheance->montant_paye > 0)
                    <span class="ms-2 text-success">
                        ({{ number_format($_prochaineEcheance->montant_paye, 0, ',', ' ') }} FCFA déjà payés)
                    </span>
                @endif
            </div>
        </div>
    </div>
@endif

{{-- Boutons d'action --}}
@if($location->statut === 'actif')
    <div class="row g-2 mt-2 mb-2 no-print">
        <div class="col-md-6">
            <button class="btn btn-success btn-sm w-100" data-bs-toggle="modal" data-bs-target="#genererEcheancesModal_{{ $_suffix }}">
                <i class="ri-add-line me-1"></i>Générer nouvelles échéances
            </button>
        </div>
        <div class="col-md-6">
            <button class="btn btn-warning btn-sm w-100" data-bs-toggle="modal" data-bs-target="#resilierModal_{{ $_suffix }}">
                <i class="ri-close-line me-1"></i>Résilier la location
            </button>
        </div>
    </div>
@endif

{{-- Tableau des échéances --}}
@if($_echeances->count() > 0)
    <div class="table-responsive mt-2">
        <table class="table table-sm table-hover mb-0" style="font-size: 12px;">
            <thead class="table-light">
                <tr>
                    <th class="no-print" style="width: 30px;"></th>
                    <th style="width: 30px;">#</th>
                    <th>Date Échéance</th>
                    <th class="text-end">Montant Dû</th>
                    <th class="text-end">Montant Payé</th>
                    <th class="text-end">Reste</th>
                    <th class="text-center">Retard</th>
                    <th class="text-center">Statut</th>
                    <th class="text-center no-print" style="width: 120px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($_echeances as $idx => $_ech)
                    @php
                        $_estComplet = $_ech->montant_paye >= $_ech->montant_du;
                        $_dateDepassee = $_ech->date_echeance->isPast();
                        $_joursRetard = $_dateDepassee ? (int)$_ech->date_echeance->diffInDays(now()) : 0;
                        $_resteAPayer = $_ech->montant_du - $_ech->montant_paye;
                        $_hasPaiements = $_ech->paiements && $_ech->paiements->count() > 0;
                        
                        if ($_estComplet) {
                            $_statutLabel = 'Payé';
                            $_badgeClass = 'bg-success';
                            $_rowClass = 'table-success-soft';
                        } elseif ($_dateDepassee && $_joursRetard > 30) {
                            $_statutLabel = 'Impayé (' . $_joursRetard . 'j)';
                            $_badgeClass = 'bg-danger';
                            $_rowClass = 'table-danger';
                        } elseif ($_dateDepassee && $_joursRetard > 0) {
                            $_statutLabel = 'En retard (' . $_joursRetard . 'j)';
                            $_badgeClass = 'bg-danger';
                            $_rowClass = 'table-warning';
                        } elseif ($_ech->montant_paye > 0) {
                            $_statutLabel = 'Partiel';
                            $_badgeClass = 'bg-info';
                            $_rowClass = '';
                        } else {
                            $_statutLabel = 'À échéance';
                            $_badgeClass = 'bg-secondary';
                            $_rowClass = '';
                        }
                    @endphp
                    <tr class="{{ $_rowClass }}">
                        {{-- Expand paiements --}}
                        <td class="no-print text-center">
                            @if($_hasPaiements)
                                <i class="ri-arrow-right-s-line btn-expand-ech" style="cursor:pointer; font-size: 16px;"
                                   onclick="toggleEchDetail(this, 'ech_detail_{{ $_suffix }}_{{ $_ech->id }}')" title="Voir les paiements"></i>
                            @endif
                        </td>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <i class="ri-calendar-line me-1 text-muted" style="font-size: 10px;"></i>
                            {{ $_ech->date_echeance->format('d/m/Y') }}
                        </td>
                        <td class="text-end">{{ number_format($_ech->montant_du, 0, ',', ' ') }} F</td>
                        <td class="text-end text-success">{{ number_format($_ech->montant_paye, 0, ',', ' ') }} F</td>
                        <td class="text-end {{ $_resteAPayer > 0 ? 'text-danger fw-bold' : '' }}">
                            {{ number_format($_resteAPayer, 0, ',', ' ') }} F
                        </td>
                        <td class="text-center">
                            @if($_joursRetard > 0)
                                <span class="badge bg-{{ $_joursRetard > 30 ? 'danger' : ($_joursRetard > 7 ? 'warning text-dark' : 'secondary') }}">
                                    {{ $_joursRetard }}j
                                </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge {{ $_badgeClass }}">{{ $_statutLabel }}</span>
                        </td>
                        <td class="text-center no-print">
                            @if(!$_estComplet && $location->statut === 'actif')
                                <button class="btn btn-primary btn-sm py-0 px-1" data-bs-toggle="modal"
                                        data-bs-target="#payerEcheanceModal_{{ $_suffix }}_{{ $_ech->id }}" title="Enregistrer un paiement">
                                    <i class="ri-wallet-line"></i> Payer
                                </button>
                            @elseif(!$_estComplet && $location->statut === 'resilie')
                                <span class="badge bg-secondary" style="font-size: 10px;"><i class="ri-lock-line"></i> Verrouillé</span>
                            @elseif($_estComplet)
                                <span class="text-success" style="font-size: 14px;"><i class="ri-checkbox-circle-fill"></i></span>
                            @endif
                        </td>
                    </tr>
                    
                    {{-- Ligne détail paiements (expandable) --}}
                    @if($_hasPaiements)
                        <tr class="echeance-detail-row" id="ech_detail_{{ $_suffix }}_{{ $_ech->id }}">
                            <td colspan="9" style="background-color: #f8f9fa; padding: 15px !important;">
                                <div class="ps-4">
                                    <h6 class="mb-2" style="font-size: 12px;">
                                        <i class="ri-history-line me-1"></i>Historique des paiements
                                    </h6>
                                    <table class="table table-sm table-bordered mb-0" style="font-size: 11px;">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Date</th>
                                                <th class="text-end">Montant</th>
                                                <th>Commission</th>
                                                <th>Méthode</th>
                                                <th>Référence</th>
                                                <th>Notes</th>
                                                <th class="text-center no-print" style="width: 60px;">Reçu</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($_ech->paiements as $_paiement)
                                                <tr>
                                                    <td>{{ \Carbon\Carbon::parse($_paiement->date_paiement)->format('d/m/Y H:i') }}</td>
                                                    <td class="text-end"><strong class="text-success">{{ number_format($_paiement->montant, 0, ',', ' ') }} F</strong></td>
                                                    <td>
                                                        @if($_paiement->commission_agence)
                                                            <span class="badge bg-warning">{{ number_format($_paiement->commission_agence, 0, ',', ' ') }} {{ $_paiement->type_commission == 'pourcentage' ? '%' : 'F' }}</span>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td><span class="badge bg-info">{{ ucfirst($_paiement->methode_paiement) }}</span></td>
                                                    <td>{{ $_paiement->reference ?? '-' }}</td>
                                                    <td>{{ $_paiement->notes ?? '-' }}</td>
                                                    <td class="text-center no-print">
                                                        <a href="{{ route('backend.locations.recu-paiement', $_paiement) }}"
                                                           class="btn btn-outline-primary py-0 px-1" style="font-size: 11px;" title="Reçu">
                                                            <i class="ri-file-download-line"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="alert alert-info mt-2 mb-0">
        <i class="ri-information-line me-1"></i>Aucune échéance sur cette période.
    </div>
@endif

{{-- ====== MODALS ====== --}}

{{-- Modals: Payer échéance --}}
@if($location->statut === 'actif')
    @foreach($_echeances as $_ech)
        @if($_ech->montant_paye < $_ech->montant_du)
            @php $_resteModal = $_ech->montant_du - $_ech->montant_paye; @endphp
            <div class="modal fade" id="payerEcheanceModal_{{ $_suffix }}_{{ $_ech->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <form action="{{ route('backend.locations.enregistrer-paiement-loyer', $_ech) }}" method="POST">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title"><i class="ri-wallet-line me-1"></i>Enregistrer paiement loyer</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="alert alert-info mb-3">
                                    <strong>Échéance :</strong> {{ $_ech->date_echeance->format('d/m/Y') }}<br>
                                    <strong>Montant dû :</strong> {{ number_format($_ech->montant_du, 0, ',', ' ') }} FCFA<br>
                                    <strong>Déjà payé :</strong> {{ number_format($_ech->montant_paye, 0, ',', ' ') }} FCFA<br>
                                    <strong>Reste à payer :</strong> {{ number_format($_resteModal, 0, ',', ' ') }} FCFA
                                </div>

                                <div class="mb-3">
                                    <label class="form-label d-block">Type de paiement</label>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input type-paiement-radio" type="radio"
                                               name="type_paiement_{{ $_suffix }}_{{ $_ech->id }}"
                                               id="paiement_total_{{ $_suffix }}_{{ $_ech->id }}"
                                               value="total" checked>
                                        <label class="form-check-label" for="paiement_total_{{ $_suffix }}_{{ $_ech->id }}">
                                            <i class="ri-money-dollar-circle-line me-1"></i>Montant total
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input type-paiement-radio" type="radio"
                                               name="type_paiement_{{ $_suffix }}_{{ $_ech->id }}"
                                               id="paiement_partiel_{{ $_suffix }}_{{ $_ech->id }}"
                                               value="partiel">
                                        <label class="form-check-label" for="paiement_partiel_{{ $_suffix }}_{{ $_ech->id }}">
                                            <i class="ri-percent-line me-1"></i>Montant partiel
                                        </label>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Montant du paiement <span class="text-danger">*</span></label>
                                    <input type="number" name="montant" class="form-control montant-paiement-input"
                                           id="montant_input_{{ $_suffix }}_{{ $_ech->id }}"
                                           data-montant-total="{{ $_resteModal }}"
                                           value="{{ $_resteModal }}" required step="1" min="1"
                                           max="{{ $_resteModal }}" readonly>
                                    <small class="text-muted">Maximum: {{ number_format($_resteModal, 0, ',', ' ') }} FCFA</small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Date du paiement <span class="text-danger">*</span></label>
                                    <input type="date" name="date_paiement" class="form-control" value="{{ date('Y-m-d') }}" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Méthode de paiement <span class="text-danger">*</span></label>
                                    <select name="methode_paiement" class="form-select" required>
                                        <option value="">Sélectionner une méthode</option>
                                        <option value="espèces">Espèces</option>
                                        <option value="virement">Virement bancaire</option>
                                        <option value="chèque">Chèque</option>
                                        <option value="carte_bancaire">Carte bancaire</option>
                                        <option value="mobile_money">Mobile Money</option>
                                        <option value="autre">Autre</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Référence du paiement</label>
                                    <input type="text" name="reference" class="form-control" placeholder="Ex: N° de transaction, chèque, etc.">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Notes</label>
                                    <textarea name="notes" class="form-control" rows="2"></textarea>
                                </div>

                                @if($location->commission_agence)
                                    <div class="alert alert-info">
                                        <i class="ri-information-line me-1"></i>
                                        La commission de l'agence sera automatiquement calculée
                                        ({{ $location->commission_agence }} {{ $location->type_commission == 'pourcentage' ? '%' : 'FCFA' }}).
                                    </div>
                                @endif
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="ri-save-line me-1"></i>Enregistrer
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    @endforeach
@endif

{{-- Modal: Générer nouvelles échéances --}}
@if($location->statut === 'actif')
    <div class="modal fade" id="genererEcheancesModal_{{ $_suffix }}" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('backend.locations.generer-nouvelles-echeances', $location) }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title"><i class="ri-add-line me-1"></i>Générer nouvelles échéances</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="ri-information-line me-1"></i>
                            Génération de nouvelles échéances mensuelles à partir de la dernière existante.
                        </div>
                        @php
                            $_derniereEcheance = $location->echeances()->orderBy('date_echeance', 'desc')->first();
                            $_echeancesFutures = $location->echeances()->where('date_echeance', '>', now())->count();
                        @endphp
                        @if($_derniereEcheance)
                            <div class="mb-3 bg-light p-3 rounded">
                                <p class="mb-2"><strong>Dernière échéance :</strong> {{ $_derniereEcheance->date_echeance->format('d/m/Y') }}</p>
                                <p class="mb-2">
                                    <strong>Échéances à venir :</strong>
                                    <span class="badge {{ $_echeancesFutures < 3 ? 'bg-danger' : ($_echeancesFutures < 6 ? 'bg-warning text-dark' : 'bg-success') }}">
                                        {{ $_echeancesFutures }} mois
                                    </span>
                                </p>
                                <p class="mb-0"><strong>Montant mensuel :</strong> {{ number_format($location->loyer_mensuel, 0, ',', ' ') }} FCFA</p>
                                @if($_echeancesFutures >= 6)
                                    <div class="alert alert-success mt-2 mb-0">
                                        <i class="ri-information-line me-1"></i>
                                        Vous avez déjà {{ $_echeancesFutures }} mois d'échéances programmées.
                                    </div>
                                @endif
                            </div>
                        @endif
                        <div class="mb-3">
                            <label class="form-label">Nombre de mois à générer <span class="text-danger">*</span></label>
                            <select name="nombre_mois" class="form-select" required>
                                <option value="">Choisir...</option>
                                <option value="3">3 mois</option>
                                <option value="6" selected>6 mois</option>
                                <option value="12">12 mois (1 an)</option>
                                <option value="24">24 mois (2 ans)</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-success">
                            <i class="ri-add-line me-1"></i>Générer les échéances
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal: Résilier la location --}}
    <div class="modal fade" id="resilierModal_{{ $_suffix }}" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('backend.locations.resilier', $location) }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title"><i class="ri-close-line me-1"></i>Résilier la location</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="ri-alert-line me-1"></i>
                            <strong>Attention !</strong> Êtes-vous sûr de vouloir résilier cette location ?
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Date de fin de location <span class="text-danger">*</span></label>
                            <input type="date" name="date_fin" class="form-control" required
                                   value="{{ now()->format('Y-m-d') }}">
                            <small class="text-muted">Date effective de fin du contrat de location</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Raison de la résiliation <span class="text-danger">*</span></label>
                            <textarea name="note_admin" class="form-control" rows="4" required
                                      placeholder="Expliquez pourquoi la location est résiliée"></textarea>
                        </div>
                        <div class="alert alert-danger mb-0">
                            <i class="ri-information-line me-1"></i>
                            Les paiements futurs seront bloqués après résiliation.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-warning">
                            <i class="ri-close-line me-1"></i>Confirmer la résiliation
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endif

{{-- Script pour toggle et radio (chargé une seule fois) --}}
@once
<style>
    .echeance-detail-row { display: none; }
    .echeance-detail-row.show { display: table-row; }
    .btn-expand-ech { transition: transform 0.2s; }
    .btn-expand-ech.rotated { transform: rotate(90deg); }
    .table-success-soft { background-color: rgba(40, 167, 69, 0.05) !important; }
</style>
<script>
    function toggleEchDetail(btn, detailId) {
        var row = document.getElementById(detailId);
        if (row) {
            row.classList.toggle('show');
            btn.classList.toggle('rotated');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.type-paiement-radio').forEach(function(radio) {
            radio.addEventListener('change', function() {
                var nameParts = this.name.replace('type_paiement_', '');
                var montantInput = document.getElementById('montant_input_' + nameParts);
                if (!montantInput) return;
                var montantTotal = montantInput.dataset.montantTotal;
                if (this.value === 'total') {
                    montantInput.value = montantTotal;
                    montantInput.setAttribute('readonly', 'readonly');
                } else {
                    montantInput.value = '';
                    montantInput.removeAttribute('readonly');
                    montantInput.focus();
                }
            });
        });
    });
</script>
@endonce
