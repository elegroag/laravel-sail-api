<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PrependHashToMercurioRuuid extends Command
{
    protected $signature = 'mercurio:ruuid-prefix-hash
                            {--dry-run : Simular sin escribir en la base de datos}
                            {--table= : Procesar solo una tabla (ej. mercurio31)}
                            {--chunk=500 : Cantidad de registros por lote}';

    protected $description = 'Garantiza el prefijo # en el ruuid y ajusta el año según fecsol (los registros de 2026 solo conservan el prefijo)';

    private const PREFIX = '#';

    private const KEEP_YEAR = '2026';

    /**
     * Tablas a procesar. La clave es la tabla, el valor es la columna de fecha
     * de referencia para determinar el año, o null si el año no se debe tocar
     * (solo se antepone el prefijo).
     *
     * @var array<string, string|null>
     */
    private array $tables = [
        'mercurio30' => 'fecsol',
        'mercurio31' => 'fecsol',
        'mercurio32' => 'fecsol',
        'mercurio34' => 'fecsol',
        'mercurio35' => null,
        'mercurio36' => 'fecsol',
        'mercurio38' => 'fecsol',
        'mercurio39' => 'fecsol',
        'mercurio40' => 'fecsol',
        'mercurio41' => 'fecsol',
        'mercurio47' => 'fecsol',
    ];

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $chunkSize = max(1, (int) $this->option('chunk'));
        $onlyTable = $this->option('table');

        if ($onlyTable !== null && ! array_key_exists($onlyTable, $this->tables)) {
            $this->error("Tabla no válida: {$onlyTable}");
            $this->line('Tablas disponibles: '.implode(', ', array_keys($this->tables)));

            return self::FAILURE;
        }

        $tables = $onlyTable !== null
            ? [$onlyTable => $this->tables[$onlyTable]]
            : $this->tables;

        if ($dryRun) {
            $this->warn('Modo simulación (--dry-run): no se escribirá en la base de datos.');
        }

        $totalUpdated = 0;
        $totalSkipped = 0;
        $totalYearUpdated = 0;
        $totalInvalid = 0;

        foreach ($tables as $table => $dateColumn) {
            if (! Schema::hasTable($table)) {
                $this->warn("Tabla omitida (no existe): {$table}");

                continue;
            }

            if (! Schema::hasColumn($table, 'ruuid')) {
                $this->warn("Tabla omitida (sin columna ruuid): {$table}");

                continue;
            }

            // Si la tabla debe actualizar el año pero no tiene la columna de fecha, no se puede.
            if ($dateColumn !== null && ! Schema::hasColumn($table, $dateColumn)) {
                $this->warn("Tabla {$table}: sin columna '{$dateColumn}', solo se antepondrá el prefijo.");
                $dateColumn = null;
            }

            $this->info("Procesando {$table}...");

            $result = $this->processTable($table, $dateColumn, $chunkSize, $dryRun);

            $totalUpdated += $result['updated'];
            $totalSkipped += $result['skipped'];
            $totalYearUpdated += $result['year_updated'];
            $totalInvalid += $result['invalid'];

            $this->line("  → actualizados: {$result['updated']}, año actualizado: {$result['year_updated']}, formato inválido: {$result['invalid']}, omitidos: {$result['skipped']}");
        }

        $this->newLine();
        $this->info('Resumen global');
        $this->line("  Registros actualizados:      {$totalUpdated}");
        $this->line("  Con año actualizado:         {$totalYearUpdated}");
        $this->line("  Formato de ruuid inválido:   {$totalInvalid}");
        $this->line("  Registros omitidos:          {$totalSkipped}");

        if ($dryRun) {
            $this->warn('Ejecución simulada. Para aplicar los cambios, ejecute sin --dry-run.');
        }

        return self::SUCCESS;
    }

    /**
     * @return array{updated: int, skipped: int, year_updated: int, invalid: int}
     */
    private function processTable(string $table, ?string $dateColumn, int $chunkSize, bool $dryRun): array
    {
        $updated = 0;
        $skipped = 0;
        $yearUpdated = 0;
        $invalid = 0;

        $columns = ['id', 'ruuid'];
        if ($dateColumn !== null) {
            $columns[] = $dateColumn;
        }

        DB::table($table)
            ->select($columns)
            ->whereNotNull('ruuid')
            ->where('ruuid', '!=', '')
            ->orderBy('id')
            ->chunkById($chunkSize, function ($rows) use ($table, $dateColumn, $dryRun, &$updated, &$skipped, &$yearUpdated, &$invalid) {
                foreach ($rows as $row) {
                    $current = (string) $row->ruuid;

                    // Trabajamos siempre sobre el valor sin prefijo para poder
                    // corregir el año aunque ya se haya antepuesto el # antes.
                    $base = str_starts_with($current, self::PREFIX)
                        ? substr($current, strlen(self::PREFIX))
                        : $current;

                    $targetYear = $this->resolveYear($dateColumn, $row);

                    // Regla: si la tabla actualiza año pero la fecha es NULL, se omite el registro.
                    if ($dateColumn !== null && $targetYear === null) {
                        $skipped++;

                        continue;
                    }

                    $didYearUpdate = false;

                    // Solo intentamos actualizar el año si hay año objetivo y no es 2026.
                    // El año se toma siempre de fecsol (fuente de verdad).
                    if ($targetYear !== null && $targetYear !== self::KEEP_YEAR) {
                        $isValidFormat = false;
                        $replaced = $this->replaceYear($base, $targetYear, $isValidFormat);

                        if (! $isValidFormat) {
                            $invalid++;
                        } elseif ($replaced !== $base) {
                            $base = $replaced;
                            $didYearUpdate = true;
                        }
                    }

                    $newValue = self::PREFIX.$base;

                    // Si nada cambió (ya tenía prefijo y el año ya era correcto), se omite.
                    if ($newValue === $current) {
                        $skipped++;

                        continue;
                    }

                    if (! $dryRun) {
                        DB::table($table)
                            ->where('id', $row->id)
                            ->update(['ruuid' => $newValue]);
                    }

                    $updated++;
                    if ($didYearUpdate) {
                        $yearUpdated++;
                    }
                }
            }, 'id');

        return [
            'updated' => $updated,
            'skipped' => $skipped,
            'year_updated' => $yearUpdated,
            'invalid' => $invalid,
        ];
    }

    /**
     * Obtiene el año (YYYY) de la columna de fecha de referencia, o null si no
     * aplica o el valor es NULL/no parseable.
     */
    private function resolveYear(?string $dateColumn, object $row): ?string
    {
        if ($dateColumn === null) {
            return null;
        }

        $value = $row->{$dateColumn} ?? null;

        if ($value === null || $value === '' || $value === '0000-00-00') {
            return null;
        }

        // Las fechas vienen como 'YYYY-MM-DD' o 'YYYY-MM-DD HH:MM:SS'.
        if (preg_match('/^(\d{4})-\d{2}-\d{2}/', (string) $value, $m)) {
            return $m[1];
        }

        return null;
    }

    /**
     * Reemplaza el segundo segmento (año) de un ruuid con formato TIPO-AAAA-NNNNN.
     * Marca $isValidFormat en false si el ruuid no cumple ese patrón.
     */
    private function replaceYear(string $ruuid, string $year, ?bool &$isValidFormat): string
    {
        if (preg_match('/^([A-Za-z]+)-(\d{4})-(.+)$/', $ruuid, $m)) {
            $isValidFormat = true;

            return $m[1].'-'.$year.'-'.$m[3];
        }

        $isValidFormat = false;

        return $ruuid;
    }
}
