<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdPostToComentarioTable extends Migration
{
    public function up()
    {
        Schema::table('comentario', function (Blueprint $table) {
            $table->unsignedBigInteger('id_post'); // Agrega el campo id_post

            // Define la clave foránea para id_post que hace referencia a posts.id
            $table->foreign('id_post')->references('id')->on('posts')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('comentario', function (Blueprint $table) {
            $table->dropForeign(['id_post']);
            $table->dropColumn('id_post');
        });
    }
}
