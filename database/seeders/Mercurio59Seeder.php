<?php

namespace Database\Seeders;

use App\Models\Mercurio59;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;

class Mercurio59Seeder extends Seeder
{
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();
        $rows = $legacy->select('SELECT * FROM mercurio59');

        $fillable = (new Mercurio59())->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            Mercurio59::updateOrCreate(
                ['codinf' => $row['codinf']],
                $data
            );
        }

        $legacy->disconnect();
    }
}
