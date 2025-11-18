<?php

namespace App\Http\Controllers\Mercurio;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Library\Collections\ParamsEmpresa;
use App\Models\Adapter\DbBase;
use App\Models\FormularioDinamico;
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
use App\Services\Api\ApiSubsidio;
use Illuminate\Http\Request;

class ActualizaEmpresaController extends ApplicationController
{
    protected $tipopc = '5';

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
        return view('mercurio.actualizadatos.index', [
            'title' => 'Solicitud de actualización de datos',
            'documento' => $this->user['documento'],
            'coddoc' => $this->user['coddoc'],
            'codciu' => $this->user['codciu'],
            'tipo' => $this->tipo,
        ]);
    }

    public function guardar(Request $request)
    {
        $this->db->begin();
        $actualizaEmpresaService = new ActualizaEmpresaService;
        try {
            $clave_certificado = $request->input('clave');
            $documento = $this->user['documento'];
            $coddoc = $this->user['coddoc'];
            $tipo = $this->tipo;
            $tipact = 'E';

            $logger = new Logger;
            $log = $logger->registrarLog(
                false,
                'Guarda actualizacion datos empresa',
                json_encode($request->all())
            );

            $asignarFuncionario = new AsignarFuncionario;

            $id = $request->input('id');

            $params = [
                'fecsol' => date('Y-m-d'),
                'fecest' => date('Y-m-d'),
                'estado' => 'T',
                'tipact' => $tipact,
                'tipo' => $tipo,
                'coddoc' => $coddoc,
                'documento' => $documento,
                'usuario' => $asignarFuncionario->asignar($this->tipopc, $this->user['codciu']),
            ];

            if (is_null($id) || $id == '') {
                $solicitud = $actualizaEmpresaService->createByFormData($params);
            } else {
                $res = $actualizaEmpresaService->updateByFormData($id, $params);
                if (! $res) {
                    throw new DebugException('Error no se actualizo los datos', 301);
                }
                $solicitud = $actualizaEmpresaService->findById($id);
            }

            Mercurio33::where('documento', $documento)
                ->where('coddoc', $coddoc)
                ->delete();

            $campos = Mercurio28::where('tipo', $tipo)->get();
            if ($campos) {
                foreach ($campos as $mercurio28) {
                    $valor = $request->input($mercurio28->getCampo());
                    if ($valor == '') {
                        continue;
                    }

                    $mercurio33 = Mercurio33::where('documento', $documento)
                        ->where('coddoc', $coddoc)
                        ->where('actualizacion', $solicitud->getId())
                        ->where('campo', $mercurio28->getCampo())
                        ->first();

                    if ($mercurio33) {
                        $mercurio33->valor = $valor;
                        $mercurio33->save();
                    } else {
                        Mercurio33::create(
                            [
                                'tipo' => $mercurio28->getTipo(),
                                'coddoc' => $coddoc,
                                'documento' => $documento,
                                'campo' => $mercurio28->getCampo(),
                                'antval' => $valor,
                                'valor' => $valor,
                                'estado' => 'P',
                                'motivo' => '',
                                'fecest' => date('Y-m-d'),
                                'usuario' => $solicitud->getUsuario(),
                                'actualizacion' => $solicitud->getId(),
                                'log' => $log
                            ]
                        );
                    }
                }
            }

            $data = [];
            $mercurio33 = Mercurio33::where('documento', $documento)
                ->where('coddoc', $coddoc)
                ->where('actualizacion', $id)
                ->get();

            if ($mercurio33) {
                foreach ($mercurio33 as $m33) {
                    $data[$m33->campo] = $m33->valor;
                }
            }
            $data = array_merge($solicitud->toArray(), $data);

            $empresa_sisu = $actualizaEmpresaService->buscarEmpresaSubsidio($documento);
            if ($empresa_sisu && count($empresa_sisu) > 0) {
                $empresa = new Mercurio30();
                $empresa->fill($empresa_sisu);
            } else {
                $empresa = new Mercurio30();
            }
            $empresa->id = $id;

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
                [
                    'tipopc' => $this->tipopc,
                    'coddoc' => 27,
                    'id' => $solicitud->id,
                ]
            ))->salvarDatos($out);

            $response = [
                'success' => true,
                'msj' => 'Registro completado con éxito',
                'data' => $data,
            ];
            $this->db->commit();
        } catch (DebugException $e) {
            $response = [
                'success' => false,
                'msj' => $e->getMessage(),
            ];
            $this->db->rollBack();
        }

        return $this->renderObject($response);
    }

    public function params()
    {
        $documento = $this->user['documento'];
        try {
            $mtipoDocumentos = new Gener18;
            $tipoDocumentos = [];

            foreach ($mtipoDocumentos->all() as $mtipo) {
                if ($mtipo->getCoddoc() == '7' || $mtipo->getCoddoc() == '2') {
                    continue;
                }
                $tipoDocumentos["{$mtipo->getCoddoc()}"] = $mtipo->getDetdoc();
            }

            $msubsi54 = new Subsi54;
            $tipsoc = [];
            foreach ($msubsi54->all() as $entity) {
                $tipsoc["{$entity->getTipsoc()}"] = $entity->getDetalle();
            }

            $coddoc = [];
            foreach ($mtipoDocumentos->all() as $entity) {
                if ($entity->getCoddoc() == '7' || $entity->getCoddoc() == '2') {
                    continue;
                }
                $coddoc["{$entity->getCoddoc()}"] = $entity->getDetdoc();
            }

            $coddocrepleg = [];
            foreach ($mtipoDocumentos->all() as $entity) {
                if ($entity->getCodrua() == 'TI' || $entity->getCodrua() == 'RC') {
                    continue;
                }
                $coddocrepleg["{$entity->getCodrua()}"] = $entity->getDetdoc();
            }

            $zonas = [];
            $mgener09 = new Gener09;
            foreach ($mgener09->getFind("conditions: codzon >='18000' and codzon <= '19000'") as $entity) {
                $zonas["{$entity->getCodzon()}"] = $entity->getDetzon();
            }

            $procesadorComando = new ApiSubsidio();
            $procesadorComando->send(
                [
                    'servicio' => 'ComfacaAfilia',
                    'metodo' => 'parametros_empresa',
                ]
            );
            $paramsEmpresa = new ParamsEmpresa;
            $paramsEmpresa->setDatosCaptura($procesadorComando->toArray());

            $actualizaEmpresaService = new ActualizaEmpresaService;
            $empresa_sisu = $actualizaEmpresaService->buscarEmpresaSubsidio($documento);
            $ciudades = ParamsEmpresa::getCiudades();

            $list_sucursales = [];
            if ($empresa_sisu && count($empresa_sisu) > 0) {
                $sucursales = ($empresa_sisu['sucursales'] && count($empresa_sisu['sucursales']) > 0) ? $empresa_sisu['sucursales'] : false;
                if ($sucursales) {
                    foreach ($sucursales as $sucursal) {
                        if ($sucursal['estado'] != 'I') {
                            $list_sucursales[$sucursal['codsuc']] = $sucursal['detalle'] . ' - ' . $ciudades[$sucursal['codzon']];
                        }
                    }
                }
            }

            $coddoc = $tipoDocumentos;
            $data = [
                'tipafi' => get_array_tipos(),
                'coddoc' => $coddoc,
                'tipdoc' => $coddoc,
                'tipper' => tipper_array(),
                'tipsoc' => $tipsoc,
                'calemp' => calemp_array(),
                'codciu' => ParamsEmpresa::getCiudades(),
                'coddocrepleg' => $coddocrepleg,
                'codzon' => $zonas,
                'codact' => ParamsEmpresa::getActividades(),
                'tipemp' => ParamsEmpresa::getTipoEmpresa(),
                'codcaj' => ParamsEmpresa::getCodigoCajas(),
                'ciupri' => ParamsEmpresa::getCiudades(),
                'ciunac' => ParamsEmpresa::getCiudades(),
                'tipsal' => tipsal_array(),
                'autoriza' => autoriza_array(),
                'ciupri' => ParamsEmpresa::getCiudades(),
                'codsuc' => $list_sucursales,
            ];

            $formulario = FormularioDinamico::where('name', 'mercurio471')->first();
            $componentes = $formulario->componentes()->get();
            $componentes = $componentes->map(function ($componente) use ($data) {
                $_componente = $componente->toArray();
                if (isset($data[$componente->name])) {
                    $_componente['data_source'] = $data[$componente->name];
                }
                $_componente['id'] = $componente->name;
                return $_componente;
            });

            $salida = [
                'success' => true,
                'data' => $componentes,
                'msj' => 'OK',
            ];
        } catch (DebugException $e) {
            $salida = [
                'success' => false,
                'msj' => $e->getMessage(),
            ];
        }

        return $this->renderObject($salida, false);
    }

    public function borrar(Request $request)
    {
        try {
            $id = $request->input('id');
            $solicitud = Mercurio47::where('id', $id)->first();
            if ($solicitud) {
                if ($solicitud->getEstado() != 'T') {
                    Mercurio10::where('numero', $id)->where('tipopc', $this->tipopc)->delete();
                }
                Mercurio33::where('actualizacion', $id)->delete();
                Mercurio47::where('id', $id)->delete();
            }
            $salida = [
                'success' => true,
                'msj' => 'El registro se borro con éxito del sistema.',
            ];
        } catch (DebugException $e) {
            $salida = [
                'success' => false,
                'msj' => $e->getMessage(),
            ];
        }

        return $this->renderObject($salida);
    }

    public function archivos_requeridos($mercurio47)
    {
        $archivos = [];
        $mercurio14 = Mercurio14::where('tipopc', $this->tipopc)->get();

        $mercurio10 = $this->db->fetchOne("SELECT item, estado, campos_corregir
        FROM mercurio10
        WHERE numero='{$mercurio47->getId()}' AND tipopc='{$this->tipopc}' ORDER BY item DESC LIMIT 1");

        $corregir = false;
        if ($mercurio10) {
            if ($mercurio10['estado'] == 'D') {
                $campos = $mercurio10['campos_corregir'];
                $corregir = explode(';', $campos);
            }
        }
        foreach ($mercurio14 as $m14) {
            $m12 = Mercurio12::where('coddoc', $m14->getCoddoc())->first();
            $mercurio37 = Mercurio37::where('tipopc', $this->tipopc)->where('numero', $mercurio47->getId())->where('coddoc', $m14->getCoddoc())->first();
            $corrige = false;
            if ($corregir) {
                if (in_array($m12->getCoddoc(), $corregir)) {
                    $corrige = true;
                }
            }
            $obliga = ($m14->getObliga() == 'S') ? "<br><small class='text-danger'>Obligatorio</small>" : '';
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
        $html = view('actualizadatos/tmp/archivos_requeridos', [
            'load_archivos' => $archivos,
            'path' => $mercurio01->getPath(),
            'puede_borrar' => ($mercurio47->getEstado() == 'P' || $mercurio47->getEstado() == 'A') ? false : true,
            'mercurio14' => $mercurio14,
        ])->render();

        return $html;
    }

    public function borrarArchivo(Request $request)
    {
        try {
            $numero = $this->clp($request, 'id');
            $coddoc = $this->clp($request, 'coddoc');
            $mercurio37 = Mercurio37::where('tipopc', $this->tipopc)->where('numero', $numero)->where('coddoc', $coddoc)->first();

            $filepath = storage_path('temp/' . $mercurio37->getArchivo());
            if (file_exists($filepath)) {
                unlink($filepath);
            }

            Mercurio37::where('tipopc', $this->tipopc)
                ->where('numero', $numero)
                ->where('coddoc', $coddoc)
                ->delete();

            $response = [
                'success' => true,
                'msj' => 'El archivo se borro de forma correcta',
            ];
        } catch (DebugException $e) {
            $response = [
                'success' => false,
                'msj' => $e->getMessage(),
            ];
        }

        return $this->renderObject($response, false);
    }

    public function guardarArchivo(Request $request)
    {
        try {
            $id = $request->input('id');
            $coddoc = $request->input('coddoc');

            $guardarArchivoService = new GuardarArchivoService([
                'tipopc' => $this->tipopc,
                'coddoc' => $coddoc,
                'id' => $id,
            ]);
            $mercurio37 = $guardarArchivoService->main();
            $response = [
                'success' => true,
                'msj' => 'Ok archivo procesado',
                'data' => $mercurio37->getArray(),
            ];
        } catch (DebugException $e) {
            $response = [
                'success' => false,
                'msj' => $e->getMessage(),
            ];
        }

        return $this->renderObject($response, false);
    }

    public function enviarCaja(Request $request)
    {
        try {
            $id = $request->input('id');
            $actualizaService = new ActualizaEmpresaService;

            $asignarFuncionario = new AsignarFuncionario;
            $usuario = $asignarFuncionario->asignar($this->tipopc, $this->user['codciu']);

            $actualizaService->enviarCaja(new SenderValidationCaja, $id, $usuario);

            $salida = [
                'success' => true,
                'msj' => 'El envio de la solicitud se ha completado con éxito',
            ];
        } catch (DebugException $e) {
            $salida = [
                'success' => false,
                'msj' => $e->getMessage(),
            ];
        }

        return $this->renderObject($salida);
    }

    public function formulario($id)
    {
        $mercurio47 = Mercurio47::where('id', $id)->where('documento', $this->user['documento'])->first();
        if ($mercurio47) {
            $campos = Mercurio33::where('actualizacion', $id)->get()->mapWithKeys(function ($row) {
                return [$row->campo => $row->valor];
            })->toArray();
        }

        $procesadorComando = new ApiSubsidio();
        $procesadorComando->send(
            [
                'servicio' => 'ComfacaAfilia',
                'metodo' => 'parametros_empresa',
            ]
        );
        $paramsEmpresa = new ParamsEmpresa;
        $paramsEmpresa->setDatosCaptura($procesadorComando->toArray());

        $actualizaEmpresaService = new ActualizaEmpresaService;
        $empresa_sisu = $actualizaEmpresaService->buscarEmpresaSubsidio($campos->nit);
        $empresa = ($empresa_sisu && count($empresa_sisu) > 0) ? $empresa_sisu : false;

        $timer = strtotime('now');
        $file = "formulario_afiliacion_{$id}_{$timer}.pdf";

        $formularios = new Formularios;
        $formularios->actualizadatosAfiliacion(
            [
                $empresa,
                $campos,
            ],
            $file
        );
    }

    public function renderTable($estado = '')
    {
        $this->setResponse('view');
        $actualizaEmpresaService = new ActualizaEmpresaService;
        $html = view(
            'mercurio/actualizadatos/tmp/solicitudes',
            [
                'path' => base_path(),
                'solicitudes' => $actualizaEmpresaService->findAllByEstado($estado),
            ]
        )->render();

        return $this->renderText($html);
    }

    public function sucursales()
    {
        try {
            $actualizaEmpresaService = new ActualizaEmpresaService;

            $empresa_sisu = $actualizaEmpresaService->buscarEmpresaSubsidio($this->user['documento']);
            $list_sucursales = [];
            if ($empresa_sisu && count($empresa_sisu) > 0) {
                $sucursales = ($empresa_sisu['sucursales'] && count($empresa_sisu['sucursales']) > 0) ? $empresa_sisu['sucursales'] : false;
                if ($sucursales) {
                    foreach ($sucursales as $sucursal) {
                        if ($sucursal['estado'] != 'I') {
                            $list_sucursales[$sucursal['codsuc']] = $sucursal['detalle'] . ' ' . $sucursal['codzon'];
                        }
                    }
                }
            }

            $salida = [
                'success' => true,
                'msj' => 'Proceso completado con éxito',
                'data' => $list_sucursales,
            ];
        } catch (DebugException $e) {
            $salida = ['success' => false, 'msj' => $e->getMessage()];
        }

        return $this->renderObject($salida, false);
    }

    public function searchRequest($id)
    {
        try {
            if (is_null($id)) {
                throw new DebugException('Error no hay solicitud a buscar', 301);
            }

            $mmercurio47 = Mercurio47::where('id', $id)->where('documento', $this->user['documento'])->where('coddoc', $this->user['coddoc'])->first();
            if ($mmercurio47 == false) {
                throw new DebugException('Error la solicitud no está disponible para acceder.', 301);
            } else {
                $solicitud = $mmercurio47->getArray();
            }
            $data = [];
            $mercurio33 = Mercurio33::where('actualizacion', $mmercurio47->getId())->get();
            foreach ($mercurio33 as $m33) {
                $data[$m33->campo] = $m33->valor;
            }

            $solicitud = array_merge($data, $solicitud);
            $salida = [
                'success' => true,
                'data' => $solicitud,
                'msj' => 'OK',
            ];
        } catch (DebugException $e) {
            $salida = [
                'success' => false,
                'msj' => $e->getMessage(),
            ];
        }

        return $this->renderObject($salida, false);
    }

    public function empresaSisu()
    {
        try {
            $actualizaEmpresaService = new ActualizaEmpresaService;
            $empresa_sisu = $actualizaEmpresaService->buscarEmpresaSubsidio($this->user['documento']);
            $salida = [
                'success' => true,
                'data' => ($empresa_sisu && count($empresa_sisu) > 0) ? $empresa_sisu : false,
                'msj' => 'OK',
            ];
        } catch (DebugException $e) {
            $salida = [
                'success' => false,
                'msj' => $e->getMessage(),
            ];
        }

        return $this->renderObject($salida, false);
    }

    public function consultaDocumentos($id)
    {
        try {
            $empresaService = new ActualizaEmpresaService;

            $sindepe = Mercurio47::where('id', $id)
                ->where('documento', $this->user['documento'])
                ->where('coddoc', $this->user['coddoc'])
                ->whereNotIn('estado', ['I', 'X'])
                ->first();

            if (! $sindepe) {
                throw new DebugException('Error no se puede identificar ID', 301);
            }
            $salida = [
                'success' => true,
                'data' => $empresaService->dataArchivosRequeridos($sindepe),
                'msj' => 'OK',
            ];
        } catch (DebugException $e) {
            $salida = [
                'success' => false,
                'msj' => $e->getMessage(),
            ];
        }

        return $this->renderObject($salida, false);
    }

    public function seguimiento(Request $request)
    {
        try {
            $actualizaEmpresaService = new ActualizaEmpresaService;
            $out = $actualizaEmpresaService->consultaSeguimiento($request->input('id'));
            $salida = [
                'success' => true,
                'data' => $out,
            ];
        } catch (DebugException $e) {
            $salida = ['success' => false, 'msj' => $e->getMessage()];
        }
        return response()->json($salida);
    }
}
