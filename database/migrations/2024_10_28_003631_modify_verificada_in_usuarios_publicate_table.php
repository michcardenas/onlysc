<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyVerificadaInUsuariosPublicateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('usuarios_publicate', function (Blueprint $table) {
            // Modificar la columna verificada para que sea INTEGER, permita NULL, y sea única
            $table->integer('verificada')->nullable()->default(null)->change();

            // Asegurar que los valores sean únicos, exceptuando NULL
            $table->unique('verificada');
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
            // Revertir la columna a su estado original si es necesario
            $table->dropUnique(['verificada']);
            $table->tinyInteger('verificada')->default(0)->change();
        });
    }
}
