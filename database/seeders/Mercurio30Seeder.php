<?php

namespace Database\Seeders;

use App\Models\Mercurio30;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;

class Mercurio30Seeder extends Seeder
{
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();

        $rows = $legacy->select('SELECT * FROM mercurio30');

        $fillable = (new Mercurio30())->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            Mercurio30::updateOrCreate(
                ['id' => $row['id']],
                $data
            );
        }

        $legacy->disconnect();
    }
}
