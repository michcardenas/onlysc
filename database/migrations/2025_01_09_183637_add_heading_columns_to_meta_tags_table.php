<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('meta_tags', function (Blueprint $table) {
            $table->string('heading_h1')->nullable();
            $table->string('heading_h2')->nullable();
            $table->text('additional_text')->nullable();
        });
    }

    public function down()
    {
        Schema::table('meta_tags', function (Blueprint $table) {
            $table->dropColumn('heading_h1');
            $table->dropColumn('heading_h2');
            $table->dropColumn('additional_text');
        });
    }
};