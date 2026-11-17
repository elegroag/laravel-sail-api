<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Controller;
use App\Services\Cajas\Gener42Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class Gener42Controller extends Controller
{
    public function __construct(
        protected Gener42Service $service
    ) {}

    public function index(Request $request): Response
    {
        $usuario = $request->query('usuario');
        $usuario = $usuario !== null && $usuario !== '' ? $usuario : null;

        return Inertia::render('Cajas/Gener42/Index', [
            'usuarios' => $this->service->opcionesUsuarios(),
            'usuario' => $usuario,
            'asignados' => $usuario !== null ? $this->service->permisosAsignados($usuario) : [],
            'disponibles' => $usuario !== null ? $this->service->permisosDisponibles($usuario) : [],
        ]);
    }

    public function assign(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $result = $this->service->asignar($data['usuario'], $data['permisos']);

        if (! $result['flag']) {
            return redirect()->back()->withErrors(['form' => $result['msj']]);
        }

        return redirect()
            ->route('cajas.gener42.index', ['usuario' => $data['usuario']])
            ->with('success', $result['msj']);
    }

    public function revoke(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $result = $this->service->quitar($data['usuario'], $data['permisos']);

        if (! $result['flag']) {
            return redirect()->back()->withErrors(['form' => $result['msj']]);
        }

        return redirect()
            ->route('cajas.gener42.index', ['usuario' => $data['usuario']])
            ->with('success', $result['msj']);
    }

    /**
     * @return array{usuario: int|string, permisos: list<string>}
     */
    private function validated(Request $request): array
    {
        $data = $request->validate([
            'usuario' => 'required|integer',
            'permisos' => 'required|array|min:1',
            'permisos.*' => 'required|string|max:5',
        ]);

        return [
            'usuario' => (int) $data['usuario'],
            'permisos' => array_values($data['permisos']),
        ];
    }
}
