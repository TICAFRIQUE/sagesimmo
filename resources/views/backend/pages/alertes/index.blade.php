@extends('backend.layouts.master')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <!-- Page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0"><i class="ri-alarm-warning-line me-2"></i>Alertes & Retards</h4>
                        <div class="page-title-right">
                            <form action="{{ route('backend.alertes.mettre-a-jour') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-primary">
                                    <i class="ri-refresh-line me-1"></i>Actualiser les statuts
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cartes de statistiques -->
            <div class="row">
                <div class="col-xl-3 col-md-6">
                    <div class="card card-animate bg-danger text-white">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <p class="text-uppercase fw-medium text-white-50 mb-0">Impayées (>30j)</p>
                                </div>
                                <div class="flex-shrink-0">
                                    <i class="ri-alert-line display-6"></i>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between mt-3">
                                <div>
                                    <h4 class="fs-22 fw-semibold mb-1 text-white">{{ $stats['nb_impayees'] }}</h4>
                                    <p class="text-white-75 mb-0">{{ number_format($stats['total_impaye'], 0, ',', ' ') }} FCFA</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card card-animate bg-warning">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <p class="text-uppercase fw-medium text-muted mb-0">En retard</p>
                                </div>
                                <div class="flex-shrink-0">
                                    <i class="ri-alarm-warning-line display-6 text-dark"></i>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between mt-3">
                                <div>
                                    <h4 class="fs-22 fw-semibold mb-1">{{ $stats['nb_retard'] }}</h4>
                                    <p class="text-muted mb-0">{{ number_format($stats['total_retard'], 0, ',', ' ') }} FCFA</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card card-animate bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <p class="text-uppercase fw-medium text-white-50 mb-0">À venir (7j)</p>
                                </div>
                                <div class="flex-shrink-0">
                                    <i class="ri-calendar-event-line display-6"></i>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between mt-3">
                                <div>
                                    <h4 class="fs-22 fw-semibold mb-1 text-white">{{ $stats['nb_a_venir'] }}</h4>
                                    <p class="text-white-75 mb-0">Échéances prochaines</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card card-animate bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <p class="text-uppercase fw-medium text-white-50 mb-0">Total en attente</p>
                                </div>
                                <div class="flex-shrink-0">
                                    <i class="ri-money-dollar-circle-line display-6"></i>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between mt-3">
                                <div>
                                    <h4 class="fs-22 fw-semibold mb-1 text-white">{{ number_format($stats['total_impaye'] + $stats['total_retard'], 0, ',', ' ') }}</h4>
                                    <p class="text-white-75 mb-0">FCFA</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Échéances impayées -->
            @if($impayees->count() > 0)
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-danger text-white">
                            <h5 class="card-title mb-0 text-white">
                                <i class="ri-alert-line me-2"></i>Échéances impayées (plus de 30 jours de retard)
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Date échéance</th>
                                            <th>Locataire</th>
                                            <th>Bien</th>
                                            <th>Montant dû</th>
                                            <th>Payé</th>
                                            <th>Restant</th>
                                            <th>Retard</th>
                                            <th>Statut</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($impayees as $echeance)
                                        <tr class="table-danger">
                                            <td>
                                                <strong>{{ $echeance->date_echeance->format('d/m/Y') }}</strong>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>{{ $echeance->location->locataire->name }}</strong><br>
                                                    <small class="text-muted">{{ $echeance->location->locataire->telephone }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="{{ route('backend.annonces.show', $echeance->location->annonce) }}" class="text-primary">
                                                    {{ Str::limit($echeance->location->annonce->titre, 30) }}
                                                </a>
                                            </td>
                                            <td><strong>{{ number_format($echeance->montant_du, 0, ',', ' ') }} FCFA</strong></td>
                                            <td>{{ number_format($echeance->montant_paye, 0, ',', ' ') }} FCFA</td>
                                            <td class="text-danger"><strong>{{ number_format($echeance->montantRestant(), 0, ',', ' ') }} FCFA</strong></td>
                                            <td>
                                                <span class="badge bg-danger">{{ $echeance->joursDeRetard() }} jours</span>
                                            </td>
                                            <td>{!! $echeance->statut_badge !!}</td>
                                            <td>
                                                <a href="{{ route('backend.locations.show', $echeance->location) }}" 
                                                   class="btn btn-sm btn-primary">
                                                    <i class="ri-eye-line"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Échéances en retard -->
            @if($enRetard->count() > 0)
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-warning">
                            <h5 class="card-title mb-0">
                                <i class="ri-alarm-warning-line me-2"></i>Échéances en retard (moins de 30 jours)
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Date échéance</th>
                                            <th>Locataire</th>
                                            <th>Bien</th>
                                            <th>Montant dû</th>
                                            <th>Payé</th>
                                            <th>Restant</th>
                                            <th>Retard</th>
                                            <th>Statut</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($enRetard as $echeance)
                                        <tr class="table-warning">
                                            <td>{{ $echeance->date_echeance->format('d/m/Y') }}</td>
                                            <td>
                                                <div>
                                                    <strong>{{ $echeance->location->locataire->name }}</strong><br>
                                                    <small class="text-muted">{{ $echeance->location->locataire->telephone }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="{{ route('backend.annonces.show', $echeance->location->annonce) }}" class="text-primary">
                                                    {{ Str::limit($echeance->location->annonce->titre, 30) }}
                                                </a>
                                            </td>
                                            <td>{{ number_format($echeance->montant_du, 0, ',', ' ') }} FCFA</td>
                                            <td>{{ number_format($echeance->montant_paye, 0, ',', ' ') }} FCFA</td>
                                            <td class="text-warning"><strong>{{ number_format($echeance->montantRestant(), 0, ',', ' ') }} FCFA</strong></td>
                                            <td>
                                                <span class="badge bg-warning">{{ $echeance->joursDeRetard() }} jours</span>
                                            </td>
                                            <td>{!! $echeance->statut_badge !!}</td>
                                            <td>
                                                <a href="{{ route('backend.locations.show', $echeance->location) }}" 
                                                   class="btn btn-sm btn-primary">
                                                    <i class="ri-eye-line"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Échéances à venir -->
            @if($aVenir->count() > 0)
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h5 class="card-title mb-0 text-white">
                                <i class="ri-calendar-event-line me-2"></i>Échéances à venir (7 prochains jours)
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Date échéance</th>
                                            <th>Dans</th>
                                            <th>Locataire</th>
                                            <th>Bien</th>
                                            <th>Montant</th>
                                            <th>Statut</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($aVenir as $echeance)
                                        <tr>
                                            <td>{{ $echeance->date_echeance->format('d/m/Y') }}</td>
                                            <td>
                                                <span class="badge bg-info">
                                                    {{ $echeance->date_echeance->diffForHumans() }}
                                                </span>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>{{ $echeance->location->locataire->name }}</strong><br>
                                                    <small class="text-muted">{{ $echeance->location->locataire->telephone }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="{{ route('backend.annonces.show', $echeance->location->annonce) }}" class="text-primary">
                                                    {{ Str::limit($echeance->location->annonce->titre, 30) }}
                                                </a>
                                            </td>
                                            <td><strong>{{ number_format($echeance->montant_du, 0, ',', ' ') }} FCFA</strong></td>
                                            <td>{!! $echeance->statut_badge !!}</td>
                                            <td>
                                                <a href="{{ route('backend.locations.show', $echeance->location) }}" 
                                                   class="btn btn-sm btn-primary">
                                                    <i class="ri-eye-line"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @if($impayees->count() == 0 && $enRetard->count() == 0 && $aVenir->count() == 0)
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="ri-checkbox-circle-line display-1 text-success"></i>
                            <h4 class="mt-3">Aucune alerte pour le moment</h4>
                            <p class="text-muted">Toutes les échéances sont à jour !</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
@endsection
