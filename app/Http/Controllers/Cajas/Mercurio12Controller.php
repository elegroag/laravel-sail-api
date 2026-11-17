<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Controller;
use App\Models\Mercurio12;
use App\Services\Cajas\Mercurio12Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class Mercurio12Controller extends Controller
{
    public function __construct(
        protected Mercurio12Service $service
    ) {}

    public function index(Request $request): Response
    {
        $paginator = $this->service->paginate($request);

        $items = [
            'data' => collect($paginator->items())->map(fn (Mercurio12 $row) => [
                'coddoc' => (int) $row->coddoc,
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

        return Inertia::render('Cajas/Mercurio12/Index', [
            'items' => $items,
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
            ->route('cajas.mercurio12.index', $request->only(['q', 'per_page']))
            ->with('success', $result['msj']);
    }

    public function update(Request $request, int $coddoc): RedirectResponse
    {
        $existing = Mercurio12::findOrFail($coddoc);
        $data = $this->validated($request, (int) $existing->coddoc);
        $result = $this->service->guardar($data, (int) $existing->coddoc);

        if (! $result['flag']) {
            return redirect()->back()->withErrors(['form' => $result['msj']])->withInput();
        }

        return redirect()
            ->route('cajas.mercurio12.index', $request->only(['q', 'per_page']))
            ->with('success', $result['msj']);
    }

    public function destroy(int $coddoc): RedirectResponse
    {
        $result = $this->service->borrar($coddoc);

        if (! $result['flag']) {
            return redirect()->back()->withErrors(['form' => $result['msj']]);
        }

        return redirect()->back()->with('success', $result['msj']);
    }

    /**
     * @return array{coddoc: int, detalle: string}
     */
    private function validated(Request $request, ?int $currentCoddoc = null): array
    {
        $data = $request->validate([
            'coddoc' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('mercurio12', 'coddoc')->ignore($currentCoddoc, 'coddoc'),
            ],
            'detalle' => 'required|string|max:60',
        ]);

        return [
            'coddoc' => (int) $data['coddoc'],
            'detalle' => $data['detalle'],
        ];
    }
}
