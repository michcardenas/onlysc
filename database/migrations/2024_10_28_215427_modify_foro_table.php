<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyForoTable extends Migration
{
    public function up()
    {
        Schema::table('foro', function (Blueprint $table) {
            // Eliminar los timestamps
            $table->dropTimestamps();

            // Asegurarte de que 'fecha' sea un timestamp
            $table->timestamp('fecha')->useCurrent()->change(); // Si 'fecha' ya existe
        });
    }

    public function down()
    {
        Schema::table('foro', function (Blueprint $table) {
            // Volver a agregar los timestamps
            $table->timestamps();
            
            // Opcional: Si deseas revertir la columna 'fecha' a su estado anterior
            // Puedes volver a definirla como quieras, aquí solo la dejo como timestamp
            $table->timestamp('fecha')->nullable()->change(); // O el estado que quieras
        });
    }
}
