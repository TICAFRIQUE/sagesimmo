@extends('frontend.layouts.master')

@section('title', 'Nos biens immobiliers - Sage Immo')

@section('content')
    <!-- Page Header -->
    {{-- <div class="page-header" style="background: linear-gradient(135deg, #43542A 0%, #2d3a1c 100%); padding: 4rem 0;">
        <div class="container">
            <h1 class="text-white mb-3" data-aos="fade-down">Nos biens immobiliers</h1>
            <nav aria-label="breadcrumb" data-aos="fade-up">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white">Accueil</a></li>
                    <li class="breadcrumb-item text-white active">Biens</li>
                </ol>
            </nav>
        </div>
    </div> --}}

    <section class="section-padding">
        <div class="container">
            <!-- Filtre horizontal -->
            <div class="filter-horizontal mb-4" data-aos="fade-down">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <h5 class="mb-0 me-3">
                                <i class="ri-filter-3-line"></i> Filtres
                            </h5>
                            <button class="btn btn-sm btn-outline-secondary ms-auto" type="button" data-bs-toggle="collapse"
                                data-bs-target="#filterCollapse">
                                <i class="ri-arrow-down-s-line"></i> Afficher/Masquer
                            </button>
                        </div>

                        <div class="collapse show" id="filterCollapse">
                            <form action="{{ route('properties.index') }}" method="GET">
                                <div class="row g-3">
                                    <!-- Type d'annonce -->
                                    <div class="col-lg-3 col-md-4 col-sm-6">
                                        <label for="type_annonce" class="form-label fw-bold small">Type d'annonce</label>
                                        <select class="form-select form-select-sm" name="type_annonce" id="type_annonce">
                                            <option value="" {{ !request('type_annonce') ? 'selected' : '' }}>Tous
                                            </option>
                                            <option value="location"
                                                {{ request('type_annonce') == 'location' ? 'selected' : '' }}>Location
                                            </option>
                                            <option value="vente"
                                                {{ request('type_annonce') == 'vente' ? 'selected' : '' }}>Vente</option>
                                        </select>
                                    </div>

                                    <!-- Type de bien -->
                                    <div class="col-lg-3 col-md-4 col-sm-6">
                                        <label for="type_bien_id" class="form-label fw-bold small">Type de bien</label>
                                        <select class="form-select form-select-sm" name="type_bien_id" id="type_bien_id">
                                            <option value="">Tous</option>
                                            @foreach ($typesBiens as $type)
                                                <option value="{{ $type->id }}"
                                                    {{ request('type_bien_id') == $type->id ? 'selected' : '' }}>
                                                    {{ $type->nom }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Ville -->
                                    <div class="col-lg-3 col-md-4 col-sm-6">
                                        <label for="ville" class="form-label fw-bold small">Ville</label>
                                        <select class="form-select form-select-sm" name="ville" id="ville"
                                            onchange="chargerCommunesFiltre()">
                                            <option value="">Sélectionner une ville...</option>
                                            @foreach ($villes as $nomVille => $communes)
                                                <option value="{{ $nomVille }}"
                                                    {{ request('ville') == $nomVille ? 'selected' : '' }}>
                                                    {{ $nomVille }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Commune -->
                                    <div class="col-lg-3 col-md-4 col-sm-6">
                                        <label for="commune" class="form-label fw-bold small">Commune</label>
                                        <select class="form-select form-select-sm" name="commune" id="commune"
                                            {{ !request('ville') ? 'disabled' : '' }}>
                                            <option value="">Sélectionner une commune...</option>
                                            @if (request('ville') && isset($villes[request('ville')]))
                                                @foreach ($villes[request('ville')] as $commune)
                                                    <option value="{{ $commune }}"
                                                        {{ request('commune') == $commune ? 'selected' : '' }}>
                                                        {{ $commune }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>

                                    <!-- Prix Min -->
                                    <div class="col-lg-3 col-md-4 col-sm-6">
                                        <label class="form-label fw-bold small">Prix Min (FCFA)</label>
                                        <input type="number" class="form-control form-control-sm" min="1"
                                            name="prix_min" value="{{ request('prix_min') }}" placeholder="Ex: 50000">
                                    </div>

                                    <!-- Prix Max -->
                                    <div class="col-lg-3 col-md-4 col-sm-6">
                                        <label class="form-label fw-bold small">Prix Max (FCFA)</label>
                                        <input type="number" class="form-control form-control-sm" min="1"
                                            name="prix_max" value="{{ request('prix_max') }}" placeholder="Ex: 500000">
                                    </div>

                                    <!-- Chambres -->
                                    <div class="col-lg-3 col-md-4 col-sm-6">
                                        <label for="chambres" class="form-label fw-bold small">Chambres min.</label>
                                        <select class="form-select form-select-sm" name="chambres" id="chambres">
                                            <option value="">Toutes</option>
                                            <option value="1" {{ request('chambres') == '1' ? 'selected' : '' }}>1+
                                            </option>
                                            <option value="2" {{ request('chambres') == '2' ? 'selected' : '' }}>2+
                                            </option>
                                            <option value="3" {{ request('chambres') == '3' ? 'selected' : '' }}>3+
                                            </option>
                                            <option value="4" {{ request('chambres') == '4' ? 'selected' : '' }}>4+
                                            </option>
                                            <option value="5" {{ request('chambres') == '5' ? 'selected' : '' }}>5+
                                            </option>
                                        </select>
                                    </div>


                                    <!-- Boutons -->
                                    <div class="col-lg-3 col-md-6 d-flex align-items-end gap-2">
                                        <button type="submit" class="btn btn-accent btn-sm flex-fill">
                                            <i class="ri-search-line"></i> Rechercher
                                        </button>
                                        <a href="{{ route('properties.index') }}" class="btn btn-outline-secondary btn-sm py-2">
                                            <i class="ri-refresh-line"></i> Réinitialiser
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Liste des biens -->
            <div class="row">
                <div class="col-12">
                    <!-- Tri et résultats -->
                    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3"
                        data-aos="fade-down">
                        <div>
                            <h5 class="mb-1">{{ $biens->total() }} bien(s) trouvé(s)</h5>
                            @if (request()->has('localisation') && request('localisation'))
                                <p class="text-muted mb-0">
                                    <i class="ri-map-pin-line"></i> {{ request('localisation') }}
                                </p>
                            @endif
                        </div>

                        {{-- <div class="d-flex gap-2 align-items-center">
                            <label class="mb-0">Trier par:</label>
                            <select class="form-select form-select-sm" style="width: auto;"
                                onchange="window.location.href=this.value">
                                <option
                                    value="{{ route('properties.index', array_merge(request()->except('sort'), ['sort' => 'recent'])) }}"
                                    {{ request('sort', 'recent') == 'recent' ? 'selected' : '' }}>
                                    Plus récents
                                </option>
                                <option
                                    value="{{ route('properties.index', array_merge(request()->except('sort'), ['sort' => 'prix_asc'])) }}"
                                    {{ request('sort') == 'prix_asc' ? 'selected' : '' }}>
                                    Prix croissant
                                </option>
                                <option
                                    value="{{ route('properties.index', array_merge(request()->except('sort'), ['sort' => 'prix_desc'])) }}"
                                    {{ request('sort') == 'prix_desc' ? 'selected' : '' }}>
                                    Prix décroissant
                                </option>
                                <option
                                    value="{{ route('properties.index', array_merge(request()->except('sort'), ['sort' => 'superficie_desc'])) }}"
                                    {{ request('sort') == 'superficie_desc' ? 'selected' : '' }}>
                                    Plus grande superficie
                                </option>
                            </select>
                        </div> --}}
                    </div>

                    <!-- Grille des biens -->
                    <div class="row g-4">
                        @forelse($biens as $bien)
                            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 50 }}">
                                @include('frontend.components.property-card', ['property' => $bien])
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="text-center py-5">
                                    <i class="ri-home-line" style="font-size: 5rem; color: var(--text-light);"></i>
                                    <h4 class="mt-4 mb-3">Aucun bien trouvé</h4>
                                    <p class="text-muted">Essayez de modifier vos critères de recherche</p>
                                    <a href="{{ route('properties.index') }}" class="btn btn-accent mt-3">
                                        <i class="ri-refresh-line"></i> Réinitialiser les filtres
                                    </a>
                                </div>
                            </div>
                        @endforelse
                    </div>

                    <!-- Pagination -->
                    @if ($biens->hasPages())
                        <div class="mt-5" data-aos="fade-up">
                            {{ $biens->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection

@section('css')
    <style>
        .page-header .breadcrumb {
            background: transparent;
        }

        .page-header .breadcrumb-item+.breadcrumb-item::before {
            color: rgba(255, 255, 255, 0.7);
        }

        /* Filtre horizontal */
        .filter-horizontal .card {
            border-radius: 12px;
            border: 1px solid #e5e7eb;
        }

        .filter-horizontal .form-label {
            color: var(--text-dark);
            margin-bottom: 0.5rem;
        }

        .filter-horizontal .btn-group label {
            font-size: 0.875rem;
            padding: 0.375rem 0.75rem;
        }

        .filter-horizontal .form-control-sm,
        .filter-horizontal .form-select-sm {
            border-radius: 6px;
        }

        .filter-horizontal .btn-check:checked+.btn-outline-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .pagination {
            gap: 0.5rem;
        }

        .page-link {
            border-radius: 8px;
            border: 1px solid var(--border-color);
            color: var(--text-dark);
            padding: 0.5rem 1rem;
        }

        .page-link:hover {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .page-item.active .page-link {
            background: var(--primary-color);
            border-color: var(--primary-color);
        }

        @media (max-width: 991px) {
            .filter-horizontal .row.g-3 {
                gap: 1rem !important;
            }
        }
    </style>
@endsection

@section('scripts')
    <script>
        // Données ville-commune
        const villesCommunesData = @json($villes);

        // Fonction pour charger les communes
        function chargerCommunesFiltre() {
            const villeSelect = document.getElementById('ville');
            const communeSelect = document.getElementById('commune');
            const villeSelectionnee = villeSelect.value;

            communeSelect.innerHTML = '<option value="">Sélectionner une commune...</option>';

            if (villeSelectionnee && villesCommunesData[villeSelectionnee] && villesCommunesData[villeSelectionnee].length >
                0) {
                communeSelect.disabled = false;
                villesCommunesData[villeSelectionnee].forEach(commune => {
                    const option = document.createElement('option');
                    option.value = commune;
                    option.textContent = commune;
                    communeSelect.appendChild(option);
                });
            } else {
                communeSelect.disabled = true;
            }
        }
    </script>
@endsection
