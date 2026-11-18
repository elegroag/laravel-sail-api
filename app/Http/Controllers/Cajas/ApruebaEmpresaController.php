<?php

namespace App\Http\Controllers\Cajas;

use App\Exceptions\DebugException;
use App\Http\Controllers\Cajas\Concerns\EditaSolicitud;
use App\Http\Controllers\Controller;
use App\Models\Mercurio30;
use App\Services\Cajas\AprobacionEmpresaService;
use App\Services\Cajas\Solicitudes\SolicitudDetalleService;
use App\Services\Cajas\Solicitudes\SolicitudListService;
use App\Services\CajaServices\EditarSolicitudFormulario;
use App\Services\CajaServices\EditarSolicitudService;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ApruebaEmpresaController extends Controller
{
    use EditaSolicitud;

    protected string $tipopc = AprobacionEmpresaService::TIPOPC;

    protected ?array $user;

    public function __construct(
        private readonly AprobacionEmpresaService $service,
        private readonly SolicitudDetalleService $detalle,
    ) {}

    /**
     * El controlador se instancia antes de que el middleware inicie la sesión, por eso el usuario se lee al despachar.
     */
    public function callAction($method, $parameters)
    {
        $this->user = session('user') ?? null;

        return $this->{$method}(...array_values($parameters));
    }

    public function index(Request $request): Response
    {
        $usuario = (string) ($this->user['usuario'] ?? '');
        $filters = $this->service->filtersFromRequest($request);
        $paginator = $this->service->paginate($filters, $usuario);
        $counts = $this->service->counts($usuario);

        $estadoOptions = [];
        foreach (SolicitudListService::ESTADOS as $value => $label) {
            $estadoOptions[] = ['value' => $value, 'label' => $label, 'count' => $counts[$value] ?? 0];
        }

        return Inertia::render('Cajas/AprobacionEmp/Index', [
            'items' => [
                'data' => $this->service->mapRows($paginator),
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
            'filters' => $filters,
            'estadoOptions' => $estadoOptions,
            'perPageOptions' => SolicitudListService::PER_PAGE_OPTIONS,
        ]);
    }

    public function show(int $id): Response
    {
        $solicitud = $this->solicitudOrRedirect($id);
        $sisu = $this->service->consultarSisu((string) $solicitud->nit);
        $acciones = $this->service->acciones($solicitud, $sisu['afiliada']);

        return Inertia::render('Cajas/AprobacionEmp/Show', [
            'solicitud' => $this->resumen($solicitud),
            'secciones' => $this->service->secciones($solicitud),
            'adjuntos' => $this->detalle->adjuntos($this->tipopc, $id, route('cajas.aprobacionemp.show', ['id' => $id])),
            'seguimiento' => $this->detalle->seguimiento($this->tipopc, $id),
            'acciones' => $acciones,
            'sisu' => $sisu,
            'motivos' => $this->detalle->motivos(),
            'camposCorregibles' => $this->detalle->camposCorregibles(),
            'aprobar' => $acciones['aprobar'] ? $this->service->aprobarFormulario($solicitud) : null,
        ]);
    }

    public function archivo(Request $request, int $id, string $coddoc): BinaryFileResponse|JsonResponse|RedirectResponse
    {
        $this->solicitudOrRedirect($id);
        $archivo = $this->detalle->archivoAdjunto($this->tipopc, $id, $coddoc);
        if ($archivo === null) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'El archivo no está disponible.'], 404);
            }

            return redirect()->route('cajas.aprobacionemp.show', ['id' => $id])->with('error', 'El archivo no está disponible.');
        }

        return response()->file($archivo['path'], [
            'Content-Disposition' => ($request->boolean('download') ? 'attachment' : 'inline').'; filename="'.$archivo['name'].'"',
        ]);
    }

    public function edit(int $id, EditarSolicitudService $editar, EditarSolicitudFormulario $formulario): Response|RedirectResponse
    {
        $solicitud = $this->solicitudOrRedirect($id);

        try {
            $data = $editar->solicitudEditable($this->tipopc, $id);
            $campos = $formulario->campos($this->tipopc);
        } catch (DebugException $e) {
            return redirect()->route('cajas.aprobacionemp.show', ['id' => $id])->with('error', $e->getMessage());
        }

        $editables = array_flip($editar->campos($this->tipopc));
        $valores = array_map(fn ($v) => $v === null ? '' : (string) $v, array_intersect_key($data, $editables));

        return Inertia::render('Cajas/AprobacionEmp/Edit', [
            'solicitud' => $this->resumen($solicitud),
            'data' => $valores,
            'campos' => $campos,
        ]);
    }

    public function update(Request $request, int $id, EditarSolicitudService $editar): RedirectResponse
    {
        $this->solicitudOrRedirect($id);
        $request->merge(['id' => $id]);

        try {
            $validated = $request->validate($editar->rules($this->tipopc));
            $resultado = $editar->editar(
                $this->tipopc,
                $id,
                $this->datosEdicion($validated),
                (string) ($this->user['usuario'] ?? '')
            );
        } catch (DebugException $e) {
            return back()->withErrors(['form' => $e->getMessage()])->withInput();
        }

        return redirect()
            ->route('cajas.aprobacionemp.show', ['id' => $id])
            ->with('success', $resultado['cambios']
                ? 'La información de la solicitud se actualizó con éxito.'
                : 'No se detectaron cambios en la información de la solicitud.');
    }

    public function aprobar(Request $request, int $id): RedirectResponse
    {
        $this->solicitudOrRedirect($id);
        $validated = $request->validate(
            $this->service->aprobarRules(),
            ['required' => 'El campo :attribute es obligatorio.'],
            $this->service->aprobarAtributos()
        );

        try {
            $correo = $this->service->aprobar($id, $validated);
        } catch (DebugException $e) {
            return back()->withErrors(['form' => $e->getMessage()])->withInput();
        } catch (\Throwable $e) {
            Log::error('Aprobación empresa falló', ['id' => $id, 'error' => $e->getMessage()]);

            return back()->withErrors(['form' => 'No se pudo aprobar la empresa: '.$e->getMessage()])->withInput();
        }

        return redirect()
            ->route('cajas.aprobacionemp.index')
            ->with('success', $correo
                ? 'La empresa fue aprobada y registrada en SISU.'
                : 'La empresa fue aprobada y registrada en SISU, pero no se pudo enviar el correo de notificación.');
    }

    public function devolver(Request $request, int $id): RedirectResponse
    {
        $this->solicitudOrRedirect($id);
        $validated = $request->validate([
            'codest' => 'required|string|max:10',
            'nota' => 'required|string|max:800',
            'campos_corregir' => 'sometimes|array',
            'campos_corregir.*' => 'string|max:100',
        ], ['required' => 'El campo :attribute es obligatorio.'], ['codest' => 'motivo', 'nota' => 'nota']);

        try {
            $this->service->devolver($id, $validated['codest'], $validated['nota'], $validated['campos_corregir'] ?? []);
        } catch (DebugException $e) {
            return back()->withErrors(['form' => $e->getMessage()])->withInput();
        } catch (\Throwable $e) {
            Log::error('Devolución empresa falló', ['id' => $id, 'error' => $e->getMessage()]);

            return back()->withErrors(['form' => 'No se pudo devolver la solicitud: '.$e->getMessage()])->withInput();
        }

        return redirect()->route('cajas.aprobacionemp.index')->with('success', 'La solicitud fue devuelta con éxito.');
    }

    public function rechazar(Request $request, int $id): RedirectResponse
    {
        $this->solicitudOrRedirect($id);
        $validated = $request->validate([
            'codest' => 'required|string|max:10',
            'nota' => 'required|string|max:800',
        ], ['required' => 'El campo :attribute es obligatorio.'], ['codest' => 'motivo', 'nota' => 'nota']);

        try {
            $this->service->rechazar($id, $validated['codest'], $validated['nota']);
        } catch (DebugException $e) {
            return back()->withErrors(['form' => $e->getMessage()])->withInput();
        } catch (\Throwable $e) {
            Log::error('Rechazo empresa falló', ['id' => $id, 'error' => $e->getMessage()]);

            return back()->withErrors(['form' => 'No se pudo rechazar la solicitud: '.$e->getMessage()])->withInput();
        }

        return redirect()->route('cajas.aprobacionemp.index')->with('success', 'La solicitud fue rechazada con éxito.');
    }

    public function reaprobar(Request $request, int $id): RedirectResponse
    {
        $this->solicitudOrRedirect($id);
        $validated = $request->validate(
            ['nota' => 'required|string|max:800'],
            ['required' => 'El campo :attribute es obligatorio.'],
            ['nota' => 'nota']
        );

        try {
            $this->service->reaprobar($id, $validated['nota']);
        } catch (\Throwable $e) {
            Log::error('Reaprobación empresa falló', ['id' => $id, 'error' => $e->getMessage()]);

            return back()->withErrors(['form' => 'No se pudo realizar el movimiento: '.$e->getMessage()])->withInput();
        }

        return redirect()->route('cajas.aprobacionemp.show', ['id' => $id])->with('success', 'Movimiento realizado con éxito.');
    }

    public function deshacerForm(int $id): Response|RedirectResponse
    {
        $solicitud = $this->solicitudOrRedirect($id);
        if ($solicitud->estado !== 'A') {
            return redirect()->route('cajas.aprobacionemp.show', ['id' => $id])
                ->with('error', 'Solo se puede deshacer la afiliación de solicitudes aprobadas.');
        }

        return Inertia::render('Cajas/AprobacionEmp/Deshacer', [
            'solicitud' => $this->resumen($solicitud),
            'motivos' => $this->detalle->motivos(),
        ]);
    }

    public function deshacer(Request $request, int $id): RedirectResponse
    {
        $this->solicitudOrRedirect($id);
        $validated = $request->validate([
            'action' => 'required|in:D,R,I',
            'codest' => 'required_if:action,D,R|nullable|string|max:10',
            'nota' => 'required|string|max:800',
            'send_email' => 'sometimes|boolean',
        ], ['required' => 'El campo :attribute es obligatorio.', 'required_if' => 'El campo :attribute es obligatorio.'], [
            'action' => 'acción',
            'codest' => 'motivo',
            'nota' => 'nota',
        ]);

        try {
            $resultado = $this->service->deshacer(
                $id,
                $validated['action'],
                $validated['codest'] ?? null,
                $validated['nota'],
                (bool) ($validated['send_email'] ?? false)
            );
        } catch (DebugException $e) {
            return back()->withErrors(['form' => 'No se pudo deshacer la aprobación: '.$e->getMessage()])->withInput();
        } catch (\Throwable $e) {
            Log::error('Deshacer aprobación empresa falló', ['id' => $id, 'error' => $e->getMessage()]);

            return back()->withErrors(['form' => 'No se pudo deshacer la aprobación: '.$e->getMessage()])->withInput();
        }

        if (! $resultado['success']) {
            return back()->withErrors(['form' => $resultado['msj']])->withInput();
        }

        return redirect()->route('cajas.aprobacionemp.show', ['id' => $id])->with('success', $resultado['msj']);
    }

    public function aportes(int $id): Response
    {
        $solicitud = $this->solicitudOrRedirect($id);

        return Inertia::render('Cajas/AprobacionEmp/Aportes', [
            'solicitud' => $this->resumen($solicitud),
            'resultado' => $this->service->aportes($solicitud),
        ]);
    }

    protected function datosEdicion(array $validated): array
    {
        if (isset($validated['tipsoc']) && strlen($validated['tipsoc']) == 1) {
            $validated['tipsoc'] = str_pad($validated['tipsoc'], 2, '0', STR_PAD_LEFT);
        }

        $tipper = $validated['tipper'] ?? null;
        if ($tipper === 'N' || $tipper === 'J') {
            $sufijo = $tipper === 'N' ? '' : 'repleg';
            $repleg = trim(preg_replace('/\s+/', ' ', implode(' ', [
                $validated["priape{$sufijo}"] ?? '',
                $validated["segape{$sufijo}"] ?? '',
                $validated["prinom{$sufijo}"] ?? '',
                $validated["segnom{$sufijo}"] ?? '',
            ])));
            if ($repleg !== '') {
                $validated['repleg'] = $repleg;
            }
        }

        return $validated;
    }

    private function solicitudOrRedirect(int $id): Mercurio30
    {
        try {
            return $this->service->findOrFail($id);
        } catch (DebugException $e) {
            throw new HttpResponseException(
                redirect()->route('cajas.aprobacionemp.index')->with('error', $e->getMessage())
            );
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function resumen(Mercurio30 $s): array
    {
        return [
            'id' => (int) $s->id,
            'nit' => (string) $s->nit,
            'razsoc' => (string) $s->razsoc,
            'estado' => (string) $s->estado,
            'estado_detalle' => (string) $s->getEstadoDetalle(),
            'motivo' => (string) $s->motivo,
            'urls' => [
                'index' => route('cajas.aprobacionemp.index'),
                'show' => route('cajas.aprobacionemp.show', ['id' => $s->id]),
            ],
        ];
    }
}
