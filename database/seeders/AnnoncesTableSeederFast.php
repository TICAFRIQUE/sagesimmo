<?php

namespace Database\Seeders;

use App\Models\Annonce;
use App\Models\TypeBien;
use App\Models\Equipement;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AnnoncesTableSeederFast extends Seeder
{
    /**
     * Run the database seeds (Version rapide sans téléchargement d'images)
     */
    public function run(): void
    {
        // Récupérer les données nécessaires
        $typesBiens = TypeBien::all();
        $equipements = Equipement::all();
        
        // Récupérer les utilisateurs avec rôles, sinon tous les utilisateurs
        $users = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['proprietaire', 'admin']);
        })->get();
        
        // Si aucun utilisateur avec rôles, prendre tous les utilisateurs
        if ($users->isEmpty()) {
            $users = User::all();
        }

        if ($typesBiens->isEmpty() || $users->isEmpty()) {
            $this->command->error('Veuillez d\'abord exécuter les seeders TypeBiens et Users');
            return;
        }

        // Charger les villes depuis le fichier de configuration
        $villesConfig = config('ville-commune');
        
        $villes = [
            ['nom' => 'ABIDJAN', 'quartiers' => $villesConfig['ABIDJAN']],
            ['nom' => 'YAMOUSSOUKRO', 'quartiers' => ['Habitat', 'Assabou', 'Morofe', 'N\'Zuessy', 'Dioulakro', 'Centre-ville']],
            ['nom' => 'BOUAKÉ', 'quartiers' => ['Dar-Es-Salam', 'Koko', 'Air France', 'Belleville', 'Liberté', 'Commerce']],
            ['nom' => 'DALOA', 'quartiers' => ['Lobia', 'Tazibouo', 'Commerce', 'Kennedy', 'Garage', 'Marais']],
            ['nom' => 'SAN-PÉDRO', 'quartiers' => ['Cité', 'Bardot', 'Balmer', 'Sear', 'Bateau Cassé', 'Plateau']],
            ['nom' => 'KORHOGO', 'quartiers' => ['Petit Paris', 'Sinistré', 'Koko', 'Résidentiel', 'Commerce', 'Air France']],
            ['nom' => 'MAN', 'quartiers' => ['Dokoui', 'Libreville', 'Domobly', 'Sangouiné', 'Centre-ville']],
        ];

        // Templates de descriptions riches
        $descriptionsTemplates = [
            "Magnifique {type} offrant un cadre de vie exceptionnel dans le quartier {quartier}. {caracteristiques}. Idéal pour une famille recherchant confort et tranquillité. Proche de toutes commodités : écoles, marchés, centres de santé.",
            
            "Belle propriété {type} située dans un quartier calme et recherché de {quartier}. {caracteristiques}. Environnement sécurisé et agréable, parfait pour un investissement locatif ou une résidence principale. Documentation complète disponible.",
            
            "Superbe {type} moderne et lumineux avec finitions de qualité supérieure. {caracteristiques}. Emplacement privilégié à {quartier} avec accès facile aux commerces, écoles internationales et axes routiers principaux.",
            
            "Propriété d'exception dans un environnement résidentiel de standing à {quartier}. {caracteristiques}. Construction récente aux normes internationales. Rare sur le marché, à saisir rapidement!",
            
            "Charmant {type} offrant confort et praticité dans le secteur de {quartier}. {caracteristiques}. Idéalement situé, proche des axes routiers et des services essentiels. Cadre paisible et sécurisé.",
            
            "Splendide {type} alliant modernité et fonctionnalité. {caracteristiques}. Situé à {quartier}, ce bien bénéficie d'un emplacement stratégique avec toutes les commodités à proximité. État impeccable.",
        ];

        $caracteristiques = [
            "Cuisine américaine équipée, grand salon cathédrale, chambres climatisées avec placards intégrés",
            "Vaste terrasse couverte, parking pour 3 véhicules, jardin arboré avec système d'arrosage",
            "Construction moderne et bien entretenue, carrelage de première qualité, faux plafond design",
            "Sécurité 24h/24 avec gardien, portail automatique, double citerne d'eau avec système de pompage",
            "Vue panoramique dégagée, environnement calme sans vis-à-vis, finitions soignées et matériaux nobles",
            "Espaces bien agencés et fonctionnels, très lumineux, excellente ventilation naturelle",
            "Piscine privée, salle de gym, buanderie équipée, générateur électrique",
            "Système de vidéosurveillance, alarme connectée, portail sécurisé, éclairage extérieur automatique",
            "Garage fermé, local technique, cave à vin, suite parentale avec dressing",
        ];

        $this->command->info('Génération de 100 annonces (version rapide)...');
        $bar = $this->command->getOutput()->createProgressBar(100);

        for ($i = 1; $i <= 100; $i++) {
            $typeBien = $typesBiens->random();
            $typeTransaction = rand(0, 1) ? 'location' : 'vente';
            $ville = $villes[array_rand($villes)];
            $quartier = $ville['quartiers'][array_rand($ville['quartiers'])];
            
            $nombreChambres = rand(1, 6);
            $nombreSallesBain = rand(1, min(3, $nombreChambres));
            $nombrePieces = $nombreChambres + $nombreSallesBain + rand(1, 4);
            $surface = rand(40, 500);
            
            // Prix selon le type et la localisation
            if ($typeTransaction === 'location') {
                $prixBase = $surface * rand(800, 3000);
            } else {
                $prixBase = $surface * rand(150000, 800000);
            }
            
            // Ajustement du prix selon la ville
            $multiplicateurs = [
                'ABIDJAN' => 1.8,
                'YAMOUSSOUKRO' => 1.3,
                'BOUAKÉ' => 1.1,
                'DALOA' => 1.0,
                'SAN-PÉDRO' => 1.2,
                'KORHOGO' => 0.9,
                'MAN' => 0.85,
            ];
            $prix = round($prixBase * $multiplicateurs[$ville['nom']], -3);

            // Génération des commissions
            $typeCommission = rand(0, 1) ? 'pourcentage' : 'fixe';
            if ($typeCommission === 'pourcentage') {
                // Commission en pourcentage (entre 2% et 10%)
                $commission = rand(2, 10);
            } else {
                // Commission fixe (basée sur le prix)
                if ($typeTransaction === 'location') {
                    // Pour les locations: entre 50 000 et 150 000 FCFA
                    $commission = rand(50, 150) * 1000;
                } else {
                    // Pour les ventes: entre 0,5% et 2% du prix (arrondi)
                    $commission = round($prix * rand(5, 20) / 1000, -3);
                }
            }

            // Génération de la description
            $descTemplate = $descriptionsTemplates[array_rand($descriptionsTemplates)];
            $description = str_replace(
                ['{type}', '{quartier}', '{caracteristiques}'],
                [strtolower($typeBien->nom), $quartier, $caracteristiques[array_rand($caracteristiques)]],
                $descTemplate
            );

            // Adresses réalistes
            $rues = ['Avenue de la République', 'Boulevard Saint-Michel', 'Rue de la Paix', 'Avenue Jean-Paul II', 
                     'Rue du Commerce', 'Boulevard de la Marina', 'Avenue Clozel', 'Rue Zinsou', 
                     'Avenue Steinmetz', 'Rue Bayol', 'Boulevard Lagunaire'];
            
            $adresse = $rues[array_rand($rues)] . ', Lot ' . rand(1, 999);

            // Coordonnées GPS approximatives selon la ville (Côte d'Ivoire)
            $coordonnees = [
                'ABIDJAN' => ['lat' => 5.3600 + (rand(-100, 100) / 1000), 'lng' => -4.0083 + (rand(-100, 100) / 1000)],
                'YAMOUSSOUKRO' => ['lat' => 6.8276 + (rand(-50, 50) / 1000), 'lng' => -5.2893 + (rand(-50, 50) / 1000)],
                'BOUAKÉ' => ['lat' => 7.6906 + (rand(-50, 50) / 1000), 'lng' => -5.0300 + (rand(-50, 50) / 1000)],
                'DALOA' => ['lat' => 6.8770 + (rand(-50, 50) / 1000), 'lng' => -6.4503 + (rand(-50, 50) / 1000)],
                'SAN-PÉDRO' => ['lat' => 4.7485 + (rand(-50, 50) / 1000), 'lng' => -6.6363 + (rand(-50, 50) / 1000)],
                'KORHOGO' => ['lat' => 9.4580 + (rand(-50, 50) / 1000), 'lng' => -5.6294 + (rand(-50, 50) / 1000)],
                'MAN' => ['lat' => 7.4125 + (rand(-50, 50) / 1000), 'lng' => -7.5544 + (rand(-50, 50) / 1000)],
            ];

            // Création de l'annonce
            $annonce = Annonce::create([
                'titre' => $typeBien->nom . ' ' . $nombreChambres . ' chambres - ' . $quartier . ', ' . $ville['nom'],
                'description' => $description,
                'type_transaction' => $typeTransaction,
                'type_bien_id' => $typeBien->id,
                'prix' => $prix,
                'commission' => $commission,
                'type_commission' => $typeCommission,
                'surface' => $surface,
                'nombre_chambres' => $nombreChambres,
                'nombre_salles_bain' => $nombreSallesBain,
                'nombre_pieces' => $nombrePieces,
                'etage' => rand(0, 5),
                'adresse' => $adresse,
                'ville' => $ville['nom'],
                'quartier' => $quartier,
                'code_postal' => '00225',
                'latitude' => $coordonnees[$ville['nom']]['lat'],
                'longitude' => $coordonnees[$ville['nom']]['lng'],
                'statut' => $i <= 85 ? 'disponible' : ['en_attente', 'loue', 'vendu'][array_rand(['en_attente', 'loue', 'vendu'])],
                'en_vedette' => rand(0, 100) < 25, // 25% en vedette
                'date_disponibilite' => now()->addDays(rand(-30, 60)),
                'annee_construction' => rand(2010, 2025),
                'caracteristiques_supplementaires' => json_encode([
                    'orientation' => ['Nord', 'Sud', 'Est', 'Ouest'][array_rand(['Nord', 'Sud', 'Est', 'Ouest'])],
                    'etat' => ['Neuf', 'Excellent', 'Bon', 'À rénover'][array_rand(['Neuf', 'Excellent', 'Bon', 'À rénover'])],
                    'meuble' => rand(0, 1) ? 'Oui' : 'Non',
                    'acces_handicape' => rand(0, 1) ? 'Oui' : 'Non',
                    'exposition' => ['Sud', 'Nord', 'Est', 'Ouest', 'Sud-Est', 'Sud-Ouest'][array_rand(['Sud', 'Nord', 'Est', 'Ouest', 'Sud-Est', 'Sud-Ouest'])],
                ]),
                'reference' => 'SAGE-' . strtoupper(Str::random(8)),
                'proprietaire_id' => $users->random()->id, // Le propriétaire du bien
                'created_by_id' => $users->random()->id,   // Celui qui a créé l'annonce
                'nombre_vues' => rand(0, 500),
            ]);

            // Ajouter des équipements (entre 4 et 10)
            if ($equipements->isNotEmpty()) {
                $randomEquipements = $equipements->random(rand(4, min(10, $equipements->count())));
                $annonce->equipements()->attach($randomEquipements->pluck('id')->toArray());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->command->newLine();
        $this->command->info('✅ 100 annonces créées avec succès!');
        $this->command->info('ℹ️  Note: Les images seront des placeholders. Utilisez AnnoncesTableSeeder pour des images réelles.');
    }
}
