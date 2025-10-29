<?php

namespace Database\Seeders;

use App\Models\Mercurio10;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;

class Mercurio10Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();  

        // Leer registros desde la base legada
        $rows = $legacy->select('SELECT * FROM mercurio10 limit 1000');

        // Campos permitidos del modelo
        $fillable = (new Mercurio10())->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            Mercurio10::updateOrCreate(
                [
                    'tipopc' => $row['tipopc'], 
                    'numero' => $row['numero'], 
                    'item' => $row['item']
                ],
                $data
            );
        }

        $legacy->disconnect();
    }
}
