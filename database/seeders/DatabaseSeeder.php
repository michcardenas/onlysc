<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Ejecutar los nuevos seeders
        $this->call([
            ServiciosSeeder::class,
            AtributosSeeder::class,
        ]);
    }
}