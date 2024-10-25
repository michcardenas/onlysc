<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsuariosPublicateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usuarios_publicate', function (Blueprint $table) {
            $table->id();
            $table->string('fantasia');
            $table->string('email');
            $table->string('nombre');
            $table->string('password');
            $table->string('telefono')->nullable();
            $table->string('ubicacion');
            $table->integer('edad');
            $table->string('color_ojos')->nullable();
            $table->decimal('altura', 5, 2)->nullable();
            $table->decimal('peso', 5, 2)->nullable();
            $table->text('disponibilidad')->nullable();
            $table->json('servicios')->nullable();
            $table->json('servicios_adicionales')->nullable();
            $table->json('fotos')->nullable();
            $table->text('cuentanos')->nullable();
            $table->boolean('verificada')->default(false); // Columna 'verificada'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('usuarios_publicate');
    }
}
