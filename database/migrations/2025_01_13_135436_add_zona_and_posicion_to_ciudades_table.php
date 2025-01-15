<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('ciudades', function (Blueprint $table) {
            $table->string('zona')->nullable(); // Columna zona (puede ser NULL)
            $table->integer('posicion')->nullable(); // Columna posicion (puede ser NULL)
            $table->unique(['zona', 'posicion']); // Restricción única: no puede haber la misma posición en la misma zona
        });
    }
    
    public function down()
    {
        Schema::table('ciudades', function (Blueprint $table) {
            $table->dropUnique(['zona', 'posicion']); // Eliminar la restricción única
            $table->dropColumn(['zona', 'posicion']); // Eliminar las columnas
        });
    }
    
};
