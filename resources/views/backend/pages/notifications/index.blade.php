@extends('backend.layouts.master')

@section('title')
    Notifications
@endsection

@section('content')
    @component('backend.components.breadcrumb')
        @slot('li_1')
            Dashboard
        @endslot
        @slot('title')
            Notifications
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="ri-notification-3-line me-1"></i>Mes Notifications
                    </h5>
                    @if($notifications->where('read_at', null)->count() > 0)
                        <form action="{{ route('backend.notifications.read-all') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-primary">
                                <i class="ri-check-double-line me-1"></i>Tout marquer comme lu
                            </button>
                        </form>
                    @endif
                </div>
                <div class="card-body">
                    @if($notifications->count() > 0)
                        <div class="list-group">
                            @foreach($notifications as $notification)
                                <div class="list-group-item {{ $notification->read_at ? '' : 'list-group-item-action active' }}">
                                    <div class="d-flex w-100 justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="avatar-xs me-3">
                                                    <span class="avatar-title rounded-circle {{ $notification->data['type'] == 'demande_vente' ? 'bg-success' : 'bg-info' }} text-white">
                                                        <i class="bx {{ $notification->data['type'] == 'demande_vente' ? 'bx-shopping-bag' : 'bx-home' }} fs-18"></i>
                                                    </span>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1">
                                                        {{ $notification->data['message'] ?? 'Nouvelle notification' }}
                                                        @if(!$notification->read_at)
                                                            <span class="badge bg-danger ms-2">Nouveau</span>
                                                        @endif
                                                    </h6>
                                                    <p class="text-muted mb-0 fs-12">
                                                        <i class="ri-time-line me-1"></i>{{ $notification->created_at->diffForHumans() }}
                                                    </p>
                                                </div>
                                            </div>
                                            
                                            @if($notification->data['type'] == 'demande_vente')
                                                <div class="ms-5 ps-2">
                                                    <p class="mb-1"><strong>Client :</strong> {{ $notification->data['client_nom'] ?? 'N/A' }}</p>
                                                    <p class="mb-1"><strong>Bien :</strong> {{ $notification->data['bien_titre'] ?? 'N/A' }}</p>
                                                    <p class="mb-1"><strong>Prix :</strong> {{ number_format($notification->data['prix'] ?? 0, 0, ',', ' ') }} FCFA</p>
                                                </div>
                                            @else
                                                <div class="ms-5 ps-2">
                                                    <p class="mb-1"><strong>Locataire :</strong> {{ $notification->data['locataire_nom'] ?? 'N/A' }}</p>
                                                    <p class="mb-1"><strong>Bien :</strong> {{ $notification->data['bien_titre'] ?? 'N/A' }}</p>
                                                    <p class="mb-1"><strong>Loyer :</strong> {{ number_format($notification->data['loyer'] ?? 0, 0, ',', ' ') }} FCFA</p>
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <div class="ms-3">
                                            @if(isset($notification->data['url']))
                                                <a href="{{ route('backend.notifications.read', $notification->id) }}" class="btn btn-sm btn-primary">
                                                    <i class="ri-eye-line me-1"></i>Voir
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-3">
                            {{ $notifications->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <img src="{{ URL::asset('build/images/svg/bell.svg') }}" class="img-fluid" alt="Aucune notification" style="max-width: 200px;">
                            <h5 class="mt-3">Aucune notification</h5>
                            <p class="text-muted">Vous n'avez reçu aucune notification pour le moment.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
