<?php

namespace App\Http\Controllers\Cajas;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use App\Models\Mercurio30;
use App\Models\Mercurio31;
use App\Models\Mercurio32;
use App\Models\Mercurio33;
use App\Models\Mercurio34;
use App\Models\Mercurio38;
use App\Models\Mercurio41;
use App\Models\Mercurio47;
use App\Services\Utils\GeneralService;
use Illuminate\Http\Request;

class PrincipalController extends ApplicationController
{
    protected $db;

    protected $user;

    protected $tipfun;

    public function __construct()
    {
        $this->db = DbBase::rawConnect();
        $this->user = session()->has('user') ? session('user') : null;
        $this->tipfun = session()->has('tipfun') ? session('tipfun') : null;
    }

    public function indexAction()
    {
        $user = $this->user['usuario'];
        $servicios = [
            'afiliacion' => [
                [
                    'name' => 'Afiliación Empresas',
                    'cantidad' => [
                        'pendientes' => Mercurio30::where(['estado' => 'P', 'usuario' => $user])->count(),
                        'aprobados' => Mercurio30::where(['estado' => 'A', 'usuario' => $user])->count(),
                        'rechazados' => Mercurio30::where(['estado' => 'R', 'usuario' => $user])->count(),
                        'devueltos' => Mercurio30::where(['estado' => 'D', 'usuario' => $user])->count(),
                        'temporales' => Mercurio30::where(['estado' => 'T', 'usuario' => $user])->count(),
                    ],
                    'icon' => 'E',
                    'url' => 'aprobacionemp/index',
                    'imagen' => 'empresas.jpg',
                ],
                [
                    'name' => 'Afiliación Independientes',
                    'cantidad' => [
                        'pendientes' => Mercurio41::where(['estado' => 'P', 'usuario' => $user])->count(),
                        'aprobados' => Mercurio41::where(['estado' => 'A', 'usuario' => $user])->count(),
                        'rechazados' => Mercurio41::where(['estado' => 'R', 'usuario' => $user])->count(),
                        'devueltos' => Mercurio41::where(['estado' => 'D', 'usuario' => $user])->count(),
                        'temporales' => Mercurio41::where(['estado' => 'T', 'usuario' => $user])->count(),
                    ],
                    'icon' => 'I',
                    'url' => 'aprobaindepen/index',
                    'imagen' => 'independiente.jpg',
                ],
                [
                    'name' => 'Afiliación Pensionados',
                    'cantidad' => [
                        'pendientes' => Mercurio38::where(['estado' => 'P', 'usuario' => $user])->count(),
                        'aprobados' => Mercurio38::where(['estado' => 'A', 'usuario' => $user])->count(),
                        'rechazados' => Mercurio38::where(['estado' => 'R', 'usuario' => $user])->count(),
                        'devueltos' => Mercurio38::where(['estado' => 'D', 'usuario' => $user])->count(),
                        'temporales' => Mercurio38::where(['estado' => 'T', 'usuario' => $user])->count(),
                    ],
                    'icon' => 'P',
                    'url' => 'aprobacionpen/index',
                    'imagen' => 'pensionado.jpg',
                ],
                [
                    'name' => 'Afiliación Trabajadores',
                    'cantidad' => [
                        'pendientes' => Mercurio31::where(['estado' => 'P', 'usuario' => $user])->count(),
                        'aprobados' => Mercurio31::where(['estado' => 'A', 'usuario' => $user])->count(),
                        'rechazados' => Mercurio31::where(['estado' => 'R', 'usuario' => $user])->count(),
                        'devueltos' => Mercurio31::where(['estado' => 'D', 'usuario' => $user])->count(),
                        'temporales' => Mercurio31::where(['estado' => 'T', 'usuario' => $user])->count(),
                    ],
                    'icon' => 'T',
                    'url' => 'aprobaciontra/index',
                    'imagen' => 'trabajadores.jpg',
                ],
                [
                    'name' => 'Afiliación Conyuges',
                    'cantidad' => [
                        'pendientes' => Mercurio32::where(['estado' => 'P', 'usuario' => $user])->count(),
                        'aprobados' => Mercurio32::where(['estado' => 'A', 'usuario' => $user])->count(),
                        'rechazados' => Mercurio32::where(['estado' => 'R', 'usuario' => $user])->count(),
                        'devueltos' => Mercurio32::where(['estado' => 'D', 'usuario' => $user])->count(),
                        'temporales' => Mercurio32::where(['estado' => 'T', 'usuario' => $user])->count(),
                    ],
                    'icon' => 'C',
                    'url' => 'aprobacioncon/index',
                    'imagen' => 'conyuges.jpg',
                ],
                [
                    'name' => 'Afiliación Beneficiarios',
                    'cantidad' => [
                        'pendientes' => Mercurio34::where(['estado' => 'P', 'usuario' => $user])->count(),
                        'aprobados' => Mercurio34::where(['estado' => 'A', 'usuario' => $user])->count(),
                        'rechazados' => Mercurio34::where(['estado' => 'R', 'usuario' => $user])->count(),
                        'devueltos' => Mercurio34::where(['estado' => 'D', 'usuario' => $user])->count(),
                        'temporales' => Mercurio34::where(['estado' => 'T', 'usuario' => $user])->count(),
                    ],
                    'icon' => 'B',
                    'url' => 'aprobacionben/index',
                    'imagen' => 'beneficiarios.jpg',
                ],
                [
                    'name' => 'Actualización datos Empresas',
                    'cantidad' => [
                        'pendientes' => Mercurio33::where(['estado' => 'P', 'usuario' => $user])->count(),
                        'aprobados' => Mercurio33::where(['estado' => 'A', 'usuario' => $user])->count(),
                        'rechazados' => Mercurio33::where(['estado' => 'R', 'usuario' => $user])->count(),
                        'devueltos' => Mercurio33::where(['estado' => 'D', 'usuario' => $user])->count(),
                        'temporales' => Mercurio33::where(['estado' => 'T', 'usuario' => $user])->count(),
                    ],
                    'icon' => 'AE',
                    'url' => 'actualizaemp/index',
                    'imagen' => 'empresas.jpg',
                ],
                [
                    'name' => 'Actualización datos Trabajador',
                    'cantidad' => [
                        'pendientes' => Mercurio47::where(['estado' => 'P', 'usuario' => $user])->count(),
                        'aprobados' => Mercurio47::where(['estado' => 'A', 'usuario' => $user])->count(),
                        'rechazados' => Mercurio47::where(['estado' => 'R', 'usuario' => $user])->count(),
                        'devueltos' => Mercurio47::where(['estado' => 'D', 'usuario' => $user])->count(),
                        'temporales' => Mercurio47::where(['estado' => 'T', 'usuario' => $user])->count(),
                    ],
                    'icon' => 'AT',
                    'url' => 'actualizatra/index',
                    'imagen' => 'datos_basicos.jpg',
                ],
            ],
            'productos' => [
                [
                    'name' => 'Lista Productos',
                    'cantidad' => 0,
                    'icon' => 'L',
                    'url' => 'admproductos/lista',
                    'imagen' => 'registro_empresa.jpg',
                ],
                [
                    'name' => 'Complemento nutricional',
                    'cantidad' => 0,
                    'icon' => 'N',
                    'url' => 'admproductos/aplicados/27',
                    'imagen' => 'complemento.jpg',
                ],
            ],
        ];

        return view('cajas/principal/index', [
            'tipfun' => $this->tipfun,
            'usuario' => $this->user['usuario'],
            'nombre' => $this->user['nombre'],
            'servicios' => $servicios,
        ]);
    }

