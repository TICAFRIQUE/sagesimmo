<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sages Immo - Plateforme Immobilière #1 en Côte d\'Ivoire | Vente, Location & Gestion')</title>

    {{-- SEO Meta Tags --}}
    <meta name="description" content="@yield('meta_description', 'Sages Immo : Votre partenaire immobilier de confiance en Côte d\'Ivoire. Achat, vente, location et gestion de biens immobiliers. Plus de 10 ans d\'expérience à Abidjan. Trouvez votre bien idéal aujourd\'hui !')">
    <meta name="keywords" content="@yield('meta_keywords', 'agence immobilière Abidjan, vente appartement Côte d\'Ivoire, location maison Abidjan, achat villa Cocody, gestion immobilière CI, immobilier Marcory, appartement à louer Plateau, terrain à vendre Abidjan, Sages Immo, agence immobilière Côte d\'Ivoire, propriété Riviera, immobilier Yopougon')">
    <meta name="author" content="Sages Immo">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <meta name="geo.region" content="CI">
    <meta name="geo.placename" content="Abidjan">
    <link rel="canonical" href="@yield('canonical', request()->url())">

    {{-- Open Graph / Facebook --}}
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:url" content="@yield('og_url', request()->url())">
    <meta property="og:title" content="@yield('og_title', 'Sages Immo - Agence Immobilière #1 en Côte d\'Ivoire | Vente, Location & Gestion')">
    <meta property="og:description" content="@yield('og_description', 'Trouvez votre bien immobilier idéal en Côte d\'Ivoire avec Sages Immo. Villas, appartements, terrains à vendre ou à louer. Expert en gestion immobilière depuis plus de 10 ans.')">
    <meta property="og:image" content="@yield('og_image', asset('images/logo/logo.png'))">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="Sages Immo - Votre expert immobilier en Côte d\'Ivoire">
    <meta property="og:site_name" content="Sages Immo">
    <meta property="og:locale" content="fr_FR">
    <meta property="og:locale:alternate" content="fr_CI">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="@yield('twitter_url', request()->url())">
    <meta name="twitter:title" content="@yield('twitter_title', 'Sages Immo - Immobilier en Côte d\'Ivoire | Vente & Location')">
    <meta name="twitter:description" content="@yield('twitter_description', 'Achat, vente, location et gestion de biens immobiliers en Côte d\'Ivoire. Plus de 10 ans d\'expertise au service de vos projets.')">
    <meta name="twitter:image" content="@yield('twitter_image', asset('images/logo/logo.png'))">
    <meta name="twitter:image:alt" content="Sages Immo - Plateforme immobilière Côte d\'Ivoire">
    <meta name="twitter:site" content="@SagesImmo">
    <meta name="twitter:creator" content="@SagesImmo">

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('images/favicon/favicon.ico') }}">
    {{-- Favicons --}}
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/favicon/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('images/favicon/site.webmanifest') }}">





    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;600;700&display=swap"
        rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Remix Icons -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />

    <!-- Custom CSS -->
    @include('frontend.layouts.partials.styles')

    @yield('css')


</head>

