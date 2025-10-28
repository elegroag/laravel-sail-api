<?php

namespace Database\Seeders;

use App\Models\Mercurio30;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;

class Mercurio30Seeder extends Seeder
{
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();

        $rows = $legacy->select('SELECT * FROM mercurio30');

        $fillable = (new Mercurio30())->getFillable();

        foreach ($rows as $row) {
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
            $data['sat_fecapr'] = $row['fecha_aprobacion_sat'];
            $data['sat_cedrep'] = $row['documento_representante_sat'];
            $data['sat_numtra'] = $row['numero_transaccion'];

            unset($data['fecha_aprobacion_sat']);
            unset($data['documento_representante_sat']);
            unset($data['numero_transaccion']);
            
            Mercurio30::updateOrCreate(
                ['id' => $row['id']],
                $data
            );
        }

        $legacy->disconnect();
    }
}
