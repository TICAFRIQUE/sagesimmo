<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sage Immo - Plateforme de Gestion Immobilière')</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}">

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
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

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
        <!-- WhatsApp Button -->
        <a href="https://wa.me/{{ $data_parametre?->contact_whatsapp }}?text=Bonjour%2C%20je%20souhaite%20avoir%20des%20informations" 
           target="_blank" 
           class="floating-btn whatsapp-btn"
           data-bs-toggle="tooltip" 
           data-bs-placement="left" 
           title="Contactez-nous sur WhatsApp">
            <i class="ri-whatsapp-line"></i>
        </a>

        <!-- Scroll to Top Button -->
        <button id="scrollToTop" 
                class="floating-btn scroll-top-btn" 
                style="display: none;"
                data-bs-toggle="tooltip" 
                data-bs-placement="left" 
                title="Retour en haut">
            <i class="ri-arrow-up-line"></i>
        </button>
    </div>

    <style>
        .floating-buttons {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 1000;
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
                bottom: 20px;
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
    </style>

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
