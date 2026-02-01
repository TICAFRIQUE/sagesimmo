<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        DB::table('users')->delete();
        
        // Créer l'utilisateur développeur
        DB::table('users')->insert(array (
            0 => 
            array (
                'id' => 13029781151,
                'username' => 'developpeur',
                'phone' => '0142855584',
                'email' => 'developpeur@gmail.com',
                'email_verified_at' => NULL,
                'password' => '$2y$12$xVJkKjsoY/E5ZjJL.jbu7ufYS5gFtxkXEo.Ue2cjONLTQlgo7Vc22',
                'avatar' => NULL,
                'role' => 'developpeur',
                'remember_token' => NULL,
                'created_at' => '2025-04-22 11:16:21',
                'updated_at' => '2025-04-24 14:55:19',
                'deleted_at' => NULL,
            ),
        ));

        // Assigner le rôle développeur
        $developer = User::where('email', 'developpeur@gmail.com')->first();
        $developer->assignRole('developpeur');
        
        // Générer 20 utilisateurs clients avec rôles
        $this->command->info('Génération de 20 utilisateurs clients...');
        
        $roles = ['locataire', 'proprietaire', 'acheteur'];
        $prenoms = ['Jean', 'Marie', 'Kouadio', 'Aya', 'Ibrahim', 'Fatima', 'Yao', 'Aminata', 'Kofi', 'Adjoua'];
        $noms = ['Kouassi', 'Diallo', 'Traoré', 'N\'Guessan', 'Ouattara', 'Koné', 'Bamba', 'Yapi', 'Touré', 'Brou'];

        for ($i = 1; $i <= 20; $i++) {
            $prenom = $prenoms[array_rand($prenoms)];
            $nom = $noms[array_rand($noms)];
            $roleName = $roles[array_rand($roles)];
            
            $email = strtolower($prenom . '.' . $nom . $i . '@example.com');
            $phone = '07' . rand(10, 99) . rand(10, 99) . rand(10, 99) . rand(10, 99);

            // Créer l'utilisateur
            $user = User::create([
                'username' => $prenom . ' ' . $nom,
                'email' => $email,
                'phone' => $phone,
                'role' => $roleName,
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]);

            // Assigner le rôle
            $user->assignRole($roleName);

            $this->command->info("✓ {$user->username} créé avec le rôle: {$roleName}");
        }

        $this->command->newLine();
        $this->command->info('✅ 20 utilisateurs créés avec succès!');
        $this->command->info('   - Locataires: ' . User::role('locataire')->count());
        $this->command->info('   - Propriétaires: ' . User::role('proprietaire')->count());
        $this->command->info('   - Acheteurs: ' . User::role('acheteur')->count());
        $this->command->newLine();
        $this->command->warn('📌 Mot de passe par défaut: password');
        
    }
}