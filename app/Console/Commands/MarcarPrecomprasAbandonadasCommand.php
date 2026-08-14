<?php

namespace App\Console\Commands;

use App\Jobs\MarcarPrecomprasAbandonadas;
use Illuminate\Console\Command;

class MarcarPrecomprasAbandonadasCommand extends Command
{
    protected $signature = 'precompras:marcar-abandonadas
                            {--dias=7 : Dias de antiguedad de PE para marcar como AB}
                            {--dry-run : Solo cuenta candidatas, no escribe}
                            {--queue : Encola el job en lugar de ejecutarlo en sincronico}';

    protected $description = 'Marca como abandonadas (AB) las precompras pendientes (PE) antiguas';

    public function handle(): int
    {
        $dias = max(1, (int) $this->option('dias'));
        $dryRun = (bool) $this->option('dry-run');
        $queue = (bool) $this->option('queue');

        if ($dryRun) {
            $this->warn('Modo simulación (--dry-run): no se escribirá en la base de datos.');
        }

        $this->info("TTL: {$dias} dia(s). Estado origen: PE → AB.");

        if ($queue && ! $dryRun) {
            MarcarPrecomprasAbandonadas::dispatch($dias, false);
            $this->info('Job encolado: MarcarPrecomprasAbandonadas.');

            return self::SUCCESS;
        }

        $resultado = (new MarcarPrecomprasAbandonadas($dias, $dryRun))->handle();

        if ($dryRun) {
            $this->info("Candidatas a abandonar: {$resultado}");
        } else {
            $this->info("Precompras actualizadas a AB: {$resultado}");
        }

        return self::SUCCESS;
    }
}
