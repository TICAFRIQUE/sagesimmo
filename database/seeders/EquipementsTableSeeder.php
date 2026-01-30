<?php

namespace Database\Seeders;

use App\Models\Equipement;
use Illuminate\Database\Seeder;

class EquipementsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $equipements = [
            [
                'nom' => 'Ascenseur',
                'description' => 'Immeuble équipé d\'un ascenseur',
                'icone' => 'ri-arrow-up-down-line',
                'ordre' => 1,
            ],
            [
                'nom' => 'Parking',
                'description' => 'Place de parking disponible',
                'icone' => 'ri-parking-box-line',
                'ordre' => 2,
            ],
            [
                'nom' => 'Jardin',
                'description' => 'Espace jardin privé ou partagé',
                'icone' => 'ri-plant-line',
                'ordre' => 3,
            ],
            [
                'nom' => 'Piscine',
                'description' => 'Piscine privée ou commune',
                'icone' => 'ri-water-flash-line',
                'ordre' => 4,
            ],
            [
                'nom' => 'Balcon',
                'description' => 'Balcon ou loggia',
                'icone' => 'ri-door-open-line',
                'ordre' => 5,
            ],
            [
                'nom' => 'Terrasse',
                'description' => 'Terrasse privée',
                'icone' => 'ri-community-line',
                'ordre' => 6,
            ],
            [
                'nom' => 'Meublé',
                'description' => 'Bien entièrement meublé',
                'icone' => 'ri-home-smile-2-line',
                'ordre' => 7,
            ],
            [
                'nom' => 'Climatisation',
                'description' => 'Système de climatisation',
                'icone' => 'ri-temp-cold-line',
                'ordre' => 8,
            ],
            [
                'nom' => 'Chauffage',
                'description' => 'Système de chauffage',
                'icone' => 'ri-fire-line',
                'ordre' => 9,
            ],
            [
                'nom' => 'Cuisine équipée',
                'description' => 'Cuisine entièrement équipée',
                'icone' => 'ri-restaurant-line',
                'ordre' => 10,
            ],
            [
                'nom' => 'Internet',
                'description' => 'Connexion Internet haut débit',
                'icone' => 'ri-wifi-line',
                'ordre' => 11,
            ],
            [
                'nom' => 'Sécurité',
                'description' => 'Système de sécurité (gardien, caméras)',
                'icone' => 'ri-shield-check-line',
                'ordre' => 12,
            ],
            [
                'nom' => 'Garage',
                'description' => 'Garage fermé',
                'icone' => 'ri-car-line',
                'ordre' => 13,
            ],
            [
                'nom' => 'Cave',
                'description' => 'Cave ou espace de rangement',
                'icone' => 'ri-archive-drawer-line',
                'ordre' => 14,
            ],
        ];

        foreach ($equipements as $equipement) {
            Equipement::create($equipement);
        }
    }
}
