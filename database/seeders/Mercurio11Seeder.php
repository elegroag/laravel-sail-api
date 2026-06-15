<?php

namespace Database\Seeders;

use App\Models\Mercurio11;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;

class Mercurio11Seeder extends Seeder
{
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();
        $rows = $legacy->select('SELECT * FROM mercurio11 LIMIT 1000');

        $fillable = (new Mercurio11())->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            Mercurio11::updateOrCreate(
                ['codest' => $row['codest']],
                $data
            );
        }

        $legacy->disconnect();
    }
}
