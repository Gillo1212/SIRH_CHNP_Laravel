<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Migration de données — Restreindre les types de congés aux 3 types officiels CHNP :
 *   1. Congé Administratif   — 30 jours (déductible)
 *   2. Congé de Maternité    — 14 semaines / 98 jours (non déductible)
 *   3. Congé Rayon X         — 22 jours (non déductible, agents exposés aux rayonnements)
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('solde_conges')->truncate();
        DB::table('conges')->truncate();
        DB::table('type_conges')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        DB::table('type_conges')->insert([
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
        ]);
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('solde_conges')->truncate();
        DB::table('conges')->truncate();
        DB::table('type_conges')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
