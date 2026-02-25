<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        DB::table('roles')->delete();
        
        DB::table('roles')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'administrateur',
                'guard_name' => 'web',
                'created_at' => '2025-04-22 08:40:37',
                'updated_at' => '2025-04-22 08:57:57',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'developpeur',
                'guard_name' => 'web',
                'created_at' => '2025-04-22 08:40:46',
                'updated_at' => '2025-04-22 08:40:46',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'superadmin',
                'guard_name' => 'web',
                'created_at' => '2025-04-23 08:47:08',
                'updated_at' => '2025-04-23 08:47:08',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'proprietaire',
                'guard_name' => 'web',
                'created_at' => '2026-01-23 00:00:00',
                'updated_at' => '2026-01-23 00:00:00',
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'locataire',
                'guard_name' => 'web',
                'created_at' => '2026-01-23 00:00:00',
                'updated_at' => '2026-01-23 00:00:00',
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'acheteur',
                'guard_name' => 'web',
                'created_at' => '2026-01-23 00:00:00',
                'updated_at' => '2026-01-23 00:00:00',
            ),
            6 => 
            array (
                'id' => 7,
                'name' => 'prospect',
                'guard_name' => 'web',
                'created_at' => '2026-02-10 00:00:00',
                'updated_at' => '2026-02-10 00:00:00',
            ),
                7 => 
            array (
                'id' => 8,
                'name' => 'commercial',
                'guard_name' => 'web',
                'created_at' => '2026-02-10 00:00:00',
                'updated_at' => '2026-02-10 00:00:00',
            )
        ));
        
        
    }
}