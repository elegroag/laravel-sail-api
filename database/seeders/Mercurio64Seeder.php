<?php

namespace Database\Seeders;

use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;
use App\Models\Mercurio64;

class Mercurio64Seeder extends Seeder
{
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();

        $fillable = (new Mercurio64())->getFillable();

        $rows = $legacy->select('SELECT * FROM mercurio64');

        foreach ($rows as $row) {
            // Construir payload limitado a fillable/columns
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
            if(!is_numeric($data['tipo'])){
                continue;
            }

            Mercurio64::updateOrCreate([
                'numero' => $row['numero']
            ], $data);
        }

        $legacy->disconnect();
    }
}
