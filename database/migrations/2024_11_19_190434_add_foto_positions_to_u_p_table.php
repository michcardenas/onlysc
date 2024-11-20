<?php

// En tu migración (create_foto_positions_table.php)
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFotoPositionsToUPTable extends Migration
{
    public function up()
    {
        Schema::table('usuarios_publicate', function (Blueprint $table) {
            $table->json('foto_positions')->nullable()->after('fotos');
        });
    }

    public function down()
    {
        Schema::table('usuarios_publicate', function (Blueprint $table) {
            $table->dropColumn('foto_positions');
        });
    }
}