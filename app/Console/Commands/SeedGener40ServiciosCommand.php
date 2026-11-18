<?php

namespace App\Console\Commands;

use App\Models\Gener40;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SeedGener40ServiciosCommand extends Command
{
    protected $signature = 'cajas:gener40-seed-servicios
                            {--dry-run : Solo muestra el resumen sin guardar}
                            {--start-codigo= : Código numérico inicial (por defecto max+1)}';

    protected $description = 'Crea acciones Gener40 para conciliacion-servicios, admservicios y reporte-compras-servicios (idempotente por controller+action)';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $definitions = $this->definitions();
        $nextCodigo = $this->resolveStartCodigo();

        $stats = [
            'definidas' => count($definitions),
            'creadas' => 0,
            'omitidas' => 0,
        ];
        $rows = [];

        DB::beginTransaction();
        try {
            foreach ($definitions as $def) {
                $exists = Gener40::query()
                    ->where('controller', $def['controller'])
                    ->where('action', $def['action'])
                    ->first();

                if ($exists) {
                    $stats['omitidas']++;
                    $rows[] = [
                        (string) $exists->codigo,
                        $def['detalle'],
                        $def['controller'],
                        $def['action'],
                        'ya existe',
                    ];

                    continue;
                }

                $codigo = (string) $nextCodigo;
                if (strlen($codigo) > 5) {
                    DB::rollBack();
                    $this->error("El código {$codigo} supera el máximo de 5 caracteres.");

                    return self::FAILURE;
                }

                while (Gener40::query()->where('codigo', $codigo)->exists()) {
                    $nextCodigo++;
                    $codigo = (string) $nextCodigo;
                    if (strlen($codigo) > 5) {
                        DB::rollBack();
                        $this->error("No hay códigos libres de máximo 5 caracteres a partir de {$codigo}.");

                        return self::FAILURE;
                    }
                }

                if (! $dryRun) {
                    Gener40::create([
                        'codigo' => $codigo,
                        'detalle' => $def['detalle'],
                        'controller' => $def['controller'],
                        'action' => $def['action'],
                        'orden' => $nextCodigo,
                    ]);
                }

                $stats['creadas']++;
                $rows[] = [
                    $codigo,
                    $def['detalle'],
                    $def['controller'],
                    $def['action'],
                    $dryRun ? 'a crear' : 'creado',
                ];
                $nextCodigo++;
            }

            if ($dryRun) {
                DB::rollBack();
            } else {
                DB::commit();
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error('Error al crear acciones: '.$e->getMessage());

            return self::FAILURE;
        }

        if ($rows !== []) {
            $this->table(['Código', 'Detalle', 'Controller', 'Action', 'Estado'], $rows);
        }

        $this->newLine();
        $this->info($dryRun ? 'Simulación (dry-run) completada.' : 'Seed de acciones de servicios completado.');
        $this->table(
            ['Métrica', 'Cantidad'],
            [
                ['Definidas en mapa', $stats['definidas']],
                ['Omitidas (ya existen)', $stats['omitidas']],
                [$dryRun ? 'A crear' : 'Creadas', $stats['creadas']],
            ]
        );

        return self::SUCCESS;
    }

    private function resolveStartCodigo(): int
    {
        $option = $this->option('start-codigo');
        if ($option !== null && $option !== '') {
            return max(1, (int) $option);
        }

        $max = (int) Gener40::query()
            ->selectRaw('MAX(CAST(codigo AS UNSIGNED)) as max_codigo')
            ->value('max_codigo');

        return max(1, $max + 1);
    }

    /**
     * Acciones de reportes / admin de ventas de servicios.
     *
     * @return list<array{detalle: string, controller: string, action: string}>
     */
    private function definitions(): array
    {
        return [
            // cajas/conciliacion-servicios
            [
                'detalle' => 'INDEX CONCILIACION SERVICIOS',
                'controller' => 'ConciliacionServiciosController',
                'action' => 'index',
            ],
            [
                'detalle' => 'REPORTE CONCILIACION SERVICIOS',
                'controller' => 'ConciliacionServiciosController',
                'action' => 'reporte',
            ],

            // cajas/admservicios
            [
                'detalle' => 'INDEX ADMSERVICIOS',
                'controller' => 'AdmserviciosController',
                'action' => 'index',
            ],
            [
                'detalle' => 'VER ADMSERVICIOS',
                'controller' => 'AdmserviciosController',
                'action' => 'ver',
            ],
            [
                'detalle' => 'DETALLE ADMSERVICIOS',
                'controller' => 'AdmserviciosController',
                'action' => 'detalle',
            ],
            [
                'detalle' => 'REGISTRAR SUBSIDIO ADMSERVICIOS',
                'controller' => 'AdmserviciosController',
                'action' => 'registrarSubsidio',
            ],
            [
                'detalle' => 'MARCAR VENCIDA ADMSERVICIOS',
                'controller' => 'AdmserviciosController',
                'action' => 'marcarVencida',
            ],
            [
                'detalle' => 'DEVOLUCION EN TRAMITE ADMSERVICIOS',
                'controller' => 'AdmserviciosController',
                'action' => 'devolucionEnTramite',
            ],
            [
                'detalle' => 'DEVOLUCION APROBAR ADMSERVICIOS',
                'controller' => 'AdmserviciosController',
                'action' => 'devolucionAprobar',
            ],
            [
                'detalle' => 'DEVOLUCION RECHAZAR ADMSERVICIOS',
                'controller' => 'AdmserviciosController',
                'action' => 'devolucionRechazar',
            ],
            [
                'detalle' => 'REPORTE ADMSERVICIOS',
                'controller' => 'AdmserviciosController',
                'action' => 'reporte',
            ],

            // cajas/reporte-compras-servicios
            [
                'detalle' => 'INDEX REPORTE COMPRAS SERVICIOS',
                'controller' => 'ReporteComprasServiciosController',
                'action' => 'index',
            ],
            [
                'detalle' => 'EXPORTAR REPORTE COMPRAS SERVICIOS',
                'controller' => 'ReporteComprasServiciosController',
                'action' => 'exportar',
            ],
        ];
    }
}
