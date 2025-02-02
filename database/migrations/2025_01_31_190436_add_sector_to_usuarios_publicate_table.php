<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('usuarios_publicate', function (Blueprint $table) {
            $table->foreignId('sectores')
                  ->nullable()
                  ->constrained('sectores')
                  ->onDelete('set null')
                  ->onUpdate('cascade');
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
            $table->dropForeign(['sectores']);
            $table->dropColumn('sectores');
        });
    }
};