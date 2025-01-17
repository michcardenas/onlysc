<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('meta_tags', function (Blueprint $table) {
            $table->text('texto_zonas')->nullable()->after('fondo');
            $table->string('titulo_tarjetas', 255)->nullable()->after('texto_zonas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('meta_tags', function (Blueprint $table) {
            $table->dropColumn('texto_zonas');
            $table->dropColumn('titulo_tarjetas');
        });
    }
};
