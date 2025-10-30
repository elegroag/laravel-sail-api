<?php

namespace Database\Seeders;

use App\Models\Mercurio06;
use App\Models\Mercurio32;
use App\Services\LegacyDatabaseService;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class Mercurio32Seeder extends Seeder
{
    /**
     * Ejecuta las semillas de la base de datos.
     */
    public function run(): void
    {
        if (Mercurio06::count() == 0) {
            $this->call([
                Mercurio06Seeder::class,
                Mercurio07Seeder::class,
                Mercurio11Seeder::class,
            ]);
        }

        $legacy = new LegacyDatabaseService();

        // Leer registros desde la base legada
        $rows = $legacy->select('SELECT * FROM mercurio32');

        // Campos permitidos del modelo
        $fillable = (new Mercurio32())->getFillable();

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

            unset($data['zoneurbana']);
            $data['tiecon'] = $row['tiecon'] ?? '1';

            if ($data['tippag'] == null || $data['tippag'] == '') {
                $data['tippag'] = 'T';
            }

            if ($data['numcue'] == null || $data['numcue'] == '') {
                $data['numcue'] = '0';
            }

            if ($data['codban'] == null || $data['codban'] == '') {
                $data['codban'] = '0';
            }

            if ($data['peretn'] == null || $data['peretn'] == '') {
                $data['peretn'] = '7';
            }

            $data['ruuid'] = (string) Str::orderedUuid();
            Mercurio32::updateOrCreate(
                ['id' => $row['id']],
                $data
            );
        }

        $legacy->disconnect();
    }
}
