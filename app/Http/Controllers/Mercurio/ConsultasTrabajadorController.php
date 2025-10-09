<?php

namespace App\Http\Controllers\Mercurio;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Library\Collections\ParamsBeneficiario;
use App\Library\Collections\ParamsConyuge;
use App\Library\Collections\ParamsTrabajador;
use App\Models\Adapter\DbBase;
use App\Models\Mercurio28;
use App\Models\Mercurio31;
use App\Models\Mercurio32;
use App\Models\Mercurio33;
use App\Models\Mercurio34;
use App\Models\Mercurio45;
use App\Services\Utils\Comman;
use App\Services\Utils\Logger;
use Illuminate\Http\Request;

class ConsultasTrabajadorController extends ApplicationController
{
    protected $db;

    protected $user;

    protected $tipo;

    public function __construct()
    {
        $this->db = DbBase::rawConnect();
        $this->user = session()->has('user') ? session('user') : null;
        $this->tipo = session()->has('tipo') ? session('tipo') : null;
    }

    public function indexAction()
    {
        return view('mercurio/subsidio/index', [
            'title' => 'Subsidio',
        ]);
    }

    public function historialAction()
    {
        $tipo = session()->get('tipo');
        $documento = $this->user['documento'];
        $coddoc = $this->user['coddoc'];

        $mercurio32 = Mercurio32::where([
            'tipo' => $tipo,
            'coddoc' => $coddoc,
            'documento' => $documento,
        ])
            ->orderBy('id', 'desc')
            ->first();

        $mercurio33 = Mercurio33::where([
            'tipo' => $tipo,
            'coddoc' => $coddoc,
            'documento' => $documento,
        ])
            ->orderBy('id', 'desc')
            ->first();
        $mercurio34 = Mercurio34::where([
            'tipo' => $tipo,
            'coddoc' => $coddoc,
            'documento' => $documento,
        ])
            ->orderBy('id', 'desc')
            ->first();

        $mercurio45 = Mercurio45::where([
            'tipo' => $tipo,
            'coddoc' => $coddoc,
            'documento' => $documento,
        ])
            ->orderBy('id', 'desc')
            ->first();

        $actualizacion_basico = "<table class='table table-hover align-items-center table-bordered'>";
        $actualizacion_basico .= '<thead>';
        $actualizacion_basico .= '<tr>';
        $actualizacion_basico .= "<th scope='col'>Campo</th>";
        $actualizacion_basico .= "<th scope='col'>Valor Anterior </th>";
        $actualizacion_basico .= "<th scope='col'>Valor Nuevo</th>";
        $actualizacion_basico .= "<th scope='col'>Estado</th>";
        $actualizacion_basico .= "<th scope='col'>Fecha Estado</th>";
        $actualizacion_basico .= "<th scope='col'>Motivo</th>";
        $actualizacion_basico .= '</tr>';
        $actualizacion_basico .= '</thead>';
        $actualizacion_basico .= "<tbody class='list'>";
        if ($mercurio33->count() == 0) {
            $actualizacion_basico .= "<tr align='center'>";
            $actualizacion_basico .= '<td colspan=6><p>No hay datos para mostrar</p></td>';
            $actualizacion_basico .= '<tr>';
            $actualizacion_basico .= '</tr>';
        } else {
            foreach ($mercurio33 as $mmercurio33) {
                $mmercurio28 = Mercurio28::where('campo', $mmercurio33->getCampo())->first();

                if (! $mmercurio28) {
                    continue;
                }
                $actualizacion_basico .= '<tr>';
                $actualizacion_basico .= "<td>{$mmercurio28->getDetalle()}</td>";
                $actualizacion_basico .= "<td>{$mmercurio33->getAntval()}</td>";
                $actualizacion_basico .= "<td>{$mmercurio33->getValor()}</td>";
                $actualizacion_basico .= "<td>{$mmercurio33->getEstadoDetalle()}</td>";
                $actualizacion_basico .= "<td>{$mmercurio33->getFecest()}</td>";
                $actualizacion_basico .= "<td>{$mmercurio33->getMotivo()}</td>";
                $actualizacion_basico .= '</tr>';
            }
        }

        $actualizacion_basico .= '</tbody>';
        $actualizacion_basico .= '</table>';

        $html_afiliacion_conyuge = "<table class='table table-hover align-items-center table-bordered'>";
        $html_afiliacion_conyuge .= '<thead>';
        $html_afiliacion_conyuge .= '<tr>';
        $html_afiliacion_conyuge .= "<th scope='col'>Cedula</th>";
        $html_afiliacion_conyuge .= "<th scope='col'>Nombre </th>";
        $html_afiliacion_conyuge .= "<th scope='col'>Estado</th>";
        $html_afiliacion_conyuge .= "<th scope='col'>Fecha Estado</th>";
        $html_afiliacion_conyuge .= "<th scope='col'>Motivo</th>";
        $html_afiliacion_conyuge .= '</tr>';
        $html_afiliacion_conyuge .= '</thead>';
        $html_afiliacion_conyuge .= "<tbody class='list'>";
        if (count($mercurio32) == 0) {
            $html_afiliacion_conyuge .= "<tr align='center'>";
            $html_afiliacion_conyuge .= '<td colspan=5><p>No hay datos para mostrar</p></td>';
            $html_afiliacion_conyuge .= '<tr>';
            $html_afiliacion_conyuge .= '</tr>';
        }
        foreach ($mercurio32 as $mmercurio32) {
            $html_afiliacion_conyuge .= '<tr>';
            $html_afiliacion_conyuge .= "<td>{$mmercurio32->getCedcon()}</td>";
            $html_afiliacion_conyuge .= "<td>{$mmercurio32->getPriape()} {$mmercurio32->getPrinom()}</td>";
            $html_afiliacion_conyuge .= "<td>{$mmercurio32->getEstadoDetalle()}</td>";
            $html_afiliacion_conyuge .= "<td>{$mmercurio32->getFecest()}</td>";
            $html_afiliacion_conyuge .= "<td>{$mmercurio32->getMotivo()}</td>";
            $html_afiliacion_conyuge .= '</tr>';
        }
        $html_afiliacion_conyuge .= '</tbody>';
        $html_afiliacion_conyuge .= '</table>';

        $html_afiliacion_beneficiario = "<table class='table table-hover align-items-center table-bordered'>";
        $html_afiliacion_beneficiario .= '<thead>';
        $html_afiliacion_beneficiario .= '<tr>';
        $html_afiliacion_beneficiario .= "<th scope='col'>Documento</th>";
        $html_afiliacion_beneficiario .= "<th scope='col'>Nombre </th>";
        $html_afiliacion_beneficiario .= "<th scope='col'>Estado</th>";
        $html_afiliacion_beneficiario .= "<th scope='col'>Fecha Estado</th>";
        $html_afiliacion_beneficiario .= "<th scope='col'>Motivo</th>";
        $html_afiliacion_beneficiario .= '</tr>';
        $html_afiliacion_beneficiario .= '</thead>';
        $html_afiliacion_beneficiario .= "<tbody class='list'>";
        if (count($mercurio34) == 0) {
            $html_afiliacion_beneficiario .= "<tr align='center'>";
            $html_afiliacion_beneficiario .= '<td colspan=5><p>No hay datos para mostrar</p></td>';
            $html_afiliacion_beneficiario .= '<tr>';
            $html_afiliacion_beneficiario .= '</tr>';
        }
        foreach ($mercurio34 as $mmercurio34) {
            $html_afiliacion_beneficiario .= '<tr>';
            $html_afiliacion_beneficiario .= "<td>{$mmercurio34->getNumdoc()}</td>";
            $html_afiliacion_beneficiario .= "<td>{$mmercurio34->getPriape()} {$mmercurio34->getPrinom()}</td>";
            $html_afiliacion_beneficiario .= "<td>{$mmercurio34->getEstadoDetalle()}</td>";
            $html_afiliacion_beneficiario .= "<td>{$mmercurio34->getFecest()}</td>";
            $html_afiliacion_beneficiario .= "<td>{$mmercurio34->getMotivo()}</td>";
            $html_afiliacion_beneficiario .= '</tr>';
        }
        $html_afiliacion_beneficiario .= '</tbody>';
        $html_afiliacion_beneficiario .= '</table>';

        $html_certificados = "<table class='table table-hover align-items-center table-bordered'>";
        $html_certificados .= '<thead>';
        $html_certificados .= '<tr>';
        $html_certificados .= "<th scope='col'>Beneficiario</th>";
        $html_certificados .= "<th scope='col'>Certificado</th>";
        $html_certificados .= "<th scope='col'>Estado</th>";
        $html_certificados .= "<th scope='col'>Fecha Estado</th>";
        $html_certificados .= "<th scope='col'>Motivo</th>";
        $html_certificados .= '</tr>';
        $html_certificados .= '</thead>';
        $html_certificados .= "<tbody class='list'>";
        if (count($mercurio45) == 0) {
            $html_certificados .= "<tr align='center'>";
            $html_certificados .= '<td colspan=5><p>No hay datos para mostrar</p></td>';
            $html_certificados .= '<tr>';
            $html_certificados .= '</tr>';
        }
        foreach ($mercurio45 as $mmercurio45) {
            $html_certificados .= '<tr>';
            $html_certificados .= "<td>{$mmercurio45->getNombre()}</td>";
            $html_certificados .= "<td>{$mmercurio45->getNomcer()}</td>";
            $html_certificados .= "<td>{$mmercurio45->getEstadoDetalle()}</td>";
            $html_certificados .= "<td>{$mmercurio45->getFecest()}</td>";
            $html_certificados .= "<td>{$mmercurio45->getMotivo()}</td>";
            $html_certificados .= '</tr>';
        }
        $html_certificados .= '</tbody>';
        $html_certificados .= '</table>';

        return view('mercurio/subsidio/historial', [
            'title' => 'Historial',
            'actualizacion_basico' => $actualizacion_basico,
            'html_afiliacion_beneficiario' => $html_afiliacion_beneficiario,
            'html_afiliacion_conyuge' => $html_afiliacion_conyuge,
            'html_certificados' => $html_certificados,
        ]);
    }

