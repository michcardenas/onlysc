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
            $table->text('texto_zonas_centro')->nullable()->after('texto_zonas');
            $table->text('texto_zonas_sur')->nullable()->after('texto_zonas_centro');
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
            $table->dropColumn(['texto_zonas_centro', 'texto_zonas_sur']);
        });
    }
};
