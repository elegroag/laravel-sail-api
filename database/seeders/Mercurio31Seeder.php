<?php

namespace Database\Seeders;

use App\Models\Mercurio31;
use App\Services\LegacyDatabaseService;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class Mercurio31Seeder extends Seeder
{

    /**
     * Ejecuta las semillas de la base de datos.
     */
    public function run(): void
    {
        $this->call([
            Mercurio06Seeder::class,
            Mercurio07Seeder::class,
            Mercurio11Seeder::class,
        ]);

        $legacy = new LegacyDatabaseService();

        // Leer registros desde la base legada
        $rows = $legacy->select('SELECT * FROM mercurio31');

        // Campos permitidos del modelo
        $fillable = (new Mercurio31())->getFillable();

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

            if($data['tippag'] == null || $data['tippag'] == ''){
                $data['tippag'] = 'T';
            }

            if($data['numcue'] == null || $data['numcue'] == ''){
                $data['numcue'] = 0;
            }

            if($data['codban'] == null || $data['codban'] == ''){
                $data['codban'] = 0;
            }

            unset($data['zoneurbana']);

            $data['ruuid'] = $row['ruuid'] ?? (string) Str::orderedUuid();

            Mercurio31::updateOrCreate(
                ['id' => $row['id']],
                $data
            );
        }

        $legacy->disconnect();
    }
}
