<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateIndexForPostsTable extends Migration
{
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropIndex('comentarios_id_foro_foreign'); // Eliminar índice
            $table->index('id_blog', 'posts_id_foro_foreign'); // Crear nuevo índice con el nombre actualizado
        });
    }

    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropIndex('posts_id_foro_foreign'); // Eliminar índice
            $table->index('id_blog', 'comentarios_id_foro_foreign'); // Restaurar el nombre anterior del índice
        });
    }
}
