<?php

namespace Database\Seeders;

use App\Models\Mercurio26;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;

class Mercurio26Seeder extends Seeder
{
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();
        $rows = $legacy->select('SELECT * FROM mercurio26 LIMIT 1000');

        $fillable = (new Mercurio26())->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            Mercurio26::updateOrCreate(
                ['numero' => $row['numero']],
                $data
            );
        }

        $legacy->disconnect();
    }
}