<body>
    <!-- Header -->
    @include('frontend.layouts.partials.header')

    <!-- Main Content -->
    <main>
        @include('sweetalert::alert')

        @yield('content')
    </main>

    <!-- Footer -->
    @include('frontend.layouts.partials.footer')

    <!-- Floating Buttons -->
    <div class="floating-buttons">

        <!--bouton accueil / home-->
        <a href="{{ route('home') }}" class="floating-btn home-btn  " data-bs-toggle="tooltip" data-bs-placement="left"
            title="Accueil">
            <i class="ri-home-4-line"></i>
        </a>


        <!-- Bouton flottant recherche bien (mobile) -->
        <button class="floating-btn search-btn d-block d-md-none" data-bs-toggle="modal"
            data-bs-target="#searchBienModal" title="Rechercher un bien">
            <i class="ri-search-line"></i>
        </button>


        <!-- WhatsApp Button -->
        <a href="https://wa.me/{{ $data_parametre?->contact_whatsapp }}?text=Bonjour%2C%20je%20souhaite%20avoir%20des%20informations"
            target="_blank" class="floating-btn whatsapp-btn" data-bs-toggle="tooltip" data-bs-placement="left"
            title="Contactez-nous sur WhatsApp">
            <i class="ri-whatsapp-line"></i>
        </a>

        <!-- Scroll to Top Button -->
        {{-- <button id="scrollToTop" class="floating-btn scroll-top-btn"  data-bs-toggle="tooltip"
            data-bs-placement="left" title="Retour en haut">
            <i class="ri-arrow-up-line"></i>
        </button> --}}
    </div>

    <style>
        .floating-buttons {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 1050;
            /* Au-dessus de la barre mobile CTA (z-index: 1040) */
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .floating-btn {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            text-decoration: none;
            animation: pulse 2s infinite;
        }

        .floating-btn i {
            font-size: 28px;
            color: white;
        }

        .whatsapp-btn {
            background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
        }

        .whatsapp-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(37, 211, 102, 0.4);
        }

        .scroll-top-btn {
            background: linear-gradient(135deg, #43542A 0%, #2d3a1c 100%);
        }

        .scroll-top-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(67, 84, 42, 0.4);
        }

        /* Style visible pour le bouton de recherche */
        .search-btn {
            background: linear-gradient(135deg, #4f46e5 0%, #06b6d4 100%);
        }

        .search-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(79, 70, 229, 0.35);
        }

        /* style pour le bouton d'accueil */
        .home-btn {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
        }

        .home-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(30, 58, 138, 0.4);
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            }

            50% {
                box-shadow: 0 4px 25px rgba(0, 0, 0, 0.3);
            }

            100% {
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .floating-buttons {
                bottom: 90px;
                /* Ajusté pour ne pas chevaucher la barre CTA mobile */
                right: 20px;
            }

            .floating-btn {
                width: 50px;
                height: 50px;
            }

            .floating-btn i {
                font-size: 24px;
            }
        }

        /* Ajustement supplémentaire pour les petits écrans */
        @media (max-width: 576px) {
            .floating-buttons {
                bottom: 85px;
                right: 15px;
                gap: 10px;
            }

            .floating-btn {
                width: 48px;
                height: 48px;
            }
        }
    </style>

    {{-- Search modal included globally so floating button works on every page --}}
    @include('frontend.layouts.partials.search-modal')

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true
        });

        // Scroll to Top functionality
        $(document).ready(function() {
            const scrollTopBtn = $('#scrollToTop');

            // Show/hide scroll to top button
            $(window).scroll(function() {
                if ($(this).scrollTop() > 300) {
                    scrollTopBtn.fadeIn();
                } else {
                    scrollTopBtn.fadeOut();
                }
            });

            // Smooth scroll to top
            scrollTopBtn.click(function() {
                $('html, body').animate({
                    scrollTop: 0
                }, 600);
                return false;
            });

            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });

        // Initialize Select2
        $(document).ready(function() {
            // Initialize Select2 on all select elements
            $('select').not('.no-select2').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: function() {
                    return $(this).data('placeholder') || 'Sélectionnez une option';
                },
                allowClear: true,
                language: {
                    noResults: function() {
                        return "Aucun résultat trouvé";
                    },
                    searching: function() {
                        return "Recherche en cours...";
                    },
                    inputTooShort: function(args) {
                        var remainingChars = args.minimum - args.input.length;
                        return "Veuillez entrer " + remainingChars + " caractère(s) supplémentaire(s)";
                    },
                    loadingMore: function() {
                        return "Chargement de plus de résultats...";
                    },
                    maximumSelected: function(args) {
                        return "Vous pouvez seulement sélectionner " + args.maximum + " élément(s)";
                    }
                }
            });

            // Reinitialize Select2 on dynamic content
            $(document).on('DOMNodeInserted', function(e) {
                if ($(e.target).is('select') && !$(e.target).hasClass('no-select2')) {
                    $(e.target).select2({
                        theme: 'bootstrap-5',
                        width: '100%',
                        allowClear: true
                    });
                }
            });
        });
    </script>

    @yield('scripts')
</body>

</html>
