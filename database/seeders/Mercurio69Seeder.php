<?php

namespace Database\Seeders;

use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Mercurio69;

class Mercurio69Seeder extends Seeder
{
    /**
     * Ejecuta las semillas de la base de datos.
     */
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();

        // Columnas según migración y PK autoincremental numero
        $columns = ['numero', 'tipo', 'documento', 'coddoc', 'codser', 'puntos', 'fecsis', 'hora'];

        // Leer registros desde la base legada
        $rows = $legacy->select('SELECT ' . implode(',', $columns) . ' FROM mercurio69');

        $useModel = class_exists(Mercurio69::class);
        $fillable = $useModel ? (new Mercurio69())->getFillable() : $columns;

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            $identity = ['numero' => $row['numero']];

            if ($useModel) {
                Mercurio69::updateOrCreate($identity, $data);
            } else {
                DB::table('mercurio69')->updateOrInsert($identity, $data);
            }
        }

        $legacy->disconnect();
    }
}
