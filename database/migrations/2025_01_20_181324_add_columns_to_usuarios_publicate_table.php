<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToUsuariosPublicateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('usuarios_publicate', function (Blueprint $table) {
            $table->json('videos')->nullable(); // Usamos json para almacenar el array de videos
            $table->string('u1')->nullable();
            $table->string('u2')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('usuarios_publicate', function (Blueprint $table) {
            $table->dropColumn('videos');
            $table->dropColumn('u1');
            $table->dropColumn('u2');
        });
    }
}