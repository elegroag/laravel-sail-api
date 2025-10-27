<?php

namespace Database\Seeders;

use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Mercurio62;

class Mercurio62Seeder extends Seeder
{
    /**
     * Ejecuta las semillas de la base de datos.
     */
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();

        // Columnas y PK compuesta según migración
        $columns = ['tipo', 'documento', 'coddoc', 'salgir', 'salrec', 'consumo', 'puntos', 'punuti'];
        $keys = ['tipo', 'documento', 'coddoc'];

        // Leer registros desde la base legada
        $rows = $legacy->select('SELECT ' . implode(',', $columns) . ' FROM mercurio62');

        $useModel = class_exists(Mercurio62::class);
        $fillable = $useModel ? (new Mercurio62())->getFillable() : $columns;

        foreach ($rows as $row) {
            // Construir payload limitado a fillable/columns
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            $identity = [
                'tipo' => $row['tipo'],
                'documento' => $row['documento'],
                'coddoc' => $row['coddoc'],
            ];

            if ($useModel) {
                Mercurio62::updateOrCreate($identity, $data);
            } else {
                DB::table('mercurio62')->updateOrInsert($identity, $data);
            }
        }

        $legacy->disconnect();
    }
}
