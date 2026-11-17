<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Controller;
use App\Models\Mercurio09;
use App\Services\Cajas\Mercurio09Service;
use App\Services\Cajas\Mercurio14Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class Mercurio09Controller extends Controller
{
    public function __construct(
        protected Mercurio09Service $service,
        protected Mercurio14Service $mercurio14Service,
    ) {}

    public function index(Request $request): Response
    {
        $paginator = $this->service->paginate($request);

        return Inertia::render('Cajas/Mercurio09/Index', [
            'items' => [
                'data' => collect($paginator->items())->map(fn (Mercurio09 $row) => [
                    'tipopc' => (string) $row->tipopc,
                    'detalle' => (string) $row->detalle,
                    'dias' => $row->dias !== null ? (int) $row->dias : null,
                    'allows_empresa' => $this->service->allowsEmpresa((string) $row->tipopc),
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
            ->route('cajas.mercurio09.index', $request->only(['q', 'per_page']))
            ->with('success', $result['msj']);
    }

    public function update(Request $request, string $tipopc): RedirectResponse
    {
        Mercurio09::findOrFail($tipopc);
        $data = $this->validated($request, $tipopc);
        $result = $this->service->guardar($data, $tipopc);

        if (! $result['flag']) {
            return redirect()->back()->withErrors(['form' => $result['msj']])->withInput();
        }

        return redirect()
            ->route('cajas.mercurio09.index', $request->only(['q', 'per_page']))
            ->with('success', $result['msj']);
    }

    public function destroy(string $tipopc): RedirectResponse
    {
        $result = $this->service->borrar($tipopc);

        if (! $result['flag']) {
            return redirect()->back()->withErrors(['form' => $result['msj']]);
        }

        return redirect()->back()->with('success', $result['msj']);
    }

    public function documentos(Request $request, string $tipopc): Response
    {
        $tipo = Mercurio09::findOrFail($tipopc);
        $allowsEmpresa = $this->service->allowsEmpresa($tipopc);
        $tab = (string) $request->query('tab', 'trabajador');
        if (! in_array($tab, ['trabajador', 'empresa'], true) || ($tab === 'empresa' && ! $allowsEmpresa)) {
            $tab = 'trabajador';
        }

        $tipsocOptions = $allowsEmpresa ? $this->mercurio14Service->opcionesTipsoc() : [];
        $tipsoc = (string) $request->query('tipsoc', '');
        if ($tipsoc === '' && $tipsocOptions !== []) {
            $tipsoc = (string) $tipsocOptions[0]['value'];
        }

        return Inertia::render('Cajas/Mercurio09/Documentos', [
            'tipo' => [
                'tipopc' => (string) $tipo->tipopc,
                'detalle' => (string) $tipo->detalle,
                'dias' => $tipo->dias !== null ? (int) $tipo->dias : null,
                'allows_empresa' => $allowsEmpresa,
            ],
            'tab' => $tab,
            'tipsoc' => $tipsoc,
            'tipsocOptions' => $tipsocOptions,
            'documentos' => $tab === 'empresa' && $tipsoc !== ''
                ? $this->service->documentosEmpresa($tipopc, $tipsoc)
                : $this->service->documentosTrabajador($tipopc),
        ]);
    }

    public function syncTrabajador(Request $request, string $tipopc): RedirectResponse
    {
        $data = $request->validate([
            'coddoc' => 'required|integer',
            'adjunto' => 'required|boolean',
            'obliga' => 'nullable|in:S,N',
        ]);

        $result = $this->service->syncTrabajador(
            $tipopc,
            (int) $data['coddoc'],
            (bool) $data['adjunto'],
            $data['obliga'] ?? null,
        );

        if (! $result['flag']) {
            return redirect()->back()->withErrors(['form' => $result['msj']]);
        }

        return redirect()
            ->route('cajas.mercurio09.documentos', ['tipopc' => $tipopc, 'tab' => 'trabajador'])
            ->with('success', $result['msj']);
    }

    public function syncEmpresa(Request $request, string $tipopc): RedirectResponse
    {
        $data = $request->validate([
            'tipsoc' => 'required|string|max:2',
            'coddoc' => 'required|integer',
            'adjunto' => 'required|boolean',
            'obliga' => 'nullable|in:S,N',
        ]);

        $result = $this->service->syncEmpresa(
            $tipopc,
            (string) $data['tipsoc'],
            (int) $data['coddoc'],
            (bool) $data['adjunto'],
            $data['obliga'] ?? null,
        );

        if (! $result['flag']) {
            return redirect()->back()->withErrors(['form' => $result['msj']]);
        }

        return redirect()
            ->route('cajas.mercurio09.documentos', [
                'tipopc' => $tipopc,
                'tab' => 'empresa',
                'tipsoc' => $data['tipsoc'],
            ])
            ->with('success', $result['msj']);
    }

    /**
     * @return array{tipopc: string, detalle: string, dias: int|null}
     */
    private function validated(Request $request, ?string $currentTipopc = null): array
    {
        $isUpdate = $currentTipopc !== null;

        $data = $request->validate([
            'tipopc' => $isUpdate
                ? ['nullable', 'string', 'max:2']
                : ['required', 'string', 'min:1', 'max:2'],
            'detalle' => 'required|string|max:45',
            'dias' => 'nullable|integer|min:0|max:99999',
        ]);

        return [
            'tipopc' => $isUpdate ? $currentTipopc : (string) $data['tipopc'],
            'detalle' => (string) $data['detalle'],
            'dias' => array_key_exists('dias', $data) && $data['dias'] !== null && $data['dias'] !== ''
                ? (int) $data['dias']
                : null,
        ];
    }
}
