<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBlogIdToForoTable extends Migration
{
    public function up()
    {
        Schema::table('foro', function (Blueprint $table) {
            $table->unsignedBigInteger('id_blog')->nullable(); // Agregar columna id_blog
        });
    }

    public function down()
    {
        Schema::table('foro', function (Blueprint $table) {
            $table->dropColumn('id_blog'); // Eliminar la columna si es necesario
        });
    }
}
