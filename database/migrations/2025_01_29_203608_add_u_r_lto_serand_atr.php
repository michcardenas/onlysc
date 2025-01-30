<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('servicios', function (Blueprint $table) {
            $table->string('url')->after('nombre');
        });

        Schema::table('atributos', function (Blueprint $table) {
            $table->string('url')->after('nombre');
        });
    }

    public function down()
    {
        Schema::table('servicios', function (Blueprint $table) {
            $table->dropColumn('url');
        });

        Schema::table('atributos', function (Blueprint $table) {
            $table->dropColumn('url');
        });
    }
};