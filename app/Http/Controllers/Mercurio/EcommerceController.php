<?php

namespace App\Http\Controllers\Mercurio;

use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\PrecompraServicio;
use App\Services\Api\ApiEpayco;
use App\Services\Api\ApiSubsidio;
use App\Services\Ecommerce\EstadoPrecompra;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * Controlador de Servicios - Venta de Servicios de Cajas
 *
 * APIs consumidas desde Portal_Mercurio:
 * - identifica-trabajador   (identifica trabajador por cedula)
 * - listar-servicios        (lista servicios disponibles)
 * - validar-tarifas         (valida condiciones y retorna tarifa)
 * - guardar-venta           (guarda la venta)
 * - mis-compras              (consulta compras realizadas)
 */
class EcommerceController extends ApplicationController
{
    protected ApiSubsidio $api;

    protected ApiEpayco $epayco;

    protected ?array $user;

    protected ?string $tipo;

    public function __construct(ApiEpayco $epayco)
    {
        $this->api = new ApiSubsidio;
        $this->epayco = $epayco;
        $this->user = session('user') ?? null;
        $this->tipo = session('tipo') ?? null;
    }

    /**
     * GET /mercurio/servicios/index
     * Vista principal del formulario de compra de servicios
     */
    public function index()
    {
        if (config('app.app_mode') === 'production') {
            set_flashdata('notify', [
                'msj' => 'Estamos trabajando para habilitar muy pronto el catálogo de servicios. Agradecemos tu comprensión.',
                'code' => 503,
            ]);

            return redirect()->route('principal.index');
        }

        $documento = self::getActUser('documento');

        return view(
            'mercurio/ecommerce/index',
            [
                'EPAYCO_PUBLIC_KEY' => config('app.epayco.public_key'),
                'EPAYCO_TEST' => config('app.epayco.mode') === 'development' ? 'true' : 'false',
                'documento' => $documento,
                'pendientesCount' => PrecompraServicio::where('documento', $documento)
                    ->where('estado', EstadoPrecompra::PENDIENTE)
                    ->count(),
                'title' => 'Catálogo de Servicios',
            ]
        );
    }

    /**
     * GET /mercurio/servicios/compras-pendientes
     * Vista de precompras abandonadas (pendientes de pago)
     */
    public function comprasPendientes()
    {
        return view('mercurio/ecommerce/pendientes', [
            'EPAYCO_PUBLIC_KEY' => config('app.epayco.public_key'),
            'EPAYCO_TEST' => config('app.epayco.mode') === 'development' ? 'true' : 'false',
            'documento' => self::getActUser('documento'),
            'motivosDesestimacion' => EstadoPrecompra::MOTIVOS_DESESTIMACION,
            'title' => 'Compras Pendientes de Pago',
        ]);
    }

    /**
     * GET /mercurio/servicios/ver-compras
     */
    public function verCompras()
    {
        $documento = self::getActUser('documento');

        return view('mercurio/ecommerce/ver_compras', [
            'documento' => $documento,
            'title' => 'Mis Compras',
        ]);
    }

