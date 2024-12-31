<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCiudadIdToSeoTemplates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('seo_templates', function (Blueprint $table) {
            // Agregar columna ciudad_id como clave foránea
            $table->unsignedBigInteger('ciudad_id')->nullable();
            
            // Agregar restricción de clave foránea
            $table->foreign('ciudad_id')
                  ->references('id')
                  ->on('ciudades')
                  ->onDelete('set null');  // Establece como null si la ciudad es eliminada

            // Opcional: Agregar índice para mejorar rendimiento de búsquedas
            $table->index('ciudad_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('seo_templates', function (Blueprint $table) {
            // Eliminar índice
            $table->dropIndex('seo_templates_ciudad_id_index');
            
            // Eliminar restricción de clave foránea
            $table->dropForeign('seo_templates_ciudad_id_foreign');
            
            // Eliminar columna
            $table->dropColumn('ciudad_id');
        });
    }
}