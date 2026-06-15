<?php

namespace Database\Seeders;

use App\Models\Mercurio05;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;

class Mercurio05Seeder extends Seeder
{
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();
        $rows = $legacy->select('SELECT * FROM mercurio05');

        $fillable = (new Mercurio05())->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            Mercurio05::updateOrCreate(
                [
                    'codofi' => $row['codofi'],
                    'codciu' => $row['codciu'],
                ],
                $data
            );
        }

        $legacy->disconnect();
    }
}
