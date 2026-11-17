<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Controller;
use App\Models\Mercurio14;
use App\Services\Cajas\Mercurio14Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class Mercurio14Controller extends Controller
{
    public function __construct(
        protected Mercurio14Service $service
    ) {}

    public function index(Request $request): Response
    {
        $paginator = $this->service->paginate($request);

        $items = [
            'data' => collect($paginator->items())->map(fn (Mercurio14 $row) => [
                'tipopc' => $row->tipopc,
                'tipsoc' => $row->tipsoc,
                'coddoc' => (int) $row->coddoc,
                'tipopc_detalle' => $row->mercurio09?->detalle,
                'tipsoc_detalle' => $row->subsi54?->detalle,
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

        return Inertia::render('Cajas/Mercurio14/Index', [
            'items' => $items,
            'tipopcOptions' => $this->service->opcionesTipopc(),
            'tipsocOptions' => $this->service->opcionesTipsoc(),
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
            ->route('cajas.mercurio14.index', $request->only(['q', 'per_page']))
            ->with('success', $result['msj']);
    }

    public function update(Request $request, string $tipopc, string $tipsoc, int $coddoc): RedirectResponse
    {
        Mercurio14::where('tipopc', $tipopc)
            ->where('tipsoc', $tipsoc)
            ->where('coddoc', $coddoc)
            ->firstOrFail();

        $data = $this->validated($request, $tipopc, $tipsoc, $coddoc);
        $data['tipopc'] = $tipopc;
        $data['tipsoc'] = $tipsoc;
        $data['coddoc'] = $coddoc;

        $result = $this->service->guardar($data, true);

        if (! $result['flag']) {
            return redirect()->back()->withErrors(['form' => $result['msj']])->withInput();
        }

        return redirect()
            ->route('cajas.mercurio14.index', $request->only(['q', 'per_page']))
            ->with('success', $result['msj']);
    }

    public function destroy(string $tipopc, string $tipsoc, int $coddoc): RedirectResponse
    {
        $result = $this->service->borrar($tipopc, $tipsoc, $coddoc);

        if (! $result['flag']) {
            return redirect()->back()->withErrors(['form' => $result['msj']]);
        }

        return redirect()->back()->with('success', $result['msj']);
    }

    /**
     * @return array{tipopc: string, tipsoc: string, coddoc: int, obliga: string, auto_generado: int, nota: ?string}
     */
    private function validated(
        Request $request,
        ?string $currentTipopc = null,
        ?string $currentTipsoc = null,
        ?int $currentCoddoc = null
    ): array {
        $isUpdate = $currentTipopc !== null && $currentTipsoc !== null && $currentCoddoc !== null;

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
                'tipsoc' => [
                    'required',
                    'string',
                    'max:2',
                    'exists:subsi54,tipsoc',
                ],
                'coddoc' => [
                    'required',
                    'integer',
                    'exists:mercurio12,coddoc',
                    Rule::unique('mercurio14', 'coddoc')
                        ->where(fn ($query) => $query
                            ->where('tipopc', $request->input('tipopc'))
                            ->where('tipsoc', $request->input('tipsoc'))),
                ],
                ...$rules,
            ]);
        }

        return [
            'tipopc' => $isUpdate ? $currentTipopc : (string) $data['tipopc'],
            'tipsoc' => $isUpdate ? $currentTipsoc : (string) $data['tipsoc'],
            'coddoc' => $isUpdate ? $currentCoddoc : (int) $data['coddoc'],
            'obliga' => (string) $data['obliga'],
            'auto_generado' => (int) $data['auto_generado'],
            'nota' => $data['nota'] ?? null,
        ];
    }
}
