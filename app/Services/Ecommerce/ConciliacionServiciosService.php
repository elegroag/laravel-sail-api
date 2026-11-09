<?php

namespace App\Services\Ecommerce;

use App\Exceptions\DebugException;
use App\Models\EpaycoTransaccion;
use App\Models\PrecompraServicio;
use App\Services\Api\ApiSubsidio;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Cruza precompras pagadas (PA) y aceptadas ePayco no-PA con ventas de Subsidio.
 *
 * Conciliación (CONCILIADA / VALOR_DIFERENTE) exige las dos condiciones:
 * 1) venta.refpago === precompras_servicios.ref_payco
 * 2) existe epayco_transacciones.ref_payco igual para esa misma precompra_id
 * y además venta.estado === 'A' (aprobada).
 *
 * Solo coincidir por ePayco (sin igualdad en la precompra) no concilia.
 */
class ConciliacionServiciosService
{
    public const CONCILIADA = 'CONCILIADA';

    public const PAGADA_SIN_VENTA = 'PAGADA_SIN_VENTA';

    public const VALOR_DIFERENTE = 'VALOR_DIFERENTE';

    public const VENTA_SIN_PRECOMPRA_PA = 'VENTA_SIN_PRECOMPRA_PA';

    public const ACEPTADA_EPAYCO_NO_PA = 'ACEPTADA_EPAYCO_NO_PA';

    /** Venta y precompra coinciden en ref_payco, pero no hay tx ePayco con esa misma ref. */
    public const SIN_TX_EPAYCO = 'SIN_TX_EPAYCO';

    /** PA con venta (refs OK) pero servi232.estado distinto de A. */
    public const VENTA_NO_APROBADA = 'VENTA_NO_APROBADA';

    public const ESTADOS = [
        self::CONCILIADA => 'Conciliada',
        self::PAGADA_SIN_VENTA => 'Pagada sin venta',
        self::VALOR_DIFERENTE => 'Valor diferente',
        self::VENTA_SIN_PRECOMPRA_PA => 'Venta sin precompra PA',
        self::ACEPTADA_EPAYCO_NO_PA => 'Aceptada ePayco (no PA)',
        self::SIN_TX_EPAYCO => 'Sin tx ePayco coincidente',
        self::VENTA_NO_APROBADA => 'Venta no aprobada',
    ];

    /** Estado de venta aprobada en Subsidio (servi232). */
    public const VENTA_ESTADO_APROBADA = 'A';

    private const TOLERANCIA_VALOR = 0.01;

    private const PER_PAGE = 500;

    /** Código ePayco de transacción aceptada. */
    public const EPAYCO_ACEPTADA = '1';

    public function __construct(
        protected ?ApiSubsidio $api = null
    ) {}

    protected function api(): ApiSubsidio
    {
        return $this->api ??= new ApiSubsidio;
    }

    /**
     * @return array{filas: list<array<string, mixed>>, resumen: array<string, int>}
     */
    public function conciliar(string $desde, string $hasta): array
    {
        $desdeCarbon = Carbon::createFromFormat('Y-m-d', $desde)->startOfDay();
        $hastaCarbon = Carbon::createFromFormat('Y-m-d', $hasta)->endOfDay();

        $precomprasPa = PrecompraServicio::query()
            ->where('estado', EstadoPrecompra::PAGADO)
            ->where(function ($q) {
                $q->where(function ($q2) {
                    $q2->whereNotNull('ref_payco')->where('ref_payco', '!=', '');
                })->orWhereHas('transaccionesEpayco', function ($tx) {
                    $tx->where('cod_estado', self::EPAYCO_ACEPTADA)
                        ->whereNotNull('ref_payco')
                        ->where('ref_payco', '!=', '');
                });
            })
            ->whereBetween('fecha_pago', [$desdeCarbon, $hastaCarbon])
            ->orderBy('fecha_pago')
            ->get();

        $precomprasAceptadasNoPa = PrecompraServicio::query()
            ->where('estado', '!=', EstadoPrecompra::PAGADO)
            ->whereNotIn('estado', [EstadoPrecompra::VENCIDA, EstadoPrecompra::DEVOLUCION])
            ->whereBetween('fecha_precompra', [$desdeCarbon, $hastaCarbon])
            ->whereHas('transaccionesEpayco', function ($q) {
                $q->where('cod_estado', self::EPAYCO_ACEPTADA)
                    ->whereNotNull('ref_payco')
                    ->where('ref_payco', '!=', '');
            })
            ->orderBy('fecha_precompra')
            ->get();

        // Ampliar 1 día a cada lado por desfases de medianoche / zona horaria.
        $apiDesde = $desdeCarbon->copy()->subDay()->toDateString();
        $apiHasta = $hastaCarbon->copy()->addDay()->toDateString();
        $ventas = $this->cargarVentasSubsidio($apiDesde, $apiHasta);

        $ventasPorRef = [];
        foreach ($ventas as $venta) {
            $ref = trim((string) ($venta['refpago'] ?? ''));
            if ($ref === '') {
                continue;
            }
            $ventasPorRef[$ref] = $venta;
        }

        $universo = $precomprasPa->concat($precomprasAceptadasNoPa)->unique('id')->values();
        $refsEpaycoPorPrecompraId = $this->cargarRefsEpaycoPorPrecompraIds(
            $universo->pluck('id')->filter()->all()
        );

        // Índice solo por ref de la precompra (no por refs alternativas de ePayco).
        $precomprasPorRef = $this->indexarPrecomprasPorRefPropia($universo);

        $refsLocales = collect(array_keys($precomprasPorRef->all()));
        $refsVenta = collect(array_keys($ventasPorRef));
        $refsExtra = $refsVenta->diff($refsLocales)->values();

        if ($refsExtra->isNotEmpty()) {
            foreach ($this->resolverPrecomprasPorRefPropia($refsExtra->all()) as $ref => $precompra) {
                if (! $precomprasPorRef->has($ref)) {
                    $precomprasPorRef->put($ref, $precompra);
                }
            }
        }

        return $this->clasificar(
            $precomprasPa,
            $ventasPorRef,
            $precomprasPorRef,
            $precomprasAceptadasNoPa,
            $refsEpaycoPorPrecompraId
        );
    }

