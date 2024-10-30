<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateForoTable extends Migration
{
    public function up()
    {
        Schema::create('foro', function (Blueprint $table) {
            $table->id(); // Campo id
            $table->string('titulo'); // Campo titulo
            $table->string('subtitulo'); // Campo subtitulo
            $table->text('contenido'); // Campo contenido
            $table->string('foto')->nullable(); // Campo foto
            $table->foreignId('id_usuario')->constrained('users'); // Campo id_usuario, asumiendo que tienes una tabla users
            $table->timestamp('fecha')->useCurrent(); // Campo fecha
        });
    }

    public function down()
    {
        Schema::dropIfExists('foro');
    }
}

