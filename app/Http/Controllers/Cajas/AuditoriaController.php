<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Gener02;
use App\Models\Mercurio09;
use App\Services\ReportGenerator\Products\OptimizedXlsxProduct;
use App\Services\Utils\CalculatorDias;
use App\Services\Utils\GeneralService;
use App\Services\Utils\RegistroSeguimiento;
use App\Support\AuditoriaSolicitudFieldsBuilder;
use App\Support\AuditoriaSolicitudResolver;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class AuditoriaController extends ApplicationController
{

    protected $user;

    protected $tipfun;

    public function __construct()
    {
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
        $mercurio = $consultasOldServices->consultaTipopc($tipopc, 'auditoria', '', '', $condi);

        $hasExtra = in_array($tipopc, ['8', '5']);
        $result = [];
        $datos = $mercurio['datos'] ?? [];

        if ($datos instanceof Paginator) {
            $datos = $datos->items();
        } elseif ($datos instanceof Collection) {
            $datos = $datos->all();
        }

        foreach ($datos as $mmercurio) {
            $dias_vencidos = CalculatorDias::calcular($tipopc, $mmercurio->getId());
            $result[] = [
                'id' => $mmercurio->getId(),
                'documento' => $this->getDocumento($mmercurio, $tipopc),
                'nombre' => $this->getNombre($mmercurio, $tipopc),
                'responsable' => $this->getResponsable($mmercurio),
                'fecha' => Carbon::parse($mmercurio->getFecest())->format('Y-m-d'),
                'fecsol' => $this->formatFecha($mmercurio, 'fecsol'),
                'fecapr' => $mmercurio->getEstado() === 'A' ? $this->formatFecha($mmercurio, 'fecapr') ?? Carbon::parse($mmercurio->getFecest())->format('Y-m-d') : '',
                'radicado' => $mmercurio->ruuid ?? null,
                'dias_vencidos' => $dias_vencidos,
                'extra' => $hasExtra ? $this->getExtra($mmercurio, $tipopc) : null,
                'estado' => $mmercurio->getEstadoDetalle(),
                'estado_tipo' => $mmercurio->getEstado(),
            ];
        }

        $columns = ['Documento', 'Nombre', 'Responsable', 'Fecha', 'Fecsol', 'Fecapr', 'Radicado', 'Días'];
        if ($hasExtra) {
            $columns[] = 'Extra';
        }
        $columns[] = 'Estado';
        $columns[] = 'Acciones';

        return response()->json([
            'data' => $result,
            'hasExtra' => $hasExtra,
            'total' => count($result),
            'columns' => $columns,
        ]);
    }

    private function getDocumento($mmercurio, string $tipopc): string
    {
        return match ($tipopc) {
            '1', '9', '10', '11', '12', '13' => $this->callGetter($mmercurio, 'getCedtra'),
            '2' => $this->callGetter($mmercurio, 'getNit'),
            '3' => $this->callGetter($mmercurio, 'getCedcon'),
            '4' => $this->callGetter($mmercurio, 'getNumdoc'),
            default => $this->callGetter($mmercurio, 'getDocumento'),
        };
    }

    private function getNombre($mmercurio, string $tipopc): string
    {
        return match ($tipopc) {
            '2' => $this->callGetter($mmercurio, 'getRazsoc'),
            '5' => $this->callGetter($mmercurio, 'getDocumentoDetalle'),
            '7' => $this->callGetter($mmercurio, 'getNomtra'),
            '8' => $this->callGetter($mmercurio, 'getNombre'),
            default => $this->resolveNombrePersona($mmercurio),
        };
    }

    private function resolveNombrePersona($mmercurio): string
    {
        if (method_exists($mmercurio, 'getNombre')) {
            $nombre = trim((string) $mmercurio->getNombre());
            if ($nombre !== '') {
                return $nombre;
            }
        }

        if (method_exists($mmercurio, 'getNombreCompleto')) {
            $nombre = trim((string) $mmercurio->getNombreCompleto());
            if ($nombre !== '') {
                return $nombre;
            }
        }

        return trim(implode(' ', array_filter([
            $this->callGetter($mmercurio, 'getPrinom'),
            $this->callGetter($mmercurio, 'getSegnom'),
            $this->callGetter($mmercurio, 'getPriape'),
            $this->callGetter($mmercurio, 'getSegape'),
        ], fn ($part) => $part !== '')));
    }

    private function callGetter($mmercurio, string $getter): string
    {
        if (! method_exists($mmercurio, $getter)) {
            return '';
        }

        $value = $mmercurio->{$getter}();

        return trim((string) ($value ?? ''));
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

    private function formatFecha($mmercurio, string $field): ?string
    {
        $getter = 'get'.ucfirst($field);
        $value = method_exists($mmercurio, $getter) ? $mmercurio->$getter() : ($mmercurio->{$field} ?? null);

        if (empty($value)) {
            return null;
        }

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    public function reporteAuditoria(Request $request)
    {
        $headers = [];
        $rows = [];
        $this->buildReporte($request, $headers, $rows);

        $fecha = new \DateTime;
        $filename = 'reporte_auditoria_'.$fecha->format('Ymd').'.xlsx';

        return OptimizedXlsxProduct::streamFromArray($headers, $rows, $filename);
    }

    public function exportarExcel(Request $request)
    {
        $headers = [];
        $rows = [];
        $this->buildReporte($request, $headers, $rows);

        $fecha = new \DateTime;
        $filename = 'auditoria_export_'.$fecha->format('Ymd_His').'.xlsx';

        return OptimizedXlsxProduct::streamFromArray($headers, $rows, $filename);
    }

    private function buildReporte(Request $request, array &$headers, array &$rows): void
    {
        $tipopc = $request->input('tipopc');
        $fecini = $request->input('fecini');
        $fecfin = $request->input('fecfin');

        $condi = "mercurio10.fecsis>='{$fecini}' and mercurio10.fecsis<='{$fecfin}'";
        $consultasOldServices = new GeneralService;
        $mercurio = $consultasOldServices->consultaTipopc($tipopc, 'auditoria', '', '', $condi);

        $hasExtra = in_array($tipopc, ['8', '5']);
        $headers = ['Documento', 'Nombre', 'Responsable', 'Fecha', 'Fecsol', 'Fecapr', 'Radicado', 'Dias'];
        if ($hasExtra) {
            $headers[] = 'Extra';
        }
        $headers[] = 'Estado';

        $rows = [];
        $datos = $mercurio['datos'] ?? [];

        if ($datos instanceof Paginator) {
            $datos = $datos->items();
        } elseif ($datos instanceof Collection) {
            $datos = $datos->all();
        }

        foreach ($datos as $mmercurio) {
            $dias_vencidos = CalculatorDias::calcular($tipopc, $mmercurio->getId());
            $fila = [
                $this->getDocumento($mmercurio, $tipopc),
                $this->getNombre($mmercurio, $tipopc),
                $this->getResponsable($mmercurio),
                $mmercurio->getFecest(),
                $this->formatFecha($mmercurio, 'fecsol'),
                $this->formatFecha($mmercurio, 'fecapr'),
                $mmercurio->ruuid ?? '',
                $dias_vencidos,
            ];
            if ($hasExtra) {
                $fila[] = $this->getExtra($mmercurio, $tipopc);
            }
            $fila[] = $mmercurio->getEstadoDetalle();
            $rows[] = $fila;
        }
    }

    public function info(Request $request, $id)
    {
        $this->setResponse('ajax');
        $tipopc = $request->input('tipopc');
        $id = $request->input('id');
        $response = $this->consultaTipopc($tipopc, 'info', $id);

        return $this->renderObject($response);
    }

    public function infor(Request $request)
    {
        try {
            $validated = $request->validate([
                'tipopc' => 'required|string',
                'id' => 'required|integer',
            ]);

            $tipopc = $validated['tipopc'];
            $id = (int) $validated['id'];

            $solicitud = AuditoriaSolicitudResolver::resolve($tipopc, $id);
            if ($solicitud === null) {
                return response()->json([
                    'success' => false,
                    'msj' => 'Solicitud no encontrada',
                ]);
            }

            $hasExtra = in_array($tipopc, ['5', '8'], true);
            $tipoDetalle = Mercurio09::where('tipopc', $tipopc)->value('detalle') ?? $tipopc;

            $fields = [
                'Tipo de opción' => $tipoDetalle,
                'ID' => $solicitud->getId(),
                'Documento' => $this->getDocumento($solicitud, $tipopc),
                'Nombre' => $this->getNombre($solicitud, $tipopc),
                'Responsable' => $this->getResponsable($solicitud),
                'Estado' => method_exists($solicitud, 'getEstadoDetalle') ? $solicitud->getEstadoDetalle() : ($solicitud->estado ?? ''),
                'Fecha solicitud' => $this->formatFecha($solicitud, 'fecsol'),
                'Fecha estado' => $this->formatFecha($solicitud, 'fecest'),
                'Fecha aprobación' => $this->formatFecha($solicitud, 'fecapr'),
                'Radicado' => $solicitud->ruuid ?? '',
                'Días vencidos' => CalculatorDias::calcular($tipopc, $id),
            ];

            if ($hasExtra) {
                $fields['Extra'] = $this->getExtra($solicitud, $tipopc);
            }

            $seguimientoHtml = (new RegistroSeguimiento)->consultaSeguimiento($tipopc, $solicitud);
            $extraFields = (new AuditoriaSolicitudFieldsBuilder)->build($tipopc, $solicitud);

            $html = view('cajas.auditoria._detalle', [
                'fields' => $fields,
                'extraFields' => $extraFields,
                'seguimientoHtml' => $seguimientoHtml,
            ])->render();

            return response()->json([
                'success' => true,
                'html' => $html,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'msj' => $e->getMessage(),
            ]);
        }
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
