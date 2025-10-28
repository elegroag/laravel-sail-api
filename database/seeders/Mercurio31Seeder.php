<?php

namespace Database\Seeders;

use App\Models\Mercurio31;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;

class Mercurio31Seeder extends Seeder
{
    /**
     * Ejecuta las semillas de la base de datos.
     */
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();

        // Leer registros desde la base legada
        $rows = $legacy->select('SELECT * FROM mercurio31');

        // Campos permitidos del modelo
        $fillable = (new Mercurio31())->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            if($data['documento'] < 5) continue;
            if(!is_numeric($data['coddoc'])){
                continue;
            }
            if(!is_numeric($data['documento'])){
                continue;
            }

            Mercurio31::updateOrCreate(
                ['id' => $row['id']],
                $data
            );
        }

        $legacy->disconnect();
    }
}
