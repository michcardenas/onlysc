<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComentariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comentarios', function (Blueprint $table) {
            $table->id(); // ID auto-incremental
            $table->foreignId('id_foro')->constrained('foro')->onDelete('cascade'); // Clave foránea que referencia a la tabla 'foro'
            $table->foreignId('id_usuario')->constrained('users')->onDelete('cascade'); // Clave foránea que referencia a la tabla 'users'
            $table->text('comentario'); // Comentario
            $table->timestamps(); // Created at y Updated at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comentarios');
    }
}
