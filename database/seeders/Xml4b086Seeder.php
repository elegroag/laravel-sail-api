<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Xml4b086;

class Xml4b086Seeder extends Seeder
{
    /**
     * Ejecuta las semillas de la base de datos.
     */
    public function run(): void
    {
        $gruposEtnicos = [
            ['codgru' => 1, 'nombre' => 'AFROCOLOMBIANO'],
            ['codgru' => 2, 'nombre' => 'COMUNIDAD NEGRA'],
            ['codgru' => 3, 'nombre' => 'INDIGENA'],
            ['codgru' => 4, 'nombre' => 'PALANQUERO'],
            ['codgru' => 5, 'nombre' => 'RAIZAL DEL ARCHIPIELAGO DE SAN ANDRES, PROVIDENCIA Y SANTA CATALINA'],
            ['codgru' => 6, 'nombre' => 'ROOM/GITANO'],
            ['codgru' => 7, 'nombre' => 'NO SE AUTORECONOCE EN NINGUNO DE LOS ANTERIORES'],
            ['codgru' => 8, 'nombre' => 'NO DISPONIBLE'],
        ];

        foreach ($gruposEtnicos as $grupo) {
            Xml4b086::updateOrCreate(
                ['codgru' => $grupo['codgru']],
                $grupo
            );
        }
    }
}
