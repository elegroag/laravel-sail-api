<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Controller;
use App\Models\Mercurio11;
use App\Services\Cajas\Mercurio11Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class Mercurio11Controller extends Controller
{
    public function __construct(
        protected Mercurio11Service $service
    ) {}

    public function index(Request $request): Response
    {
        $paginator = $this->service->paginate($request);

        return Inertia::render('Cajas/Mercurio11/Index', [
            'items' => [
                'data' => collect($paginator->items())->map(fn (Mercurio11 $row) => [
                    'codest' => (string) $row->codest,
                    'detalle' => (string) $row->detalle,
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
            ->route('cajas.mercurio11.index', $request->only(['q', 'per_page']))
            ->with('success', $result['msj']);
    }

    public function update(Request $request, string $codest): RedirectResponse
    {
        Mercurio11::findOrFail($codest);
        $data = $this->validated($request, $codest);
        $result = $this->service->guardar($data, $codest);

        if (! $result['flag']) {
            return redirect()->back()->withErrors(['form' => $result['msj']])->withInput();
        }

        return redirect()
            ->route('cajas.mercurio11.index', $request->only(['q', 'per_page']))
            ->with('success', $result['msj']);
    }

    public function destroy(string $codest): RedirectResponse
    {
        $result = $this->service->borrar($codest);

        if (! $result['flag']) {
            return redirect()->back()->withErrors(['form' => $result['msj']]);
        }

        return redirect()->back()->with('success', $result['msj']);
    }

    /**
     * @return array{codest: string, detalle: string}
     */
    private function validated(Request $request, ?string $currentCodest = null): array
    {
        $isUpdate = $currentCodest !== null;

        $data = $request->validate([
            'codest' => $isUpdate
                ? ['nullable', 'string', 'size:2']
                : ['required', 'string', 'size:2'],
            'detalle' => 'required|string|max:45',
        ]);

        return [
            'codest' => $isUpdate ? $currentCodest : strtoupper((string) $data['codest']),
            'detalle' => (string) $data['detalle'],
        ];
    }
}
