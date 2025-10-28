<?php

namespace Database\Seeders;

use App\Models\Mercurio54;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;

class Mercurio54Seeder extends Seeder
{
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();

        // Leer registros desde la base legada
        $rows = $legacy->select('SELECT * FROM mercurio54');

        // Campos permitidos del modelo
        $fillable = (new Mercurio54())->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            Mercurio54::updateOrCreate(
                [
                    'tipo' => $row['tipo'], 
                    'coddoc' => $row['coddoc'], 
                    'documento' => $row['documento']
                ],
                $data
            );
        }

        $legacy->disconnect();
    }   
}
