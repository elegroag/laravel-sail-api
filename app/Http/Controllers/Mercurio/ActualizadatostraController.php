<?
namespace App\Http\Controllers\Mercurio;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Library\Auth\AuthJwt;
use App\Library\Auth\SessionCookies;
use App\Library\Collections\ParamsTrabajador;
use App\Models\Adapter\DbBase;
use App\Models\Gener09;
use App\Models\Gener18;
use App\Models\Mercurio33;
use App\Models\Mercurio47;
use App\Models\Subsi54;
use App\Services\Entidades\DatosTrabajadorService;
use App\Services\Utils\AsignarFuncionario;
use App\Services\Utils\Comman;
use App\Services\Utils\GeneralService;
use App\Services\Utils\GuardarArchivoService;
use App\Services\Utils\SenderValidationCaja;
use Illuminate\Http\Request;
use TCPDF;

class ActualizadatostraController extends ApplicationController
{
    protected $tipopc = 14;
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
        return view('actualizadatostra.index', [
            'title' => 'Solicitud de actualización de datos',
            'cedtra' => $this->user
        ]);
    }

    public function paramsAction()
    {
        $this->setResponse("ajax");
        try {
            $nit = parent::getActUser("documento");

            $mtipoDocumentos = new Gener18();
            $tipoDocumentos = array();

            foreach ($mtipoDocumentos->find() as $mtipo) {
                if ($mtipo->getCoddoc() == '7' || $mtipo->getCoddoc() == '2') continue;
                $tipoDocumentos["{$mtipo->getCoddoc()}"] = $mtipo->getDetdoc();
            }

            $msubsi54 = new Subsi54();
            $tipsoc = array();
            foreach ($msubsi54->find() as $entity) {
                $tipsoc["{$entity->getTipsoc()}"] = $entity->getDetalle();
            }

            $coddoc = array();
            foreach ($mtipoDocumentos->find() as $entity) {
                if ($entity->getCoddoc() == '7' || $entity->getCoddoc() == '2') continue;
                $coddoc["{$entity->getCoddoc()}"] = $entity->getDetdoc();
            }

            $coddocrepleg = array();
            foreach ($mtipoDocumentos->find() as $entity) {
                if ($entity->getCodrua() == 'TI' || $entity->getCodrua() == 'RC') continue;
                $coddocrepleg["{$entity->getCodrua()}"] = $entity->getDetdoc();
            }

            $codciu = array();
            $mgener09 = new Gener09();
            foreach ($mgener09->find("*", "conditions: codzon >='18000' and codzon <= '19000'") as $entity) {
                $codciu["{$entity->getCodzon()}"] = $entity->getDetzon();
            }

            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
                array(
                    "servicio" => "ComfacaAfilia",
                    "metodo" => "parametros_trabajadores"
                )
            );
            $paramsTrabajador = new ParamsTrabajador();
            $paramsTrabajador->setDatosCaptura($procesadorComando->toArray());

            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
                array(
                    "servicio" => "ComfacaEmpresas",
                    "metodo" => "buscar_sucursales_en_empresa/{$nit}",
                    "params" => ''
                )
            );
            $rqs = $procesadorComando->toArray();

            $codsuc = array();
            $sucursales = $rqs['data'];
            if ($sucursales) {
                foreach ($sucursales as $data) {
                    if ($data['estado'] == 'I') continue;
                    if (isset($codciu[$data['codzon']])) {
                        $codsuc["{$data['codsuc']}"] = $data['detalle'] . ' - DE ' . $codciu[$data['codzon']];
                    } else {
                        $codsuc["{$data['codsuc']}"] = $data['detalle'];
                    }
                }
            }

            $tipafi = $this->Mercurio07->getArrayTipos();
            $coddoc = $tipoDocumentos;
            $data = array(
                'tipafi' => $tipafi,
                'tipdoc' => $coddoc,
                'tipper' => $this->Mercurio30->getTipperArray(),
                'tipsoc' => $tipsoc,
                'calemp' => $this->Mercurio30->getCalempArray(),
                'codciu' => $codciu,
                'codzon' => $codciu,
                'respo_tipdoc' => $coddoc,
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
                'tipafi' => ParamsTrabajador::getTipoAfiliado(),
                'cargo' => ParamsTrabajador::getOcupaciones(),
                'orisex' => ParamsTrabajador::getOrientacionSexual(),
                'facvul' => ParamsTrabajador::getVulnerabilidades(),
                'peretn' => ParamsTrabajador::getPertenenciaEtnicas(),
                'ciunac' => ParamsTrabajador::getCiudades(),
                'labora_otra_empresa' => ParamsTrabajador::getLaboraOtraEmpresa(),
                'tippag' => ParamsTrabajador::getTipoPago(),
                'resguardo_id' => ParamsTrabajador::getResguardos(),
                'pub_indigena_id' => ParamsTrabajador::getPueblosIndigenas(),
                'codban' => ParamsTrabajador::getBancos(),
                'tipsal' =>  $this->Mercurio31->getTipsalArray(),
                'tipcue' => ParamsTrabajador::getTipoCuenta(),
                'ruralt' => ParamsTrabajador::getRural(),
                'tipjor' => array("C" => "COMPLETA", "M" => "MEDIA", "P" => "PARCIAL"),
                "autoriza" => array("S" => "SI", "N" => "NO"),
                "comision" => array("S" => "SI", "N" => "NO"),
                'labora_otra_empresa' => array("S" => "SI", "N" => "NO"),
                'codsuc' => $codsuc
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

    function buscarSolicitudes($estado = '')
    {
        $documento =  parent::getActUser("documento");
        if (empty($estado)) {
            $mercurio47 = $this->db->fetchAll("SELECT * FROM mercurio47 WHERE documento='{$documento}' AND estado IN('T','D','P','A','X') ORDER BY id, estado DESC");
        } else {
            $mercurio47 = $this->db->fetchAll("SELECT * FROM mercurio47 WHERE documento='{$documento}' AND estado='{$estado}' ORDER BY id DESC");
        }

        foreach ($mercurio47 as $ai => $row) {
            $rqs = $this->db->fetchOne("SELECT count(mercurio10.numero) as cantidad 
                FROM mercurio10 
                LEFT JOIN mercurio47 ON mercurio47.id = mercurio10.numero
                WHERE mercurio10.tipopc='{$this->tipopc}' AND 
                mercurio47.id ='{$row['id']}' 
            ");

            $trayecto = $this->db->fetchOne("SELECT max(mercurio10.item), mercurio10.*  
                FROM mercurio10 
                LEFT JOIN mercurio47 ON mercurio47.id=mercurio10.numero
                WHERE mercurio10.tipopc='{$this->tipopc}' AND 
                mercurio47.id ='{$row['id']}' LIMIT 1 
            ");

            $mercurio47[$ai] = $row;
            $mercurio47[$ai]["cantidad_eventos"] = $rqs['cantidad'];
            $mercurio47[$ai]["fecha_ultima_solicitud"] = $trayecto['fecsis'];
            $mercurio47[$ai]["estado_detalle"] = $this->Mercurio47->getEstadoInArray($row['estado']);
            $mercurio47[$ai]["tipo_actualizacion_detalle"] = $this->Mercurio47->getTipoActualizacionInArray($row['tipo_actualizacion']);
        }
        return $mercurio47;
    }

    function buscarTrabajadorSubsidio($cedtra)
    {
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
        $salida =  $procesadorComando->toArray();
        return ($salida['success'] == true) ? $salida['data'] : false;
    }

    public function inforAction()
    {
        $this->setResponse("ajax");
        try {
            $documento = parent::getActUser("documento");
            $datos = $this->buscarTrabajadorSubsidio($documento);
            $salida = array(
                "success" => true,
                "data" => $datos,
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

    function loadDisplaySubsidio($trabajador)
    {
       /*  Tag::displayTo("nit", $trabajador['nit']);
        Tag::displayTo("cedtra", $trabajador['cedtra']);
        Tag::displayTo("telefono", $trabajador['telefono']);
        Tag::displayTo("email", $trabajador['email']);
        Tag::displayTo("codzon", $trabajador['codzon']);
        Tag::displayTo("prinom", $trabajador['prinom']);
        Tag::displayTo("segnom", $trabajador['segnom']);
        Tag::displayTo("priape", $trabajador['priape']);
        Tag::displayTo("segape", $trabajador['segape']);
        Tag::displayTo("direccion", $trabajador['direccion']);
        Tag::displayTo("codciu", $trabajador['codciu']); */
    }

    public function loadDisplay($mercurio33)
    {
        foreach ($mercurio33 as $mercurio) {
            $row = $mercurio->getArray();
            $campo = $row['campo'];
            //Tag::displayTo("{$campo}", $row['valor']);
        }
    }

    function buscarEmpresaSubsidio($nit)
    {
        $procesadorComando = Comman::Api();
        $procesadorComando->runCli(
            array(
                "servicio" => "ComfacaEmpresas",
                "metodo" => "informacion_empresa",
                "params" => array("nit" => $nit)
            )
        );
        $salida =  $procesadorComando->toArray();
        if ($salida['success']) {
            return $salida;
        } else {
            return false;
        }
    }

    /**
     * guardar function
     * @changed [2024-12-00]
     * @author elegroag <elegroag@ibero.edu.co>
     * @return void
     */
    public function guardarAction(Request $request)
    {
        $this->setResponse("ajax");
        $datosTrabajadorService = new DatosTrabajadorService();
        //$datosTrabajadorService->setTransa();

        try {
                $asignarFuncionario = new AsignarFuncionario();
                $id = $request->input('id', "addslaches", "alpha", "extraspaces", "striptags");
                $tipo_actualizacion = $request->input('tipo_actualizacion');
                $usuario = $asignarFuncionario->asignar($this->tipopc, parent::getActUser("codciu"));
                $params = array(
                    'documento' => parent::getActUser("documento"),
                    'usuario' => $usuario,
                    'tipo' =>  parent::getActUser("tipo"),
                    'coddoc' =>  parent::getActUser("coddoc"),
                    'fecha_solicitud' =>  date('Y-m-d'),
                    'fecha_estado' =>  date('Y-m-d'),
                    'tipo_actualizacion' =>  $tipo_actualizacion
                );

                $solicitud = null;
                if (is_null($id) || $id == "") {
                    $data['id'] = null;
                    $data['usuario'] = 2;
                    $data['estado'] = 'T';
                    $solicitud = $datosTrabajadorService->createByFormData($params);
                    $id = $solicitud->getId();
                } else {
                    $res = $datosTrabajadorService->updateByFormData($id, $params);
                    if ($res == false) {
                        throw new DebugException("Error no se actualizo los datos", 301);
                    }
                }

                $campos = $this->db->inQueryAssoc("SELECT * FROM mercurio28 WHERE tipo='" . parent::getActUser("tipo") . "'");
                foreach ($campos as $mercurio28) {
                    $valor = $request->input($mercurio28['campo']);
                    $mercurio33 = new Mercurio33();
                    $mercurio33->id = 0;
                    $mercurio33->log = $id;
                    $mercurio33->tipo = parent::getActUser("tipo");
                    $mercurio33->coddoc = parent::getActUser("coddoc");
                    $mercurio33->documento = parent::getActUser("documento");
                    $mercurio33->campo = $mercurio28['campo'];
                    $mercurio33->antval = $valor;
                    $mercurio33->valor = $valor;
                    $mercurio33->estado = "P";
                    $mercurio33->usuario = $usuario;
                    $mercurio33->actualizacion = $id;
                    $mercurio33->save();
                }

                //$datosTrabajadorService->endTransa();
                $solicitud = $datosTrabajadorService->findById($id);

                ob_end_clean();
                $response = array(
                    "msj" => "Proceso se ha completado con éxito",
                    "success" => true,
                    "data" => $solicitud->getArray()
                );
          
        } catch (DebugException $e) {
            $response = array(
                'success' => false,
                'msj' => $e->getMessage()
            );
        }
        return $this->renderObject($response, false);
    }

    public function borrarAction(Request $request)
    {
        $this->setResponse("ajax");
        try {
            $id = $request->input('id');
            $solicitud = $this->Mercurio47->findFirst("id='{$id}'");
            if ($solicitud) {
                if ($solicitud->getEstado() != 'T') {
                    $this->Mercurio10->deleteAll("numero='{$id}' AND tipopc='{$this->tipopc}'");
                }
                $this->Mercurio33->deleteAll("actualizacion='{$id}'");
                $this->Mercurio47->deleteAll("id='{$id}'");
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
        $this->renderText(json_encode($salida, JSON_NUMERIC_CHECK));
    }

    public function editar_solicitudAction(Request $request)
    {
        $this->setResponse("ajax");
        $modelos = array("mercurio33", "mercurio47");
        //$Transaccion = parent::startTrans($modelos);
        $generalService = new GeneralService();
        $id_log = $generalService->registrarLog(false, "actualización datos basicos", "");
        try {
                $id = $request->input('id');
                $coddoc = parent::getActUser("coddoc");
                $documento = parent::getActUser("documento");
                $tipo = parent::getActUser("tipo");
                $solicitud = $this->db->fetchOne("SELECT * FROM mercurio47 WHERE id='{$id}' and documento='{$documento}'");
                if (!$solicitud) {
                    throw new DebugException("Error la solicitud no es correcta para continuar.", 501);
                }

                $campos = $this->db->fetchAll("SELECT * FROM mercurio28 WHERE tipo='{$tipo}'");
                foreach ($campos as $mercurio28) {
                    $valor = $request->input($mercurio28['campo']);
                    if (empty($valor)) continue;

                    $mercurio33 = $this->db->fetchOne("SELECT * FROM mercurio33 WHERE documento = '{$documento}' and actualizacion = '{$id}' and campo = '{$mercurio28['campo']}'");
                    if ($mercurio33) {

                        Mercurio33::where("id", $mercurio33['id'])
                            ->where("documento", $documento)
                            ->update(['valor'=> $valor]);
                            
                    } else {
                        $mercurio33 = new Mercurio33();
                        $mercurio33->setId(0);
                        $mercurio33->setLog($id_log);
                        $mercurio33->setTipo($tipo);
                        $mercurio33->setCoddoc($coddoc);
                        $mercurio33->setDocumento($documento);
                        $mercurio33->setCampo($mercurio28['campo']);
                        $mercurio33->setMotivo('');
                        $mercurio33->setFecest(null);
                        $mercurio33->setAntval($valor);
                        $mercurio33->setValor($valor);
                        $mercurio33->setEstado("P");
                        $mercurio33->setUsuario($solicitud['usuario']);
                        $mercurio33->setActualizacion($id);
                        $mercurio33->save();
                    }
                }
                //parent::finishTrans();
                
                $salida = array(
                    "msj" => "Proceso se ha completado con éxito",
                    "success" => true,
                    "id" => $id
                );

        } catch (DebugException $er) {
            $salida = array(
                "success" => false,
                "msj" => $er->getMessage()
            );
        }
        $this->renderText(json_encode($salida));
    }

    function archivos_requeridos($mercurio47)
    {
        $archivos = array();
        $mercurio13 = $this->Mercurio13->find("tipopc='{$this->tipopc}'");
        

        $mercurio10 = $this->db->fetchOne("SELECT item, estado, campos_corregir 
        FROM mercurio10 
        WHERE numero='{$mercurio47->getId()}' AND tipopc='{$this->tipopc}' ORDER BY item DESC LIMIT 1");

        $corregir = false;
        if ($mercurio10) {
            if ($mercurio10['estado'] == 'D') {
                $campos = $mercurio10['campos_corregir'];
                $corregir = explode(";", $campos);
            }
        }
        foreach ($mercurio13 as $m13) {
            $m12 = $this->Mercurio12->findFirst("coddoc='{$m13->getCoddoc()}'");
            $mercurio37 = $this->Mercurio37->findFirst("tipopc='{$this->tipopc}' and numero='{$mercurio47->getId()}' and coddoc='{$m13->getCoddoc()}'");
            $corrige = false;
            if ($corregir) {
                if (in_array($m12->getCoddoc(), $corregir)) {
                    $corrige = true;
                }
            }
            $obliga = ($m13->getObliga() == "S") ? "<br><small class='text-danger'>Obligatorio</small>" : "";
            $archivo = new \stdClass;
            $archivo->obliga = $obliga;
            $archivo->id = $mercurio47->getId();
            $archivo->coddoc = $m13->getCoddoc();
            $archivo->detalle = $m12->getDetalle();
            $archivo->diponible = ($mercurio37) ? $mercurio37->getArchivo() : false;
            $archivo->corrige = $corrige;
            $archivos[] = $archivo;
        }
        $mercurio01 = $this->Mercurio01->findFirst();
        $html = view("actualizadatos/tmp/archivos_requeridos", array(
            "load_archivos" => $archivos,
            "path" => $mercurio01->getPath(),
            "puede_borrar" => ($mercurio47->getEstado() == 'P' || $mercurio47->getEstado() == 'A') ? false : true,
            "mercurio13" => $mercurio13
        ))->render();

        return $html;
    }

    public function reload_archivosAction(Request $request)
    {
        $this->setResponse("ajax");
        try {
            $documento = parent::getActUser("documento");
            $id = $request->input('id');
            $mercurio47 = $this->Mercurio47->findFirst(" id='{$id}' and documento='{$documento}'");
            if (!$mercurio47) {
                throw new DebugException("No se requiere de ninguna acción", 501);
            } else {
                $salida = array(
                    "documentos_adjuntos" => $this->archivos_requeridos($mercurio47),
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

    public function borrarArchivoAction(Request $request)
    {
        try {
            $this->setResponse("ajax");
            $numero = $request->input('numero', "addslaches", "alpha", "extraspaces", "striptags");
            $coddoc = $request->input('coddoc', "addslaches", "alpha", "extraspaces", "striptags");
            //$response = parent::startFunc();
            $mercurio01 = $this->Mercurio01->findFirst();
            $mercurio37 = $this->Mercurio37->findFirst("tipopc='{$this->tipopc}' and numero='{$numero}' and coddoc='{$coddoc}'");
            if (file_exists($mercurio01->getPath() . $mercurio37->getArchivo())) {
                unlink($mercurio01->getPath() . $mercurio37->getArchivo());
            }
            $this->Mercurio37->deleteAll("tipopc='{$this->tipopc}' and numero='{$numero}' and coddoc='{$coddoc}'");

            $response = "Se borro con éxito el archivo";

        } catch (DebugException $err) {
            $response = "No se puede realizar la opcion";
        }
        return $this->renderText(json_encode($response));
    }

    /**
     * guardarArchivoAction function
     *
     * @return void
     */
    public function guardarArchivoAction(Request $request)
    {
        $this->setResponse("ajax");
        try {
            $id = $request->input('id', "addslaches", "alpha", "extraspaces", "striptags");
            $coddoc = $request->input('coddoc', "addslaches", "alpha", "extraspaces", "striptags");

            $guardarArchivoService = new GuardarArchivoService(
                array(
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
            $datosService = new DatosTrabajadorService();
            //$datosService->setTransa();

            $asignarFuncionario = new AsignarFuncionario();
            $usuario = $asignarFuncionario->asignar($this->tipopc, parent::getActUser("codciu"));
            $datosService->enviarCaja(new SenderValidationCaja(), $id, $usuario);

            //$datosService->endTransa();
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

    function getTiposDocumentos()
    {
        return array(
            1 => 'CC',
            10 => 'TMF',
            11 => 'CD',
            12 => 'ISE',
            13 => 'V',
            14 => 'PT',
            2 => 'TI',
            3 => 'NIT',
            4 => 'CE',
            5 => 'NU',
            6 => 'PA',
            7 => 'RC',
            8 => 'PEP',
            9 => 'CB'
        );
    }

    public function descargar_formularioAction($id)
    {
        $this->setResponse("view");
        $documento = parent::getActUser("documento");
        $mercurio47 = $this->Mercurio47->findFirst("id='{$id}' and documento='{$documento}'");
        if (!$mercurio47) {
        } else {
            $campos = new \stdClass;
            $mercurio33 = $this->db->fetchAll("SELECT * FROM mercurio33 WHERE actualizacion='{$id}'");
            foreach ($mercurio33 as $ai => $row) {
                $campos->$row['campo'] = $row['valor'];
            }
        }

        $procesadorComando = Comman::Api();
        $procesadorComando->runCli(
            array(
                "servicio" => "ComfacaAfilia",
                "metodo" => "parametros_trabajadores",
                "params" => true
            ),
            false
        );
        $datos_captura =  $procesadorComando->toArray();
        $_bancos = array();
        foreach ($datos_captura['codigo_cuenta'] as $data) {
            $_bancos[$data["codcue"]] = $data['detalle'];
        }

        $procesadorComando = Comman::Api();
        $procesadorComando->runCli(
            array(
                "servicio" => "ComfacaAfilia",
                "metodo" => "listar_ciudades_departamentos",
                "params" => true
            ),
            false
        );
        $salida =  $procesadorComando->toArray();

        $_departamentos = array();
        foreach ($salida['departamentos'] as $data) $_departamentos["{$data['coddep']}"] = $data['detdep'];

        $_codzon = array();
        foreach ($salida['zonas'] as $data) $_codzon["{$data['codzon']}"] = $data['detzon'];

        $_codciu = array();
        foreach ($salida['ciudades'] as $data) $_codciu["{$data['codciu']}"] = $data['detciu'];

        $rqs = $this->buscarTrabajadorSubsidio($documento);
        $trabajador = (count($rqs['data']) > 0) ? $rqs['data'] : false;

        $pdf = new TCPDF("P", 'mm', 'Letter');
        $pdf->AddPage();
        $pdf->SetTextColor(0);
        $pdf->SetFont('Arial', '', 9);
        $pdf->Image(base_path(). 'public/docs/formulario_mercurio/fomulario_actualizacion_trabajador_parte_1.jpeg', 0, 0, "216", "280");
        $tipos_documentos = $this->getTiposDocumentos();

        $pdf->setY(57);
        $pdf->setX(20);
        $pdf->Cell(180, 5, $campos->prinom . ' ' . $campos->segnom . ' ' . $campos->priape . ' ' . $campos->segape, 0, 0, 'L');

        $pdf->SetFont('Arial', '', 8);
        $pdf->setY(53);
        $pdf->setX(150);
        $pdf->Cell(53, 6, '(' . @$tipos_documentos["{$trabajador['coddoc']}"] . ') ' . $documento, 0, 0, 'L');

        $pdf->setY(62);
        $pdf->setX(150);
        $pdf->Cell(53, 6, $campos->expedicion, 0, 0, 'L');
        $pdf->SetFont('Arial', '', 9);

        $pdf->setY(75);
        $pdf->setX(20);
        $pdf->Cell(60, 5, substr($campos->dirlab, 0, 12), 0, 0, 'L');

        $pdf->setX(77);
        $pdf->Cell(60, 5, substr($campos->dirlab, 13), 0, 0, 'L');


        $pdf->SetFont('Arial', '', 8);
        $pdf->setX(118);
        $pdf->Cell(60, 5, $_codciu["{$campos->codzon}"], 0, 0, 'L');

        $departamento = substr($campos->codzon, 0, 2);
        $pdf->setY(78);
        $pdf->setX(155);
        $pdf->Cell(60, 5, $_departamentos["{$departamento}"], 0, 0, 'L');

        //telefono
        $pdf->SetFont('Arial', '', 9);
        $pdf->setY(88);
        $pdf->setX(20);
        $pdf->Cell(60, 5, $campos->celular, 0, 0, 'L');

        $pdf->setX(78);
        $pdf->Cell(60, 5, $campos->telefono, 0, 0, 'L');

        $pdf->SetFont('Arial', '', 7.5);
        $pdf->setX(120);
        $pdf->Cell(65, 5, $campos->email, 0, 0, 'L');

        $pdf->SetFont('Arial', '', 9);

        if ($campos->tippag == 'D') {
            $pdf->setY(114);
            $pdf->setX(28);
            $pdf->Cell(10, 5, 'X', 0, 0, 'L');

            $pdf->setX(77);
            $pdf->Cell(30, 5, $campos->celular, 0, 0, 'L');

            $pdf->setY(127);
            $pdf->setX(18);
            $pdf->Cell(30, 5, $_bancos['51'], 0, 0, 'L');

            $pdf->setX(77);
            $pdf->Cell(40, 5, 'AHORROS', 0, 0, 'L');

            $pdf->setX(120);
            $pdf->Cell(30, 5, $campos->numcue, 0, 0, 'L');
        } elseif ($campos->tippag == 'A') {
            $pdf->setY(115);
            $pdf->setX(127);
            $pdf->Cell(10, 5, 'X', 0, 0, 'L');

            $pdf->setY(127);
            $pdf->setX(18);
            $pdf->Cell(65, 5, $_bancos[$campos->banco], 0, 0, 'L');

            $pdf->setX(77);
            $pdf->Cell(40, 5, (($campos->tipcuenta == 'A') ? 'AHORROS' : 'CORRIENTE'), 0, 0, 'L');

            $pdf->setX(120);
            $pdf->Cell(30, 5, $campos->numcue, 0, 0, 'L');
        }

        //direccion comercial
        $pdf->MultiCell(195, 5, "", 0, 'L', 0);
        $pdf->AddPage();
        $pdf->Image('public/docs/formulario_mercurio/fomulario_actualizacion_trabajador_parte_2.jpeg', 0, 0, "216", "280");

        ob_end_clean();
        $pdf->Output("formulario_afiliacion.pdf", "D");
    }

    public function renderTableAction($estado = '')
    {
        $this->setResponse("view");
        $datosTrabajadorService = new DatosTrabajadorService();
        $html = view(
            "actualizadatostra/tmp/solicitudes",
            array(
                "path" => base_path(),
                "solicitudes" => $datosTrabajadorService->findAllByEstado($estado)
            )
        )->render();

        return $this->renderText($html);
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

            $mmercurio47 = $this->Mercurio47->findFirst(" id='{$id}' AND documento='{$documento}' AND coddoc='{$coddoc}'");
            if ($mmercurio47 == False) {
                throw new DebugException("Error la solicitud no está disponible para acceder.", 301);
            } else {
                $solicitud = $mmercurio47->getArray();
            }
            $data = array();
            $mercurio33 = $this->Mercurio33->find(" actualizacion='{$mmercurio47->getId()}'");
            foreach ($mercurio33 as $m33) $data[$m33->campo] = $m33->valor;

            $solicitud = array_merge($data, $solicitud);
            $salida = array(
                "success" => true,
                "data" => $solicitud,
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

            $datosTrabajadorService = new DatosTrabajadorService();
            $mtrabajador = (new Mercurio47)->findFirst(" id='{$id}' AND documento='{$documento}' AND coddoc='{$coddoc}' AND tipo_actualizacion='T'");
            if ($mtrabajador == false) {
                throw new DebugException("Error no se puede identificar el propietario de la solicitud", 301);
            }

            $salida = array(
                'success' => true,
                'data' => $datosTrabajadorService->dataArchivosRequeridos($mtrabajador),
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
}
