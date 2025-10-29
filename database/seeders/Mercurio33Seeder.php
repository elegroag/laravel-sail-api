<?php

namespace Database\Seeders;

use App\Models\Mercurio33;
use App\Models\Mercurio47;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;

class Mercurio33Seeder extends Seeder
{
    /**
     * Ejecuta las semillas de la base de datos.
     */
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();

        // Leer registros desde la base legada
        $rows = $legacy->select('SELECT * FROM mercurio33');

        // Campos permitidos del modelo
        $fillable = (new Mercurio33())->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            if($data['documento'] < 5) continue;
            if(!is_numeric($data['coddoc'])){
                continue;
            }
            if(!is_numeric($data['documento'])){
                continue;
            }
            if($data['tipo'] != null || $data['tipo'] != ''){
                continue;
            }

            if(Mercurio47::where('id', $row['actualizacion'])->exists() == false){
                continue;
            }
            // Clave compuesta
            Mercurio33::updateOrCreate(
                [
                    'id' => $row['id']
                ],
                $data
            );
        }

        $legacy->disconnect();
    }
}
