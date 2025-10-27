<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Services\LegacyDatabaseService;
use App\Models\Mercurio16;

class Mercurio16Seeder extends Seeder
{
    public function run(): void
    {
        // Conexión a la base de datos legada (puedes mover estos datos a .env si lo prefieres)
        $legacyDb = new LegacyDatabaseService();
        // Obtener datos de la base legada

        $legacyModel = $legacyDb->select('SELECT * FROM mercurio16');

        // Insertar en la nueva base usando Eloquent (solo escritura en la base actual de Laravel)
        foreach ($legacyModel as $model) {
            Mercurio16::updateOrCreate(
                [
                    'id' => $model['id'],
                ],
                [
                    'documento' => $model['documento'],
                    'fecha' => $model['fecha'],
                    'firma' => $model['firma'],
                    'coddoc' => $model['coddoc'],
                    'keyprivate' => $model['keyprivate'],
                    'keypublic' => $model['keypublic'],
                    'password' => $model['password'],
                ]
            );
        }
        $legacyDb->disconnect();
    }
}
