<?php

namespace App\Http\Controllers\Mercurio;

use App\Exceptions\AuthException;
use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Library\Collections\ParamsEmpresa;
use App\Library\Collections\ParamsFacultativo;
use App\Library\Collections\ParamsTrabajador;
use App\Models\Adapter\DbBase;
use App\Models\FormularioDinamico;
use App\Models\Gener09;
use App\Models\Gener18;
use App\Models\Mercurio07;
use App\Models\Mercurio10;
use App\Models\Mercurio36;
use App\Models\Mercurio37;
use App\Models\Subsi54;
use App\Services\Entidades\FacultativoService;
use App\Services\FormulariosAdjuntos\FacultativoAdjuntoService;
use App\Services\FormulariosAdjuntos\Formularios;
use App\Services\Utils\AsignarFuncionario;
use App\Services\Utils\ChangeCuentaService;
use App\Services\Utils\GuardarArchivoService;
use App\Services\Utils\SenderValidationCaja;
use App\Services\Api\ApiSubsidio;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FacultativoController extends ApplicationController
{
    protected $tipopc = '10';

    /**
     * facultativoService variable
     *
     * @var FacultativoService
     */
    private $facultativoService;

    /**
     * asignarFuncionario variable
     *
     * @var AsignarFuncionario
     */
    protected $asignarFuncionario;

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
            return view('mercurio/facultativo/index', [
                'title' => 'Afiliación Facultativos',
                'tipo' => $this->tipo,
                'documento' => $this->user['documento'],
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

    public function actualizar(Request $request)
    {
        $this->setResponse('ajax');
        try {
            $id = $request->input('id');
            $params = $this->serializeData($request);

            $params['tipo'] = parent::getActUser('tipo');
            $params['coddoc'] = parent::getActUser('coddoc');
            $params['documento'] = parent::getActUser('documento');
            $params['estado'] = 'T';

            $this->facultativoService = new FacultativoService;
            $this->asignarFuncionario = new AsignarFuncionario;
            $params['usuario'] = $this->asignarFuncionario->asignar($this->tipopc, $this->user['codciu']);

            $this->facultativoService->updateByFormData($id, $params);
            // $this->facultativoService->endTransa();
            $pensionado = $this->facultativoService->findById($id);
            $data = $pensionado->getArray();

            $response = [
                'success' => true,
                'msj' => 'Registro completado con éxito',
                'data' => $data,
            ];
        } catch (\Throwable $e) {
            $response = $this->handleException($e, $request);
        }

        return response()->json($response);
    }

    /**
     * guardar function
     *
     * @changed [2023-12-01]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     *
     * @return void
     */
    public function guardar(Request $request)
    {
        $this->db->begin();

        try {
            $facultativoService = new FacultativoService;

            $id = $request->input('id');
            $clave_certificado = $request->input('clave');
            $params = $this->serializeData($request);

            $params['tipo'] = $this->tipo;
            $params['coddoc'] = $this->user['coddoc'];
            $params['documento'] = $this->user['documento'];

            $this->asignarFuncionario = new AsignarFuncionario;
            $params['usuario'] = $this->asignarFuncionario->asignar($this->tipopc, $this->user['codciu']);

            if (is_null($id) || $id == '') {
                $facultativo = $facultativoService->createByFormData($params);
            } else {
                $res = $facultativoService->updateByFormData($id, $params);
                if ($res == false) {
                    throw new DebugException('Error no se actualizo los datos', 301);
                }
                $facultativo = $facultativoService->findById($id);
            }

            $facultativoService->paramsApi();
            FacultativoAdjuntoService::generarAdjuntos($facultativo, $this->tipopc, $clave_certificado);
            $salida = [
                'success' => true,
                'msj' => 'Registro completado con éxito',
                'data' => $facultativo->toArray(),
            ];
            $this->db->commit();
        } catch (\Throwable $e) {
            $this->db->rollBack();
            $salida = $this->handleException($e, $request);
        }
        return response()->json($salida);
    }

    /**
     * serializeData function
     *
     * @return array
     */
    public function serializeData(Request $request)
    {
        return [
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
        ];
    }

    /**
     * valida function
     *
     * @return void
     */
    public function valida(Request $request)
    {
        try {

            $cedtra = $request->input('cedtra');
            $solicitud = Mercurio36::where('documento', $cedtra)->whereIn('estado', ['A', 'I'])->first();

            $solicitudPrevia = false;
            if ($solicitud) {
                $solicitudPrevia = $solicitud->toArray();
            }

            $procesadorComando = new ApiSubsidio();
            $procesadorComando->send(
                [
                    'servicio' => 'ComfacaEmpresas',
                    'metodo' => 'informacion_empresa',
                    'params' => [
                        'nit' => $cedtra,
                    ],
                ]
            );

            $empresa = null;
            $trabajador = null;

            $out = $procesadorComando->toArray();
            if ($out['success']) {
                $empresa = (count($out['data']) > 0) ? $out['data'] : false;
            }

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

            $out = $procesadorComando->toArray();
            if ($out['success']) {
                $trabajador = (count($out['data']) > 0) ? $out['data'] : false;
            }

            $response = [
                'success' => true,
                'solicitud_previa' => $solicitudPrevia,
                'empresa' => $empresa,
                'trabajador' => $trabajador,
            ];
        } catch (\Throwable $e) {
            $response = $this->handleException($e, $request);
        }

        return response()->json($response);
    }

    /**
     * borrarArchivo function
     *
     * @return void
     */
    public function borrarArchivo(Request $request)
    {
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
        } catch (\Throwable $e) {
            $response = $this->handleException($e, $request);
        }

        return response()->json($response);
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
        } catch (\Throwable $e) {
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
        try {
            $id = $request->input('id');
            $facultativoService = new FacultativoService;

            $asignarFuncionario = new AsignarFuncionario;
            $usuario = $asignarFuncionario->asignar($this->tipopc, $this->user['codciu']);

            $facultativoService->enviarCaja(new SenderValidationCaja, $id, $usuario);

            $salida = [
                'success' => true,
                'msj' => 'El envio de la solicitud se ha completado con éxito',
            ];
        } catch (\Throwable $e) {
            $salida = $this->handleException($e, $request);
        }

        return response()->json($salida);
    }

    public function descargar_formulario($id)
    {
        $mercurio36 = Mercurio36::where('id', $id)->first();

        $procesadorComando = new ApiSubsidio();
        $procesadorComando->send(
            [
                'servicio' => 'ComfacaAfilia',
                'metodo' => 'parametros_empresa',
            ]
        );

        $datos_captura = $procesadorComando->toArray();
        $paramsEmpresa = new ParamsEmpresa;
        $paramsEmpresa->setDatosCaptura($datos_captura);

        $time = strtotime('now');
        $file = "formulario_{$mercurio36->getNit()}_{$time}.pdf";
        $formularios = new Formularios;
        $formularios->facultativoAfiliacion(
            [
                'pensionado' => $mercurio36,
            ],
            $file
        );

        return response()->json([
            'success' => true,
            'name' => $file,
            'url' => 'facultitivo/downloadFile/' . $file,
        ]);
    }

    public function reloadArchivos(Request $request)
    {
        $facultativoService = new FacultativoService;
        try {
            $cedtra = $request->input('cedtra');
            $id = $request->input('id');

            $mercurio36 = Mercurio36::where('cedtra', $cedtra)->where('id', $id)->first();

            if (! $mercurio36) {
                throw new DebugException('La empresa no está disponible para notificar por email', 501);
            } else {

                $salida = [
                    'documentos_adjuntos' => $facultativoService->archivosRequeridos($mercurio36),
                    'success' => true,
                ];
            }
        } catch (\Throwable $e) {
            $salida = $this->handleException($e, $request);
            $salida['documentos_adjuntos'] = [];
        }

        return response()->json($salida);
    }

    /**
     * borrar function
     * cancelar la solicitud
     *
     * @return void
     */
    public function borrar(Request $request)
    {
        try {
            $user = $this->user;
            $documento = $user['documento'];
            $coddoc = $user['coddoc'];

            $id = $request->input('id');
            $m36 = Mercurio36::where('id', $id)->where('documento', $documento)->where('coddoc', $coddoc)->first();
            if ($m36) {
                if ($m36->getEstado() != 'T') {
                    Mercurio10::where('numero', $id)->where('tipopc', $this->tipopc)->delete();
                }
                Mercurio36::where('id', $id)->delete();
            }
            $salida = [
                'success' => true,
                'msj' => 'El registro se borro con éxito del sistema.',
            ];
        } catch (\Throwable $e) {
            $salida = $this->handleException($e, $request);
        }

        return response()->json($salida);
    }

    public function downloadFile($archivo = '')
    {
        $fichero = 'public/temp/' . $archivo;
        return $this->renderFile($fichero);
    }

    /**
     * params function
     *
     * @return void
     */
    public function params()
    {
        try {
            $mtipoDocumentos = new Gener18;
            $tipoDocumentos = [];

            foreach ($mtipoDocumentos->all() as $mtipo) {
                if ($mtipo->getCoddoc() == '7' || $mtipo->getCoddoc() == '2' || $mtipo->getCoddoc() == '3') {
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
                    'metodo' => 'parametros_empresa',
                ]
            );
            $paramsFacultativo = new ParamsFacultativo;
            $paramsFacultativo->setDatosCaptura($procesadorComando->toArray());

            $procesadorComando = new ApiSubsidio();
            $procesadorComando->send(
                [
                    'servicio' => 'ComfacaAfilia',
                    'metodo' => 'parametros_trabajadores',
                ]
            );
            $paramsTrabajador = new ParamsTrabajador;
            $paramsTrabajador->setDatosCaptura($procesadorComando->toArray());

            $mtipafi = ParamsTrabajador::getTipoAfiliado();
            $tipo_afiliados = [];
            foreach ($mtipafi as $key => $tipo) {
                if ($key == '63') {
                    $tipo_afiliados[$key] = $tipo;
                }
            }

            $data = [
                'resguardo_id' => ParamsTrabajador::getResguardos(),
                'pub_indigena_id' => ParamsTrabajador::getPueblosIndigenas(),
                'codban' => ParamsTrabajador::getBancos(),
                'codzon' => ParamsFacultativo::getZonas(),
                'codact' => ParamsFacultativo::getActividades(),
                'tipemp' => ParamsFacultativo::getTipoEmpresa(),
                'codcaj' => ParamsFacultativo::getCodigoCajas(),
                'ciupri' => ParamsFacultativo::getCiudades(),
                'tipcon' => ParamsTrabajador::getTipoContrato(),
                'cargo' => ParamsTrabajador::getOcupaciones(),
                'ciunac' => ParamsFacultativo::getCiudades(),
                'tipdoc' => $tipoDocumentos,
                'tipper' => tipper_array(),
                'tipsoc' => $tipsoc,
                'calemp' => calemp_array(),
                'codciu' => $codciu,
                'coddocrepleg' => $coddocrepleg,
                'sexo' => sexos_array(),
                'estciv' => estados_civiles_array(),
                'cabhog' => cabeza_hogar(),
                'captra' => capacidad_trabajar(),
                'tipdis' => tipo_discapacidad_array(),
                'nivedu' => nivel_educativo_array(),
                'rural' => es_rural(),
                'trasin' => es_sindicalizado(),
                'vivienda' => vivienda_array(),
                'tipafi' => $tipo_afiliados,
                'orisex' => orientacion_sexual_array(),
                'facvul' => vulnerabilidades_array(),
                'peretn' => pertenencia_etnica_array(),
                'labora_otra_empresa' => labora_otra_empresa_array(),
                'tippag' => tipo_pago_array(),
                'tipsal' => tipsal_array(),
                'tipcue' => tipo_cuenta_array(),
                'ruralt' => es_rural(),
                'autoriza' => autoriza_array(),
            ];

            $formulario = FormularioDinamico::where('name', 'mercurio36')->first();
            $componentes = $formulario->componentes()->get();
            $componentes = $componentes->map(function ($componente) use ($data) {
                $_componente = $componente->toArray();
                if (isset($data[$componente->name])) {
                    $_componente['data_source'] = $data[$componente->name];
                }
                $_componente['id'] = $componente->name;
                return $_componente;
            });

            $solicitante = Mercurio07::where('documento', $this->user['documento'])
                ->where('coddoc', $this->user['coddoc'])
                ->where('tipo', $this->tipo)
                ->first();

            $hoy = Carbon::now();
            $componentes['props'] = [
                'name' => null,
                'cedtra' => $solicitante->documento,
                'coddoc' => $solicitante->coddoc,
                'tipdoc' => $solicitante->coddoc,
                'tipo' => $solicitante->tipo,
                'email' => $solicitante->email,
                'codciu' => $solicitante->codciu,
                'fecsol' => $hoy->format('Y-m-d'),
            ];

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

    public function searchRequest($id)
    {

        try {
            if (is_null($id)) {
                throw new DebugException('Error no hay solicitud a buscar', 301);
            }
            $documento = $this->user['documento'];
            $coddoc = $this->user['coddoc'];

            $solicitud = Mercurio36::where('id', $id)->where('documento', $documento)->where('coddoc', $coddoc)->first();
            if ($solicitud == false) {
                throw new DebugException('Error la solicitud no está disponible para acceder.', 301);
            } else {
                $data = $solicitud->getArray();
            }
            $salida = [
                'success' => true,
                'data' => $data,
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
            $facultativoService = new FacultativoService;

            $sindepe = Mercurio36::where('id', $id)->where('documento', $documento)->where('coddoc', $coddoc)->first();
            if ($sindepe == false) {
                throw new DebugException('Error no se puede identificar el propietario de la solicitud', 301);
            }
            $salida = [
                'success' => true,
                'data' => $facultativoService->dataArchivosRequeridos($sindepe),
                'msj' => 'OK',
            ];
        } catch (\Throwable $e) {
            $salida = $this->handleException($e, request());
        }

        return response()->json($salida);
    }

    public function renderTable($estado = '')
    {
        try {

            $this->facultativoService = new FacultativoService;
            $html = view(
                'mercurio/facultativo/tmp/solicitudes',
                [
                    'path' => base_path(),
                    'facultativos' => $this->facultativoService->findAllByEstado($estado),
                ]
            )->render();

            $this->setResponse('view');
            return $this->renderText($html);
        } catch (\Throwable $e) {
            $salida = $this->handleException($e, request());

            return response()->json($salida);
        }
    }

    public function seguimiento(Request $request)
    {
        try {
            $facultativoService = new FacultativoService;
            $out = $facultativoService->consultaSeguimiento($request->input('id'));
            $salida = [
                'success' => true,
                'data' => $out,
            ];
        } catch (\Throwable $e) {
            $salida = $this->handleException($e, $request);
        }
        return response()->json($salida);
    }

    public function administrar_cuenta($id = '')
    {
        try {
            if ($id == '') {
                throw new AuthException('El id del la solicitud no está disponible.', 501);
            }

            $solicitud = Mercurio36::where('id', $id)->where('estado', 'A')->first();
            $request = new Request(
                [
                    'tipo' => 'F',
                    'coddoc' => $solicitud->tipdoc,
                    'documento' => $solicitud->cedtra,
                    'usuario' => $solicitud->priape . ' ' . $solicitud->segape . ' ' . $solicitud->prinom . ' ' . $solicitud->segnom,
                ]
            );

            $change = new ChangeCuentaService;
            if ($change->initializa($request)) {
                set_flashdata('success', [
                    'msj' => 'La administración de la cuenta se ha inicializado con éxito.',
                    'code' => 200,
                ]);

                return redirect('principal.index');
            }
        } catch (\Throwable $e) {
            $salida = $this->handleException($e, request());
            set_flashdata('error', [
                'msj' => $salida['msj'],
                'code' => $salida['code'],
            ]);

            return redirect()->route('principal/index');
        }
    }
}
