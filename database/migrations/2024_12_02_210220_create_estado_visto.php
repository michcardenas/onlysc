<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEstadoVisto extends Migration
{
    public function up()
    {
        Schema::create('estado_visto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estado_id')->constrained('estados')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('visto_at');
            $table->timestamps();
            
            $table->unique(['estado_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('estado_visto');
    }
}