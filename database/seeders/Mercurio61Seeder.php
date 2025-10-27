<?php

namespace Database\Seeders;

use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;
use App\Models\Mercurio61;

class Mercurio61Seeder extends Seeder
{
    /**
     * Ejecuta las semillas de la base de datos.
     */
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();

        // Leer registros desde la base legada (columnas según migración)
        $rows = $legacy->select('SELECT numero, item, tipo, documento, cantidad, valor FROM mercurio61');

        // Si existe el modelo, usar sus fillable; de lo contrario, usar columnas de la migración
        $fillable = class_exists(Mercurio61::class)
            ? (new Mercurio61())->getFillable()
            : ['numero', 'item', 'tipo', 'documento', 'cantidad', 'valor'];

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            // Upsert por llave compuesta (numero, item)
            Mercurio61::updateOrCreate(
                [
                    'numero' => $row['numero'],
                    'item' => $row['item'],
                ],
                $data
            );
        }

        $legacy->disconnect();
    }
}
