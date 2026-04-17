<?php

namespace Database\Seeders;

use App\Models\Mercurio06;
use App\Models\Mercurio07;
use App\Models\Mercurio09;
use App\Models\Mercurio11;
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

        if (Mercurio06::count() == 0) {
            $this->call([
                Mercurio06Seeder::class,
            ]);
        }
        if (Mercurio07::count() == 0) {
            $this->call([
                Mercurio07Seeder::class,
            ]);
        }
        if (Mercurio11::count() == 0) {
            $this->call([
                Mercurio11Seeder::class,
            ]);
        }
        if (Mercurio09::count() == 0) {
            $this->call([
                Mercurio09Seeder::class,
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

            if (!is_numeric($data['coddoc'])) {
                continue;
            }
            if (!is_numeric($data['numero'])) {
                continue;
            }
            if ($data['tipopc'] == null || $data['tipopc'] == '') {
                continue;
            }
            //valida si el tipopc existe en mercurio09
            if (!Mercurio09::where('tipopc', $data['tipopc'])->exists()) {
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
