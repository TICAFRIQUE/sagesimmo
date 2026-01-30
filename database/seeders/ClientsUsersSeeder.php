<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class ClientsUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer les rôles s'ils n'existent pas
        $roleLocataire = Role::firstOrCreate(['name' => 'locataire', 'guard_name' => 'web']);
        $roleProprietaire = Role::firstOrCreate(['name' => 'proprietaire', 'guard_name' => 'web']);
        $roleAcheteur = Role::firstOrCreate(['name' => 'acheteur', 'guard_name' => 'web']);

        $this->command->info('Génération de 20 utilisateurs clients...');

        $roles = [$roleLocataire, $roleProprietaire, $roleAcheteur];
        $prenoms = ['Jean', 'Marie', 'Kouadio', 'Aya', 'Ibrahim', 'Fatima', 'Yao', 'Aminata', 'Kofi', 'Adjoua'];
        $noms = ['Kouassi', 'Diallo', 'Traoré', 'N\'Guessan', 'Ouattara', 'Koné', 'Bamba', 'Yapi', 'Touré', 'Brou'];
        $villes = ['Abidjan', 'Yamoussoukro', 'Bouaké', 'Daloa', 'San-Pédro', 'Korhogo', 'Man'];

        for ($i = 1; $i <= 20; $i++) {
            $prenom = $prenoms[array_rand($prenoms)];
            $nom = $noms[array_rand($noms)];
            $ville = $villes[array_rand($villes)];
            $role = $roles[array_rand($roles)];
            
            // Créer un username unique
            $username = strtolower($prenom . $nom . $i);
            $email = strtolower($prenom . '.' . $nom . $i . '@example.com');
            $phone = '07' . rand(10, 99) . rand(10, 99) . rand(10, 99) . rand(10, 99);

            // Créer l'utilisateur
            $user = User::create([
                'username' => $prenom . ' ' . $nom,
                'email' => $email,
                'phone' => $phone,
                'password' => Hash::make('password'), // Mot de passe par défaut
                'email_verified_at' => now(),
            ]);

            // Assigner le rôle
            $user->assignRole($role->name);

            $this->command->info("✓ {$user->username} créé avec le rôle: {$role->name}");
        }

        $this->command->newLine();
        $this->command->info('✅ 20 utilisateurs créés avec succès!');
        $this->command->info('   - Locataires: ' . User::role('locataire')->count());
        $this->command->info('   - Propriétaires: ' . User::role('proprietaire')->count());
        $this->command->info('   - Acheteurs: ' . User::role('acheteur')->count());
        $this->command->newLine();
        $this->command->warn('📌 Mot de passe par défaut pour tous: password');
    }
}
