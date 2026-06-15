<?php

namespace Database\Seeders;

use App\Models\Xml4b005;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;

class Xml4b005Seeder extends Seeder
{
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();

        $rows = $legacy->select('SELECT * FROM xml4b005 LIMIT 1000');

        $fillable = (new Xml4b005())->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            Xml4b005::updateOrCreate(
                ['tipgen' => $row['tipgen']],
                $data
            );
        }

        $legacy->disconnect();
    }
}
