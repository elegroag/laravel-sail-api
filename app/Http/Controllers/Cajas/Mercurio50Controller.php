<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Controller;
use App\Models\Mercurio50;
use App\Services\Cajas\Mercurio50Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class Mercurio50Controller extends Controller
{
    public function __construct(
        protected Mercurio50Service $service
    ) {}

    public function index(Request $request): Response
    {
        $paginator = $this->service->paginate($request);

        $items = [
            'data' => collect($paginator->items())->map(fn (Mercurio50 $row) => [
                'codapl' => (string) $row->codapl,
                'webser' => $row->webser,
                'path' => $row->path,
                'urlonl' => $row->urlonl,
                'puncom' => $row->puncom,
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

        return Inertia::render('Cajas/Mercurio50/Index', [
            'items' => $items,
            'canCreate' => Mercurio50::count() === 0,
            'filters' => [
                'q' => (string) $request->query('q', ''),
                'per_page' => (int) $request->query('per_page', 10),
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        if (Mercurio50::count() > 0) {
            return redirect()
                ->back()
                ->withErrors(['form' => 'Ya existe una configuración básica. Edite el registro existente.'])
                ->withInput();
        }

        $result = $this->service->crear($data);

        if (! $result['flag']) {
            return redirect()->back()->withErrors(['form' => $result['msj']])->withInput();
        }

        return redirect()
            ->route('cajas.mercurio50.index', $request->only(['q', 'per_page']))
            ->with('success', $result['msj']);
    }

    public function update(Request $request, string $codapl): RedirectResponse
    {
        Mercurio50::where('codapl', $codapl)->firstOrFail();

        $data = $this->validated($request, $codapl);
        $result = $this->service->actualizar($codapl, $data);

        if (! $result['flag']) {
            return redirect()->back()->withErrors(['form' => $result['msj']])->withInput();
        }

        return redirect()
            ->route('cajas.mercurio50.index', $request->only(['q', 'per_page']))
            ->with('success', $result['msj']);
    }

    /**
     * @return array{codapl: string, webser: ?string, path: ?string, urlonl: ?string, puncom: ?string}
     */
    private function validated(Request $request, ?string $currentCodapl = null): array
    {
        $isUpdate = $currentCodapl !== null;

        $rules = [
            'webser' => 'nullable|string|max:50',
            'path' => 'nullable|string|max:45',
            'urlonl' => 'nullable|string|max:60',
            'puncom' => 'nullable|integer',
        ];

        if ($isUpdate) {
            $validated = $request->validate($rules);

            return [
                'codapl' => $currentCodapl,
                'webser' => $validated['webser'] ?? null,
                'path' => $validated['path'] ?? null,
                'urlonl' => $validated['urlonl'] ?? null,
                'puncom' => isset($validated['puncom']) ? (string) $validated['puncom'] : null,
            ];
        }

        $validated = $request->validate([
            ...$rules,
            'codapl' => ['required', 'string', 'size:2', Rule::unique('mercurio50', 'codapl')],
        ]);

        return [
            'codapl' => $validated['codapl'],
            'webser' => $validated['webser'] ?? null,
            'path' => $validated['path'] ?? null,
            'urlonl' => $validated['urlonl'] ?? null,
            'puncom' => isset($validated['puncom']) ? (string) $validated['puncom'] : null,
        ];
    }
}
