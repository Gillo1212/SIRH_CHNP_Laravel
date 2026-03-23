<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TypeCongeSeeder extends Seeder
{
    public function run(): void
    {
        $typeConges = [
            [
                'libelle' => 'Congé Administratif',
                'duree' => '30 jours par an',
                'nb_jours_droit' => 30,
                'deductible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'libelle' => 'Congé de Maternité',
                'duree' => '14 semaines',
                'nb_jours_droit' => 98, // 14 semaines
                'deductible' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'libelle' => 'Congé de Maladie',
                'duree' => 'Selon certificat médical',
                'nb_jours_droit' => 90, // 3 mois maximum
                'deductible' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'libelle' => 'Congé Exceptionnel',
                'duree' => 'Variable selon motif',
                'nb_jours_droit' => 10,
                'deductible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'libelle' => 'Congé Sans Solde',
                'duree' => 'Variable',
                'nb_jours_droit' => 0,
                'deductible' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'libelle' => 'Congé Syndical',
                'duree' => 'Selon convention',
                'nb_jours_droit' => 12,
                'deductible' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'libelle' => 'Congé de Formation',
                'duree' => 'Variable',
                'nb_jours_droit' => 15,
                'deductible' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('type_conges')->insert($typeConges);
    }
}