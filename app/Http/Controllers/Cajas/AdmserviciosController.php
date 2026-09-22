<?php

namespace App\Http\Controllers\Cajas;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\PrecompraServicio;
use App\Services\Api\ApiEpayco;
use App\Services\Api\ApiSubsidio;
use App\Services\Ecommerce\EpaycoCuentaResolver;
use App\Services\Ecommerce\EstadoPrecompra;
use App\Services\Utils\GeneralService;
use App\Services\Utils\Paginate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Consulta administrativa de las ventas/precompras de servicios
 * realizadas por los usuarios del portal (tabla precompras_servicios).
 */
class AdmserviciosController extends ApplicationController
{
    protected $query = '1=1';

    protected $cantidad_pagina = 10;

    protected $user;

    protected $tipo;

    public function __construct()
    {
        $this->user = session()->has('user') ? session('user') : null;
        $this->tipo = session()->has('tipo') ? session('tipo') : null;
    }

    public function index()
    {
        $campo_field = [
            'documento' => 'Documento',
            'codser' => 'Servicio',
            'numero' => 'Apertura',
            'codben' => 'Beneficiario',
            'estado' => 'Estado (PE, PA, DE, RE)',
            'ref_payco' => 'Referencia ePayco',
            'valor' => 'Valor',
            'fecha_precompra' => 'Fecha precompra',
            'fecha_pago' => 'Fecha pago',
        ];

        return view('cajas.admservicios.index', [
            'title' => 'Ventas de Servicios',
            'campo_filtro' => $campo_field,
            'filtro_estados' => EstadoPrecompra::DESCRIPCIONES,
        ]);
    }

    public function aplicarFiltro(Request $request)
    {
        $consultasOldServices = new GeneralService;
        $this->query = $consultasOldServices->converQuery($request);

        return $this->buscar($request);
    }

    public function changeCantidadPagina(Request $request)
    {
        $numero = $request->input('numero');
        if ($numero != '' && is_numeric($numero)) {
            $this->cantidad_pagina = (int) $numero;
        }

        return $this->buscar($request);
    }

    public function buscar(Request $request)
    {
        $pagina = ($request->input('pagina') == '') ? 1 : $request->input('pagina');
        $numero = $request->input('numero');
        if ($numero != '' && is_numeric($numero)) {
            $this->cantidad_pagina = (int) $numero;
        }

        $paginate = Paginate::execute(
            $this->queryPrecompras($request)->get(),
            $pagina,
            $this->cantidad_pagina
        );

        $html = $this->showTabla($paginate);
        $consultasOldServices = new GeneralService;
        $html_paginate = $consultasOldServices->showPaginate($paginate, $this->cantidad_pagina);

        $response['consulta'] = $html;
        $response['paginate'] = $html_paginate;

        return $this->renderObject($response, false);
    }

    public function showTabla($paginate)
    {
        return view('cajas.admservicios._tabla', [
            'paginate' => $paginate,
        ])->render();
    }

    /**
     * POST /cajas/admservicios/detalle/{id}
     * HTML del detalle de precompra + historial epayco_transacciones + registro Subsidio.
     */
    public function detalle(int $id)
    {
        $precompra = PrecompraServicio::with([
            'transaccionesEpayco' => function ($q) {
                $q->orderBy('id');
            },
        ])->find($id);

        if (! $precompra) {
            return $this->renderObject([
                'success' => false,
                'msj' => 'La precompra no existe',
            ]);
        }

        $html = $this->renderDetalleHtml($precompra);

        return $this->renderObject([
            'success' => true,
            'html' => $html,
            'titulo' => 'Precompra #'.$precompra->id,
        ]);
    }

