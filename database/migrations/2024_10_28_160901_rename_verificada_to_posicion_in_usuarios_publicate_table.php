<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameVerificadaToPosicionInUsuariosPublicateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('usuarios_publicate', function (Blueprint $table) {
            // Renombrar la columna 'verificada' a 'posicion'
            $table->renameColumn('verificada', 'posicion');

            // Asegurar que 'posicion' tenga valores únicos y permita null
            $table->integer('posicion')->nullable()->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('usuarios_publicate', function (Blueprint $table) {
            // Renombrar de vuelta la columna 'posicion' a 'verificada'
            $table->renameColumn('posicion', 'verificada');

            // Revertir la unicidad y permitir null en 'verificada'
            $table->boolean('verificada')->nullable()->change();
        });
    }
}