    /**
     * consulta_nucleoAction function
     *
     * @param  string  $cedtra
     * @return void
     */
    public function consultaNucleoViewAction($cedtra = '')
    {
        return view('mercurio/subsidio/consulta_nucleo', [
            'title' => 'Consulta nucleo familiar',
            'cedtra' => $cedtra,
        ]);
    }

    public function consultaNucleoAction()
    {
        $this->setResponse('ajax');
        $cedtra = parent::getActUser('documento');
        $ps = Comman::Api();
        $ps->runCli(
            [
                'servicio' => 'PoblacionAfiliada',
                'metodo' => 'nucleo_familiar_trabajador',
                'params' => [
                    'cedtra' => $cedtra,
                ],
            ]
        );

        $out = $ps->toArray();
        if (! $out['success']) {
            $dataTrabajador = [];
            $dataConyuges = [];
            $dataBeneficiarios = [];
        } else {
            $dataTrabajador = $out['data']['trabajador'];
            $dataConyuges = $out['data']['conyuges'];
            $dataBeneficiarios = $out['data']['beneficiarios'];
        }

        $ps = Comman::Api();
        $ps->runCli(
            [
                'servicio' => 'ComfacaAfilia',
                'metodo' => 'parametros_trabajadores',
            ]
        );
        $paramsTrabajador = new ParamsTrabajador;
        $paramsTrabajador->setDatosCaptura($ps->toArray());

        $ps = Comman::Api();
        $ps->runCli(
            [
                'servicio' => 'ComfacaAfilia',
                'metodo' => 'parametros_conyuges',
            ]
        );
        $paramsConyuge = new ParamsConyuge;
        $paramsConyuge->setDatosCaptura($ps->toArray());

        $ps = Comman::Api();
        $ps->runCli(
            [
                'servicio' => 'ComfacaAfilia',
                'metodo' => 'parametros_beneficiarios',
            ]
        );
        $paramsBeneficiario = new ParamsBeneficiario;
        $paramsBeneficiario->setDatosCaptura($ps->toArray());

        $this->renderObject(
            [
                'success' => true,
                'data' => [
                    'trabajador' => $dataTrabajador,
                    'conyuges' => $dataConyuges,
                    'beneficiarios' => $dataBeneficiarios,
                    'params' => [
                        '_coddoc' => ParamsTrabajador::getTiposDocumentos(),
                        '_sexo' => ParamsTrabajador::getSexos(),
                        '_estciv' => ParamsTrabajador::getEstadoCivil(),
                        '_cabhog' => ParamsTrabajador::getCabezaHogar(),
                        '_codciu' => ParamsTrabajador::getCiudades(),
                        '_codzon' => ParamsTrabajador::getZonas(),
                        '_captra' => ParamsTrabajador::getCapacidadTrabajar(),
                        '_tipdis' => ParamsTrabajador::getTipoDiscapacidad(),
                        '_nivedu' => ParamsTrabajador::getNivelEducativo(),
                        '_tippag' => ParamsTrabajador::getTipoPago(),
                        '_rural' => ParamsTrabajador::getRural(),
                        '_tipcon' => ParamsTrabajador::getTipoContrato(),
                        '_trasin' => ParamsTrabajador::getSindicalizado(),
                        '_vivienda' => ParamsTrabajador::getVivienda(),
                        '_tipafi' => ParamsTrabajador::getTipoAfiliado(),
                        '_estado' => (new Mercurio31)->getEstados(),
                        '_comper' => ParamsConyuge::getCompaneroPermanente(),
                        '_parent' => ParamsBeneficiario::getParentesco(),
                        '_huerfano' => ParamsBeneficiario::getHuerfano(),
                        '_tiphij' => ParamsBeneficiario::getTipoHijo(),
                        '_calendario' => ParamsBeneficiario::getCalendario(),
                        '_huerfano' => ParamsBeneficiario::getHuerfano(),
                        '_tiphij' => ParamsBeneficiario::getTipoHijo(),
                        '_calendario' => ParamsBeneficiario::getCalendario(),
                        '_codcat' => (new Mercurio31)->getCategoria(),
                    ],
                ],
            ]
        );
    }

