<?php

namespace Database\Seeders;

use App\Models\Mercurio12;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;

class Mercurio12Seeder extends Seeder
{
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();
        $rows = $legacy->select('SELECT * FROM mercurio12');

        $fillable = (new Mercurio12())->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            Mercurio12::updateOrCreate(
                ['coddoc' => $row['coddoc']],
                $data
            );
        }

        $legacy->disconnect();
    }
}
