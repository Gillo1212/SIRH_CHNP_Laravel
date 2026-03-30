<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DivisionSeeder extends Seeder
{
    public function run(): void
    {
        // Les divisions sont des sous-unités des services.
        // Elles sont créées sans service parent par défaut (id_service nullable).
        // Les associer à un service se fait via l'interface ou en ajoutant id_service ici.
        $divisions = [
            ['nom_division' => 'Division A', 'id_service' => null, 'created_at' => now(), 'updated_at' => now()],
            ['nom_division' => 'Division B', 'id_service' => null, 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('divisions')->insert($divisions);
    }
}