    /**
     * @param  Collection<int, PrecompraServicio>  $precomprasPa
     * @param  array<string, array<string, mixed>>  $ventasPorRefpago
     * @param  Collection<string, PrecompraServicio>  $precomprasPorRef
     * @param  Collection<int, PrecompraServicio>|null  $precomprasAceptadasNoPa
     * @param  array<int, list<string>>  $refsEpaycoPorPrecompraId
     * @return array{filas: list<array<string, mixed>>, resumen: array<string, int>}
     */
    public function clasificar(
        Collection $precomprasPa,
        array $ventasPorRefpago,
        Collection $precomprasPorRef,
        ?Collection $precomprasAceptadasNoPa = null,
        array $refsEpaycoPorPrecompraId = []
    ): array {
        $filas = [];
        $refsProcesados = [];
        $precomprasAceptadasNoPa ??= collect();

        foreach ($precomprasPa as $precompra) {
            $ref = $this->refPropia($precompra);
            if ($ref === '') {
                $filas[] = $this->fila(self::PAGADA_SIN_VENTA, $precompra, null);
                continue;
            }

            $refsProcesados[$ref] = true;
            $venta = $ventasPorRefpago[$ref] ?? null;

            if ($venta === null) {
                $filas[] = $this->fila(self::PAGADA_SIN_VENTA, $precompra, null);
                continue;
            }

            $filas[] = $this->fila(
                $this->estadoPareja($precompra, $venta, $refsEpaycoPorPrecompraId),
                $precompra,
                $venta
            );
        }

        foreach ($precomprasAceptadasNoPa as $precompra) {
            $ref = $this->refPropia($precompra);
            if ($ref === '') {
                $filas[] = $this->fila(self::ACEPTADA_EPAYCO_NO_PA, $precompra, null);
                continue;
            }
            if (isset($refsProcesados[$ref])) {
                continue;
            }

            $venta = $ventasPorRefpago[$ref] ?? null;
            $refsProcesados[$ref] = true;
            $filas[] = $this->fila(self::ACEPTADA_EPAYCO_NO_PA, $precompra, $venta);
        }

        foreach ($ventasPorRefpago as $ref => $venta) {
            if (isset($refsProcesados[$ref])) {
                continue;
            }

            $precompra = $precomprasPorRef->get($ref);

            // Omitir solo si precompra VE/DV y la venta no está aprobada (estado != A).
            if (
                $precompra
                && ($precompra->isVencida() || $precompra->isDevolucion())
                && ! $this->ventaEstaAprobada($venta)
            ) {
                $refsProcesados[$ref] = true;
                continue;
            }

            // PA con misma ref_payco de precompra: aplicar ambas condiciones (no omitir).
            if ($precompra && $precompra->isPagado()) {
                $filas[] = $this->fila(
                    $this->estadoPareja($precompra, $venta, $refsEpaycoPorPrecompraId),
                    $precompra,
                    $venta
                );
                $refsProcesados[$ref] = true;
                continue;
            }

            // VE/DV con venta aprobada: reportar sin enlazar la precompra excluida.
            if ($precompra && ($precompra->isVencida() || $precompra->isDevolucion())) {
                $precompra = null;
            }

            $filas[] = $this->fila(self::VENTA_SIN_PRECOMPRA_PA, $precompra, $venta);
            $refsProcesados[$ref] = true;
        }

        $resumen = [
            self::CONCILIADA => 0,
            self::PAGADA_SIN_VENTA => 0,
            self::VALOR_DIFERENTE => 0,
            self::VENTA_SIN_PRECOMPRA_PA => 0,
            self::ACEPTADA_EPAYCO_NO_PA => 0,
            self::SIN_TX_EPAYCO => 0,
            self::VENTA_NO_APROBADA => 0,
        ];
        foreach ($filas as $fila) {
            $resumen[$fila['estado']] = ($resumen[$fila['estado']] ?? 0) + 1;
        }

        return [
            'filas' => $filas,
            'resumen' => $resumen,
        ];
    }

