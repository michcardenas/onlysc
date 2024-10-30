<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Primero eliminamos las foreign keys existentes si hay
        Schema::table('comentarios', function (Blueprint $table) {
            $table->dropForeign(['id_blog']);
            $table->dropForeign(['id_usuario']);
        });

        // Ahora añadimos las nuevas foreign keys
        Schema::table('comentarios', function (Blueprint $table) {
            $table->foreign('id_blog')
                  ->references('id_blog')
                  ->on('foro')
                  ->onDelete('cascade');
            
            $table->foreign('id_usuario')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('comentarios', function (Blueprint $table) {
            $table->dropForeign(['id_blog']);
            $table->dropForeign(['id_usuario']);
        });
    }
};