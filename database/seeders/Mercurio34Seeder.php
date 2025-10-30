<?php

namespace Database\Seeders;

use App\Models\Mercurio06;
use App\Models\Mercurio34;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class Mercurio34Seeder extends Seeder
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
        $rows = $legacy->select('SELECT * FROM mercurio34');

        // Campos permitidos del modelo
        $fillable = (new Mercurio34())->getFillable();

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

            unset($data['celular']);

            $data['ruuid'] = (string) Str::orderedUuid();

            Mercurio34::updateOrCreate(
                ['id' => $row['id']],
                $data
            );
        }

        $legacy->disconnect();
    }
}
