@extends('backend.layouts.master')

@section('title')
   Gestion des Charges
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-tools"></i> Gestion des Charges
            </h1>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('charges.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Ajouter une charge
            </a>
        </div>
    </div>

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Erreurs :</strong>
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('charges.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="annonce_id" class="form-label">Bien</label>
                    <select name="annonce_id" id="annonce_id" class="form-select">
                        <option value="">-- Tous les biens --</option>
                        @foreach($biens as $bien)
                            <option value="{{ $bien->id }}" @selected(request('annonce_id') == $bien->id)>
                                {{ $bien->titre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="type_charge" class="form-label">Type</label>
                    <select name="type_charge" id="type_charge" class="form-select">
                        <option value="">-- Tous --</option>
                        <option value="maintenance" @selected(request('type_charge') === 'maintenance')>Maintenance</option>
                        <option value="reparation" @selected(request('type_charge') === 'reparation')>Réparation</option>
                        <option value="taxe" @selected(request('type_charge') === 'taxe')>Taxe</option>
                        <option value="autre" @selected(request('type_charge') === 'autre')>Autre</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="date_debut" class="form-label">Date début</label>
                    <input type="date" name="date_debut" id="date_debut" class="form-control"
                        value="{{ request('date_debut') }}">
                </div>

                <div class="col-md-3">
                    <label for="date_fin" class="form-label">Date fin</label>
                    <input type="date" name="date_fin" id="date_fin" class="form-control"
                        value="{{ request('date_fin') }}">
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Filtrer
                    </button>
                    <a href="{{ route('charges.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Réinitialiser
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tableau des charges -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                Liste des charges ({{ $charges->total() }} au total)
            </h5>
        </div>
        <div class="card-body">
            @if($charges->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr class="table-light">
                            <th>Date</th>
                            <th>Bien</th>
                            <th>Type</th>
                            <th>Description</th>
                            <th class="text-end">Montant</th>
                            <th class="text-center">Référence</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($charges as $charge)
                        <tr>
                            <td>
                                <strong>{{ $charge->date_charge->format('d/m/Y') }}</strong>
                            </td>
                            <td>
                                <div>
                                    <strong>{{ $charge->annonce->titre }}</strong><br>
                                    <small class="text-muted">{{ $charge->annonce->adresse }}</small>
                                </div>
                            </td>
                            <td>
                                @switch($charge->type_charge)
                                    @case('maintenance')
                                        <span class="badge bg-info">
                                            <i class="fas fa-wrench"></i> Maintenance
                                        </span>
                                        @break
                                    @case('reparation')
                                        <span class="badge bg-warning">
                                            <i class="fas fa-hammer"></i> Réparation
                                        </span>
                                        @break
                                    @case('taxe')
                                        <span class="badge bg-danger">
                                            <i class="fas fa-percent"></i> Taxe
                                        </span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary">
                                            <i class="fas fa-ellipsis-h"></i> Autre
                                        </span>
                                @endswitch
                            </td>
                            <td>
                                {{ $charge->description ?? '-' }}
                                @if($charge->reference)
                                    <br>
                                    <small class="text-muted">Réf: {{ $charge->reference }}</small>
                                @endif
                            </td>
                            <td class="text-end font-weight-bold">
                                {{ number_format($charge->montant, 0, ',', ' ') }} F
                            </td>
                            <td class="text-center">
                                @if($charge->reference)
                                    <span class="badge bg-light text-dark">{{ $charge->reference }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('charges.edit', $charge) }}" class="btn btn-outline-primary"
                                        title="Éditer">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('charges.destroy', $charge) }}" method="POST"
                                        style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger"
                                            onclick="return confirm('Confirmer la suppression ?')"
                                            title="Supprimer">
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
            <nav aria-label="Page navigation">
                {{ $charges->links() }}
            </nav>
            @else
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Aucune charge trouvée pour les critères sélectionnés.
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
