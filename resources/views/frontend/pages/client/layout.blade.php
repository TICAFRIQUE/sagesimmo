@extends('frontend.layouts.master')

@section('title', 'Mon Espace - Sage Immo')

@section('css')
<style>
    .client-dashboard {
        padding: 4rem 0;
        background: #f8f9fa;
        min-height: calc(100vh - 200px);
    }

    .client-sidebar {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        position: sticky;
        top: 100px;
    }

    .client-sidebar .nav-link {
        padding: 0.75rem 1rem;
        margin-bottom: 0.5rem;
        border-radius: 8px;
        color: #495057;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .client-sidebar .nav-link:hover,
    .client-sidebar .nav-link.active {
        background: #0ab39c;
        color: white;
    }

    .client-sidebar .nav-link i {
        font-size: 1.2rem;
    }

    .client-content {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .stat-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 12px;
        padding: 1.5rem;
        color: white;
        margin-bottom: 1.5rem;
        transition: transform 0.3s;
    }

    .stat-card:hover {
        transform: translateY(-5px);
    }

    .stat-card.success {
        background: linear-gradient(135deg, #0ab39c 0%, #3dd598 100%);
    }

    .stat-card.warning {
        background: linear-gradient(135deg, #f7b84b 0%, #f06548 100%);
    }

    .stat-card.info {
        background: linear-gradient(135deg, #299cdb 0%, #3dd5f3 100%);
    }

    .stat-card .stat-icon {
        font-size: 2.5rem;
        opacity: 0.8;
    }

    .stat-card .stat-value {
        font-size: 2rem;
        font-weight: 700;
        margin: 0.5rem 0;
    }

    .stat-card .stat-label {
        opacity: 0.9;
        font-size: 0.9rem;
    }

    .demande-card {
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
        transition: all 0.3s;
    }

    .demande-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transform: translateX(5px);
    }

    .visite-card {
        background: #f0f8ff;
        border-left: 4px solid #299cdb;
        border-radius: 8px;
        padding: 1.5rem;
        margin-bottom: 1rem;
    }

    .badge-lg {
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
    }
</style>
@endsection

@section('content')
<section class="client-dashboard">
    <div class="container">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3 mb-4">
                @include('frontend.pages.client.partials.sidebar')
            </div>

            <!-- Main Content -->
            <div class="col-lg-9">
                @yield('client-content')
            </div>
        </div>
    </div>
</section>
@endsection
