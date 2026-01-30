<div class="card property-card">
    <div class="property-image">
        @if($property->hasMedia('images') && $property->getMedia('images')->count() > 0)
            @php
                $images = $property->getMedia('images');
                $carouselId = 'carousel-' . $property->slug;
            @endphp
            
            <div id="{{ $carouselId }}" class="carousel slide property-carousel" data-bs-ride="false">
                <div class="carousel-inner">
                    @foreach($images as $index => $image)
                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                            <img src="{{ $image->getUrl() }}" class="d-block w-100" alt="{{ $property->titre }}">
                        </div>
                    @endforeach
                </div>
                
                @if($images->count() > 1)
                    <!-- Contrôles Previous/Next -->
                    <button class="carousel-control-prev" type="button" data-bs-target="#{{ $carouselId }}" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Précédent</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#{{ $carouselId }}" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Suivant</span>
                    </button>
                    
                    <!-- Indicateurs (points) -->
                    <div class="carousel-indicators">
                        @foreach($images as $index => $image)
                            <button type="button" data-bs-target="#{{ $carouselId }}" 
                                    data-bs-slide-to="{{ $index }}" 
                                    class="{{ $index === 0 ? 'active' : '' }}" 
                                    aria-current="{{ $index === 0 ? 'true' : 'false' }}" 
                                    aria-label="Image {{ $index + 1 }}">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>
        @else
            <img src="https://via.placeholder.com/400x300?text=Aucune+image" alt="Aucune image">
        @endif
        
        <span class="property-badge {{ $property->type_transaction == 'vente' ? 'badge-vente' : '' }}">
            {{ ucfirst($property->type_transaction) }}
        </span>
        
        <div class="property-price">
            {{ number_format($property->prix, 0, ',', ' ') }} FCFA
            @if($property->type_transaction == 'location')
                <small>/mois</small>
            @endif
        </div>
    </div>
    
    <div class="property-info">
        <h3 class="property-title">
            <a href="{{ route('properties.show', $property->slug) }}" class="text-decoration-none text-dark">
                {{ Str::limit($property->titre, 50) }}
            </a>
        </h3>
        
        @if($property->typeBien)
            <div class="property-type mb-2">
                <i class="ri-building-line"></i>
                <span>{{ $property->typeBien->nom }}</span>
            </div>
        @endif
        
        <div class="property-location">
            <i class="ri-map-pin-line"></i>
            <span>{{ $property->ville }}, {{ $property->quartier }}</span>
        </div>
        
        <div class="property-features">
            @if($property->nombre_chambres)
                <div class="feature-item">
                    <i class="ri-hotel-bed-line"></i>
                    <span>{{ $property->nombre_chambres }} Ch.</span>
                </div>
            @endif
            
            @if($property->nombre_salles_bain)
                <div class="feature-item">
                    <i class="ri-drop-line"></i>
                    <span>{{ $property->nombre_salles_bain }} SDB</span>
                </div>
            @endif
            
            @if($property->surface)
                <div class="feature-item">
                    <i class="ri-ruler-line"></i>
                    <span>{{ $property->surface }} m²</span>
                </div>
            @endif
        </div>
        
        <div class="mt-3">
            <a href="{{ route('properties.show', $property->slug) }}" class="btn btn-primary w-100">
                <i class="ri-eye-line"></i> Voir détails
            </a>
        </div>
    </div>
</div>
