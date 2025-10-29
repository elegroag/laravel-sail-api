<?php

namespace Database\Seeders;

use App\Models\Xml4b091;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;

class Xml4b091Seeder extends Seeder
{
    /**
     * Ejecuta las semillas de la base de datos.
     */
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();

        // Leer registros desde la base legada
        $rows = $legacy->select('SELECT * FROM xml4b091');

        // Campos permitidos del modelo
        $fillable = (new Xml4b091())->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            Xml4b091::updateOrCreate(
                ['codpai' => $row['codpai']], // Ajustar campo clave según modelo
                $data
            );
        }

        $legacy->disconnect();
    }
}
