<?php

namespace App\Console\Commands;

use App\Support\AuditoriaSolicitudResolver;
use Illuminate\Console\Command;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use InvalidArgumentException;

class BackfillMercurio10Feccie extends Command
{
    protected $signature = 'mercurio10:backfill-feccie
                            {--dry-run : Solo cuenta, no escribe}
                            {--chunk=500 : Cantidad de pares tipopc/numero por lote}
                            {--tipopc= : Procesar solo un tipopc}';

    protected $description = 'Recalcula feccie en Mercurio10: fecsis del siguiente, fecapr si A, o fecsis propio si cerrada=S';

    public function handle(): int
    {
        if (
            ! Schema::hasTable('mercurio10')
            || ! Schema::hasColumn('mercurio10', 'feccie')
            || ! Schema::hasColumn('mercurio10', 'fecsis')
        ) {
            $this->error('mercurio10 debe existir con columnas feccie y fecsis. Ejecuta las migraciones primero.');

            return self::FAILURE;
        }

        $dryRun = (bool) $this->option('dry-run');
        $chunkSize = max(1, (int) $this->option('chunk'));
        $onlyTipopc = $this->option('tipopc');

        if ($onlyTipopc !== null && trim((string) $onlyTipopc) !== '') {
            $onlyTipopc = (string) $onlyTipopc;
        } else {
            $onlyTipopc = null;
        }

        if ($dryRun) {
            $this->warn('Modo simulación (--dry-run): no se escribirá en la base de datos.');
        }

        $totalPares = (int) DB::query()->fromSub($this->paresQuery($onlyTipopc), 'pares')->count();
        $this->info("Pares tipopc/numero a procesar: {$totalPares}");

        if ($totalPares === 0) {
            $this->info('Nada que procesar.');

            return self::SUCCESS;
        }

        $updated = 0;
        $sinCambio = 0;
        $sinSolicitud = 0;

        $afterTipopc = null;
        $afterNumero = null;

        while (true) {
            $pares = $this->fetchParesChunk($onlyTipopc, $chunkSize, $afterTipopc, $afterNumero);

            if ($pares->isEmpty()) {
                break;
            }

            $solicitudes = $this->cargarSolicitudesPorPares($pares);
            $eventosPorPar = $this->cargarEventosPorPares($pares);

            foreach ($pares as $par) {
                $tipopc = (string) $par->tipopc;
                $numero = (int) $par->numero;
                $key = $tipopc.'|'.$numero;

                $eventos = $eventosPorPar[$key] ?? collect();
                if ($eventos->isEmpty()) {
                    continue;
                }

                $sol = $solicitudes[$key] ?? null;
                if ($sol === null) {
                    $sinSolicitud += $eventos->count();
                }

                $fecapr = null;
                if ($sol !== null && strtoupper((string) ($sol['estado'] ?? '')) === 'A') {
                    $fecapr = $this->normalizarFecha($sol['fecapr'] ?? null);
                }

                $lista = $eventos->values();
                $count = $lista->count();

                for ($i = 0; $i < $count; $i++) {
                    $evento = $lista[$i];
                    $actual = $this->normalizarFecha($evento->feccie ?? null);

                    if ($i < $count - 1) {
                        $nuevo = $this->normalizarFecha($lista[$i + 1]->fecsis ?? null);
                    } elseif ($fecapr !== null) {
                        $nuevo = $fecapr;
                    } elseif (strtoupper((string) ($evento->cerrada ?? 'N')) === 'S') {
                        // Cerrado sin siguiente ni fecapr: usar fecsis del propio evento.
                        $nuevo = $this->normalizarFecha($evento->fecsis ?? null);
                    } else {
                        $nuevo = null;
                    }

                    if ($actual === $nuevo) {
                        $sinCambio++;

                        continue;
                    }

                    if (! $dryRun) {
                        DB::table('mercurio10')
                            ->where('tipopc', $evento->tipopc)
                            ->where('numero', $evento->numero)
                            ->where('item', $evento->item)
                            ->update(['feccie' => $nuevo]);
                    }

                    $updated++;
                }
            }

            $last = $pares->last();
            $afterTipopc = (string) $last->tipopc;
            $afterNumero = (int) $last->numero;
        }

        $this->info("Actualizados: {$updated}");
        $this->info("Sin cambio: {$sinCambio}");
        $this->info("Eventos sin solicitud (último usa null salvo regla A): {$sinSolicitud}");
        $this->info($dryRun ? 'Dry-run finalizado.' : 'Proceso finalizado.');

        return self::SUCCESS;
    }

