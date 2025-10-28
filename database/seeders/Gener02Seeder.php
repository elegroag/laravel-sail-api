<?php

namespace Database\Seeders;

use App\Models\Gener02;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;

class Gener02Seeder extends Seeder
{
    /**
     * Ejecuta las semillas de la base de datos.
     */
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();

        // Leer registros desde la base legada
        $rows = $legacy->select('SELECT * FROM gener02');

        // Campos permitidos del modelo
        $fillable = (new Gener02())->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                if($field == 'acceso'){
                    if ($row[$field] == null || $row[$field] == '') $row[$field]= 'H';
                }
                $data[$field] = $row[$field] ?? null;
            }

            Gener02::updateOrCreate(
                ['usuario' => $row['usuario']],
                $data
            );
        }

        $legacy->disconnect();
    }
}
