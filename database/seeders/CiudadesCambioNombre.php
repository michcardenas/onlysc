<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CiudadesCambioNombre extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Eliminar los datos existentes
        DB::table('ciudades')->truncate();

        // Insertar los datos corregidos
        DB::table('ciudades')->insert([
            ['nombre' => 'Antofagasta'],
            ['nombre' => 'Calama'],
            ['nombre' => 'Chillán'],
            ['nombre' => 'Concepción'],
            ['nombre' => 'Copiapó'],
            ['nombre' => 'Curicó'],
            ['nombre' => 'Iquique'],
            ['nombre' => 'La Serena'],
            ['nombre' => 'Linares'],
            ['nombre' => 'Los Ángeles'],
            ['nombre' => 'Osorno'],
            ['nombre' => 'Pucón'],
            ['nombre' => 'Puerto Montt'],
            ['nombre' => 'Punta Arenas'],
            ['nombre' => 'Quilpué'],
            ['nombre' => 'Rancagua'],
            ['nombre' => 'San Fernando'],
            ['nombre' => 'Santiago de Chile'],
            ['nombre' => 'Talca'],
            ['nombre' => 'Temuco'],
            ['nombre' => 'Valdivia'],
            ['nombre' => 'Viña del Mar'],
        ]);
    }
}