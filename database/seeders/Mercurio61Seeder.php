<?php

namespace Database\Seeders;

use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;
use App\Models\Mercurio61;

class Mercurio61Seeder extends Seeder
{
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();

        $rows = $legacy->select('SELECT * FROM mercurio61');

        $fillable = (new Mercurio61())->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            Mercurio61::create(
                $data
            );
        }

        $legacy->disconnect();
    }
}
