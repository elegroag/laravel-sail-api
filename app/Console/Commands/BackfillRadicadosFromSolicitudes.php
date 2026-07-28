<?php

namespace App\Console\Commands;

use App\Models\Mercurio30;
use App\Models\Mercurio31;
use App\Models\Mercurio32;
use App\Models\Mercurio34;
use App\Models\Mercurio36;
use App\Models\Mercurio38;
use App\Models\Mercurio39;
use App\Models\Mercurio41;
use App\Models\Mercurio47;
use App\Models\Radicado;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BackfillRadicadosFromSolicitudes extends Command
{
    /**
     * Tablas de solicitud con columna ruuid => modelo Eloquent.
     *
     * @var array<string, class-string<Model>>
     */
    private const SOLICITUD_MODELS = [
        'mercurio30' => Mercurio30::class,
        'mercurio31' => Mercurio31::class,
        'mercurio32' => Mercurio32::class,
        'mercurio34' => Mercurio34::class,
        'mercurio36' => Mercurio36::class,
        'mercurio38' => Mercurio38::class,
        'mercurio39' => Mercurio39::class,
        'mercurio41' => Mercurio41::class,
        'mercurio47' => Mercurio47::class,
    ];

    protected $signature = 'radicados:backfill-from-solicitudes
                            {--dry-run : Solo cuenta y lista, no escribe}
                            {--regenerate-unparseable : Regenera radicado TIPO-VIGENCIA-NUMERO para ruuid no parseables (p. ej. UUID legado)}';

    protected $description = 'Inserta en radicados los ruuid de solicitudes huérfanos; normaliza prefijo #; opcionalmente regenera no parseables';

    public function handle(): int
    {
        if (! Schema::hasTable('radicados') || ! Schema::hasColumn('radicados', 'radicado')) {
            $this->error('La tabla radicados no existe o no tiene columna radicado.');

            return self::FAILURE;
        }

        $dryRun = (bool) $this->option('dry-run');
        $regenerate = (bool) $this->option('regenerate-unparseable');

        if ($dryRun) {
            $this->warn('Modo simulación (--dry-run): no se escribirá en la base de datos.');
        }

        if ($regenerate) {
            $this->warn('Modo --regenerate-unparseable: los UUID/legados se reemplazarán por un radicado nuevo.');
        }

        $orphans = $this->collectOrphanRuuids();
        $this->info('Ruuid huérfanos distintos: ' . count($orphans));

        /** @var array<string, true> $existingByRadicado */
        $existingByRadicado = [];
        /** @var array<string, string> $compositeOwner tipo|vigencia|numero => radicado */
        $compositeOwner = [];

        foreach (Radicado::query()->get(['radicado', 'tipo', 'vigencia', 'numero']) as $row) {
            $radicado = (string) $row->radicado;
            $existingByRadicado[$radicado] = true;
            $compositeOwner[$this->compositeKey((string) $row->tipo, (string) $row->vigencia, (int) $row->numero)] = $radicado;
        }

        $inserted = 0;
        $linkedExisting = 0;
        $normalized = 0;
        $regenerated = 0;
        $unparseable = [];
        $conflicts = [];

        foreach ($orphans as $orphan) {
            $canonical = $this->canonicalRuuid($orphan);
            $parsed = $this->parseRuuid($canonical);

            if (isset($existingByRadicado[$canonical])) {
                if (! $dryRun) {
                    $normalized += $this->rewriteRuuidReferences($orphan, $canonical);
                }
                $linkedExisting++;

                continue;
            }

            if ($parsed !== null) {
                [$tipo, $vigencia, $numero] = $parsed;
                $ckey = $this->compositeKey($tipo, $vigencia, $numero);
                $owner = $compositeOwner[$ckey] ?? null;

                if ($owner !== null && $owner !== $canonical) {
                    $conflicts[] = $orphan . ($orphan !== $canonical ? " → {$canonical}" : '') . " (ocupado por {$owner})";

                    continue;
                }

                if ($dryRun) {
                    $inserted++;
                    $existingByRadicado[$canonical] = true;
                    $compositeOwner[$ckey] = $canonical;

                    continue;
                }

                try {
                    DB::transaction(function () use ($tipo, $vigencia, $numero, $canonical, $orphan, &$normalized) {
                        Radicado::query()->create([
                            'tipo' => $tipo,
                            'vigencia' => (string) $vigencia,
                            'numero' => $numero,
                            'radicado' => $canonical,
                        ]);
                        $normalized += $this->rewriteRuuidReferences($orphan, $canonical);
                    });
                    $existingByRadicado[$canonical] = true;
                    $compositeOwner[$ckey] = $canonical;
                    $inserted++;
                } catch (\Throwable $e) {
                    $conflicts[] = $orphan . ' (' . $e->getMessage() . ')';
                }

                continue;
            }

            if (! $regenerate) {
                $unparseable[] = $orphan;

                continue;
            }

            if ($dryRun) {
                $regenerated++;

                continue;
            }

            try {
                $regenerated += $this->regenerateUnparseable($orphan);
            } catch (\Throwable $e) {
                $conflicts[] = $orphan . ' (regenerate: ' . $e->getMessage() . ')';
            }
        }

        $this->table(
            ['Métrica', 'Cantidad'],
            [
                ['Huérfanos detectados', (string) count($orphans)],
                [$dryRun ? 'Insertables (dry-run)' : 'Insertados en radicados', (string) $inserted],
                ['Ya existían en radicados (solo normalizar)', (string) $linkedExisting],
                ['Referencias ruuid normalizadas/actualizadas', (string) $normalized],
                [$dryRun ? 'Regenerables (dry-run)' : 'Filas regeneradas', (string) $regenerated],
                ['No parseables', (string) count($unparseable)],
                ['Conflictos (vigencia/tipo/numero)', (string) count($conflicts)],
            ]
        );

        if ($unparseable !== []) {
            $this->warn('No parseables (muestra hasta 20). Usa --regenerate-unparseable para reemplazarlos:');
            foreach (array_slice($unparseable, 0, 20) as $value) {
                $this->line('  - ' . $value);
            }
        }

        if ($conflicts !== []) {
            $this->warn('Conflictos (muestra hasta 20):');
            foreach (array_slice($conflicts, 0, 20) as $value) {
                $this->line('  - ' . $value);
            }
        }

        $remaining = $this->countRemainingOrphans();
        $this->info("Huérfanos restantes (ruuid no nulo sin match en radicados): {$remaining}");

        $unrepaired = count($unparseable) + count($conflicts);
        if ($unrepaired > 0) {
            $this->error('Quedan huérfanos no reparables. No crear la FK todavía.');

            return self::FAILURE;
        }

        if (! $dryRun && $remaining > 0) {
            $this->error('Quedan huérfanos pendientes tras el backfill. No crear la FK todavía.');

            return self::FAILURE;
        }

        $this->info($dryRun
            ? 'Dry-run OK: todos los huérfanos son reparables con las opciones actuales.'
            : 'Backfill OK: no quedan huérfanos.');

        return self::SUCCESS;
    }

    private function compositeKey(string $tipo, string $vigencia, int $numero): string
    {
        return strtoupper($tipo) . '|' . $vigencia . '|' . $numero;
    }

    /**
     * @return array<int, string>
     */
    private function collectOrphanRuuids(): array
    {
        $values = [];

        foreach (array_keys(self::SOLICITUD_MODELS) as $table) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'ruuid')) {
                continue;
            }

            $rows = DB::table($table)
                ->select('ruuid')
                ->whereNotNull('ruuid')
                ->where('ruuid', '!=', '')
                ->whereNotExists(function ($q) use ($table) {
                    $q->select(DB::raw(1))
                        ->from('radicados')
                        ->whereColumn('radicados.radicado', $table . '.ruuid');
                })
                ->distinct()
                ->pluck('ruuid');

            foreach ($rows as $ruuid) {
                $values[trim((string) $ruuid)] = true;
            }
        }

        $list = array_keys($values);
        sort($list);

        return $list;
    }

    private function countRemainingOrphans(): int
    {
        $total = 0;

        foreach (array_keys(self::SOLICITUD_MODELS) as $table) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'ruuid')) {
                continue;
            }

            $total += (int) DB::table($table)
                ->whereNotNull('ruuid')
                ->where('ruuid', '!=', '')
                ->whereNotExists(function ($q) use ($table) {
                    $q->select(DB::raw(1))
                        ->from('radicados')
                        ->whereColumn('radicados.radicado', $table . '.ruuid');
                })
                ->distinct('ruuid')
                ->count('ruuid');
        }

        return $total;
    }

    private function canonicalRuuid(string $ruuid): string
    {
        $trimmed = trim($ruuid);

        return str_starts_with($trimmed, '#')
            ? substr($trimmed, 1)
            : $trimmed;
    }

    /**
     * @return array{0: string, 1: string, 2: int}|null
     */
    private function parseRuuid(string $ruuid): ?array
    {
        if (! preg_match('/^([A-Za-z]{3})-(\d{4})-(\d+)$/', trim($ruuid), $m)) {
            return null;
        }

        return [strtoupper($m[1]), $m[2], (int) $m[3]];
    }

    /**
     * Reescribe ruuid en solicitudes y mercurio10 (prefijo # → canónico, o regeneración).
     */
    private function rewriteRuuidReferences(string $old, string $new): int
    {
        if ($old === $new) {
            return 0;
        }

        $updated = 0;

        foreach (array_keys(self::SOLICITUD_MODELS) as $table) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'ruuid')) {
                continue;
            }

            $updated += DB::table($table)->where('ruuid', $old)->update(['ruuid' => $new]);
        }

        if (Schema::hasTable('mercurio10') && Schema::hasColumn('mercurio10', 'ruuid')) {
            $oldLen = strlen($old);
            $updated += DB::update(
                'UPDATE mercurio10
                 SET ruuid = CONCAT(?, SUBSTRING(ruuid, ?))
                 WHERE ruuid = ? OR ruuid LIKE ?',
                [$new, $oldLen + 1, $old, $old . '-%']
            );
        }

        return $updated;
    }

    /**
     * Genera un radicado nuevo por cada fila de solicitud con ese ruuid no parseable.
     */
    private function regenerateUnparseable(string $orphan): int
    {
        $count = 0;

        foreach (self::SOLICITUD_MODELS as $table => $modelClass) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'ruuid')) {
                continue;
            }

            $ids = DB::table($table)->where('ruuid', $orphan)->orderBy('id')->pluck('id');

            foreach ($ids as $id) {
                $model = $modelClass::query()->find($id);
                if ($model === null) {
                    continue;
                }

                $old = (string) $model->getAttribute('ruuid');
                $model->regenerateUuid();
                $model->save();
                $new = (string) $model->getAttribute('ruuid');

                if (
                    Schema::hasTable('mercurio10')
                    && Schema::hasColumn('mercurio10', 'ruuid')
                    && $old !== ''
                    && $old !== $new
                ) {
                    $tipopcs = $this->tipopcsForTable($table);
                    if ($tipopcs !== []) {
                        $oldLen = strlen($old);
                        $placeholders = implode(',', array_fill(0, count($tipopcs), '?'));
                        DB::update(
                            "UPDATE mercurio10
                             SET ruuid = CONCAT(?, SUBSTRING(ruuid, ?))
                             WHERE numero = ?
                               AND tipopc IN ({$placeholders})
                               AND (ruuid = ? OR ruuid LIKE ?)",
                            [
                                $new,
                                $oldLen + 1,
                                $id,
                                ...$tipopcs,
                                $old,
                                $old . '-%',
                            ]
                        );
                    }
                }

                $count++;
            }
        }

        return $count;
    }

    /**
     * @return array<int, string>
     */
    private function tipopcsForTable(string $table): array
    {
        return match ($table) {
            'mercurio31' => ['1'],
            'mercurio30' => ['2'],
            'mercurio32' => ['3'],
            'mercurio34' => ['4'],
            'mercurio47' => ['5', '6', '14'],
            'mercurio38' => ['9'],
            'mercurio36' => ['10'],
            'mercurio39' => ['11'],
            'mercurio41' => ['13'],
            default => [],
        };
    }
}
