<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up()
   {
    Schema::table('meta_tags', function (Blueprint $table) {
        $table->string('tipo')->after('page');
     });
   }

   public function down()
   {
       Schema::dropIfExists('meta_tags');
   }
};
