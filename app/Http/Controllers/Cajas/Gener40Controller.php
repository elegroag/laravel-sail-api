<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Controller;
use App\Models\Gener40;
use App\Services\Cajas\Gener40Service;
use App\Services\Reports\OportunidadAfiliacionExcelExporter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Gener40Controller extends Controller
{
    public function __construct(
        protected Gener40Service $service
    ) {}

    public function index(Request $request): Response
    {
        $paginator = $this->service->paginate($request);
        $rutas = $this->service->opcionesRuta();

        return Inertia::render('Cajas/Gener40/Index', [
            'items' => [
                'data' => collect($paginator->items())->map(fn (Gener40 $row) => [
                    'codigo' => (string) $row->codigo,
                    'detalle' => (string) ($row->detalle ?? ''),
                    'controller' => $row->controller !== null && $row->controller !== '' ? (string) $row->controller : null,
                    'action' => $row->action !== null && $row->action !== '' ? (string) $row->action : null,
                    'orden' => $row->orden !== null ? (int) $row->orden : null,
                    'asignaciones' => (int) ($row->asignaciones_count ?? 0),
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
            'controllers' => $rutas['controllers'],
            'actions' => $rutas['actions'],
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
            ->route('cajas.gener40.index', $request->only(['q', 'per_page']))
            ->with('success', $result['msj']);
    }

    public function update(Request $request, string $codigo): RedirectResponse
    {
        Gener40::findOrFail($codigo);
        $data = $this->validated($request, $codigo);
        $result = $this->service->guardar($data, $codigo);

        if (! $result['flag']) {
            return redirect()->back()->withErrors(['form' => $result['msj']])->withInput();
        }

        return redirect()
            ->route('cajas.gener40.index', $request->only(['q', 'per_page']))
            ->with('success', $result['msj']);
    }

    public function destroy(string $codigo): RedirectResponse
    {
        $result = $this->service->borrar($codigo);

        if (! $result['flag']) {
            return redirect()->back()->withErrors(['form' => $result['msj']]);
        }

        return redirect()->back()->with('success', $result['msj']);
    }

    public function exportar(): StreamedResponse
    {
        return OportunidadAfiliacionExcelExporter::stream(
            ['Código', 'Detalle', 'Controller', 'Action', 'Orden'],
            $this->service->exportRows(),
            'gener40_acciones_modulos_'.date('Ymd_His').'.xlsx'
        );
    }

    /**
     * @return array{codigo: string, detalle: string, controller: ?string, action: ?string, orden: ?int}
     */
    private function validated(Request $request, ?string $currentCodigo = null): array
    {
        $isUpdate = $currentCodigo !== null;

        $data = $request->validate([
            'codigo' => $isUpdate
                ? ['nullable', 'string', 'max:5']
                : ['required', 'string', 'max:5'],
            'detalle' => 'required|string|max:255',
            'controller' => 'nullable|string|max:150',
            'action' => 'nullable|string|max:150',
            'orden' => 'nullable|integer|min:0|max:99999',
        ]);

        return [
            'codigo' => $isUpdate ? (string) $currentCodigo : trim((string) $data['codigo']),
            'detalle' => trim((string) $data['detalle']),
            'controller' => filled($data['controller'] ?? null) ? trim((string) $data['controller']) : null,
            'action' => filled($data['action'] ?? null) ? trim((string) $data['action']) : null,
            'orden' => array_key_exists('orden', $data) && $data['orden'] !== null && $data['orden'] !== ''
                ? (int) $data['orden']
                : null,
        ];
    }
}