    /**
     * POST /cajas/admservicios/registrar-subsidio/{id}
     * 1) Valida pago en ePayco (Apify).
     * 2) Si está aprobado, llama Movil/guardar-venta.
     * 3) Devuelve HTML de detalle actualizado (mis-compras).
     */
    public function registrarSubsidio(int $id, ApiEpayco $epayco, EpaycoCuentaResolver $epaycoCuentaResolver)
    {
        $precompra = PrecompraServicio::find($id);
        if (! $precompra) {
            return $this->renderObject([
                'success' => false,
                'msj' => 'La precompra no existe',
            ]);
        }

        $refpago = trim((string) ($precompra->ref_payco ?? ''));
        if ($refpago === '') {
            return $this->renderObject([
                'success' => false,
                'msj' => 'La precompra no tiene referencia ePayco para validar.',
            ]);
        }

        $yaEnSubsidio = $this->consultarVentaSubsidioViaMisCompras($precompra);
        if (($yaEnSubsidio['registrada'] ?? false) === true) {
            return $this->renderObject([
                'success' => true,
                'msj' => 'La compra ya estaba registrada en Subsidio.',
                'registrada' => true,
                'pago' => null,
                'html' => $this->renderDetalleHtml($precompra->fresh([
                    'transaccionesEpayco' => fn ($q) => $q->orderBy('id'),
                ])),
            ]);
        }

        try {
            $apiEpayco = $this->apiEpaycoParaPrecompra($precompra, $epayco, $epaycoCuentaResolver);
            $pago = $apiEpayco->validarReferenciaApify($refpago);

            if (! ($pago['success'] ?? false)) {
                return $this->renderObject([
                    'success' => false,
                    'msj' => (string) ($pago['errors'] ?? 'No se pudo validar el pago en ePayco.'),
                    'pago' => null,
                    'registrada' => false,
                ]);
            }

            $dataPago = is_array($pago['data'] ?? null) ? $pago['data'] : [];
            $aprobado = (bool) ($dataPago['aprobado'] ?? false);
            $pagoResumen = [
                'aprobado' => $aprobado,
                'ref_payco' => $dataPago['ref_payco'] ?? $refpago,
                'cod_estado' => $dataPago['cod_estado'] ?? null,
                'respuesta' => $dataPago['respuesta'] ?? null,
                'motivo' => $dataPago['motivo'] ?? null,
                'monto' => $dataPago['monto'] ?? null,
                'transaction_id' => $dataPago['x_transaction_id'] ?? null,
                'approval_code' => $dataPago['x_approval_code'] ?? null,
                'origen_consulta' => $dataPago['origen_consulta'] ?? null,
            ];

            if (! $aprobado) {
                return $this->renderObject([
                    'success' => false,
                    'msj' => 'Pago consultado en ePayco: no aprobado. No se registra en Subsidio.',
                    'pago' => $pagoResumen,
                    'registrada' => false,
                ]);
            }

            $guardado = $this->guardarVentaSubsidio($precompra, $refpago);
            if (! ($guardado['ok'] ?? false)) {
                return $this->renderObject([
                    'success' => false,
                    'msj' => (string) ($guardado['msg'] ?? 'No se pudo guardar la venta en Subsidio.'),
                    'pago' => $pagoResumen,
                    'registrada' => false,
                    'subsidio' => $guardado['resultado'] ?? null,
                ]);
            }

            $precompra = $precompra->fresh([
                'transaccionesEpayco' => fn ($q) => $q->orderBy('id'),
            ]);

            return $this->renderObject([
                'success' => true,
                'msj' => 'Pago aprobado y venta registrada en Subsidio.',
                'pago' => $pagoResumen,
                'registrada' => true,
                'subsidio' => $guardado['resultado'] ?? null,
                'html' => $this->renderDetalleHtml($precompra),
                'titulo' => 'Precompra #'.$precompra->id,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Admservicios.registrarSubsidio: error', [
                'precompra_id' => $id,
                'ref_payco' => $refpago,
                'message' => $e->getMessage(),
            ]);

            return $this->renderObject([
                'success' => false,
                'msj' => 'Error al registrar en Subsidio: '.$e->getMessage(),
                'registrada' => false,
            ]);
        }
    }

    /**
     * HTML del detalle (misma vista que detalle/).
     */
    protected function renderDetalleHtml(PrecompraServicio $precompra): string
    {
        $ventaSubsidio = $this->consultarVentaSubsidioViaMisCompras($precompra);
        $puedeRegistrarSubsidio = ($ventaSubsidio['registrada'] ?? false) !== true
            && trim((string) ($precompra->ref_payco ?? '')) !== '';

        return view('cajas.admservicios._detalle', [
            'precompra' => $precompra,
            'ventaSubsidio' => $ventaSubsidio,
            'puedeRegistrarSubsidio' => $puedeRegistrarSubsidio,
        ])->render();
    }

