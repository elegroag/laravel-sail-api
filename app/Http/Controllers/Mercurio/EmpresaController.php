<?php

namespace App\Http\Controllers\Mercurio;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Library\Collections\ParamsEmpresa;
use App\Library\Collections\ParamsTrabajador;
use App\Models\Adapter\DbBase;
use App\Models\FormularioDinamico;
use App\Models\Gener09;
use App\Models\Gener18;
use App\Models\Mercurio10;
use App\Models\Mercurio30;
use App\Models\Mercurio37;
use App\Models\Subsi54;
use App\Models\Tranoms;
use App\Services\Entidades\EmpresaService;
use App\Services\FormulariosAdjuntos\EmpresaAdjuntoService;
use App\Services\Utils\AsignarFuncionario;
use App\Services\Utils\GuardarArchivoService;
use App\Services\Utils\SenderValidationCaja;
use App\Services\Api\ApiSubsidio;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EmpresaController extends ApplicationController
{
    protected $db;

    protected $user;

    protected $tipo;

    public function __construct()
    {
        $this->db = DbBase::rawConnect();
        $this->user = session('user') ?? null;
        $this->tipo = session('tipo') ?? null;
    }

    protected $tipopc = '2';

    public function index()
    {
        try {
            return view('mercurio/empresa/index', [
                'tipo' => $this->tipo,
                'documento' => $this->user['documento'],
                'coddoc' => $this->user['coddoc'],
                'title' => 'Afiliación de empresas',
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

    public function renderTable(Request $request, Response $response, string $estado = '')
    {
        try {
            $empresaService = new EmpresaService;
            $html = view(
                'mercurio/empresa/tmp/solicitudes',
                [
                    'path' => base_path(),
                    'empresas' => $empresaService->findAllByEstado($estado),
                ]
            )->render();

            $this->setResponse('view');
            return $this->renderText($html);
        } catch (\Throwable $e) {
            $salida = $this->handleException($e, $request);
            return $this->renderText($salida);
        }
    }

    /**
     * POST /empresa/buscar_empresa
     * Busca empresa en servicio de subsidio (independiente del estado)
     */
    public function buscarEmpresa(Request $request)
    {
        try {
            $nit = $request->input('nit');
            if (! $nit) {
                throw new DebugException('El nit es requerido', 422);
            }

            $service = new EmpresaService;
            $empresa_sisu = $service->buscarEmpresaSubsidio($nit);
            if (!$empresa_sisu || count($empresa_sisu) == 0) {
                throw new DebugException("No se encontró la empresa en subsidio", 500);
            } else {
                $salida = [
                    'success' => true,
                    'data' => $empresa_sisu
                ];
            }
        } catch (\Throwable $e) {
            $salida = $this->handleException($e, $request);
        }
        return response()->json($salida);
    }

    /**
     * POST /empresa/guardar
     * Crea o actualiza la solicitud de afiliación de empresa
     */
    public function guardar(Request $request, Response $response)
    {
        try {
            $this->db->begin();

            $service = new EmpresaService();
            $id = $request->input('id');
            $clave_certificado = $request->input('clave');
            $params = $this->serializeData($request);

            if (is_null($id)) {
                $empresa = $service->createByFormData($params);
            } else {
                $ok = $service->updateByFormData($id, $params);
                if ($ok === false) {
                    throw new DebugException('No se pudo actualizar la solicitud', 500);
                }
                $empresa = $service->findById($id);
            }

            $service->addTrabajadoresNomina(
                $request->input('tranoms'),
                $empresa->id
            );

            EmpresaAdjuntoService::generarAdjuntos(
                $empresa,
                $this->tipopc,
                $clave_certificado
            );

            $salida = [
                'success' => true,
                'msj' => 'Registro completado con éxito',
                'data' => $empresa->toArray(),
            ];

            $this->db->commit();
        } catch (\Throwable $e) {
            $this->db->rollBack();
            $salida = $this->handleException($e, $request);
        }
        return response()->json($salida);
    }

    /**
     * POST /empresa/borrar_archivo
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

    /**
     * POST /empresa/guardar_archivo
     */
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
            $salida = [
                'success' => true,
                'msj' => 'Ok archivo procesado',
                'data' => $mercurio37->toArray(),
            ];
        } catch (\Throwable $e) {
            $salida = $this->handleException($e, $request);
        }

        return response()->json($salida);
    }

    /**
     * GET /empresa/archivos_requeridos/{id}
     */
    public function archivosRequeridos($id)
    {
        try {
            $service = new EmpresaService;
            $solicitud = $service->findById($id);
            if (! $solicitud) {
                throw new DebugException('No existe la solicitud', 404);
            }
            $data = $service->dataArchivosRequeridos($solicitud);

            $salida = [
                'success' => true,
                'data' => $data,
            ];
        } catch (\Throwable $e) {
            $salida = $this->handleException($e, request());
        }
        return response()->json($salida);
    }

    /**
     * POST /empresa/enviar_caja
     */
    public function enviarCaja(Request $request)
    {
        try {
            $id = $request->input('id');
            $asignarFuncionario = new AsignarFuncionario;
            $usuario = $asignarFuncionario->asignar($this->tipopc, $this->user['codciu']);

            $service = new EmpresaService;
            $service->enviarCaja(new SenderValidationCaja, $id, $usuario);

            $salida = [
                'success' => true,
                'msj' => 'El envío de la solicitud se ha completado con éxito',
            ];
        } catch (\Throwable $e) {
            $salida = $this->handleException($e, $request);
        }
        return response()->json($salida);
    }

    public function seguimiento(Request $request)
    {
        try {
            $service = new EmpresaService;
            $out = $service->consultaSeguimiento($request->input('id'));
            $salida = [
                'success' => true,
                'data' => $out,
            ];
        } catch (\Throwable $e) {
            $salida = $this->handleException($e, $request);
        }

        return response()->json($salida);
    }

    public function params()
    {
        try {
            $coddoc = Gener18::whereNotIn('coddoc', ['7', '5', '2'])->pluck('detdoc', 'coddoc');
            $tipsoc = Subsi54::where('tipsoc', '!=', '08')->pluck('detalle', 'tipsoc');
            $codciu = Gener09::where("codzon", '>=', 18000)
                ->where("codzon", "<=", 19000)
                ->pluck('detzon', 'codzon');

            $procesadorComando = new ApiSubsidio();
            $procesadorComando->send(
                [
                    'servicio' => 'ComfacaAfilia',
                    'metodo' => 'parametros_empresa',
                ]
            );

            $paramsEmpresa = new ParamsEmpresa;
            $paramsEmpresa->setDatosCaptura($procesadorComando->toArray());

            $procesadorComando = new ApiSubsidio();
            $procesadorComando->send(
                [
                    'servicio' => 'ComfacaAfilia',
                    'metodo' => 'parametros_trabajadores',
                ]
            );
            $paramsTrabajador = new ParamsTrabajador;
            $paramsTrabajador->setDatosCaptura($procesadorComando->toArray());

            $coddocrepleg = tipo_document_repleg_detalle();
            unset($coddocrepleg['RC']);
            unset($coddocrepleg['TI']);

            $data = [
                'tipafi' => get_array_tipos(),
                'coddoc' => $coddoc,
                'tipper' => tipper_array(),
                'tipsoc' => $tipsoc,
                'calemp' => calemp_array(),
                'codciu' => $codciu,
                'coddocrepleg' => $coddocrepleg,
                'codzon' => ParamsEmpresa::getZonas(),
                'codact' => ParamsEmpresa::getActividades(),
                'tipemp' => ParamsEmpresa::getTipoEmpresa(),
                'codcaj' => ParamsEmpresa::getCodigoCajas(),
                'ciupri' => ParamsEmpresa::getCiudades(),
                'ciunac' => ParamsEmpresa::getCiudades(),
                'tipsal' => tipsal_array(),
                'autoriza' => autoriza_array(),
                'ciupri' => ParamsEmpresa::getCiudades(),
                'cartra' => ParamsTrabajador::getOcupaciones()
            ];

            $formulario = FormularioDinamico::where('name', 'mercurio30')->first();
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

    /**
     * GET /empresa/download_temp/{archivo}
     */
    public function downloadFile($archivo = '')
    {
        $this->setResponse('view');
        $fichero = public_path('temp/' . $archivo);
        if (! file_exists($fichero)) {
            throw new DebugException('Archivo no disponible', 404);
        }
        return $this->renderFile($fichero);
    }

    /**
     * GET /empresa/download_docs/{archivo}
     */
    public function downloadDocs($archivo = '')
    {
        $this->setResponse('view');
        $fichero = public_path('docs/formulario_mercurio/' . $archivo);
        if (! file_exists($fichero)) {
            throw new DebugException('Documento no disponible', 404);
        }

        return $this->renderFile($fichero);
    }

    /**
     * GET /empresa/digito_verification?nit=XXXX
     */
    public function digitoVerification(Request $request)
    {
        try {
            $nit = $request->input('nit');
            if (! $nit) {
                throw new DebugException('El nit es requerido', 422);
            }

            $service = new EmpresaService;
            $dv = $service->digver($nit);

            $salida = [
                'success' => true,
                'digver' => $dv,
            ];
        } catch (\Throwable $e) {
            $salida = $this->handleException($e, $request);
        }
        return response()->json($salida);
    }

    /**
     * GET /empresa/search_request/{id}
     */
    public function searchRequest(Request $request, Response $response, int $id)
    {
        $this->setResponse('ajax');
        try {
            if (empty($id)) {
                throw new DebugException('Error no hay solicitud a buscar', 422);
            }

            $documento = $this->user['documento'] ?? '';
            $coddoc = $this->user['coddoc'] ?? '';

            $solicitud = (new Mercurio30)->findFirst(" id='{$id}' AND documento='{$documento}' AND coddoc='{$coddoc}'");
            if ($solicitud == false) {
                throw new DebugException('Error la solicitud no está disponible para acceder.', 404);
            }
            $data = method_exists($solicitud, 'getArray') ? $solicitud->getArray() : [];

            $tranoms = Tranoms::where('request', $id)->get();
            $data['tranoms'] = $tranoms->toArray();
            $salida = [
                'success' => true,
                'data' => $data,
                'msj' => 'OK',
            ];
        } catch (\Throwable $e) {
            $salida = $this->handleException($e, $request);
        }

        return $this->renderObject($salida);
    }

    /**
     * GET /empresa/consulta_documentos/{id}
     */
    public function consultaDocumentos(Request $request, Response $response, int $id)
    {
        try {
            $documento = $this->user['documento'] ?? '';
            $coddoc = $this->user['coddoc'] ?? '';
            $service = new EmpresaService;

            $mempresa = Mercurio30::where('id', $id)
                ->where('documento', $documento)
                ->where('coddoc', $coddoc)
                ->whereNotIn('estado', ['I', 'X'])
                ->first();

            if ($mempresa == false) {
                throw new DebugException('Error no se puede identificar el propietario de la solicitud', 404);
            }

            $salida = [
                'success' => true,
                'data' => $service->dataArchivosRequeridos($mempresa),
                'msj' => 'OK',
            ];
        } catch (\Throwable $e) {
            $salida = $this->handleException($e, $request);
        }

        return $this->renderObject($salida);
    }

    /**
     * POST /empresa/borrar
     */
    public function borrar(Request $request)
    {
        try {
            $this->db->begin();
            $documento = $this->user['documento'] ?? '';
            $coddoc = $this->user['coddoc'] ?? '';

            $id = $request->input('id');

            $m30 = Mercurio30::where('id', $id)
                ->where('documento', $documento)
                ->where('coddoc', $coddoc)
                ->first();

            if ($m30) {
                if ($m30->getEstado() != 'T') {
                    Mercurio10::where('numero', $id)->where('tipopc', $this->tipopc)->delete();
                }
            }
            Mercurio30::where('id', $id)
                ->where('documento', $documento)
                ->where('coddoc', $coddoc)
                ->delete();

            $this->db->commit();
            $salida = [
                'success' => true,
                'msj' => 'Ok',
            ];
        } catch (\Throwable $e) {
            $this->db->rollBack();
            $salida = $this->handleException($e, $request);
        }
        return response()->json($salida);
    }

    public function valida(Request $request)
    {
        try {
            $nit = $request->input('nit');
            $solicitud_previa = Mercurio30::whereIn("estado", ['P', 'T', 'D'])->where("nit", $nit)->count();
            $empresa = false;
            $empresaService = new EmpresaService;
            $empresa_sisu = $empresaService->buscarEmpresaSubsidio($nit);
            if ($empresa_sisu) {
                $empresa = (count($empresa_sisu) > 0) ? $empresa_sisu : false;
            }
            $response = [
                'success' => true,
                'solicitud_previa' => ($solicitud_previa > 0) ? true : false,
                'empresa' => $empresa,
            ];
        } catch (\Throwable $e) {
            $response = $this->handleException($e, $request);
        }
        return response()->json($response);
    }

    public function serializeData(Request $request)
    {
        return [
            'nit' => $request->input('nit'),
            'tipdoc' => $request->input('tipdoc'),
            'razsoc' => $request->input('razsoc'),
            'sigla' => $request->input('sigla'),
            'digver' => $request->input('digver'),
            'calemp' => 'E',
            'cedrep' => $request->input('cedrep'),
            'repleg' => $request->input('repleg'),
            'direccion' => $request->input('direccion'),
            'codciu' => $request->input('codciu'),
            'codzon' => $request->input('codzon'),
            'telefono' => $request->input('telefono'),
            'celular' => $request->input('celular'),
            'fax' => $request->input('fax'),
            'email' => $request->input('email'),
            'codact' => $request->input('codact'),
            'fecini' => $request->input('fecini'),
            'tottra' => $request->input('tottra'),
            'valnom' => $request->input('valnom'),
            'tipsoc' => $request->input('tipsoc'),
            'dirpri' => $request->input('dirpri'),
            'ciupri' => $request->input('ciupri'),
            'telpri' => $request->input('telpri'),
            'celpri' => $request->input('celpri'),
            'emailpri' => $request->input('emailpri'),
            'tipemp' => $request->input('tipemp'),
            'tipper' => $request->input('tipper'),
            'priape' => $request->input('priape'),
            'segape' => $request->input('segape'),
            'prinom' => $request->input('prinom'),
            'segnom' => $request->input('segnom'),
            'matmer' => $request->input('matmer'),
            'codcaj' => $request->input('codcaj'),
            'coddocrepleg' => $request->input('coddocrepleg'),
            'log' => '0',
            'tipo' => $this->tipo,
            'coddoc' => $this->user['coddoc'],
            'documento' => $this->user['documento'],
            'barnotif' => $request->input('barnotif'),
            'barcomer' => $request->input('barcomer'),
        ];
    }

    public function miEmpresa()
    {
        try {
            $ps = new ApiSubsidio();
            $ps->send(
                [
                    'servicio' => 'ComfacaEmpresas',
                    'metodo' => 'informacion_empresa',
                    'params' => [
                        'nit' => $this->user['documento'],
                    ],
                ]
            );

            $empresa = $ps->toArray();
            if ($empresa['success'] == false) {
                set_flashdata('error', [
                    'msj' => 'Error al acceder al servicio de información de la empresa. Verifique que el NIT sea correcto.',
                    'code' => 401,
                ]);

                return redirect()->route('principal/index');
            }

            $ps = new ApiSubsidio();
            $ps->send(
                [
                    'servicio' => 'ComfacaAfilia',
                    'metodo' => 'parametros_empresa',
                ]
            );
            $paramsEmpresa = new ParamsEmpresa;
            $paramsEmpresa->setDatosCaptura($ps->toArray());

            $params = [
                'calemp' => ParamsEmpresa::getCalidadEmpresa(),
                'ciudad' => ParamsEmpresa::getCiudades(),
                'codzon' => ParamsEmpresa::getZonas(),
                'codigo_cajas' => ParamsEmpresa::getCodigoCajas(),
                'coddoc' => ParamsEmpresa::getTipoDocumentos(),
                'coddocrepleg' => ParamsEmpresa::getCodruaDocumentos(),
                'tipsoc' => ParamsEmpresa::getTipoSociedades(),
                'codact' => ParamsEmpresa::getActividades(),
                'tipper' => ParamsEmpresa::getTipoPersona(),
                'tipemp' => ParamsEmpresa::getTipoEmpresa(),
                'departamentos' => ParamsEmpresa::getDepartamentos(),
                'tipo_duracion' => ParamsEmpresa::getTipoDuracion(),
                'codind' => ParamsEmpresa::getCodigoIndice(),
                'paga_mes' => ParamsEmpresa::getPagaMes(),
                'forma_presentacion' => ParamsEmpresa::getFormaPresentacion(),
                'pymes' => ParamsEmpresa::getPymes(),
                'contratista' => ParamsEmpresa::getContratista(),
                'tipapo' => ParamsEmpresa::getTipoAportante(),
                'oficina' => ParamsEmpresa::getOficina(),
                'colegio' => ParamsEmpresa::getColegio(),
                'estado' => ['A' => 'ACTIVA', 'I' => 'INACTIVA', 'S' => 'SUSPENDIDA', 'D' => 'DESACTUALIZADA'],
                'autoriza' => ['S' => 'SI', 'N' => 'NO'],
            ];

            return view('mercurio/empresa/miempresa', [
                'empresa' => $empresa['data'],
                'trayectorias' => $empresa['data']['trayectoria'],
                'sucursales' => $empresa['data']['sucursales'],
                'parametros' => $params,
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
}
