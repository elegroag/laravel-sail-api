<?php

namespace App\Console\Commands;

use App\Support\AuditoriaSolicitudResolver;
use Illuminate\Console\Command;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use InvalidArgumentException;

class BackfillMercurio10Ruuid extends Command
{
    private const MAX_RUUID_LENGTH = 20;

    protected $signature = 'mercurio10:backfill-ruuid
                            {--dry-run : Solo cuenta, no escribe}
                            {--chunk=500 : Cantidad de pares tipopc/numero por lote}
                            {--tipopc= : Procesar solo un tipopc}';

    protected $description = 'Asigna ruuid vacío en Mercurio10 (P) desde la solicitud + item (2 dígitos)';

    public function handle(): int
    {
        if (! Schema::hasTable('mercurio10') || ! Schema::hasColumn('mercurio10', 'ruuid')) {
            $this->error('La tabla mercurio10 no existe o no tiene la columna ruuid. Ejecuta las migraciones primero.');

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
        $this->info("Pares tipopc/numero con eventos P sin ruuid: {$totalPares}");

        if ($totalPares === 0) {
            $this->info('Nada que procesar.');

            return self::SUCCESS;
        }

        $updated = 0;
        $omitidosSinSolicitud = 0;
        $omitidosSinBase = 0;
        $omitidosTipopc = 0;
        $invalidos = 0;

        $afterTipopc = null;
        $afterNumero = null;

        while (true) {
            $pares = $this->fetchParesChunk($onlyTipopc, $chunkSize, $afterTipopc, $afterNumero);

            if ($pares->isEmpty()) {
                break;
            }

            $bases = $this->cargarBasesPorPares($pares);

            foreach ($pares as $par) {
                $tipopc = (string) $par->tipopc;
                $numero = (int) $par->numero;
                $cacheKey = $tipopc.'|'.$numero;
                $baseInfo = $bases[$cacheKey] ?? ['status' => 'missing'];

                $eventosCount = (int) ($par->eventos_count ?? $this->contarEventosVacios($tipopc, $numero));

                if ($baseInfo['status'] === 'tipopc') {
                    $omitidosTipopc += $eventosCount;

                    continue;
                }

                if ($baseInfo['status'] === 'missing') {
                    $omitidosSinSolicitud += $eventosCount;

                    continue;
                }

                if ($baseInfo['status'] === 'empty') {
                    $omitidosSinBase += $eventosCount;

                    continue;
                }

                $base = $baseInfo['base'];
                // Sufijo fijo de 3 chars: "-NN" (item 2 dígitos). Si item > 99 puede crecer.
                if (strlen($base) + 3 > self::MAX_RUUID_LENGTH) {
                    $invalidos += $eventosCount;

                    continue;
                }

                if ($dryRun) {
                    $updated += $eventosCount;

                    continue;
                }

                $affected = DB::affectingStatement(
                    'UPDATE mercurio10
                     SET ruuid = CONCAT(?, \'-\', LPAD(item, 2, \'0\'))
                     WHERE tipopc = ?
                       AND numero = ?
                       AND estado = \'P\'
                       AND (ruuid IS NULL OR ruuid = \'\')
                       AND CHAR_LENGTH(?) + 1 + CHAR_LENGTH(CAST(item AS CHAR)) <= ?',
                    [$base, $tipopc, $numero, $base, self::MAX_RUUID_LENGTH]
                );

                $updated += $affected;

                $skippedLen = $eventosCount - $affected;
                if ($skippedLen > 0) {
                    $invalidos += $skippedLen;
                }
            }

            $last = $pares->last();
            $afterTipopc = (string) $last->tipopc;
            $afterNumero = (int) $last->numero;
        }

        $this->info("Actualizados: {$updated}");
        $this->info("Omitidos (tipopc no mapeado): {$omitidosTipopc}");
        $this->info("Omitidos (sin solicitud): {$omitidosSinSolicitud}");
        $this->info("Omitidos (solicitud sin ruuid): {$omitidosSinBase}");
        $this->info('Inválidos (ruuid > '.self::MAX_RUUID_LENGTH.' chars): '.$invalidos);
        $this->info($dryRun ? 'Dry-run finalizado.' : 'Proceso finalizado.');

        return self::SUCCESS;
    }

    private function paresQuery(?string $onlyTipopc): Builder
    {
        return DB::table('mercurio10')
            ->select('tipopc', 'numero', DB::raw('COUNT(*) as eventos_count'))
            ->where('estado', 'P')
            ->where(function ($q) {
                $q->whereNull('ruuid')->orWhere('ruuid', '');
            })
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
     * @return array<string, array{status: string, base?: string}>
     */
    private function cargarBasesPorPares(Collection $pares): array
    {
        $result = [];
        $byTipopc = [];

        foreach ($pares as $par) {
            $tipopc = (string) $par->tipopc;
            $numero = (int) $par->numero;
            $byTipopc[$tipopc][] = $numero;
        }

        foreach ($byTipopc as $tipopc => $numeros) {
            try {
                $modelClass = AuditoriaSolicitudResolver::modelClassFor($tipopc);
            } catch (InvalidArgumentException) {
                foreach ($numeros as $numero) {
                    $result[$tipopc.'|'.$numero] = ['status' => 'tipopc'];
                }

                continue;
            }

            $table = (new $modelClass)->getTable();
            if (! Schema::hasColumn($table, 'ruuid')) {
                foreach ($numeros as $numero) {
                    $result[$tipopc.'|'.$numero] = ['status' => 'empty'];
                }

                continue;
            }

            $numeros = array_values(array_unique($numeros));
            $rows = $modelClass::query()
                ->whereIn('id', $numeros)
                ->get(['id', 'ruuid']);

            $found = [];
            foreach ($rows as $row) {
                $found[(int) $row->id] = trim((string) ($row->ruuid ?? ''));
            }

            foreach ($numeros as $numero) {
                $key = $tipopc.'|'.$numero;
                if (! array_key_exists($numero, $found)) {
                    $result[$key] = ['status' => 'missing'];

                    continue;
                }

                if ($found[$numero] === '') {
                    $result[$key] = ['status' => 'empty'];

                    continue;
                }

                $result[$key] = ['status' => 'ok', 'base' => $found[$numero]];
            }
        }

        return $result;
    }

    private function contarEventosVacios(string $tipopc, int $numero): int
    {
        return (int) DB::table('mercurio10')
            ->where('tipopc', $tipopc)
            ->where('numero', $numero)
            ->where('estado', 'P')
            ->where(function ($q) {
                $q->whereNull('ruuid')->orWhere('ruuid', '');
            })
            ->count();
    }
}
