<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ServiceUrlSeeder extends Seeder
{
    public function run()
    {
        $services = DB::table('servicios')->get();
        
        foreach($services as $service) {
            DB::table('servicios')
                ->where('id', $service->id)
                ->update([
                    'url' => Str::slug($service->nombre)
                ]);
        }
    }
}