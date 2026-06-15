<?php

namespace Database\Seeders;

use App\Models\Mercurio14;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;

class Mercurio14Seeder extends Seeder
{
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();
        $rows = $legacy->select('SELECT * FROM mercurio14');

        $fillable = (new Mercurio14())->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            Mercurio14::updateOrCreate(
                [
                    'tipopc' => $row['tipopc'],
                    'tipsoc' => $row['tipsoc'],
                    'coddoc' => $row['coddoc'],
                ],
                $data
            );
        }

        $legacy->disconnect();
    }
}
