<?php

namespace Database\Seeders;

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

        $this->call([
            Mercurio06Seeder::class,
            Mercurio07Seeder::class,
            Mercurio09Seeder::class,
        ]);


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
