Copy<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up()
   {
       Schema::table('posts', function (Blueprint $table) {
           $table->unsignedBigInteger('chica_id')->nullable()->after('id_usuario');
           $table->foreign('chica_id')->references('id')->on('usuarios_publicate')->onDelete('set null');
       });
   }

   public function down()
   {
       Schema::table('posts', function (Blueprint $table) {
           $table->dropForeign(['chica_id']);
           $table->dropColumn('chica_id');
       });
   }
};