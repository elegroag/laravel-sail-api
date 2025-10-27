<?php

namespace Database\Seeders;

use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Mercurio64;

class Mercurio64Seeder extends Seeder
{
    /**
     * Ejecuta las semillas de la base de datos.
     */
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();

        // Columnas según migración y PK autoincremental numero
        $columns = ['numero', 'tipo', 'documento', 'coddoc', 'tipmov', 'pergir', 'online', 'transferencia', 'valor', 'fecsis', 'hora', 'estado'];

        // Leer registros desde la base legada
        $rows = $legacy->select('SELECT ' . implode(',', $columns) . ' FROM mercurio64');

        $useModel = class_exists(Mercurio64::class);
        $fillable = $useModel ? (new Mercurio64())->getFillable() : $columns;

        foreach ($rows as $row) {
            // Construir payload limitado a fillable/columns
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            $identity = ['numero' => $row['numero']];

            if ($useModel) {
                Mercurio64::updateOrCreate($identity, $data);
            } else {
                DB::table('mercurio64')->updateOrInsert($identity, $data);
            }
        }

        $legacy->disconnect();
    }
}
