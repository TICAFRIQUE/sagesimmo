@extends('backend.layouts.master')

@section('title')
   Gestion des Versements
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-money-check-alt"></i> Versements aux Propriétaires
            </h1>
        </div>
        {{-- <div class="col-md-4 text-end">
            <a href="{{ route('backend.versements.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Ajouter un versement
            </a>
        </div> --}}
    </div>

    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('backend.versements.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="proprietaire_id" class="form-label">Propriétaire</label>
                    <select name="proprietaire_id" id="proprietaire_id" class="form-select">
                        <option value="">-- Tous --</option>
                        @foreach($proprietaires as $prop)
                            <option value="{{ $prop->id }}" @selected(request('proprietaire_id') == $prop->id)>
                                {{ $prop->username }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="statut" class="form-label">Statut</label>
                    <select name="statut" id="statut" class="form-select">
                        <option value="">-- Tous --</option>
                        <option value="en_attente" @selected(request('statut') == 'en_attente')>En attente</option>
                        <option value="effectue" @selected(request('statut') == 'effectue')>Effectué</option>
                        <option value="annule" @selected(request('statut') == 'annule')>Annulé</option>
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label for="date_debut" class="form-label">Date début</label>
                    <input type="date" name="date_debut" id="date_debut" class="form-control"
                        value="{{ request('date_debut') }}">
                </div>
                
                <div class="col-md-2">
                    <label for="date_fin" class="form-label">Date fin</label>
                    <input type="date" name="date_fin" id="date_fin" class="form-control"
                        value="{{ request('date_fin') }}">
                </div>
                
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 mt-4">
                        <i class="fas fa-search"></i> Filtrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des versements -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-list"></i> Versements enregistrés ({{ $versements->total() }} total)
            </h5>
        </div>
        <div class="card-body">
            @if($versements->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr class="table-light">
                                <th>Propriétaire</th>
                                <th>Date</th>
                                <th>Période</th>
                                <th class="text-end">Montant</th>
                                <th>Mode</th>
                                <th>Référence</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($versements as $versement)
                            <tr>
                                <td>
                                    <strong>{{ $versement->proprietaire->username }}</strong><br>
                                    <small class="text-muted">{{ $versement->proprietaire->email }}</small>
                                </td>
                                <td>{{ $versement->date_versement->format('d/m/Y') }}</td>
                                <td>
                                    @if($versement->date_debut && $versement->date_fin)
                                        <small>{{ $versement->date_debut->format('d/m/Y') }} - {{ $versement->date_fin->format('d/m/Y') }}</small>
                                    @else
                                        <small class="text-muted">N/A</small>
                                    @endif
                                </td>
                                <td class="text-end font-weight-bold">
                                    {{ number_format($versement->montant, 0, ',', ' ') }} F
                                </td>
                                <td>
                                    <small class="badge bg-light text-dark">{{ $versement->mode_versement }}</small>
                                </td>
                                <td>
                                    <small>{{ $versement->reference ?? '-' }}</small>
                                </td>
                                <td>
                                    @if($versement->statut === 'effectue')
                                        <span class="badge bg-success">Effectué</span>
                                    @elseif($versement->statut === 'en_attente')
                                        <span class="badge bg-warning">En attente</span>
                                    @else
                                        <span class="badge bg-danger">Annulé</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('backend.versements.edit', $versement) }}" class="btn btn-outline-primary" title="Éditer">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if($versement->statut !== 'annule')
                                        <form action="{{ route('backend.versements.cancel', $versement) }}" method="POST" style="display: inline;" onsubmit="return confirm('Annuler ce versement ?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-outline-warning" title="Annuler">
                                                <i class="fas fa-times-circle"></i>
                                            </button>
                                        </form>
                                        @endif
                                        <form action="{{ route('backend.versements.destroy', $versement) }}" method="POST" style="display: inline;" onsubmit="return confirm('Supprimer ce versement ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm" title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $versements->links() }}
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Aucun versement enregistré.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
