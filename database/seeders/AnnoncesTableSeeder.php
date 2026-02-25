<?php

namespace Database\Seeders;

use App\Models\Annonce;
use App\Models\TypeBien;
use App\Models\Equipement;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class AnnoncesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer les données nécessaires
        $typesBiens = TypeBien::all();
        $equipements = Equipement::all();
        
        // Récupérer les utilisateurs avec rôles, sinon tous les utilisateurs
        $users = User::whereHas('roles', function($q) {
            $q->where('name', 'proprietaire');
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

        // Templates de descriptions
        $descriptionsTemplates = [
            "Magnifique propriété offrant un cadre de vie exceptionnel. {caracteristiques}. Idéal pour une famille recherchant confort et tranquillité. Proche de toutes commodités.",
            "Belle propriété {type} située dans un quartier calme et recherché. {caracteristiques}. Environnement sécurisé et agréable, parfait pour un investissement locatif.",
            "Superbe {type} moderne et lumineux avec finitions de qualité. {caracteristiques}. Emplacement privilégié avec accès facile aux commerces et écoles.",
            "Propriété d'exception dans un environnement résidentiel de standing. {caracteristiques}. Rare sur le marché, à saisir rapidement!",
            "Charmant {type} offrant confort et praticité. {caracteristiques}. Idéalement situé, proche des axes routiers et des services essentiels.",
        ];

        $caracteristiques = [
            "Cuisine équipée, salon spacieux, chambres climatisées",
            "Grande terrasse, parking privé, jardin arboré",
            "Moderne et bien entretenu, carrelage de qualité",
            "Sécurité 24h/24, portail automatique, citerne d'eau",
            "Vue dégagée, environnement calme, finitions soignées",
            "Espaces bien agencés, lumineux, ventilation naturelle",
        ];

        // URLs d'images immobilières gratuites (Unsplash - domaine immobilier)
        $imageUrls = [
            'https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=800',
            'https://images.unsplash.com/photo-1580587771525-78b9dba3b914?w=800',
            'https://images.unsplash.com/photo-1568605114967-8130f3a36994?w=800',
            'https://images.unsplash.com/photo-1572120360610-d971b9d7767c?w=800',
            'https://images.unsplash.com/photo-1582268611958-ebfd161ef9cf?w=800',
            'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?w=800',
            'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=800',
            'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?w=800',
            'https://images.unsplash.com/photo-1613977257363-707ba9348227?w=800',
            'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?w=800',
            'https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?w=800',
            'https://images.unsplash.com/photo-1600047509807-ba8f99d2cdde?w=800',
            'https://images.unsplash.com/photo-1600585154526-990dced4db0d?w=800',
            'https://images.unsplash.com/photo-1600566753086-00f18fb6b3ea?w=800',
            'https://images.unsplash.com/photo-1600573472550-8090b5e0745e?w=800',
        ];

        $this->command->info('Génération de 10 annonces avec images...');
        $bar = $this->command->getOutput()->createProgressBar(10);

        for ($i = 1; $i <= 10; $i++) {
            $typeBien = $typesBiens->random();
            $typeTransaction = rand(0, 1) ? 'location' : 'vente';
            $ville = $villes[array_rand($villes)];
            $quartier = $ville['quartiers'][array_rand($ville['quartiers'])];
            
            $nombreChambres = rand(1, 6);
            $nombreSallesBain = rand(1, min(3, $nombreChambres));
            $nombrePieces = $nombreChambres + $nombreSallesBain + rand(1, 3);
            $surface = rand(40, 500);
            
            // Prix selon le type et la localisation
            if ($typeTransaction === 'location') {
                $prixBase = $surface * rand(500, 2000);
            } else {
                $prixBase = $surface * rand(100000, 500000);
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
            $multiplicateur = $multiplicateurs[$ville['nom']] ?? 1.0;
            $prix = round($prixBase * $multiplicateur, -3);

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
                ['{type}', '{caracteristiques}'],
                [strtolower($typeBien->nom), $caracteristiques[array_rand($caracteristiques)]],
                $descTemplate
            );

            // Création de l'annonce
            $annonce = Annonce::create([
                'titre' => $typeBien->nom . ' ' . $nombreChambres . ' chambres - ' . $quartier,
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
                'adresse' => 'Lot ' . rand(1, 999) . ', Rue ' . rand(1, 50),
                'ville' => $ville['nom'],
                'quartier' => $quartier,
                'code_postal' => '00225',
                'statut' => 'disponible',
                'en_vedette' => rand(0, 100) < 20, // 20% en vedette
                'date_disponibilite' => now()->addDays(rand(-30, 60)),
                'annee_construction' => rand(2010, 2025),
                'caracteristiques_supplementaires' => json_encode([
                    'orientation' => ['Nord', 'Sud', 'Est', 'Ouest'][array_rand(['Nord', 'Sud', 'Est', 'Ouest'])],
                    'etat' => ['Neuf', 'Excellent', 'Bon', 'À rénover'][array_rand(['Neuf', 'Excellent', 'Bon', 'À rénover'])],
                    'meuble' => rand(0, 1) ? 'Oui' : 'Non',
                ]),
                'reference' => 'REF-' . strtoupper(Str::random(8)),
                'proprietaire_id' => $users->random()->id, // Le propriétaire du bien
                'created_by_id' => $users->random()->id,   // Celui qui a créé l'annonce
                'nombre_vues' => rand(0, 500),
                'est_bien_agence' => rand(0, 1) ? true : false,
            ]);

            // Ajouter des équipements (entre 3 et 8)
            if ($equipements->isNotEmpty()) {
                $randomEquipements = $equipements->random(rand(3, min(8, $equipements->count())));
                $annonce->equipements()->attach($randomEquipements->pluck('id')->toArray());
            }

            // Ajouter des images (entre 3 et 6 images par annonce)
            $nombreImages = rand(3, 6);
            for ($j = 0; $j < $nombreImages; $j++) {
                try {
                    // Sélectionner une URL d'image aléatoire
                    $imageUrl = $imageUrls[array_rand($imageUrls)] . '&sig=' . rand(1, 1000); // Ajouter un paramètre unique
                    
                    // Télécharger l'image
                    $imageContent = @file_get_contents($imageUrl);
                    
                    if ($imageContent !== false) {
                        // Créer un fichier temporaire
                        $tempFile = tmpfile();
                        $tempPath = stream_get_meta_data($tempFile)['uri'];
                        file_put_contents($tempPath, $imageContent);
                        
                        // Ajouter l'image via Spatie Media Library
                        $annonce->addMedia($tempPath)
                            ->usingFileName('property_' . $annonce->id . '_' . ($j + 1) . '.jpg')
                            ->toMediaCollection('images');
                        
                        fclose($tempFile);
                    }
                } catch (\Exception $e) {
                    // En cas d'erreur, continuer sans l'image
                    $this->command->warn("Impossible d'ajouter l'image pour l'annonce {$annonce->id}");
                }
            }

            $bar->advance();
        }

        $bar->finish();
        $this->command->newLine();
        $this->command->info('✅ 100 annonces créées avec succès avec leurs images!');
    }
}
