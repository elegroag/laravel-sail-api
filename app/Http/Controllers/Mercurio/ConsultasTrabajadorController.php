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
use App\Models\Mercurio47;
use App\Services\Utils\Comman;
use App\Services\Utils\Logger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConsultasTrabajadorController extends ApplicationController
{
    protected $db;

    protected $user;

    protected $tipo;

    public function __construct()
    {
        $this->db = DbBase::rawConnect();
        $this->user = session('user') ?? null;
        $this->tipo = session('tipo') ?? null;
    }

    public function index()
    {
        return view('mercurio/subsidio/index', [
            'title' => 'Subsidio',
        ]);
    }

    public function historial()
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
            ->get();

        $mercurio47 = Mercurio47::where([
            'tipo' => $tipo,
            'coddoc' => $coddoc,
            'documento' => $documento,
            'tipact' => 'T',
        ])
            ->orderBy('id', 'desc')
            ->get();

        $mercurio33 = Mercurio33::select(DB::raw('mercurio33.*'), DB::raw('mercurio28.detalle as campo_detalle'))
            ->join('mercurio47', 'mercurio33.actualizacion', '=', 'mercurio47.id')
            ->join('mercurio28', 'mercurio33.campo', '=', 'mercurio28.campo')
            ->where([
                'mercurio28.tipo' => $tipo,
                'mercurio47.tipo' => $tipo,
                'mercurio47.coddoc' => $coddoc,
                'mercurio47.documento' => $documento,
            ])
            ->orderBy('mercurio47.id', 'desc')
            ->get();

        $mercurio34 = Mercurio34::where([
            'tipo' => $tipo,
            'coddoc' => $coddoc,
            'documento' => $documento,
        ])
            ->orderBy('id', 'desc')
            ->get();

        $mercurio45 = Mercurio45::where([
            'tipo' => $tipo,
            'coddoc' => $coddoc,
            'documento' => $documento,
        ])
            ->orderBy('id', 'desc')
            ->get();

        return view('mercurio/subsidio/historial', [
            'title' => 'Historial',
            'mercurio32' => $mercurio32,
            'mercurio33' => $mercurio33,
            'mercurio34' => $mercurio34,
            'mercurio45' => $mercurio45,
            'mercurio47' => $mercurio47,

        ]);
    }

    /**
     * consulta_nucleo function
     *
     * @param  string  $cedtra
     * @return void
     */
    public function consultaNucleoView($cedtra = '')
    {
        return view('mercurio/subsidio/consulta_nucleo', [
            'title' => 'Consulta nucleo familiar',
            'cedtra' => $cedtra,
        ]);
    }

    public function consultaNucleo()
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

    public function consultaGiroView()
    {
        return view('mercurio/subsidio/consulta_giro', [
            'title' => 'Consulta Giro',
        ]);
    }

    public function consultaGiro(Request $request)
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

    public function consultaNoGiroView()
    {
        return view('mercurio/subsidio/consulta_no_giro', [
            'title' => 'Consulta No Giro',
        ]);
    }

    public function consultaNoGiro(Request $request)
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

    public function consultaPlanillaTrabajadorView()
    {
        return view('mercurio/subsidio/consulta_planilla_trabajador', [
            'title' => 'Planillas Pila',
        ]);
    }

    public function consultaPlanillaTrabajador(Request $request)
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

    public function consultaTarjeta()
    {
        $ps = Comman::Api();
        $ps->runCli(
            [
                'servicio' => 'CuotaMonetaria',
                'metodo' => 'saldo_pendiente_cobrar_trabajador',
                'params' => [
                    'cedtra' => $this->user['documento'],
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
            'saldo_pendiente' => 0,
        ]);
    }

    public function certificadoAfiliacionView()
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

    public function certificadoAfiliacion(Request $request)
    {
        $tipo = $request->input('tipo');
        $logger = new Logger;
        $logger->registrarLog(false, 'Certificado De Afiliacion', $tipo);
        header("Location: https://comfacaenlinea.com.co/SYS/Subsidio/subflo/gene_certi_tra/$tipo/" . parent::getActUser('documento'));
    }
}
