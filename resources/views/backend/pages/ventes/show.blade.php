@extends('backend.layouts.master')
@section('title')
   Détails de la vente
@endsection
@section('css')
    <style>
        .info-card {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
        }
        .payment-history {
            max-height: 400px;
            overflow-y: auto;
        }
    </style>
@endsection
@section('content')
    @component('backend.components.breadcrumb')
        @slot('li_1')
            <a href="{{ route('backend.ventes.index') }}">Ventes</a>
        @endslot
        @slot('title')
            Détails de la vente #{{ $vente->id }}
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
                    @if($vente->demandeInteret)
                        <div class="alert alert-info mb-3">
                            <i class="ri-link me-2"></i>Cette vente provient de la 
                            <a href="{{ route('backend.demandes.show', $vente->demandeInteret->id) }}" class="alert-link">
                                Demande #{{ $vente->demandeInteret->id }}
                            </a>
                        </div>
                    @endif
                    @if($vente->annonce->getFirstMediaUrl('images'))
                        <img src="{{ $vente->annonce->getFirstMediaUrl('images') }}" class="img-fluid rounded mb-3" alt="Bien">
                    @endif
                    <h5>{{ $vente->annonce->titre }}</h5>
                    <p class="text-muted">{{ $vente->annonce->ville }}, {{ $vente->annonce->quartier }}</p>
                    <div class="info-card">
                        <div class="row">
                            <div class="col-6">
                                <strong>Type:</strong> {{ $vente->annonce->typeBien->nom ?? 'N/A' }}
                            </div>
                            <div class="col-6">
                                <strong>Surface:</strong> {{ $vente->annonce->surface }} m²
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informations du client -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="ri-user-line me-2"></i>Informations du client</h5>
                </div>
                <div class="card-body">
                    <div class="info-card">
                        <p><strong>Nom:</strong> {{ $vente->client->username }}</p>
                        <p><strong>Email:</strong> {{ $vente->client->email }}</p>
                        <p><strong>Téléphone:</strong> {{ $vente->client->phone ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Détails de la vente -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="ri-money-dollar-circle-line me-2"></i>Détails de la vente</h5>
                </div>
                <div class="card-body">
                    <div class="info-card">
                        <div class="row mb-2">
                            <div class="col-6"><strong>Prix de vente:</strong></div>
                            <div class="col-6 text-end">{{ number_format($vente->prix_vente, 0, ',', ' ') }} FCFA</div>
                        </div>
                        @if($vente->commission_agence)
                        <div class="row mb-2">
                            <div class="col-6"><strong>Commission agence:</strong></div>
                            <div class="col-6 text-end text-info">
                                @if($vente->type_commission === 'pourcentage')
                                    {{ $vente->commission_agence }}% = {{ number_format($vente->calculerCommission(), 0, ',', ' ') }} FCFA
                                @else
                                    {{ number_format($vente->commission_agence, 0, ',', ' ') }} FCFA (Fixe)
                                @endif
                            </div>
                        </div>
                        @else
                        <div class="row mb-2">
                            <div class="col-6"><strong>Commission agence:</strong></div>
                            <div class="col-6 text-end text-muted">Non définie</div>
                        </div>
                        @endif
                        <div class="row mb-2">
                            <div class="col-6"><strong>Montant payé:</strong></div>
                            <div class="col-6 text-end text-success">{{ number_format($vente->montantTotal(), 0, ',', ' ') }} FCFA</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6"><strong>Reste à payer:</strong></div>
                            <div class="col-6 text-end text-danger">{{ number_format($vente->resteAPayer(), 0, ',', ' ') }} FCFA</div>
                        </div>
                        <hr>
                        <div class="row mb-2">
                            <div class="col-6"><strong>Date de vente:</strong></div>
                            <div class="col-6 text-end">{{ $vente->date_vente->format('d/m/Y') }}</div>
                        </div>
                        @if($vente->date_signature)
                        <div class="row mb-2">
                            <div class="col-6"><strong>Date de signature:</strong></div>
                            <div class="col-6 text-end">{{ $vente->date_signature->format('d/m/Y') }}</div>
                        </div>
                        @endif
                        <div class="row">
                            <div class="col-6"><strong>Statut:</strong></div>
                            <div class="col-6 text-end">
                                @if($vente->statut == 'completé')
                                    <span class="badge bg-success">Complété</span>
                                @elseif($vente->statut == 'en_cours')
                                    <span class="badge bg-warning">En cours</span>
                                @else
                                    <span class="badge bg-danger">Annulé</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @if($vente->notes)
                        <div class="mt-3">
                            <strong>Notes:</strong>
                            <p class="mt-2">{{ $vente->notes }}</p>
                        </div>
                    @endif
                    <div class="mt-3">
                        <a href="{{ route('backend.ventes.edit', $vente) }}" class="btn btn-primary btn-sm">
                            <i class="ri-edit-line me-1"></i>Modifier
                        </a>
                        <form action="{{ route('backend.ventes.destroy', $vente) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette vente ? Cette action est irréversible.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="ri-delete-bin-line me-1"></i>Supprimer
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Historique des paiements -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0"><i class="ri-history-line me-2"></i>Historique des paiements</h5>
                    <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#addPaiementModal">
                        <i class="ri-add-line me-1"></i>Ajouter un paiement
                    </button>
                </div>
                <div class="card-body payment-history">
                    @forelse($vente->paiements as $paiement)
                        <div class="info-card">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="mb-1">{{ number_format($paiement->montant, 0, ',', ' ') }} FCFA</h6>
                                    <small class="text-muted">{{ $paiement->date_paiement->format('d/m/Y') }}</small>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-info">{{ ucfirst($paiement->methode_paiement) }}</span>
                                    @if($paiement->reference)
                                        <div><small>Réf: {{ $paiement->reference }}</small></div>
                                    @endif
                                </div>
                            </div>
                            @if($paiement->notes)
                                <p class="mt-2 mb-0 small">{{ $paiement->notes }}</p>
                            @endif
                        </div>
                    @empty
                        <p class="text-center text-muted">Aucun paiement enregistré</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Ajouter paiement -->
    <div class="modal fade" id="addPaiementModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('backend.ventes.add-paiement', $vente) }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Ajouter un paiement</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <strong>Reste à payer:</strong> {{ number_format($vente->resteAPayer(), 0, ',', ' ') }} FCFA
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Montant <span class="text-danger">*</span></label>
                            <input type="number" name="montant" id="montant" class="form-control" required step="0.01" max="{{ $vente->resteAPayer() }}">
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
        const montantInput = document.getElementById('montant');
        const montantError = document.getElementById('montant-error');
        const form = montantInput?.closest('form');
        const montantRestant = {{ $vente->resteAPayer() }};
        
        if (montantInput && form) {
            // Valider le montant avant soumission
            form.addEventListener('submit', function(e) {
                const montant = parseFloat(montantInput.value);
                
                if (montant > montantRestant) {
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
