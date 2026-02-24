<div class="modal fade" id="commercialModal{{ $user->id }}" tabindex="-1" aria-labelledby="commercialModalLabel{{ $user->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="commercialModalLabel{{ $user->id }}">Commercial en charge de {{ $user->username }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    @if ($user->commercial)
                                                        <div class="d-flex align-items-center">
                                                            @if ($user->commercial->hasMedia('avatar'))
                                                                <img src="{{ $user->commercial->getFirstMediaUrl('avatar') }}" alt="{{ $user->commercial->username }}" class="user-avatar me-3">
                                                            @else
                                                                <div class="avatar-sm rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                                                    <span class="fw-bold">{{ substr($user->commercial->username, 0, 1) }}</span>
                                                                </div>
                                                            @endif
                                                            <div>
                                                                <h6 class="mb-0">{{ $user->commercial?->username }}</h6>
                                                                <small class="text-muted">{{ $user->commercial?->email }}</small>
                                                                <small class="text-muted">{{ $user->commercial?->phone }}</small>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <p class="text-muted">Aucun commercial assigné.</p>
                                                    @endif
                                                </div>
                                                <div class="modal-footer"></div>
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>