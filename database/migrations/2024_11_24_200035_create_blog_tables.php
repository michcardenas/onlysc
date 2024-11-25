<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Artículos del blog
        Schema::create('blog_articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->string('titulo');
            $table->string('slug')->unique();
            $table->text('extracto')->nullable();
            $table->longText('contenido');
            $table->string('imagen')->nullable();
            $table->boolean('destacado')->default(false);
            $table->enum('estado', ['borrador', 'publicado', 'privado'])->default('borrador');
            $table->datetime('fecha_publicacion')->nullable();
            $table->integer('visitas')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        // Tags
        Schema::create('blog_tags', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        // Tabla pivote para artículos y tags
        Schema::create('blog_article_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained('blog_articles')->onDelete('cascade');
            $table->foreignId('tag_id')->constrained('blog_tags')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('blog_article_tag');
        Schema::dropIfExists('blog_tags');
        Schema::dropIfExists('blog_articles');
    }
};