    private function paresQuery(?string $onlyTipopc): Builder
    {
        return DB::table('mercurio10')
            ->select('tipopc', 'numero')
            ->when($onlyTipopc !== null, fn ($q) => $q->where('tipopc', $onlyTipopc))
            ->groupBy('tipopc', 'numero');
    }

    /**
     * @return Collection<int, object>
     */
    private function fetchParesChunk(
        ?string $onlyTipopc,
        int $chunkSize,
        ?string $afterTipopc,
        ?int $afterNumero
    ): Collection {
        $query = $this->paresQuery($onlyTipopc)
            ->orderBy('tipopc')
            ->orderBy('numero')
            ->limit($chunkSize);

        if ($afterTipopc !== null && $afterNumero !== null) {
            $query->where(function ($q) use ($afterTipopc, $afterNumero) {
                $q->where('tipopc', '>', $afterTipopc)
                    ->orWhere(function ($q2) use ($afterTipopc, $afterNumero) {
                        $q2->where('tipopc', $afterTipopc)
                            ->where('numero', '>', $afterNumero);
                    });
            });
        }

        return $query->get();
    }

    /**
     * @param  Collection<int, object>  $pares
     * @return array<string, array{estado: ?string, fecapr: ?string}|null>
     */
    private function cargarSolicitudesPorPares(Collection $pares): array
    {
        $result = [];
        $byTipopc = [];

        foreach ($pares as $par) {
            $byTipopc[(string) $par->tipopc][] = (int) $par->numero;
        }

        foreach ($byTipopc as $tipopc => $numeros) {
            $numeros = array_values(array_unique($numeros));

            try {
                $modelClass = AuditoriaSolicitudResolver::modelClassFor($tipopc);
            } catch (InvalidArgumentException) {
                foreach ($numeros as $numero) {
                    $result[$tipopc.'|'.$numero] = null;
                }

                continue;
            }

            $table = (new $modelClass)->getTable();
            $hasEstado = Schema::hasColumn($table, 'estado');
            $hasFecapr = Schema::hasColumn($table, 'fecapr');

            if (! $hasEstado && ! $hasFecapr) {
                foreach ($numeros as $numero) {
                    $result[$tipopc.'|'.$numero] = null;
                }

                continue;
            }

            $columns = ['id'];
            if ($hasEstado) {
                $columns[] = 'estado';
            }
            if ($hasFecapr) {
                $columns[] = 'fecapr';
            }

            $rows = $modelClass::query()
                ->whereIn('id', $numeros)
                ->get($columns);

            $found = [];
            foreach ($rows as $row) {
                $found[(int) $row->id] = [
                    'estado' => $hasEstado ? (string) ($row->estado ?? '') : null,
                    'fecapr' => $hasFecapr ? ($row->fecapr ?? null) : null,
                ];
            }

            foreach ($numeros as $numero) {
                $result[$tipopc.'|'.$numero] = $found[$numero] ?? null;
            }
        }

        return $result;
    }

    /**
     * @param  Collection<int, object>  $pares
     * @return array<string, Collection<int, object>>
     */
    private function cargarEventosPorPares(Collection $pares): array
    {
        if ($pares->isEmpty()) {
            return [];
        }

        $byTipopc = [];
        foreach ($pares as $par) {
            $byTipopc[(string) $par->tipopc][] = (int) $par->numero;
        }

        $query = DB::table('mercurio10')
            ->select(['tipopc', 'numero', 'item', 'fecsis', 'feccie', 'cerrada'])
            ->where(function ($q) use ($byTipopc) {
                foreach ($byTipopc as $tipopc => $numeros) {
                    $q->orWhere(function ($q2) use ($tipopc, $numeros) {
                        $q2->where('tipopc', $tipopc)
                            ->whereIn('numero', array_values(array_unique($numeros)));
                    });
                }
            })
            ->orderBy('tipopc')
            ->orderBy('numero')
            ->orderBy('item');

        $grouped = [];
        foreach ($query->get() as $evento) {
            $key = (string) $evento->tipopc.'|'.(int) $evento->numero;
            $grouped[$key] ??= collect();
            $grouped[$key]->push($evento);
        }

        return $grouped;
    }

    private function normalizarFecha(mixed $valor): ?string
    {
        if ($valor === null) {
            return null;
        }

        $fecha = trim((string) $valor);
        if ($fecha === '' || $fecha === '0000-00-00' || str_starts_with($fecha, '0000-00-00')) {
            return null;
        }

        // Carbon/Eloquent pueden devolver datetime; nos quedamos con Y-m-d.
        if (preg_match('/^(\d{4}-\d{2}-\d{2})/', $fecha, $m) === 1) {
            return $m[1];
        }

        return null;
    }
}
