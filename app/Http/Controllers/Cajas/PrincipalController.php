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
use App\Services\SatApi\SatConsultaServices;
use App\Services\Utils\Comman;
use App\Services\Utils\GeneralService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PrincipalController extends ApplicationController
{

    protected $db;
    protected $user;
    protected $tipo;

    public function __construct()
    {
        $this->db = DbBase::rawConnect();
        $this->user = session()->has('user') ? session('user') : null;
        $this->tipo = session()->has('tipo') ? session('tipo') : null;
        $this->setParamToView("instancePath", env('APP_URL') . 'Cajas/');
    }

    public function indexAction($permiso_menu = 1)
    {
        $this->setParamToView("hide_header", true);
        $this->setParamToView("title", "Inicio");
        $user = parent::getActUser("usuario");
        $servicios = array(
            'afiliacion' => array(
                array(
                    'name' => 'Afiliación Empresas',
                    'cantidad' => array(
                        'pendientes' => (new Mercurio30())->count("*", "conditions: estado='P' and usuario = '" . $user . "'"),
                        'aprobados' => (new Mercurio30)->count("*", "conditions: estado='A' and usuario = '" . $user . "'"),
                        'rechazados' => (new Mercurio30)->count("*", "conditions: estado='R' and usuario = '" . $user . "'"),
                        'devueltos' => (new Mercurio30)->count("*", "conditions: estado='D' and usuario = '" . $user . "'"),
                        'temporales' => (new Mercurio30)->count("*", "conditions: estado='T'")
                    ),
                    'icon' => 'E',
                    'url' => 'aprobacionemp/index',
                    'imagen' => env('APP_URL') . 'img/Mercurio/empresas.jpg',
                ),
                array(
                    'name' => 'Afiliación Independientes',
                    'cantidad' => array(
                        'pendientes' => (new Mercurio41())->count("id.*", "conditions: estado='P' and usuario = '" . $user . "'"),
                        'aprobados' => (new Mercurio41)->count("id.*", "conditions: estado='A' and usuario = '" . $user . "'"),
                        'rechazados' => (new Mercurio41)->count("id.*", "conditions: estado='R' and usuario = '" . $user . "'"),
                        'devueltos' => (new Mercurio41)->count("id.*", "conditions: estado='D' and usuario = '" . $user . "'"),
                        'temporales' => (new Mercurio41)->count("id.*", "conditions: estado='T'"),
                    ),
                    'icon' => 'I',
                    'url' => 'aprobaindepen/index',
                    'imagen' => env('APP_URL') . 'img/Mercurio/independiente.jpg',
                ),
                array(
                    'name' => 'Afiliación Pensionados',
                    'cantidad' => array(
                        'pendientes' => (new Mercurio38())->count("id.*", "conditions: estado='P' and usuario = '" . $user . "'"),
                        'aprobados' => (new Mercurio38)->count("id.*", "conditions: estado='A' and usuario = '" . $user . "'"),
                        'rechazados' => (new Mercurio38)->count("id.*", "conditions: estado='R' and usuario = '" . $user . "'"),
                        'devueltos' => (new Mercurio38)->count("id.*", "conditions: estado='D' and usuario = '" . $user . "'"),
                        'temporales' => (new Mercurio38)->count("id.*", "conditions: estado='T'"),
                    ),
                    'icon' => 'P',
                    'url' => 'aprobacionpen/index',
                    'imagen' => env('APP_URL') . 'img/Mercurio/pensionado.jpg',
                ),
                array(
                    'name' => 'Afiliación Trabajadores',
                    'cantidad' => array(
                        'pendientes' => (new Mercurio31())->count("id.*", "conditions: estado='P' and usuario = '" . $user . "'"),
                        'aprobados' => (new Mercurio31)->count("id.*", "conditions: estado='A' and usuario = '" . $user . "'"),
                        'rechazados' => (new Mercurio31)->count("id.*", "conditions: estado='R' and usuario = '" . $user . "'"),
                        'devueltos' => (new Mercurio31)->count("id.*", "conditions: estado='D' and usuario = '" . $user . "'"),
                        'temporales' => (new Mercurio31)->count("id.*", "conditions: estado='T'"),
                    ),
                    'icon' => 'T',
                    'url' => 'aprobaciontra/index',
                    'imagen' => env('APP_URL') . 'img/Mercurio/trabajadores.jpg',
                ),
                array(
                    'name' => 'Afiliación Conyuges',
                    'cantidad' => array(
                        'pendientes' => (new Mercurio32())->count("id.*", "conditions: estado='P' and usuario = '" . $user . "'"),
                        'aprobados' => (new Mercurio32)->count("id.*", "conditions: estado='A' and usuario = '" . $user . "'"),
                        'rechazados' => (new Mercurio32)->count("id.*", "conditions: estado='R' and usuario = '" . $user . "'"),
                        'devueltos' => (new Mercurio32)->count("id.*", "conditions: estado='D' and usuario = '" . $user . "'"),
                        'temporales' => (new Mercurio32)->count("id.*", "conditions: estado='T'"),
                    ),
                    'icon' => 'C',
                    'url' => 'aprobacioncon/index',
                    'imagen' => env('APP_URL') . 'img/Mercurio/conyuges.jpg',
                ),
                array(
                    'name' => 'Afiliación Beneficiarios',
                    'cantidad' => array(
                        'pendientes' => (new Mercurio34())->count("id.*", "conditions: estado='P' and usuario = '" . $user . "'"),
                        'aprobados' => (new Mercurio34)->count("id.*", "conditions: estado='A' and usuario = '" . $user . "'"),
                        'rechazados' => (new Mercurio34)->count("id.*", "conditions: estado='R' and usuario = '" . $user . "'"),
                        'devueltos' => (new Mercurio34)->count("id.*", "conditions: estado='D' and usuario = '" . $user . "'"),
                        'temporales' => (new Mercurio34)->count("id.*", "conditions: estado='T'"),
                    ),
                    'icon' => 'B',
                    'url' => 'aprobacionben/index',
                    'imagen' => env('APP_URL') . 'img/Mercurio/beneficiarios.jpg',
                ),
                array(
                    'name' => 'Actualización datos Empresas',
                    'cantidad' => array(
                        'pendientes' => (new Mercurio33())->count("id.*", "conditions: estado='P' and usuario = '" . $user . "'"),
                        'aprobados' => (new Mercurio33)->count("id.*", "conditions: estado='A' and usuario = '" . $user . "'"),
                        'rechazados' => (new Mercurio33)->count("id.*", "conditions: estado='R' and usuario = '" . $user . "'"),
                        'devueltos' => (new Mercurio33)->count("id.*", "conditions: estado='D' and usuario = '" . $user . "'"),
                        'temporales' => (new Mercurio33)->count("id.*", "conditions: estado='T'"),
                    ),
                    'icon' => 'AE',
                    'url' => 'actualizardatos/index',
                    'imagen' => env('APP_URL') . 'img/Mercurio/empresas.jpg',
                ),
                array(
                    'name' => 'Actualización datos Trabajador',
                    'cantidad' => array(
                        'pendientes' => (new Mercurio47())->count("id.*", "conditions: estado='P' and usuario = '" . $user . "'"),
                        'aprobados' => (new Mercurio47)->count("id.*", "conditions: estado='A' and usuario = '" . $user . "'"),
                        'rechazados' => (new Mercurio47)->count("id.*", "conditions: estado='R' and usuario = '" . $user . "'"),
                        'devueltos' => (new Mercurio47)->count("id.*", "conditions: estado='D' and usuario = '" . $user . "'"),
                        'temporales' => (new Mercurio47)->count("id.*", "conditions: estado='T'"),
                    ),
                    'icon' => 'AT',
                    'url' => 'aprobaciondatos/index',
                    'imagen' => env('APP_URL') . 'img/Mercurio/datos_basicos.jpg',
                )
            ),
            'productos' => array(
                array(
                    'name' => 'Lista Productos',
                    'cantidad' => 0,
                    'icon' => 'L',
                    'url' => 'admproductos/lista',
                    'imagen' => env('APP_URL') . 'img/Mercurio/registro_empresa.jpg',
                ),
                array(
                    'name' => 'Complemento nutricional',
                    'cantidad' => 0,
                    'icon' => 'N',
                    'url' => 'admproductos/aplicados/27',
                    'imagen' => env('APP_URL') . 'img/Mercurio/complemento.jpg',
                )
            )
        );

        $this->setParamToView("servicios", $servicios);
        #Tag::displayTo("permiso_menu", $permiso_menu);
    }

    public function dashboardAction()
    {
        $this->setParamToView("hide_header", true);
        $this->setParamToView("title", "Estadística");
    }

    public function traerUsuariosRegistradosAction()
    {
        $this->setResponse("ajax");
        $data = array();
        $labels = array();
        $params['nit'] = parent::getActUser("documento");
        $mercurio07 = $this->Mercurio07->findAllBySql("select tipo,count(*) as documento from mercurio07 group by 1");
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
        $this->setResponse("ajax");
        $data = array();
        $params['nit'] = parent::getActUser("documento");
        $mercurio20 = $this->Mercurio20->findAllBySql("select accion,count(*) as log from mercurio20 group by 1");
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
        $this->setResponse("ajax");
        $labels = array();
        $data = array();
        $params['nit'] = parent::getActUser("documento");
        $mercurio10 = $this->Mercurio10->findAllBySql("select codest,count(*) as numero from mercurio10 WHERE codest is not null group by 1");
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
        $this->setResponse("ajax");
        try {
            $data = array();
            $params['nit'] = parent::getActUser("documento");
            $gener02 = $this->Gener02->findAllBySql(
                "SELECT distinct gener02.usuario, gener02.nombre, gener02.login
            FROM gener02,mercurio08
            WHERE gener02.usuario = mercurio08.usuario"
            );

            foreach ($gener02 as $mgener02) {
                $count = 0;
                $mercurio09 = $this->Mercurio09->find();
                foreach ($mercurio09 as $mmercurio09) {
                    $condi = "estado='P'";

                    $generalService = new GeneralService();
                    $result = $generalService->consultaTipopc($mmercurio09->getTipopc(), 'count', "", $mgener02->getUsuario(), $condi);
                    $count += $result['count'];
                }
                $data[] = $count;
                $labels[] = $mgener02->getNombre();
            }

            $response = array(
                'data' => $data,
                'labels' => $labels,
                'success' => true
            );
        } catch (DebugException $err) {
            $response = array(
                'success' => false,
                'msj' => $err->getMessage()
            );
        }
        return $this->renderObject($response, false);
    }

    public function downloadGlobalAction($filepath = "")
    {
        $archivo = base64_decode($filepath);
        if (preg_match('/(public)(\/)(temp)/i', $archivo) == false) {
            $fichero = "public/temp/{$archivo}";
        } else {
            $fichero = $archivo;
            $archivo = basename($archivo);
        }
        $ext = substr(strrchr($archivo, "."), 1);
        if (file_exists($fichero)) {
            header('Content-Description: File Transfer');
            header("Content-Type: application/{$ext}");
            header("Content-Disposition: attachment; filename={$archivo}");
            header('Cache-Control: must-revalidate');
            header('Expires: 0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($fichero));
            ob_clean();
            readfile($fichero);
            exit();
        } else {
            exit();
        }
    }

    public function fileExisteGlobalAction(Request $request)
    {
        $this->setResponse("ajax");
        $filepath = $request->input('filepath');
        $archivo = base64_decode($filepath);
        if (preg_match('/(public)(\/)(temp)/i', $archivo) == false) {
            $fichero = "public/temp/{$archivo}";
        } else {
            $fichero = $archivo;
            $archivo = basename($archivo);
        }
        $ext = substr(strrchr($archivo, "."), 1);
        if (file_exists($fichero)) {
            echo "{\"success\":true}";
        } else {
            echo "{\"success\":false}";
        }
    }
}
