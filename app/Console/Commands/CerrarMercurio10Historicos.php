<?php

namespace App\Console\Commands;

use App\Models\Mercurio10;
use App\Services\Utils\Mercurio10Cierre;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CerrarMercurio10Historicos extends Command
{
    protected $signature = 'mercurio10:cerrar-historicos
                            {--dry-run : Solo cuenta, no escribe}
                            {--estados= : Estados de respuesta a marcar (por defecto A,X,D)}
                            {--cerrar-pendientes : También cierra eventos P de esas solicitudes}';

    protected $description = 'Marca cerrada=S en Mercurio10 históricos (A/X/D) y opcionalmente sus P abiertos';

    public function handle(): int
    {
        if (! Schema::hasTable('mercurio10') || ! Schema::hasColumn('mercurio10', 'cerrada')) {
            $this->error('La tabla mercurio10 no existe o no tiene la columna cerrada. Ejecuta las migraciones primero.');

            return self::FAILURE;
        }

        $dryRun = (bool) $this->option('dry-run');
        $estados = $this->resolverEstados();
        $hoy = Mercurio10Cierre::fechaCierreHoy();
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
            return Mercurio10Cierre::ESTADOS_CIERRE;
        }

        return collect(explode(',', (string) $raw))
            ->map(fn ($e) => strtoupper(trim($e)))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    /**
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
            WHERE p.estado = 'P'
              AND (p.cerrada IS NULL OR p.cerrada = '' OR p.cerrada = 'N')
        ";

        $countP = (int) (DB::selectOne($countSql, $estados)->aggregate ?? 0);
        $this->info("Pendientes P asociados a cerrar: {$countP}");

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
            SET p.cerrada = 'S'{$setFeccie}
            WHERE p.estado = 'P'
              AND (p.cerrada IS NULL OR p.cerrada = '' OR p.cerrada = 'N')
        ", $bindings);

        $this->info("Actualizados (P): {$updatedP}");
    }

    /**
     * Completa feccie en filas ya cerradas sin fecha (usa fecsis del propio evento).
     */
    private function completarFeccieEnCerrados(bool $dryRun): void
    {
        $query = Mercurio10::query()
            ->where('cerrada', 'S')
            ->whereNull('feccie')
            ->whereNotNull('fecsis');

        $count = (clone $query)->count();
        $this->info("Cerrados sin feccie a completar (con fecsis): {$count}");

        if ($dryRun || $count === 0) {
            return;
        }

        $updated = DB::update('
            UPDATE mercurio10
            SET feccie = fecsis
            WHERE cerrada = \'S\'
              AND feccie IS NULL
              AND fecsis IS NOT NULL
        ');

        $this->info("Completados feccie desde fecsis: {$updated}");
    }
}
