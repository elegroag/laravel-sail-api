<?php

namespace Database\Seeders;

use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;
use App\Models\Mercurio19;

class Mercurio19Seeder extends Seeder
{
    public function run(): void
    {
        // Conexión a la base de datos legada (puedes mover estos datos a .env si lo prefieres)
        $legacyDb = new LegacyDatabaseService();
        // Obtener datos de la base legada

        $legacyModel = $legacyDb->select('SELECT * FROM mercurio19');

        // Insertar en la nueva base usando Eloquent (solo escritura en la base actual de Laravel)
        foreach ($legacyModel as $model) {
            Mercurio19::updateOrCreate(
                [
                    'documento' => $model['documento'],
                    'coddoc' => $model['coddoc'],
                    'tipo' => $model['tipo'],
                ],
                [
                    'codigo' => $model['codigo'],
                    'codver' => $model['codver'],
                    'respuesta' => $model['respuesta'],
                    'inicio' => $model['inicio'],
                    'intentos' => $model['intentos'],
                    'token' => $model['token'],
                ]
            );
        }
        $legacyDb->disconnect();
    }
}
