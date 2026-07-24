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
            $updated = $queryRespuestas->update(['cerrada' => 'S']);
            $this->info("Actualizados (respuestas): {$updated}");
        }

        if ($this->option('cerrar-pendientes')) {
            $this->cerrarPendientesAsociados($estados, $dryRun);
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
    private function cerrarPendientesAsociados(array $estados, bool $dryRun): void
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

        $updatedP = DB::update("
            UPDATE mercurio10 p
            INNER JOIN (
                SELECT DISTINCT tipopc, numero
                FROM mercurio10
                WHERE estado IN ({$placeholders})
            ) r ON r.tipopc = p.tipopc AND r.numero = p.numero
            SET p.cerrada = 'S'
            WHERE p.estado = 'P'
              AND (p.cerrada IS NULL OR p.cerrada = '' OR p.cerrada = 'N')
        ", $estados);

        $this->info("Actualizados (P): {$updatedP}");
    }
}