    /**
     * @param  list<int|string>  $precompraIds
     * @return array<int, list<string>>
     */
    protected function cargarRefsEpaycoPorPrecompraIds(array $precompraIds): array
    {
        $precompraIds = array_values(array_unique(array_filter(array_map('intval', $precompraIds))));
        if ($precompraIds === []) {
            return [];
        }

        $map = [];
        EpaycoTransaccion::query()
            ->whereIn('precompra_id', $precompraIds)
            ->whereNotNull('ref_payco')
            ->where('ref_payco', '!=', '')
            ->orderBy('id')
            ->get(['precompra_id', 'ref_payco'])
            ->each(function (EpaycoTransaccion $tx) use (&$map) {
                $pid = (int) $tx->precompra_id;
                $ref = trim((string) $tx->ref_payco);
                if ($ref === '') {
                    return;
                }
                $map[$pid] ??= [];
                if (! in_array($ref, $map[$pid], true)) {
                    $map[$pid][] = $ref;
                }
            });

        return $map;
    }

    /**
     * Solo resuelve por precompras_servicios.ref_payco (nunca por ePayco).
     *
     * @param  list<string>  $refs
     * @return array<string, PrecompraServicio>
     */
    protected function resolverPrecomprasPorRefPropia(array $refs): array
    {
        $refs = array_values(array_unique(array_filter(array_map(
            static fn ($r) => trim((string) $r),
            $refs
        ))));
        if ($refs === []) {
            return [];
        }

        $porRef = [];
        PrecompraServicio::query()
            ->whereIn('ref_payco', $refs)
            ->get()
            ->each(function (PrecompraServicio $p) use (&$porRef) {
                $ref = trim((string) $p->ref_payco);
                if ($ref !== '' && ! isset($porRef[$ref])) {
                    $porRef[$ref] = $p;
                }
            });

        return $porRef;
    }

    /**
     * @param  Collection<int, PrecompraServicio>  $precompras
     * @return Collection<string, PrecompraServicio>
     */
    protected function indexarPrecomprasPorRefPropia(Collection $precompras): Collection
    {
        $map = collect();
        foreach ($precompras as $precompra) {
            $ref = $this->refPropia($precompra);
            if ($ref !== '' && ! $map->has($ref)) {
                $map->put($ref, $precompra);
            }
        }

        return $map;
    }

    protected function refPropia(PrecompraServicio $precompra): string
    {
        return trim((string) ($precompra->ref_payco ?? ''));
    }

    /**
     * @param  array<int, list<string>>  $refsEpaycoPorPrecompraId
     */
    protected function tieneTxEpaycoConRef(PrecompraServicio $precompra, string $ref, array $refsEpaycoPorPrecompraId): bool
    {
        $ref = trim($ref);
        if ($ref === '') {
            return false;
        }

        return in_array($ref, $refsEpaycoPorPrecompraId[(int) $precompra->id] ?? [], true);
    }

    /**
     * @param  array<string, mixed>  $venta
     * @param  array<int, list<string>>  $refsEpaycoPorPrecompraId
     */
    protected function estadoPareja(
        PrecompraServicio $precompra,
        array $venta,
        array $refsEpaycoPorPrecompraId
    ): string {
        $ref = $this->refPropia($precompra);
        $refVenta = trim((string) ($venta['refpago'] ?? ''));

        // Condición 1: la venta debe coincidir con la ref de la precompra.
        if ($ref === '' || $ref !== $refVenta) {
            return self::PAGADA_SIN_VENTA;
        }

        // Condición 2: debe existir la misma ref en epayco_transacciones.
        if (! $this->tieneTxEpaycoConRef($precompra, $ref, $refsEpaycoPorPrecompraId)) {
            return self::SIN_TX_EPAYCO;
        }

        // Condición 3: la venta en Subsidio debe estar aprobada.
        if (! $this->ventaEstaAprobada($venta)) {
            return self::VENTA_NO_APROBADA;
        }

        return $this->estadoValor($precompra, $venta);
    }

