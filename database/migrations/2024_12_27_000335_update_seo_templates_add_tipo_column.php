<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateSeoTemplatesAddTipoColumn extends Migration
{
    public function up()
    {
        Schema::table('seo_templates', function (Blueprint $table) {
            $table->string('tipo')->change(); // Cambiamos el default de 'filtros' y lo hacemos nullable
        });

        // Actualizar los registros existentes
        DB::table('seo_templates')->where('tipo', 'filtros')->update(['tipo' => 'single']);
    }

    public function down()
    {
        Schema::table('seo_templates', function (Blueprint $table) {
            $table->string('tipo')->default('filtros')->change();
        });

        // Revertir los registros
        DB::table('seo_templates')->whereIn('tipo', ['single', 'multiple', 'complex'])->update(['tipo' => 'filtros']);
    }
}