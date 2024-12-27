<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSeoTemplatesTable extends Migration
{
    public function up()
    {
        Schema::create('seo_templates', function (Blueprint $table) {
            $table->id();
            $table->string('tipo')->default('filtros');
            $table->text('description_template');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('seo_templates');
    }
}
