<?php

namespace Database\Seeders;

use App\Models\Mercurio07;
use App\Models\Mercurio36;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class Mercurio36Seeder extends Seeder
{
    /**
     * Ejecuta las semillas de la base de datos.
     */
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();

        $rows = $legacy->select('SELECT * FROM mercurio36');

        $fillable = (new Mercurio36())->getFillable();

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

            // Validar FK: el registro padre debe existir en mercurio07
            $existsInMercurio07 = Mercurio07::where('tipo', $data['tipo'])
                ->where('coddoc', $data['coddoc'])
                ->where('documento', $data['documento'])
                ->exists();

            if (!$existsInMercurio07) {
                continue;
            }

            $model = Mercurio36::updateOrCreate(
                ['id' => $row['id']],
                $data
            );
            $model->regenerateUuid();
            $model->save();
        }

        $legacy->disconnect();
    }
}
