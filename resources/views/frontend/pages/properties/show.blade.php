@extends('frontend.layouts.master')

@section('title', $bien->titre . ' - Sage Immo')

@section('css')
    <style>
        .property-gallery {
            position: relative;
            margin-bottom: 2rem;
        }

        /* Carousel Styles */
        .property-carousel {
            height: 500px;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .property-carousel .carousel-inner {
            height: 100%;
        }

        .property-carousel .carousel-item {
            height: 100%;
            cursor: pointer;
        }

        .property-carousel .carousel-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .property-carousel .carousel-control-prev,
        .property-carousel .carousel-control-next {
            width: 50px;
            height: 50px;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(0, 0, 0, 0.6);
            border-radius: 50%;
            opacity: 0.8;
        }

        .property-carousel .carousel-control-prev:hover,
        .property-carousel .carousel-control-next:hover {
            opacity: 1;
            background: rgba(0, 0, 0, 0.8);
        }

        .property-carousel .carousel-control-prev {
            left: 20px;
        }

        .property-carousel .carousel-control-next {
            right: 20px;
        }

        .property-carousel .carousel-indicators {
            bottom: 20px;
        }

        .property-carousel .carousel-indicators button {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin: 0 5px;
        }

        /* Lightbox Styles */
        .lightbox-modal {
            display: none;
            position: fixed;
            z-index: 9999;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.95);
            align-items: center;
            justify-content: center;
        }

        .lightbox-modal.active {
            display: flex;
        }

        .lightbox-content {
            position: relative;
            max-width: 90%;
            max-height: 90%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .lightbox-content img {
            max-width: 100%;
            max-height: 90vh;
            object-fit: contain;
            border-radius: 8px;
        }

        .lightbox-close {
            position: absolute;
            top: 20px;
            right: 30px;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 2px solid white;
            font-size: 30px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
            z-index: 10000;
        }

        .lightbox-close:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: rotate(90deg);
        }

        .lightbox-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 2px solid white;
            font-size: 24px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
            z-index: 10000;
        }

        .lightbox-nav:hover {
            background: rgba(255, 255, 255, 0.4);
        }

        .lightbox-prev {
            left: 30px;
        }

        .lightbox-next {
            right: 30px;
        }

        .lightbox-counter {
            position: absolute;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0, 0, 0, 0.6);
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            font-size: 16px;
            z-index: 10000;
        }

        .image-count-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            z-index: 10;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .image-count-badge i {
            font-size: 1.1rem;
        }

        .property-details-card {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            position: sticky;
            top: 100px;
        }

        .price-tag {
            font-size: 2rem;
            font-weight: 700;
            color: var(--accent-color);
            margin-bottom: 1rem;
        }

        .feature-list {
            list-style: none;
            padding: 0;
        }

        .feature-list li {
            padding: 0.75rem 0;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
        }

        .feature-list li:last-child {
            border-bottom: none;
        }

        .contact-form {
            background: var(--bg-light);
            border-radius: 12px;
            padding: 2rem;
        }

        .description-section {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            margin-bottom: 2rem;
        }

        .equipement-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: var(--bg-light);
            border-radius: 20px;
            margin: 0.25rem;
        }

        /* Carousel biens similaires */
        .similar-properties-section {
            background: var(--bg-light);
            padding: 4rem 0;
            margin-top: 0;
        }

        .similar-carousel {
            position: relative;
            overflow: visible;
        }

        .similar-carousel .carousel-inner {
            overflow: visible;
            padding: 20px 0;
        }

        .similar-carousel .carousel-item {
            transition: transform 0.6s ease-in-out;
        }

        .similar-carousel .row {
            margin: 0 -15px;
        }

        .similar-carousel .row>div {
            padding: 0 15px;
        }

        .similar-carousel .property-card {
            height: 100%;
            margin-bottom: 0;
        }

        /* Styles pour le carousel des property-card dans les biens similaires */
        .similar-carousel .property-carousel {
            height: 100%;
        }

        .similar-carousel .property-carousel .carousel-inner {
            height: 100%;
        }

        .similar-carousel .property-carousel .carousel-item {
            height: 100%;
        }

        .similar-carousel .property-carousel .carousel-item img {
            height: 250px;
            width: 100%;
            object-fit: cover;
        }

        .similar-carousel .property-image {
            position: relative;
            height: 250px;
            overflow: hidden;
        }

        .similar-carousel .property-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s;
        }

        .similar-carousel .property-card:hover .property-image img {
            transform: scale(1.05);
        }

        .similar-carousel .property-carousel .carousel-control-prev,
        .similar-carousel .property-carousel .carousel-control-next {
            width: 40px;
            height: 40px;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(0, 0, 0, 0.7);
            border-radius: 50%;
            opacity: 0.7;
            transition: all 0.3s;
            border: 2px solid rgba(255, 255, 255, 0.8);
        }

        .similar-carousel .property-card:hover .carousel-control-prev,
        .similar-carousel .property-card:hover .carousel-control-next {
            opacity: 1;
            background: rgba(0, 0, 0, 0.9);
        }

        .similar-carousel .property-carousel .carousel-control-prev {
            left: 10px;
        }

        .similar-carousel .property-carousel .carousel-control-next {
            right: 10px;
        }

        .similar-carousel .property-carousel .carousel-control-prev-icon,
        .similar-carousel .property-carousel .carousel-control-next-icon {
            width: 20px;
            height: 20px;
        }

        .similar-carousel .property-carousel .carousel-indicators {
            bottom: 10px;
            margin-bottom: 0;
            z-index: 2;
        }

        .similar-carousel .property-carousel .carousel-indicators button {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            border: none;
            background-color: rgba(255, 255, 255, 0.5);
            margin: 0 4px;
            padding: 0;
            transition: all 0.3s;
        }

        .similar-carousel .property-carousel .carousel-indicators button.active {
            width: 10px;
            height: 10px;
            background-color: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .similar-carousel .property-carousel .carousel-indicators button:hover {
            background-color: rgba(255, 255, 255, 0.8);
        }
    
    /* Format horizontal compact pour biens similaires */
    .similar-property-compact {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        transition: all 0.3s;
        display: flex;
        height: 180px;
        margin-bottom: 1.5rem;
    }
    
    .similar-property-compact:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }
    
    .similar-property-image {
        width: 250px;
        min-width: 250px;
        height: 100%;
        position: relative;
        overflow: hidden;
    }
    
    .similar-property-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s;
    }
    
    .similar-property-compact:hover .similar-property-image img {
        transform: scale(1.1);
    }
    
    .similar-property-badge {
        position: absolute;
        top: 10px;
        left: 10px;
        background: var(--primary-color);
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 15px;
        font-size: 0.75rem;
        font-weight: 600;
        z-index: 1;
    }
    
    .similar-property-badge.badge-vente {
        background: var(--accent-color);
    }
    
    .similar-property-content {
        flex: 1;
        padding: 1.25rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    
    .similar-property-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: var(--text-dark);
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .similar-property-title:hover {
        color: var(--accent-color);
    }
    
    .similar-property-location {
        color: var(--text-light);
        font-size: 0.9rem;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }
    
    .similar-property-features {
        display: flex;
        gap: 1rem;
        margin-bottom: 0.75rem;
        flex-wrap: wrap;
    }
    
    .similar-feature-item {
        display: flex;
        align-items: center;
        gap: 0.25rem;
        font-size: 0.85rem;
        color: var(--text-light);
    }
    
    .similar-feature-item i {
        color: var(--primary-color);
        font-size: 1rem;
    }
    
    .similar-property-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .similar-property-price {
        font-size: 1.3rem;
        font-weight: 700;
        color: var(--accent-color);
    }
    
    .similar-property-price small {
        font-size: 0.8rem;
        font-weight: 500;
    }
    
    .similar-property-type {
        background: var(--bg-light);
        color: var(--primary-color);
        padding: 0.25rem 0.75rem;
        border-radius: 15px;
        font-size: 0.85rem;
        font-weight: 600;
    }
    
    @media (max-width: 768px) {
        .similar-property-compact {
            flex-direction: column;
            height: auto;
        }
        
        .similar-property-image {
            width: 100%;
            height: 200px;
        }
    }
            width: 50px;
            height: 50px;
            top: 50%;
            transform: translateY(-50%);
            background: var(--primary-color);
            border-radius: 50%;
            opacity: 0.9;
            transition: all 0.3s;
        }

        .similar-carousel .carousel-control-prev:hover,
        .similar-carousel .carousel-control-next:hover {
            opacity: 1;
            background: var(--secondary-color);
            transform: translateY(-50%) scale(1.1);
        }

        .similar-carousel .carousel-control-prev {
            left: -60px;
        }

        .similar-carousel .carousel-control-next {
            right: -60px;
        }

        @media (max-width: 1200px) {
            .similar-carousel .carousel-control-prev {
                left: -25px;
            }

            .similar-carousel .carousel-control-next {
                right: -25px;
            }
        }

        @media (max-width: 768px) {
            .similar-carousel .carousel-control-prev {
                left: 10px;
            }

            .similar-carousel .carousel-control-next {
                right: 10px;
            }
        }
    </style>
