@extends('backend.layouts.master')
@section('title')
   Détails de la location
@endsection
@section('css')
    <style>
        .info-card {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
        }
        .echeance-list {
            max-height: 500px;
            overflow-y: auto;
        }
        .echeance-item {
            border-left: 4px solid #ddd;
            padding-left: 15px;
            margin-bottom: 15px;
        }
        .echeance-item.paye {
            border-color: #28a745;
        }
        .echeance-item.en_retard {
            border-color: #dc3545;
        }
        .echeance-item.partiel {
            border-color: #17a2b8;
        }
        .echeance-item.impaye {
            border-color: #ffc107;
        }
    </style>
@endsection
@section('content')
    @component('backend.components.breadcrumb')
        @slot('li_1')
            <a href="{{ route('backend.locations.index') }}">Locations</a>
        @endslot
        @slot('title')
            Détails de la location #{{ $location->id }}
        @endslot
    @endcomponent

    <div class="row">
        <!-- Informations du bien -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="ri-home-4-line me-2"></i>Informations du bien</h5>
                </div>
                <div class="card-body">
                    @if($location->demandeInteret)
                        <div class="alert alert-info mb-3">
                            <i class="ri-link me-2"></i>Cette location provient de la 
                            <a href="{{ route('backend.demandes.show', $location->demandeInteret->id) }}" class="alert-link">
                                Demande #{{ $location->demandeInteret->id }}
                            </a>
                        </div>
                    @endif
                    @if($location->annonce->getFirstMediaUrl('images'))
                        <img src="{{ $location->annonce->getFirstMediaUrl('images') }}" class="img-fluid rounded mb-3" alt="Bien">
                    @endif
                    <h5>{{ $location->annonce->titre }}</h5>
                    <p class="text-muted">{{ $location->annonce->ville }}, {{ $location->annonce->quartier }}</p>
                    <div class="info-card">
                        <div class="row">
                            <div class="col-6">
                                <strong>Type:</strong> {{ $location->annonce->typeBien->nom ?? 'N/A' }}
                            </div>
                            <div class="col-6">
                                <strong>Surface:</strong> {{ $location->annonce->surface }} m²
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informations du locataire -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="ri-user-line me-2"></i>Informations du locataire</h5>
                </div>
                <div class="card-body">
                    <div class="info-card">
                        <p><strong>Nom:</strong> {{ $location->locataire->name }}</p>
                        <p><strong>Email:</strong> {{ $location->locataire->email }}</p>
                        <p><strong>Téléphone:</strong> {{ $location->locataire->telephone ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Détails de la location -->
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="ri-money-dollar-circle-line me-2"></i>Détails de la location</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="info-card text-center">
                                <h6>Loyer mensuel</h6>
                                <h4 class="text-primary">{{ number_format($location->loyer_mensuel, 0, ',', ' ') }} FCFA</h4>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-card text-center">
                                <h6>Caution</h6>
                                <h4 class="text-info">{{ number_format($location->caution ?? 0, 0, ',', ' ') }} FCFA</h4>
                                @if($location->nombre_cautions)
                                    <small class="text-muted">{{ $location->nombre_cautions }} mois</small>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-card text-center">
                                <h6>Date début</h6>
                                <h4>{{ $location->date_debut->format('d/m/Y') }}</h4>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-card text-center">
                                <h6>Statut</h6>
                                <h4>
                                    @if($location->statut == 'actif')
                                        <span class="badge bg-success">Actif</span>
                                    @elseif($location->statut == 'terminé')
                                        <span class="badge bg-secondary">Terminé</span>
                                    @else
                                        <span class="badge bg-danger">Résilié</span>
                                    @endif
                                </h4>
                            </div>
                        </div>
                    </div>
                    @if($location->conditions)
                        <div class="mt-3">
                            <strong>Conditions:</strong>
                            <p class="mt-2">{{ $location->conditions }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Gestion des échéances -->
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="ri-calendar-check-line me-2"></i>Échéances de loyer</h5>
                </div>
                <div class="card-body echeance-list">
                    @forelse($location->echeances()->orderBy('date_echeance', 'desc')->get() as $echeance)
                        <div class="echeance-item {{ $echeance->statut }}">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">{{ $echeance->date_echeance->format('d/m/Y') }}</h6>
                                    <div class="mb-2">
                                        @if($echeance->statut == 'payé')
                                            <span class="badge bg-success">Payé</span>
                                        @elseif($echeance->statut == 'en_retard')
                                            <span class="badge bg-danger">En retard</span>
                                        @elseif($echeance->statut == 'partiel')
                                            <span class="badge bg-info">Paiement partiel</span>
                                        @elseif($echeance->statut == 'impayé')
                                            <span class="badge bg-warning">Impayé</span>
                                        @else
                                            <span class="badge bg-secondary">En attente</span>
                                        @endif
                                    </div>
                                    <div>
                                        <strong>Montant:</strong> {{ number_format($echeance->montant_du, 0, ',', ' ') }} FCFA
                                        @if($echeance->montantRestant() > 0)
                                            <br><small class="text-danger">Reste: {{ number_format($echeance->montantRestant(), 0, ',', ' ') }} FCFA</small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-muted">Aucune échéance enregistrée</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Historique détaillé des paiements -->
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0"><i class="ri-money-dollar-circle-line me-2"></i>Historique des paiements</h5>
                    <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#addPaiementModal">
                        <i class="ri-add-line me-1"></i>Ajouter un paiement
                    </button>
                </div>
                <div class="card-body">
                    @if($location->paiements->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Montant</th>
                                        <th>Méthode</th>
                                        <th>Référence</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($location->paiements()->orderBy('date_paiement', 'desc')->get() as $paiement)
                                        <tr>
                                            <td>{{ $paiement->date_paiement->format('d/m/Y') }}</td>
                                            <td>
                                                <strong class="text-success">{{ number_format($paiement->montant, 0, ',', ' ') }} FCFA</strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ ucfirst($paiement->methode_paiement) }}</span>
                                            </td>
                                            <td>
                                                @if($paiement->reference)
                                                    <code>{{ $paiement->reference }}</code>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($paiement->notes)
                                                    <small>{{ $paiement->notes }}</small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th>Total payé:</th>
                                        <th colspan="4" class="text-success">
                                            {{ number_format($location->montantTotalPaye(), 0, ',', ' ') }} FCFA
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="ri-file-list-line" style="font-size: 48px; color: #ccc;"></i>
                            <p class="text-muted mt-2">Aucun paiement enregistré</p>
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addPaiementModal">
                                <i class="ri-add-line me-1"></i>Ajouter le premier paiement
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Ajouter paiement -->
    <div class="modal fade" id="addPaiementModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('backend.locations.add-paiement', $location) }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Ajouter un paiement</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Échéance associée</label>
                            <select name="echeance_id" id="echeance_id" class="form-select">
                                <option value="">Échéance non spécifiée</option>
                                @php
                                    $echeancesNonPayees = $location->echeances()->where('statut', '!=', 'payé')->orderBy('date_echeance')->get();
                                @endphp
                                @foreach($echeancesNonPayees as $echeance)
                                    <option value="{{ $echeance->id }}" data-montant-restant="{{ $echeance->montantRestant() }}">
                                        {{ $echeance->date_echeance->format('d/m/Y') }} - 
                                        Reste: {{ number_format($echeance->montantRestant(), 0, ',', ' ') }} FCFA
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted" id="montant-restant-info"></small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Montant <span class="text-danger">*</span></label>
                            <input type="number" name="montant" id="montant" class="form-control" required step="0.01">
                            <div class="invalid-feedback" id="montant-error"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Date de paiement <span class="text-danger">*</span></label>
                            <input type="date" name="date_paiement" class="form-control" required value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Méthode de paiement <span class="text-danger">*</span></label>
                            <select name="methode_paiement" class="form-select" required>
                                <option value="virement">Virement</option>
                                <option value="espèces">Espèces</option>
                                <option value="chèque">Chèque</option>
                                <option value="carte_bancaire">Carte bancaire</option>
                                <option value="autre">Autre</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Référence</label>
                            <input type="text" name="reference" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
<script>
    // Validation du montant de paiement
    document.addEventListener('DOMContentLoaded', function() {
        const echeanceSelect = document.getElementById('echeance_id');
        const montantInput = document.getElementById('montant');
        const montantError = document.getElementById('montant-error');
        const montantRestantInfo = document.getElementById('montant-restant-info');
        const form = montantInput?.closest('form');
        
        if (echeanceSelect && montantInput) {
            // Afficher le montant restant quand on sélectionne une échéance
            echeanceSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const montantRestant = selectedOption.getAttribute('data-montant-restant');
                
                if (montantRestant) {
                    montantRestantInfo.textContent = 'Montant maximum: ' + new Intl.NumberFormat('fr-FR').format(montantRestant) + ' FCFA';
                    montantRestantInfo.classList.add('text-info');
                    montantInput.max = montantRestant;
                } else {
                    montantRestantInfo.textContent = '';
                    montantInput.removeAttribute('max');
                }
            });
            
            // Valider le montant avant soumission
            form?.addEventListener('submit', function(e) {
                const selectedOption = echeanceSelect.options[echeanceSelect.selectedIndex];
                const montantRestant = parseFloat(selectedOption.getAttribute('data-montant-restant'));
                const montant = parseFloat(montantInput.value);
                
                if (montantRestant && montant > montantRestant) {
                    e.preventDefault();
                    montantInput.classList.add('is-invalid');
                    montantError.textContent = 'Le montant ne peut pas dépasser ' + new Intl.NumberFormat('fr-FR').format(montantRestant) + ' FCFA';
                    return false;
                }
            });
            
            // Retirer l'erreur lors de la modification
            montantInput.addEventListener('input', function() {
                this.classList.remove('is-invalid');
            });
        }
    });
</script>
@endsection
