<?php

namespace App\Http\Controllers\Mercurio;

use App\Http\Controllers\Adapter\ApplicationController;
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

    public function __construct()
    {
        $this->api = new ApiSubsidio();
    }

    /**
     * GET /mercurio/servicios/index
     * Vista principal del formulario de compra de servicios
     */
    public function index()
    {
        $EPAYCO_PUBLIC_KEY = '5700c4372a4369500c22efede64aa3f3';
        $EPAYCO_TEST = 'true';
        $documento = self::getActUser('documento');

        return view('mercurio/ecommerce/index', [
            'EPAYCO_PUBLIC_KEY' => $EPAYCO_PUBLIC_KEY,
            'EPAYCO_TEST' => $EPAYCO_TEST,
            'documento' => $documento,
            'title' => 'Compra de Servicio',
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
                return response()->json($this->errorFunc('Debe ingresar una cedula valida'));
            }

            $this->api->send([
                'servicio' => 'movil',
                'metodo' => 'identifica-trabajador',
                'params' => ['cedtra' => $cedtra],
            ]);

            $resultado = $this->api->toArray();

            if (! ($resultado['flag'] ?? true)) {
                $msg = $resultado['msg'] ?? 'Trabajador no encontrado';
                return response()->json($this->errorFunc($msg));
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

            return response()->json($this->successFunc('Trabajador encontrado', $data));
        } catch (\Throwable $e) {
            $this->setLogger($e->getMessage());
            return response()->json($this->errorFunc('Error al buscar el trabajador: ' . $e->getMessage()));
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
                'servicio' => 'movil',
                'metodo' => 'listar-servicios',
            ]);

            $resultado = $this->api->toArray();

            if (! ($resultado['flag'] ?? true)) {
                $msg = $resultado['msg'] ?? 'Error al cargar servicios';
                return response()->json($this->errorFunc($msg));
            }

            return response()->json($this->successFunc('', $resultado['data'] ?? []));
        } catch (\Throwable $e) {
            $this->setLogger($e->getMessage());
            return response()->json($this->errorFunc('Error al cargar servicios: ' . $e->getMessage()));
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
                return response()->json($this->errorFunc('Debe seleccionar un servicio'));
            }

            $params = [
                'cedtra' => $cedtra,
                'codser' => $codser,
                'numero' => $numero,
                'codben' => ! empty($codben) ? $codben : $cedtra,
            ];

            $this->api->send([
                'servicio' => 'movil',
                'metodo' => 'validar-tarifas',
                'params' => $params,
            ]);

            $resultado = $this->api->toArray();

            if (! ($resultado['flag'] ?? true)) {
                $msg = $resultado['msg'] ?? 'Error al validar tarifa';
                return response()->json($this->errorFunc($msg));
            }

            return response()->json($this->successFunc('Tarifa obtenida', $resultado['data'] ?? []));
        } catch (\Throwable $e) {
            $this->setLogger($e->getMessage());
            return response()->json($this->errorFunc('Error al validar tarifa: ' . $e->getMessage()));
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
                return response()->json($this->errorFunc('Referencia de pago no proporcionada'));
            }

            $url = 'https://secure.epayco.co/validation/v1/reference/' . urlencode($ref_payco);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Accept: application/json',
            ]);

            $result = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($curlError) {
                return response()->json($this->errorFunc('Error al consultar ePayco: ' . $curlError));
            }

            if ($httpCode != 200) {
                return response()->json($this->errorFunc('ePayco respondio con codigo HTTP: ' . $httpCode));
            }

            $epaycoData = json_decode($result, true);

            if (! $epaycoData || ! isset($epaycoData['data'])) {
                return response()->json($this->errorFunc('Respuesta invalida de ePayco'));
            }

            $txData = $epaycoData['data'];
            $codEstado = isset($txData['x_cod_transaction_state']) ? intval($txData['x_cod_transaction_state']) : 0;
            $respuesta = $txData['x_response'] ?? 'Sin respuesta';
            $motivo = $txData['x_response_reason_text'] ?? '';
            $monto = $txData['x_amount'] ?? '0';
            $refPaycoConfirmado = $txData['x_ref_payco'] ?? $ref_payco;

            $pagoAprobado = ($codEstado == 1);

            $data = [
                'aprobado' => $pagoAprobado,
                'cod_estado' => $codEstado,
                'respuesta' => $respuesta,
                'motivo' => $motivo,
                'monto' => $monto,
                'ref_payco' => $refPaycoConfirmado,
            ];

            $msg = $pagoAprobado ? 'Pago aprobado' : 'Pago no aprobado';

            return response()->json($this->successFunc($msg, $data));
        } catch (\Throwable $e) {
            $this->setLogger($e->getMessage());
            return response()->json($this->errorFunc('Error al validar pago: ' . $e->getMessage()));
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
                'servicio' => 'movil',
                'metodo' => 'guardar-venta',
                'params' => $params,
            ]);

            $resultado = $this->api->toArray();

            if (! ($resultado['flag'] ?? true)) {
                $msg = $resultado['msg'] ?? 'Error al guardar la venta';
                return response()->json($this->errorFunc($msg));
            }

            $codbenLog = ! empty($codben) ? $codben : $cedtra;
            $this->setLogger("Venta Servicio - cedtra: $cedtra, codben: $codbenLog, codser: $codser, refpago: $refpago");

            return response()->json($this->successFunc('Venta guardada exitosamente', $resultado['data'] ?? []));
        } catch (\Throwable $e) {
            $this->setLogger($e->getMessage());
            return response()->json($this->errorFunc('Error al guardar la venta: ' . $e->getMessage()));
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
                return response()->json($this->errorFunc('Debe ingresar una cedula valida'));
            }

            $params = [
                'cedtra' => $cedtra,
                'limit' => 20,
            ];

            $this->api->send([
                'servicio' => 'movil',
                'metodo' => 'mis-compras',
                'params' => $params,
            ]);

            $resultado = $this->api->toArray();

            if (! ($resultado['flag'] ?? true)) {
                $msg = $resultado['msg'] ?? 'Error al cargar compras';
                return response()->json($this->errorFunc($msg));
            }

            return response()->json($this->successFunc('', $resultado['data'] ?? []));
        } catch (\Throwable $e) {
            $this->setLogger($e->getMessage());
            return response()->json($this->errorFunc('Error al cargar compras: ' . $e->getMessage()));
        }
    }
}