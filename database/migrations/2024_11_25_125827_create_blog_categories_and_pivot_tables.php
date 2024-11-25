<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Crear tabla de categorías
        Schema::create('blog_categories', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('slug')->unique();
            $table->string('descripcion')->nullable();
            $table->timestamps();
        });

        // Crear tabla pivote
        Schema::create('blog_article_category', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blog_article_id')->constrained()->onDelete('cascade');
            $table->foreignId('blog_category_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('blog_article_category');
        Schema::dropIfExists('blog_categories');
    }
};