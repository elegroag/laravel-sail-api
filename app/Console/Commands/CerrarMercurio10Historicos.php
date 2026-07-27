<?php

namespace App\Console\Commands;

use App\Models\Mercurio10;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CerrarMercurio10Historicos extends Command
{
    /**
     * Estados de respuesta a marcar como cerrados en el backfill histórico.
     *
     * @var array<int, string>
     */
    private const ESTADOS_RESPUESTA = ['A', 'X', 'D'];

    protected $signature = 'mercurio10:cerrar-historicos
                            {--dry-run : Solo cuenta, no escribe}
                            {--estados= : Estados de respuesta a marcar (por defecto A,X,D)}
                            {--cerrar-pendientes : También cierra eventos P previos de esas solicitudes}';

    protected $description = 'Backfill histórico: cierra A/X/D y P previos; el último ítem en P queda abierto sin feccie';

    public function handle(): int
    {
        if (! Schema::hasTable('mercurio10') || ! Schema::hasColumn('mercurio10', 'cerrada')) {
            $this->error('La tabla mercurio10 no existe o no tiene la columna cerrada. Ejecuta las migraciones primero.');

            return self::FAILURE;
        }

        $dryRun = (bool) $this->option('dry-run');
        $estados = $this->resolverEstados();
        $hoy = Carbon::now()->toDateString();
        $conFeccie = Schema::hasColumn('mercurio10', 'feccie');

        if ($estados === []) {
            $this->error('Debes indicar al menos un estado en --estados');

            return self::FAILURE;
        }

        if ($dryRun) {
            $this->warn('Modo simulación (--dry-run): no se escribirá en la base de datos.');
        }

        $this->info('Estados de respuesta: '.implode(', ', $estados));

        $queryRespuestas = Mercurio10::query()
            ->whereIn('estado', $estados)
            ->where(function ($q) {
                $q->where('cerrada', 'N')
                    ->orWhereNull('cerrada')
                    ->orWhere('cerrada', '');
            });

        $countRespuestas = (clone $queryRespuestas)->count();
        $this->info("Respuestas abiertas a cerrar: {$countRespuestas}");

        if (! $dryRun && $countRespuestas > 0) {
            $payload = ['cerrada' => 'S'];
            if ($conFeccie) {
                $payload['feccie'] = $hoy;
            }
            $updated = $queryRespuestas->update($payload);
            $this->info("Actualizados (respuestas): {$updated}");
        }

        if ($this->option('cerrar-pendientes')) {
            $this->cerrarPendientesAsociados($estados, $dryRun, $hoy, $conFeccie);
        }

        $this->reabrirUltimosPendientes($dryRun, $conFeccie);

        if ($conFeccie) {
            $this->completarFeccieEnCerrados($dryRun);
        }

        $this->info($dryRun ? 'Dry-run finalizado.' : 'Proceso finalizado.');

        return self::SUCCESS;
    }

    /**
     * @return array<int, string>
     */
    private function resolverEstados(): array
    {
        $raw = $this->option('estados');

        if ($raw === null || trim((string) $raw) === '') {
            return self::ESTADOS_RESPUESTA;
        }

        return collect(explode(',', (string) $raw))
            ->map(fn ($e) => strtoupper(trim($e)))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    /**
     * Cierra P previos de solicitudes con respuesta A/X/D.
     * El último ítem en P no se cierra (sigue abierto, sin feccie).
     *
     * @param  array<int, string>  $estados
     */
    private function cerrarPendientesAsociados(array $estados, bool $dryRun, string $hoy, bool $conFeccie): void
    {
        $placeholders = implode(',', array_fill(0, count($estados), '?'));

        // Subconsulta derivada: MySQL no permite UPDATE de la misma tabla
        // referenciada directamente en FROM/EXISTS sin wrapper.
        $countSql = "
            SELECT COUNT(*) AS aggregate
            FROM mercurio10 p
            INNER JOIN (
                SELECT DISTINCT tipopc, numero
                FROM mercurio10
                WHERE estado IN ({$placeholders})
            ) r ON r.tipopc = p.tipopc AND r.numero = p.numero
            INNER JOIN (
                SELECT tipopc, numero, MAX(item) AS max_item
                FROM mercurio10
                GROUP BY tipopc, numero
            ) last ON last.tipopc = p.tipopc AND last.numero = p.numero
            WHERE p.estado = 'P'
              AND p.item <> last.max_item
              AND (p.cerrada IS NULL OR p.cerrada = '' OR p.cerrada = 'N')
        ";

        $countP = (int) (DB::selectOne($countSql, $estados)->aggregate ?? 0);
        $this->info("Pendientes P previos a cerrar (excl. último ítem): {$countP}");

        if ($dryRun || $countP === 0) {
            return;
        }

        $setFeccie = $conFeccie ? ', p.feccie = ?' : '';
        $bindings = $conFeccie ? array_merge([$hoy], $estados) : $estados;

        $updatedP = DB::update("
            UPDATE mercurio10 p
            INNER JOIN (
                SELECT DISTINCT tipopc, numero
                FROM mercurio10
                WHERE estado IN ({$placeholders})
            ) r ON r.tipopc = p.tipopc AND r.numero = p.numero
            INNER JOIN (
                SELECT tipopc, numero, MAX(item) AS max_item
                FROM mercurio10
                GROUP BY tipopc, numero
            ) last ON last.tipopc = p.tipopc AND last.numero = p.numero
            SET p.cerrada = 'S'{$setFeccie}
            WHERE p.estado = 'P'
              AND p.item <> last.max_item
              AND (p.cerrada IS NULL OR p.cerrada = '' OR p.cerrada = 'N')
        ", $bindings);

        $this->info("Actualizados (P previos): {$updatedP}");
    }

    /**
     * Si el último evento está en P, fuerza cerrada=N y sin feccie.
     */
    private function reabrirUltimosPendientes(bool $dryRun, bool $conFeccie): void
    {
        $feccieCond = $conFeccie ? ' OR p.feccie IS NOT NULL' : '';

        $countSql = "
            SELECT COUNT(*) AS aggregate
            FROM mercurio10 p
            INNER JOIN (
                SELECT tipopc, numero, MAX(item) AS max_item
                FROM mercurio10
                GROUP BY tipopc, numero
            ) last ON last.tipopc = p.tipopc
               AND last.numero = p.numero
               AND last.max_item = p.item
            WHERE p.estado = 'P'
              AND (p.cerrada = 'S'{$feccieCond})
        ";

        $count = (int) (DB::selectOne($countSql)->aggregate ?? 0);
        $this->info("Últimos P mal cerrados a reabrir: {$count}");

        if ($dryRun || $count === 0) {
            return;
        }

        $setFeccie = $conFeccie ? ', p.feccie = NULL' : '';

        $updated = DB::update("
            UPDATE mercurio10 p
            INNER JOIN (
                SELECT tipopc, numero, MAX(item) AS max_item
                FROM mercurio10
                GROUP BY tipopc, numero
            ) last ON last.tipopc = p.tipopc
               AND last.numero = p.numero
               AND last.max_item = p.item
            SET p.cerrada = 'N'{$setFeccie}
            WHERE p.estado = 'P'
              AND (p.cerrada = 'S'{$feccieCond})
        ");

        $this->info("Reabiertos (último P): {$updated}");
    }

    /**
     * Completa feccie en filas ya cerradas sin fecha (usa fecsis del propio evento).
     * No toca el último ítem si sigue en P.
     */
    private function completarFeccieEnCerrados(bool $dryRun): void
    {
        $countSql = '
            SELECT COUNT(*) AS aggregate
            FROM mercurio10 m
            INNER JOIN (
                SELECT tipopc, numero, MAX(item) AS max_item
                FROM mercurio10
                GROUP BY tipopc, numero
            ) last ON last.tipopc = m.tipopc AND last.numero = m.numero
            WHERE m.cerrada = \'S\'
              AND m.feccie IS NULL
              AND m.fecsis IS NOT NULL
              AND NOT (m.estado = \'P\' AND m.item = last.max_item)
        ';

        $count = (int) (DB::selectOne($countSql)->aggregate ?? 0);
        $this->info("Cerrados sin feccie a completar (con fecsis): {$count}");

        if ($dryRun || $count === 0) {
            return;
        }

        $updated = DB::update('
            UPDATE mercurio10 m
            INNER JOIN (
                SELECT tipopc, numero, MAX(item) AS max_item
                FROM mercurio10
                GROUP BY tipopc, numero
            ) last ON last.tipopc = m.tipopc AND last.numero = m.numero
            SET m.feccie = m.fecsis
            WHERE m.cerrada = \'S\'
              AND m.feccie IS NULL
              AND m.fecsis IS NOT NULL
              AND NOT (m.estado = \'P\' AND m.item = last.max_item)
        ');

        $this->info("Completados feccie desde fecsis: {$updated}");
    }
}