    public function dashboardAction()
    {
        $this->setParamToView('hide_header', true);
        $this->setParamToView('title', 'Estadística');
    }

    public function traerUsuariosRegistradosAction()
    {
        $this->setResponse('ajax');
        $data = [];
        $labels = [];
        $params['nit'] = parent::getActUser('documento');
        $mercurio07 = $this->Mercurio07->findAllBySql('select tipo,count(*) as documento from mercurio07 group by 1');
        foreach ($mercurio07 as $mmercurio07) {
            $mercurio06 = $this->Mercurio06->findFirst("tipo='{$mmercurio07->getTipo()}'");
            $data[] = $mmercurio07->getDocumento();
            $labels[] = $mercurio06->getDetalle();
        }
        $response['data'] = $data;
        $response['labels'] = $labels;

        return $this->renderObject($response, false);
    }

    public function traerOpcionMasUsuadaAction()
    {
        $this->setResponse('ajax');
        $data = [];
        $params['nit'] = parent::getActUser('documento');
        $mercurio20 = $this->Mercurio20->findAllBySql('select accion,count(*) as log from mercurio20 group by 1');
        foreach ($mercurio20 as $mmercurio20) {
            $data[] = $mmercurio20->getLog();
            $labels[] = $mmercurio20->getAccion();
        }
        $response['data'] = $data;
        $response['labels'] = $labels;

        return $this->renderObject($response, false);
    }

