<?php

namespace App\Http\Controllers\Mercurio;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Library\Collections\ParamsTrabajador;
use App\Models\Adapter\DbBase;
use App\Models\FormularioDinamico;
use App\Models\Gener09;
use App\Models\Gener18;
use App\Models\Mercurio01;
use App\Models\Mercurio10;
use App\Models\Mercurio12;
use App\Models\Mercurio13;
use App\Models\Mercurio28;
use App\Models\Mercurio33;
use App\Models\Mercurio37;
use App\Models\Mercurio47;
use App\Models\Subsi54;
use App\Services\Entidades\DatosTrabajadorService;
use App\Services\Utils\AsignarFuncionario;
use App\Services\Utils\GuardarArchivoService;
use App\Services\Utils\Logger;
use App\Services\Utils\SenderValidationCaja;
use App\Services\Api\ApiSubsidio;
use App\Services\Entidades\EmpresaService;
use Illuminate\Http\Request;
use TCPDF;

class ActualizaTrabajadorController extends ApplicationController
{
    protected $tipopc = 14;

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
        try {
            return view('mercurio.actualizadatostra.index', [
                'title' => 'Solicitud de actualización de datos',
                'cedtra' => $this->user['documento'],
            ]);
        } catch (\Throwable $e) {
            $salida = $this->handleException($e, request());
            set_flashdata('error', [
                'msj' => $salida['msj'],
                'code' => $salida['code'],
            ]);

            return redirect()->route('principal/index');
        }
    }

    public function params()
    {
        try {
            $nit = $this->user['documento'];

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

            $codciu = [];
            $mgener09 = new Gener09;
            foreach ($mgener09->getFind("conditions: codzon >='18000' and codzon <= '19000'") as $entity) {
                $codciu["{$entity->getCodzon()}"] = $entity->getDetzon();
            }

            $procesadorComando = new ApiSubsidio();
            $procesadorComando->send(
                [
                    'servicio' => 'ComfacaAfilia',
                    'metodo' => 'parametros_trabajadores',
                ]
            );
            $paramsTrabajador = new ParamsTrabajador;
            $paramsTrabajador->setDatosCaptura($procesadorComando->toArray());

            $procesadorComando = new ApiSubsidio();
            $procesadorComando->send(
                [
                    'servicio' => 'ComfacaEmpresas',
                    'metodo' => "buscar_sucursales_en_empresa/{$nit}",
                    'params' => '',
                ]
            );
            $rqs = $procesadorComando->toArray();

            $codsuc = [];
            $sucursales = $rqs['data'];
            if ($sucursales) {
                foreach ($sucursales as $data) {
                    if ($data['estado'] == 'I') {
                        continue;
                    }
                    if (isset($codciu[$data['codzon']])) {
                        $codsuc["{$data['codsuc']}"] = $data['detalle'] . ' - DE ' . $codciu[$data['codzon']];
                    } else {
                        $codsuc["{$data['codsuc']}"] = $data['detalle'];
                    }
                }
            }

            $tipafi = get_array_tipos();
            $data = [
                'tipafi' => $tipafi,
                'tipdoc' => $tipoDocumentos,
                'tipper' => tipper_array(),
                'tipsoc' => $tipsoc,
                'calemp' => calemp_array(),
                'codciu' => $codciu,
                'codzon' => $codciu,
                'respo_tipdoc' => $tipoDocumentos,
                'sexo' => sexos_array(),
                'estciv' => estados_civiles_array(),
                'cabhog' => cabeza_hogar(),
                'captra' => capacidad_trabajar(),
                'tipdis' => tipo_discapacidad_array(),
                'nivedu' => ParamsTrabajador::getNivelEducativo(),
                'rural' => ParamsTrabajador::getRural(),
                'tipcon' => ParamsTrabajador::getTipoContrato(),
                'trasin' => ParamsTrabajador::getSindicalizado(),
                'vivienda' => ParamsTrabajador::getVivienda(),
                'cargo' => ParamsTrabajador::getOcupaciones(),
                'orisex' => ParamsTrabajador::getOrientacionSexual(),
                'facvul' => ParamsTrabajador::getVulnerabilidades(),
                'peretn' => ParamsTrabajador::getPertenenciaEtnicas(),
                'ciunac' => ParamsTrabajador::getCiudades(),
                'tippag' => ParamsTrabajador::getTipoPago(),
                'resguardo_id' => ParamsTrabajador::getResguardos(),
                'pub_indigena_id' => ParamsTrabajador::getPueblosIndigenas(),
                'codban' => ParamsTrabajador::getBancos(),
                'tipsal' => tipsal_array(),
                'tipcue' => ParamsTrabajador::getTipoCuenta(),
                'ruralt' => ParamsTrabajador::getRural(),
                'tipjor' => tipo_jornada_array(),
                'autoriza' => autoriza_array(),
                'comision' => comision_array(),
                'labora_otra_empresa' => labora_otra_empresa_array(),
                'codsuc' => $codsuc,
            ];

            $formulario = FormularioDinamico::where('name', 'mercurio472')->first();
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
        } catch (\Throwable $e) {
            $salida = $this->handleException($e, request());
        }

        return response()->json($salida);
    }

    public function buscarSolicitudes($estado = '')
    {
        $documento = $this->user['documento'];
        if (empty($estado)) {
            $mercurio47 = $this->db->inQueryAssoc("SELECT * FROM mercurio47 WHERE documento='{$documento}' AND estado IN('T','D','P','A','X') ORDER BY id, estado DESC");
        } else {
            $mercurio47 = $this->db->inQueryAssoc("SELECT * FROM mercurio47 WHERE documento='{$documento}' AND estado='{$estado}' ORDER BY id DESC");
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
            $mercurio47[$ai]['cantidad_eventos'] = $rqs['cantidad'];
            $mercurio47[$ai]['fecha_ultima_solicitud'] = $trayecto['fecsis'];
            $mercurio47[$ai]['estado_detalle'] = solicitud_estado_detalle($row['estado']);
            $mercurio47[$ai]['tipact_detalle'] = solicitud_tipo_actualizacion_detalle($row['tipact']);
        }

        return $mercurio47;
    }

    public function buscarTrabajadorSubsidio($cedtra)
    {
        $procesadorComando = new ApiSubsidio();
        $procesadorComando->send(
            [
                'servicio' => 'ComfacaEmpresas',
                'metodo' => 'informacion_trabajador',
                'params' => [
                    'cedtra' => $cedtra,
                ],
            ]
        );
        $salida = $procesadorComando->toArray();

        return ($salida['success'] == true) ? $salida['data'] : false;
    }

    public function infor()
    {
        try {
            $documento = $this->user['documento'];
            $datos = $this->buscarTrabajadorSubsidio($documento);
            $salida = [
                'success' => true,
                'data' => $datos,
                'msj' => 'OK',
            ];
        } catch (\Throwable $e) {
            $salida = $this->handleException($e, request());
        }

        return response()->json($salida);
    }

    function buscarEmpresaSubsidio($nit)
    {
        $empresaService = new EmpresaService;
        return $empresaService->buscarEmpresaSubsidio($nit);
    }

    /**
     * guardar function
     *
     * @changed [2024-12-00]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     *
     * @return void
     */
    public function guardar(Request $request)
    {
        $this->db->begin();
        try {
            $datosTrabajadorService = new DatosTrabajadorService;
            $asignarFuncionario = new AsignarFuncionario;
            $id = $request->input('id');
            $tipact = 'T';
            $usuario = $asignarFuncionario->asignar($this->tipopc, $this->user['codciu']);
            $params = [
                'documento' => $this->user['documento'],
                'usuario' => $usuario,
                'tipo' => $this->tipo,
                'coddoc' => $this->user['coddoc'],
                'fecsol' => date('Y-m-d'),
                'fecest' => date('Y-m-d'),
                'tipact' => $tipact,
            ];

            $logger = new Logger;
            $log = $logger->registrarLog(
                false,
                'Guarda actualizacion datos trabajador',
                json_encode($request->all())
            );

            $solicitud = null;
            if (is_null($id) || $id == '') {
                $data['id'] = null;
                $data['usuario'] = 2;
                $data['estado'] = 'T';
                $solicitud = $datosTrabajadorService->createByFormData($params);
                $id = $solicitud->getId();
            } else {
                $res = $datosTrabajadorService->updateByFormData($id, $params);
                if ($res == false) {
                    throw new DebugException('Error no se actualizo los datos', 301);
                }
            }

            $campos = Mercurio28::where('tipo', $this->tipo)->get();
            foreach ($campos as $mercurio28) {
                $valor = $request->input($mercurio28->campo);
                Mercurio33::create(
                    [
                        'log' => $log,
                        'tipo' => $this->tipo,
                        'coddoc' => $this->user['coddoc'],
                        'documento' => $this->user['documento'],
                        'campo' => $mercurio28->campo,
                        'antval' => ($valor) ? $valor : '@',
                        'valor' => $valor,
                        'estado' => 'P',
                        'usuario' => $usuario,
                        'actualizacion' => $id,
                    ]
                );
            }

            $solicitud = $datosTrabajadorService->findById($id);
            $salida = [
                'msj' => 'Proceso se ha completado con éxito',
                'success' => true,
                'data' => $solicitud->toArray(),
            ];
            $this->db->commit();
        } catch (\Throwable $e) {
            $this->db->rollBack();
            $salida = $this->handleException($e, $request);
        }
        return response()->json($salida);
    }

    public function borrar(Request $request)
    {
        $this->db->begin();
        try {
            $id = $request->input('id');
            $solicitud = Mercurio47::where('id', $id)->first();

            if ($solicitud) {
                if ($solicitud->getEstado() != 'T') {
                    Mercurio10::where('numero', $id)
                        ->where('tipopc', $this->tipopc)
                        ->delete();
                }

                Mercurio33::where('actualizacion', $id)->delete();
                Mercurio47::where('id', $id)->delete();
            }
            $salida = [
                'success' => true,
                'msj' => 'El registro se borro con éxito del sistema.',
            ];
            $this->db->commit();
        } catch (\Throwable $e) {
            $this->db->rollBack();
            $salida = $this->handleException($e, $request);
        }

        return response()->json($salida);
    }

    public function editarSolicitud(Request $request)
    {
        $this->db->begin();
        $logger = new Logger;
        $id_log = $logger->registrarLog(false, 'actualización datos basicos', '');
        try {
            $id = $request->input('id');

            $coddoc = $this->user['coddoc'];
            $documento = $this->user['documento'];
            $tipo = $this->tipo;

            $solicitud = $this->db->fetchOne("SELECT * FROM mercurio47 WHERE id='{$id}' and documento='{$documento}'");
            if (! $solicitud) {
                throw new DebugException('Error la solicitud no es correcta para continuar.', 501);
            }

            $campos = $this->db->inQueryAssoc("SELECT * FROM mercurio28 WHERE tipo='{$tipo}'");
            foreach ($campos as $mercurio28) {
                $valor = $request->input($mercurio28['campo']);
                if (empty($valor)) {
                    continue;
                }

                $mercurio33 = $this->db->fetchOne("SELECT * FROM mercurio33 WHERE documento = '{$documento}' and actualizacion = '{$id}' and campo = '{$mercurio28['campo']}'");
                if ($mercurio33) {

                    Mercurio33::where('id', $mercurio33['id'])
                        ->where('documento', $documento)
                        ->update(['valor' => $valor]);
                } else {
                    $mercurio33 = new Mercurio33;
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
                    $mercurio33->setEstado('P');
                    $mercurio33->setUsuario($solicitud['usuario']);
                    $mercurio33->setActualizacion($id);
                    $mercurio33->save();
                }
            }

            $salida = [
                'msj' => 'Proceso se ha completado con éxito',
                'success' => true,
                'id' => $id,
            ];
            $this->db->commit();
        } catch (\Throwable $e) {
            $this->db->rollBack();
            $salida = $this->handleException($e, $request);
        }

        return response()->json($salida);
    }

    function archivosRequeridos($mercurio47)
    {
        $archivos = [];
        $mercurio13 = Mercurio13::where('tipopc', $this->tipopc)->get();

        $mercurio10 = Mercurio10::where('numero', $mercurio47->getId())
            ->where('tipopc', $this->tipopc)
            ->orderBy('item', 'desc')
            ->first();

        $corregir = false;
        if ($mercurio10 && $mercurio10->getEstado() == 'D') {
            $campos = $mercurio10->getCamposCorregir();
            $corregir = explode(';', $campos);
        }

        foreach ($mercurio13 as $m13) {
            $m12 = Mercurio12::where('coddoc', $m13->getCoddoc())->first();

            $mercurio37 = Mercurio37::where('tipopc', $this->tipopc)
                ->where('numero', $mercurio47->getId())
                ->where('coddoc', $m13->getCoddoc())
                ->first();

            $corrige = false;
            if ($corregir) {
                if (in_array($m12->getCoddoc(), $corregir)) {
                    $corrige = true;
                }
            }
            $obliga = ($m13->getObliga() == 'S') ? "<br><small class='text-danger'>Obligatorio</small>" : '';
            $archivo = new \stdClass;
            $archivo->obliga = $obliga;
            $archivo->id = $mercurio47->getId();
            $archivo->coddoc = $m13->getCoddoc();
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
            'mercurio13' => $mercurio13,
        ])->render();

        return $html;
    }

    public function reloadArchivos(Request $request)
    {
        try {
            $documento = $this->user['documento'];
            $id = $request->input('id');
            $mercurio47 = Mercurio47::where('id', $id)->where('documento', $documento)->first();
            if (! $mercurio47) {
                throw new DebugException('No se requiere de ninguna acción', 501);
            } else {
                $salida = [
                    'documentos_adjuntos' => $this->archivosRequeridos($mercurio47),
                    'success' => true,
                ];
            }
        } catch (\Throwable $e) {
            $salida = $this->handleException($e, $request);
            $salida['documentos_adjuntos'] = [];
        }

        return response()->json($salida);
    }

    public function borrarArchivo(Request $request)
    {
        $this->db->begin();
        try {
            $numero = $request->input('id');
            $coddoc = $request->input('coddoc');
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
            $this->db->commit();
        } catch (\Throwable $e) {
            $this->db->rollBack();
            $response = $this->handleException($e, $request);
        }

        return response()->json($response);
    }

    /**
     * guardarArchivo function
     *
     * @return void
     */
    public function guardarArchivo(Request $request)
    {
        $this->db->begin();
        try {
            $id = $request->input('id');
            $coddoc = $request->input('coddoc');

            $guardarArchivoService = new GuardarArchivoService(
                [
                    'tipopc' => $this->tipopc,
                    'coddoc' => $coddoc,
                    'id' => $id,
                ]
            );
            $mercurio37 = $guardarArchivoService->main();
            $response = [
                'success' => true,
                'msj' => 'Ok archivo procesado',
                'data' => $mercurio37->getArray(),
            ];
            $this->db->commit();
        } catch (\Throwable $e) {
            $this->db->rollBack();
            $response = $this->handleException($e, $request);
        }

        return response()->json($response);
    }

    /**
     * enviarCaja function
     *
     * @return void
     */
    public function enviarCaja(Request $request)
    {
        $this->db->begin();
        try {
            $id = $request->input('id');
            $datosService = new DatosTrabajadorService;

            $asignarFuncionario = new AsignarFuncionario;
            $usuario = $asignarFuncionario->asignar($this->tipopc, $this->user['codciu']);
            $datosService->enviarCaja(new SenderValidationCaja, $id, $usuario);


            $salida = [
                'success' => true,
                'msj' => 'El envio de la solicitud se ha completado con éxito',
            ];
            $this->db->commit();
        } catch (\Throwable $e) {
            $this->db->rollBack();
            $salida = $this->handleException($e, $request);
        }

        return response()->json($salida);
    }

    public function getTiposDocumentos()
    {
        return [
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
            9 => 'CB',
        ];
    }

    public function descargarFormulario($id)
    {
        $this->setResponse('view');
        $documento = $this->user['documento'];
        $mercurio47 = Mercurio47::where('id', $id)->where('documento', $documento)->first();
        if (! $mercurio47) {
        } else {
            $campos = new \stdClass;
            $mercurio33 = $this->db->inQueryAssoc("SELECT * FROM mercurio33 WHERE actualizacion='{$id}'");
            foreach ($mercurio33 as $ai => $row) {
                $campos->$row['campo'] = $row['valor'];
            }
        }

        $procesadorComando = new ApiSubsidio();
        $procesadorComando->send(
            [
                'servicio' => 'ComfacaAfilia',
                'metodo' => 'parametros_trabajadores',
            ]
        );
        $datos_captura = $procesadorComando->toArray();
        $_bancos = [];
        foreach ($datos_captura['codigo_cuenta'] as $data) {
            $_bancos[$data['codcue']] = $data['detalle'];
        }

        $procesadorComando = new ApiSubsidio();
        $procesadorComando->send(
            [
                'servicio' => 'ComfacaAfilia',
                'metodo' => 'listar_ciudades_departamentos',
            ]
        );
        $salida = $procesadorComando->toArray();

        $_departamentos = [];
        foreach ($salida['departamentos'] as $data) {
            $_departamentos["{$data['coddep']}"] = $data['detdep'];
        }

        $_codzon = [];
        foreach ($salida['zonas'] as $data) {
            $_codzon["{$data['codzon']}"] = $data['detzon'];
        }

        $_codciu = [];
        foreach ($salida['ciudades'] as $data) {
            $_codciu["{$data['codciu']}"] = $data['detciu'];
        }

        $rqs = $this->buscarTrabajadorSubsidio($documento);
        $trabajador = (count($rqs['data']) > 0) ? $rqs['data'] : false;

        $pdf = new TCPDF('P', 'mm', 'Letter');
        $pdf->AddPage();
        $pdf->SetTextColor(0);
        $pdf->SetFont('Arial', '', 9);
        $pdf->Image(base_path() . 'public/docs/formulario_mercurio/fomulario_actualizacion_trabajador_parte_1.jpeg', 0, 0, '216', '280');
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

        // telefono
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

        // direccion comercial
        $pdf->MultiCell(195, 5, '', 0, 'L', 0);
        $pdf->AddPage();
        $pdf->Image('public/docs/formulario_mercurio/fomulario_actualizacion_trabajador_parte_2.jpeg', 0, 0, '216', '280');

        ob_end_clean();
        $pdf->Output('formulario_afiliacion.pdf', 'D');
    }

    public function renderTable($estado = '')
    {
        try {
            $datosTrabajadorService = new DatosTrabajadorService;
            $html = view(
                'mercurio/actualizadatostra/tmp/solicitudes',
                [
                    'path' => base_path(),
                    'solicitudes' => $datosTrabajadorService->findAllByEstado($estado),
                ]
            )->render();

            $this->setResponse('view');
            return $this->renderText($html);
        } catch (\Throwable $e) {
            $salida = $this->handleException($e, request());
            return response()->json($salida);
        }
    }

    public function searchRequest($id)
    {
        try {
            if (is_null($id)) {
                throw new DebugException('Error no hay solicitud a buscar', 301);
            }
            $documento = $this->user['documento'];
            $coddoc = $this->user['coddoc'];

            $mmercurio47 = Mercurio47::where('id', $id)
                ->where('documento', $documento)
                ->where('coddoc', $coddoc)
                ->first();

            if ($mmercurio47 == false) {
                throw new DebugException('Error la solicitud no está disponible para acceder.', 301);
            } else {
                $solicitud = $mmercurio47->getArray();
            }
            $data = [];
            $data = Mercurio33::where('actualizacion', $mmercurio47->getId())->get()->pluck('valor', 'campo')->toArray();

            $solicitud = array_merge($data, $solicitud);
            $salida = [
                'success' => true,
                'data' => $solicitud,
                'msj' => 'OK',
            ];
        } catch (\Throwable $e) {
            $salida = $this->handleException($e, request());
        }

        return response()->json($salida);
    }

    public function consultaDocumentos($id)
    {
        try {
            $documento = $this->user['documento'];
            $coddoc = $this->user['coddoc'];

            $datosTrabajadorService = new DatosTrabajadorService;
            $mtrabajador = Mercurio47::where('id', $id)
                ->where('documento', $documento)
                ->where('coddoc', $coddoc)
                ->where('tipact', 'T')
                ->first();
            if ($mtrabajador == false) {
                throw new DebugException('Error no se puede identificar el propietario de la solicitud', 301);
            }

            $salida = [
                'success' => true,
                'data' => $datosTrabajadorService->dataArchivosRequeridos($mtrabajador),
                'msj' => 'OK',
            ];
        } catch (\Throwable $e) {
            $salida = $this->handleException($e, request());
        }

        return response()->json($salida);
    }

    public function seguimiento(Request $request)
    {
        try {
            $actualizaEmpresaService = new DatosTrabajadorService;
            $out = $actualizaEmpresaService->consultaSeguimiento($request->input('id'));
            $salida = [
                'success' => true,
                'data' => $out,
            ];
        } catch (\Throwable $e) {
            $salida = $this->handleException($e, $request);
        }
        return response()->json($salida);
    }
}
