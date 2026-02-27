<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DefaultProprietaireSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $email = 'agence@local.com';

        // Ne pas créer si déjà présent
        if (User::where('email', $email)->exists()) {
            return;
        }

        $user = User::create([
            'username' => 'agence',
            'email' => $email,
            'phone' => '0000000000',
            'password' => Hash::make('password'),
            'role' => 'proprietaire',
            'type_proprietaire' => 'agence',
        ]);

        // Assigner le rôle spatie si la méthode est disponible
        if (method_exists($user, 'assignRole')) {
            try {
                $user->assignRole('proprietaire');
            } catch (\Throwable $e) {
                // ignore si rôle non trouvé
            }
        }
    }
}
