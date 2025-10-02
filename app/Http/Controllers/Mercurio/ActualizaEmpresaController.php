<?php

namespace App\Http\Controllers\Mercurio;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Library\Collections\ParamsEmpresa;
use App\Models\Adapter\DbBase;
use App\Models\Gener09;
use App\Models\Gener18;
use App\Models\Mercurio01;
use App\Models\Mercurio07;
use App\Models\Mercurio10;
use App\Models\Mercurio12;
use App\Models\Mercurio14;
use App\Models\Mercurio28;
use App\Models\Mercurio30;
use App\Models\Mercurio31;
use App\Models\Mercurio33;
use App\Models\Mercurio37;
use App\Models\Mercurio47;
use App\Models\Subsi54;
use App\Services\Entidades\ActualizaEmpresaService;
use App\Services\FormulariosAdjuntos\DatosEmpresaService;
use App\Services\FormulariosAdjuntos\Formularios;
use App\Services\Utils\AsignarFuncionario;
use App\Services\Utils\Comman;
use App\Services\Utils\GuardarArchivoService;
use App\Services\Utils\Logger;
use App\Services\Utils\SenderValidationCaja;
use Illuminate\Http\Request;

class ActualizaEmpresaController extends ApplicationController
{
    protected $tipopc = "5";
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
        return view('mercurio.actualizadatos.index', [
            'title' => 'Solicitud de actualización de datos',
            'documento' => $this->user['documento'],
            'coddoc' => $this->user['coddoc'],
            'codciu' => $this->user['codciu'],
            'tipo' => $this->tipo
        ]);
    }


    public function guardarAction(Request $request)
    {
        $this->db->begin();
        $actualizaEmpresaService = new ActualizaEmpresaService();
        try {
            $clave_certificado = $request->input('clave');
            $documento = $this->user['documento'];
            $coddoc = $this->user['coddoc'];
            $tipo = $this->tipo;

            $logger = new Logger();
            $log = $logger->registrarLog(
                false,
                "Guarda actualizacion datos trabajador",
                json_encode($request->all())
            );

            $asignarFuncionario = new AsignarFuncionario();

            $id = $request->input('id');

            $params = array(
                'fecha_solicitud' => date('Y-m-d'),
                'fecha_estado' => date('Y-m-d'),
                'estado' => 'T',
                'tipo_actualizacion' => 'E',
                'tipo' => $tipo,
                'coddoc'  => $coddoc,
                'documento'  => $documento,
                'usuario'  => $asignarFuncionario->asignar($this->tipopc, $this->user['codciu']),
            );

            if (is_null($id) || $id == '') {
                $solicitud = $actualizaEmpresaService->createByFormData($params);
            } else {
                $res = $actualizaEmpresaService->updateByFormData($id, $params);
                if (!$res) {
                    throw new DebugException("Error no se actualizo los datos", 301);
                }
                $solicitud = $actualizaEmpresaService->findById($id);
            }

            Mercurio33::where('documento', $documento)
                ->where('coddoc', $coddoc)
                ->delete();

            $campos = Mercurio28::where("tipo", $tipo)->get();
            if ($campos) {
                foreach ($campos as $mercurio28) {
                    $valor = $request->input($mercurio28->getCampo());
                    if ($valor == '') continue;

                    $mercurio33 = Mercurio33::where('documento', $documento)
                        ->where('coddoc', $coddoc)
                        ->where('actualizacion', $solicitud->getId())
                        ->where('campo', $mercurio28->getCampo())
                        ->first();

                    if ($mercurio33) {
                        $mercurio33->valor = $valor;
                        $mercurio33->save();
                    } else {
                        $mercurio33 = new Mercurio33();
                        $mercurio33->tipo = $mercurio28->getTipo();
                        $mercurio33->coddoc = $coddoc;
                        $mercurio33->documento = $documento;
                        $mercurio33->campo = $mercurio28->getCampo();
                        $mercurio33->antval = $valor;
                        $mercurio33->valor = $valor;
                        $mercurio33->estado = 'P';
                        $mercurio33->motivo = '';
                        $mercurio33->fecest = date('Y-m-d');
                        $mercurio33->usuario = $solicitud->getUsuario();
                        $mercurio33->actualizacion = $solicitud->getId();
                        $mercurio33->log = $log;
                        $mercurio33->save();
                    }
                }
            }

            $data = array();
            $mercurio33 = Mercurio33::where('documento', $documento)
                ->where('coddoc', $coddoc)
                ->where('actualizacion', $id)
                ->get();

            if ($mercurio33) {
                foreach ($mercurio33 as $m33) $data[$m33->campo] = $m33->valor;
            }
            $data = array_merge($solicitud->getArray(), $data);

            $out = $actualizaEmpresaService->buscarEmpresaSubsidio($documento);
            $empresa = new Mercurio30($out['data']);
            $empresa->setId($solicitud->getId());

            $actualizaEmpresaService = new DatosEmpresaService(
                [
                    'empresa' => $empresa->toArray(),
                    'campos' => $data,
                    'documento' => $documento,
                    'coddoc' => $coddoc,
                    'nit' => $documento,
                ]
            );
            $actualizaEmpresaService->setClaveCertificado($clave_certificado);

            $out = $actualizaEmpresaService->formulario()->getResult();
            (new GuardarArchivoService(
                array(
                    'tipopc' => $this->tipopc,
                    'coddoc' => 27,
                    'id' => $empresa->getId()
                )
            ))->salvarDatos($out);

            $response = [
                'success' => true,
                'msj' => 'Registro completado con éxito',
                'data' => $data
            ];
            $this->db->commit();
        } catch (DebugException $e) {
            $response = [
                'success' => false,
                'msj' => $e->getMessage()
            ];
            $this->db->rollBack();
        }

        return $this->renderObject($response);
    }

    public function paramsAction()
    {
        $documento = parent::getActUser("documento");
        $this->setResponse("ajax");

        try {
            $mtipoDocumentos = new Gener18();
            $tipoDocumentos = array();

            foreach ($mtipoDocumentos->all() as $mtipo) {
                if ($mtipo->getCoddoc() == '7' || $mtipo->getCoddoc() == '2') continue;
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

            $zonas = array();
            $mgener09 = new Gener09();
            foreach ($mgener09->getFind("conditions: codzon >='18000' and codzon <= '19000'") as $entity) {
                $zonas["{$entity->getCodzon()}"] = $entity->getDetzon();
            }

            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
                array(
                    "servicio" => "ComfacaAfilia",
                    "metodo" => "parametros_empresa"
                ),
                false
            );
            $paramsEmpresa = new ParamsEmpresa();
            $paramsEmpresa->setDatosCaptura($procesadorComando->toArray());

            $actualizaEmpresaService = new ActualizaEmpresaService();
            $rqs = $actualizaEmpresaService->buscarEmpresaSubsidio($documento);
            $ciudades = ParamsEmpresa::getCiudades();

            $list_sucursales = array();
            if ($rqs) {
                $sucursales = (count($rqs['sucursales']) > 0) ? $rqs['sucursales'] : false;
                if ($sucursales) {
                    foreach ($sucursales as $sucursal) {
                        if ($sucursal['estado'] != 'I') {
                            $list_sucursales[$sucursal['codsuc']] = $sucursal['detalle'] . ' - ' . $ciudades[$sucursal['codzon']];
                        }
                    }
                }
            }

            $tipafi = (new Mercurio07())->getArrayTipos();
            $coddoc = $tipoDocumentos;
            $data = array(
                'tipafi' => $tipafi,
                'coddoc' => $coddoc,
                'tipdoc' => $coddoc,
                'tipper' => (new Mercurio30())->getTipperArray(),
                'tipsoc' => $tipsoc,
                'calemp' => (new Mercurio30())->getCalempArray(),
                'codciu' => ParamsEmpresa::getCiudades(),
                'coddocrepleg' => $coddocrepleg,
                'codzon' => $zonas,
                'codact' => ParamsEmpresa::getActividades(),
                'tipemp' => ParamsEmpresa::getTipoEmpresa(),
                'codcaj' => ParamsEmpresa::getCodigoCajas(),
                'ciupri' => ParamsEmpresa::getCiudades(),
                'ciunac' => ParamsEmpresa::getCiudades(),
                'tipsal' => (new Mercurio31())->getTipsalArray(),
                "autoriza" => array("S" => "SI", "N" => "NO"),
                "ciupri" => ParamsEmpresa::getCiudades(),
                'codsuc' => $list_sucursales
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

    public function borrarAction(Request $request)
    {
        $this->setResponse("ajax");
        try {
            $id = $request->input('id');
            $solicitud = Mercurio47::where("id", $id)->first();
            if ($solicitud) {
                if ($solicitud->getEstado() != 'T') {
                    Mercurio10::where("numero", $id)->where("tipopc", $this->tipopc)->delete();
                }
                Mercurio33::where("actualizacion", $id)->delete();
                Mercurio47::where("id", $id)->delete();
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
        return $this->renderObject($salida);
    }

    function archivos_requeridos($mercurio47)
    {
        $archivos = array();
        $mercurio14 = Mercurio14::where("tipopc", $this->tipopc)->get();


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
        foreach ($mercurio14 as $m14) {
            $m12 = Mercurio12::where("coddoc", $m14->getCoddoc())->first();
            $mercurio37 = Mercurio37::where("tipopc", $this->tipopc)->where("numero", $mercurio47->getId())->where("coddoc", $m14->getCoddoc())->first();
            $corrige = false;
            if ($corregir) {
                if (in_array($m12->getCoddoc(), $corregir)) {
                    $corrige = true;
                }
            }
            $obliga = ($m14->getObliga() == "S") ? "<br><small class='text-danger'>Obligatorio</small>" : "";
            $archivo = new \stdClass;
            $archivo->obliga = $obliga;
            $archivo->id = $mercurio47->getId();
            $archivo->coddoc = $m14->getCoddoc();
            $archivo->detalle = $m12->getDetalle();
            $archivo->diponible = ($mercurio37) ? $mercurio37->getArchivo() : false;
            $archivo->corrige = $corrige;
            $archivos[] = $archivo;
        }
        $mercurio01 = Mercurio01::first();
        $html = view("actualizadatos/tmp/archivos_requeridos", array(
            "load_archivos" => $archivos,
            "path" => $mercurio01->getPath(),
            "puede_borrar" => ($mercurio47->getEstado() == 'P' || $mercurio47->getEstado() == 'A') ? false : true,
            "mercurio14" => $mercurio14
        ))->render();

        return $html;
    }

    public function borrarArchivoAction(Request $request)
    {
        $this->setResponse("ajax");
        try {
            $numero = $this->clp($request, 'id');
            $coddoc = $this->clp($request, 'coddoc');
            $mercurio37 = Mercurio37::where("tipopc", $this->tipopc)->where("numero", $numero)->where("coddoc", $coddoc)->first();

            $filepath = storage_path('temp/' . $mercurio37->getArchivo());
            if (file_exists($filepath)) {
                unlink($filepath);
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

    public function enviarCajaAction(Request $request)
    {
        $this->setResponse("ajax");
        try {
            $id = $request->input('id', "addslaches", "alpha", "extraspaces", "striptags");
            $actualizaService = new ActualizaEmpresaService();
            //$actualizaService->setTransa();

            $asignarFuncionario = new AsignarFuncionario();
            $usuario = $asignarFuncionario->asignar($this->tipopc, $this->user['codciu']);

            $actualizaService->enviarCaja(new SenderValidationCaja(), $id, $usuario);
            //$actualizaService->endTransa();

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


    public function formularioAction($id)
    {
        $this->setResponse("view");
        $documento = parent::getActUser("documento");
        $mercurio47 = Mercurio47::where("id", $id)->where("documento", $documento)->first();
        if ($mercurio47) {
            $campos = Mercurio33::where('actualizacion', $id)->get()->mapWithKeys(function ($row) {
                return [$row->campo => $row->valor];
            })->toArray();
        }

        $procesadorComando = Comman::Api();
        $procesadorComando->runCli(
            array(
                "servicio" => "ComfacaAfilia",
                "metodo" => "parametros_empresa"
            ),
            false
        );
        $paramsEmpresa = new ParamsEmpresa();
        $paramsEmpresa->setDatosCaptura($procesadorComando->toArray());

        $actualizaEmpresaService = new ActualizaEmpresaService();
        $rqs = $actualizaEmpresaService->buscarEmpresaSubsidio($campos->nit);
        $empresa = (count($rqs['data']) > 0) ? $rqs['data'] : false;

        $timer = strtotime('now');
        $file = "formulario_afiliacion_{$id}_{$timer}.pdf";

        $formularios = new Formularios();
        $formularios->actualizadatosAfiliacion(
            array(
                $empresa,
                $campos
            ),
            $file
        );
    }

    public function renderTableAction($estado = '')
    {
        $this->setResponse("view");
        $actualizaEmpresaService = new ActualizaEmpresaService();
        $html = view(
            "mercurio/actualizadatos/tmp/solicitudes",
            array(
                "path" => base_path(),
                "solicitudes" => $actualizaEmpresaService->findAllByEstado($estado)
            )
        )->render();

        return $this->renderText($html);
    }

    public function sucursalesAction()
    {
        try {
            $documento = parent::getActUser("documento");
            $actualizaEmpresaService = new ActualizaEmpresaService();

            $rqs = $actualizaEmpresaService->buscarEmpresaSubsidio($documento);
            $list_sucursales = array();
            if ($rqs) {
                $sucursales = (count($rqs['sucursales']) > 0) ? $rqs['sucursales'] : false;
                if ($sucursales) {
                    foreach ($sucursales as $sucursal) {
                        if ($sucursal['estado'] != 'I') {
                            $list_sucursales[$sucursal['codsuc']] = $sucursal['detalle'] . ' ' . $sucursal['codzon'];
                        }
                    }
                }
            }

            $salida = array(
                'success' => true,
                'msj' => 'Proceso completado con éxito',
                'data' => $list_sucursales
            );
        } catch (DebugException $e) {
            $salida = array('success' => false, 'msj' => $e->getMessage());
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

            $mmercurio47 = Mercurio47::where("id", $id)->where("documento", $documento)->where("coddoc", $coddoc)->first();
            if ($mmercurio47 == False) {
                throw new DebugException("Error la solicitud no está disponible para acceder.", 301);
            } else {
                $solicitud = $mmercurio47->getArray();
            }
            $data = array();
            $mercurio33 = Mercurio33::where("actualizacion", $mmercurio47->getId())->get();
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

    public function empresaSisuAction()
    {
        $this->setResponse("ajax");
        try {
            $documento = parent::getActUser("documento");
            $actualizaEmpresaService = new ActualizaEmpresaService();
            $rqs = $actualizaEmpresaService->buscarEmpresaSubsidio($documento);
            $salida = array(
                "success" => true,
                "data" => ($rqs) ? $rqs['data'] : false,
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
            $empresaService = new ActualizaEmpresaService();

            $sindepe = Mercurio47::where('id', $id)
                ->where('documento', $documento)
                ->where('coddoc', $coddoc)
                ->whereNotIn('estado', ['I', 'X'])
                ->first();

            if (!$sindepe) {
                throw new DebugException("Error no se puede identificar ID", 301);
            }
            $salida = array(
                'success' => true,
                'data' => $empresaService->dataArchivosRequeridos($sindepe),
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

    public function seguimientoAction($id)
    {
        $this->setResponse("ajax");
        try {
            $actualizaEmpresaService = new ActualizaEmpresaService();
            $out = $actualizaEmpresaService->consultaSeguimiento($id);
            $salida = array(
                "success" => true,
                "data" => $out
            );
        } catch (DebugException $e) {
            $salida = array('success' => false, 'msj' => $e->getMessage());
        }
        return $this->renderObject($salida, false);
    }
}