    public function traerMotivoMasUsuadaAction()
    {
        $this->setResponse('ajax');
        $labels = [];
        $data = [];
        $params['nit'] = parent::getActUser('documento');
        $mercurio10 = $this->Mercurio10->findAllBySql('select codest,count(*) as numero from mercurio10 WHERE codest is not null group by 1');
        foreach ($mercurio10 as $mmercurio10) {
            $mercurio11 = $this->Mercurio11->findFirst("codest='{$mmercurio10->getCodest()}'");
            $data[] = $mmercurio10->getNumero();
            $labels[] = $mercurio11->getDetalle();
        }
        $response['data'] = $data;
        $response['labels'] = $labels;

        return $this->renderObject($response, false);
    }

    public function traerCargaLaboralAction()
    {
        $this->setResponse('ajax');
        try {
            $data = [];
            $params['nit'] = parent::getActUser('documento');
            $gener02 = $this->Gener02->findAllBySql(
                'SELECT distinct gener02.usuario, gener02.nombre, gener02.login
            FROM gener02,mercurio08
            WHERE gener02.usuario = mercurio08.usuario'
            );

            foreach ($gener02 as $mgener02) {
                $count = 0;
                $mercurio09 = $this->Mercurio09->find();
                foreach ($mercurio09 as $mmercurio09) {
                    $condi = "estado='P'";

                    $generalService = new GeneralService;
                    $result = $generalService->consultaTipopc($mmercurio09->getTipopc(), 'count', '', $mgener02->getUsuario(), $condi);
                    $count += $result['count'];
                }
                $data[] = $count;
                $labels[] = $mgener02->getNombre();
            }

            $response = [
                'data' => $data,
                'labels' => $labels,
                'success' => true,
            ];
        } catch (DebugException $err) {
            $response = [
                'success' => false,
                'msj' => $err->getMessage(),
            ];
        }

        return $this->renderObject($response, false);
    }

    public function downloadGlobalAction($filepath = '')
    {
        $archivo = base64_decode($filepath);
        if (preg_match('/(public)(\/)(temp)/i', $archivo) == false) {
            $fichero = "public/temp/{$archivo}";
        } else {
            $fichero = $archivo;
            $archivo = basename($archivo);
        }
        $ext = substr(strrchr($archivo, '.'), 1);
        if (file_exists($fichero)) {
            header('Content-Description: File Transfer');
            header("Content-Type: application/{$ext}");
            header("Content-Disposition: attachment; filename={$archivo}");
            header('Cache-Control: must-revalidate');
            header('Expires: 0');
            header('Pragma: public');
            header('Content-Length: '.filesize($fichero));
            ob_clean();
            readfile($fichero);
            exit();
        } else {
            exit();
        }
    }

    public function fileExisteGlobalAction(Request $request)
    {
        $this->setResponse('ajax');
        $filepath = $request->input('filepath');
        $archivo = base64_decode($filepath);
        if (preg_match('/(public)(\/)(temp)/i', $archivo) == false) {
            $fichero = "public/temp/{$archivo}";
        } else {
            $fichero = $archivo;
            $archivo = basename($archivo);
        }
        $ext = substr(strrchr($archivo, '.'), 1);
        if (file_exists($fichero)) {
            echo '{"success":true}';
        } else {
            echo '{"success":false}';
        }
    }
}
