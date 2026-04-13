<?php

namespace Database\Seeders;

use App\Models\Mercurio06;
use App\Models\Mercurio07;
use App\Models\Mercurio11;
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

            // Verificar que el registro exista en mercurio07 antes de insertar
            $existsInMercurio07 = Mercurio07::where('tipo', $data['tipo'])
                ->where('coddoc', $data['coddoc'])
                ->where('documento', $data['documento'])
                ->exists();

            if (!$existsInMercurio07) {
                // Si no existe en mercurio07, omitir este registro
                continue;
            }

            // Crear o actualizar el registro
            $model = Mercurio30::updateOrCreate(
                ['id' => $row['id']],
                $data
            );

            // Generar el ruuid usando el trait HasCustomUuid
            $model->regenerateUuid();
            $model->save();
        }

        $legacy->disconnect();
    }
}
