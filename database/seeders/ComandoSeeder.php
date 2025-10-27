<?php

namespace Database\Seeders;

use App\Models\Comandos;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;

class ComandoSeeder extends Seeder
{
    public function run(): void
    {
        // Conexión a la base de datos legada (puedes mover estos datos a .env si lo prefieres)
        $legacyDb = new LegacyDatabaseService();
        // Obtener datos de la base legada

        $legacyModel = $legacyDb->select('SELECT * FROM comandos');

        // Insertar en la nueva base usando Eloquent (solo escritura en la base actual de Laravel)
        foreach ($legacyModel as $model) {
            Comandos::updateOrCreate(
                [
                    'id' => $model['id'],
                ],
                [
                    'fecha_runner' => $model['fecha_runner'],
                    'hora_runner' => $model['hora_runner'],
                    'usuario' => $model['usuario'],
                    'progreso' => $model['progreso'],
                    'estado' => $model['estado'],
                    'proceso' => $model['proceso'],
                    'linea_comando' => $model['linea_comando'],
                    'estructura' => $model['estructura'],
                    'parametros' => $model['parametros'],
                    'resultado' => $model['resultado'],
                ]
            );
        }
        $legacyDb->disconnect();
    }
}
