<?php

namespace Database\Seeders;

use App\Models\Tranoms;
use Illuminate\Database\Seeder;
use App\Services\LegacyDatabaseService;

class TranomsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();
        $rows = $legacy->select('SELECT * FROM tranoms');

        // Campos permitidos del modelo
        $fillable = (new Tranoms())->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            Tranoms::updateOrCreate(
                ['id' => $row['id']],
                $data
            );
        }

        $legacy->disconnect();
    }
}
