<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Ordre important : respecter les dépendances
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            DivisionSeeder::class,
            ServiceSeeder::class,
            TypeCongeSeeder::class,
            TypePosteSeeder::class,
            UserSeeder::class,
            ContratSeeder::class,
            RichDataSeeder::class,
        ]);
    }
}