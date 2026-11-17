<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Controller;
use App\Models\Mercurio01;
use App\Services\Cajas\Mercurio01Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class Mercurio01Controller extends Controller
{
    public function __construct(
        protected Mercurio01Service $service
    ) {}

    public function index(Request $request): Response
    {
        $paginator = $this->service->paginate($request);

        $items = [
            'data' => collect($paginator->items())->map(fn (Mercurio01 $row) => [
                'codapl' => $row->codapl,
                'email' => $row->email,
                'clave' => $row->clave,
                'path' => $row->path,
                'ftpserver' => $row->ftpserver,
                'pathserver' => $row->pathserver,
                'userserver' => $row->userserver,
                'passserver' => $row->passserver,
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

        return Inertia::render('Cajas/Mercurio01/Index', [
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
            return redirect()
                ->back()
                ->withErrors(['form' => $result['msj']])
                ->withInput();
        }

        return redirect()
            ->route('cajas.mercurio01.index')
            ->with('success', $result['msj']);
    }

    public function update(Request $request, string $codapl): RedirectResponse
    {
        $existing = Mercurio01::findOrFail($codapl);
        $data = $this->validated($request, $existing->codapl);

        $result = $this->service->guardar($data, $existing->codapl);

        if (! $result['flag']) {
            return redirect()
                ->back()
                ->withErrors(['form' => $result['msj']])
                ->withInput();
        }

        return redirect()
            ->route('cajas.mercurio01.index', $request->only(['q', 'per_page']))
            ->with('success', $result['msj']);
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?string $currentCodapl = null): array
    {
        return $request->validate([
            'codapl' => [
                'required',
                'string',
                'size:2',
                Rule::unique('mercurio01', 'codapl')->ignore($currentCodapl, 'codapl'),
            ],
            'email' => 'nullable|email|max:45',
            'clave' => 'nullable|string|max:20',
            'path' => 'required|string|max:45',
            'ftpserver' => 'nullable|string|max:45',
            'pathserver' => 'nullable|string|max:45',
            'userserver' => 'nullable|string|max:45',
            'passserver' => 'nullable|string|max:45',
        ]);
    }
}
