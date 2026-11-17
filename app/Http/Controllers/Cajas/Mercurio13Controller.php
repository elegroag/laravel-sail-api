<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Controller;
use App\Models\Mercurio13;
use App\Services\Cajas\Mercurio13Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class Mercurio13Controller extends Controller
{
    public function __construct(
        protected Mercurio13Service $service
    ) {}

    public function index(Request $request): Response
    {
        $paginator = $this->service->paginate($request);

        $items = [
            'data' => collect($paginator->items())->map(fn (Mercurio13 $row) => [
                'tipopc' => $row->tipopc,
                'coddoc' => (int) $row->coddoc,
                'tipopc_detalle' => $row->mercurio09?->detalle,
                'coddoc_detalle' => $row->mercurio12?->detalle,
                'obliga' => $row->obliga,
                'auto_generado' => (int) $row->auto_generado,
                'nota' => $row->nota,
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

        return Inertia::render('Cajas/Mercurio13/Index', [
            'items' => $items,
            'tipopcOptions' => $this->service->opcionesTipopc(),
            'coddocOptions' => $this->service->opcionesCoddoc(),
            'filters' => [
                'q' => (string) $request->query('q', ''),
                'per_page' => (int) $request->query('per_page', 10),
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $result = $this->service->guardar($data, false);

        if (! $result['flag']) {
            return redirect()->back()->withErrors(['form' => $result['msj']])->withInput();
        }

        return redirect()
            ->route('cajas.mercurio13.index', $request->only(['q', 'per_page']))
            ->with('success', $result['msj']);
    }

    public function update(Request $request, string $tipopc, int $coddoc): RedirectResponse
    {
        Mercurio13::where('tipopc', $tipopc)->where('coddoc', $coddoc)->firstOrFail();

        $data = $this->validated($request, $tipopc, $coddoc);
        $data['tipopc'] = $tipopc;
        $data['coddoc'] = $coddoc;

        $result = $this->service->guardar($data, true);

        if (! $result['flag']) {
            return redirect()->back()->withErrors(['form' => $result['msj']])->withInput();
        }

        return redirect()
            ->route('cajas.mercurio13.index', $request->only(['q', 'per_page']))
            ->with('success', $result['msj']);
    }

    public function destroy(string $tipopc, int $coddoc): RedirectResponse
    {
        $result = $this->service->borrar($tipopc, $coddoc);

        if (! $result['flag']) {
            return redirect()->back()->withErrors(['form' => $result['msj']]);
        }

        return redirect()->back()->with('success', $result['msj']);
    }

    /**
     * @return array{tipopc: string, coddoc: int, obliga: string, auto_generado: int, nota: ?string}
     */
    private function validated(Request $request, ?string $currentTipopc = null, ?int $currentCoddoc = null): array
    {
        $isUpdate = $currentTipopc !== null && $currentCoddoc !== null;

        $rules = [
            'obliga' => 'required|in:S,N',
            'auto_generado' => 'required|in:0,1',
            'nota' => 'nullable|string|max:255',
        ];

        if ($isUpdate) {
            $data = $request->validate($rules);
        } else {
            $data = $request->validate([
                'tipopc' => [
                    'required',
                    'string',
                    'size:2',
                    'exists:mercurio09,tipopc',
                ],
                'coddoc' => [
                    'required',
                    'integer',
                    'exists:mercurio12,coddoc',
                    Rule::unique('mercurio13', 'coddoc')->where(fn ($query) => $query->where('tipopc', $request->input('tipopc'))),
                ],
                ...$rules,
            ]);
        }

        return [
            'tipopc' => $isUpdate ? $currentTipopc : (string) $data['tipopc'],
            'coddoc' => $isUpdate ? $currentCoddoc : (int) $data['coddoc'],
            'obliga' => (string) $data['obliga'],
            'auto_generado' => (int) $data['auto_generado'],
            'nota' => $data['nota'] ?? null,
        ];
    }
}
