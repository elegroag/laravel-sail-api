<?php

namespace App\Http\Controllers\Mercurio;

use App\Http\Controllers\Adapter\ApplicationController;
use App\Services\Api\ApiEpayco;
use App\Services\Api\ApiSubsidio;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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

    public function __construct(ApiEpayco $epayco)
    {
        $this->api = new ApiSubsidio();
        $this->epayco = $epayco;
    }

    /**
     * GET /mercurio/servicios/index
     * Vista principal del formulario de compra de servicios
     */
    public function index()
    {
        return view(
            'mercurio/ecommerce/index',
            [
                'EPAYCO_PUBLIC_KEY' => config('app.epayco.public_key'),
                'EPAYCO_TEST' => config('app.epayco.mode') === 'development' ? 'true' : 'false',
                'documento' => self::getActUser('documento'),
                'title' => 'Compra de Servicio',
            ]
        );
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
                        'message' => 'Debe ingresar una cedula valida'
                    ]
                );
            }

            $this->api->send([
                'servicio' => 'Movil',
                'metodo' => 'identifica-trabajador',
                'params' => ['cedtra' => $cedtra],
            ]);

            $resultado = $this->api->toArray();

            if (! ($resultado['success'] ?? false)) {
                return response()->json([
                    'success' => false,
                    'message' => $resultado['message'] ?? 'Trabajador no encontrado'
                ]);
            }

            $data = $resultado['data'] ?? [];

            if (isset($data['trabajador'])) {
                if (! isset($data['nucleo_familiar'])) {
                    $data['nucleo_familiar'] = [];
                }
            } else {
                $data = [
                    'trabajador' => $data,
                    'nucleo_familiar' => $resultado['nucleo_familiar'] ?? [],
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'Trabajador encontrado'
            ]);
        } catch (\Throwable $e) {
            $this->setLogger($e->getMessage());
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Error al buscar el trabajador',
                    'errors' => $e->getMessage()
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

            if (! ($resultado['success'] ?? false)) {
                return response()->json([
                    'success' => false,
                    'message' => $resultado['message'] ?? 'Error al cargar servicios'
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => $resultado['data'] ?? [],
                'message' => 'Proceso completado con éxito'
            ]);
        } catch (\Throwable $e) {
            $this->setLogger($e->getMessage());
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Error al cargar servicios: ' . $e->getMessage()
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
                    'message' => 'Debe seleccionar un servicio'
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

            if (! ($resultado['success'] ?? false)) {
                $msg = $resultado['message'] ?? 'Error al validar tarifa';
                return response()->json([
                    'success' => false,
                    'message' => $msg
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => $resultado['data'] ?? [],
                'message' => 'Tarifa obtenida'
            ]);
        } catch (\Throwable $e) {
            $this->setLogger($e->getMessage());
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Error al validar tarifa: ' . $e->getMessage()
                ]
            );
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

            if (empty(trim($ref_payco))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Referencia de pago no proporcionada'
                ]);
            }

            $resultado = $this->epayco->validarReferencia($ref_payco);

            if (! $resultado['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $resultado['errors'] ?? 'Error al validar referencia'
                ]);
            }

            $data = $resultado['data'];
            $msg = $data['aprobado'] ? 'Pago aprobado' : 'Pago no aprobado';

            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => $msg
            ]);
        } catch (\Throwable $e) {
            $this->setLogger($e->getMessage());
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Error al validar pago: ' . $e->getMessage()
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

            if (! ($resultado['success'] ?? false)) {
                $msg = $resultado['message'] ?? 'Error al guardar la venta';
                return response()->json([
                    'success' => false,
                    'message' => $msg
                ]);
            }

            $codbenLog = ! empty($codben) ? $codben : $cedtra;
            $this->setLogger("Venta Servicio - cedtra: $cedtra, codben: $codbenLog, codser: $codser, refpago: $refpago");

            return response()->json([
                'success' => true,
                'data' => $resultado['data'] ?? [],
                'message' => 'Venta guardada exitosamente'
            ]);
        } catch (\Throwable $e) {
            $this->setLogger($e->getMessage());
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Error al guardar la venta: ' . $e->getMessage()
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
                    'message' => 'Debe ingresar una cedula valida'
                ]);
            }

            $params = [
                'cedtra' => $cedtra,
                'limit' => 20,
            ];

            $this->api->send([
                'servicio' => 'Movil',
                'metodo' => 'mis-compras',
                'params' => $params,
            ]);

            $resultado = $this->api->toArray();

            if (! ($resultado['success'] ?? false)) {
                $msg = $resultado['message'] ?? 'Error al cargar compras';
                return response()->json([
                    'success' => false,
                    'message' => $msg
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => $resultado['data'] ?? [],
                'message' => ''
            ]);
        } catch (\Throwable $e) {
            $this->setLogger($e->getMessage());
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Error al cargar compras: ' . $e->getMessage()
                ]
            );
        }
    }
}
