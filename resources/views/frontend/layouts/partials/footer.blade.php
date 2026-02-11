<footer class="site-footer">
    <div class="container">
        <div class="row g-4">
            <!-- À propos -->
            <div class="col-lg-4 col-md-6">
                <div class="footer-brand">
                    <img src="{{ $data_parametre ? URL::asset($data_parametre?->getFirstMediaUrl('logo_footer')) : URL::asset('images/camera-icon.png') }}"
                        alt="Sage Immo Logo" width="auto" height="70">
                </div>
                <p class="mb-4">
                    Votre partenaire de confiance pour trouver la propriété de vos rêves.
                    Nous vous accompagnons dans tous vos projets immobiliers.
                </p>
                <div class="d-flex gap-3">
                    <a href="{{$data_parametre?->lien_facebook ?? '#'}}" class="text-white fs-4"><i class="ri-facebook-circle-fill"></i></a>
                    <a href="{{$data_parametre?->lien_twitter ?? '#'}}" class="text-white fs-4"><i class="ri-twitter-fill"></i></a>
                    <a href="{{$data_parametre?->lien_instagram ?? '#'}}" class="text-white fs-4"><i class="ri-instagram-fill"></i></a>
                    <a href="{{$data_parametre?->lien_linkedin ?? '#'}}" class="text-white fs-4"><i class="ri-linkedin-box-fill"></i></a>
                </div>
            </div>

            <!-- Liens rapides -->
            <div class="col-lg-2 col-md-6">
                <h5 class="text-white mb-3">Liens rapides</h5>
                <ul class="footer-links">
                    <li><a href="{{ route('home') }}">Accueil</a></li>
                    <li><a href="{{ route('properties.index') }}">Nos biens</a></li>
                    <li><a href="{{ route('properties.index', ['type_annonce' => 'location']) }}">Location</a></li>
                    <li><a href="{{ route('properties.index', ['type_annonce' => 'vente']) }}">Vente</a></li>
                </ul>
            </div>

            <!-- Services -->
            <div class="col-lg-3 col-md-6">
                <h5 class="text-white mb-3">Nos services</h5>
                <ul class="footer-links">
                    <li><a href="#">Gestion locative</a></li>
                    <li><a href="#">Estimation gratuite</a></li>
                    <li><a href="#">Accompagnement achat</a></li>
                    <li><a href="#">Conseil juridique</a></li>
                </ul>
            </div>

            <!-- Contact -->
            <div class="col-lg-3 col-md-6">
                <h5 class="text-white mb-3">Contact</h5>
                <ul class="footer-links">
                    <li>
                        <i class="ri-map-pin-line"></i>
                        {{$data_parametre?->localisation ?? '123 Rue de l\'Abidjan, Côte d\'Ivoire'}}
                    </li>
                    <li>
                        <i class="ri-phone-line"></i>
                        {{$data_parametre?->contact_principal ?? '+225 01 23 45 67 89'}}
                    </li>
                    <li>
                        <i class="ri-mail-line"></i>
                        {{$data_parametre?->email_principal ?? 'contact@sageimmo.fr'}}
                    </li>
                    <li>
                        <i class="ri-time-line"></i>
                        Lun - Ven: 9h - 18h
                    </li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; {{ date('Y') }} Sages Immo. Tous droits réservés.</p>
        </div>
    </div>
</footer>
