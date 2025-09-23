<?php

namespace Database\Seeders;

use App\Models\Mercurio26;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class Mercurio26Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $archivosMultimedia = [
            [
                'numero' => 1,
                'archivo' => 'promo_1.jpg',
                'nota' => null,
                'estado' => 'A',
                'tipo' => 'F',
                'orden' => 1,
            ],
            [
                'numero' => 2,
                'archivo' => 'promo_2.jpg',
                'nota' => null,
                'estado' => 'A',
                'tipo' => 'F',
                'orden' => 2,
            ],
            [
                'numero' => 3,
                'archivo' => 'promo_3.jpg',
                'nota' => null,
                'estado' => 'A',
                'tipo' => 'F',
                'orden' => 3,
            ],
            [
                'numero' => 5,
                'archivo' => 'promo_5.jpg',
                'nota' => null,
                'estado' => 'A',
                'tipo' => 'F',
                'orden' => 5,
            ],
            [
                'numero' => 6,
                'archivo' => 'promo_6.jpg',
                'nota' => null,
                'estado' => 'A',
                'tipo' => 'F',
                'orden' => 6,
            ],
            [
                'numero' => 7,
                'archivo' => 'promo_7.jpg',
                'nota' => null,
                'estado' => 'A',
                'tipo' => 'F',
                'orden' => 7,
            ],
            [
                'numero' => 8,
                'archivo' => 'promo_8.mp4',
                'nota' => null,
                'estado' => 'A',
                'tipo' => 'V',
                'orden' => 8,
            ],
            [
                'numero' => 10,
                'archivo' => 'promo_10.mp4',
                'nota' => null,
                'estado' => 'A',
                'tipo' => 'V',
                'orden' => 10,
            ],
            [
                'numero' => 11,
                'archivo' => 'promo_11.mp4',
                'nota' => null,
                'estado' => 'A',
                'tipo' => 'V',
                'orden' => 11,
            ],
        ];

        foreach ($archivosMultimedia as $archivo) {
            Mercurio26::updateOrCreate(
                ['numero' => $archivo['numero']],
                $archivo
            );
        }
    }

    /* INSERT INTO `mercurio26` VALUES (1,'promo_1.jpg',1,'F'),(2,'promo_2.jpg',2,'F'),(3,'promo_3.jpg',3,'F'),(5,'promo_5.jpg',5,'F'),(6,'promo_6.jpg',6,'F'),(7,'promo_7.jpg',7,'F'),(8,'promo_8.mp4',8,'V'),(10,'promo_10.mp4',10,'V'),(11,'promo_11.mp4',11,'V'); */
}
