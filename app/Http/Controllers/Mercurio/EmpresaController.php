<?php

namespace App\Http\Controllers\Mercurio;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Library\Collections\ParamsEmpresa;
use App\Library\Collections\ParamsTrabajador;
use App\Models\Adapter\DbBase;
use App\Models\Gener09;
use App\Models\Gener18;
use App\Models\Mercurio07;
use App\Models\Mercurio10;
use App\Models\Mercurio30;
use App\Models\Mercurio31;
use App\Models\Mercurio37;
use App\Models\Subsi54;
use App\Models\Tranoms;
use App\Services\Entidades\EmpresaService;
use App\Services\FormulariosAdjuntos\EmpresaAdjuntoService;
use App\Services\Utils\AsignarFuncionario;
use App\Services\Utils\Comman;
use App\Services\Utils\GuardarArchivoService;
use App\Services\Utils\SenderValidationCaja;
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
        $this->user = session()->has('user') ? session('user') : null;
        $this->tipo = session()->has('tipo') ? session('tipo') : null;
    }

    protected $tipopc = '2';

    public function index()
    {
        return view('mercurio/empresa/index', [
            'tipo' => $this->tipo,
            'documento' => $this->user['documento'],
            'coddoc' => $this->user['coddoc'],
            'title' => 'Afiliación de empresas',
        ]);
    }

    public function renderTable(Request $request, Response $response, string $estado = '')
    {
        $this->setResponse('view');
        $empresaService = new EmpresaService;

        $html = view(
            'mercurio/empresa/tmp/solicitudes',
            [
                'path' => base_path(),
                'empresas' => $empresaService->findAllByEstado($estado),
            ]
        )->render();

        return $this->renderText($html);
    }

    /**
     * POST /empresa/buscar_empresa
     * Busca empresa en servicio de subsidio (independiente del estado)
     */
    public function buscarEmpresa(Request $request)
    {
        $this->setResponse('ajax');
        try {
            $nit = $this->clp($request, 'nit');
            if (! $nit) {
                throw new DebugException('El nit es requerido', 422);
            }

            $service = new EmpresaService;
            $salida = $service->buscarEmpresaSubsidio($nit);

            if ($salida === false) {
                $salida = ['success' => false, 'msj' => 'No se encontró la empresa en subsidio'];
            } else {
                $salida = ['success' => true, 'data' => $salida['data']];
            }
        } catch (DebugException $e) {
            $salida = ['success' => false, 'msj' => $e->getMessage()];
        }

        return $this->renderObject($salida);
    }

    /**
     * POST /empresa/guardar
     * Crea o actualiza la solicitud de afiliación de empresa
     */
    public function guardar(Request $request, Response $response)
    {
        $service = new EmpresaService;
        $this->db->begin();
        try {
            $id = $request->input('id', null);
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
                $empresa->getId()
            );

            $adjuntoService = new EmpresaAdjuntoService($empresa);
            $adjuntoService->setClaveCertificado($clave_certificado);
            $out = $adjuntoService->formulario()->getResult();
            (new GuardarArchivoService(
                [
                    'tipopc' => $this->tipopc,
                    'coddoc' => 1,
                    'id' => $empresa->getId(),
                ]
            ))->salvarDatos($out);

            $out = $adjuntoService->tratamientoDatos()->getResult();
            (new GuardarArchivoService(
                [
                    'tipopc' => $this->tipopc,
                    'coddoc' => 25,
                    'id' => $empresa->getId(),
                ]
            ))->salvarDatos($out);

            $out = $adjuntoService->cartaSolicitud()->getResult();
            (new GuardarArchivoService(
                [
                    'tipopc' => $this->tipopc,
                    'coddoc' => 24,
                    'id' => $empresa->getId(),
                ]
            ))->salvarDatos($out);

            $out = $adjuntoService->trabajadoresNomina()->getResult();
            (new GuardarArchivoService(
                [
                    'tipopc' => $this->tipopc,
                    'coddoc' => 11,
                    'id' => $empresa->getId(),
                ]
            ))->salvarDatos($out);

            ob_end_clean();

            $salida = [
                'success' => true,
                'msj' => 'Registro completado con éxito',
                'data' => $empresa->toArray(),
            ];

            $this->db->commit();
        } catch (DebugException $e) {
            $this->db->rollBack();
            $salida = [
                'success' => false,
                'msj' => $e->getMessage(),
            ];
        }

        return $this->renderObject($salida);
    }

    /**
     * POST /empresa/borrar_archivo
     */
    public function borrarArchivo(Request $request)
    {
        $this->setResponse('ajax');
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

    /**
     * POST /empresa/guardar_archivo
     */
    public function guardarArchivo(Request $request)
    {
        $this->setResponse('ajax');
        try {
            $id = $this->clp($request, 'id');
            $coddoc = $this->clp($request, 'coddoc');

            $guardarArchivoService = new GuardarArchivoService([
                'tipopc' => $this->tipopc,
                'coddoc' => $coddoc,
                'id' => $id,
            ]);
            $mercurio37 = $guardarArchivoService->main();

            $salida = [
                'success' => true,
                'msj' => 'Ok archivo procesado',
                'data' => method_exists($mercurio37, 'getArray') ? $mercurio37->getArray() : null,
            ];
        } catch (DebugException $e) {
            $salida = [
                'success' => false,
                'msj' => $e->getMessage(),
            ];
        }

        return $this->renderObject($salida);
    }

    /**
     * GET /empresa/archivos_requeridos/{id}
     */
    public function archivosRequeridos($id)
    {
        $this->setResponse('ajax');
        try {
            $service = new EmpresaService;
            $solicitud = $service->findById($id);
            if (! $solicitud) {
                throw new DebugException('No existe la solicitud', 404);
            }
            $data = $service->dataArchivosRequeridos($solicitud);

            return $this->renderObject(['success' => true, 'data' => $data]);
        } catch (DebugException $e) {
            return $this->renderObject(['success' => false, 'msj' => $e->getMessage()]);
        }
    }

    /**
     * POST /empresa/enviar_caja
     */
    public function enviarCaja(Request $request)
    {
        $this->setResponse('ajax');
        try {
            $id = (int) $this->clp($request, 'id');

            $asignarFuncionario = new AsignarFuncionario;
            $usuario = $asignarFuncionario->asignar($this->tipopc, $this->user['codciu']);

            $service = new EmpresaService;
            $service->enviarCaja(new SenderValidationCaja, $id, $usuario);

            $salida = [
                'success' => true,
                'msj' => 'El envío de la solicitud se ha completado con éxito',
            ];
        } catch (DebugException $e) {
            $salida = [
                'success' => false,
                'msj' => $e->getMessage(),
            ];
        }

        return $this->renderObject($salida);
    }

    /**
     * GET /empresa/seguimiento/{id}
     */
    public function seguimiento(Request $request, Response $response, int $id)
    {
        $this->setResponse('ajax');
        try {
            $service = new EmpresaService;
            $out = $service->consultaSeguimiento($id);
            $salida = [
                'success' => true,
                'data' => $out,
            ];
        } catch (DebugException $e) {
            $salida = [
                'success' => false,
                'msj' => $e->getMessage(),
            ];
        }

        return $this->renderObject($salida);
    }

    public function params()
    {
        $this->setResponse('ajax');
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
                if ($entity->getTipsoc() == '08') {
                    continue;
                }
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

            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
                [
                    'servicio' => 'ComfacaAfilia',
                    'metodo' => 'parametros_empresa',
                ]
            );

            $paramsEmpresa = new ParamsEmpresa;
            $paramsEmpresa->setDatosCaptura($procesadorComando->toArray());

            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
                [
                    'servicio' => 'ComfacaAfilia',
                    'metodo' => 'parametros_trabajadores',
                ],
                false
            );
            $paramsTrabajador = new ParamsTrabajador;
            $paramsTrabajador->setDatosCaptura($procesadorComando->toArray());

            $tipafi = (new Mercurio07)->getArrayTipos();
            $coddoc = $tipoDocumentos;
            $data = [
                'tipafi' => $tipafi,
                'coddoc' => $coddoc,
                'tipper' => (new Mercurio30)->getTipperArray(),
                'tipsoc' => $tipsoc,
                'calemp' => (new Mercurio30)->getCalempArray(),
                'codciu' => $codciu,
                'coddocrepleg' => $coddocrepleg,
                'codzon' => ParamsEmpresa::getZonas(),
                'codact' => ParamsEmpresa::getActividades(),
                'tipemp' => ParamsEmpresa::getTipoEmpresa(),
                'codcaj' => ParamsEmpresa::getCodigoCajas(),
                'ciupri' => ParamsEmpresa::getCiudades(),
                'ciunac' => ParamsEmpresa::getCiudades(),
                'tipsal' => (new Mercurio31)->getTipsalArray(),
                'autoriza' => ['S' => 'SI', 'N' => 'NO'],
                'ciupri' => ParamsEmpresa::getCiudades(),
            ];

            $salida = [
                'success' => true,
                'data' => $data,
                'msj' => 'OK',
            ];
        } catch (DebugException $err) {
            $salida = [
                'success' => false,
                'msj' => $err->getMessage(),
            ];
        }

        return $this->renderObject($salida);
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
        $this->setResponse('ajax');
        try {
            $nit = $this->clp($request, 'nit');
            if (! $nit) {
                throw new DebugException('El nit es requerido', 422);
            }

            $service = new EmpresaService;
            $dv = $service->digver($nit);

            $salida = [
                'success' => true,
                'digver' => $dv,
            ];
        } catch (DebugException $e) {
            $salida = [
                'success' => false,
                'msj' => $e->getMessage(),
            ];
        }

        return $this->renderObject($salida);
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
        } catch (DebugException $e) {
            $salida = [
                'success' => false,
                'msj' => $e->getMessage(),
            ];
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
        } catch (DebugException $e) {
            $salida = [
                'success' => false,
                'msj' => $e->getMessage(),
            ];
        }

        return $this->renderObject($salida);
    }

    /**
     * POST /empresa/borrar
     */
    public function borrar(Request $request)
    {
        $this->setResponse('ajax');
        try {
            $this->db->begin();
            $documento = $this->user['documento'] ?? '';
            $coddoc = $this->user['coddoc'] ?? '';

            $id = $this->clp($request, 'id');

            $m30 = (new Mercurio30)->findFirst("id='{$id}' and documento='{$documento}' and coddoc='{$coddoc}'");
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
        } catch (DebugException $e) {
            $this->db->rollBack();
            $salida = [
                'success' => false,
                'msj' => $e->getMessage(),
            ];
        }

        return $this->renderObject($salida);
    }

    public function valida(Request $request)
    {
        $this->setResponse('ajax');
        try {
            $nit = $this->clp($request, 'nit');
            $solicitud_previa = (new Mercurio30)->getCount('*', "conditions: estado IN('P','T','D') AND nit='{$nit}'");
            $empresa = false;

            $empresaService = new EmpresaService;
            $rqs = $empresaService->buscarEmpresaSubsidio($nit);
            if ($rqs) {
                $empresa = (count($rqs['data']) > 0) ? $rqs['data'] : false;
            }

            $response = [
                'success' => true,
                'solicitud_previa' => ($solicitud_previa > 0) ? true : false,
                'empresa' => $empresa,
            ];
        } catch (DebugException $err) {
            $response = [
                'success' => false,
                'msj' => $err->getMessage(),
            ];
        }

        return $this->renderObject($response);
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
        ];
    }

    public function miEmpresa()
    {
        $ps = Comman::Api();
        $ps->runCli(
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

        $ps = Comman::Api();
        $ps->runCli(
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
    }
}
