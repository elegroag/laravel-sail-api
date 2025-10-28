<?php

namespace Database\Seeders;

use App\Models\Mercurio07;
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

        $fillable = (new Mercurio19())->getFillable();

        // Insertar en la nueva base usando Eloquent (solo escritura en la base actual de Laravel)
        foreach ($legacyModel as $model) {
             $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $model[$field] ?? null;
            }

            if($data['intentos'] == null) $data['intentos'] = 0;
            if($data['documento'] < 5) continue;
            if(!is_numeric($data['coddoc'])){
                continue;
            }
            if(!is_numeric($data['documento'])){
                continue;
            }
            if(!is_numeric($data['tipo'])){
                continue;
            }
            //existe en mercurio7
            $mercurio7 = Mercurio07::where('documento', $model['documento'])->where('coddoc', $model['coddoc'])->where('tipo', $model['tipo']);
            if($mercurio7->exists() == false){
                continue;
            }

            Mercurio19::updateOrCreate(
                [
                    'documento' => $model['documento'],
                    'coddoc' => $model['coddoc'],
                    'tipo' => $model['tipo'],
                ],
                $data
            );
        }
        $legacyDb->disconnect();
    }
}
