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
                                            <input type="number" class="form-control" name="prix_min" min="1"
                                                placeholder="Prix min (FCFA)">
                                        </div>

                                        <div class="col-md-6">
                                            <input type="number" class="form-control" name="prix_max" min="1"
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

    <!-- Section Propriétaires - Confiez-nous votre bien -->
    <section class="section-padding position-relative">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0" data-aos="fade-right">
                    <div class="pe-lg-5">
                        <span class="badge bg-accent mb-3" style="font-size: 0.9rem; padding: 8px 20px;">
                            <i class="ri-vip-crown-line me-2"></i>PROPRIÉTAIRES
                        </span>
                        <h2 class="display-5 fw-bold mb-4" style="color: #43542A;">
                            Confiez la gestion de votre bien à des experts
                        </h2>
                        <p class="lead text-muted mb-4">
                            Sage Immo prend en charge la gestion complète de votre patrimoine immobilier.
                            Concentrez-vous sur l'essentiel, nous nous occupons du reste.
                        </p>

                        <div class="d-grid gap-3 mb-4">
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0">
                                    <div class="rounded-circle bg-accent d-flex align-items-center justify-content-center"
                                        style="width: 50px; height: 50px;">
                                        <i class="ri-money-dollar-circle-line fs-4 text-white"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="mb-1">Maximisez vos revenus</h5>
                                    <p class="text-muted mb-0">Valorisation optimale de votre bien et perception régulière
                                        des loyers</p>
                                </div>
                            </div>

                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0">
                                    <div class="rounded-circle bg-accent d-flex align-items-center justify-content-center"
                                        style="width: 50px; height: 50px;">
                                        <i class="ri-time-line fs-4 text-white"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="mb-1">Gagnez du temps</h5>
                                    <p class="text-muted mb-0">Nous gérons les visites, la sélection des locataires et
                                        l'administratif</p>
                                </div>
                            </div>

                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0">
                                    <div class="rounded-circle bg-accent d-flex align-items-center justify-content-center"
                                        style="width: 50px; height: 50px;">
                                        <i class="ri-shield-check-line fs-4 text-white"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="mb-1">Sécurité garantie</h5>
                                    <p class="text-muted mb-0">Sélection rigoureuse des locataires et garantie des loyers
                                        impayés</p>
                                </div>
                            </div>

                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0">
                                    <div class="rounded-circle bg-accent d-flex align-items-center justify-content-center"
                                        style="width: 50px; height: 50px;">
                                        <i class="ri-tools-line fs-4 text-white"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="mb-1">Maintenance & Suivi</h5>
                                    <p class="text-muted mb-0">Entretien régulier et gestion des réparations de votre bien
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap gap-3">
                            {{-- <a href="mailto:contact@sageimmo.com" class="btn btn-accent btn-lg px-4">
                                <i class="ri-mail-line me-2"></i>Demander un devis gratuit
                            </a> --}}
                            <a href="tel:" class="btn btn-outline-primary btn-lg px-4">
                                <i class="ri-phone-line me-2"></i>Nous appeler
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6" data-aos="fade-left">
                    <div class="position-relative">
                        <div class="card border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
                            <div class="card-body p-5"
                                style="background: linear-gradient(135deg, #43542A 0%, #2d3a1c 100%);">
                                <div class="text-center text-white">
                                    <i class="ri-building-2-line display-1 mb-4" style="opacity: 0.9;"></i>
                                    <h3 class="fw-bold mb-4">Nos services de gestion</h3>

                                    <div class="row g-3 mb-4">
                                        <div class="col-6">
                                            <div class="p-3 rounded" style="background: rgba(255, 255, 255, 0.1);">
                                                <i class="ri-search-line fs-3 mb-2"></i>
                                                <p class="small mb-0">Recherche de locataires</p>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="p-3 rounded" style="background: rgba(255, 255, 255, 0.1);">
                                                <i class="ri-file-text-line fs-3 mb-2"></i>
                                                <p class="small mb-0">Rédaction des baux</p>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="p-3 rounded" style="background: rgba(255, 255, 255, 0.1);">
                                                <i class="ri-wallet-3-line fs-3 mb-2"></i>
                                                <p class="small mb-0">Gestion financière</p>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="p-3 rounded" style="background: rgba(255, 255, 255, 0.1);">
                                                <i class="ri-home-gear-line fs-3 mb-2"></i>
                                                <p class="small mb-0">Entretien du bien</p>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- <div class="alert alert-warning mb-0" style="background: rgba(255, 193, 7, 0.2); border: none;">
                                        <i class="ri-star-line me-2"></i>
                                        <strong>Offre spéciale :</strong> Premier mois de gestion gratuit !
                                    </div> --}}
                                </div>
                            </div>
                        </div>

                        <!-- Decorative elements -->
                        <div class="position-absolute top-0 start-0 translate-middle">
                            <div class="rounded-circle bg-accent" style="width: 60px; height: 60px; opacity: 0.3;"></div>
                        </div>
                        <div class="position-absolute bottom-0 end-0 translate-middle">
                            <div class="rounded-circle bg-primary" style="width: 80px; height: 80px; opacity: 0.2;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Biens en vente -->
    @if ($biensVente->count() > 0)
        <section class="section-padding bg-light">
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

    <!-- Qui sommes-nous -->
    {{-- <section class="section-padding">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title" data-aos="fade-up">Qui sommes-nous ?</h2>
                <p class="section-subtitle" data-aos="fade-up">L'agence immobilière de confiance au Sénégal</p>
            </div>

            <div class="row align-items-center g-5">
                <div class="col-lg-6" data-aos="fade-right">
                    <div class="position-relative">
                        <img src="{{ asset('images/about-team.jpg') }}" alt="Équipe Sage Immo"
                            class="img-fluid rounded-4 shadow-lg"
                            onerror="this.src='https://images.unsplash.com/photo-1600880292203-757bb62b4baf?w=600'">

                        <div class="position-absolute bottom-0 start-0 m-4 p-4 bg-white rounded-3 shadow-lg"
                            style="max-width: 250px;">
                            <div class="d-flex align-items-center mb-2">
                                <i class="ri-award-line fs-1 text-accent me-3"></i>
                                <div>
                                    <h3 class="mb-0 fw-bold" style="color: #43542A;">10+</h3>
                                    <p class="mb-0 small text-muted">Années d'expérience</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6" data-aos="fade-left">
                    <h3 class="fw-bold mb-4" style="color: #43542A;">Votre partenaire immobilier depuis 2015</h3>

                    <p class="text-muted mb-4">
                        Sage Immo est une agence immobilière sénégalaise de référence, spécialisée dans la
                        location, la vente et la gestion de biens immobiliers. Forte de plus de 10 ans d'expérience,
                        notre équipe d'experts accompagne particuliers et professionnels dans tous leurs projets.
                    </p>

                    <p class="text-muted mb-4">
                        Nous nous distinguons par notre approche personnalisée, notre connaissance approfondie
                        du marché local et notre engagement à offrir des services de qualité supérieure. Notre mission
                        est de faciliter vos transactions immobilières en toute transparence et sécurité.
                    </p>

                    <div class="row g-4 mb-4">
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="rounded-circle bg-accent bg-opacity-10 p-3">
                                        <i class="ri-team-line fs-4 text-accent"></i>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <h5 class="mb-0">500+</h5>
                                    <p class="text-muted small mb-0">Clients satisfaits</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="rounded-circle bg-accent bg-opacity-10 p-3">
                                        <i class="ri-home-smile-line fs-4 text-accent"></i>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <h5 class="mb-0">200+</h5>
                                    <p class="text-muted small mb-0">Biens gérés</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-3">
                        <a href="{{ route('properties.index') }}" class="btn btn-accent btn-lg">
                            <i class="ri-search-line me-2"></i>Découvrir nos biens
                        </a>
                        <a href="mailto:contact@sageimmo.com" class="btn btn-outline-primary btn-lg">
                            <i class="ri-mail-line me-2"></i>Contactez-nous
                        </a>
                    </div>
                </div>
            </div>

            <!-- Nos valeurs -->
            <div class="row g-4 mt-5">
                <div class="col-12 text-center mb-4">
                    <h3 class="fw-bold" style="color: #43542A;" data-aos="fade-up">Nos valeurs</h3>
                </div>
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="text-center p-4 h-100 border rounded-3 hover-shadow transition-all">
                        <div class="mb-3">
                            <i class="ri-shield-check-line fs-1 text-accent"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Transparence</h5>
                        <p class="text-muted small mb-0">Des informations claires et honnêtes à chaque étape</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="text-center p-4 h-100 border rounded-3 hover-shadow transition-all">
                        <div class="mb-3">
                            <i class="ri-customer-service-2-line fs-1 text-accent"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Écoute</h5>
                        <p class="text-muted small mb-0">Votre satisfaction est notre priorité absolue</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="text-center p-4 h-100 border rounded-3 hover-shadow transition-all">
                        <div class="mb-3">
                            <i class="ri-lightbulb-flash-line fs-1 text-accent"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Expertise</h5>
                        <p class="text-muted small mb-0">Une connaissance approfondie du marché immobilier</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="400">
                    <div class="text-center p-4 h-100 border rounded-3 hover-shadow transition-all">
                        <div class="mb-3">
                            <i class="ri-medal-line fs-1 text-accent"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Excellence</h5>
                        <p class="text-muted small mb-0">Un service de qualité supérieure à chaque instant</p>
                    </div>
                </div>
            </div>
        </div>
    </section> --}}

    <!-- Nos Services -->
    <section class="section-padding" style="background: linear-gradient(135deg, #43542A 0%, #2d3a1c 100%);">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="text-white mb-3" data-aos="fade-up">Pourquoi choisir Sage Immo ?</h2>
                <p class="text-white-50 fs-5" data-aos="fade-up">Des services de qualité pour tous vos projets immobiliers
                </p>
            </div>

            <div class="row g-4">
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="text-center p-4"
                        style="background: rgba(255, 255, 255, 0.1); border-radius: 15px; backdrop-filter: blur(10px);">
                        <div class="mb-3">
                            <i class="ri-shield-check-line" style="font-size: 3rem; color: #ffc107;"></i>
                        </div>
                        <h4 class="text-white mb-3">Fiabilité & Sécurité</h4>
                        <p class="text-white-50 mb-0">Transactions sécurisées et accompagnement professionnel à chaque
                            étape</p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="text-center p-4"
                        style="background: rgba(255, 255, 255, 0.1); border-radius: 15px; backdrop-filter: blur(10px);">
                        <div class="mb-3">
                            <i class="ri-customer-service-2-line" style="font-size: 3rem; color: #ffc107;"></i>
                        </div>
                        <h4 class="text-white mb-3">Support 24/7</h4>
                        <p class="text-white-50 mb-0">Une équipe dédiée à votre écoute pour répondre à toutes vos questions
                        </p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="text-center p-4"
                        style="background: rgba(255, 255, 255, 0.1); border-radius: 15px; backdrop-filter: blur(10px);">
                        <div class="mb-3">
                            <i class="ri-hand-heart-line" style="font-size: 3rem; color: #ffc107;"></i>
                        </div>
                        <h4 class="text-white mb-3">Large Sélection</h4>
                        <p class="text-white-50 mb-0">Des centaines de biens disponibles pour tous les budgets et besoins
                        </p>
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
