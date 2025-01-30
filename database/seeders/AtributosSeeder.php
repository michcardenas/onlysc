<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Atributo;

class AtributosSeeder extends Seeder
{
    public function run()
    {
        $atributos = [
            "Busto grande",
            "Busto mediano",
            "Busto pequeño",
            "Cara visible",
            "Cola grande",
            "Cola mediana",
            "Cola pequeña",
            "Con video",
            "Contextura delgada",
            "Contextura grande",
            "Contextura mediana",
            "Depilacion full",
            "Depto propio",
            "En promocion",
            "English",
            "Escort independiente",
            "Español",
            "Estatura alta",
            "Estatura mediana",
            "Estatura pequeña",
            "Hentai",
            "Morena",
            "Mulata",
            "No fuma",
            "Ojos claros",
            "Ojos oscuros",
            "Peliroja",
            "Portugues",
            "Relato erotico",
            "Rubia",
            "Tatuajes",
            "Trigueña"
        ];

        foreach ($atributos as $index => $atributo) {
            Atributo::create([
                'nombre' => $atributo,
                'posicion' => $index + 1,
                'url' => strtolower(str_replace(' ', '-', $atributo)) // Generar URL automática
            ]);
        }
    }
}