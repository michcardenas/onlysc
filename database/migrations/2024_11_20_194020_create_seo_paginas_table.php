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
        Schema::create('seo_paginas', function (Blueprint $table) {
            $table->id(); // ID único
            $table->string('nombre'); // Nombre de la página
            $table->string('h1')->nullable(); // Etiqueta H1
            $table->string('h2')->nullable(); // Etiqueta H2
            $table->string('h3')->nullable(); // Etiqueta H3
            $table->text('meta_title')->nullable(); // Título meta
            $table->text('meta_description')->nullable(); // Descripción meta
            $table->text('meta_keywords')->nullable(); // Palabras clave meta
            $table->timestamps(); // Campos created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seo_paginas');
    }
};
