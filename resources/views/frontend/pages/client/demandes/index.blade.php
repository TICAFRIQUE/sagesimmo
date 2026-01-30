@extends('frontend.pages.client.layout')

@section('client-content')
<div class="client-content">
    <h2 class="mb-4">
        <i class="ri-message-3-line"></i> Mes Demandes
    </h2>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="ri-check-line"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('client.demandes') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="statut" class="form-label">Filtrer par statut</label>
                    <select name="statut" id="statut" class="form-select">
                        <option value="">Tous les statuts</option>
                        <option value="nouvelle" {{ request('statut') == 'nouvelle' ? 'selected' : '' }}>Nouvelle</option>
                        <option value="visite_planifiee" {{ request('statut') == 'visite_planifiee' ? 'selected' : '' }}>Visite planifiée</option>
                        <option value="visite_effectuee" {{ request('statut') == 'visite_effectuee' ? 'selected' : '' }}>Visite effectuée</option>
                        <option value="documents_recus" {{ request('statut') == 'documents_recus' ? 'selected' : '' }}>Documents reçus</option>
                        <option value="dossier_valide" {{ request('statut') == 'dossier_valide' ? 'selected' : '' }}>Dossier validé</option>
                        <option value="contrat_genere" {{ request('statut') == 'contrat_genere' ? 'selected' : '' }}>Contrat généré</option>
                        <option value="paiement_en_attente" {{ request('statut') == 'paiement_en_attente' ? 'selected' : '' }}>Paiement en attente</option>
                        <option value="paiement_valide" {{ request('statut') == 'paiement_valide' ? 'selected' : '' }}>Paiement validé</option>
                        <option value="cloture_refus" {{ request('statut') == 'cloture_refus' ? 'selected' : '' }}>Clôturée - Refusée</option>
                        <option value="cloture_non_interesse" {{ request('statut') == 'cloture_non_interesse' ? 'selected' : '' }}>Clôturée - Non intéressé</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="ri-filter-line"></i> Filtrer
                    </button>
                    <a href="{{ route('client.demandes') }}" class="btn btn-secondary">
                        <i class="ri-refresh-line"></i> Réinitialiser
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des demandes -->
    @if($demandes->count() > 0)
        <div class="row">
            @foreach($demandes as $demande)
                <div class="col-md-12 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-2">
                                    @if($demande->annonce->hasMedia('image_principale'))
                                        <img src="{{ $demande->annonce->getFirstMediaUrl('image_principale') }}" 
                                             class="img-fluid rounded" 
                                             alt="{{ $demande->annonce->titre }}">
                                    @else
                                        <img src="{{ asset('build/images/no-image.png') }}" 
                                             class="img-fluid rounded" 
                                             alt="Pas d'image">
                                    @endif
                                </div>
                                <div class="col-md-5">
                                    <h5 class="mb-2">{{ $demande->annonce->titre }}</h5>
                                    <p class="text-muted mb-1">
                                        <i class="ri-file-line"></i> Réf: {{ $demande->annonce->reference }}
                                    </p>
                                    <p class="text-muted mb-1">
                                        <i class="ri-map-pin-line"></i> 
                                        {{ $demande->annonce->quartier }}, {{ $demande->annonce->ville }}
                                    </p>
                                    <p class="text-muted mb-0">
                                        <i class="ri-calendar-line"></i> 
                                        Demandé le {{ $demande->created_at->format('d/m/Y') }}
                                    </p>
                                </div>
                                <div class="col-md-2 text-center">
                                    {!! $demande->statut_badge !!}
                                    @if($demande->date_visite)
                                        <div class="mt-2">
                                            <small class="text-muted">
                                                <i class="ri-calendar-event-line"></i>
                                                {{ \Carbon\Carbon::parse($demande->date_visite)->format('d/m/Y H:i') }}
                                            </small>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-3 text-end">
                                    <a href="{{ route('client.demandes.show', $demande->id) }}" 
                                       class="btn btn-sm btn-primary mb-2 w-100">
                                        <i class="ri-eye-line"></i> Voir détails
                                    </a>
                                    <a href="{{ route('properties.show', $demande->annonce->slug) }}" 
                                       class="btn btn-sm btn-outline-secondary w-100" 
                                       target="_blank">
                                        <i class="ri-external-link-line"></i> Voir le bien
                                    </a>
                                    @if($demande->statut == 'nouvelle')
                                        <form action="{{ route('client.demandes.cancel', $demande->id) }}" 
                                              method="POST" 
                                              class="mt-2"
                                              onsubmit="return confirm('Voulez-vous vraiment annuler cette demande ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                                                <i class="ri-close-line"></i> Annuler
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $demandes->links() }}
        </div>
    @else
        <div class="alert alert-info">
            <i class="ri-information-line"></i> 
            Vous n'avez pas encore de demandes. 
            <a href="{{ route('properties.index') }}">Parcourez nos biens</a> pour commencer.
        </div>
    @endif
</div>
@endsection
