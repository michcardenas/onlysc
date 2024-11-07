<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('disponibilidad', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('publicate_id');
            $table->enum('dia', ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo']);
            $table->time('hora_desde');
            $table->time('hora_hasta');
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->timestamps();

            // Referencia correcta a la tabla usuarios_publicate
            $table->foreign('publicate_id')
                  ->references('id')
                  ->on('usuarios_publicate')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disponibilidad');
    }
};