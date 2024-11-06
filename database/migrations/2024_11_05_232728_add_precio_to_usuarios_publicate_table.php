<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPrecioToUsuariosPublicateTable extends Migration
{
    /**
     * Run the migration.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('usuarios_publicate', function (Blueprint $table) {
            $table->decimal('precio', 10, 2)->nullable(); // 10 dígitos en total, 2 decimales
        });
    }

    /**
     * Reverse the migration.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('usuarios_publicate', function (Blueprint $table) {
            $table->dropColumn('precio');
        });
    }
}