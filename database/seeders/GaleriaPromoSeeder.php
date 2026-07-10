<?php

namespace Database\Seeders;

use App\Models\Mercurio26;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class GaleriaPromoSeeder extends Seeder
{
    /**
     * Siembra la galería principal (mercurio26) con imágenes promocionales generadas.
     * Ejecutar manualmente: php artisan db:seed --class=GaleriaPromoSeeder
     */
    public function run(): void
    {
        $items = [
            ['numero' => 1, 'archivo' => 'promo_1.jpg', 'nota' => 'ADULTO MAYOR - CLUB ÉPOCA DORADA'],
            ['numero' => 2, 'archivo' => 'promo_2.jpg', 'nota' => 'GIMNASIO'],
            ['numero' => 3, 'archivo' => 'promo_3.jpg', 'nota' => 'ESCUELA DE INGLES CONVERSACIONAL'],
            ['numero' => 4, 'archivo' => 'promo_4.jpg', 'nota' => 'ESCUELA DE FORMACION DANZAS BASICA'],
            ['numero' => 5, 'archivo' => 'promo_5.jpg', 'nota' => 'ESCUELA DE FORMACION EN ARTES PLASTICAS'],
            ['numero' => 6, 'archivo' => 'promo_6.jpg', 'nota' => 'ESCUELA DE FORMACION MUSICA ESPECIALIZADA'],
            ['numero' => 7, 'archivo' => 'promo_7.jpg', 'nota' => 'CURSOS DE COCINA'],
            ['numero' => 8, 'archivo' => 'promo_8.jpg', 'nota' => 'CENTRO RECREACIONAL UIS'],
            ['numero' => 9, 'archivo' => 'promo_9.jpg', 'nota' => 'SALUD Y NUTRICION'],
            ['numero' => 10, 'archivo' => 'promo_10.jpg', 'nota' => 'SUBSIDIO FORMACION DEPORTIVA'],
        ];

        Schema::disableForeignKeyConstraints();
        Mercurio26::truncate();
        Schema::enableForeignKeyConstraints();

        foreach ($items as $item) {
            Mercurio26::create([
                'numero' => $item['numero'],
                'archivo' => $item['archivo'],
                'nota' => $item['nota'],
                'estado' => 'A',
                'tipo' => 'F',
                'orden' => $item['numero'],
            ]);
        }
    }
}
