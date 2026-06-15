<?php

namespace Database\Seeders;

use App\Models\MenuPermission;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;

class MenuPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();
        $rows = $legacy->select('SELECT * FROM menu_permissions');

        $fillable = (new MenuPermission())->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            MenuPermission::updateOrCreate(
                ['id' => $row['id']],
                $data
            );
        }

        $legacy->disconnect();
    }
}
