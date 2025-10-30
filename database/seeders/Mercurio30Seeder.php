<?php

namespace Database\Seeders;

use App\Models\Mercurio06;
use App\Models\Mercurio30;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class Mercurio30Seeder extends Seeder
{
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

        $rows = $legacy->select('SELECT * FROM mercurio30');

        $fillable = (new Mercurio30())->getFillable();

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
            $data['sat_fecapr'] = $row['fecha_aprobacion_sat'];
            $data['sat_cedrep'] = $row['documento_representante_sat'];
            $data['sat_numtra'] = $row['numero_transaccion'];

            unset($data['fecha_aprobacion_sat']);
            unset($data['documento_representante_sat']);
            unset($data['numero_transaccion']);

            $data['ruuid'] = (string) Str::orderedUuid();

            Mercurio30::updateOrCreate(
                ['id' => $row['id']],
                $data
            );
        }

        $legacy->disconnect();
    }
}
