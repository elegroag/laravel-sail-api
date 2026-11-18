<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Controller;
use App\Models\Mercurio04;
use App\Services\Cajas\Mercurio04Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class Mercurio04Controller extends Controller
{
    public function __construct(
        protected Mercurio04Service $service
    ) {}

    public function index(Request $request): Response
    {
        $paginator = $this->service->paginate($request);

        return Inertia::render('Cajas/Mercurio04/Index', [
            'items' => [
                'data' => collect($paginator->items())->map(fn (Mercurio04 $row) => [
                    'codofi' => (string) $row->codofi,
                    'detalle' => (string) $row->detalle,
                    'principal' => (string) $row->principal,
                    'estado' => (string) $row->estado,
                ])->values()->all(),
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
            'filters' => [
                'q' => (string) $request->query('q', ''),
                'per_page' => (int) $request->query('per_page', 10),
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $result = $this->service->guardar($data);

        if (! $result['flag']) {
            return redirect()->back()->withErrors(['form' => $result['msj']])->withInput();
        }

        return redirect()
            ->route('cajas.mercurio04.index', $request->only(['q', 'per_page']))
            ->with('success', $result['msj']);
    }

    public function update(Request $request, string $codofi): RedirectResponse
    {
        Mercurio04::findOrFail($codofi);
        $data = $this->validated($request, $codofi);
        $result = $this->service->guardar($data, $codofi);

        if (! $result['flag']) {
            return redirect()->back()->withErrors(['form' => $result['msj']])->withInput();
        }

        return redirect()
            ->route('cajas.mercurio04.index', $request->only(['q', 'per_page']))
            ->with('success', $result['msj']);
    }

    public function destroy(string $codofi): RedirectResponse
    {
        $result = $this->service->borrar($codofi);

        if (! $result['flag']) {
            return redirect()->back()->withErrors(['form' => $result['msj']]);
        }

        return redirect()->back()->with('success', $result['msj']);
    }

    public function opciones(string $codofi): Response
    {
        $oficina = Mercurio04::findOrFail($codofi);

        return Inertia::render('Cajas/Mercurio04/Opciones', [
            'oficina' => [
                'codofi' => (string) $oficina->codofi,
                'detalle' => (string) $oficina->detalle,
                'principal' => (string) $oficina->principal,
                'estado' => (string) $oficina->estado,
            ],
            'tipopcOptions' => $this->service->opcionesTipopc(),
            'usuarioOptions' => $this->service->opcionesUsuarios(),
            'asignaciones' => $this->service->asignaciones($codofi),
        ]);
    }

    public function storeOpcion(Request $request, string $codofi): RedirectResponse
    {
        $data = $request->validate([
            'tipopc' => 'required|string|max:2',
            'usuario' => 'required|integer',
        ]);

        $result = $this->service->guardarOpcion(
            $codofi,
            (string) $data['tipopc'],
            (int) $data['usuario'],
        );

        if (! $result['flag']) {
            return redirect()->back()->withErrors(['form' => $result['msj']])->withInput();
        }

        return redirect()
            ->route('cajas.mercurio04.opciones', ['codofi' => $codofi])
            ->with('success', $result['msj']);
    }

    public function destroyOpcion(Request $request, string $codofi): RedirectResponse
    {
        $data = $request->validate([
            'tipopc' => 'required|string|max:2',
            'usuario' => 'required|integer',
        ]);

        $result = $this->service->borrarOpcion(
            $codofi,
            (string) $data['tipopc'],
            (int) $data['usuario'],
        );

        if (! $result['flag']) {
            return redirect()->back()->withErrors(['form' => $result['msj']]);
        }

        return redirect()
            ->route('cajas.mercurio04.opciones', ['codofi' => $codofi])
            ->with('success', $result['msj']);
    }

    /**
     * @return array{codofi: string, detalle: string, principal: string, estado: string}
     */
    private function validated(Request $request, ?string $currentCodofi = null): array
    {
        $isUpdate = $currentCodofi !== null;

        $data = $request->validate([
            'codofi' => $isUpdate
                ? ['nullable', 'string', 'max:2']
                : ['required', 'string', 'min:1', 'max:2'],
            'detalle' => 'required|string|max:45',
            'principal' => ['required', Rule::in(['S', 'N'])],
            'estado' => ['required', Rule::in(['A', 'I'])],
        ]);

        return [
            'codofi' => $isUpdate ? $currentCodofi : (string) $data['codofi'],
            'detalle' => (string) $data['detalle'],
            'principal' => (string) $data['principal'],
            'estado' => (string) $data['estado'],
        ];
    }
}
