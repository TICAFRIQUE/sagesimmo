<style>
    :root {
        --primary-color: #43542A;
        --secondary-color: #364423;
        --accent-color: #E84E1B;
        --text-dark: #1e293b;
        --text-light: #64748b;
        --border-color: #e2e8f0;
        --bg-light: #f8fafc;
    }
    
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    
    body {
        font-family: 'Inter', sans-serif;
        color: var(--text-dark);
        line-height: 1.6;
        overflow-x: hidden;
    }
    }
    
    h1, h2, h3, h4, h5, h6 {
        font-family: 'Playfair Display', serif;
        font-weight: 600;
    }
    
    /* Header */
    .site-header {
        background: #fff;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        position: sticky;
        top: 0;
        z-index: 1000;
    }
    
    .navbar {
        padding: 1rem 0;
    }
    
    .navbar-brand {
        font-family: 'Playfair Display', serif;
        font-size: 1.8rem;
        font-weight: 700;
        color: var(--primary-color) !important;
    }
    
    .nav-link {
        color: var(--text-dark);
        font-weight: 500;
        padding: 0.5rem 1rem !important;
        transition: color 0.3s;
    }
    
    .nav-link:hover {
        color: var(--accent-color);
    }
    
    .nav-link.active {
        color: var(--accent-color);
        font-weight: 600;
    }
    
    .btn-primary {
        background: var(--primary-color);
        border: none;
        padding: 0.625rem 1.5rem;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.3s;
    }
    
    .btn-primary:hover {
        background: var(--secondary-color);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(67, 84, 42, 0.3);
    }
    
    .btn-accent {
        background: var(--accent-color);
        border: none;
        color: white;
        padding: 0.625rem 1.5rem;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.3s;
    }
    
    .btn-accent:hover {
        background: #d14416;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(232, 78, 27, 0.4);
    }
    
    .btn-outline-primary {
        color: var(--primary-color);
        border: 2px solid var(--primary-color);
        padding: 0.5rem 1.5rem;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.3s;
    }
    
    .btn-outline-primary:hover {
        background: var(--primary-color);
        color: white;
    }
    
    .btn-outline-accent {
        color: var(--accent-color);
        border: 2px solid var(--accent-color);
        padding: 0.5rem 1.5rem;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.3s;
    }
    
    .btn-outline-accent:hover {
        background: var(--accent-color);
        color: white;
    }
    
    /* Hero/Banner Section */
    .hero-section {
        /* background-image: linear-gradient(rgba(67, 84, 42, 0.7), rgba(45, 58, 28, 0.8)), url('/images/hero-bg.jpg'); */
        background-size: auto;
        background-position: center;
        background-repeat: no-repeat;
        background-attachment: fixed;
        padding: 70px 0 70px;
        position: relative;
        overflow: hidden;
    }
    
    .hero-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('images/banniere/banner3.jpg');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        filter: brightness(0.7);
        pointer-events: none;
        z-index: -1;
    }
    
    .hero-content h1 {
        font-size: 3.5rem;
        color: white;
        margin-bottom: 1.5rem;
        font-weight: 700;
    }
    
    .hero-content p {
        font-size: 1.25rem;
        color: rgba(255,255,255,0.9);
        margin-bottom: 2rem;
    }
    
    /* Search Form */
    .search-form {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        box-shadow: 0 20px 60px rgba(0,0,0,0.15);
    }
    
    .search-tabs {
        margin-bottom: 1.5rem;
        border-bottom: 2px solid var(--border-color);
    }
    
    .search-tabs .nav-link {
        color: var(--text-light);
        border: none;
        border-bottom: 3px solid transparent;
        padding: 0.75rem 1.5rem;
        margin-bottom: -2px;
    }
    
    .search-tabs .nav-link.active {
        color: var(--accent-color);
        border-bottom-color: var(--accent-color);
        background: transparent;
    }
    
    /* Property Cards */
    .property-card {
        border: none;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        transition: all 0.3s;
        height: 100%;
    }
    
    .property-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.15);
    }
    
    .property-image {
        position: relative;
        height: 250px;
        overflow: hidden;
    }
    
    .property-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s;
    }
    
    .property-card:hover .property-image img {
        transform: scale(1.05);
    }
    
    /* Carrousel d'images pour les annonces */
    .property-carousel {
        height: 100%;
    }
    
    .property-carousel .carousel-inner {
        height: 100%;
    }
    
    .property-carousel .carousel-item {
        height: 100%;
    }
    
    .property-carousel .carousel-item img {
        height: 250px;
        object-fit: cover;
    }
    
    /* Contrôles du carrousel */
    .property-carousel .carousel-control-prev,
    .property-carousel .carousel-control-next {
        width: 40px;
        height: 40px;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(0, 0, 0, 0.5);
        border-radius: 50%;
        opacity: 0;
        transition: opacity 0.3s;
    }
    
    .property-card:hover .carousel-control-prev,
    .property-card:hover .carousel-control-next {
        opacity: 1;
    }
    
    .property-carousel .carousel-control-prev {
        left: 10px;
    }
    
    .property-carousel .carousel-control-next {
        right: 10px;
    }
    
    .property-carousel .carousel-control-prev-icon,
    .property-carousel .carousel-control-next-icon {
        width: 20px;
        height: 20px;
    }
    
    /* Indicateurs (points) personnalisés */
    .property-carousel .carousel-indicators {
        bottom: 10px;
        margin-bottom: 0;
        z-index: 2;
    }
    
    .property-carousel .carousel-indicators button {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        border: none;
        background-color: rgba(255, 255, 255, 0.5);
        margin: 0 4px;
        padding: 0;
        transition: all 0.3s;
    }
    
    .property-carousel .carousel-indicators button.active {
        width: 10px;
        height: 10px;
        background-color: white;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }
    
    .property-carousel .carousel-indicators button:hover {
        background-color: rgba(255, 255, 255, 0.8);
    }
    
    .property-badge {
        position: absolute;
        top: 1rem;
        left: 1rem;
        background: var(--primary-color);
        color: white;
        padding: 0.375rem 0.875rem;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 600;
    }
    
    .property-badge.badge-vente {
        background: var(--accent-color);
    }
    
    .property-price {
        position: absolute;
        bottom: 1rem;
        right: 1rem;
        background: rgba(0,0,0,0.75);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-weight: 700;
        font-size: 1.125rem;
    }
    
    .property-info {
        padding: 1.5rem;
    }
    
    .property-title {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: var(--text-dark);
    }
    
    .property-location {
        color: var(--text-light);
        display: flex;
        align-items: center;
        gap: 0.375rem;
        margin-bottom: 1rem;
    }
    
    .property-type {
        color: var(--accent-color);
        display: flex;
        align-items: center;
        gap: 0.375rem;
        font-size: 0.9rem;
        font-weight: 600;
    }
    
    .property-type i {
        font-size: 1.1rem;
    }
    
    .property-features {
        display: flex;
        gap: 1.5rem;
        padding-top: 1rem;
        border-top: 1px solid var(--border-color);
    }
    
    .feature-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--text-light);
        font-size: 0.875rem;
    }
    
    /* Section Titles */
    .section-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 1rem;
        text-align: center;
    }
    
    .section-subtitle {
        color: var(--text-light);
        text-align: center;
        font-size: 1.125rem;
        margin-bottom: 3rem;
    }
    
    /* Stats Section */
    .stats-section {
        background: var(--bg-light);
        padding: 4rem 0;
    }
    
    .stat-card {
        text-align: center;
        padding: 2rem;
    }
    
    .stat-number {
        font-size: 3rem;
        font-weight: 700;
        color: var(--accent-color);
        margin-bottom: 0.5rem;
    }
    
    .stat-label {
        color: var(--text-light);
        font-size: 1.125rem;
    }
    
    /* Footer */
    .site-footer {
        background: #1e293b;
        color: rgba(255,255,255,0.8);
        padding: 4rem 0 2rem;
    }
    
    .footer-brand {
        font-family: 'Playfair Display', serif;
        font-size: 1.5rem;
        font-weight: 700;
        color: white;
        margin-bottom: 1rem;
    }
    
    .footer-links {
        list-style: none;
        padding: 0;
    }
    
    .footer-links li {
        margin-bottom: 0.75rem;
    }
    
    .footer-links a {
        color: rgba(255,255,255,0.7);
        text-decoration: none;
        transition: color 0.3s;
    }
    
    .footer-links a:hover {
        color: white;
    }
    
    .footer-bottom {
        border-top: 1px solid rgba(255,255,255,0.1);
        padding-top: 2rem;
        margin-top: 3rem;
        text-align: center;
        color: rgba(255,255,255,0.5);
    }
    
    /* Utilities */
    .section-padding {
        padding: 2rem 0;
    }
    
    /* Filter Sidebar */
    .filter-sidebar {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    
    .filter-title {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid var(--border-color);
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .hero-content h1 {
            font-size: 2rem;
        }
        
        .section-title {
            font-size: 1.75rem;
        }
        
        .property-features {
            flex-wrap: wrap;
        }
    }

    /* ========================================
       Remplacer les couleurs bleues Bootstrap par les couleurs du logo
       ======================================== */
    
    /* Focus sur les champs de formulaire - Remplacer le bleu par la couleur primaire */
    .form-control:focus,
    .form-select:focus,
    textarea:focus,
    input[type="text"]:focus,
    input[type="email"]:focus,
    input[type="password"]:focus,
    input[type="tel"]:focus,
    input[type="number"]:focus,
    input[type="date"]:focus,
    input[type="search"]:focus {
        border-color: var(--primary-color) !important;
        box-shadow: 0 0 0 0.25rem rgba(67, 84, 42, 0.25) !important;
        outline: none !important;
    }

    /* Focus sur les boutons */
    .btn:focus,
    button:focus,
    .btn-primary:focus,
    .btn-outline-primary:focus {
        box-shadow: 0 0 0 0.25rem rgba(67, 84, 42, 0.25) !important;
        outline: none !important;
    }

    .btn-accent:focus,
    .btn-outline-accent:focus {
        box-shadow: 0 0 0 0.25rem rgba(232, 78, 27, 0.25) !important;
        outline: none !important;
    }

    /* Liens - Remplacer le bleu par la couleur d'accent */
    a {
        color: var(--accent-color);
        text-decoration: none;
    }

    a:hover {
        color: var(--primary-color);
    }

    /* Texte primaire Bootstrap */
    .text-primary {
        color: var(--primary-color) !important;
    }

    /* Background primaire Bootstrap */
    .bg-primary {
        background-color: var(--primary-color) !important;
    }

    /* Border primaire Bootstrap */
    .border-primary {
        border-color: var(--primary-color) !important;
    }

    /* Badges */
    .badge.bg-primary {
        background-color: var(--primary-color) !important;
    }

    /* Alertes */
    .alert-primary {
        background-color: rgba(67, 84, 42, 0.1) !important;
        border-color: var(--primary-color) !important;
        color: var(--primary-color) !important;
    }

    /* Pagination */
    .pagination .page-link {
        color: var(--primary-color);
    }

    .pagination .page-link:hover {
        color: var(--accent-color);
        background-color: rgba(67, 84, 42, 0.1);
        border-color: var(--primary-color);
    }

    .pagination .page-item.active .page-link {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }

    /* Checkbox et Radio personnalisés */
    .form-check-input:checked {
        background-color: var(--primary-color) !important;
        border-color: var(--primary-color) !important;
    }

    .form-check-input:focus {
        border-color: var(--primary-color) !important;
        box-shadow: 0 0 0 0.25rem rgba(67, 84, 42, 0.25) !important;
    }

    /* Spinner/Loader */
    .spinner-border {
        color: var(--primary-color);
    }

    /* Progress bar */
    .progress-bar {
        background-color: var(--primary-color);
    }

    /* Nav tabs et pills */
    .nav-tabs .nav-link.active {
        color: var(--accent-color) !important;
        border-bottom-color: var(--accent-color) !important;
    }

    .nav-pills .nav-link.active {
        background-color: var(--primary-color) !important;
    }

    /* Liste group */
    .list-group-item.active {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }

    /* Dropdown */
    .dropdown-item:hover,
    .dropdown-item:focus {
        background-color: rgba(67, 84, 42, 0.1);
        color: var(--primary-color);
    }

    .dropdown-item.active {
        background-color: var(--primary-color);
    }

    /* Modal */
    .modal-header .btn-close:focus {
        box-shadow: 0 0 0 0.25rem rgba(67, 84, 42, 0.25) !important;
    }

    /* Accordion */
    .accordion-button:not(.collapsed) {
        background-color: rgba(67, 84, 42, 0.1);
        color: var(--primary-color);
    }

    .accordion-button:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.25rem rgba(67, 84, 42, 0.25);
    }

    /* Breadcrumb */
    .breadcrumb-item.active {
        color: var(--accent-color);
    }

    .breadcrumb-item + .breadcrumb-item::before {
        color: var(--text-light);
    }

    /* Outline sur tous les éléments focusables */
    *:focus-visible {
        outline: 2px solid var(--primary-color) !important;
        outline-offset: 2px;
    }

    /* Range input */
    input[type="range"]::-webkit-slider-thumb {
        background: var(--primary-color);
    }

    input[type="range"]::-moz-range-thumb {
        background: var(--primary-color);
    }

    input[type="range"]:focus::-webkit-slider-thumb {
        box-shadow: 0 0 0 0.25rem rgba(67, 84, 42, 0.25);
    }

    /* Switch */
    .form-switch .form-check-input:checked {
        background-color: var(--primary-color) !important;
        border-color: var(--primary-color) !important;
    }

    /* ========== Select2 Custom Styles ========== */
    .select2-container--bootstrap-5 .select2-selection {
        border-radius: 8px;
        border: 1px solid var(--border-color);
        min-height: 38px;
        transition: all 0.3s ease;
    }

    .select2-container--bootstrap-5 .select2-selection:focus,
    .select2-container--bootstrap-5.select2-container--focus .select2-selection,
    .select2-container--bootstrap-5.select2-container--open .select2-selection {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.25rem rgba(67, 84, 42, 0.15);
    }

    .select2-container--bootstrap-5 .select2-selection--single {
        padding: 0.375rem 0.75rem;
    }

    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
        line-height: 1.5;
        padding-left: 0;
        color: var(--text-dark);
    }

    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__placeholder {
        color: var(--text-light);
    }

    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__arrow {
        height: 100%;
        right: 8px;
    }

    .select2-container--bootstrap-5 .select2-dropdown {
        border-radius: 8px;
        border: 1px solid var(--border-color);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .select2-container--bootstrap-5 .select2-results__option {
        padding: 0.5rem 0.75rem;
        transition: background-color 0.2s ease;
    }

    .select2-container--bootstrap-5 .select2-results__option--highlighted {
        background-color: var(--primary-color) !important;
        color: white;
    }

    .select2-container--bootstrap-5 .select2-results__option--selected {
        background-color: rgba(67, 84, 42, 0.1);
        color: var(--primary-color);
    }

    .select2-container--bootstrap-5 .select2-search--dropdown .select2-search__field {
        border: 1px solid var(--border-color);
        border-radius: 6px;
        padding: 0.5rem 0.75rem;
        margin: 0.5rem;
        width: calc(100% - 1rem);
    }

    .select2-container--bootstrap-5 .select2-search--dropdown .select2-search__field:focus {
        border-color: var(--primary-color);
        outline: none;
        box-shadow: 0 0 0 0.25rem rgba(67, 84, 42, 0.15);
    }

    .select2-container--bootstrap-5 .select2-selection--multiple {
        padding: 0.25rem 0.5rem;
        min-height: 38px;
    }

    .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice {
        background-color: var(--primary-color);
        border: none;
        color: white;
        border-radius: 6px;
        padding: 0.25rem 0.5rem;
        margin: 0.25rem;
    }

    .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice__remove {
        color: white;
        margin-right: 0.5rem;
        opacity: 0.8;
        transition: opacity 0.2s;
    }

    .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice__remove:hover {
        opacity: 1;
    }

    /* Select2 sizing */
    .select2-container--bootstrap-5 .select2-selection.form-select-sm {
        min-height: 31px;
        font-size: 0.875rem;
    }

    .select2-container--bootstrap-5 .select2-selection.form-select-lg {
        min-height: 48px;
        font-size: 1.25rem;
    }

    /* Select2 Clear button */
    .select2-container--bootstrap-5 .select2-selection__clear {
        color: var(--text-light);
        margin-right: 0.5rem;
        transition: color 0.2s;
    }

    .select2-container--bootstrap-5 .select2-selection__clear:hover {
        color: var(--accent-color);
    }

    /* Animation d'ouverture */
    @keyframes select2FadeIn {
        from {
            opacity: 0;
            transform: translateY(-5px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .select2-container--bootstrap-5.select2-container--open .select2-dropdown {
        animation: select2FadeIn 0.2s ease-out;
    }

    /* Loading state */
    .select2-container--bootstrap-5 .select2-results__option--loading {
        color: var(--text-light);
    }

    /* No results */
    .select2-container--bootstrap-5 .select2-results__message {
        color: var(--text-light);
        padding: 1rem;
        text-align: center;
    }

    /* Hover effects */
    .hover-shadow {
        transition: all 0.3s ease;
    }

    .hover-shadow:hover {
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
        transform: translateY(-5px);
    }

    .transition-all {
        transition: all 0.3s ease;
    }

    /* About section image effects */
    .img-fluid.rounded-4 {
        transition: transform 0.3s ease;
    }

    .img-fluid.rounded-4:hover {
        transform: scale(1.02);
    }

    /* Prevent decorative elements from causing overflow on mobile */
    @media (max-width: 991.98px) {
        .position-absolute.translate-middle {
            display: none;
        }
    }
</style>

