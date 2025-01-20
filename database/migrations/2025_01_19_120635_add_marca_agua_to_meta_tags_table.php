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
            $table->string('marca_agua')->nullable()->after('fondo');
        });
    }

    public function down()
    {
        Schema::table('meta_tags', function (Blueprint $table) {
            $table->dropColumn('marca_agua');
        });
    }
};
