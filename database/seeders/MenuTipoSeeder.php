<?php

namespace Database\Seeders;

use App\Models\MenuTipo;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;

class MenuTipoSeeder extends Seeder
{
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();
        $rows = $legacy->select('SELECT * FROM menu_tipos');

        $fillable = (new MenuTipo())->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            MenuTipo::updateOrCreate(
                ['id' => $row['id']],
                $data
            );
        }

        $legacy->disconnect();
    }
}
