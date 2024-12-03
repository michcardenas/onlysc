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
        Schema::table('ciudades', function (Blueprint $table) {
            $table->string('url', 255)->nullable(); // Cambia 'string' para especificar que es VARCHAR
        });
    }
    
    public function down()
    {
        Schema::table('ciudades', function (Blueprint $table) {
            $table->dropColumn('url');
        });
    }
    
    
};
