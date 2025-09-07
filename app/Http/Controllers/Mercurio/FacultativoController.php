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
        return view("facultativo/index", [
            "title" => "Afiliación Facultativos",
            'tipo' => $this->tipo,
            'documento' => $this->user['documento'],
        ]);
    }

    public function actualizarAction(Request $request)
    {
        $this->setResponse("ajax");
        try {
            $id = $request->input('id', "addslaches", "alpha", "extraspaces", "striptags");
            $params = $this->serializeData($request);
            $params['id'] = $id;
            $params['tipo'] = parent::getActUser("tipo");
            $params['coddoc'] = parent::getActUser("coddoc");
            $params['documento'] = parent::getActUser("documento");
            $params['estado'] = 'T';

            $this->facultativoService = new FacultativoService;
            $this->asignarFuncionario = new AsignarFuncionario;
            $params['usuario'] = $this->asignarFuncionario->asignar($this->tipopc, parent::getActUser("codciu"));

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
        # $facultativoService->setTransa();

        try {
            $id = $request->input('id', "addslaches", "extraspaces", "striptags");
            $params = $this->serializeData($request);
            $params['tipo'] = parent::getActUser("tipo");
            $params['coddoc'] = parent::getActUser("coddoc");
            $params['documento'] = parent::getActUser("documento");
            $params['estado'] = 'T';

            $this->asignarFuncionario = new AsignarFuncionario();
            $params['usuario'] = $this->asignarFuncionario->asignar($this->tipopc, parent::getActUser("codciu"));

            if (is_null($id) || $id == '') {
                $params['id'] = null;
                $params['estado'] = 'T';
                $facultativo = $facultativoService->createByFormData($params);
                $soli = $facultativo->getArray();
                $id = $soli['id'];
            } else {
                $res = $facultativoService->updateByFormData($id, $params);
                if ($res == false) {
                    throw new DebugException("Error no se actualizo los datos", 301);
                }
            }

            # $facultativoService->endTransa();

            $facultativo = $facultativoService->findById($id);
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
        } catch (DebugException $e) {
            $response = array(
                'success' => false,
                'msj' => $e->getMessage()
            );
        }
        return $this->renderObject($response, false);
    }

    /**
     * serializeData function
     * @return array
     */
    function serializeData(Request $request)
    {
        return array(
            'coddocrepleg' => $request->input('coddocrepleg', "addslaches", "extraspaces", "striptags"),
            'cedtra' => $request->input('cedtra', "addslaches", "extraspaces", "striptags"),
            'rural' => $request->input('rural', "addslaches", "extraspaces", "striptags"),
            'vivienda' => $request->input('vivienda', "addslaches", "extraspaces", "striptags"),
            'tipafi' => $request->input('tipafi', "addslaches", "extraspaces", "striptags"),
            'fecini' => $request->input('fecini', "extraspaces"),
            'tippag' => $request->input('tippag', "addslaches", "extraspaces", "striptags"),
            'cargo' => $request->input('cargo', "addslaches", "extraspaces", "striptags"),
            'resguardo_id' => $request->input('resguardo_id', "addslaches", "extraspaces", "striptags"),
            'peretn' => $request->input('peretn', "addslaches", "extraspaces", "striptags"),
            'pub_indigena_id' => $request->input('pub_indigena_id', "addslaches", "extraspaces", "striptags"),
            'cedtra' => $request->input('cedtra', "addslaches", "extraspaces", "striptags"),
            'tipdoc' => $request->input('tipdoc', "addslaches", "alpha", "extraspaces", "striptags"),
            'priape' => $request->input('priape', "addslaches", "extraspaces", "striptags"),
            'segape' => $request->input('segape', "addslaches", "extraspaces", "striptags"),
            'prinom' => $request->input('prinom', "addslaches", "extraspaces", "striptags"),
            'segnom' => $request->input('segnom', "addslaches", "extraspaces", "striptags"),
            'fecnac' => $request->input('fecnac', "addslaches", "extraspaces", "striptags"),
            'ciunac' => $request->input('ciunac', "addslaches", "extraspaces", "striptags"),
            'sexo' => $request->input('sexo', "addslaches", "extraspaces", "striptags"),
            'estciv' => $request->input('estciv', "addslaches", "extraspaces", "striptags"),
            'cabhog' => $request->input('cabhog', "addslaches", "extraspaces", "striptags"),
            'codciu' => $request->input('codciu', "addslaches", "extraspaces", "striptags"),
            'codzon' => $request->input('codzon', "addslaches", "extraspaces", "striptags"),
            'direccion' => $request->input('direccion', "addslaches", "extraspaces", "striptags"),
            'barrio' => $request->input('barrio', "addslaches", "extraspaces", "striptags"),
            'telefono' => $request->input('telefono', "addslaches", "extraspaces", "striptags"),
            'celular' => $request->input('celular', "addslaches", "extraspaces", "striptags"),
            'email' => $request->input('email', "addslaches", "extraspaces", "striptags"),
            'salario' => $request->input('salario', "addslaches", "extraspaces", "striptags"),
            'captra' => $request->input('captra', "addslaches", "extraspaces", "striptags"),
            'tipdis' => $request->input('tipdis', "addslaches", "extraspaces", "striptags"),
            'nivedu' => $request->input('nivedu', "addslaches", "extraspaces", "striptags"),
            'vivienda' => $request->input('vivienda', "addslaches", "extraspaces", "striptags"),
            'autoriza' => $request->input('autoriza', "addslaches", "extraspaces", "striptags"),
            'codact' => $request->input('codact', "addslaches", "extraspaces", "striptags"),
            'codcaj' => $request->input('codcaj', "addslaches", "extraspaces", "striptags"),
            'facvul' => $request->input('facvul', "addslaches", "extraspaces", "striptags"),
            'orisex' => $request->input('orisex', "addslaches", "extraspaces", "striptags"),
            'codban' => $request->input('codban', "addslaches", "extraspaces", "striptags"),
            'calemp' => 'F',
            'tipcue' => $request->input('tipcue', "addslaches", "extraspaces", "striptags"),
            'numcue' => $request->input('numcue', "addslaches", "extraspaces", "striptags"),
            'cargo' => $request->input('cargo', "addslaches", "extraspaces", "striptags"),
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

            $cedtra = $request->input('cedtra', "addslaches", "alpha", "extraspaces", "striptags");
            $solicitud = (new Mercurio36())->findFirst(" documento='{$cedtra}' and estado IN('A','I')");

            $solicitudPrevia = false;
            if ($solicitud) {
                $solicitudPrevia = $solicitud->getArray();
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
            $usuario = $asignarFuncionario->asignar($this->tipopc, parent::getActUser("codciu"));

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
            "facultativo/tmp/solicitudes",
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
