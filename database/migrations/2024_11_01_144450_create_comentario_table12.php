<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComentarioTable12 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comentario', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_blog');
            $table->unsignedBigInteger('id_usuario');
            $table->text('comentario');
            $table->timestamps();

            // Índices y claves foráneas
            $table->foreign('id_usuario')->references('id')->on('users')->onDelete('cascade');
            $table->index('id_blog', 'comentario_id_foro_foreign');
            $table->index('id_usuario', 'comentario_id_usuario_foreign');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comentario');
    }
}
