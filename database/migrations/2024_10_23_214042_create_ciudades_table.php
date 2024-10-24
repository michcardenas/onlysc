<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateCiudadesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ciudades', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->timestamps();
        });

        // Insertar registros iniciales
        DB::table('ciudades')->insert([
            ['nombre' => 'antofagasta'],
            ['nombre' => 'calama'],
            ['nombre' => 'chillan'],
            ['nombre' => 'concepcion'],
            ['nombre' => 'copiapo'],
            ['nombre' => 'curico'],
            ['nombre' => 'iquique'],
            ['nombre' => 'la-serena'],
            ['nombre' => 'linares'],
            ['nombre' => 'los-angeles'],
            ['nombre' => 'osorno'],
            ['nombre' => 'pucon'],
            ['nombre' => 'puerto-montt'],
            ['nombre' => 'punta-arenas'],
            ['nombre' => 'quilpue'],
            ['nombre' => 'rancagua'],
            ['nombre' => 'san-fernando'],
            ['nombre' => 'santiago-de-chile'],
            ['nombre' => 'talca'],
            ['nombre' => 'temuco'],
            ['nombre' => 'valdivia'],
            ['nombre' => 'vina-del-mar'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ciudades');
    }
}
