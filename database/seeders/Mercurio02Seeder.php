<?php

namespace Database\Seeders;

use App\Models\Mercurio02;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;

class Mercurio02Seeder extends Seeder
{
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();
        $rows = $legacy->select('SELECT * FROM mercurio02');

        $fillable = (new Mercurio02())->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            Mercurio02::updateOrCreate(
                ['codcaj' => $row['codcaj']],
                $data
            );
        }

        $legacy->disconnect();
    }
}
