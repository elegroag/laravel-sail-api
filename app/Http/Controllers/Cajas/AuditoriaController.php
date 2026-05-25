<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use App\Models\Gener02;
use App\Models\Mercurio09;
use App\Services\ReportGenerator\Products\OptimizedXlsxProduct;
use App\Services\Utils\CalculatorDias;
use App\Services\Utils\GeneralService;
use Illuminate\Http\Request;

class AuditoriaController extends ApplicationController
{
    protected $db;

    protected $user;

    protected $tipfun;

    public function __construct()
    {
        $this->db = DbBase::rawConnect();
        $this->user = session('user');
        $this->tipfun = session('tipfun');
    }

    public function index()
    {
        return view('cajas.auditoria.index', [
            'title' => 'Consulta Historica',
            'mercurio09' => Mercurio09::all(),
        ]);
    }

    public function consultaAuditoria(Request $request)
    {
        $tipopc = $request->input('tipopc');
        $fecini = $request->input('fecini');
        $fecfin = $request->input('fecfin');

        $condi = "mercurio10.fecsis>='{$fecini}' and mercurio10.fecsis<='{$fecfin}'";
        $consultasOldServices = new GeneralService;
        $mercurio = $consultasOldServices->consultaTipopc($tipopc, 'all', '', '', $condi);

        $hasExtra = in_array($tipopc, ['8', '5']);
        $result = [];

        foreach ($mercurio['datos'] ?? [] as $mmercurio) {
            $dias_vencidos = CalculatorDias::calcular($tipopc, $mmercurio->getId());
            $result[] = [
                'documento' => $this->getDocumento($mmercurio, $tipopc),
                'nombre' => $this->getNombre($mmercurio, $tipopc),
                'responsable' => $this->getResponsable($mmercurio),
                'fecha' => $mmercurio->getFecest(),
                'dias_vencidos' => $dias_vencidos,
                'extra' => $hasExtra ? $this->getExtra($mmercurio, $tipopc) : null,
                'estado' => $mmercurio->getEstadoDetalle(),
                'estado_tipo' => $mmercurio->getEstado(),
            ];
        }

        return response()->json([
            'data' => $result,
            'hasExtra' => $hasExtra,
        ]);
    }

    private function getDocumento($mmercurio, string $tipopc): string
    {
        return match ($tipopc) {
            '1', '9', '10', '11', '12' => $mmercurio->getCedtra(),
            '2' => $mmercurio->getNit(),
            '3' => $mmercurio->getCedcon(),
            default => $mmercurio->getDocumento(),
        };
    }

    private function getNombre($mmercurio, string $tipopc): string
    {
        return match ($tipopc) {
            '1', '3', '8', '9', '10' => $mmercurio->getNombre(),
            '2' => $mmercurio->getRazsoc(),
            '5' => $mmercurio->getDocumentoDetalle(),
            '7' => $mmercurio->getNomtra(),
            default => $mmercurio->getNombre(),
        };
    }

    private function getResponsable($mmercurio): string
    {
        $gener02 = Gener02::where('usuario', $mmercurio->getUsuario())->first();

        return $gener02 ? $gener02->getNombre() : '';
    }

    private function getExtra($mmercurio, string $tipopc): string
    {
        if ($tipopc == '5') {
            return $mmercurio->getCampoDetalle().' - '.$mmercurio->getAntval().' - '.$mmercurio->getValor();
        }

        return $mmercurio->getNomcer();
    }

    public function reporteAuditoria(Request $request)
    {
        $tipopc = $request->input('tipopc');
        $fecini = $request->input('fecini');
        $fecfin = $request->input('fecfin');

        $condi = "mercurio10.fecsis>='{$fecini}' and mercurio10.fecsis<='{$fecfin}'";
        $consultasOldServices = new GeneralService;
        $mercurio = $consultasOldServices->consultaTipopc($tipopc, 'all', '', '', $condi);

        $hasExtra = in_array($tipopc, ['8', '5']);
        $headers = ['Documento', 'Nombre', 'Responsable', 'Fecha', 'Dias'];
        if ($hasExtra) {
            $headers[] = 'Extra';
        }
        $headers[] = 'Estado';

        $rows = [];
        foreach ($mercurio['datos'] ?? [] as $mmercurio) {
            $dias_vencidos = CalculatorDias::calcular($tipopc, $mmercurio->getId());
            $fila = [
                $this->getDocumento($mmercurio, $tipopc),
                $this->getNombre($mmercurio, $tipopc),
                $this->getResponsable($mmercurio),
                $mmercurio->getFecest(),
                $dias_vencidos,
            ];
            if ($hasExtra) {
                $fila[] = $this->getExtra($mmercurio, $tipopc);
            }
            $fila[] = $mmercurio->getEstadoDetalle();
            $rows[] = $fila;
        }

        $fecha = new \DateTime;
        $filename = 'reporte_auditoria_'.$fecha->format('Ymd').'.xlsx';

        return OptimizedXlsxProduct::streamFromArray($headers, $rows, $filename);
    }

    public function info(Request $request, $id)
    {
        $this->setResponse('ajax');
        $tipopc = $request->input('tipopc');
        $id = $request->input('id');
        $response = $this->consultaTipopc($tipopc, 'info', $id);

        return $this->renderObject($response);
    }

    public function consultaTipopc($tipopc, $tipo_consulta, $numero = '', $usuario = '', $condi = '', $page = 1)
    {
        $condi_extra = '';
        if ($condi != '') {
            $condi_extra = " $condi";
        }
        $params = [
            'tipopc' => $tipopc,
            'tipo_consulta' => $tipo_consulta,
            'numero' => $numero,
            'usuario' => $usuario,
            'condi' => $condi_extra,
        ];

        try {
            $result = GeneralService::consultaTipopc($params);
            $this->setResponse('ajax');

            return $this->renderObject($result);
        } catch (\Exception $e) {
            $this->set_flashdata('error', ['msj' => $e->getMessage()]);

            return redirect()->route('auditoria.index');
        }
    }
}