    public function consultaGiroViewAction()
    {
        return view('mercurio/subsidio/consulta_giro', [
            'title' => 'Consulta Giro',
        ]);
    }

    public function consultaGiroAction(Request $request)
    {
        $this->setResponse('ajax');
        try {

            $perini = $request->input('perini');
            $perfin = $request->input('perfin');
            $params['cedtra'] = parent::getActUser('documento');
            $params['perini'] = $perini;
            $params['perfin'] = $perfin;

            $ps = Comman::Api();
            $ps->runCli(
                [
                    'servicio' => 'CuotaMonetaria',
                    'metodo' => 'cuotas_by_trabajador',
                    'params' => [
                        'post' => $params,
                    ],
                ]
            );

            $out = $ps->toArray();
            if (! $out['success']) {
                $response = [
                    'success' => false,
                    'message' => $out['message'],
                ];
            } else {
                $response = [
                    'success' => true,
                    'data' => $out['data'],
                ];
            }
        } catch (DebugException $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }

        return $this->renderObject($response);
    }

    public function consultaNoGiroViewAction()
    {
        return view('mercurio/subsidio/consulta_no_giro', [
            'title' => 'Consulta No Giro',
        ]);
    }

    public function consultaNoGiroAction(Request $request)
    {
        $this->setResponse('ajax');
        try {
            $perini = $request->input('perini');
            $perfin = $request->input('perfin');
            $params['cedtra'] = parent::getActUser('documento');
            $params['perini'] = $perini;
            $params['perfin'] = $perfin;

            $ps = Comman::Api();
            $ps->runCli(
                [
                    'servicio' => 'CuotaMonetaria',
                    'metodo' => 'nogiro_by_trabajador',
                    'params' => [
                        'post' => $params,
                    ],
                ]
            );

            $out = $ps->toArray();
            if (! $out['success']) {
                $response = [
                    'success' => false,
                    'message' => $out['message'],
                ];
            } else {
                $response = [
                    'success' => true,
                    'data' => $out['data'],
                ];
            }
        } catch (DebugException $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }

        return $this->renderObject($response);
    }

