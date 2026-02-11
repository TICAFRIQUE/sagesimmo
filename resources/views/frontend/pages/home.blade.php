@extends('frontend.layouts.master')

@section('title', 'Accueil - Sage Immo | Votre plateforme immobilière')

@section('content')
    <!-- Hero Section avec formulaire de recherche -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6" data-aos="fade-right">
                    <div class="hero-content">
                        <h1>Trouvez la propriété de vos rêves</h1>
                        <p>
                            Explorez des milliers de biens immobiliers disponibles à la location ou à la vente.
                            Votre future maison vous attend.
                        </p>
                    </div>
                </div>

                <div class="col-lg-6" data-aos="fade-left">
                    <div class="search-form">
                        <h3 class="mb-4">Rechercher un bien</h3>

                        <!-- Tabs pour Location/Vente -->
                        <ul class="nav nav-tabs search-tabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#location"
                                    type="button">
                                    <i class="ri-home-heart-line"></i> Location
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#vente" type="button">
                                    <i class="ri-shopping-cart-line"></i> Vente
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content mt-4">
                            <!-- Location -->
                            <div class="tab-pane fade show active" id="location">
                                <form action="{{ route('search') }}" method="GET">
                                    <input type="hidden" name="type_annonce" value="location">

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <select class="form-select" name="ville" id="ville-location"
                                                onchange="chargerCommunesLocation()">
                                                <option value="">Sélectionner une ville</option>
                                                @foreach (array_keys($villes) as $ville)
                                                    <option value="{{ $ville }}">{{ $ville }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-6">
                                            <select class="form-select" name="commune" id="commune-location" disabled>
                                                <option value="">Sélectionner une commune...</option>
                                            </select>
                                        </div>

                                        <div class="col-md-6">
                                            <select class="form-select" name="type_bien_id">
                                                <option value="">Type de bien</option>
                                                @foreach ($typesBiens as $type)
                                                    <option value="{{ $type->id }}">{{ $type->nom }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-6">
                                            <select class="form-select" name="chambres">
                                                <option value="">Chambres</option>
                                                <option value="1">1+</option>
                                                <option value="2">2+</option>
                                                <option value="3">3+</option>
                                                <option value="4">4+</option>
                                            </select>
                                        </div>

                                        <div class="col-md-6">
                                            <input type="number" class="form-control" name="prix_min"
                                                placeholder="Prix min (FCFA)">
                                        </div>

                                        <div class="col-md-6">
                                            <input type="number" class="form-control" name="prix_max"
                                                placeholder="Prix max (FCFA)">
                                        </div>

                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary w-100 py-3">
                                                <i class="ri-search-line"></i> Rechercher
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!-- Vente -->
                            <div class="tab-pane fade" id="vente">
                                <form action="{{ route('search') }}" method="GET">
                                    <input type="hidden" name="type_annonce" value="vente">

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <select class="form-select" name="ville" id="ville-vente"
                                                onchange="chargerCommunesVente()">
                                                <option value="">Sélectionner une ville</option>
                                                @foreach (array_keys($villes) as $ville)
                                                    <option value="{{ $ville }}">{{ $ville }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-6">
                                            <select class="form-select" name="commune" id="commune-vente" disabled>
                                                <option value="">Sélectionner une commune...</option>
                                            </select>
                                        </div>

                                        <div class="col-md-6">
                                            <select class="form-select" name="type_bien_id">
                                                <option value="">Type de bien</option>
                                                @foreach ($typesBiens as $type)
                                                    <option value="{{ $type->id }}">{{ $type->nom }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-6">
                                            <select class="form-select" name="chambres">
                                                <option value="">Chambres</option>
                                                <option value="1">1+</option>
                                                <option value="2">2+</option>
                                                <option value="3">3+</option>
                                                <option value="4">4+</option>
                                            </select>
                                        </div>

                                        <div class="col-md-6">
                                            <input type="number" class="form-control" name="prix_min"
                                                placeholder="Prix min (FCFA)">
                                        </div>

                                        <div class="col-md-6">
                                            <input type="number" class="form-control" name="prix_max"
                                                placeholder="Prix max (FCFA)">
                                        </div>

                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary w-100 py-3">
                                                <i class="ri-search-line"></i> Rechercher
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistiques -->
    {{-- <section class="stats-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6 mb-4 mb-lg-0" data-aos="fade-up" data-aos-delay="100">
                    <div class="stat-card">
                        <div class="stat-number">{{ $stats['total_biens'] }}+</div>
                        <div class="stat-label">Biens disponibles</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4 mb-lg-0" data-aos="fade-up" data-aos-delay="200">
                    <div class="stat-card">
                        <div class="stat-number">{{ $stats['biens_location'] }}</div>
                        <div class="stat-label">Biens en location</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4 mb-lg-0" data-aos="fade-up" data-aos-delay="300">
                    <div class="stat-card">
                        <div class="stat-number">{{ $stats['biens_vente'] }}</div>
                        <div class="stat-label">Biens en vente</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="400">
                    <div class="stat-card">
                        <div class="stat-number">{{ $stats['types_biens'] }}</div>
                        <div class="stat-label">Types de biens</div>
                    </div>
                </div>
            </div>
        </div>
    </section> --}}

    <!-- Biens récents -->
    <section class="section-padding">
        <div class="container">
            <h2 class="section-title" data-aos="fade-up">Biens récemment ajoutés</h2>
            <p class="section-subtitle" data-aos="fade-up">Découvrez nos dernières offres immobilières</p>

            <div class="row g-4">
                @forelse($biensRecents as $bien)
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 100 }}">
                        @include('frontend.components.property-card', ['property' => $bien])
                    </div>
                @empty
                    <div class="col-12">
                        <div class="text-center py-5">
                            <i class="ri-home-line display-1 text-muted"></i>
                            <p class="text-muted mt-3">Aucun bien disponible pour le moment</p>
                        </div>
                    </div>
                @endforelse
            </div>

            @if ($biensRecents->count() > 0)
                <div class="text-center mt-5" data-aos="fade-up">
                    <a href="{{ route('properties.index') }}" class="btn btn-accent btn-lg">
                        Voir tous les biens <i class="ri-arrow-right-line"></i>
                    </a>
                </div>
            @endif
        </div>
    </section>

    <!-- Biens en location -->
    @if ($biensLocation->count() > 0)
        <section class="section-padding bg-light">
            <div class="container">
                <h2 class="section-title" data-aos="fade-up">Biens en location</h2>
                <p class="section-subtitle" data-aos="fade-up">Trouvez votre prochain logement</p>

                <div class="row g-4">
                    @foreach ($biensLocation as $bien)
                        <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 100 }}">
                            @include('frontend.components.property-card', ['property' => $bien])
                        </div>
                    @endforeach
                </div>

                <div class="text-center mt-5" data-aos="fade-up">
                    <a href="{{ route('properties.index', ['type_annonce' => 'location']) }}"
                        class="btn btn-outline-accent btn-lg">
                        Voir toutes les locations <i class="ri-arrow-right-line"></i>
                    </a>
                </div>
            </div>
        </section>
    @endif

    <!-- Biens en vente -->
    @if ($biensVente->count() > 0)
        <section class="section-padding">
            <div class="container">
                <h2 class="section-title" data-aos="fade-up">Biens en vente</h2>
                <p class="section-subtitle" data-aos="fade-up">Investissez dans votre future propriété</p>

                <div class="row g-4">
                    @foreach ($biensVente as $bien)
                        <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 100 }}">
                            @include('frontend.components.property-card', ['property' => $bien])
                        </div>
                    @endforeach
                </div>

                <div class="text-center mt-5" data-aos="fade-up">
                    <a href="{{ route('properties.index', ['type_annonce' => 'vente']) }}" class="btn btn-accent btn-lg">
                        Voir toutes les ventes <i class="ri-arrow-right-line"></i>
                    </a>
                </div>
            </div>
        </section>
    @endif

    <!-- Nos Services -->
    <section class="section-padding" style="background: linear-gradient(135deg, #43542A 0%, #2d3a1c 100%);">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="text-white mb-3" data-aos="fade-up">Pourquoi choisir Sage Immo ?</h2>
                <p class="text-white-50 fs-5" data-aos="fade-up">Des services de qualité pour tous vos projets immobiliers</p>
            </div>

            <div class="row g-4">
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="text-center p-4" style="background: rgba(255, 255, 255, 0.1); border-radius: 15px; backdrop-filter: blur(10px);">
                        <div class="mb-3">
                            <i class="ri-shield-check-line" style="font-size: 3rem; color: #ffc107;"></i>
                        </div>
                        <h4 class="text-white mb-3">Fiabilité & Sécurité</h4>
                        <p class="text-white-50 mb-0">Transactions sécurisées et accompagnement professionnel à chaque étape</p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="text-center p-4" style="background: rgba(255, 255, 255, 0.1); border-radius: 15px; backdrop-filter: blur(10px);">
                        <div class="mb-3">
                            <i class="ri-customer-service-2-line" style="font-size: 3rem; color: #ffc107;"></i>
                        </div>
                        <h4 class="text-white mb-3">Support 24/7</h4>
                        <p class="text-white-50 mb-0">Une équipe dédiée à votre écoute pour répondre à toutes vos questions</p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="text-center p-4" style="background: rgba(255, 255, 255, 0.1); border-radius: 15px; backdrop-filter: blur(10px);">
                        <div class="mb-3">
                            <i class="ri-hand-heart-line" style="font-size: 3rem; color: #ffc107;"></i>
                        </div>
                        <h4 class="text-white mb-3">Large Sélection</h4>
                        <p class="text-white-50 mb-0">Des centaines de biens disponibles pour tous les budgets et besoins</p>
                    </div>
                </div>
            </div>

            <div class="text-center mt-5" data-aos="fade-up">
                {{-- <a href="mailto:contact@sageimmo.com" class="btn btn-accent btn-lg me-3">
                    <i class="ri-mail-line"></i> Nous contacter
                </a> --}}
                <a href="{{ route('properties.index') }}" class="btn btn-outline-light btn-lg">
                    <i class="ri-search-line"></i> Parcourir les biens
                </a>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        // Données ville-commune
        const villesCommunesData = @json($villes);

        // Fonction pour charger les communes pour Location
        function chargerCommunesLocation() {
            const villeSelect = document.getElementById('ville-location');
            const communeSelect = document.getElementById('commune-location');
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

        // Fonction pour charger les communes pour Vente
        function chargerCommunesVente() {
            const villeSelect = document.getElementById('ville-vente');
            const communeSelect = document.getElementById('commune-vente');
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
