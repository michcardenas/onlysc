<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDescripcionFotosToUsuariosPublicateTable extends Migration
{
    public function up()
    {
        Schema::table('usuarios_publicate', function (Blueprint $table) {
            $table->json('descripcion_fotos')->nullable()->after('fotos');
        });
    }

    public function down()
    {
        Schema::table('usuarios_publicate', function (Blueprint $table) {
            $table->dropColumn('descripcion_fotos');
        });
    }
}