    /**
     * Llama Movil/guardar-venta con el mismo contrato del webhook ePayco.
     *
     * @return array{ok: bool, msg: string, resultado: array<string, mixed>|null}
     */
    protected function guardarVentaSubsidio(PrecompraServicio $precompra, string $refpago): array
    {
        $items = $precompra->codbenList();
        $itemsPayload = array_map(static fn (string $codben) => ['codben' => $codben], $items);
        $codbenPrimero = $items[0] ?? ($precompra->codben ?: $precompra->documento);

        try {
            $api = new ApiSubsidio;
            $api->send([
                'servicio' => 'Movil',
                'metodo' => 'guardar-venta',
                'params' => [
                    'cedtra' => $precompra->documento,
                    'codser' => $precompra->codser,
                    'numero' => $precompra->numero,
                    'refpago' => $refpago,
                    'nota' => $precompra->nota ?? '',
                    'codben' => $codbenPrimero,
                    'items' => $itemsPayload,
                ],
            ]);

            $resultado = $api->toArray();
            if (! is_array($resultado)) {
                return [
                    'ok' => false,
                    'msg' => 'Respuesta inválida de Subsidio al guardar la venta.',
                    'resultado' => null,
                ];
            }

            $ok = (bool) ($resultado['flag'] ?? $resultado['success'] ?? false);
            if (! $ok) {
                $msg = (string) ($resultado['msg'] ?? $resultado['message'] ?? $resultado['error'] ?? 'guardar-venta rechazado por Subsidio.');

                Log::warning('Admservicios.guardarVentaSubsidio: no OK', [
                    'precompra_id' => $precompra->id,
                    'message' => $msg,
                ]);

                return [
                    'ok' => false,
                    'msg' => $msg,
                    'resultado' => $resultado,
                ];
            }

            return [
                'ok' => true,
                'msg' => (string) ($resultado['msg'] ?? 'Venta guardada en Subsidio'),
                'resultado' => $resultado,
            ];
        } catch (\Throwable $e) {
            Log::error('Admservicios.guardarVentaSubsidio: error', [
                'precompra_id' => $precompra->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'ok' => false,
                'msg' => 'Error access api / guardar-venta: '.$e->getMessage(),
                'resultado' => null,
            ];
        }
    }

    /**
     * Cliente ePayco con cuenta de la precompra (p_id_customer) si existe.
     */
    protected function apiEpaycoParaPrecompra(
        PrecompraServicio $precompra,
        ApiEpayco $epayco,
        EpaycoCuentaResolver $epaycoCuentaResolver
    ): ApiEpayco {
        if (! $precompra->p_id_customer) {
            return $epayco;
        }

        try {
            $cuenta = $epaycoCuentaResolver->findByPIdCustomer((string) $precompra->p_id_customer);

            return $epayco->withCuenta($cuenta);
        } catch (DebugException $e) {
            Log::warning('Admservicios: no se resolvió cuenta ePayco para validación', [
                'precompra_id' => $precompra->id,
                'p_id_customer' => $precompra->p_id_customer,
                'error' => $e->getMessage(),
            ]);

            return $epayco;
        }
    }

    /**
     * Consulta en API Subsidio (Movil/mis-compras) si la precompra quedó registrada.
     * Match: cedtra + codser + numero + al menos un codben de la precompra.
     *
     * @return array{consultado: bool, registrada: bool, msg: string, data: array<string, mixed>|null}
     */
    protected function consultarVentaSubsidioViaMisCompras(PrecompraServicio $precompra): array
    {
        $cedtra = trim((string) ($precompra->documento ?? ''));
        if ($cedtra === '') {
            return [
                'consultado' => false,
                'registrada' => false,
                'msg' => 'Sin documento del titular: no es posible consultar el registro en Subsidio.',
                'data' => null,
            ];
        }

        $codser = trim((string) ($precompra->codser ?? ''));
        $numero = trim((string) ($precompra->numero ?? ''));
        if ($codser === '' || $numero === '') {
            return [
                'consultado' => false,
                'registrada' => false,
                'msg' => 'Sin servicio/apertura en la precompra: no es posible cruzar con Subsidio.',
                'data' => null,
            ];
        }

        $codbens = $precompra->codbenList();

        try {
            $api = new ApiSubsidio;
            $api->send([
                'servicio' => 'Movil',
                'metodo' => 'mis-compras',
                'params' => [
                    'cedtra' => $cedtra,
                    'limit' => 100,
                ],
            ]);
            $resultado = $api->toArray();

            if (! is_array($resultado)) {
                return [
                    'consultado' => true,
                    'registrada' => false,
                    'msg' => 'Respuesta inválida de Subsidio al consultar mis-compras.',
                    'data' => null,
                ];
            }

            $flag = (bool) ($resultado['flag'] ?? $resultado['success'] ?? false);
            if (! $flag) {
                $msg = (string) ($resultado['msg'] ?? $resultado['message'] ?? $resultado['error'] ?? 'No se pudo consultar Subsidio.');

                return [
                    'consultado' => true,
                    'registrada' => false,
                    'msg' => $msg,
                    'data' => null,
                ];
            }

            $filas = $resultado['data'] ?? [];
            if (! is_array($filas) || $filas === []) {
                return [
                    'consultado' => true,
                    'registrada' => false,
                    'msg' => 'No se encontraron compras del titular en Subsidio.',
                    'data' => null,
                ];
            }

            $candidatas = [];
            foreach ($filas as $fila) {
                if (! is_array($fila)) {
                    continue;
                }
                if (trim((string) ($fila['codser'] ?? '')) !== $codser) {
                    continue;
                }
                if (trim((string) ($fila['numero'] ?? '')) !== $numero) {
                    continue;
                }
                $codbenFila = trim((string) ($fila['codben'] ?? ''));
                if ($codbens !== [] && ! in_array($codbenFila, $codbens, true)) {
                    continue;
                }
                $candidatas[] = $fila;
            }

            if ($candidatas === []) {
                return [
                    'consultado' => true,
                    'registrada' => false,
                    'msg' => 'No registrada en Subsidio (sin coincidencia de servicio/apertura/beneficiario).',
                    'data' => null,
                ];
            }

            $grupos = [];
            foreach ($candidatas as $fila) {
                $clave = trim((string) ($fila['marca'] ?? '')).'|'.trim((string) ($fila['documento'] ?? ''));
                $grupos[$clave][] = $fila;
            }

            $mejorClave = null;
            $mejorScore = -1;
            foreach ($grupos as $clave => $grupo) {
                $primera = $grupo[0];
                $score = (int) preg_replace('/\D/', '', (string) ($primera['fecha'] ?? '0')) * 1000000
                    + (int) preg_replace('/\D/', '', (string) ($primera['hora'] ?? '0'));
                if ($score >= $mejorScore) {
                    $mejorScore = $score;
                    $mejorClave = $clave;
                }
            }

            $grupoElegido = $grupos[$mejorClave] ?? $candidatas;
            $primera = $grupoElegido[0];

            $items = [];
            foreach ($grupoElegido as $idx => $fila) {
                $items[] = [
                    'sec' => $idx + 1,
                    'codben' => trim((string) ($fila['codben'] ?? '')),
                    'nombre_beneficiario' => trim((string) ($fila['nombre_beneficiario'] ?? '')),
                    'tipben' => trim((string) ($fila['tipben'] ?? '')),
                    'tipben_texto' => trim((string) ($fila['tipben_texto'] ?? '')),
                    'edad' => (int) ($fila['edad'] ?? 0),
                    'codcat' => trim((string) ($fila['codcat'] ?? '')),
                    'cantidad' => (int) ($fila['cantidad'] ?? 0),
                    'valser' => (float) ($fila['valser'] ?? 0),
                    'valsub' => (float) ($fila['valsub'] ?? 0),
                    'codser' => trim((string) ($fila['codser'] ?? '')),
                    'numero' => trim((string) ($fila['numero'] ?? '')),
                    'nombre_servicio' => trim((string) ($fila['nombre_servicio'] ?? '')),
                ];
            }

            $data = [
                'marca' => trim((string) ($primera['marca'] ?? '')),
                'documento' => trim((string) ($primera['documento'] ?? '')),
                'fecha' => trim((string) ($primera['fecha'] ?? '')),
                'hora' => trim((string) ($primera['hora'] ?? '')),
                'estado' => trim((string) ($primera['estado'] ?? '')),
                'estado_texto' => trim((string) ($primera['estado_texto'] ?? '')),
                'valpago' => (float) ($primera['valpago'] ?? 0),
                'valsub' => (float) ($primera['valsub'] ?? 0),
                'nota' => trim((string) ($primera['nota'] ?? '')),
                'cedtra_titular' => trim((string) ($primera['cedtra_titular'] ?? '')),
                'nombre_titular' => trim((string) ($primera['nombre_titular'] ?? '')),
                'codfor' => trim((string) ($primera['codfor'] ?? '')),
                'valor_forma_pago' => (float) ($primera['valor_forma_pago'] ?? 0),
                'refpago' => trim((string) ($primera['refpago'] ?? '')),
                'forma_pago_detalle' => trim((string) ($primera['forma_pago_detalle'] ?? '')),
                'items' => $items,
            ];

            return [
                'consultado' => true,
                'registrada' => true,
                'msg' => 'Venta encontrada en Subsidio (mis-compras)',
                'data' => $data,
            ];
        } catch (\Throwable $e) {
            Log::warning('Admservicios.detalle: error consulta venta Subsidio', [
                'cedtra' => $cedtra,
                'codser' => $codser,
                'numero' => $numero,
                'message' => $e->getMessage(),
            ]);

            return [
                'consultado' => true,
                'registrada' => false,
                'msg' => 'Error al consultar Subsidio: '.$e->getMessage(),
                'data' => null,
            ];
        }
    }

    public function reporte(Request $request, $format = 'csv')
    {
        try {
            $consultasOldServices = new GeneralService;
            $this->query = $consultasOldServices->converQuery($request);
            $precompras = $this->queryPrecompras($request)->get();

            $filename = 'ventas_servicios_'.date('Ymd_His').'.csv';

            return new StreamedResponse(function () use ($precompras) {
                $handle = fopen('php://output', 'w');
                // BOM para que Excel reconozca UTF-8
                fwrite($handle, "\xEF\xBB\xBF");
                fputcsv($handle, [
                    'Id',
                    'Documento',
                    'Beneficiario',
                    'Servicio',
                    'Apertura',
                    'Valor',
                    'Estado',
                    'Referencia ePayco',
                    'Transaction ID',
                    'Approval code',
                    'Fecha precompra',
                    'Fecha pago',
                    'Motivo desestimacion',
                ], ';');
                foreach ($precompras as $precompra) {
                    $tx = $precompra->ultimaTransaccionEpayco;
                    fputcsv($handle, [
                        $precompra->id,
                        $precompra->documento,
                        $precompra->codben,
                        $precompra->codser,
                        $precompra->numero,
                        $precompra->valor,
                        $precompra->estado_descripcion,
                        $precompra->ref_payco,
                        $tx?->transaction_id,
                        $tx?->approval_code,
                        optional($precompra->fecha_precompra)->format('Y-m-d H:i'),
                        optional($precompra->fecha_pago)->format('Y-m-d H:i'),
                        trim(($precompra->motivo_desestimacion ?? '').' '.($precompra->detalle_desestimacion ?? '')),
                    ], ';');
                }
                fclose($handle);
            }, 200, [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ]);
        } catch (DebugException $e) {
            $response = parent::errorFunc('No se pudo generar el reporte: '.$e->getMessage());

            return $this->renderObject($response, false);
        }
    }

    /**
     * Query base de precompras con el filtro dinamico y el chip de estado.
     */
    protected function queryPrecompras(Request $request)
    {
        $builder = PrecompraServicio::with('ultimaTransaccionEpayco')
            ->whereRaw("{$this->query}")
            ->orderByDesc('id');

        $estado = $request->input('estado');
        if ($estado != '' && array_key_exists($estado, EstadoPrecompra::DESCRIPCIONES)) {
            $builder->where('estado', $estado);
        }

        return $builder;
    }
}
