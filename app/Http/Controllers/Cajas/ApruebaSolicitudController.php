<?php

namespace App\Http\Controllers\Cajas;

use App\Exceptions\DebugException;
use App\Http\Controllers\Cajas\Concerns\EditaSolicitud;
use App\Http\Controllers\Controller;
use App\Services\Cajas\Solicitudes\SolicitudAprobacionService;
use App\Services\Cajas\Solicitudes\SolicitudDetalleService;
use App\Services\Cajas\Solicitudes\SolicitudListService;
use App\Services\CajaServices\EditarSolicitudFormulario;
use App\Services\CajaServices\EditarSolicitudService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Flujo común de los módulos de aprobación (listado, ficha, edición y acciones) sobre Inertia + React.
 * Cada módulo define su servicio, sus textos y registra sus rutas con SolicitudRoutes.
 */
abstract class ApruebaSolicitudController extends Controller
{
    use EditaSolicitud;

    protected string $tipopc;

    protected ?array $user;

    public function __construct(protected readonly SolicitudDetalleService $detalle) {}

    abstract protected function service(): SolicitudAprobacionService;

    /**
     * Textos del módulo para las páginas genéricas.
     *
     * @return array{titulo: string, singular: string, identificacion: string, nombre: string, busqueda: string}
     */
    abstract protected function modulo(): array;

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
        $service = $this->service();
        $usuario = (string) ($this->user['usuario'] ?? '');
        $filters = $service->filtersFromRequest($request);
        $paginator = $service->paginate($filters, $usuario);
        $counts = $service->counts($usuario);

        $estadoOptions = [];
        foreach (SolicitudListService::ESTADOS as $value => $label) {
            $estadoOptions[] = ['value' => $value, 'label' => $label, 'count' => $counts[$value] ?? 0];
        }

