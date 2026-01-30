<?php

namespace Database\Seeders;

use App\Models\TypeBien;
use Illuminate\Database\Seeder;

class TypeBiensTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $typeBiens = [
            [
                'nom' => 'Appartement',
                'description' => 'Appartement résidentiel dans un immeuble',
                'icone' => 'ri-building-line',
                'ordre' => 1,
            ],
            [
                'nom' => 'Maison',
                'description' => 'Maison individuelle',
                'icone' => 'ri-home-4-line',
                'ordre' => 2,
            ],
            [
                'nom' => 'Villa',
                'description' => 'Villa de standing avec jardin',
                'icone' => 'ri-home-heart-line',
                'ordre' => 3,
            ],
            [
                'nom' => 'Terrain',
                'description' => 'Terrain constructible ou agricole',
                'icone' => 'ri-landscape-line',
                'ordre' => 4,
            ],
            [
                'nom' => 'Bureau',
                'description' => 'Espace de bureau professionnel',
                'icone' => 'ri-briefcase-line',
                'ordre' => 5,
            ],
            [
                'nom' => 'Commerce',
                'description' => 'Local commercial',
                'icone' => 'ri-store-2-line',
                'ordre' => 6,
            ],
            [
                'nom' => 'Immeuble',
                'description' => 'Immeuble entier',
                'icone' => 'ri-building-2-line',
                'ordre' => 7,
            ],
            [
                'nom' => 'Studio',
                'description' => 'Studio compact',
                'icone' => 'ri-home-smile-line',
                'ordre' => 8,
            ],
            [
                'nom' => 'Entrepôt',
                'description' => 'Entrepôt ou local de stockage',
                'icone' => 'ri-archive-line',
                'ordre' => 9,
            ],
        ];

        foreach ($typeBiens as $typeBien) {
            TypeBien::create($typeBien);
        }
    }
}
