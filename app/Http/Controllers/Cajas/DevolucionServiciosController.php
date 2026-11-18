<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Controller;
use App\Models\DevolucionPrecompra;
use App\Services\Ecommerce\DevolucionPrecompraService;
use App\Services\Ecommerce\EstadoDevolucionPrecompra;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Bandeja de solicitudes de devolución creadas desde Mercurio.
 */
class DevolucionServiciosController extends Controller
{
    public const DEFAULT_PER_PAGE = 15;

    public const MAX_PER_PAGE = 100;

    public function index(Request $request): Response
    {
        $filters = [
            'estado' => (string) $request->query('estado', ''),
            'documento' => trim((string) $request->query('documento', '')),
            'q' => trim((string) $request->query('q', '')),
            'per_page' => max(
                10,
                min(self::MAX_PER_PAGE, (int) $request->query('per_page', self::DEFAULT_PER_PAGE))
            ),
        ];

        if ($filters['estado'] !== '' && ! array_key_exists($filters['estado'], EstadoDevolucionPrecompra::DESCRIPCIONES)) {
            $filters['estado'] = '';
        }

        $paginator = $this->queryDevoluciones($filters)
            ->paginate($filters['per_page'])
            ->withQueryString();

        $estadoOptions = collect(EstadoDevolucionPrecompra::DESCRIPCIONES)
            ->map(fn (string $label, string $value) => ['value' => $value, 'label' => $label])
            ->values()
            ->all();

        return Inertia::render('Cajas/DevolucionServicios/Index', [
            'filters' => $filters,
            'estadoOptions' => $estadoOptions,
            'estadoLabels' => EstadoDevolucionPrecompra::DESCRIPCIONES,
            'resumen' => $this->resumenPorEstado(),
            'items' => [
                'data' => collect($paginator->items())->map(fn (DevolucionPrecompra $dev) => $this->mapRow($dev))->values()->all(),
                'meta' => [
                    'pagination' => [
                        'current_page' => $paginator->currentPage(),
                        'last_page' => $paginator->lastPage(),
                        'per_page' => $paginator->perPage(),
                        'from' => $paginator->firstItem(),
                        'to' => $paginator->lastItem(),
                        'total' => $paginator->total(),
                    ],
                ],
            ],
        ]);
    }

    /**
     * POST /cajas/devolucion-servicios/{id}/en-tramite
     */
    public function enTramite(int $id, Request $request, DevolucionPrecompraService $service): RedirectResponse
    {
        return $this->gestionar($id, $request, function (DevolucionPrecompra $devolucion, string $usuario, ?string $nota) use ($service) {
            return $service->ponerEnTramite($devolucion, $usuario, $nota);
        }, 'Solicitud puesta en trámite.');
    }

    /**
     * POST /cajas/devolucion-servicios/{id}/aprobar
     */
    public function aprobar(int $id, Request $request, DevolucionPrecompraService $service): RedirectResponse
    {
        return $this->gestionar($id, $request, function (DevolucionPrecompra $devolucion, string $usuario, ?string $nota) use ($service) {
            return $service->aprobar($devolucion, $usuario, $nota);
        }, 'Devolución aprobada. La precompra quedó en estado Devolución.');
    }

    /**
     * POST /cajas/devolucion-servicios/{id}/rechazar
     */
    public function rechazar(int $id, Request $request, DevolucionPrecompraService $service): RedirectResponse
    {
        return $this->gestionar($id, $request, function (DevolucionPrecompra $devolucion, string $usuario, ?string $nota) use ($service) {
            return $service->rechazar($devolucion, $usuario, $nota);
        }, 'Solicitud de devolución rechazada.');
    }

    /**
     * @param  callable(DevolucionPrecompra, string, ?string): DevolucionPrecompra  $accion
     */
    protected function gestionar(int $devolucionId, Request $request, callable $accion, string $okMessage): RedirectResponse
    {
        $devolucion = DevolucionPrecompra::with('precompra')->find($devolucionId);
        if (! $devolucion) {
            return redirect()
                ->back()
                ->with('error', 'La solicitud de devolución no existe');
        }

        $data = $request->validate([
            'nota' => 'nullable|string|max:1000',
        ]);

        $user = session('user');
        $usuario = is_array($user) ? (string) ($user['usuario'] ?? 'sistema') : 'sistema';

        try {
            $accion($devolucion, $usuario, $data['nota'] ?? null);

            return redirect()
                ->back()
                ->with('success', $okMessage);
        } catch (\Throwable $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * @param  array{estado: string, documento: string, q: string}  $filters
     * @return \Illuminate\Database\Eloquent\Builder<DevolucionPrecompra>
     */
    protected function queryDevoluciones(array $filters)
    {
        $query = DevolucionPrecompra::query()
            ->with(['precompra'])
            ->orderByDesc('fecha_solicitud')
            ->orderByDesc('id');

        if ($filters['estado'] !== '' && array_key_exists($filters['estado'], EstadoDevolucionPrecompra::DESCRIPCIONES)) {
            $query->where('estado', $filters['estado']);
        }

        if ($filters['documento'] !== '') {
            $documento = $filters['documento'];
            $query->whereHas('precompra', function ($sub) use ($documento) {
                $sub->where('documento', 'like', '%'.$documento.'%');
            });
        }

        if ($filters['q'] !== '') {
            $q = $filters['q'];
            $query->where(function ($sub) use ($q) {
                $sub->where('motivo_solicitud', 'like', '%'.$q.'%')
                    ->orWhere('nota_afiliado', 'like', '%'.$q.'%')
                    ->orWhereHas('precompra', function ($p) use ($q) {
                        $p->where('documento', 'like', '%'.$q.'%')
                            ->orWhere('ref_payco', 'like', '%'.$q.'%')
                            ->orWhere('codser', 'like', '%'.$q.'%');
                    });
            });
        }

        return $query;
    }

    /**
     * @return array<string, mixed>
     */
    protected function mapRow(DevolucionPrecompra $dev): array
    {
        $pre = $dev->precompra;

        return [
            'id' => $dev->id,
            'precompra_id' => $pre?->id,
            'documento' => $pre?->documento,
            'codser' => $pre?->codser,
            'numero' => $pre?->numero,
            'valor' => $pre?->valor !== null ? (float) $pre->valor : null,
            'estado_precompra' => $pre?->estado,
            'estado_precompra_descripcion' => $pre?->estado_descripcion ?? $pre?->estado,
            'estado' => $dev->estado,
            'estado_descripcion' => $dev->estado_descripcion,
            'fecha_solicitud' => optional($dev->fecha_solicitud)->format('Y-m-d H:i'),
            'motivo_solicitud' => $dev->motivo_solicitud,
            'nota_afiliado' => $dev->nota_afiliado ? Str::limit((string) $dev->nota_afiliado, 120) : null,
            'abierta' => $dev->estaAbierta(),
            'es_solicitada' => $dev->isSolicitada(),
            'ver_url' => $pre ? route('admservicios.ver', ['id' => $pre->id]) : null,
        ];
    }

    /**
     * @return array<string, int>
     */
    protected function resumenPorEstado(): array
    {
        $counts = DevolucionPrecompra::query()
            ->select('estado', DB::raw('COUNT(*) as total'))
            ->groupBy('estado')
            ->pluck('total', 'estado')
            ->all();

        $resumen = ['TODOS' => (int) array_sum($counts)];
        foreach (EstadoDevolucionPrecompra::DESCRIPCIONES as $codigo => $_label) {
            $resumen[$codigo] = (int) ($counts[$codigo] ?? 0);
        }

        return $resumen;
    }
}
