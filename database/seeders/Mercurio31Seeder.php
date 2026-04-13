<?php

namespace Database\Seeders;

use App\Models\Mercurio06;
use App\Models\Mercurio07;
use App\Models\Mercurio11;
use App\Models\Mercurio31;
use App\Services\LegacyDatabaseService;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class Mercurio31Seeder extends Seeder
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

        $legacy = new LegacyDatabaseService();

        // Leer registros desde la base legada
        $rows = $legacy->select('SELECT * FROM mercurio31');

        // Campos permitidos del modelo
        $fillable = (new Mercurio31())->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            if ($data['documento'] < 5) continue;
            if (!is_numeric($data['coddoc'])) {
                continue;
            }
            if (!is_numeric($data['documento'])) {
                continue;
            }

            $existsInMercurio07 = Mercurio07::where('tipo', $data['tipo'])
                ->where('coddoc', $data['coddoc'])
                ->where('documento', $data['documento'])
                ->exists();

            if (!$existsInMercurio07) {
                // Si no existe en mercurio07, omitir este registro
                continue;
            }

            if ($data['tippag'] == null || $data['tippag'] == '') {
                $data['tippag'] = 'T';
            }

            if ($data['numcue'] == null || $data['numcue'] == '') {
                $data['numcue'] = 0;
            }

            if ($data['codban'] == null || $data['codban'] == '') {
                $data['codban'] = 0;
            }

            unset($data['zoneurbana']);

            $model = Mercurio31::updateOrCreate(
                ['id' => $row['id']],
                $data
            );
            $model->regenerateUuid();
            $model->save();
        }

        $legacy->disconnect();
    }
}
