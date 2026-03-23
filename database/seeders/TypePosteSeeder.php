<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TypePosteSeeder extends Seeder
{
    public function run(): void
    {
        $typePostes = [
            [
                'libelle' => 'Jour',
                'description' => 'Poste de jour (08h00 - 16h00)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'libelle' => 'Nuit',
                'description' => 'Poste de nuit (20h00 - 08h00)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'libelle' => 'Garde',
                'description' => 'Garde de 24 heures',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'libelle' => 'Permanence',
                'description' => 'Permanence sur appel',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'libelle' => 'Astreinte',
                'description' => 'Astreinte à domicile',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'libelle' => 'Repos',
                'description' => 'Jour de repos',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('type_postes')->insert($typePostes);
    }
}