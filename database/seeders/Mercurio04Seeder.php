<?php

namespace Database\Seeders;

use App\Models\Mercurio04;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;

class Mercurio04Seeder extends Seeder
{
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();
        $rows = $legacy->select('SELECT * FROM mercurio04');

        $fillable = (new Mercurio04())->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            Mercurio04::updateOrCreate(
                ['codofi' => $row['codofi']],
                $data
            );
        }

        $legacy->disconnect();
    }
}
