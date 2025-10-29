<?php

namespace Database\Seeders;

use App\Models\Mercurio06;
use App\Models\Mercurio37;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;

class Mercurio37Seeder extends Seeder
{
    /**
     * Ejecuta las semillas de la base de datos.
     */
    public function run(): void
    {

       if(Mercurio06::count() == 0){
            $this->call([
                Mercurio06Seeder::class,
                Mercurio07Seeder::class,
                Mercurio11Seeder::class,
            ]);
        }


        $legacy = new LegacyDatabaseService();

        // Leer registros desde la base legada
        $rows = $legacy->select('SELECT * FROM mercurio37');

        // Campos permitidos del modelo
        $fillable = (new Mercurio37())->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            if(!is_numeric($data['coddoc'])){
                continue;
            }
            if(!is_numeric($data['documento'])){
                continue;
            }
            if($data['documento'] < 5) continue;

            if($data['tipo'] != null || $data['tipo'] != ''){
                continue;
            }

            // Usar la clave compuesta definida en el modelo
            Mercurio37::updateOrCreate(
                [
                    'tipopc' => $row['tipopc'],
                    'numero' => $row['numero'],
                    'coddoc' => $row['coddoc']
                ],
                $data
            );
        }

        $legacy->disconnect();
    }
}
