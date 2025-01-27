<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('t_y_c', function (Blueprint $table) {
            $table->string('title')->after('id');
            $table->text('content')->after('title');
        });
    }

    public function down()
    {
        Schema::table('t_y_c', function (Blueprint $table) {
            $table->dropColumn(['title', 'content']);
        });
    }
};