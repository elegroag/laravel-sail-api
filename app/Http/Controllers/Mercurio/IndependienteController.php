<?php

namespace App\Http\Controllers\Mercurio;

use App\Exceptions\AuthException;
use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Library\Collections\ParamsEmpresa;
use App\Library\Collections\ParamsIndependiente;
use App\Library\Collections\ParamsTrabajador;
use App\Models\Adapter\DbBase;
use App\Models\Gener09;
use App\Models\Gener18;
use App\Models\Mercurio01;
use App\Models\Mercurio10;
use App\Models\Mercurio30;
use App\Models\Mercurio31;
use App\Models\Mercurio37;
use App\Models\Mercurio41;
use App\Models\Subsi54;
use App\Services\Entidades\IndependienteService;
use App\Services\FormulariosAdjuntos\Formularios;
use App\Services\FormulariosAdjuntos\IndependienteAdjuntoService;
use App\Services\Utils\AsignarFuncionario;
use App\Services\Utils\ChangeCuentaService;
use App\Services\Utils\Comman;
use App\Services\Utils\GeneralService;
use App\Services\Utils\GuardarArchivoService;
use App\Services\Utils\SenderValidationCaja;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class IndependienteController extends ApplicationController
{
    /**
     * independienteService variable
     * @var IndependienteService
     */
    protected $independienteService;

    /**
     * asignarFuncionario variable
     * @var AsignarFuncionario
     */
    protected $asignarFuncionario;
    protected $tipopc = "13";
    protected $db;
    protected $user;
    protected $tipo;

    public function __construct()
    {
        $this->db = DbBase::rawConnect();
        $this->user = session()->has('user') ? session('user') : null;
        $this->tipo = session()->has('tipo') ? session('tipo') : null;
    }

    /**
     * indexAction function
     * @param string $estado
     * @return void
     */
    public function indexAction()
    {
        return view('mercurio/independiente/index', [
            "title" => "Afiliación Independientes",
            "calemp" => "I",
            "tipper" => "N",
            "cedtra" => parent::getActUser("documento"),
            "coddoc" => parent::getActUser("coddoc")
        ]);
    }

    public function renderTableAction(Request $request, Response $response, string $estado = '')
    {
        $this->setResponse("view");

        $independienteService = new IndependienteService();
        $html = view(
            "mercurio/independiente/tmp/solicitudes",
            array(
                "path" => base_path(),
                "empresas" => $independienteService->findAllByEstado($estado)
            )
        )->render();

        return $this->renderText($html);
    }

    /**
     * showAction function
     * @param [type] $id
     * @param [type] $documento
     * @return void
     */
    public function showAction(Request $request, Response $response, int $id, int $documento)
    {
        $this->setResponse("ajax");

        $mercurio41 = Mercurio41::where("id", $id)->where("documento", $documento)->first();

        if ($mercurio41 == false) $mercurio41 = new Mercurio41();

        return $this->renderObject(
            [
                "success" => true,
                "entity" => $mercurio41->toArray()
            ]
        );
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
        $this->db->begin();

        $independienteService = new IndependienteService();
        try {

            $id = $request->input('id');
            $params = $this->serializeData($request);

            if (is_null($id) || $id == '') {
                $independiente = $independienteService->createByFormData($params);
            } else {
                $ok = $independienteService->updateByFormData($id, $params);
                if ($ok === false) throw new DebugException('No se pudo actualizar la solicitud', 500);
                $independiente = $independienteService->findById($id);
            }

            $independienteService->paramsApi();

            $independienteAdjuntoService = new IndependienteAdjuntoService($independiente);

            $out = $independienteAdjuntoService->formulario()->getResult();
            (new GuardarArchivoService(
                array(
                    'tipopc' => $this->tipopc,
                    'coddoc' => 1,
                    'id' => $independiente->getId()
                )
            ))->salvarDatos($out);

            $out = $independienteAdjuntoService->tratamientoDatos()->getResult();
            (new GuardarArchivoService(
                array(
                    'tipopc' => $this->tipopc,
                    'coddoc' => 25,
                    'id' => $independiente->getId()
                )
            ))->salvarDatos($out);


            $out = $independienteAdjuntoService->cartaSolicitud()->getResult();
            (new GuardarArchivoService(
                array(
                    'tipopc' => $this->tipopc,
                    'coddoc' => 24,
                    'id' => $independiente->getId()
                )
            ))->salvarDatos($out);

            ob_end_clean();

            $response = [
                'success' => true,
                'msj' => 'Registro completado con éxito',
                'data' => $independiente->toArray()
            ];
            $this->db->commit();
        } catch (DebugException $e) {
            $this->db->rollBack();
            $response = [
                'success' => false,
                'msj' => $e->getMessage()
            ];
        }
        return $this->renderObject($response);
    }


    public function actualizarAction(Request $request)
    {
        $this->setResponse("ajax");
        try {
            $id = $request->input('id');
            $params = $this->serializeData($request);
            $params['id'] = $id;
            $params['tipo'] = parent::getActUser("tipo");
            $params['coddoc'] = parent::getActUser("coddoc");
            $params['documento'] = parent::getActUser("documento");
            $params['estado'] = 'T';

            $this->independienteService = new IndependienteService();
            $this->asignarFuncionario = new AsignarFuncionario();
            $params['usuario'] = $this->asignarFuncionario->asignar($this->tipopc, parent::getActUser("codciu"));
            $this->independienteService->updateByFormData($id, $params);
            //$this->independienteService->endTransa();

            $independiente = $this->independienteService->findById($id);
            $data = $independiente->getArray();

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
        $fecsol = Carbon::now();
        return [
            'tipo' => $this->tipo,
            'fecsol' => $fecsol->format('Y-m-d'),
            'cedtra' => $request->input('cedtra', ''),
            'tipdoc' => $request->input('tipdoc', ''),
            'priape' => $request->input('priape', ''),
            'segape' => $request->input('segape', ''),
            'prinom' => $request->input('prinom', ''),
            'segnom' => $request->input('segnom', ''),
            'fecnac' => $request->input('fecnac', ''),
            'ciunac' => $request->input('ciunac', ''),
            'sexo' => $request->input('sexo', ''),
            'orisex' => $request->input('orisex', ''),
            'estciv' => $request->input('estciv', ''),
            'cabhog' => $request->input('cabhog', ''),
            'codciu' => $request->input('codciu', ''),
            'codzon' => $request->input('codzon', ''),
            'direccion' => $request->input('direccion', ''),
            'barrio' => $request->input('barrio', ''),
            'telefono' => $request->input('telefono', ''),
            'celular' => $request->input('celular', ''),
            'email' => $request->input('email', ''),
            'fecini' => $request->input('fecini', ''),
            'salario' => $request->input('salario', ''),
            'captra' => $request->input('captra', ''),
            'tipdis' => $request->input('tipdis', ''),
            'nivedu' => $request->input('nivedu', ''),
            'rural' => $request->input('rural', ''),
            'vivienda' => $request->input('vivienda', ''),
            'tipafi' => $request->input('tipafi', ''),
            'autoriza' => $request->input('autoriza', ''),
            'calemp' => 'I',
            'codact' => $request->input('codact', ''),
            'tippag' => $request->input('tippag', ''),
            'cargo' => $request->input('cargo', ''),
            'tipcue' => $request->input('tipcue', ''),
            'numcue' => $request->input('numcue', ''),
            'resguardo_id' => $request->input('resguardo_id', ''),
            'peretn' => $request->input('peretn', ''),
            'pub_indigena_id' => $request->input('pub_indigena_id', ''),
            'codban' => $request->input('codban', ''),
            'codcaj' => $request->input('codcaj', ''),
            'coddocrepleg' => $request->input('coddocrepleg', ''),
            'facvul' => $request->input('facvul', ''),
        ];
    }

    /**
     * validaPkAction function
     * @return void
     */
    public function validaPkAction(Request $request)
    {
        $this->setResponse("ajax");
        try {
            $cedtra = $request->input('cedtra');
            $solicitud = Mercurio41::where('documento', $cedtra)
                ->whereIn('estado', ['A', 'I'])
                ->first();

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

            $out = $procesadorComando->toArray();
            $empresa = (count($out['data']) > 0) ? $out['data'] : false;

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
            $trabajador = (count($out['data']) > 0) ? $out['data'] : false;

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
            $numero = $request->input('id');
            $coddoc = $request->input('coddoc');

            $mercurio01 = Mercurio01::first();
            $mercurio37 = Mercurio37::where('tipopc', $this->tipopc)
                ->where('numero', $numero)
                ->where('coddoc', $coddoc)
                ->first();

            $filepath = base_path() . '' . $mercurio01->getPath() . $mercurio37->getArchivo();
            if (file_exists($filepath)) {
                unlink(base_path() . '' . $mercurio01->getPath() . $mercurio37->getArchivo());
            }

            Mercurio37::where('tipopc', $this->tipopc)
                ->where('numero', $numero)
                ->where('coddoc', $coddoc)
                ->delete();

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

    /**
     * guardarArchivoAction function
     * @return void
     */
    public function guardarArchivoAction(Request $request)
    {
        $this->setResponse("ajax");
        try {
            $id = $request->input('id');
            $coddoc = $request->input('coddoc');

            $guardarArchivoService = new GuardarArchivoService(array(
                'tipopc' => $this->tipopc,
                'coddoc' => $coddoc,
                'id' => $id
            ));

            $mercurio37 = $guardarArchivoService->main();

            $response = array(
                'success' => true,
                'msj' => 'Ok archivo procesado',
                'data' => $mercurio37->getArray()
            );
        } catch (DebugException $ert) {
            $response = array(
                'success' => false,
                'msj' => $ert->getMessage()
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
            $id = $request->input('id', true, true, true, true);
            $independienteService = new IndependienteService();
            //$independienteService->setTransa();

            $asignarFuncionario = new AsignarFuncionario();
            $usuario = $asignarFuncionario->asignar($this->tipopc, parent::getActUser("codciu"));

            $independienteService->enviarCaja(new SenderValidationCaja(), $id, $usuario);
            //$independienteService->endTransa();

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

    /**
     * formularioAction function
     * @param [type] $id
     * @return void
     */
    public function formularioAction($id)
    {
        $this->setResponse("ajax");
        try {

            $mercurio41 = Mercurio41::where('id', $id)->first();
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
            $file = "formulario_{$mercurio41->getCedtra()}_{$time}.pdf";
            $formularios = new Formularios();
            $formularios->independientesAfiliacion(
                array(
                    'empresa' => $mercurio41
                ),
                $file
            );
            $salida = array(
                "success" => true,
                "name" => $file,
                "url" => 'independinte/downloadFile/' . $file
            );
        } catch (DebugException $e) {
            $salida = array(
                'success' => false,
                'msj' => $e->getMessage()
            );
        }
        return $this->renderObject($salida);
    }

    public function seguimientoAction(Request $request, Response $response, int $id)
    {
        $this->setResponse("ajax");
        try {
            $independienteService = new IndependienteService();
            $out = $independienteService->consultaSeguimiento($id);
            $salida = [
                "success" => true,
                "data" => $out
            ];
        } catch (DebugException $e) {
            $salida = ['success' => false, 'msj' => $e->getMessage()];
        }
        return $this->renderObject($salida, false);
    }

    public function downloadFileAction($archivo = "")
    {
        $this->setResponse('view');
        $fichero = "public/temp/" . $archivo;
        return $this->renderFile($fichero);
    }

    public function reloadArchivosAction(Request $request)
    {
        $this->setResponse("ajax");
        $this->independienteService = new IndependienteService();
        try {
            $cedtra = $request->input('cedtra');
            $id = $request->input('id');

            $mercurio41 = Mercurio41::where('cedtra', $cedtra)
                ->where('id', $id)
                ->first();

            if (!$mercurio41) {
                throw new DebugException("La empresa no está disponible para notificar por email", 501);
            } else {
                $salida = array(
                    "documentos_adjuntos" => $this->independienteService->archivosRequeridos($mercurio41),
                    "success" => true
                );
            }
        } catch (DebugException $e) {
            $salida = array(
                "success" => false,
                "msj" => $e->getMessage()
            );
        }
        return $this->renderObject($salida, false);
    }

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

            $independienteService = new IndependienteService();
            $independienteService->paramsApi();

            $mtipafi = ParamsTrabajador::getTipoAfiliado();
            $tipo_afiliados = array();
            foreach ($mtipafi as $key => $tipo) {
                if ($key == '3' || $key == '65' || $key == '68') {
                    $tipo_afiliados[$key] = $tipo;
                }
            }

            $coddoc = $tipoDocumentos;
            $data = array(
                'tipdoc' => $coddoc,
                'tipper' => (new Mercurio30)->getTipperArray(),
                'tipsoc' => $tipsoc,
                'calemp' => (new Mercurio30)->getCalempArray(),
                'codciu' => $codciu,
                'coddocrepleg' => $coddocrepleg,
                'codzon' => ParamsIndependiente::getZonas(),
                'codact' => ParamsIndependiente::getActividades(),
                'tipemp' => ParamsIndependiente::getTipoEmpresa(),
                'codcaj' => ParamsIndependiente::getCodigoCajas(),
                'ciupri' => ParamsIndependiente::getCiudades(),
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
                'ciunac' => ParamsIndependiente::getCiudades(),
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

    public function searchRequestAction(Request $request, Response $response, int $id)
    {
        $this->setResponse("ajax");
        try {
            if (is_null($id)) {
                throw new DebugException("Error no hay solicitud a buscar", 301);
            }
            $documento = $this->user["documento"];
            $coddoc = $this->user["coddoc"];

            $solicitud = Mercurio41::where("id", $id)
                ->where("documento", $documento)
                ->where("coddoc", $coddoc)->first();

            if ($solicitud == False) {
                throw new DebugException("Error la solicitud no está disponible para acceder.", 301);
            } else {
                $data = $solicitud->toArray();
            }

            $salida = [
                "success" => true,
                "data" => $data,
                "msj" => 'OK'
            ];
        } catch (DebugException $e) {
            $salida = [
                "success" => false,
                "msj" => $e->getMessage()
            ];
        }
        return $this->renderObject($salida);
    }

    public function consultaDocumentosAction(Request $request, Response $response, int $id)
    {
        $this->setResponse('ajax');
        try {

            $documento = $this->user['documento'] ?? '';
            $coddoc = $this->user['coddoc'] ?? '';
            $service = new IndependienteService();

            $sindepe = Mercurio41::where('id', $id)
                ->where('documento', $documento)
                ->where('coddoc', $coddoc)
                ->whereNotIn('estado', ['I', 'X'])
                ->first();

            if ($sindepe == false) throw new DebugException('Error no se puede identificar el propietario de la solicitud', 404);

            $salida = [
                'success' => true,
                'data' => $service->dataArchivosRequeridos($sindepe),
                'msj' => 'OK'
            ];
        } catch (DebugException $e) {
            $salida = [
                'success' => false,
                'msj' => $e->getMessage()
            ];
        }
        return $this->renderObject($salida);
    }

    public function cartaSolicitudAction($archivo = "")
    {
        $this->setResponse('view');
        $fichero = "public/docs/formulario_mercurio/" . $archivo;
        return $this->renderFile($fichero);
    }

    public function tratamientoDatosAction($archivo = "")
    {
        $this->setResponse('view');
        $fichero = "public/docs/formulario_mercurio/" . $archivo;
        return $this->renderFile($fichero);
    }

    public function borrarAction(Request $request)
    {
        $this->setResponse("ajax");
        $generales = new GeneralService();
        //$generales->startTrans('mercurio41');
        try {

            $documento = $this->user['documento'];
            $coddoc = $this->user['coddoc'];

            $id = $request->input('id');

            $m41 = (new Mercurio41())->findFirst("id='{$id}' and documento='{$documento}' and coddoc='{$coddoc}'");
            if ($m41) {
                if ($m41->getEstado() != 'T') (new Mercurio10())->deleteAll("numero='{$id}' AND tipopc='{$this->tipopc}'");
            }
            (new Mercurio41())->deleteAll("id='{$id}' and documento='{$documento}' and coddoc='{$coddoc}'");
            $generales->finishTrans();
            $response = array(
                'success' => true,
                'msj' => 'Ok'
            );
        } catch (DebugException $e) {
            $response = array(
                'success' => false,
                'msj' => $e->getMessage()
            );
        }
        return $this->renderObject($response, false);
    }

    public function administrar_cuentaAction($id = '')
    {
        $this->setResponse("view");
        try {
            if ($id == '') {
                throw new AuthException("El id del la solicitud no está disponible.", 501);
            }

            $solicitud = (new Mercurio41())->findFirst("id='{$id}' and estado='A'");
            $request = new Request(
                array(
                    'tipo' => 'I',
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
                redirect('principal.index');
            }
        } catch (AuthException $e) {
            set_flashdata("error", array(
                "msj" => $e->getMessage(),
                "code" => 505
            ));
            redirect('empresa.index');
            exit;
        }
    }
}
