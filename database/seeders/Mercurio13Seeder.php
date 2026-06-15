<?php

namespace Database\Seeders;

use App\Models\Mercurio13;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;

class Mercurio13Seeder extends Seeder
{
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();
        $rows = $legacy->select('SELECT * FROM mercurio13');

        $fillable = (new Mercurio13())->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            Mercurio13::updateOrCreate(
                [
                    'tipopc' => $row['tipopc'],
                    'coddoc' => $row['coddoc'],
                ],
                $data
            );
        }

        $legacy->disconnect();
    }
}
