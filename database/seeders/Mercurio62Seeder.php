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

        $fillable = (new Mercurio62())->getFillable();

        $rows = $legacy->select('SELECT * FROM mercurio62');

        foreach ($rows as $row) {
            // Construir payload limitado a fillable/columns
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            if($data['documento'] < 5) continue;
            if(!is_numeric($data['coddoc'])){
                continue;
            }
            if(!is_numeric($data['documento'])){
                continue;
            }
            if(!is_numeric($data['tipo'])){
                continue;
            }

            Mercurio62::updateOrCreate([
                'tipo' => $row['tipo'],
                'documento' => $row['documento'],
                'coddoc' => $row['coddoc'],
            ], $data);
        }

        $legacy->disconnect();
    }
}
