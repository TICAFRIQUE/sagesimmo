<footer class="site-footer">
    <div class="container">
        <div class="row g-4">
            <!-- À propos -->
            <div class="col-lg-4 col-md-6">
                <div class="footer-brand">
                    <i class="ri-building-4-line"></i> Sage Immo
                </div>
                <p class="mb-4">
                    Votre partenaire de confiance pour trouver la propriété de vos rêves. 
                    Nous vous accompagnons dans tous vos projets immobiliers.
                </p>
                <div class="d-flex gap-3">
                    <a href="#" class="text-white fs-4"><i class="ri-facebook-circle-fill"></i></a>
                    <a href="#" class="text-white fs-4"><i class="ri-twitter-fill"></i></a>
                    <a href="#" class="text-white fs-4"><i class="ri-instagram-fill"></i></a>
                    <a href="#" class="text-white fs-4"><i class="ri-linkedin-box-fill"></i></a>
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
                        123 Rue de l'Abidjan, Côte d'Ivoire
                    </li>
                    <li>
                        <i class="ri-phone-line"></i> 
                       +225 00 00 00 00
                    </li>
                    <li>
                        <i class="ri-mail-line"></i> 
                        contact@sageimmo.fr
                    </li>
                    <li>
                        <i class="ri-time-line"></i> 
                        Lun - Ven: 9h - 18h
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; {{ date('Y') }} Sage Immo. Tous droits réservés.</p>
        </div>
    </div>
</footer>
