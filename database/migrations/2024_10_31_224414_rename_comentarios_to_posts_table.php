<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameComentariosToPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Desactivar restricciones de clave foránea temporalmente
        Schema::disableForeignKeyConstraints();

        // Renombrar la tabla
        Schema::rename('comentarios', 'posts');

        // Reactivar restricciones de clave foránea
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::rename('posts', 'comentarios');
        Schema::enableForeignKeyConstraints();
    }
}
