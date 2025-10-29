<?php

namespace Database\Seeders;

use App\Models\Mercurio47;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Mercurio06;

class Mercurio47Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if(Mercurio06::count() == 0){
            $this->call([
                Mercurio06Seeder::class,
                Mercurio07Seeder::class,
                Mercurio11Seeder::class,
            ]);
        }

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

            if(!is_numeric($data['coddoc'])){
                continue;
            }
            
            if(!is_numeric($data['documento'])){
                continue;
            }
            if($data['documento'] < 5) continue;
            
            if($data['tipo'] != null || $data['tipo'] != ''){
                continue;
            }

            $data['fecapr'] = $row['fecha_estado'] ?? null;
            $data['fecsol'] = $row['fecha_solicitud'] ?? null;
            $data['fecest'] = $row['fecha_estado'] ?? null;
            $data['tipact'] = $row['tipo_actualizacion'] ?? null;

            unset($data['fecha_estado']);
            unset($data['fecha_solicitud']);
            unset($data['tipo_actualizacion']);

            $data['ruuid'] = (string) Str::orderedUuid(); 
            
            Mercurio47::updateOrCreate(
                ['id' => $row['id']],
                $data
            );
        }

        $legacy->disconnect();
    }
}
