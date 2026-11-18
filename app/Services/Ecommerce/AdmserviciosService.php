<?php

namespace App\Services\Ecommerce;

use App\Models\PrecompraServicio;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class AdmserviciosService
{
    /**
     * Filtros de consulta del listado administrativo.
     *
     * @return array{estado: string, documento: string, codser: string, numero: string, codben: string, ref_payco: string, q: string, per_page: int}
     */
    public function filtersFromRequest(Request $request): array
    {
        $estado = trim((string) $request->query('estado', ''));
        if ($estado !== '' && ! array_key_exists($estado, EstadoPrecompra::DESCRIPCIONES)) {
            $estado = '';
        }

        return [
            'estado' => $estado,
            'documento' => trim((string) $request->query('documento', '')),
            'codser' => trim((string) $request->query('codser', '')),
            'numero' => trim((string) $request->query('numero', '')),
            'codben' => trim((string) $request->query('codben', '')),
            'ref_payco' => trim((string) $request->query('ref_payco', '')),
            'q' => trim((string) $request->query('q', '')),
            'per_page' => max(1, min(100, (int) $request->query('per_page', 10))),
        ];
    }

    /**
     * @return LengthAwarePaginator<int, PrecompraServicio>
     */
    public function paginate(Request $request): LengthAwarePaginator
    {
        $filters = $this->filtersFromRequest($request);

        return $this->baseQuery($filters)
            ->paginate($filters['per_page'])
            ->appends($request->only([
                'estado',
                'documento',
                'codser',
                'numero',
                'codben',
                'ref_payco',
                'q',
                'per_page',
            ]));
    }

    /**
     * @return Collection<int, PrecompraServicio>
     */
    public function allForReport(Request $request): Collection
    {
        return $this->baseQuery($this->filtersFromRequest($request))->get();
    }

    /**
     * @param  array{estado: string, documento: string, codser: string, numero: string, codben: string, ref_payco: string, q: string, per_page?: int}  $filters
     * @return Builder<PrecompraServicio>
     */
    public function baseQuery(array $filters): Builder
    {
        $builder = PrecompraServicio::query()
            ->with('ultimaTransaccionEpayco')
            ->orderByDesc('id');

        if (($filters['estado'] ?? '') !== '') {
            $builder->where('estado', $filters['estado']);
        }

        if (($filters['documento'] ?? '') !== '') {
            $builder->where('documento', 'like', $this->like($filters['documento']));
        }

        if (($filters['codser'] ?? '') !== '') {
            $builder->where('codser', 'like', $this->like($filters['codser']));
        }

        if (($filters['numero'] ?? '') !== '') {
            $builder->where('numero', 'like', $this->like($filters['numero']));
        }

        if (($filters['codben'] ?? '') !== '') {
            $builder->where('codben', 'like', $this->like($filters['codben']));
        }

        if (($filters['ref_payco'] ?? '') !== '') {
            $builder->where('ref_payco', 'like', $this->like($filters['ref_payco']));
        }

        $q = $filters['q'] ?? '';
        if ($q !== '') {
            $like = $this->like($q);
            $builder->where(function (Builder $sub) use ($like) {
                $sub->where('documento', 'like', $like)
                    ->orWhere('codser', 'like', $like)
                    ->orWhere('numero', 'like', $like)
                    ->orWhere('codben', 'like', $like)
                    ->orWhere('ref_payco', 'like', $like)
                    ->orWhere('id', 'like', $like);
            });
        }

        return $builder;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function mapRows(iterable $precompras): array
    {
        $rows = [];
        foreach ($precompras as $precompra) {
            $rows[] = $this->mapRow($precompra);
        }

        return $rows;
    }

    /**
     * @return array<string, mixed>
     */
    public function mapRow(PrecompraServicio $precompra): array
    {
        $tx = $precompra->ultimaTransaccionEpayco;

        return [
            'id' => (int) $precompra->id,
            'documento' => (string) ($precompra->documento ?? ''),
            'codben' => (string) ($precompra->codben ?? ''),
            'codser' => (string) ($precompra->codser ?? ''),
            'numero' => (string) ($precompra->numero ?? ''),
            'valor' => (float) $precompra->valor,
            'estado' => (string) ($precompra->estado ?? ''),
            'estado_descripcion' => (string) $precompra->estado_descripcion,
            'ref_payco' => (string) ($precompra->ref_payco ?? ''),
            'transaction_id' => $tx?->transaction_id ? (string) $tx->transaction_id : null,
            'approval_code' => $tx?->approval_code ? (string) $tx->approval_code : null,
            'fecha_precompra' => optional($precompra->fecha_precompra)->format('Y-m-d H:i'),
            'fecha_pago' => optional($precompra->fecha_pago)->format('Y-m-d H:i'),
            'motivo' => $this->motivoTexto($precompra),
            'ver_url' => route('admservicios.ver', ['id' => $precompra->id]),
        ];
    }

    public function motivoTexto(PrecompraServicio $precompra): string
    {
        $motivoRaw = trim((string) ($precompra->motivo_desestimacion ?? ''));
        if ($motivoRaw === '') {
            return '';
        }

        if ($motivoRaw === EstadoPrecompra::MOTIVO_ABANDONO_CHECKOUT) {
            $motivo = 'Abandono del checkout ePayco';
        } elseif ($motivoRaw === EstadoPrecompra::MOTIVO_ABANDONO_INACTIVIDAD) {
            $motivo = 'Abandono por inactividad';
        } else {
            $motivo = EstadoPrecompra::MOTIVOS_DESESTIMACION[$motivoRaw] ?? $motivoRaw;
        }

        $detalle = trim((string) ($precompra->detalle_desestimacion ?? ''));
        if ($detalle !== '') {
            $motivo .= ' - '.$detalle;
        }

        return $motivo;
    }

    private function like(string $value): string
    {
        return '%'.str_replace(['%', '_'], ['\%', '\_'], $value).'%';
    }
}
