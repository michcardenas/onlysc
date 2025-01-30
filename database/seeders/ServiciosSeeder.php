<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Servicio;

class ServiciosSeeder extends Seeder
{
    public function run()
    {
        $servicios = [
            "Anal",
            "Atencion a domicilio",
            "Atencion en hoteles",
            "Baile erotico",
            "Besos",
            "Cambio de rol",
            "Departamento propio",
            "Disfraces",
            "Ducha erotica",
            "Eventos y cenas",
            "Eyaculacion cuerpo",
            "Eyaculacion facial",
            "Hetero",
            "Juguetes",
            "Lesbico",
            "Lluvia dorada",
            "Masaje erotico",
            "Masaje prostatico",
            "Masaje tantrico",
            "Masaje thai",
            "Masajes con final feliz",
            "Masajes desnudos",
            "Masajes eroticos",
            "Masajes para hombres",
            "Masajes sensitivos",
            "Masajes sexuales",
            "Masturbacion rusa",
            "Oral americana",
            "Oral con preservativo",
            "Oral sin preservativo",
            "Orgias",
            "Parejas",
            "Trio"
        ];

        foreach ($servicios as $index => $servicio) {
            Servicio::create([
                'nombre' => $servicio,
                'posicion' => $index + 1
            ]);
        }
    }
}