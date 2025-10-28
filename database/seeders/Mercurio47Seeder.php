<?php

namespace Database\Seeders;

use App\Models\Mercurio47;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;

class Mercurio47Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();

        // Leer registros desde la base legada
        $rows = $legacy->select('SELECT * FROM mercurio47');

        // Usar fillable del modelo para construir los datos
        $fillable = (new Mercurio47())->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            $data['fecapr'] = $row['fecha_estado'] ?? null;
            $data['fecsol'] = $row['fecha_solicitud'] ?? null;
            $data['fecest'] = $row['fecha_estado'] ?? null;
            $data['tipact'] = $row['tipo_actualizacion'] ?? null;

            unset($data['fecha_estado']);
            unset($data['fecha_solicitud']);
            unset($data['tipo_actualizacion']);
            
            Mercurio47::updateOrCreate(
                ['id' => $row['id']],
                $data
            );
        }

        $legacy->disconnect();
    }
}
