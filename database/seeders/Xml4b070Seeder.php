<?php

namespace Database\Seeders;

use App\Models\Xml4b070;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;

class Xml4b070Seeder extends Seeder
{
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();

        $rows = $legacy->select('SELECT * FROM xml4b070 LIMIT 1000');

        $fillable = (new Xml4b070())->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            Xml4b070::updateOrCreate(
                ['tipjor' => $row['tipjor']],
                $data
            );
        }

        $legacy->disconnect();
    }
}
