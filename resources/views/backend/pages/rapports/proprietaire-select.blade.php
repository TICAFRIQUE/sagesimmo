@extends('backend.layouts.master')

@section('title')
   Sélectionner un Propriétaire
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-file-invoice-dollar"></i> Sélectionner un Propriétaire
            </h1>
        </div>
    </div>

    <!-- Liste des propriétaires -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                Propriétaires disponibles ({{ $proprietaires->count() }} au total)
            </h5>
        </div>
        <div class="card-body">
            @if($proprietaires->count() > 0)
                <div class="row">
                    @foreach($proprietaires as $proprietaire)
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    @if($proprietaire->getFirstMediaUrl('avatar'))
                                        <img src="{{ $proprietaire->getFirstMediaUrl('avatar') }}" 
                                            alt="{{ $proprietaire->username }}" 
                                            class="rounded-circle me-3" width="50" height="50">
                                    @else
                                        <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-3" 
                                            style="width: 50px; height: 50px;">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <h5 class="card-title mb-0">{{ $proprietaire->username }}</h5>
                                        <small class="text-muted">{{ $proprietaire->email }}</small>
                                    </div>
                                </div>

                                @php
                                    $nombreBiens = $proprietaire->annonces()->count();
                                @endphp

                                <div class="mb-3">
                                    <small class="text-muted">
                                        <i class="fas fa-home me-1"></i>
                                        {{ $nombreBiens }} bien{{ $nombreBiens > 1 ? 's' : '' }} enregistré{{ $nombreBiens > 1 ? 's' : '' }}
                                    </small>
                                </div>

                                <a href="{{ route('rapports.proprietaire', ['proprietaire_id' => $proprietaire->id]) }}" 
                                    class="btn btn-primary btn-sm w-100">
                                    <i class="fas fa-chart-line me-1"></i> Voir le Rapport
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Aucun propriétaire enregistré.
                </div>
            @endif
        </div>
    </div>

    <!-- Aide -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card bg-light">
                <div class="card-body">
                    <h6 class="card-title">
                        <i class="fas fa-lightbulb me-2"></i>Utilisation
                    </h6>
                    <p class="mb-0">
                        Cliquez sur un propriétaire pour consulter son rapport financier complet incluant:
                        <ul class="mt-2 mb-0">
                            <li>Loyers encaissés par bien</li>
                            <li>Ventes encaissées</li>
                            <li>Charges (maintenance, réparation, taxes)</li>
                            <li>Commissions agence</li>
                            <li>Revenu net du propriétaire</li>
                        </ul>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
