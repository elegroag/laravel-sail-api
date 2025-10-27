<?php

namespace Database\Seeders;

use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Mercurio74;

class Mercurio74Seeder extends Seeder
{
    /**
     * Ejecuta las semillas de la base de datos.
     */
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();

        // Columnas según migración y PK autoincremental numrec
        $columns = ['numrec', 'archivo', 'orden', 'url', 'estado'];

        // Leer registros desde la base legada
        $rows = $legacy->select('SELECT ' . implode(',', $columns) . ' FROM mercurio74');

        $useModel = class_exists(Mercurio74::class);
        $fillable = $useModel ? (new Mercurio74())->getFillable() : $columns;

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            $identity = ['numrec' => $row['numrec']];

            if ($useModel) {
                Mercurio74::updateOrCreate($identity, $data);
            } else {
                DB::table('mercurio74')->updateOrInsert($identity, $data);
            }
        }

        $legacy->disconnect();
    }
}
