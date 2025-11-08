<?php

namespace Database\Seeders;

use App\Models\Mercurio07;
use Illuminate\Database\Seeder;
use App\Services\LegacyDatabaseService;
use App\Models\Mercurio20;

class Mercurio20Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();

        // Leer registros desde la base legada
        $rows = $legacy->select('SELECT * FROM mercurio20 limit 10000');

        // Campos permitidos del modelo
        $fillable = (new Mercurio20())->getFillable();

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

            if (!is_numeric($data['tipo'])) {
                continue;
            }

            if (
                Mercurio07::where('tipo', $data['tipo'])
                ->where('coddoc', $data['coddoc'])
                ->where('documento', $data['documento'])
                ->exists() === false
            ) {
                continue;
            }

            Mercurio20::updateOrCreate(
                ['log' => $row['log']],
                $data
            );
        }

        $legacy->disconnect();
    }
}
