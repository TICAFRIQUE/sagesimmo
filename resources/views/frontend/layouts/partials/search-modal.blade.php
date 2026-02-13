<!-- Modal de recherche bien -->
@php
    use App\Models\TypeBien;
    use App\Models\Annonce;

    // Définitions par défaut si non fournies
    $typesBiens = $typesBiens ?? TypeBien::all();
    $villes = $villes ?? config('ville-commune');

@endphp
<div class="modal fade" id="searchBienModal" tabindex="-1" aria-labelledby="searchBienModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="searchBienModalLabel">
                    <i class="ri-search-line me-1"></i> Rechercher un bien
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('search') }}" method="GET">
                    <div class="row g-3">
                        <div class="col-12">
                            <ul class="nav nav-tabs search-tabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" data-bs-toggle="tab"
                                        data-bs-target="#modal-location" type="button">Location</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#modal-vente"
                                        type="button">Vente</button>
                                </li>
                            </ul>
                        </div>

                        <div class="tab-content mt-3 w-100">
                            <div class="tab-pane fade show active" id="modal-location">
                                <input type="hidden" name="type_annonce" value="location">
                                <div class="row g-2">
                                    <div class="col-12">
                                        <select class="form-select modal-ville" name="ville">
                                            <option value="">Sélectionner une ville</option>
                                            @foreach (array_keys($villes) as $ville)
                                                <option value="{{ $ville }}">{{ $ville }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <select class="form-select modal-commune" name="commune" disabled>
                                            <option value="">Sélectionner une commune...</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <select class="form-select" name="type_bien_id">
                                            <option value="">Type de bien</option>
                                            @foreach ($typesBiens as $type)
                                                <option value="{{ $type->id }}">{{ $type->nom }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <input type="number" class="form-control" name="prix_min"
                                            placeholder="Prix min (FCFA)">
                                    </div>
                                    <div class="col-6">
                                        <input type="number" class="form-control" name="prix_max"
                                            placeholder="Prix max (FCFA)">
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="modal-vente">
                                <input type="hidden" name="type_annonce" value="vente">
                                <div class="row g-2">
                                    <div class="col-12">
                                        <select class="form-select modal-ville" name="ville">
                                            <option value="">Sélectionner une ville</option>
                                            @foreach (array_keys($villes) as $ville)
                                                <option value="{{ $ville }}">{{ $ville }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <select class="form-select modal-commune" name="commune" disabled>
                                            <option value="">Sélectionner une commune...</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <select class="form-select" name="type_bien_id">
                                            <option value="">Type de bien</option>
                                            @foreach ($typesBiens as $type)
                                                <option value="{{ $type->id }}">{{ $type->nom }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <input type="number" class="form-control" name="prix_min"
                                            placeholder="Prix min (FCFA)">
                                    </div>
                                    <div class="col-6">
                                        <input type="number" class="form-control" name="prix_max"
                                            placeholder="Prix max (FCFA)">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <script>
                        const modalVilles = @json($villes);

                        function populateCommunes(villeSelect) {
                            const ville = villeSelect.value;
                            const tabPane = villeSelect.closest('.tab-pane');
                            const communeSelect = tabPane.querySelector('.modal-commune');

                            // Vider les options
                            communeSelect.innerHTML = '<option value="">Sélectionner une commune...</option>';

                            if (!ville) {
                                communeSelect.disabled = true;
                                refreshSelect2(communeSelect);
                                return;
                            }

                            const communes = modalVilles[ville] || [];
                            if (communes.length > 0) {
                                communes.forEach(function(c) {
                                    const opt = document.createElement('option');
                                    opt.value = c;
                                    opt.textContent = c;
                                    communeSelect.appendChild(opt);
                                });
                                communeSelect.disabled = false;
                            } else {
                                communeSelect.disabled = true;
                            }

                            refreshSelect2(communeSelect);
                        }

                        function refreshSelect2(el) {
                            if (window.jQuery) {
                                const $el = $(el);
                                if ($el.hasClass('select2-hidden-accessible')) {
                                    $el.trigger('change.select2');
                                }
                            }
                        }

                        function attachVilleListeners() {
                            document.querySelectorAll('#searchBienModal .modal-ville').forEach(function(select) {
                                // Retirer l'ancien listener vanilla pour éviter les doublons
                                select.removeEventListener('change', select._villeHandler);
                                select._villeHandler = function() {
                                    populateCommunes(this);
                                };
                                select.addEventListener('change', select._villeHandler);

                                // Listener Select2 (jQuery)
                                if (window.jQuery) {
                                    $(select).off('change.ville').on('change.ville', function() {
                                        populateCommunes(this);
                                    });
                                }
                            });
                        }

                        document.addEventListener('DOMContentLoaded', function() {
                            attachVilleListeners();

                            if (window.jQuery) {
                                $('#searchBienModal').on('shown.bs.modal', function() {
                                    const $modal = $(this);

                                    // Réinitialiser Select2 sur tous les selects
                                    $modal.find('select').not('.no-select2').each(function() {
                                        const $s = $(this);
                                        try {
                                            $s.select2('destroy');
                                        } catch (e) {}
                                        $s.select2({
                                            theme: 'bootstrap-5',
                                            width: '100%',
                                            dropdownParent: $modal
                                        });
                                    });

                                    // Ré-attacher les listeners APRÈS la réinit Select2
                                    attachVilleListeners();

                                    // S'assurer que les communes sont bien désactivées au départ
                                    $modal.find('.modal-commune').each(function() {
                                        if (!this.options.length || this.options.length <= 1) {
                                            $(this).prop('disabled', true).trigger('change.select2');
                                        }
                                    });
                                });

                                $('#searchBienModal').on('hidden.bs.modal', function() {
                                    $(this).find('select').not('.no-select2').each(function() {
                                        try {
                                            $(this).select2('destroy');
                                        } catch (e) {}
                                    });
                                });
                            }
                        });
                    </script>

                    <div class="col-12 mt-3">
                        <button type="submit" class="btn btn-primary w-100">Rechercher</button>
                    </div>
            </div>
            </form>
        </div>
    </div>
</div>
</div>
