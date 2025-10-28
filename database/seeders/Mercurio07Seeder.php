<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Services\LegacyDatabaseService;
use App\Models\Mercurio07;

class Mercurio07Seeder extends Seeder
{
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();

        // Leer registros desde la base legada
        $rows = $legacy->select('SELECT * FROM mercurio07');

        // Campos permitidos del modelo
        $fillable = (new Mercurio07())->getFillable();

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
            Mercurio07::updateOrCreate(
                [
                    'tipo' => $row['tipo'],
                    'coddoc' => $row['coddoc'],
                    'documento' => $row['documento'],
                ],
                $data
            );
        }

        $legacy->disconnect();
    }
}
