<?php

namespace App\Http\Controllers\Mercurio;

use App\Exceptions\AuthException;
use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Library\Collections\ParamsEmpresa;
use App\Library\Collections\ParamsFacultativo;
use App\Library\Collections\ParamsTrabajador;
use App\Models\Adapter\DbBase;
use App\Models\Gener09;
use App\Models\Gener18;
use App\Models\Mercurio01;
use App\Models\Mercurio10;
use App\Models\Mercurio30;
use App\Models\Mercurio31;
use App\Models\Mercurio36;
use App\Models\Mercurio37;
use App\Models\Subsi54;
use App\Services\Entidades\FacultativoService;
use App\Services\FormulariosAdjuntos\FacultativoAdjuntoService;
use App\Services\FormulariosAdjuntos\Formularios;
use App\Services\Utils\AsignarFuncionario;
use App\Services\Utils\ChangeCuentaService;
use App\Services\Utils\Comman;
use App\Services\Utils\GeneralService;
use App\Services\Utils\GuardarArchivoService;
use App\Services\Utils\SenderValidationCaja;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FacultativoController extends ApplicationController
{
    protected $tipopc = 10;
    /**
     * facultativoService variable
     * @var FacultativoService
     */
    private $facultativoService;

    /**
     * asignarFuncionario variable
     * @var AsignarFuncionario
     */
    protected $asignarFuncionario;

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
        return view("mercurio/facultativo/index", [
            "title" => "Afiliación Facultativos",
            'tipo' => $this->tipo,
            'documento' => $this->user['documento'],
        ]);
    }

    public function actualizarAction(Request $request)
    {
        $this->setResponse("ajax");
        try {
            $id = $request->input('id');
            $params = $this->serializeData($request);

            $params['tipo'] = parent::getActUser("tipo");
            $params['coddoc'] = parent::getActUser("coddoc");
            $params['documento'] = parent::getActUser("documento");
            $params['estado'] = 'T';

            $this->facultativoService = new FacultativoService;
            $this->asignarFuncionario = new AsignarFuncionario;
            $params['usuario'] = $this->asignarFuncionario->asignar($this->tipopc, $this->user['codciu']);

            $this->facultativoService->updateByFormData($id, $params);
            # $this->facultativoService->endTransa();
            $pensionado = $this->facultativoService->findById($id);
            $data =  $pensionado->getArray();

            $response = array(
                'success' => true,
                'msj' => 'Registro completado con éxito',
                'data' => $data
            );
        } catch (DebugException $e) {
            $response = array(
                'success' => false,
                'msj' => $e->getMessage()
            );
        }
        return $this->renderObject($response, false);
    }

    /**
     * guardarAction function
     * @changed [2023-12-01]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     * @return void
     */
    public function guardarAction(Request $request)
    {
        $this->setResponse("ajax");
        $facultativoService = new FacultativoService();
        $this->db->begin();

        try {
            $id = $request->input('id', "addslaches", "extraspaces", "striptags");
            $params = $this->serializeData($request);

            $params['tipo'] = $this->tipo;
            $params['coddoc'] = $this->user['coddoc'];
            $params['documento'] = $this->user['documento'];

            $this->asignarFuncionario = new AsignarFuncionario();
            $params['usuario'] = $this->asignarFuncionario->asignar($this->tipopc, $this->user['codciu']);

            if (is_null($id) || $id == '') {
                $facultativo = $facultativoService->createByFormData($params);
            } else {
                $res = $facultativoService->updateByFormData($id, $params);
                if ($res == false) {
                    throw new DebugException("Error no se actualizo los datos", 301);
                }
                $facultativo = $facultativoService->findById($id);
            }

            $facultativoService->paramsApi();
            $facultativoAdjuntoService = new FacultativoAdjuntoService($facultativo);
            $out = $facultativoAdjuntoService->formulario()->getResult();
            (new GuardarArchivoService(
                array(
                    'tipopc' => $this->tipopc,
                    'coddoc' => 1,
                    'id' => $facultativo->getId()
                )
            ))->salvarDatos($out);

            $out = $facultativoAdjuntoService->tratamientoDatos()->getResult();
            (new GuardarArchivoService(
                array(
                    'tipopc' => $this->tipopc,
                    'coddoc' => 25,
                    'id' => $facultativo->getId()
                )
            ))->salvarDatos($out);

            $out = $facultativoAdjuntoService->cartaSolicitud()->getResult();
            (new GuardarArchivoService(
                array(
                    'tipopc' => $this->tipopc,
                    'coddoc' => 24,
                    'id' => $facultativo->getId()
                )
            ))->salvarDatos($out);

            $data =  $facultativo->getArray();

            $response = array(
                'success' => true,
                'msj' => 'Registro completado con éxito',
                'data' => $data
            );
            $this->db->commit();
        } catch (DebugException $e) {
            $response = array(
                'success' => false,
                'msj' => $e->getMessage()
            );
            $this->db->rollBack();
        }
        return $this->renderObject($response);
    }

    /**
     * serializeData function
     * @return array
     */
    function serializeData(Request $request)
    {
        return array(
            'coddocrepleg' => $request->input('coddocrepleg'),
            'cedtra' => $request->input('cedtra'),
            'rural' => $request->input('rural'),
            'vivienda' => $request->input('vivienda'),
            'tipafi' => $request->input('tipafi'),
            'fecini' => $request->input('fecini'),
            'tippag' => $request->input('tippag'),
            'cargo' => $request->input('cargo'),
            'resguardo_id' => $request->input('resguardo_id'),
            'peretn' => $request->input('peretn'),
            'pub_indigena_id' => $request->input('pub_indigena_id'),
            'cedtra' => $request->input('cedtra'),
            'tipdoc' => $request->input('tipdoc'),
            'priape' => $request->input('priape'),
            'segape' => $request->input('segape'),
            'prinom' => $request->input('prinom'),
            'segnom' => $request->input('segnom'),
            'fecnac' => $request->input('fecnac'),
            'ciunac' => $request->input('ciunac'),
            'sexo' => $request->input('sexo'),
            'estciv' => $request->input('estciv'),
            'cabhog' => $request->input('cabhog'),
            'codciu' => $request->input('codciu'),
            'codzon' => $request->input('codzon'),
            'direccion' => $request->input('direccion'),
            'barrio' => $request->input('barrio'),
            'telefono' => $request->input('telefono'),
            'celular' => $request->input('celular'),
            'email' => $request->input('email'),
            'salario' => $request->input('salario'),
            'captra' => $request->input('captra'),
            'tipdis' => $request->input('tipdis'),
            'nivedu' => $request->input('nivedu'),
            'vivienda' => $request->input('vivienda'),
            'autoriza' => $request->input('autoriza'),
            'codact' => $request->input('codact'),
            'codcaj' => $request->input('codcaj'),
            'facvul' => $request->input('facvul'),
            'orisex' => $request->input('orisex'),
            'codban' => $request->input('codban'),
            'calemp' => 'F',
            'tipcue' => $request->input('tipcue'),
            'numcue' => $request->input('numcue'),
            'cargo' => $request->input('cargo'),
            'fecsol' => date('Y-m-d'),
        );
    }

    /**
     * validaAction function
     * @return void
     */
    public function validaAction(Request $request)
    {
        $this->setResponse("ajax");
        try {

            $cedtra = $request->input('cedtra');
            $solicitud = Mercurio36::where("documento", $cedtra)->whereIn("estado", ['A', 'I'])->first();

            $solicitudPrevia = false;
            if ($solicitud) {
                $solicitudPrevia = $solicitud->toArray();
            }

            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
                array(
                    "servicio" => "ComfacaEmpresas",
                    "metodo" => "informacion_empresa",
                    "params" => array(
                        "nit" => $cedtra
                    )
                )
            );

            $empresa = null;
            $trabajador = null;

            $out = $procesadorComando->toArray();
            if ($out['success']) {
                $empresa = (count($out['data']) > 0) ? $out['data'] : false;
            }

            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
                array(
                    "servicio" => "ComfacaEmpresas",
                    "metodo" => "informacion_trabajador",
                    "params" => array(
                        'cedtra' => $cedtra
                    )
                )
            );

            $out =  $procesadorComando->toArray();
            if ($out['success']) {
                $trabajador = (count($out['data']) > 0) ? $out['data'] : false;
            }

            $response = array(
                "success" => true,
                "solicitud_previa" => $solicitudPrevia,
                "empresa" => $empresa,
                "trabajador" => $trabajador
            );
        } catch (DebugException $e) {
            $response = array(
                "success" => false,
                "msj" =>  "No se pudo validar la información, {$e->getMessage()}"
            );
        }
        return $this->renderObject($response, false);
    }

    /**
     * borrarArchivoAction function
     * @return void
     */
    public function borrarArchivoAction(Request $request)
    {
        $this->setResponse("ajax");
        try {
            $numero = $request->input('id', "addslaches", "alpha", "extraspaces", "striptags");
            $coddoc = $request->input('coddoc', "addslaches", "alpha", "extraspaces", "striptags");

            $mercurio01 = (new Mercurio01())->findFirst();
            $mercurio37 = (new Mercurio37())->findFirst("tipopc='{$this->tipopc}' and numero='{$numero}' and coddoc='{$coddoc}'");

            $filepath = base_path() . '' . $mercurio01->getPath() . $mercurio37->getArchivo();
            if (file_exists($filepath)) {
                unlink(base_path() . '' . $mercurio01->getPath() . $mercurio37->getArchivo());
            }

            (new Mercurio37())->deleteAll("tipopc='{$this->tipopc}' and numero='{$numero}' and coddoc='{$coddoc}'");

            $response = array(
                "success" => true,
                "msj" => "El archivo se borro de forma correcta"
            );
        } catch (DebugException $e) {
            $response = array(
                "success" => false,
                "msj" => $e->getMessage()
            );
        }
        return $this->renderObject($response, false);
    }

    public function guardarArchivoAction(Request $request)
    {
        $this->setResponse("ajax");
        try {
            $id = $request->input('id', "addslaches", "alpha", "extraspaces", "striptags");
            $coddoc = $request->input('coddoc', "addslaches", "alpha", "extraspaces", "striptags");

            $guardarArchivoService = new GuardarArchivoService(array(
                'tipopc' => $this->tipopc,
                'coddoc' => $coddoc,
                'id' => $id
            ));

            $mercurio37 = (new Mercurio37())->findFirst("tipopc='{$this->tipopc}' and numero='{$id}' and coddoc='{$coddoc}'");

            $response = array(
                'success' => true,
                'msj' => 'Ok archivo procesado',
                'data' => $mercurio37->getArray()
            );
        } catch (DebugException $e) {
            $response = array(
                'success' => false,
                'msj' => $e->getMessage()
            );
        }
        return $this->renderObject($response, false);
    }

    /**
     * enviarCajaAction function
     * @return void
     */
    public function enviarCajaAction(Request $request)
    {
        $this->setResponse("ajax");
        try {
            $id = $request->input('id', "addslaches", "alpha", "extraspaces", "striptags");
            $facultativoService = new FacultativoService();
            # $facultativoService->setTransa();

            $asignarFuncionario = new AsignarFuncionario();
            $usuario = $asignarFuncionario->asignar($this->tipopc, $this->user['codciu']);

            $facultativoService->enviarCaja(new SenderValidationCaja(), $id, $usuario);
            # $facultativoService->endTransa();

            $salida = array(
                "success" => true,
                "msj" => "El envio de la solicitud se ha completado con éxito"
            );
        } catch (DebugException $e) {
            $salida = array(
                "success" => false,
                "msj" => $e->getMessage()
            );
        }
        return $this->renderObject($salida);
    }

    public function descargar_formularioAction($id)
    {
        ///public/docs/formulario_mercurio/formulario_independiente.png
        $this->setResponse("ajax");

        $mercurio36 = (new Mercurio36())->findFirst("id='{$id}'");



        $procesadorComando = Comman::Api();
        $procesadorComando->runCli(
            array(
                "servicio" => "ComfacaAfilia",
                "metodo" => "parametros_empresa"
            ),
            false
        );

        $datos_captura =  $procesadorComando->toArray();
        $paramsEmpresa = new ParamsEmpresa();
        $paramsEmpresa->setDatosCaptura($datos_captura);

        $time = strtotime('now');
        $file = "formulario_{$mercurio36->getNit()}_{$time}.pdf";
        $formularios = new Formularios();
        $formularios->facultativoAfiliacion(
            array(
                'pensionado' => $mercurio36
            ),
            $file
        );
        return $this->renderObject(array(
            "success" => true,
            "name" => $file,
            "url" => "facultitivo/downloadFile/" . $file
        ));
    }


    public function reloadArchivosAction(Request $request)
    {
        $this->setResponse("ajax");

        $this->facultativoService = new FacultativoService;
        try {
            $cedtra = $request->input('cedtra');
            $id = $request->input('id');

            $mercurio36 = (new Mercurio36())->findFirst("cedtra='{$cedtra}' and id='{$id}'");

            if (!$mercurio36) {
                throw new DebugException("La empresa no está disponible para notificar por email", 501);
            } else {

                $salida = array(
                    "documentos_adjuntos" => $this->facultativoService->archivosRequeridos($mercurio36),
                    "success" => true
                );
            }
        } catch (DebugException $e) {
            $salida = array(
                "success" => false,
                "msj" => $e->getMessage()
            );
        }
        $this->renderText(json_encode($salida, JSON_NUMERIC_CHECK));
    }

    /**
     * borrar function
     * cancelar la solicitud
     * @return void
     */
    public function borrarAction(Request $request)
    {
        $this->setResponse("ajax");
        try {
            $user = $this->user;
            $documento = $user['documento'];
            $coddoc = $user['coddoc'];

            $id = $request->input('id');
            $m36 = (new Mercurio36())->findFirst("id='{$id}' AND documento='{$documento}' and coddoc='{$coddoc}'");
            if ($m36) {
                if ($m36->getEstado() != 'T') {
                    (new Mercurio10())->deleteAll("numero='{$id}' AND tipopc='{$this->tipopc}'");
                }
                (new Mercurio36())->deleteAll("id='{$id}'");
            }
            $salida = array(
                "success" => true,
                "msj" => "El registro se borro con éxito del sistema."
            );
        } catch (DebugException $e) {
            $salida = array(
                "success" => false,
                "msj" => $e->getMessage()
            );
        }
        return $this->renderObject($salida, false);
    }

    public function downloadFileAction($archivo = "")
    {
        $this->setResponse('view');
        $fichero = "public/temp/" . $archivo;
        return $this->renderFile($fichero);
    }

    /**
     * paramsAction function
     * @return void
     */
    public function paramsAction()
    {
        $this->setResponse("ajax");

        try {
            $mtipoDocumentos = new Gener18();
            $tipoDocumentos = array();

            foreach ($mtipoDocumentos->all() as $mtipo) {
                if ($mtipo->getCoddoc() == '7' || $mtipo->getCoddoc() == '2' || $mtipo->getCoddoc() == '3') continue;
                $tipoDocumentos["{$mtipo->getCoddoc()}"] = $mtipo->getDetdoc();
            }

            $msubsi54 = new Subsi54();
            $tipsoc = array();
            foreach ($msubsi54->all() as $entity) {
                $tipsoc["{$entity->getTipsoc()}"] = $entity->getDetalle();
            }

            $coddoc = array();
            foreach ($mtipoDocumentos->all() as $entity) {
                if ($entity->getCoddoc() == '7' || $entity->getCoddoc() == '2') continue;
                $coddoc["{$entity->getCoddoc()}"] = $entity->getDetdoc();
            }

            $coddocrepleg = array();
            foreach ($mtipoDocumentos->all() as $entity) {
                if ($entity->getCodrua() == 'TI' || $entity->getCodrua() == 'RC') continue;
                $coddocrepleg["{$entity->getCodrua()}"] = $entity->getDetdoc();
            }

            $codciu = array();
            $mgener09 = new Gener09();
            foreach ($mgener09->getFind("conditions: codzon >='18000' and codzon <= '19000'") as $entity) {
                $codciu["{$entity->getCodzon()}"] = $entity->getDetzon();
            }

            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
                array(
                    "servicio" => "ComfacaAfilia",
                    "metodo" => "parametros_empresa"
                ),
                false
            );
            $paramsFacultativo = new ParamsFacultativo();
            $paramsFacultativo->setDatosCaptura($procesadorComando->toArray());

            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
                array(
                    "servicio" => "ComfacaAfilia",
                    "metodo" => "parametros_trabajadores"
                ),
                false
            );
            $paramsTrabajador = new ParamsTrabajador();
            $paramsTrabajador->setDatosCaptura($procesadorComando->toArray());

            $mtipafi = ParamsTrabajador::getTipoAfiliado();
            $tipo_afiliados = array();
            foreach ($mtipafi as $key => $tipo) {
                if ($key == '63') {
                    $tipo_afiliados[$key] = $tipo;
                }
            }

            $coddoc = $tipoDocumentos;
            $data = array(
                'tipdoc' => $coddoc,
                'tipper' => (new Mercurio30())->getTipperArray(),
                'tipsoc' => $tipsoc,
                'calemp' => (new Mercurio30())->getCalempArray(),
                'codciu' => $codciu,
                'coddocrepleg' => $coddocrepleg,
                'codzon' => ParamsFacultativo::getZonas(),
                'codact' => ParamsFacultativo::getActividades(),
                'tipemp' => ParamsFacultativo::getTipoEmpresa(),
                'codcaj' => ParamsFacultativo::getCodigoCajas(),
                'ciupri' => ParamsFacultativo::getCiudades(),
                'sexo' => ParamsTrabajador::getSexos(),
                'estciv' => ParamsTrabajador::getEstadoCivil(),
                'cabhog' => ParamsTrabajador::getCabezaHogar(),
                'captra' => ParamsTrabajador::getCapacidadTrabajar(),
                'tipdis' => ParamsTrabajador::getTipoDiscapacidad(),
                'nivedu' => ParamsTrabajador::getNivelEducativo(),
                'rural' => ParamsTrabajador::getRural(),
                'tipcon' => ParamsTrabajador::getTipoContrato(),
                'trasin' => ParamsTrabajador::getSindicalizado(),
                'vivienda' => ParamsTrabajador::getVivienda(),
                'tipafi' => $tipo_afiliados,
                'cargo' => ParamsTrabajador::getOcupaciones(),
                'orisex' => ParamsTrabajador::getOrientacionSexual(),
                'facvul' => ParamsTrabajador::getVulnerabilidades(),
                'peretn' => ParamsTrabajador::getPertenenciaEtnicas(),
                'ciunac' => ParamsFacultativo::getCiudades(),
                'labora_otra_empresa' => ParamsTrabajador::getLaboraOtraEmpresa(),
                'tippag' => ParamsTrabajador::getTipoPago(),
                'resguardo_id' => ParamsTrabajador::getResguardos(),
                'pub_indigena_id' => ParamsTrabajador::getPueblosIndigenas(),
                'codban' => ParamsTrabajador::getBancos(),
                'tipsal' => (new Mercurio31())->getTipsalArray(),
                'tipcue' => ParamsTrabajador::getTipoCuenta(),
                'ruralt' => ParamsTrabajador::getRural(),
                "autoriza" => array("S" => "SI", "N" => "NO")
            );

            $salida = array(
                "success" => true,
                "data" => $data,
                "msj" => 'OK'
            );
        } catch (DebugException $e) {
            $salida = array(
                "success" => false,
                "msj" => $e->getMessage()
            );
        }
        return $this->renderObject($salida, false);
    }

    public function searchRequestAction($id)
    {
        $this->setResponse("ajax");
        try {
            if (is_null($id)) {
                throw new DebugException("Error no hay solicitud a buscar", 301);
            }
            $documento = parent::getActUser("documento");
            $coddoc = parent::getActUser("coddoc");

            $solicitud = (new Mercurio36())->findFirst(" id='{$id}' AND documento='{$documento}' AND coddoc='{$coddoc}'");
            if ($solicitud == False) {
                throw new DebugException("Error la solicitud no está disponible para acceder.", 301);
            } else {
                $data = $solicitud->getArray();
            }
            $salida = array(
                "success" => true,
                "data" => $data,
                "msj" => 'OK'
            );
        } catch (DebugException $e) {
            $salida = array(
                "success" => false,
                "msj" => $e->getMessage()
            );
        }
        return $this->renderObject($salida, false);
    }

    public function consultaDocumentosAction($id)
    {
        $this->setResponse("ajax");
        try {
            $documento = parent::getActUser("documento");
            $coddoc = parent::getActUser("coddoc");
            $facultativoService = new FacultativoService();

            $sindepe = (new Mercurio36())->findFirst("id='{$id}' AND documento='{$documento}' AND coddoc='{$coddoc}' AND estado NOT IN('I','X')");
            if ($sindepe == false) {
                throw new DebugException("Error no se puede identificar el propietario de la solicitud", 301);
            }
            $salida = array(
                'success' => true,
                'data' => $facultativoService->dataArchivosRequeridos($sindepe),
                'msj' => 'OK'
            );
        } catch (DebugException $e) {
            $salida = array(
                "success" => false,
                "msj" => $e->getMessage()
            );
        }
        return $this->renderObject($salida, false);
    }

    public function renderTableAction($estado = '')
    {
        $this->setResponse("view");
        $this->facultativoService = new FacultativoService();
        $html = view(
            "mercurio/facultativo/tmp/solicitudes",
            array(
                "path" => base_path(),
                "facultativos" => $this->facultativoService->findAllByEstado($estado)
            )
        )->render();
        return $this->renderText($html);
    }

    public function seguimientoAction($id)
    {
        $this->setResponse("ajax");
        try {
            $facultativoService = new FacultativoService();
            $out = $facultativoService->consultaSeguimiento($id);
            $salida = array(
                "success" => true,
                "data" => $out
            );
        } catch (DebugException $e) {
            $salida = array('success' => false, 'msj' => $e->getMessage());
        }
        return $this->renderObject($salida, false);
    }

    public function administrar_cuentaAction($id = '')
    {
        $this->setResponse("view");
        try {
            if ($id == '') {
                throw new AuthException("El id del la solicitud no está disponible.", 501);
            }

            $solicitud = (new Mercurio36())->findFirst("id='{$id}' and estado='A'");
            $request = new Request(
                array(
                    'tipo' => 'F',
                    'coddoc' => $solicitud->getTipdoc(),
                    'documento' => $solicitud->getCedtra(),
                    'usuario' => $solicitud->getPriape() . ' ' . $solicitud->getSegape() . ' ' . $solicitud->getPrinom() . ' ' . $solicitud->getSegnom()
                )
            );

            $change = new ChangeCuentaService();
            if ($change->initializa($request)) {
                set_flashdata("success", array(
                    "msj" => "La administración de la cuenta se ha inicializado con éxito.",
                    "code" => 200
                ));

                return redirect('principal.index');
            }
        } catch (AuthException $e) {
            set_flashdata("error", array(
                "msj" => $e->getMessage(),
                "code" => 505
            ));

            return redirect('empresa.index');
        }
    }
}
