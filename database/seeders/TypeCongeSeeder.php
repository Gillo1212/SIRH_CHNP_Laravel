<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TypeCongeSeeder extends Seeder
{
    public function run(): void
    {
        // Truncater proprement (FK désactivées)
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('solde_conges')->truncate();
        DB::table('conges')->truncate();
        DB::table('type_conges')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $typeConges = [
            [
                'libelle'        => 'Congé Administratif',
                'duree'          => '30 jours par an',
                'nb_jours_droit' => 30,
                'deductible'     => true,
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            [
                'libelle'        => 'Congé de Maternité',
                'duree'          => '14 semaines (98 jours)',
                'nb_jours_droit' => 98,
                'deductible'     => false,
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            [
                'libelle'        => 'Congé Rayon X',
                'duree'          => '22 jours',
                'nb_jours_droit' => 22,
                'deductible'     => false,
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
        ];

        DB::table('type_conges')->insert($typeConges);
    }
}