@endsection

@section('content')
    <!-- Page Header -->
    {{-- <div class="page-header" style="background: linear-gradient(135deg, #43542A 0%, #2d3a1c 100%); padding: 3rem 0;">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white">Accueil</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('properties.index') }}" class="text-white">Biens</a></li>
                    <li class="breadcrumb-item text-white active">Détails</li>
                </ol>
            </nav>
        </div>
    </div> --}}

    <section class="section-padding">
        <div class="container">
            <div class="row">
                <!-- Contenu principal -->
                <div class="col-lg-8">
                    <!-- Galerie photos - Carousel -->
                    <div class="property-gallery" data-aos="fade-up">
                        @if ($bien->hasMedia('images'))
                            @php
                                $images = $bien->getMedia('images');
                                $carouselId = 'property-carousel-' . $bien->slug;
                            @endphp

                            <div id="{{ $carouselId }}" class="carousel slide property-carousel" data-bs-ride="false">
                                <div class="carousel-inner">
                                    @foreach ($images as $index => $image)
                                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}"
                                            onclick="openLightbox({{ $index }})">
                                            <img src="{{ $image->getUrl() }}" class="d-block w-100"
                                                alt="{{ $bien->titre }} - Image {{ $index + 1 }}">
                                        </div>
                                    @endforeach
                                </div>

                                @if ($images->count() > 1)
                                    <!-- Contrôles de navigation -->
                                    <button class="carousel-control-prev" type="button"
                                        data-bs-target="#{{ $carouselId }}" data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Précédent</span>
                                    </button>
                                    <button class="carousel-control-next" type="button"
                                        data-bs-target="#{{ $carouselId }}" data-bs-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Suivant</span>
                                    </button>

                                    <!-- Indicateurs -->
                                    <div class="carousel-indicators">
                                        @foreach ($images as $index => $image)
                                            <button type="button" data-bs-target="#{{ $carouselId }}"
                                                data-bs-slide-to="{{ $index }}"
                                                class="{{ $index === 0 ? 'active' : '' }}"
                                                aria-current="{{ $index === 0 ? 'true' : 'false' }}"
                                                aria-label="Image {{ $index + 1 }}">
                                            </button>
                                        @endforeach
                                    </div>
                                @endif

                                <span class="property-badge position-absolute" style="top: 1rem; left: 1rem; z-index: 10;">
                                    {{ ucfirst($bien->type_transaction) }}
                                </span>

                                <span class="image-count-badge">
                                    <i class="ri-image-line"></i>
                                    {{ $images->count() }} {{ $images->count() > 1 ? 'photos' : 'photo' }}
                                </span>
                            </div>
                        @else
                            <div class="property-carousel">
                                <img src="https://via.placeholder.com/800x500?text=Aucune+image" alt="Aucune image">
                            </div>
                        @endif
                    </div>

                    <!-- Lightbox Modal -->
                    @if ($bien->hasMedia('images'))
                        <div class="lightbox-modal" id="lightboxModal">
                            <button class="lightbox-close" onclick="closeLightbox()">×</button>
                            <button class="lightbox-nav lightbox-prev" onclick="changeLightboxImage(-1)">
                                <i class="ri-arrow-left-s-line"></i>
                            </button>
                            <div class="lightbox-content">
                                <img id="lightboxImage" src="" alt="">
                            </div>
                            <button class="lightbox-nav lightbox-next" onclick="changeLightboxImage(1)">
                                <i class="ri-arrow-right-s-line"></i>
                            </button>
                            <div class="lightbox-counter" id="lightboxCounter"></div>
                        </div>
                    @endif

                    <!-- Titre et description -->
                    <div class="description-section" data-aos="fade-up">
                        <h1 class="mb-3">{{ $bien->titre }}</h1>
                        <p class="text-muted fs-5 mb-4">
                            <i class="ri-map-pin-line"></i> {{ $bien->adresse }}, {{ $bien->quartier }},
                            {{ $bien->ville }}
                        </p>

                        <!-- Prix mobile (visible uniquement sur mobile) -->
                        <div class="d-lg-none mb-4">
                            <div class="price-tag">
                                {{ number_format($bien->prix, 0, ',', ' ') }} FCFA
                                @if ($bien->type_transaction == 'location')
                                    <small class="fs-6">/mois</small>
                                @endif
                            </div>
                        </div>

                        <div class="d-flex gap-4 mb-4 flex-wrap">
                            @if ($bien->nombre_chambres)
                                <div class="d-flex align-items-center">
                                    <i class="ri-hotel-bed-line fs-4 text-primary me-2"></i>
                                    <div>
                                        <strong>{{ $bien->nombre_chambres }}</strong><br>
                                        <small class="text-muted">Chambres</small>
                                    </div>
                                </div>
                            @endif

                            @if ($bien->nombre_salles_bain)
                                <div class="d-flex align-items-center">
                                    <i class="ri-drop-line fs-4 text-primary me-2"></i>
                                    <div>
                                        <strong>{{ $bien->nombre_salles_bain }}</strong><br>
                                        <small class="text-muted">Salles de bain</small>
                                    </div>
                                </div>
                            @endif

                            @if ($bien->surface)
                                <div class="d-flex align-items-center">
                                    <i class="ri-ruler-line fs-4 text-primary me-2"></i>
                                    <div>
                                        <strong>{{ $bien->surface }}</strong><br>
                                        <small class="text-muted">m²</small>
                                    </div>
                                </div>
                            @endif

                            @if ($bien->nombre_pieces)
                                <div class="d-flex align-items-center">
                                    <i class="ri-layout-grid-line fs-4 text-primary me-2"></i>
                                    <div>
                                        <strong>{{ $bien->nombre_pieces }}</strong><br>
                                        <small class="text-muted">Pièces</small>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <hr>

                        <h4 class="mb-3">Description</h4>
                        <div class="text-muted" style="white-space: pre-line;">{{ $bien->description }}</div>
                    </div>

                    <!-- Équipements -->
                    @if ($bien->equipements->count() > 0)
                        <div class="description-section" data-aos="fade-up">
                            <h4 class="mb-3">
                                <i class="ri-tools-line"></i> Équipements et commodités
                            </h4>
                            <div class="d-flex flex-wrap">
                                @foreach ($bien->equipements as $equipement)
                                    <span class="equipement-badge">
                                        <i class="{{ $equipement->icone ?? 'ri-check-line' }}"></i>
                                        {{ $equipement->nom }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Formulaire de contact -->
                    <div class="contact-form" data-aos="fade-up" id="contact-form">
                        <h4 class="mb-4">
                            <i class="ri-message-3-line"></i> Intéressé par ce bien ?
                        </h4>

                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <div class="alert alert-info">
                            <i class="ri-information-line"></i> Cliquez sur le bouton "Je suis intéressé" dans la barre latérale pour devenir prospect et envoyer votre demande.
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Informations principales -->
                    <div class="property-details-card" data-aos="fade-left">
                        <div class="price-tag">
                            {{ number_format($bien->prix, 0, ',', ' ') }} FCFA
                            @if ($bien->type_transaction == 'location')
                                <small class="fs-6">/mois</small>
                            @endif
                        </div>

                        <ul class="feature-list">
                            <li>
                                <span><i class="ri-money-dollar-circle-line"></i> Type</span>
                                <strong>{{ ucfirst($bien->type_transaction) }}</strong>
                            </li>
                            <li>
                                <span><i class="ri-home-4-line"></i> Type de bien</span>
                                <strong>{{ $bien->typeBien->nom ?? 'N/A' }}</strong>
                            </li>
                            @if ($bien->surface)
                                <li>
                                    <span><i class="ri-ruler-line"></i> Superficie</span>
                                    <strong>{{ $bien->surface }} m²</strong>
                                </li>
                            @endif
                            @if ($bien->nombre_pieces)
                                <li>
                                    <span><i class="ri-layout-grid-line"></i> Pièces</span>
                                    <strong>{{ $bien->nombre_pieces }}</strong>
                                </li>
                            @endif
                            <li>
                                <span><i class="ri-eye-line"></i> Vues</span>
                                <strong>{{ $bien->nombre_vues ?? 0 }}</strong>
                            </li>
                            <li>
                                <span><i class="ri-calendar-line"></i> Publié le</span>
                                <strong>{{ $bien->created_at->format('d/m/Y') }}</strong>
                            </li>
                        </ul>

                        <div class="mt-4">
                            {{-- <a href="tel:+33123456789" class="btn btn-outline-primary w-100 mb-2">
                                <i class="ri-phone-line"></i> Appeler
                            </a> --}}
                            <button type="button" class="btn btn-accent w-100" data-bs-toggle="modal" data-bs-target="#interestModal">
                                <i class="ri-message-3-line"></i> Je suis intéressé
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Biens similaires en format compact -->
    @if ($biensSimilaires->count() > 0)
        <section class="similar-properties-section">
            <div class="container">
                <div class="text-center mb-5">
                    <h2 class="section-title" data-aos="fade-up">Biens similaires</h2>
                    <p class="section-subtitle" data-aos="fade-up">Découvrez d'autres biens qui pourraient vous intéresser
                    </p>
                </div>

                <div class="row">
                    @foreach ($biensSimilaires as $similaire)
                        <div class="col-12" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 100 }}">
                            <div class="similar-property-compact">
                                <div class="similar-property-image">
                                    @if($similaire->hasMedia('images'))
                                        <img src="{{ $similaire->getFirstMediaUrl('images') }}" alt="{{ $similaire->titre }}">
                                    @else
                                        <img src="https://via.placeholder.com/250x180?text=Aucune+image" alt="Aucune image">
                                    @endif
                                    
                                    <span class="similar-property-badge {{ $similaire->type_transaction == 'vente' ? 'badge-vente' : '' }}">
                                        {{ ucfirst($similaire->type_transaction) }}
                                    </span>
                                </div>
                                
                                <div class="similar-property-content">
                                    <div>
                                        <h3 class="similar-property-title">
                                            <a href="{{ route('properties.show', $similaire->slug) }}" class="text-decoration-none text-dark">
                                                {{ $similaire->titre }}
                                            </a>
                                        </h3>
                                        
                                        <div class="similar-property-location">
                                            <i class="ri-map-pin-line"></i>
                                            <span>{{ $similaire->ville }}, {{ $similaire->quartier }}</span>
                                        </div>
                                        
                                        <div class="similar-property-features">
                                            @if($similaire->typeBien)
                                                <div class="similar-feature-item">
                                                    <i class="ri-building-line"></i>
                                                    <span>{{ $similaire->typeBien->nom }}</span>
                                                </div>
                                            @endif
                                            
                                            @if($similaire->nombre_chambres)
                                                <div class="similar-feature-item">
                                                    <i class="ri-hotel-bed-line"></i>
                                                    <span>{{ $similaire->nombre_chambres }} Ch.</span>
                                                </div>
                                            @endif
                                            
                                            @if($similaire->nombre_salles_bain)
                                                <div class="similar-feature-item">
                                                    <i class="ri-drop-line"></i>
                                                    <span>{{ $similaire->nombre_salles_bain }} SDB</span>
                                                </div>
                                            @endif
                                            
                                            @if($similaire->surface)
                                                <div class="similar-feature-item">
                                                    <i class="ri-ruler-line"></i>
                                                    <span>{{ $similaire->surface }} m²</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="similar-property-footer">
                                        <div class="similar-property-price">
                                            {{ number_format($similaire->prix, 0, ',', ' ') }} FCFA
                                            @if($similaire->type_transaction == 'location')
                                                <small>/mois</small>
                                            @endif
                                        </div>
                                        
                                        <a href="{{ route('properties.show', $similaire->slug) }}" class="btn btn-sm btn-primary">
                                            <i class="ri-eye-line"></i> Voir détails
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

