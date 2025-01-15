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
            $table->string('fondo')->nullable()->after('additional_text_more');
        });
    }

    public function down()
    {
        Schema::table('meta_tags', function (Blueprint $table) {
            $table->dropColumn('fondo');
        });
    }
};
