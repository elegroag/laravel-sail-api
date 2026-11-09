<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\DevolucionPrecompra;
use App\Services\Ecommerce\DevolucionPrecompraService;
use App\Services\Ecommerce\EstadoDevolucionPrecompra;
use App\Services\Utils\GeneralService;
use App\Services\Utils\Paginate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Bandeja de solicitudes de devolución creadas desde Mercurio.
 */
class DevolucionServiciosController extends ApplicationController
{
    protected $cantidad_pagina = 15;

    protected $user;

    public function __construct()
    {
        $this->user = session()->has('user') ? session('user') : null;
    }

    public function index()
    {
        return view('cajas.devolucion_servicios.index', [
            'title' => 'Devoluciones de servicios',
            'filtro_estados' => EstadoDevolucionPrecompra::DESCRIPCIONES,
            'resumen' => $this->resumenPorEstado(),
        ]);
    }

    public function buscar(Request $request)
    {
        $pagina = ($request->input('pagina') == '') ? 1 : (int) $request->input('pagina');
        $numero = $request->input('numero');
        if ($numero != '' && is_numeric($numero)) {
            $this->cantidad_pagina = (int) $numero;
        }

        $items = $this->queryDevoluciones($request)->get();
        $paginate = Paginate::execute($items, $pagina, $this->cantidad_pagina);

        $consultasOldServices = new GeneralService;

        return $this->renderObject([
            'consulta' => view('cajas.devolucion_servicios._tabla', [
                'paginate' => $paginate,
            ])->render(),
            'paginate' => $consultasOldServices->showPaginate($paginate, $this->cantidad_pagina),
            'resumen' => $this->resumenPorEstado(),
            'total' => $items->count(),
        ], false);
    }

    public function changeCantidadPagina(Request $request)
    {
        $numero = $request->input('numero');
        if ($numero != '' && is_numeric($numero)) {
            $this->cantidad_pagina = (int) $numero;
        }

        return $this->buscar($request);
    }

    /**
     * POST /cajas/devolucion-servicios/{id}/en-tramite
     */
    public function enTramite(int $id, Request $request, DevolucionPrecompraService $service)
    {
        return $this->gestionar($id, $request, function (DevolucionPrecompra $devolucion, string $usuario, ?string $nota) use ($service) {
            return $service->ponerEnTramite($devolucion, $usuario, $nota);
        }, 'Solicitud puesta en trámite.');
    }

    /**
     * POST /cajas/devolucion-servicios/{id}/aprobar
     */
    public function aprobar(int $id, Request $request, DevolucionPrecompraService $service)
    {
        return $this->gestionar($id, $request, function (DevolucionPrecompra $devolucion, string $usuario, ?string $nota) use ($service) {
            return $service->aprobar($devolucion, $usuario, $nota);
        }, 'Devolución aprobada. La precompra quedó en estado Devolución.');
    }

    /**
     * POST /cajas/devolucion-servicios/{id}/rechazar
     */
    public function rechazar(int $id, Request $request, DevolucionPrecompraService $service)
    {
        return $this->gestionar($id, $request, function (DevolucionPrecompra $devolucion, string $usuario, ?string $nota) use ($service) {
            return $service->rechazar($devolucion, $usuario, $nota);
        }, 'Solicitud de devolución rechazada.');
    }

    /**
     * @param  callable(DevolucionPrecompra, string, ?string): DevolucionPrecompra  $accion
     */
    protected function gestionar(int $devolucionId, Request $request, callable $accion, string $okMessage)
    {
        $devolucion = DevolucionPrecompra::with('precompra')->find($devolucionId);
        if (! $devolucion) {
            return $this->renderObject([
                'success' => false,
                'msj' => 'La solicitud de devolución no existe',
            ]);
        }

        $data = $request->validate([
            'nota' => 'nullable|string|max:1000',
        ]);

        $usuario = (string) ($this->user['usuario'] ?? 'sistema');

        try {
            $accion($devolucion, $usuario, $data['nota'] ?? null);

            return $this->renderObject([
                'success' => true,
                'msj' => $okMessage,
            ]);
        } catch (\Throwable $e) {
            return $this->renderObject([
                'success' => false,
                'msj' => $e->getMessage(),
            ]);
        }
    }

    protected function queryDevoluciones(Request $request)
    {
        $estado = trim((string) $request->input('estado', ''));
        $documento = trim((string) $request->input('documento', ''));
        $q = trim((string) $request->input('q', ''));

        $query = DevolucionPrecompra::query()
            ->with(['precompra'])
            ->orderByDesc('fecha_solicitud')
            ->orderByDesc('id');

        if ($estado !== '' && array_key_exists($estado, EstadoDevolucionPrecompra::DESCRIPCIONES)) {
            $query->where('estado', $estado);
        }

        if ($documento !== '') {
            $query->whereHas('precompra', function ($sub) use ($documento) {
                $sub->where('documento', 'like', '%' . $documento . '%');
            });
        }

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('motivo_solicitud', 'like', '%' . $q . '%')
                    ->orWhere('nota_afiliado', 'like', '%' . $q . '%')
                    ->orWhereHas('precompra', function ($p) use ($q) {
                        $p->where('documento', 'like', '%' . $q . '%')
                            ->orWhere('ref_payco', 'like', '%' . $q . '%')
                            ->orWhere('codser', 'like', '%' . $q . '%');
                    });
            });
        }

        return $query;
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
