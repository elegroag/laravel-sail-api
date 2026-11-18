<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Controller;
use App\Services\Cajas\ReasignaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ReasignaController extends Controller
{
    public function __construct(
        protected ReasignaService $reasignaService
    ) {}

    public function index(Request $request): Response
    {
        $filters = $request->validate([
            'accion' => ['nullable', 'in:C,P'],
            'tipopc' => ['nullable', 'string'],
            'usuario' => ['nullable', 'string'],
        ]);

        $accion = (string) ($filters['accion'] ?? 'C');
        $tipopc = (string) ($filters['tipopc'] ?? '');
        $usuario = (string) ($filters['usuario'] ?? '');

        $queried = $accion === 'C' && $tipopc !== '' && $usuario !== '';
        $solicitudes = $queried
            ? $this->reasignaService->consultar($tipopc, $usuario)
            : [];

        return Inertia::render('Cajas/Reasigna/Index', [
            'tipopcOptions' => $this->reasignaService->tipopcOptions(),
            'tipopcMasivoOptions' => $this->reasignaService->tipopcMasivoOptions(),
            'usuariosOptions' => $this->reasignaService->usuariosOptions(),
            'usuariosByTipopc' => $this->reasignaService->usuariosByTipopc(),
            'filters' => [
                'accion' => $accion,
                'tipopc' => $tipopc,
                'usuario' => $usuario,
            ],
            'solicitudes' => $solicitudes,
            'queried' => $queried,
        ]);
    }

    public function procesoReasignarMasivo(Request $request): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'tipopc' => ['required', 'string'],
            'usuori' => ['required', 'string'],
            'usudes' => ['required', 'string'],
            'fecini' => ['required', 'date_format:Y-m-d'],
            'fecfin' => ['required', 'date_format:Y-m-d'],
        ]);

        $result = $this->reasignaService->reasignarMasivo(
            $data['tipopc'],
            $data['usuori'],
            $data['usudes'],
            $data['fecini'],
            $data['fecfin'],
        );

        if ($request->wantsJson() && ! $request->header('X-Inertia')) {
            return response()->json($result);
        }

        return redirect()
            ->route('reasigna.index', ['accion' => 'P'])
            ->with('success', $result['msj']);
    }

    public function cambiarUsuario(Request $request): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'tipopc' => ['required', 'string'],
            'id' => ['required'],
            'usuario' => ['required', 'string'],
        ]);

        $result = $this->reasignaService->cambiarUsuario(
            $data['tipopc'],
            $data['id'],
            $data['usuario'],
        );

        if (! $result['success']) {
            if ($request->wantsJson() && ! $request->header('X-Inertia')) {
                return response()->json($result, 422);
            }

            return redirect()->back()->withErrors(['form' => $result['msj']]);
        }

        if ($request->wantsJson() && ! $request->header('X-Inertia')) {
            return response()->json($result);
        }

        return redirect()
            ->back()
            ->with('success', $result['msj']);
    }
}
