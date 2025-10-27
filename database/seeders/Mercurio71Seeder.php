<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class Mercurio71Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();

        // Columnas según migración y PK autoincremental numero
        $columns = ['numero', 'tipo', 'documento', 'coddoc', 'codser', 'puntos', 'fecsis', 'hora'];

        // Leer registros desde la base legada
        $rows = $legacy->select('SELECT ' . implode(',', $columns) . ' FROM mercurio71');

        $useModel = class_exists(Mercurio71::class);
        $fillable = $useModel ? (new Mercurio71())->getFillable() : $columns;

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            $identity = ['numero' => $row['numero']];

            if ($useModel) {
                Mercurio71::updateOrCreate($identity, $data);
            } else {
                DB::table('mercurio71')->updateOrInsert($identity, $data);
            }
        }

        $legacy->disconnect();
    }
}
