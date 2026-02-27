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
                    <i class="fas fa-file-invoice-dollar"></i> Rapport financier par propriétaire @if (isset($dateDebut) && isset($dateFin))
                        du <small class="text-primary">{{ $dateDebut->format('d/m/Y') }} au
                            {{ $dateFin->format('d/m/Y') }}</small>
                    @endif
                </h1>

                {{-- <h5 class="mb-0">
                    Propriétaires disponibles ({{ $proprietaires->count() }} au total)
                    @if (isset($dateDebut) && isset($dateFin))
                        - Période: <small>{{ $dateDebut->format('d/m/Y') }} au {{ $dateFin->format('d/m/Y') }}</small>
                    @endif
                </h5> --}}
            </div>
        </div>



        <!-- KPI Global -->
        @if (isset($kpiGlobal))
            <div class="row mb-2">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body py-3 px-3">
                            <small class="text-muted d-block mb-1" style="font-size: 11px;">Versements Disponibles</small>
                            <h5 class="mb-0" style="color: #ffc107; font-size: 14px;">
                                {{ number_format($kpiGlobal['versements_disponibles'], 0, ',', ' ') }} F</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body py-3 px-3">
                            <small class="text-muted d-block mb-1" style="font-size: 11px;">Versements Partiels</small>
                            <h5 class="mb-0" style="color: #17a2b8; font-size: 14px;">
                                {{ number_format($kpiGlobal['versements_partiels'], 0, ',', ' ') }} F</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body py-3 px-3">
                            <small class="text-muted d-block mb-1" style="font-size: 11px;">Versements Effectués</small>
                            <h5 class="mb-0" style="color: #28a745; font-size: 14px;">
                                {{ number_format($kpiGlobal['versements_effectues'], 0, ',', ' ') }} F</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body py-3 px-3">
                            <small class="text-muted d-block mb-1" style="font-size: 11px;">Commission Perçue</small>
                            <h5 class="mb-0" style="color: #0d6efd; font-size: 14px;">
                                {{ number_format($kpiGlobal['total_commission'], 0, ',', ' ') }} F</h5>
                        </div>
                    </div>
                </div>
            </div>
        @endif



        <!-- Filtres -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('backend.rapports.proprietaire') }}" class="row g-3">
                    <div class="col-md-5">
                        <label for="proprietaire_filtre" class="form-label">Propriétaire</label>
                        <select name="proprietaire_filtre" id="proprietaire_filtre" class="form-select">
                            <option value="">-- Tous les propriétaires --</option>
                            @foreach ($allProprietaires as $prop)
                                <option value="{{ $prop->id }}" @selected(request('proprietaire_filtre') == $prop->id)>
                                    {{ $prop->username }} ({{ $prop->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="statut_filtre" class="form-label">Statut Versement</label>
                        <select name="statut_filtre" id="statut_filtre" class="form-select">
                            <option value="">-- Tous --</option>
                            <option value="en_attente" @selected(request('statut_filtre') == 'en_attente')>Versement disponible</option>
                            <option value="partiel" @selected(request('statut_filtre') == 'partiel')>Partiel</option>
                            <option value="effectue" @selected(request('statut_filtre') == 'effectue')>Effectué</option>
                            <option value="aucun" @selected(request('statut_filtre') == 'aucun')>Aucun disponible</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="date_debut" class="form-label">Date début</label>
                        <input type="date" name="date_debut" id="date_debut" class="form-control"
                            value="{{ isset($dateDebut) ? $dateDebut->format('Y-m-d') : now()->startOfMonth()->format('Y-m-d') }}">
                    </div>

                    <div class="col-md-2">
                        <label for="date_fin" class="form-label">Date fin</label>
                        <input type="date" name="date_fin" id="date_fin" class="form-control"
                            value="{{ isset($dateFin) ? $dateFin->format('Y-m-d') : now()->endOfMonth()->format('Y-m-d') }}">
                    </div>

                    <div class="col-12 text-center">
                        <button type="submit" class="btn btn-primary w-50">
                            <i class="fas fa-search"></i> Filtrer
                        </button>
                        <a href="{{ route('backend.rapports.proprietaire') }}" class="btn btn-secondary">
                            <i class="fas fa-refresh"></i> Réinitialiser
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Liste des propriétaires -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    Propriétaires disponibles ({{ $proprietaires->count() }} au total)
                    {{-- @if (isset($dateDebut) && isset($dateFin))
                        - Période: <small>{{ $dateDebut->format('d/m/Y') }} au {{ $dateFin->format('d/m/Y') }}</small>
                    @endif --}}
                </h5>
            </div>
            <div class="card-body">
                @if ($proprietaires->count() > 0)
                    <div class="row">
                        @php
                            // Grouper et trier par statut
                            $statusOrder = ['en_attente' => 1, 'partiel' => 2, 'effectue' => 3, 'secondary' => 4];
                            $proprsByStatus = [];

                            foreach ($proprietaires as $index => $proprietaire) {
                                $rapport = $aperçus[$index] ?? null;
                                $badge = $rapport['statut_versement']['badge'] ?? 'secondary';
                                $order = $statusOrder[$badge] ?? 4;

                                if (!isset($proprsByStatus[$order])) {
                                    $proprsByStatus[$order] = [];
                                }
                                $proprsByStatus[$order][] = [
                                    'proprietaire' => $proprietaire,
                                    'rapport' => $rapport,
                                    'index' => $index,
                                ];
                            }

                            ksort($proprsByStatus);
                        @endphp

                        @foreach ($proprsByStatus as $group)
                            @foreach ($group as $item)
                                @php
                                    $proprietaire = $item['proprietaire'];
                                    $rapport = $item['rapport'];
                                    $index = $item['index'];
                                    $nombreBiens = $proprietaire->annonces()->count();
                                @endphp
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-body">
                                            <!-- Header propriétaire -->
                                            <div class="d-flex align-items-center mb-2">
                                                @if ($proprietaire->hasMedia('avatar'))
                                                    <img src="{{ $proprietaire->getFirstMediaUrl('avatar') }}"
                                                        alt="{{ $proprietaire->username }}" class="rounded-circle me-2"
                                                        width="40" height="40">
                                                @else
                                                    <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-2"
                                                        style="width: 40px; height: 40px; font-size: 0.8rem;">
                                                        <i class="fas fa-user"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <h6 class="card-title mb-0" style="font-size: 14px;">
                                                        {{ $proprietaire->username }}</h6>
                                                    <small class="text-muted"
                                                        style="font-size: 11px;">{{ $proprietaire->email }}</small>
                                                </div>
                                            </div>

                                            <!--statut et nombre de biens-->
                                            <div class="d-flex justify-content-between align-items-center">
                                                <!-- Statut badge -->
                                                @if ($rapport)
                                                    <div class="mb-2">
                                                        <span class="badge bg-{{ $rapport['statut_versement']['badge'] }}"
                                                            style="font-size: 11px;">
                                                            {{ $rapport['statut_versement']['label'] }}
                                                        </span>
                                                    </div>
                                                @endif

                                                <!--afficher le nombre de biens-->
                                                <div class="mb-3"><i
                                                        class="fas fa-building text-primary me-1"></i>{{ $rapport['nombre_biens'] ?? 0 }}
                                                </div>
                                            </div>



                                            <!-- Aperçu financier simple -->
                                            @if ($rapport)
                                                <div class="mb-3 p-2 bg-light rounded" style="font-size: 12px;">
                                                    <div class="row g-2">
                                                        <div class="col-6">
                                                            <strong
                                                                class="text-success d-block">{{ number_format($rapport['revenue_net'], 0, ',', ' ') }}
                                                                F</strong>
                                                            <small class="text-muted">À Verser</small>
                                                        </div>
                                                        <div class="col-6">
                                                            <strong
                                                                class="text-info d-block">{{ number_format($rapport['montant_total_verse'], 0, ',', ' ') }}
                                                                F</strong>
                                                            <small class="text-muted">Versé</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif



                                            <a href="{{ route('backend.rapports.proprietaire', ['proprietaire_id' => $proprietaire->id]) }}"
                                                class="btn btn-primary btn-sm w-100" style="font-size: 12px;">
                                                <i class="fas fa-chart-line me-1"></i> Détails
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
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
