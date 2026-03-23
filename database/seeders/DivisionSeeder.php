<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DivisionSeeder extends Seeder
{
    public function run(): void
    {
        $divisions = [
            ['nom_division' => 'Direction Générale', 'created_at' => now(), 'updated_at' => now()],
            ['nom_division' => 'Direction des Ressources Humaines', 'created_at' => now(), 'updated_at' => now()],
            ['nom_division' => 'Direction des Soins Infirmiers', 'created_at' => now(), 'updated_at' => now()],
            ['nom_division' => 'Direction Médicale', 'created_at' => now(), 'updated_at' => now()],
            ['nom_division' => 'Division Administrative et Financière', 'created_at' => now(), 'updated_at' => now()],
            ['nom_division' => 'Division Hygiène et Sécurité', 'created_at' => now(), 'updated_at' => now()],
            ['nom_division' => 'Division Formation', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('divisions')->insert($divisions);
    }
}