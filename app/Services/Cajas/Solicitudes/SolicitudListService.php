<?php

namespace App\Services\Cajas\Solicitudes;

use App\Services\Utils\CalculatorDias;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Listado paginado de solicitudes de afiliación por estado (patrón común de los módulos Aprueba*).
 */
abstract class SolicitudListService
{
    public const ESTADOS = [
        'P' => 'Pendientes',
        'D' => 'Devueltas',
        'X' => 'Rechazadas',
        'A' => 'Aprobadas',
        'T' => 'Temporales',
    ];

    public const PER_PAGE_OPTIONS = [10, 25, 50, 100];

    /**
     * @return class-string<Model>
     */
    abstract protected function modelClass(): string;

    abstract protected function tipopc(): string;

    /**
     * @return list<string>
     */
    abstract protected function searchColumns(): array;

    abstract protected function showUrl(Model $row): string;

    /**
     * @return array<string, mixed>
     */
    abstract protected function extraRow(Model $row): array;

    /**
     * @return array{estado: string, q: string, per_page: int}
     */
    public function filtersFromRequest(Request $request): array
    {
        $estado = strtoupper((string) $request->query('estado', 'P'));
        if (! array_key_exists($estado, self::ESTADOS)) {
            $estado = 'P';
        }

        $perPage = (int) $request->query('per_page', 10);
        if (! in_array($perPage, self::PER_PAGE_OPTIONS, true)) {
            $perPage = 10;
        }

        return [
            'estado' => $estado,
            'q' => trim((string) $request->query('q', '')),
            'per_page' => $perPage,
        ];
    }

    /**
     * @param  array{estado: string, q: string, per_page: int}  $filters
     */
    public function paginate(array $filters, string $usuario): LengthAwarePaginator
    {
        return $this->baseQuery($filters, $usuario)
            ->paginate($filters['per_page'])
            ->withQueryString();
    }

    /**
     * @return array<string, int>
     */
    public function counts(string $usuario): array
    {
        $model = $this->modelClass();
        $raw = $model::query()
            ->where(function (Builder $q) use ($usuario) {
                $q->where('usuario', $usuario)->orWhere('estado', 'T');
            })
            ->selectRaw('estado, COUNT(*) as total')
            ->groupBy('estado')
            ->pluck('total', 'estado');

        $counts = [];
        foreach (array_keys(self::ESTADOS) as $estado) {
            $counts[$estado] = (int) ($raw[$estado] ?? 0);
        }

        return $counts;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function mapRows(LengthAwarePaginator $paginator): array
    {
        return collect($paginator->items())
            ->map(fn (Model $row) => $this->mapRow($row))
            ->values()
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    public function mapRow(Model $row): array
    {
        $estado = (string) $row->getAttribute('estado');
        $dias = $this->diasHabiles($row);

        return array_merge([
            'id' => (int) $row->getKey(),
            'estado' => $estado,
            'estado_detalle' => estado_detalle_value($estado) ?? $estado,
            'fecsol' => $this->fecha($row->getAttribute('fecsol')),
            'dias' => $dias,
            'alerta' => $estado === 'P' ? ($dias > 3 ? 'danger' : ($dias === 3 ? 'warning' : 'normal')) : 'normal',
            'show_url' => $this->showUrl($row),
        ], $this->extraRow($row));
    }

    /**
     * @param  array{estado: string, q: string, per_page: int}  $filters
     */
    protected function baseQuery(array $filters, string $usuario): Builder
    {
        $model = $this->modelClass();
        $query = $model::query()->where('estado', $filters['estado']);

        if ($filters['estado'] !== 'T') {
            $query->where('usuario', $usuario);
        }

        if ($filters['q'] !== '') {
            $like = '%'.addcslashes($filters['q'], '%_\\').'%';
            $query->where(function (Builder $q) use ($like) {
                foreach ($this->searchColumns() as $column) {
                    $q->orWhere($column, 'like', $like);
                }
            });
        }

        return $query
            ->orderBy('fecsol', $filters['estado'] === 'P' ? 'asc' : 'desc')
            ->orderBy($query->getModel()->getKeyName(), 'asc');
    }

    protected function diasHabiles(Model $row): int
    {
        try {
            return (int) CalculatorDias::calcular($this->tipopc(), $row->getKey(), $row->getAttribute('fecsol'));
        } catch (\Throwable) {
            return 0;
        }
    }

    protected function fecha(mixed $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable) {
            return (string) $value;
        }
    }
}
