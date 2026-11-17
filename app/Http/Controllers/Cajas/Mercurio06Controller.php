<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Controller;
use App\Models\Mercurio06;
use App\Services\Cajas\Mercurio06Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class Mercurio06Controller extends Controller
{
    public function __construct(
        protected Mercurio06Service $service
    ) {}

    public function index(Request $request): Response
    {
        $paginator = $this->service->paginate($request);
        $camposTipo = trim((string) $request->query('campos_tipo', ''));

        $items = [
            'data' => collect($paginator->items())->map(fn (Mercurio06 $row) => [
                'tipo' => $row->tipo,
                'detalle' => $row->detalle,
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
        ];

        return Inertia::render('Cajas/Mercurio06/Index', [
            'items' => $items,
            'filters' => [
                'q' => (string) $request->query('q', ''),
                'per_page' => (int) $request->query('per_page', 10),
                'campos_tipo' => $camposTipo !== '' ? $camposTipo : null,
            ],
            'campos' => $camposTipo !== '' ? $this->service->listarCampos($camposTipo) : [],
            'camposTipo' => $camposTipo !== '' ? $camposTipo : null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedTipo($request);
        $result = $this->service->guardarTipo($data);

        if (! $result['flag']) {
            return redirect()->back()->withErrors(['form' => $result['msj']])->withInput();
        }

        return redirect()
            ->route('cajas.mercurio06.index', $request->only(['q', 'per_page']))
            ->with('success', $result['msj']);
    }

    public function update(Request $request, string $tipo): RedirectResponse
    {
        $existing = Mercurio06::findOrFail($tipo);
        $data = $this->validatedTipo($request, $existing->tipo);
        $result = $this->service->guardarTipo($data, $existing->tipo);

        if (! $result['flag']) {
            return redirect()->back()->withErrors(['form' => $result['msj']])->withInput();
        }

        return redirect()
            ->route('cajas.mercurio06.index', $request->only(['q', 'per_page', 'campos_tipo']))
            ->with('success', $result['msj']);
    }

    public function destroy(string $tipo): RedirectResponse
    {
        $result = $this->service->borrarTipo($tipo);

        if (! $result['flag']) {
            return redirect()->back()->withErrors(['form' => $result['msj']]);
        }

        return redirect()->back()->with('success', $result['msj']);
    }

    public function storeCampo(Request $request, string $tipo): RedirectResponse
    {
        $data = $this->validatedCampo($request, $tipo);
        Mercurio06::findOrFail($tipo);
        $result = $this->service->guardarCampo($data);

        if (! $result['flag']) {
            return redirect()->back()->withErrors(['campo_form' => $result['msj']])->withInput();
        }

        return redirect()
            ->route('cajas.mercurio06.index', array_merge(
                $request->only(['q', 'per_page']),
                ['campos_tipo' => $tipo]
            ))
            ->with('success', $result['msj']);
    }

    public function updateCampo(Request $request, string $tipo, string $campo): RedirectResponse
    {
        $data = $this->validatedCampo($request, $tipo, $campo);
        Mercurio06::findOrFail($tipo);
        $result = $this->service->guardarCampo($data, $campo);

        if (! $result['flag']) {
            return redirect()->back()->withErrors(['campo_form' => $result['msj']])->withInput();
        }

        return redirect()
            ->route('cajas.mercurio06.index', array_merge(
                $request->only(['q', 'per_page']),
                ['campos_tipo' => $tipo]
            ))
            ->with('success', $result['msj']);
    }

    public function destroyCampo(string $tipo, string $campo): RedirectResponse
    {
        $result = $this->service->borrarCampo($tipo, $campo);

        if (! $result['flag']) {
            return redirect()->back()->withErrors(['campo_form' => $result['msj']]);
        }

        return redirect()->back()->with('success', $result['msj']);
    }

    /**
     * @return array{tipo: string, detalle: string}
     */
    private function validatedTipo(Request $request, ?string $currentTipo = null): array
    {
        return $request->validate([
            'tipo' => [
                'required',
                'string',
                'size:2',
                Rule::unique('mercurio06', 'tipo')->ignore($currentTipo, 'tipo'),
            ],
            'detalle' => 'required|string|max:40',
        ]);
    }

    /**
     * @return array{tipo: string, campo: string, detalle: string, orden: int}
     */
    private function validatedCampo(Request $request, string $tipo, ?string $currentCampo = null): array
    {
        $data = $request->validate([
            'campo' => [
                'required',
                'string',
                'max:20',
                Rule::unique('mercurio28', 'campo')
                    ->where(fn ($query) => $query->where('tipo', $tipo))
                    ->ignore($currentCampo, 'campo'),
            ],
            'detalle' => 'required|string|max:45',
            'orden' => 'required|integer|min:0',
        ]);

        return [
            'tipo' => $tipo,
            'campo' => $data['campo'],
            'detalle' => $data['detalle'],
            'orden' => (int) $data['orden'],
        ];
    }
}
