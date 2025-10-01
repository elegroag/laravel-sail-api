<?php

namespace App\Http\Controllers\Mercurio;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Library\Auth\SessionCookies;
use App\Models\Adapter\DbBase;
use App\Models\Mercurio07;
use App\Models\Mercurio30;
use App\Models\Mercurio31;
use App\Models\Mercurio32;
use App\Models\Mercurio34;
use App\Models\Mercurio36;
use App\Models\Mercurio38;
use App\Models\Mercurio41;
use App\Models\Mercurio45;
use App\Models\Mercurio47;
use App\Services\Entidades\EmpresaService;
use App\Services\Entidades\IndependienteService;
use App\Services\Entidades\ParticularService;
use App\Services\Entidades\TrabajadorService;
use App\Services\Utils\Comman;
use Carbon\Carbon;
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
    }

    public function indexAction()
    {
        if ($this->user == null) {
            return redirect()->route('login');
        }
        return view('mercurio/principal/index', [
            'tipo' => $this->tipo,
            'documento' => $this->user['documento'],
            'nombre' => $this->user['nombre']
        ]);
    }

    public function dashboardEmpresaAction()
    {
        return view('principal.dashboard_empresa', [
            'help' => false,
            'title' => "Dashboard Empresas",
            'hide_header' => true,
            'tipo' => $this->tipo,
            'documento' => $this->user['documento'],
            'nombre' => $this->user['nombre'],
        ]);
    }

    public function dashboardTrabajadorAction()
    {
        return view('principal.dashboard_trabajador', [
            'help' => false,
            'title' => "Dashboard Trabajadores",
            'hide_header' => true,
            'tipo' => $this->tipo,
            'documento' => $this->user['documento'],
            'nombre' => $this->user['nombre'],
        ]);
    }

    public function traerAportesEmpresaAction()
    {
        $this->setResponse("ajax");

        $response['labels'] = array(
            "Enero",
            "Febrero",
            "Marzo",
            "Abril",
            "Mayo",
            "Junio",
            "Julio",
            "Agosto",
            "Septiembre",
            "Octubre",
            "Noviembre",
            "Diciembre",
        );
        $data = array();

        $ps = Comman::Api();
        $ps->runCli(
            array(
                "servicio" => "AportesEmpresas",
                "metodo" => "aportes_empresa_mensual",
                "params" => array(
                    "nit" => $this->user['documento'],
                    "vigencia" => date("Y")
                )
            )
        );

        $subsi11 = $ps->toArray();
        foreach ($subsi11['data'] as $msubsi11) {
            $data[] = $msubsi11['valcon'];
        }

        $response['data'] = $data;

        return $this->renderObject($response, false);
    }

    public function traerCategoriasEmpresaAction()
    {
        $this->setResponse("ajax");
        $data = array();
        $labels = array();

        $ps = Comman::Api();
        $ps->runCli(
            array(
                "servicio" => "PoblacionAfiliada",
                "metodo" => "categoria_trabajador_empresa",
                "params" => array(
                    "nit" => $this->user['documento']
                )
            )
        );
        $subsi11 = $ps->toArray();
        if (!$subsi11['success']) {
            return $this->renderObject([
                'success' => false,
                'msj' => "No se pudo traer las categorias"
            ]);
        }

        foreach ($subsi11['data'] as $msubsi11) {
            $data[] = $msubsi11['cantidad'];
            $labels[] = $msubsi11['codcat'];
        }

        $response['data'] = $data;
        $response['labels'] = $labels;

        return $this->renderObject($response, false);
    }

    public function traerGiroEmpresaAction()
    {
        $this->setResponse("ajax");
        $data = array();
        $response['labels'] = array(
            "Enero",
            "Febrero",
            "Marzo",
            "Abril",
            "Mayo",
            "Junio",
            "Julio",
            "Agosto",
            "Septiembre",
            "Octubre",
            "Noviembre",
            "Diciembre",
        );

        $ps = Comman::Api();
        $ps->runCli(
            array(
                "servicio" => "CuotaMonetaria",
                "metodo" => "giro_trabajador_empresa",
                "params" => array(
                    "nit" => $this->user['documento']
                )
            )
        );
        $subsi09 = $ps->toArray();
        if (!$subsi09['success']) {
            return $this->renderObject([
                'success' => false,
                'msj' => "No se pudo traer el giro"
            ]);
        }

        foreach ($subsi09['data'] as $msubsi09) {
            $data[] = $msubsi09['valor'];
        }
        $response['data'] = $data;

        return $this->renderObject($response, false);
    }

    public function fileExisteGlobalAction(Request $request, Response $response, string $filepath)
    {
        $archivo = base64_decode($filepath);
        if (preg_match('/(storage)(\/)(temp)/i', $archivo) == false) {
            $fichero = storage_path('temp/' . $archivo);
        } else {
            $fichero = storage_path($archivo);
        }
        if (file_exists($fichero)) {
            return $this->renderObject(array("success" => true));
        } else {
            return $this->renderObject(array("success" => false));
        }
    }

    public function actualizaEstadoSolicitudesAction()
    {
        try {
            $this->setResponse("ajax");

            if (get_flashdata_item("Syncron") == true) {
                return $this->renderObject(array(
                    "success" => true,
                    "msj" => "Y se realizo la actualización de las solicitudes",
                ), false);
            }
            $tipo = $this->tipo;

            $coddoc = $this->user['coddoc'];
            $documento = $this->user['documento'];

            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
                array(
                    "servicio" => "ComfacaEmpresas",
                    "metodo" => "actualiza_empresa_enlinea",
                    "params" => $documento
                )
            );
            $out = $procesadorComando->toArray();
            $salida_empresas = $out;


            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
                array(
                    "servicio" => "ComfacaEmpresas",
                    "metodo" => "actualiza_trabajador_enlinea",
                    "params" => $documento
                )
            );
            $out = $procesadorComando->toArray();
            $salida_trabajadores = $out;

            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
                array(
                    "servicio" => "ComfacaEmpresas",
                    "metodo" => "actualiza_conyuge_enlinea",
                    "params" => $documento
                )
            );
            $out = $procesadorComando->toArray();
            $salida_conyuges = $out;

            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
                array(
                    "servicio" => "ComfacaEmpresas",
                    "metodo" => "actualiza_beneficiario_enlinea",
                    "params" => $documento
                )
            );
            $out = $procesadorComando->toArray();
            $salida_beneficiarios = $out;

            $hoy = Carbon::now()->format('Y-m-d');
            Mercurio07::where('documento', $documento)
                ->where('coddoc', $coddoc)
                ->where('tipo', $tipo)
                ->update(['fecha_syncron' => $hoy]);

            $salida = array(
                "success" => true,
                "msj" => "El proceso de actualización se ha completado con éxito",
                "empresas" => $salida_empresas,
                "trabajadores" => $salida_trabajadores,
                "conyuges" => $salida_conyuges,
                "beneficiarios" => $salida_beneficiarios
            );

            set_flashdata("Syncron", true, true);
        } catch (DebugException $tf) {
            $salida = array(
                "success" => false,
                "msj" => $tf->getMessage()
            );
        }
        return $this->renderObject($salida);
    }

    public function upAction()
    {
        $this->setResponse("view");
        get_flashdata_item("Syncron", true);
    }

    public function listaAdressAction()
    {
        try {
            $this->setResponse("ajax");
            $adress =  $this->db->inQueryAssoc("SELECT * FROM mercurio15 WHERE 1=1");
            $salida = array(
                "success" => true,
                "data" => $adress,
                "msj" => "El proceso de consulta completo con éxito"
            );
        } catch (DebugException $tf) {
            $salida = array(
                "success" => false,
                "msj" => $tf->getMessage()
            );
        }
        return $this->renderObject($salida);
    }


    public function serviciosAction()
    {
        $this->setResponse("ajax");
        try {
            $tipo = session('tipo');
            switch ($tipo) {
                case 'E':
                    $mservice = new EmpresaService();
                    break;
                case 'P':
                    $mservice = new ParticularService();
                    break;
                case 'I':
                case 'F':
                case 'O':
                    $mservice = new IndependienteService();
                    break;
                case 'T':
                    $mservice = new TrabajadorService();
                    break;
                default:
                    break;
            }

            if (session('estado_afiliado') == 'I') {
                $mservice = new ParticularService();
            }

            $servicios = $mservice->resumenServicios();
            $salida = [
                'success' => true,
                'msj' => 'Proceso completado con éxito',
                'data' => $servicios
            ];
        } catch (DebugException $e) {
            $salida = [
                'success' => false,
                'msj' => $e->getMessage()
            ];
        }
        return $this->renderObject($salida, false);
    }

    public function validaSyncroAction()
    {
        $this->setResponse("ajax");

        try {
            $documento = $this->user['documento'];
            $coddoc = $this->user['coddoc'];
            $tipo = $this->tipo;

            $hoy = date('Y-m-d');
            $solicitante =  (new Mercurio07)->findFirst(" documento='{$documento}' and coddoc='{$coddoc}' and tipo='{$tipo}'");
            if ($solicitante->getFechaSyncron() == '' || is_null($solicitante->getFechaSyncron())) {
                $solicitante->setFechaSyncron($hoy);
                $solicitante->save();
            }

            $hoy = Carbon::now();
            $dif = $hoy->diff(Carbon::parse($solicitante->getFechaSyncron()));
            $interval = $dif->days;
            $salida = array(
                'success' => true,
                'msj' => 'Consulta realizada con éxito',
                'data' => array(
                    'ultimo_syncron' => Carbon::parse($solicitante->getFechaSyncron())->format('d - M - Y'),
                    'syncron' => ($interval >= 10) ? true : false
                )
            );
        } catch (DebugException $e) {
            $salida = array(
                'success' => false,
                'msj' => $e->getMessage()
            );
        }
        return $this->renderObject($salida, false);
    }

    /**
     * ingresoDirigidoAction function
     * aplica para los particulares que hacen su primer registro al sistema
     * @param string $id
     * @param string $documento
     * @param string $coddoc
     * @param string $calemp
     * @return void
     */
    public function ingresoDirigidoAction(Request $request)
    {
        $this->setResponse("view");
        try {
            $dataVerify = $request->input('dataVerify');
            $tk = explode('|', base64_decode($dataVerify));

            if (count($tk) !== 2) {
                throw new DebugException("El identificador de la empresa no es correcto", 404);
            }

            $data = Kdecrypt($tk[0], $tk[1]);
            if ($data == false) {
                throw new DebugException("El identificador de la empresa no es correcto", 404);
            }

            $token = json_decode($data);
            if ($token == false || is_null($token) || is_object($token) == false) {
                throw new DebugException("El identificador de la empresa no es correcto", 404);
            }

            if (
                isset($token->documento) == false ||
                isset($token->tipo) == false ||
                isset($token->coddoc) == false ||
                isset($token->tipafi) == false
            ) {
                throw new DebugException("El identificador de la empresa no es correcto", 404);
            }

            $solicitud = false;
            switch ($token->tipafi) {
                case 'E':
                    if ($token->documento == '' || $token->tipo == '' || $token->coddoc == '') {
                        throw new DebugException("El identificador de la empresa no es correcto", 404);
                    }
                    if ($token->id == '' || is_null($token->id)) {
                        $solicitud = (new Mercurio07)->findFirst(" documento='{$token->documento}' and coddoc='{$token->coddoc}' and tipo='{$token->tipo}'");
                        $url = "mercurio/empresa/index";
                    } else {
                        $solicitud = (new Mercurio30)->findFirst(" id='{$token->id}' and documento='{$token->documento}' and coddoc='{$token->coddoc}'");
                        $url = "mercurio/empresa/index#proceso/{$token->id}";
                    }
                    break;
                case 'I':
                    if ($token->id == '' || $token->documento == '' || $token->tipo == '' || $token->coddoc == '') {
                        throw new DebugException("El identificador de la empresa no es correcto", 404);
                    }
                    $solicitud = (new Mercurio41)->findFirst(" id='{$token->id}' and documento='{$token->documento}' and coddoc='{$token->coddoc}'");
                    $url = "mercurio/independiente/index#proceso/{$token->id}";
                    break;
                case 'O':
                    if ($token->id == '' || $token->documento == '' || $token->tipo == '' || $token->coddoc == '') {
                        throw new DebugException("El identificador de la empresa no es correcto", 404);
                    }
                    $solicitud = (new  Mercurio38)->findFirst(" id='{$token->id}' and documento='{$token->documento}' and coddoc='{$token->coddoc}'");
                    $url = "mercurio/pensionado/index#proceso/{$token->id}";
                    break;
                case 'F':
                    if ($token->id == '' || $token->documento == '' || $token->tipo == '' || $token->coddoc == '') {
                        throw new DebugException("El identificador de la empresa no es correcto", 404);
                    }
                    $solicitud = (new  Mercurio36)->findFirst(" id='{$token->id}' and documento='{$token->documento}' and coddoc='{$token->coddoc}'");
                    $url = "mercurio/facultativo/index#proceso/{$token->id}";
                    break;
                default:
                    // Ingreso usuario particular
                    $solicitud = (new Mercurio07)->findFirst(" documento='{$token->documento}' and coddoc='{$token->coddoc}' and tipo='{$token->tipo}'");
                    $url = "mercurio/principal/index";
                    break;
            }

            if ($solicitud == false) {
                throw new DebugException("La identificación de la solicitud no es correcto", 404);
            }

            $auth = new SessionCookies(
                "model: mercurio07",
                "tipo: {$token->tipo}",
                "coddoc: {$token->coddoc}",
                "documento: {$token->documento}",
                "estado: A",
                "estado_afiliado: I"
            );

            if (!$auth->authenticate()) {
                throw new DebugException("Error en la autenticación del usuario", 501);
            }

            set_flashdata(
                "success",
                array(
                    "type" => "html",
                    "msj" => "<p style='font-size:1rem' class='text-left'>El usuario ha realizado el pre-registro de forma correcta</p>" .
                        "<p style='font-size:1rem' class='text-left'>El registro realizado es de tipo \"Particular\", ahora puedes realizar las afiliaciones de modo seguro.<br/>" .
                        "Las credenciales de acceso le seran enviadas a la respectiva dirección de correo registrado.<br/></p>"
                )
            );

            return redirect()->to($url);
        } catch (DebugException $e) {
            set_flashdata("error", array("msj" => $e->getMessage()));
            return redirect()->to("mercurio/login");
        }
    }

    public function estado_actualAction()
    {
        $this->setResponse("ajax");

        try {
            $tipo = $this->user['tipo'];
            $documento = $this->user['documento'];

            switch ($tipo) {
                case 'T':
                    $procesadorComando = Comman::Api();
                    $procesadorComando->runCli(
                        array(
                            "servicio" => "ComfacaEmpresas",
                            "metodo" => "informacion_trabajador",
                            "params" => array(
                                "cedtra" => $documento
                            )
                        )
                    );
                    $out = $procesadorComando->toArray();
                    break;
                case 'E':
                case 'I':
                case 'F':
                case 'O':
                    $procesadorComando = Comman::Api();
                    $procesadorComando->runCli(
                        array(
                            "servicio" => "ComfacaEmpresas",
                            "metodo" => "informacion_empresa",
                            "params" => array(
                                "nit" => $documento
                            )
                        )
                    );
                    $out = $procesadorComando->toArray();
                    break;
                default:
                    $out = false;
                    break;
            }

            $salida = array(
                'success' => true,
                'msj' => 'Proceso completado con éxito',
                'data' => $out
            );
        } catch (DebugException $e) {
            $salida = array(
                "success" => false,
                "msj" => $e->getMessage()
            );
        }
        return $this->renderObject($salida);
    }
}
