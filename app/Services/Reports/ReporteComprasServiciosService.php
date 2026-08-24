<?php

namespace App\Services\Reports;

use App\Models\PrecompraServicio;
use App\Services\Ecommerce\EstadoPrecompra;
use Carbon\Carbon;

class ReporteComprasServiciosService
{
    public const DEFAULT_PER_PAGE = 25;

    public const MAX_PER_PAGE = 100;

    /**
     * @param  array{fecini: string, fecfin: string, estado?: string|null, page?: int, per_page?: int}  $filtros
     * @return array{total: int, rows: array<int, array<string, mixed>>, resumen: array<string, mixed>, pagination: array<string, mixed>}
     */
    public function consultar(array $filtros): array
    {
        $page = max(1, (int) ($filtros['page'] ?? 1));
        $perPage = (int) ($filtros['per_page'] ?? self::DEFAULT_PER_PAGE);
        $perPage = max(10, min(self::MAX_PER_PAGE, $perPage));

        $query = $this->baseQuery($filtros);
        $total = (clone $query)->count();
        $lastPage = max(1, (int) ceil($total / $perPage));
        $page = min($page, $lastPage);
        $offset = ($page - 1) * $perPage;

        $rows = (clone $query)
            ->offset($offset)
            ->limit($perPage)
            ->get()
            ->map(fn (PrecompraServicio $item) => $this->normalize($item))
            ->all();

        $from = $total === 0 ? 0 : $offset + 1;
        $to = $total === 0 ? 0 : min($offset + $perPage, $total);

        return [
            'total' => $total,
            'rows' => $rows,
            'resumen' => $this->resumen($filtros),
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'last_page' => $lastPage,
                'from' => $from,
                'to' => $to,
                'total' => $total,
            ],
        ];
    }

    /**
     * @param  array{fecini: string, fecfin: string, estado?: string|null}  $filtros
     */
    private function baseQuery(array $filtros)
    {
        $fecini = (string) ($filtros['fecini'] ?? '');
        $fecfin = (string) ($filtros['fecfin'] ?? '');
        $estado = isset($filtros['estado']) ? trim((string) $filtros['estado']) : '';

        $query = PrecompraServicio::query()
            ->with('ultimaTransaccionEpayco')
            ->whereBetween('fecha_precompra', [
                $fecini.' 00:00:00',
                Carbon::parse($fecfin)->endOfDay()->format('Y-m-d H:i:s'),
            ])
            ->orderByDesc('fecha_precompra')
            ->orderByDesc('id');

        if ($estado !== '') {
            $query->where('estado', $estado);
        }

        return $query;
    }

    /**
     * @param  array{fecini: string, fecfin: string, estado?: string|null}  $filtros
     * @return array{por_estado: array<string, int>, valor_total: float, valor_pagado: float}
     */
    private function resumen(array $filtros): array
    {
        $base = $this->baseQuery($filtros);

        $porEstadoRaw = (clone $base)
            ->reorder()
            ->selectRaw('estado, COUNT(*) as total')
            ->groupBy('estado')
            ->pluck('total', 'estado')
            ->all();

        $porEstado = [];
        foreach (EstadoPrecompra::DESCRIPCIONES as $codigo => $label) {
            $porEstado[$codigo] = (int) ($porEstadoRaw[$codigo] ?? 0);
        }
        foreach ($porEstadoRaw as $codigo => $total) {
            if (! isset($porEstado[$codigo])) {
                $porEstado[$codigo] = (int) $total;
            }
        }

        $valorTotal = (float) ((clone $base)->reorder()->sum('valor') ?? 0);
        $valorPagado = (float) ((clone $base)
            ->reorder()
            ->where('estado', EstadoPrecompra::PAGADO)
            ->sum('valor') ?? 0);

        return [
            'por_estado' => $porEstado,
            'valor_total' => round($valorTotal, 2),
            'valor_pagado' => round($valorPagado, 2),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function normalize(PrecompraServicio $item): array
    {
        $tx = $item->ultimaTransaccionEpayco;

        return [
            'id' => $item->id,
            'documento' => $item->documento,
            'codben' => $item->codben,
            'codser' => $item->codser,
            'numero' => $item->numero,
            'valor' => $item->valor !== null ? number_format((float) $item->valor, 2, '.', '') : null,
            'estado' => $item->estado,
            'estado_detalle' => EstadoPrecompra::descripcion((string) $item->estado),
            'ref_payco' => $item->ref_payco,
            'transaction_id' => $tx?->transaction_id,
            'approval_code' => $tx?->approval_code,
            'motivo_epayco' => $item->motivo_epayco,
            'motivo_desestimacion' => $item->motivo_desestimacion,
            'detalle_desestimacion' => $item->detalle_desestimacion,
            'fecha_precompra' => $item->fecha_precompra?->format('Y-m-d H:i'),
            'fecha_pago' => $item->fecha_pago?->format('Y-m-d H:i'),
            'fecha_desestimacion' => $item->fecha_desestimacion?->format('Y-m-d H:i'),
            'nota' => $item->nota,
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function estadosLabels(): array
    {
        return EstadoPrecompra::DESCRIPCIONES;
    }
}
