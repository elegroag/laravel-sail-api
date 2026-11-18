<?php

namespace App\Console\Commands;

use App\Models\Gener40;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportGener40CsvCommand extends Command
{
    protected $signature = 'cajas:gener40-import-csv
                            {file? : Ruta al CSV (por defecto gener40_acciones_modulos.csv en la raíz del monorepo)}
                            {--dry-run : Solo muestra el resumen sin guardar}
                            {--omit=FIRMAS,PREGUNTAS,SERVICIO DOMESTICO : Patrones a omitir en el detalle, separados por coma}';

    protected $description = 'Actualiza controller y action de gener40 desde un CSV (no pisa con celdas vacías; omite acciones deprecadas)';

    public function handle(): int
    {
        $path = $this->resolvePath($this->argument('file'));
        if (! is_file($path)) {
            $this->error("No se encontró el archivo: {$path}");

            return self::FAILURE;
        }

        $omitPatterns = array_values(array_filter(array_map(
            static fn (string $p): string => mb_strtoupper(trim($p)),
            explode(',', (string) $this->option('omit'))
        )));

        $handle = fopen($path, 'r');
        if ($handle === false) {
            $this->error("No se pudo abrir el archivo: {$path}");

            return self::FAILURE;
        }

        $header = fgetcsv($handle, 0, ';');
        if (! is_array($header) || $header === []) {
            fclose($handle);
            $this->error('El CSV no tiene cabecera válida.');

            return self::FAILURE;
        }

        $header = array_map(static fn ($h) => mb_strtolower(trim((string) $h)), $header);
        $indexes = $this->columnIndexes($header);
        if ($indexes['codigo'] === null) {
            fclose($handle);
            $this->error('El CSV debe incluir la columna Código.');

            return self::FAILURE;
        }

        $stats = [
            'leidas' => 0,
            'omitidas_deprecadas' => 0,
            'omitidas_vacias' => 0,
            'no_encontradas' => 0,
            'sin_cambios' => 0,
            'actualizadas' => 0,
        ];
        $cambios = [];
        $dryRun = (bool) $this->option('dry-run');

        DB::beginTransaction();
        try {
            while (($row = fgetcsv($handle, 0, ';')) !== false) {
                if ($this->rowIsEmpty($row)) {
                    continue;
                }

                $stats['leidas']++;
                $codigo = $this->cell($row, $indexes['codigo']);
                $detalle = $this->cell($row, $indexes['detalle']);
                $controller = $this->cell($row, $indexes['controller']);
                $action = $this->cell($row, $indexes['action']);

                if ($codigo === '') {
                    continue;
                }

                if ($this->isDeprecated($detalle, $omitPatterns)) {
                    $stats['omitidas_deprecadas']++;

                    continue;
                }

                if ($controller === '' && $action === '') {
                    $stats['omitidas_vacias']++;

                    continue;
                }

                $item = Gener40::query()->where('codigo', $codigo)->first();
                if (! $item) {
                    $stats['no_encontradas']++;
                    $this->warn("Código no encontrado en gener40: {$codigo}");

                    continue;
                }

                $payload = [];
                if ($controller !== '' && (string) $item->controller !== $controller) {
                    $payload['controller'] = $controller;
                }
                if ($action !== '' && (string) $item->action !== $action) {
                    $payload['action'] = $action;
                }

                if ($payload === []) {
                    $stats['sin_cambios']++;

                    continue;
                }

                $cambios[] = [
                    $codigo,
                    $detalle !== '' ? $detalle : (string) ($item->detalle ?? ''),
                    $payload['controller'] ?? (string) ($item->controller ?? '—'),
                    $payload['action'] ?? (string) ($item->action ?? '—'),
                ];

                if (! $dryRun) {
                    $item->fill($payload);
                    $item->save();
                }

                $stats['actualizadas']++;
            }

            if ($dryRun) {
                DB::rollBack();
            } else {
                DB::commit();
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            fclose($handle);
            $this->error('Error al importar: '.$e->getMessage());

            return self::FAILURE;
        }

        fclose($handle);

        if ($cambios !== []) {
            $this->table(['Código', 'Detalle', 'Controller', 'Action'], $cambios);
        }

        $this->newLine();
        $this->info($dryRun ? 'Simulación (dry-run) completada.' : 'Importación completada.');
        $this->line("Archivo: {$path}");
        $this->line('Omitidos por patrón: '.implode(', ', $omitPatterns ?: ['(ninguno)']));
        $this->table(
            ['Métrica', 'Cantidad'],
            [
                ['Filas leídas', $stats['leidas']],
                ['Omitidas (deprecadas)', $stats['omitidas_deprecadas']],
                ['Omitidas (sin controller/action)', $stats['omitidas_vacias']],
                ['Código no encontrado', $stats['no_encontradas']],
                ['Sin cambios', $stats['sin_cambios']],
                [$dryRun ? 'A actualizar' : 'Actualizadas', $stats['actualizadas']],
            ]
        );

        if ($stats['omitidas_vacias'] > 0 && $stats['actualizadas'] === 0) {
            $this->warn('El CSV no trae Controller/Action. Usa cajas:gener40-assign-routes o completa esas columnas.');
        }

        return self::SUCCESS;
    }

    private function resolvePath(?string $file): string
    {
        if (is_string($file) && trim($file) !== '') {
            $file = trim($file);
            if ($file[0] === '/' || preg_match('/^[A-Za-z]:[\\\\\\/]/', $file) === 1) {
                return $file;
            }

            return base_path($file);
        }

        $monorepo = dirname(base_path()).DIRECTORY_SEPARATOR.'gener40_acciones_modulos.csv';
        if (is_file($monorepo)) {
            return $monorepo;
        }

        return base_path('gener40_acciones_modulos.csv');
    }

    /**
     * @param  list<string>  $header
     * @return array{codigo: ?int, detalle: ?int, controller: ?int, action: ?int}
     */
    private function columnIndexes(array $header): array
    {
        $find = static function (array $aliases) use ($header): ?int {
            foreach ($aliases as $alias) {
                $i = array_search($alias, $header, true);
                if ($i !== false) {
                    return (int) $i;
                }
            }

            return null;
        };

        return [
            'codigo' => $find(['codigo', 'código', 'code']),
            'detalle' => $find(['detalle', 'descripcion', 'descripción']),
            'controller' => $find(['controller', 'controlador']),
            'action' => $find(['action', 'accion', 'acción']),
        ];
    }

    /**
     * @param  list<string|null>  $row
     */
    private function cell(array $row, ?int $index): string
    {
        if ($index === null || ! array_key_exists($index, $row)) {
            return '';
        }

        return trim((string) $row[$index]);
    }

    /**
     * @param  list<string|null>  $row
     */
    private function rowIsEmpty(array $row): bool
    {
        foreach ($row as $value) {
            if (trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }

    /**
     * @param  list<string>  $patterns
     */
    private function isDeprecated(string $detalle, array $patterns): bool
    {
        if ($detalle === '' || $patterns === []) {
            return false;
        }

        $haystack = mb_strtoupper($detalle);
        foreach ($patterns as $pattern) {
            if ($pattern !== '' && str_contains($haystack, $pattern)) {
                return true;
            }
        }

        return false;
    }
}
