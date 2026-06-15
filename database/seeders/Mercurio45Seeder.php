<?php

namespace Database\Seeders;

use App\Models\Mercurio07;
use App\Models\Mercurio11;
use App\Models\Mercurio45;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;

class Mercurio45Seeder extends Seeder
{
    /**
     * Ejecuta las semillas de la base de datos.
     */
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();

        $rows = $legacy->select('SELECT * FROM mercurio45');

        $fillable = (new Mercurio45())->getFillable();

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
                continue;
            }

            // Validar FK opcional: codest debe existir en mercurio11 si está presente
            if (!empty($data['codest']) && !Mercurio11::where('codest', $data['codest'])->exists()) {
                $data['codest'] = null;
            }

            $model = Mercurio45::updateOrCreate(
                ['id' => $row['id']],
                $data
            );
            $model->save();
        }

        $legacy->disconnect();
    }
}
