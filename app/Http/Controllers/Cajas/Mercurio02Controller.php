<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Controller;
use App\Models\Mercurio02;
use App\Services\Cajas\Mercurio02Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class Mercurio02Controller extends Controller
{
    public function __construct(
        protected Mercurio02Service $service
    ) {}

    public function index(Request $request): Response
    {
        $paginator = $this->service->paginate($request);

        $items = [
            'data' => collect($paginator->items())->map(fn (Mercurio02 $row) => [
                'codcaj' => $row->codcaj,
                'nit' => $row->nit,
                'razsoc' => $row->razsoc,
                'sigla' => $row->sigla,
                'email' => $row->email,
                'direccion' => $row->direccion,
                'telefono' => $row->telefono,
                'codciu' => $row->codciu,
                'pagweb' => $row->pagweb,
                'pagfac' => $row->pagfac,
                'pagtwi' => $row->pagtwi,
                'pagyou' => $row->pagyou,
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

        $ciudades = collect($this->service->obtenerCiudades())
            ->map(fn (string $label, string $value) => [
                'value' => $value,
                'label' => $label,
            ])
            ->values()
            ->all();

        return Inertia::render('Cajas/Mercurio02/Index', [
            'items' => $items,
            'ciudades' => $ciudades,
            'canCreate' => Mercurio02::count() === 0,
            'filters' => [
                'q' => (string) $request->query('q', ''),
                'per_page' => (int) $request->query('per_page', 10),
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        if (Mercurio02::count() > 0) {
            return redirect()
                ->back()
                ->withErrors(['form' => 'Ya existe un registro de datos de caja. Edite el existente.'])
                ->withInput();
        }

        $result = $this->service->guardar($data);

        if (! $result['flag']) {
            return redirect()
                ->back()
                ->withErrors(['form' => $result['msj']])
                ->withInput();
        }

        return redirect()
            ->route('cajas.mercurio02.index')
            ->with('success', $result['msj']);
    }

    public function update(Request $request, string $codcaj): RedirectResponse
    {
        $existing = Mercurio02::findOrFail($codcaj);
        $data = $this->validated($request, $existing->codcaj);
        $result = $this->service->guardar($data, $existing->codcaj);

        if (! $result['flag']) {
            return redirect()
                ->back()
                ->withErrors(['form' => $result['msj']])
                ->withInput();
        }

        return redirect()
            ->route('cajas.mercurio02.index', $request->only(['q', 'per_page']))
            ->with('success', $result['msj']);
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?string $currentCodcaj = null): array
    {
        return $request->validate([
            'codcaj' => [
                'required',
                'string',
                'max:6',
                Rule::unique('mercurio02', 'codcaj')->ignore($currentCodcaj, 'codcaj'),
            ],
            'nit' => 'required|string|max:13',
            'razsoc' => 'required|string|max:60',
            'sigla' => 'required|string|max:30',
            'email' => 'required|email|max:90',
            'direccion' => 'required|string|max:90',
            'telefono' => 'required|string|max:20',
            'codciu' => 'required|string|max:5',
            'pagweb' => 'required|string|max:45',
            'pagfac' => 'nullable|string|max:45',
            'pagtwi' => 'nullable|string|max:45',
            'pagyou' => 'nullable|string|max:45',
        ]);
    }
}
