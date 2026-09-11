<?php

namespace App\Http\Controllers\Cajas;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\PrecompraServicio;
use App\Services\Ecommerce\EstadoPrecompra;
use App\Services\Utils\GeneralService;
use App\Services\Utils\Paginate;
use Illuminate\Http\Request;
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
     * HTML del detalle de precompra + historial epayco_transacciones.
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

        $html = view('cajas.admservicios._detalle', [
            'precompra' => $precompra,
        ])->render();

        return $this->renderObject([
            'success' => true,
            'html' => $html,
            'titulo' => 'Precompra #'.$precompra->id,
        ]);
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
