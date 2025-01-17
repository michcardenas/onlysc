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
        Schema::table('usuarios_publicate', function (Blueprint $table) {
            $table->json('blocked_images')->nullable()->after('fotos');
        });
    }
    
    public function down()
    {
        Schema::table('usuarios_publicate', function (Blueprint $table) {
            $table->dropColumn('blocked_images');
        });
    }
};