<!-- Modal: Formulaire d'intérêt (Prospect) -->
<div class="modal fade" id="interestModal" tabindex="-1" aria-labelledby="interestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('properties.contact', $bien->slug) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="interestModalLabel">
                        <i class="ri-message-3-line"></i> Je suis intéressé(e) par ce bien
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="alert alert-info mb-3">
                        <i class="ri-information-line"></i> Remplissez ce formulaire pour devenir prospect. Nous vous recontacterons rapidement.
                    </div>

                    <div class="row g-3">
                        <div class="col-12">
                            <label for="username" class="form-label">Nom complet *</label>
                            <input type="text" class="form-control @error('username') is-invalid @enderror" 
                                id="username" name="username" value="{{ old('username') }}" required
                                placeholder="Ex: Jean Kouassi">
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                id="email" name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Téléphone *</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                id="phone" name="phone" value="{{ old('phone') }}" required
                                placeholder="+225 XX XX XX XX XX">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label for="message_prospect" class="form-label">Message *</label>
                            <textarea class="form-control @error('message') is-invalid @enderror" 
                                id="message_prospect" name="message" rows="4" required
                                placeholder="Décrivez votre intérêt pour ce bien...">{{ old('message', 'Bonjour, je suis intéressé(e) par ce bien. Pouvez-vous me contacter pour plus d\'informations ?') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="ri-close-line"></i> Annuler
                    </button>
                    <button type="submit" class="btn btn-accent">
                        <i class="ri-send-plane-fill"></i> Envoyer ma demande
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
    <script>
        // Rouvrir le modal si des erreurs de validation existent
        @if($errors->any())
            document.addEventListener('DOMContentLoaded', function() {
                var interestModal = new bootstrap.Modal(document.getElementById('interestModal'));
                interestModal.show();
            });
        @endif

        @if ($bien->hasMedia('images'))
            // Images du bien
            const propertyImages = [
                @foreach ($bien->getMedia('images') as $media)
                    '{{ $media->getUrl() }}',
                @endforeach
            ];

            let currentLightboxIndex = 0;

            // Ouvrir le lightbox
            function openLightbox(index) {
                currentLightboxIndex = index;
                updateLightboxImage();
                document.getElementById('lightboxModal').classList.add('active');
                document.body.style.overflow = 'hidden';
            }

            // Fermer le lightbox
            function closeLightbox() {
                document.getElementById('lightboxModal').classList.remove('active');
                document.body.style.overflow = 'auto';
            }

            // Changer l'image dans le lightbox
            function changeLightboxImage(direction) {
                currentLightboxIndex += direction;

                if (currentLightboxIndex < 0) {
                    currentLightboxIndex = propertyImages.length - 1;
                } else if (currentLightboxIndex >= propertyImages.length) {
                    currentLightboxIndex = 0;
                }

                updateLightboxImage();
            }

            // Mettre à jour l'image affichée
            function updateLightboxImage() {
                document.getElementById('lightboxImage').src = propertyImages[currentLightboxIndex];
                document.getElementById('lightboxCounter').textContent =
                    `${currentLightboxIndex + 1} / ${propertyImages.length}`;
            }

            // Navigation au clavier
            document.addEventListener('keydown', function(e) {
                const modal = document.getElementById('lightboxModal');
                if (modal.classList.contains('active')) {
                    if (e.key === 'Escape') {
                        closeLightbox();
                    } else if (e.key === 'ArrowLeft') {
                        changeLightboxImage(-1);
                    } else if (e.key === 'ArrowRight') {
                        changeLightboxImage(1);
                    }
                }
            });

            // Fermer en cliquant en dehors de l'image
            document.getElementById('lightboxModal')?.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeLightbox();
                }
            });
        @endif
    </script>
@endsection
