<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('escort_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_publicate_id')
                  ->constrained('usuarios_publicate')
                  ->onDelete('cascade');
            $table->string('direccion');
            $table->string('ciudad');
            $table->string('region')->nullable();
            $table->decimal('latitud', 10, 8);
            $table->decimal('longitud', 11, 8);
            $table->string('referencia')->nullable();
            $table->boolean('is_approximate')->default(true);
            $table->timestamps();

            // Índice para búsquedas geoespaciales
            $table->index(['latitud', 'longitud']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('escort_locations');
    }
};