<?php

namespace Database\Seeders;

use App\Models\Mercurio28;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;

class Mercurio28Seeder extends Seeder
{
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();

        $rows = $legacy->select('SELECT * FROM mercurio28 LIMIT 1000');

        $fillable = (new Mercurio28())->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            Mercurio28::updateOrCreate(
                [
                    'tipo' => $row['tipo'],
                    'campo' => $row['campo'],
                ],
                $data
            );
        }

        $legacy->disconnect();
    }
}
