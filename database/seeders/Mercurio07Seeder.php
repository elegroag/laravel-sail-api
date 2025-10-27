<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Services\LegacyDatabaseService;
use App\Models\Mercurio07;

class Mercurio07Seeder extends Seeder
{
    public function run(): void
    {
        // Conexión a la base de datos legada (puedes mover estos datos a .env si lo prefieres)
        $legacyDb = new LegacyDatabaseService();
        // Obtener datos de la base legada

        $legacyModel = $legacyDb->select('SELECT * FROM mercurio07');

        // Insertar en la nueva base usando Eloquent (solo escritura en la base actual de Laravel)
        foreach ($legacyModel as $model) {
            Mercurio07::updateOrCreate(
                [
                    'id' => $model['id'],
                ],
                [
                    'tipo' => $model['tipo'],
                    'coddoc' => $model['coddoc'],
                    'documento' => $model['documento'],
                    'nombre' => $model['nombre'],
                    'email' => $model['email'],
                    'clave' => $model['clave'],
                    'feccla' => $model['feccla'],
                    'autoriza' => $model['autoriza'],
                    'codciu' => $model['codciu'],
                    'fecreg' => $model['fecreg'],
                    'estado' => $model['estado'],
                    'whatsapp' => $model['whatsapp'],
                    'fecha_syncron' => $model['fecha_syncron'],
                ]
            );
        }
        $legacyDb->disconnect();
    }
}
