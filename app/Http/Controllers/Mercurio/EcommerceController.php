<?php

namespace App\Http\Controllers\Mercurio;

use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\EpaycoTransaccion;
use App\Models\PrecompraServicio;
use App\Services\Api\ApiEpayco;
use App\Services\Api\ApiSubsidio;
use App\Services\Ecommerce\EstadoPrecompra;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
                'EPAYCO_TEST' => config('app.epayco.mode') === 'development' ? true : false,
                'EPAYCO_CHECKOUT_VERSION' => (string) config('app.epayco.checkout_version', '1'),
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
            'EPAYCO_TEST' => config('app.epayco.mode') === 'development' ? true : false,
            'EPAYCO_CHECKOUT_VERSION' => (string) config('app.epayco.checkout_version', '1'),
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
                    'message' => 'Error al cargar servicios: '.$e->getMessage(),
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
                    'message' => 'Error al validar tarifa: '.$e->getMessage(),
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
                'message' => 'Error al registrar la precompra: '.$e->getMessage(),
            ]);
        }
    }

    /**
     * POST /mercurio/servicios/crear-sesion-epayco
     * AJAX: Crea (o reutiliza) la precompra y genera una sesion de Smart Checkout v2
     * en ePayco (Apify). Devuelve el sessionId para inicializar el checkout en el front.
     */
    public function crearSesionCheckout(Request $request): JsonResponse
    {
        try {
            $data = $request->validate([
                'cedtra' => 'required|string|max:20',
                'codser' => 'required|string|max:20',
                'numero' => 'required|integer|min:1',
                'codben' => 'nullable|string|max:20',
                'nota' => 'nullable|string',
                'valor' => 'required|numeric|min:1',
                'nombre_servicio' => 'nullable|string|max:150',
                'nombre' => 'nullable|string|max:150',
                'email' => 'nullable|string|max:150',
                'precompra_id' => 'nullable|integer',
            ]);

            $precompra = null;
            if (! empty($data['precompra_id'])) {
                $precompra = PrecompraServicio::where('id', $data['precompra_id'])
                    ->where('documento', $data['cedtra'])
                    ->where('estado', EstadoPrecompra::PENDIENTE)
                    ->first();
            }

            if (! $precompra) {
                $precompra = PrecompraServicio::create([
                    'documento' => $data['cedtra'],
                    'codser' => $data['codser'],
                    'numero' => $data['numero'],
                    'codben' => ! empty($data['codben']) ? $data['codben'] : $data['cedtra'],
                    'nota' => $data['nota'] ?? '',
                    'valor' => $data['valor'],
                    'estado' => EstadoPrecompra::PENDIENTE,
                    'fecha_precompra' => now(),
                ]);
            }

            $nombreServicio = ! empty($data['nombre_servicio']) ? $data['nombre_servicio'] : 'Compra de servicio';

            $sesion = $this->epayco->crearSesionCheckout([
                'name' => $nombreServicio,
                'description' => $nombreServicio,
                'invoice' => 'ORD'.$precompra->id.'-'.time(),
                'currency' => 'COP',
                'amount' => (float) $data['valor'],
                'lang' => 'ES',
                'country' => 'CO',
                'response' => route('servicios.index'),
                'confirmation' => route('api.epayco.confirmation'),
                'extras' => [
                    'extra1' => $data['cedtra'],
                    'extra2' => $data['codser'],
                    'extra3' => (string) $data['numero'],
                    'extra4' => (string) $precompra->id,
                ],
                'billing' => [
                    'email' => ! empty($data['email']) ? $data['email'] : 'sin@email.com',
                    'name' => ! empty($data['nombre']) ? $data['nombre'] : 'Cliente',
                    'typeDoc' => 'CC',
                    'numberDoc' => $data['cedtra'],
                ],
            ]);

            if (! ($sesion['success'] ?? false)) {
                return response()->json([
                    'success' => false,
                    'message' => $sesion['errors'] ?? 'No se pudo crear la sesion de pago',
                ]);
            }

            $this->setLogger("Sesion Smart Checkout creada - precompra: {$precompra->id}, sessionId: {$sesion['sessionId']}");

            return response()->json([
                'success' => true,
                'data' => [
                    'sessionId' => $sesion['sessionId'],
                    'precompra_id' => $precompra->id,
                ],
                'message' => 'Sesion de checkout creada',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Datos invalidos para crear la sesion de pago',
                'errors' => $e->errors(),
            ]);
        } catch (\Throwable $e) {
            $this->setLogger($e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al crear la sesion de pago: '.$e->getMessage(),
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

            // Solo auditoría + ref ePayco: no promover a PA/RE aquí.
            // guardarVenta (o el webhook) es quien cambia el estado y llama a Subsidio.
            $this->actualizarPrecompraDesdePago($data, $precompraId, 'validacion', false);

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
                    'message' => 'Error al validar pago: '.$e->getMessage(),
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

            Log::info('Ecommerce.guardarVenta: inicio', [
                'cedtra' => $cedtra,
                'codser' => $codser,
                'numero' => $numero,
                'refpago' => $refpago,
                'codben' => $codben,
                'precompra_id' => $precompraId,
            ]);

            if (empty(trim((string) $refpago))) {
                Log::info('Ecommerce.guardarVenta: refpago vacia');

                return response()->json([
                    'success' => false,
                    'message' => 'Referencia de pago no proporcionada',
                ]);
            }

            $pago = $this->epayco->validarReferencia($refpago);

            Log::info('Ecommerce.guardarVenta: resultado validarReferencia', [
                'refpago' => $refpago,
                'success' => $pago['success'] ?? false,
                'aprobado' => $pago['data']['aprobado'] ?? null,
                'cod_estado' => $pago['data']['cod_estado'] ?? null,
                'respuesta' => $pago['data']['respuesta'] ?? null,
                'motivo' => $pago['data']['motivo'] ?? null,
                'errors' => $pago['errors'] ?? null,
            ]);

            if (! ($pago['success'] ?? false)) {
                Log::info('Ecommerce.guardarVenta: fallo validacion ePayco', [
                    'refpago' => $refpago,
                    'errors' => $pago['errors'] ?? null,
                ]);

                return response()->json([
                    'success' => false,
                    'message' => $pago['errors'] ?? 'No se pudo validar el pago en ePayco',
                ]);
            }

            $datosPago = $pago['data'] ?? [];
            $pagoAprobado = ($datosPago['aprobado'] ?? false) === true && (int) ($datosPago['cod_estado'] ?? 0) === 1;

            // Capturar PA antes de actualizar: si el webhook (u otro request) ya dejó
            // la precompra pagada, no reenviar guardar-venta a Subsidio (salvo FORCE_APPROVED).
            $refPaycoDatos = (string) ($datosPago['ref_payco'] ?? $refpago);
            $precompraAntes = $this->resolverPrecompraPorRefOId($refPaycoDatos, $precompraId);
            $yaPagada = $precompraAntes?->isPagado() ?? false;
            $forceApproved = $this->epayco->debeForzarAprobacion();

            $this->actualizarPrecompraDesdePago($datosPago, $precompraId);

            if (! $pagoAprobado) {
                $estado = $datosPago['cod_estado'] ?? 'desconocido';
                $motivo = $datosPago['motivo'] ?? $datosPago['respuesta'] ?? 'Pago no aprobado';

                Log::info('Ecommerce.guardarVenta: pago no aprobado', [
                    'refpago' => $refpago,
                    'cod_estado' => $estado,
                    'motivo' => $motivo,
                    'precompra_id' => $precompraId,
                ]);

                return response()->json([
                    'success' => false,
                    'message' => "El pago no fue aprobado en ePayco. Estado: {$estado}. {$motivo}",
                ]);
            }

            if ($yaPagada && ! $forceApproved) {
                Log::info('Ecommerce.guardarVenta: precompra ya PA, omitiendo Subsidio', [
                    'refpago' => $refpago,
                    'precompra_id' => $precompraAntes?->id ?? $precompraId,
                ]);

                return response()->json([
                    'success' => true,
                    'data' => [
                        'ya_pagada' => true,
                        'precompra_id' => $precompraAntes?->id,
                    ],
                    'message' => 'Venta ya registrada previamente',
                ]);
            }

            if ($yaPagada && $forceApproved) {
                Log::info('Ecommerce.guardarVenta: precompra ya PA pero FORCE_APPROVED, enviando a Subsidio', [
                    'refpago' => $refpago,
                    'precompra_id' => $precompraAntes?->id ?? $precompraId,
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

            Log::info('Ecommerce.guardarVenta: enviando a Subsidio guardar-venta', $params);

            $this->api->send([
                'servicio' => 'Movil',
                'metodo' => 'guardar-venta',
                'params' => $params,
            ]);

            $resultado = $this->api->toArray();

            Log::info('Ecommerce.guardarVenta: respuesta Subsidio', [
                'flag' => $resultado['flag'] ?? null,
                'message' => $resultado['message'] ?? null,
                'refpago' => $refpago,
            ]);

            if (! ($resultado['flag'] ?? false)) {
                $msg = $resultado['message'] ?? 'Error al guardar la venta';

                return response()->json([
                    'success' => false,
                    'message' => $msg,
                ]);
            }

            $codbenLog = ! empty($codben) ? $codben : $cedtra;
            $this->setLogger("Venta Servicio - cedtra: $cedtra, codben: $codbenLog, codser: $codser, refpago: $refpago");

            Log::info('Ecommerce.guardarVenta: venta guardada exitosamente', [
                'cedtra' => $cedtra,
                'codben' => $codbenLog,
                'codser' => $codser,
                'numero' => $numero,
                'refpago' => $refpago,
                'precompra_id' => $precompraId,
            ]);

            return response()->json([
                'success' => true,
                'data' => $resultado['data'] ?? [],
                'message' => 'Venta guardada exitosamente',
            ]);
        } catch (\Throwable $e) {
            $this->setLogger($e->getMessage());
            Log::info('Ecommerce.guardarVenta: excepcion', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Error al guardar la venta: '.$e->getMessage(),
                ]
            );
        }
    }

    /**
     * Resuelve la precompra por ref_payco o por id (para idempotencia de guardar-venta).
     */
    protected function resolverPrecompraPorRefOId(string $refPayco, int $precompraId): ?PrecompraServicio
    {
        if ($refPayco !== '') {
            $byRef = PrecompraServicio::where('ref_payco', $refPayco)->first();
            if ($byRef) {
                return $byRef;
            }
        }

        if ($precompraId > 0) {
            return PrecompraServicio::where('id', $precompraId)->first();
        }

        return null;
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
                    'message' => 'Error al cargar compras: '.$e->getMessage(),
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
                ->map(fn (PrecompraServicio $precompra) => [
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
                'message' => 'Error al cargar las compras pendientes: '.$e->getMessage(),
            ]);
        }
    }

    /**
     * POST /mercurio/servicios/historial-precompras
     * AJAX: Historial local de precompras del usuario (todos los estados).
     */
    public function historialPrecompras(): JsonResponse
    {
        try {
            $documento = $this->user['documento'] ?? '';

            $precompras = PrecompraServicio::where('documento', $documento)
                ->orderByDesc('fecha_precompra')
                ->orderByDesc('id')
                ->get()
                ->map(fn (PrecompraServicio $precompra) => [
                    'id' => $precompra->id,
                    'codser' => $precompra->codser,
                    'numero' => $precompra->numero,
                    'codben' => $precompra->codben,
                    'nota' => $precompra->nota,
                    'valor' => $precompra->valor,
                    'estado' => $precompra->estado,
                    'estado_descripcion' => $precompra->estado_descripcion,
                    'ref_payco' => $precompra->ref_payco,
                    'cod_estado_epayco' => $precompra->cod_estado_epayco,
                    'motivo_epayco' => $precompra->motivo_epayco,
                    'fecha_precompra' => $precompra->fecha_precompra?->format('Y-m-d H:i'),
                    'fecha_pago' => $precompra->fecha_pago?->format('Y-m-d H:i'),
                ]);

            return response()->json([
                'success' => true,
                'data' => $precompras,
                'message' => '',
            ]);
        } catch (\Throwable $e) {
            $this->setLogger($e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al cargar el historial de precompras: '.$e->getMessage(),
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
                'motivo' => 'required|string|in:'.implode(',', array_keys(EstadoPrecompra::MOTIVOS_DESESTIMACION)),
                'detalle' => 'required_if:motivo,'.EstadoPrecompra::MOTIVO_OTRO.'|nullable|string|max:255',
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
                'message' => 'Error al desestimar la compra: '.$e->getMessage(),
            ]);
        }
    }

    /**
     * POST /mercurio/servicios/abandonar-precompra
     * AJAX: Marca como abandonada (AB) una precompra pendiente cuando el usuario
     * cierra el checkout de ePayco sin completar el pago. No es retomable.
     */
    public function abandonarPrecompra(Request $request): JsonResponse
    {
        try {
            $data = $request->validate([
                'precompra_id' => 'required|integer|min:1',
            ]);

            $documento = self::getActUser('documento');

            $precompra = PrecompraServicio::where('id', $data['precompra_id'])
                ->where('documento', $documento)
                ->first();

            if (! $precompra) {
                return response()->json([
                    'success' => false,
                    'message' => 'La precompra no existe',
                ]);
            }

            // Si ya se pago o gestiono, no degradar
            if ($precompra->isPagado() || $precompra->isDesestimado() || $precompra->isRechazado()) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'id' => $precompra->id,
                        'estado' => $precompra->estado,
                        'abandonada' => false,
                    ],
                    'message' => 'La precompra ya fue gestionada',
                ]);
            }

            if ($precompra->isAbandonada()) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'id' => $precompra->id,
                        'estado' => $precompra->estado,
                        'abandonada' => true,
                    ],
                    'message' => 'La precompra ya estaba abandonada',
                ]);
            }

            if (! $precompra->isPendiente()) {
                return response()->json([
                    'success' => false,
                    'message' => 'La precompra no se puede abandonar en su estado actual',
                ]);
            }

            $precompra->fill([
                'estado' => EstadoPrecompra::ABANDONADA,
                'motivo_desestimacion' => EstadoPrecompra::MOTIVO_ABANDONO_CHECKOUT,
                'detalle_desestimacion' => 'Cierre del checkout de ePayco sin completar el pago',
                'fecha_desestimacion' => now(),
            ]);
            $precompra->save();

            $this->setLogger("Precompra {$precompra->id} abandonada por cierre de checkout - documento: {$documento}");

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $precompra->id,
                    'estado' => $precompra->estado,
                    'abandonada' => true,
                ],
                'message' => 'La compra fue marcada como abandonada',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Datos invalidos',
                'errors' => $e->errors(),
            ]);
        } catch (\Throwable $e) {
            $this->setLogger($e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al abandonar la compra: '.$e->getMessage(),
            ]);
        }
    }

    /**
     * Actualiza la precompra segun la respuesta de ePayco.
     * Busca por ref_payco y, si no hay coincidencia, por el id de precompra
     * enviado desde el frontend (pendiente o abandonada por carrera con onClose).
     * Siempre registra un snapshot en epayco_transacciones para auditoria.
     *
     * @param  bool  $promoverEstado  Si false, solo actualiza ref/codigos ePayco sin cambiar estado (PE/AB/…).
     */
    protected function actualizarPrecompraDesdePago(
        array $datosPago,
        int $precompraId = 0,
        string $origen = 'validacion',
        bool $promoverEstado = true
    ): void {
        try {
            $refPayco = (string) ($datosPago['ref_payco'] ?? '');

            $precompra = null;
            if ($refPayco !== '') {
                $precompra = PrecompraServicio::where('ref_payco', $refPayco)->first();
            }

            $precompraActualizable = $precompra;

            if (! $precompraActualizable && $precompraId > 0) {
                $precompraActualizable = PrecompraServicio::where('id', $precompraId)
                    ->whereIn('estado', [EstadoPrecompra::PENDIENTE, EstadoPrecompra::ABANDONADA])
                    ->first();
            }

            // Asociar auditoria aunque la precompra ya no sea PE/AB
            if (! $precompra && ! $precompraActualizable && $precompraId > 0) {
                $precompra = PrecompraServicio::where('id', $precompraId)->first();
            } elseif (! $precompra) {
                $precompra = $precompraActualizable;
            }

            // Sin promover estado: permitir actualizar metadata en cualquier precompra por id
            if (! $precompraActualizable && ! $promoverEstado && $precompraId > 0) {
                $precompraActualizable = PrecompraServicio::where('id', $precompraId)->first();
                if (! $precompra) {
                    $precompra = $precompraActualizable;
                }
            }

            $this->registrarTransaccionEpayco($precompra?->id ?? $precompraActualizable?->id, $datosPago, $origen);

            if (! $precompraActualizable) {
                return;
            }

            $codEstado = (int) ($datosPago['cod_estado'] ?? 0);
            $motivo = mb_substr((string) ($datosPago['motivo'] ?: ($datosPago['respuesta'] ?? '')), 0, 255);

            if (! $promoverEstado) {
                $precompraActualizable->fill([
                    'ref_payco' => $refPayco !== '' ? $refPayco : $precompraActualizable->ref_payco,
                    'cod_estado_epayco' => (string) $codEstado,
                    'motivo_epayco' => $motivo,
                ]);
                $precompraActualizable->save();

                $this->setLogger("Precompra {$precompraActualizable->id} metadata ePayco actualizada sin cambio de estado (ePayco: {$codEstado}, ref: {$refPayco})");

                return;
            }

            // No degradar una precompra que ya quedo pagada
            if ($precompraActualizable->isPagado()) {
                return;
            }

            $nuevoEstado = EstadoPrecompra::desdeCodigoEpayco($codEstado);

            $precompraActualizable->fill([
                'estado' => $nuevoEstado,
                'ref_payco' => $refPayco !== '' ? $refPayco : $precompraActualizable->ref_payco,
                'cod_estado_epayco' => (string) $codEstado,
                'motivo_epayco' => $motivo,
            ]);

            if ($nuevoEstado === EstadoPrecompra::PAGADO && ! $precompraActualizable->fecha_pago) {
                $precompraActualizable->fecha_pago = now();
            }

            $precompraActualizable->save();

            $this->setLogger("Precompra {$precompraActualizable->id} actualizada a estado {$nuevoEstado} (ePayco: {$codEstado}, ref: {$refPayco})");
        } catch (\Throwable $e) {
            // La trazabilidad de la precompra no debe romper el flujo de pago
            $this->setLogger('Error actualizando precompra: '.$e->getMessage());
        }
    }

    /**
     * Guarda un snapshot de la respuesta ePayco. No debe romper el flujo de pago.
     *
     * @param  array<string, mixed>  $datosPago
     */
    protected function registrarTransaccionEpayco(?int $precompraId, array $datosPago, string $origen = 'validacion'): void
    {
        try {
            EpaycoTransaccion::registrarDesdeValidacion($precompraId, $datosPago, $origen);
        } catch (\Throwable $e) {
            $this->setLogger('Error registrando epayco_transacciones: '.$e->getMessage());
        }
    }
}
