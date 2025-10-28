<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Services\LegacyDatabaseService;
use App\Models\Mercusat02;

class Mercusat02Seeder extends Seeder
{
    /**
     * Ejecuta las semillas de la base de datos.
     */
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();

        // Leer registros desde la base legada
        $rows = $legacy->select('SELECT * FROM mercusat02');

        // Campos permitidos del modelo
        $fillable = (new Mercusat02())->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            Mercusat02::updateOrCreate(
                ['id' => $row['id']],
                $data
            );
        }

        $legacy->disconnect();
    }
}