    /**
     * POST /mercurio/servicios/identificar-trabajador
     * AJAX: Identificar trabajador por cedula
     */
    public function identificarTrabajador(Request $request): JsonResponse
    {
        try {
            $cedtra = $request->input('cedtra');

            if (empty(trim($cedtra))) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Debe ingresar una cedula valida',
                    ]
                );
            }

            $this->api->send([
                'servicio' => 'Movil',
                'metodo' => 'identifica-trabajador',
                'params' => ['cedtra' => $cedtra],
            ]);

            $resultado = $this->api->toArray();

            if (! ($resultado['flag'] ?? false)) {
                return response()->json([
                    'success' => false,
                    'message' => $resultado['message'] ?? 'Trabajador no encontrado',
                ]);
            }

            $data = $resultado['data'] ?? [];

            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'Trabajador encontrado',
            ]);
        } catch (\Throwable $e) {
            $this->setLogger($e->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Error al buscar el trabajador',
                    'errors' => $e->getMessage(),
                ]
            );
        }
    }

    /**
     * POST /mercurio/servicios/listar-servicios
     * AJAX: Listar servicios disponibles
     */
    public function listarServicios(): JsonResponse
    {
        try {
            $this->api->send([
                'servicio' => 'Movil',
                'metodo' => 'listar-servicios',
            ]);

            $resultado = $this->api->toArray();

            if (! ($resultado['flag'] ?? false)) {
                return response()->json([
                    'success' => false,
                    'message' => $resultado['message'] ?? 'Error al cargar servicios',
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => $resultado['data'] ?? [],
                'message' => 'Proceso completado con éxito',
            ]);
        } catch (\Throwable $e) {
            $this->setLogger($e->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Error al cargar servicios: ' . $e->getMessage(),
                ]
            );
        }
    }

    /**
     * POST /mercurio/servicios/validar-tarifa
     * AJAX: Validar condiciones y obtener tarifa del servicio
     */
    public function validarTarifa(Request $request): JsonResponse
    {
        try {
            $cedtra = $request->input('cedtra');
            $codser = $request->input('codser');
            $numero = $request->input('numero');
            $codben = $request->input('codben');

            if (empty($codser)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Debe seleccionar un servicio',
                ]);
            }

            $params = [
                'cedtra' => $cedtra,
                'codser' => $codser,
                'numero' => $numero,
                'codben' => ! empty($codben) ? $codben : $cedtra,
            ];

            $this->api->send([
                'servicio' => 'Movil',
                'metodo' => 'validar-tarifas',
                'params' => $params,
            ]);

            $resultado = $this->api->toArray();

            if (! ($resultado['flag'] ?? false)) {
                $msg = $resultado['message'] ?? 'Error al validar tarifa';

                return response()->json([
                    'success' => false,
                    'message' => $msg,
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => $resultado['data'] ?? [],
                'message' => 'Tarifa obtenida',
            ]);
        } catch (\Throwable $e) {
            $this->setLogger($e->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Error al validar tarifa: ' . $e->getMessage(),
                ]
            );
        }
    }

    /**
     * POST /mercurio/servicios/crear-precompra
     * AJAX: Respaldar la precompra en base de datos antes de enviar el pago a ePayco.
     * La precompra nace en estado pendiente (PE) y transiciona segun la respuesta de ePayco.
     */
    public function crearPrecompra(Request $request): JsonResponse
    {
        try {
            $data = $request->validate([
                'cedtra' => 'required|string|max:20',
                'codser' => 'required|string|max:20',
                'numero' => 'required|integer|min:1',
                'codben' => 'nullable|string|max:20',
                'nota' => 'nullable|string',
                'valor' => 'nullable|numeric|min:0',
            ]);

            $precompra = PrecompraServicio::create([
                'documento' => $data['cedtra'],
                'codser' => $data['codser'],
                'numero' => $data['numero'],
                'codben' => ! empty($data['codben']) ? $data['codben'] : $data['cedtra'],
                'nota' => $data['nota'] ?? '',
                'valor' => $data['valor'] ?? null,
                'estado' => EstadoPrecompra::PENDIENTE,
                'fecha_precompra' => now(),
            ]);

            $this->setLogger("Precompra creada - id: {$precompra->id}, cedtra: {$precompra->documento}, codser: {$precompra->codser}");

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $precompra->id,
                    'estado' => $precompra->estado,
                ],
                'message' => 'Precompra registrada',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de precompra invalidos',
                'errors' => $e->errors(),
            ]);
        } catch (\Throwable $e) {
            $this->setLogger($e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al registrar la precompra: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * POST /mercurio/servicios/validar-pago-epayco
     * AJAX: Validar estado de pago en ePayco
     *
     * Estados de transaccion ePayco (x_cod_transaction_state):
     *   1 = Aceptada, 2 = Rechazada, 3 = Pendiente, 4 = Fallida
     *   6 = Reversada, 7 = Retenida, 8 = Iniciada, 9 = Expirada
     *   10 = Abandonada, 11 = Cancelada, 12 = Antifraude
     */
    public function validarPagoEpayco(Request $request): JsonResponse
    {
        try {
            $ref_payco = $request->input('ref_payco');
            $precompraId = (int) $request->input('precompra_id', 0);

            if (empty(trim($ref_payco))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Referencia de pago no proporcionada',
                ]);
            }

            $resultado = $this->epayco->validarReferencia($ref_payco);

            if (! $resultado['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $resultado['errors'] ?? 'Error al validar referencia',
                ]);
            }

            $data = $resultado['data'];
            $msg = $data['aprobado'] ? 'Pago aprobado' : 'Pago no aprobado';

            $this->actualizarPrecompraDesdePago($data, $precompraId);

            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => $msg,
            ]);
        } catch (\Throwable $e) {
            $this->setLogger($e->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Error al validar pago: ' . $e->getMessage(),
                ]
            );
        }
    }

    /**
     * POST /mercurio/servicios/guardar-venta
     * AJAX: Guardar venta despues del pago
     */
    public function guardarVenta(Request $request): JsonResponse
    {
        try {
            $cedtra = $request->input('cedtra');
            $codser = $request->input('codser');
            $numero = $request->input('numero');
            $refpago = $request->input('refpago');
            $nota = $request->input('nota', '');
            $codben = $request->input('codben');
            $precompraId = (int) $request->input('precompra_id', 0);

            if (empty(trim((string) $refpago))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Referencia de pago no proporcionada',
                ]);
            }

            $pago = $this->epayco->validarReferencia($refpago);

            if (! ($pago['success'] ?? false)) {
                return response()->json([
                    'success' => false,
                    'message' => $pago['errors'] ?? 'No se pudo validar el pago en ePayco',
                ]);
            }

            $datosPago = $pago['data'] ?? [];
            $pagoAprobado = ($datosPago['aprobado'] ?? false) === true && (int) ($datosPago['cod_estado'] ?? 0) === 1;

            $this->actualizarPrecompraDesdePago($datosPago, $precompraId);

            if (! $pagoAprobado) {
                $estado = $datosPago['cod_estado'] ?? 'desconocido';
                $motivo = $datosPago['motivo'] ?? $datosPago['respuesta'] ?? 'Pago no aprobado';

                return response()->json([
                    'success' => false,
                    'message' => "El pago no fue aprobado en ePayco. Estado: {$estado}. {$motivo}",
                ]);
            }

            $params = [
                'cedtra' => $cedtra,
                'codser' => $codser,
                'numero' => $numero,
                'refpago' => $refpago,
                'nota' => $nota,
                'codben' => ! empty($codben) ? $codben : $cedtra,
            ];

            $this->api->send([
                'servicio' => 'Movil',
                'metodo' => 'guardar-venta',
                'params' => $params,
            ]);

            $resultado = $this->api->toArray();

            if (! ($resultado['flag'] ?? false)) {
                $msg = $resultado['message'] ?? 'Error al guardar la venta';

                return response()->json([
                    'success' => false,
                    'message' => $msg,
                ]);
            }

            $codbenLog = ! empty($codben) ? $codben : $cedtra;
            $this->setLogger("Venta Servicio - cedtra: $cedtra, codben: $codbenLog, codser: $codser, refpago: $refpago");

            return response()->json([
                'success' => true,
                'data' => $resultado['data'] ?? [],
                'message' => 'Venta guardada exitosamente',
            ]);
        } catch (\Throwable $e) {
            $this->setLogger($e->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Error al guardar la venta: ' . $e->getMessage(),
                ]
            );
        }
    }

    /**
     * POST /mercurio/servicios/mis-compras
     * AJAX: Consultar compras realizadas
     */
    public function misCompras(Request $request): JsonResponse
    {
        try {
            $cedtra = $request->input('cedtra');

            if (empty(trim($cedtra))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Debe ingresar una cedula valida',
                ]);
            }

            $params = [
                'cedtra' => $cedtra,
                'limit' => (int) $request->input('limit', 500),
            ];

            $this->api->send([
                'servicio' => 'Movil',
                'metodo' => 'mis-compras',
                'params' => $params,
            ]);

            $resultado = $this->api->toArray();

            if (! ($resultado['flag'] ?? false)) {
                $msg = $resultado['message'] ?? 'Error al cargar compras';

                return response()->json([
                    'success' => false,
                    'message' => $msg,
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => $resultado['data'] ?? [],
                'message' => '',
            ]);
        } catch (\Throwable $e) {
            $this->setLogger($e->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Error al cargar compras: ' . $e->getMessage(),
                ]
            );
        }
    }

    /**
     * POST /mercurio/servicios/listar-precompras
     * AJAX: Listar precompras pendientes de pago del usuario en sesion
     */
    public function listarPrecompras(): JsonResponse
    {
        try {
            $documento = $this->user['documento'] ?? '';

            $precompras = PrecompraServicio::where('documento', $documento)
                ->where('estado', EstadoPrecompra::PENDIENTE)
                ->orderByDesc('fecha_precompra')
                ->get()
                ->map(fn(PrecompraServicio $precompra) => [
                    'id' => $precompra->id,
                    'codser' => $precompra->codser,
                    'numero' => $precompra->numero,
                    'codben' => $precompra->codben,
                    'nota' => $precompra->nota,
                    'valor' => $precompra->valor,
                    'estado' => $precompra->estado,
                    'estado_descripcion' => $precompra->estado_descripcion,
                    'fecha_precompra' => $precompra->fecha_precompra?->format('Y-m-d H:i'),
                ]);

            return response()->json([
                'success' => true,
                'data' => $precompras,
                'message' => '',
            ]);
        } catch (Exception $e) {
            $this->setLogger($e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al cargar las compras pendientes: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * POST /mercurio/servicios/desestimar-precompra
     * AJAX: Desestimar una precompra pendiente con un motivo del catalogo.
     * Si el motivo es OTRO se requiere el detalle en texto libre.
     */
    public function desestimarPrecompra(Request $request): JsonResponse
    {
        try {
            $data = $request->validate([
                'precompra_id' => 'required|integer|min:1',
                'motivo' => 'required|string|in:' . implode(',', array_keys(EstadoPrecompra::MOTIVOS_DESESTIMACION)),
                'detalle' => 'required_if:motivo,' . EstadoPrecompra::MOTIVO_OTRO . '|nullable|string|max:255',
            ], [
                'motivo.required' => 'Debe seleccionar un motivo',
                'motivo.in' => 'El motivo seleccionado no es válido',
                'detalle.required_if' => 'Debe indicar el motivo en el campo de texto',
            ]);

            $documento = self::getActUser('documento');

            $precompra = PrecompraServicio::where('id', $data['precompra_id'])
                ->where('documento', $documento)
                ->where('estado', EstadoPrecompra::PENDIENTE)
                ->first();

            if (! $precompra) {
                return response()->json([
                    'success' => false,
                    'message' => 'La compra pendiente no existe o ya fue gestionada',
                ]);
            }

            $precompra->fill([
                'estado' => EstadoPrecompra::DESESTIMADO,
                'motivo_desestimacion' => $data['motivo'],
                'detalle_desestimacion' => $data['motivo'] === EstadoPrecompra::MOTIVO_OTRO ? trim((string) $data['detalle']) : null,
                'fecha_desestimacion' => now(),
            ]);
            $precompra->save();

            $this->setLogger("Precompra {$precompra->id} desestimada - motivo: {$data['motivo']}, documento: {$documento}");

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $precompra->id,
                    'estado' => $precompra->estado,
                ],
                'message' => 'La compra fue desestimada correctamente',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => collect($e->errors())->flatten()->first() ?? 'Datos inválidos',
                'errors' => $e->errors(),
            ]);
        } catch (\Throwable $e) {
            $this->setLogger($e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al desestimar la compra: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Actualiza el estado de la precompra segun la respuesta de ePayco.
     * Busca por ref_payco y, si no hay coincidencia, por el id de precompra
     * enviado desde el frontend (solo si sigue pendiente).
     */
    protected function actualizarPrecompraDesdePago(array $datosPago, int $precompraId = 0): void
    {
        try {
            $refPayco = (string) ($datosPago['ref_payco'] ?? '');

            $precompra = null;
            if ($refPayco !== '') {
                $precompra = PrecompraServicio::where('ref_payco', $refPayco)->first();
            }

            if (! $precompra && $precompraId > 0) {
                $precompra = PrecompraServicio::where('id', $precompraId)
                    ->where('estado', EstadoPrecompra::PENDIENTE)
                    ->first();
            }

            if (! $precompra) {
                return;
            }

            // No degradar una precompra que ya quedo pagada
            if ($precompra->isPagado()) {
                return;
            }

            $codEstado = (int) ($datosPago['cod_estado'] ?? 0);
            $nuevoEstado = EstadoPrecompra::desdeCodigoEpayco($codEstado);

            $precompra->fill([
                'estado' => $nuevoEstado,
                'ref_payco' => $refPayco !== '' ? $refPayco : $precompra->ref_payco,
                'cod_estado_epayco' => (string) $codEstado,
                'motivo_epayco' => mb_substr((string) ($datosPago['motivo'] ?: ($datosPago['respuesta'] ?? '')), 0, 255),
            ]);

            if ($nuevoEstado === EstadoPrecompra::PAGADO && ! $precompra->fecha_pago) {
                $precompra->fecha_pago = now();
            }

            $precompra->save();

            $this->setLogger("Precompra {$precompra->id} actualizada a estado {$nuevoEstado} (ePayco: {$codEstado}, ref: {$refPayco})");
        } catch (\Throwable $e) {
            // La trazabilidad de la precompra no debe romper el flujo de pago
            $this->setLogger('Error actualizando precompra: ' . $e->getMessage());
        }
    }
}
