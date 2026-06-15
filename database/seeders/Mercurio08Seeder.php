<?php

namespace Database\Seeders;

use App\Models\Mercurio08;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;

class Mercurio08Seeder extends Seeder
{
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();
        $rows = $legacy->select('SELECT * FROM mercurio08');

        $fillable = (new Mercurio08())->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            Mercurio08::updateOrCreate(
                [
                    'codofi' => $row['codofi'],
                    'tipopc' => $row['tipopc'],
                ],
                $data
            );
        }

        $legacy->disconnect();
    }
}
