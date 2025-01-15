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
            $table->text('heading_h2_secondary')->nullable()->after('heading_h2');
            $table->text('additional_text_more')->nullable()->after('additional_text');
        });
    }

    public function down()
    {
        Schema::table('meta_tags', function (Blueprint $table) {
            $table->dropColumn('heading_h2_secondary');
            $table->dropColumn('additional_text_more');
        });
    }
};