    /**
     * @param  array<string, mixed>  $venta
     */
    protected function ventaEstaAprobada(array $venta): bool
    {
        return strtoupper(trim((string) ($venta['estado'] ?? ''))) === self::VENTA_ESTADO_APROBADA;
    }

    /**
     * @param  array<string, mixed>  $venta
     */
    protected function estadoValor(PrecompraServicio $precompra, array $venta): string
    {
        $valorPre = (float) $precompra->valor;
        $valorVenta = (float) ($venta['valor_forma_pago'] ?? $venta['valpago'] ?? 0);

        return abs($valorPre - $valorVenta) < self::TOLERANCIA_VALOR
            ? self::CONCILIADA
            : self::VALOR_DIFERENTE;
    }

    /**
     * @return list<array<string, mixed>>
     */
    protected function cargarVentasSubsidio(string $desde, string $hasta): array
    {
        $todas = [];
        $page = 1;
        $paginas = 1;

        do {
            $this->api()->send([
                'servicio' => 'Movil',
                'metodo' => 'conciliacion-ventas',
                'params' => [
                    'fecha_inicio' => $desde,
                    'fecha_fin' => $hasta,
                    'page' => $page,
                    'per_page' => self::PER_PAGE,
                ],
            ]);

            $resultado = $this->api()->toArray();
            if (! is_array($resultado) || ! ($resultado['flag'] ?? false)) {
                $msg = is_array($resultado)
                    ? (string) ($resultado['msg'] ?? $resultado['message'] ?? 'Error al consultar Subsidio')
                    : 'Respuesta inválida de Subsidio';

                throw new DebugException($msg, 502);
            }

            $lote = $resultado['data'] ?? [];
            if (! is_array($lote)) {
                throw new DebugException('Respuesta de conciliacion-ventas sin data válida', 502);
            }

            foreach ($lote as $fila) {
                if (is_array($fila)) {
                    $todas[] = $fila;
                }
            }

            $paginas = max(1, (int) ($resultado['paginas'] ?? 1));
            $page++;
        } while ($page <= $paginas);

        return $todas;
    }

    /**
     * @param  array<string, mixed>|null  $venta
     * @return array<string, mixed>
     */
    protected function fila(string $estado, ?PrecompraServicio $precompra, ?array $venta): array
    {
        $refVenta = $venta ? trim((string) ($venta['refpago'] ?? '')) : '';
        $refPre = $precompra ? trim((string) ($precompra->ref_payco ?? '')) : '';

        return [
            'estado' => $estado,
            'estado_descripcion' => self::ESTADOS[$estado] ?? $estado,
            'precompra_id' => $precompra?->id,
            'documento' => $precompra ? (string) $precompra->documento : (string) ($venta['cedtra_titular'] ?? ''),
            'codser' => $precompra ? (string) $precompra->codser : '',
            'numero' => $precompra ? (string) $precompra->numero : '',
            'codben' => $precompra ? (string) ($precompra->codben ?? '') : '',
            'ref_payco' => $refVenta !== '' ? $refVenta : $refPre,
            'ref_payco_precompra' => $refPre !== '' ? $refPre : null,
            'valor_precompra' => $precompra !== null ? (float) $precompra->valor : null,
            'fecha_pago' => $precompra?->fecha_pago?->format('Y-m-d H:i'),
            'fecha_precompra' => $precompra?->fecha_precompra?->format('Y-m-d H:i'),
            'estado_precompra' => $precompra?->estado,
            'estado_precompra_desc' => $precompra?->estado_descripcion,
            'venta_marca' => $venta ? trim((string) ($venta['marca'] ?? '')) : null,
            'venta_documento' => $venta ? trim((string) ($venta['documento'] ?? '')) : null,
            'venta_estado' => $venta ? strtoupper(trim((string) ($venta['estado'] ?? ''))) : null,
            'venta_fecha' => $venta ? trim((string) ($venta['fecha'] ?? '')) : null,
            'venta_valpago' => $venta !== null ? (float) ($venta['valpago'] ?? 0) : null,
            'venta_valor_forma_pago' => $venta !== null ? (float) ($venta['valor_forma_pago'] ?? 0) : null,
            'puede_registrar' => in_array($estado, [self::PAGADA_SIN_VENTA, self::ACEPTADA_EPAYCO_NO_PA], true)
                && $precompra !== null
                && $venta === null,
        ];
    }
}