        return Inertia::render('Cajas/Solicitudes/pages/Index', [
            'modulo' => $this->modulo() + ['baseUrl' => route($service->routeName('index'), [], false)],
            'items' => [
                'data' => $service->mapRows($paginator),
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
        $service = $this->service();
        $solicitud = $this->solicitudOrRedirect($id);
        $sisu = $service->consultarSisu($solicitud);
        $acciones = $service->acciones($solicitud, $sisu['afiliada']);

        return Inertia::render('Cajas/Solicitudes/pages/Show', [
            'modulo' => $this->modulo(),
            'solicitud' => $service->resumen($solicitud),
            'secciones' => $service->secciones($solicitud),
            'cambios' => $this->cambios($solicitud),
            'adjuntos' => $this->detalle->adjuntos($this->tipopc, $id, route($service->routeName('show'), ['id' => $id])),
            'seguimiento' => $this->detalle->seguimiento($this->tipopc, $id),
            'acciones' => $acciones,
            'sisu' => $sisu,
            'sisuExtra' => $service->sisuExtra($solicitud),
            'motivos' => $this->detalle->motivos(),
            'camposCorregibles' => $this->detalle->camposCorregibles(),
            'aprobar' => $acciones['aprobar'] ? $service->aprobarFormulario($solicitud) : null,
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

            return $this->aFicha($id)->with('error', 'El archivo no está disponible.');
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
            return $this->aFicha($id)->with('error', $e->getMessage());
        }

        $editables = array_flip($editar->campos($this->tipopc));
        $valores = array_map(fn ($v) => $v === null ? '' : (string) $v, array_intersect_key($data, $editables));

        return Inertia::render('Cajas/Solicitudes/pages/Edit', [
            'modulo' => $this->modulo(),
            'solicitud' => $this->service()->resumen($solicitud),
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

        return $this->aFicha($id)->with('success', $resultado['cambios']
            ? 'La información de la solicitud se actualizó con éxito.'
            : 'No se detectaron cambios en la información de la solicitud.');
    }

    public function aprobar(Request $request, int $id): RedirectResponse
    {
        $service = $this->service();
        $this->solicitudOrRedirect($id);
        $validated = $request->validate(
            $service->aprobarRules(),
            ['required' => 'El campo :attribute es obligatorio.', 'required_if' => 'El campo :attribute es obligatorio.'],
            $service->aprobarAtributos()
        );

        try {
            $correo = $service->aprobar($id, $validated);
        } catch (DebugException $e) {
            return back()->withErrors(['form' => $e->getMessage()])->withInput();
        } catch (\Throwable $e) {
            Log::error('Aprobación de solicitud falló', ['tipopc' => $this->tipopc, 'id' => $id, 'error' => $e->getMessage()]);

            return back()->withErrors(['form' => 'No se pudo aprobar la solicitud: '.$e->getMessage()])->withInput();
        }

        return $this->aLista()->with('success', $correo
            ? 'La solicitud fue aprobada y registrada en SISU.'
            : 'La solicitud fue aprobada y registrada en SISU, pero no se pudo enviar el correo de notificación.');
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
            $this->service()->devolver($id, $validated['codest'], $validated['nota'], $validated['campos_corregir'] ?? []);
        } catch (DebugException $e) {
            return back()->withErrors(['form' => $e->getMessage()])->withInput();
        } catch (\Throwable $e) {
            Log::error('Devolución de solicitud falló', ['tipopc' => $this->tipopc, 'id' => $id, 'error' => $e->getMessage()]);

            return back()->withErrors(['form' => 'No se pudo devolver la solicitud: '.$e->getMessage()])->withInput();
        }

        return $this->aLista()->with('success', 'La solicitud fue devuelta con éxito.');
    }

    public function rechazar(Request $request, int $id): RedirectResponse
    {
        $this->solicitudOrRedirect($id);
        $validated = $request->validate([
            'codest' => 'required|string|max:10',
            'nota' => 'required|string|max:800',
        ], ['required' => 'El campo :attribute es obligatorio.'], ['codest' => 'motivo', 'nota' => 'nota']);

        try {
            $this->service()->rechazar($id, $validated['codest'], $validated['nota']);
        } catch (DebugException $e) {
            return back()->withErrors(['form' => $e->getMessage()])->withInput();
        } catch (\Throwable $e) {
            Log::error('Rechazo de solicitud falló', ['tipopc' => $this->tipopc, 'id' => $id, 'error' => $e->getMessage()]);

            return back()->withErrors(['form' => 'No se pudo rechazar la solicitud: '.$e->getMessage()])->withInput();
        }

        return $this->aLista()->with('success', 'La solicitud fue rechazada con éxito.');
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
            $this->service()->reaprobar($id, $validated['nota']);
        } catch (\Throwable $e) {
            Log::error('Reaprobación de solicitud falló', ['tipopc' => $this->tipopc, 'id' => $id, 'error' => $e->getMessage()]);

            return back()->withErrors(['form' => 'No se pudo realizar el movimiento: '.$e->getMessage()])->withInput();
        }

        return $this->aFicha($id)->with('success', 'Movimiento realizado con éxito.');
    }

    public function deshacerForm(int $id): Response|RedirectResponse
    {
        $solicitud = $this->solicitudOrRedirect($id);
        if ($solicitud->getAttribute('estado') !== 'A') {
            return $this->aFicha($id)->with('error', 'Solo se puede deshacer la afiliación de solicitudes aprobadas.');
        }

        return Inertia::render('Cajas/Solicitudes/pages/Deshacer', [
            'modulo' => $this->modulo(),
            'solicitud' => $this->service()->resumen($solicitud),
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
            $resultado = $this->service()->deshacer(
                $id,
                $validated['action'],
                $validated['codest'] ?? null,
                $validated['nota'],
                (bool) ($validated['send_email'] ?? false)
            );
        } catch (DebugException $e) {
            return back()->withErrors(['form' => 'No se pudo deshacer la aprobación: '.$e->getMessage()])->withInput();
        } catch (\Throwable $e) {
            Log::error('Deshacer aprobación falló', ['tipopc' => $this->tipopc, 'id' => $id, 'error' => $e->getMessage()]);

            return back()->withErrors(['form' => 'No se pudo deshacer la aprobación: '.$e->getMessage()])->withInput();
        }

        if (! $resultado['success']) {
            return back()->withErrors(['form' => $resultado['msj']])->withInput();
        }

        return $this->aFicha($id)->with('success', $resultado['msj']);
    }

    public function aportes(int $id): Response
    {
        $service = $this->service();
        $solicitud = $this->solicitudOrRedirect($id);

        return Inertia::render('Cajas/Solicitudes/pages/Aportes', [
            'modulo' => $this->modulo(),
            'solicitud' => $service->resumen($solicitud),
            'resultado' => $service->aportes($solicitud),
        ]);
    }

    public function validarMultiafiliacion(int $id): JsonResponse
    {
        $solicitud = $this->solicitudOrRedirect($id);

        return response()->json($this->service()->validarMultiafiliacion($solicitud));
    }

    public function trayectoria(int $id): Response
    {
        $service = $this->service();
        $solicitud = $this->solicitudOrRedirect($id);

        return Inertia::render('Cajas/Solicitudes/pages/Trayectoria', [
            'modulo' => $this->modulo(),
            'solicitud' => $service->resumen($solicitud),
            'resultado' => $service->trayectoria($solicitud),
        ]);
    }

    /**
     * Cambios solicitados (solo módulos de actualización de datos).
     *
     * @return list<array<string, string>>
     */
    protected function cambios(Model $solicitud): array
    {
        return [];
    }

    protected function datosEdicion(array $validated): array
    {
        return $validated;
    }

    protected function aLista(): RedirectResponse
    {
        return redirect()->route($this->service()->routeName('index'));
    }

    protected function aFicha(int $id): RedirectResponse
    {
        return redirect()->route($this->service()->routeName('show'), ['id' => $id]);
    }

    protected function solicitudOrRedirect(int $id): Model
    {
        try {
            return $this->service()->findOrFail($id);
        } catch (DebugException $e) {
            throw new HttpResponseException($this->aLista()->with('error', $e->getMessage()));
        }
    }
}
