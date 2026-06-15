<?php

namespace Database\Seeders;

use App\Models\Mercurio09;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;

class Mercurio09Seeder extends Seeder
{
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();
        $rows = $legacy->select('SELECT * FROM mercurio09');

        $fillable = (new Mercurio09())->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            Mercurio09::updateOrCreate(
                ['tipopc' => $row['tipopc']],
                $data
            );
        }

        $legacy->disconnect();
    }
}
