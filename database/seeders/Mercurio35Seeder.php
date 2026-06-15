<?php

namespace Database\Seeders;

use App\Models\Mercurio07;
use App\Models\Mercurio35;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;

class Mercurio35Seeder extends Seeder
{
    /**
     * Ejecuta las semillas de la base de datos.
     */
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();

        $rows = $legacy->select('SELECT * FROM mercurio35');

        $fillable = (new Mercurio35())->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            // Validar FK: el registro padre debe existir en mercurio07
            $existsInMercurio07 = Mercurio07::where('tipo', $data['tipo'])
                ->where('coddoc', $data['coddoc'])
                ->where('documento', $data['documento'])
                ->exists();

            if (!$existsInMercurio07) {
                // Si no existe el padre en mercurio07, omitir este registro
                continue;
            }

            $model = Mercurio35::updateOrCreate(
                ['id' => $row['id']],
                $data
            );
            $model->regenerateUuid();
            $model->save();
        }

        $legacy->disconnect();
    }
}