    public function consultaPlanillaTrabajadorViewAction()
    {
        return view('mercurio/subsidio/consulta_planilla_trabajador', [
            'title' => 'Planillas Pila',
        ]);
    }

    public function consultaPlanillaTrabajadorAction(Request $request)
    {
        $this->setResponse('ajax');
        try {
            $perini = $request->input('perini');
            $perfin = $request->input('perfin');
            $params['cedtra'] = parent::getActUser('documento');
            $params['perini'] = $perini;
            $params['perfin'] = $perfin;

            $ps = Comman::Api();
            $ps->runCli(
                [
                    'servicio' => 'AportesEmpresas',
                    'metodo' => 'planilla_trabajador',
                    'params' => [
                        'post' => $params,
                    ],
                ]
            );
            $out = $ps->toArray();
            if (! $out['success']) {
                $response = [
                    'success' => false,
                    'message' => $out['message'],
                ];
            } else {
                $response = [
                    'success' => true,
                    'data' => $out['data'],
                ];
            }
        } catch (DebugException $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }

        return $this->renderObject($response);
    }

    public function consultaTarjetaAction()
    {
        $ps = Comman::Api();
        $ps->runCli(
            [
                'servicio' => 'CuotaMonetaria',
                'metodo' => 'saldo_pendiente_cobrar_trabajador',
                'params' => [
                    'cedtra' => parent::getActUser('documento'),
                ],
            ]
        );
        $out = $ps->toArray();
        if ($out['success'] == false) {
            $response = $out;
        } else {
            $response = $out['data'];
        }

        return view('mercurio/subsidio/consulta_tarjeta', [
            'title' => 'Consulta Saldos',
            'saldos' => $response,
        ]);
    }

    public function certificadoAfiliacionViewAction()
    {
        return view('mercurio/subsidio/certificado_afiliacion', [
            'title' => 'Certificado Afiliacion',
            'tipo' => [
                'A' => 'Certificado Afiliacion Principal',
                'I' => 'Certificacion Con Nucleo',
                'T' => 'Certificacion de Multiafiliacion',
                'P' => 'Reporte trabajador en planillas',
            ],
        ]);
    }

    public function certificadoAfiliacionAction(Request $request)
    {
        $tipo = $request->input('tipo');
        $logger = new Logger;
        $logger->registrarLog(false, 'Certificado De Afiliacion', $tipo);
        header("Location: https://comfacaenlinea.com.co/SYS/Subsidio/subflo/gene_certi_tra/$tipo/".parent::getActUser('documento'));
    }
}
