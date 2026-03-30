<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            // Services Cliniques
            ['nom_service' => 'Pédiatrie',        'type_service' => 'Clinique',        'tel_service' => '33 836 1400', 'nbre_agents' => 0],
            ['nom_service' => 'Maternité',         'type_service' => 'Clinique',        'tel_service' => '33 836 1401', 'nbre_agents' => 0],
            ['nom_service' => 'Chirurgie',         'type_service' => 'Clinique',        'tel_service' => '33 836 1402', 'nbre_agents' => 0],
            ['nom_service' => 'Médecine Interne',  'type_service' => 'Clinique',        'tel_service' => '33 836 1403', 'nbre_agents' => 0],
            ['nom_service' => 'Urgences (SAU)',    'type_service' => 'Clinique',        'tel_service' => '33 836 1404', 'nbre_agents' => 0],
            ['nom_service' => 'Réanimation',       'type_service' => 'Clinique',        'tel_service' => '33 836 1405', 'nbre_agents' => 0],

            // Services Aide au Diagnostic
            ['nom_service' => 'Radiologie',        'type_service' => 'Aide_diagnostic', 'tel_service' => '33 836 1410', 'nbre_agents' => 0],
            ['nom_service' => 'Laboratoire',       'type_service' => 'Aide_diagnostic', 'tel_service' => '33 836 1411', 'nbre_agents' => 0],
            ['nom_service' => 'Pharmacie',         'type_service' => 'Aide_diagnostic', 'tel_service' => '33 836 1412', 'nbre_agents' => 0],

            // Services Administratifs
            ['nom_service' => 'Ressources Humaines', 'type_service' => 'Administratif', 'tel_service' => '33 836 1420', 'nbre_agents' => 0],
            ['nom_service' => 'Comptabilité',        'type_service' => 'Administratif', 'tel_service' => '33 836 1421', 'nbre_agents' => 0],
            ['nom_service' => 'Finances',            'type_service' => 'Administratif', 'tel_service' => '33 836 1422', 'nbre_agents' => 0],
            ['nom_service' => 'Accueil et Orientation', 'type_service' => 'Administratif', 'tel_service' => '33 836 1423', 'nbre_agents' => 0],

            // Services Support
            ['nom_service' => 'Hygiène et Salubrité', 'type_service' => 'Support', 'tel_service' => '33 836 1430', 'nbre_agents' => 0],
            ['nom_service' => 'Maintenance',          'type_service' => 'Support', 'tel_service' => '33 836 1431', 'nbre_agents' => 0],
            ['nom_service' => 'Sécurité',             'type_service' => 'Support', 'tel_service' => '33 836 1432', 'nbre_agents' => 0],
            ['nom_service' => 'Informatique',         'type_service' => 'Support', 'tel_service' => '33 836 1433', 'nbre_agents' => 0],
        ];

        foreach ($services as $service) {
            DB::table('services')->insert(array_merge($service, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}