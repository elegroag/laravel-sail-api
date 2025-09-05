<?php

namespace App\Http\Controllers\Mercurio;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use App\Models\Mercurio01;
use App\Models\Mercurio30;
use App\Models\Mercurio37;
use App\Models\Mercurio10;
use App\Services\Entidades\EmpresaService;
use App\Services\FormulariosAdjuntos\DatosEmpresaService;
use App\Services\Utils\AsignarFuncionario;
use App\Services\Utils\Comman;
use App\Services\Utils\GuardarArchivoService;
use App\Services\Utils\SenderValidationCaja;
use App\Services\Utils\GeneralService;
use Illuminate\Http\Request;

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

    /**
     * Tipo de proceso caja (tipopc) para Empresa
     * 2: Empresa
     */
    protected $tipopc = '2';


    public function indexAction()
    {
        return view('mercurio/empresa/index', [
            'tipo' => $this->tipo,
            'documento' => $this->user['documento'],
            'title' => 'Afiliación de empresas'
        ]);
    }

    /**
     * POST /empresa/buscar_empresa
     * Busca empresa en servicio de subsidio (independiente del estado)
     */
    public function buscarEmpresaAction(Request $request)
    {
        $this->setResponse('ajax');
        try {
            $nit = $this->clp($request, 'nit');
            if (!$nit) throw new DebugException('El nit es requerido', 422);

            $service = new EmpresaService();
            $salida = $service->buscarEmpresaSubsidio($nit);
            if ($salida === false) {
                return $this->renderObject(['success' => false, 'msj' => 'No se encontró la empresa en subsidio']);
            }
            return $this->renderObject(['success' => true, 'data' => $salida['data'] ?? null]);
        } catch (\Exception $e) {
            return $this->renderObject(['success' => false, 'msj' => $e->getMessage()]);
        }
    }

    /**
     * POST /empresa/guardar
     * Crea o actualiza la solicitud de afiliación de empresa
     */
    public function guardarAction(Request $request)
    {
        $this->setResponse('ajax');
        $service = new EmpresaService();
        try {
            $id = $this->clp($request, 'id');
            $params = $request->all();

            if (empty($id)) {
                $saved = $service->createByFormData($params);
                if (!$saved) throw new DebugException('No se pudo crear la solicitud', 500);
                $solicitud = $service->findById($saved->getId());
            } else {
                $ok = $service->updateByFormData((int) $id, $params);
                if ($ok === false) throw new DebugException('No se pudo actualizar la solicitud', 500);
                $solicitud = $service->findById((int) $id);
            }

            // Generación de documentos adjuntos automáticos si aplica
            try {
                $service->paramsApi();
                $datosEmpresaService = new DatosEmpresaService([
                    'documento' => session()->has('user') ? (session('user')['documento'] ?? null) : null,
                    'coddoc' => session()->has('user') ? (session('user')['coddoc'] ?? null) : null,
                    'nit' => $this->clp($request, 'nit'),
                    'empresa' => $solicitud ? $solicitud->getArray() : [],
                    'campos' => $params,
                ]);
                $out = $datosEmpresaService->formulario();
                // Guardar adjunto generado
                (new GuardarArchivoService([
                    'tipopc' => $this->tipopc,
                    'coddoc' => 1,
                    'id' => $solicitud->getId(),
                ]))->salvarDatos($out);
            } catch (\Throwable $t) {
                // no bloquear el flujo si falla la generación del PDF
            }

            return $this->renderObject([
                'success' => true,
                'msj' => 'Proceso se ha completado con éxito',
                'data' => $solicitud ? $solicitud->getArray() : null
            ]);
        } catch (\Exception $e) {
            return $this->renderObject(['success' => false, 'msj' => $e->getMessage()]);
        }
    }

    /**
     * POST /empresa/borrar_archivo
     */
    public function borrarArchivoAction(Request $request)
    {
        $this->setResponse('ajax');
        try {
            $numero = $this->clp($request, 'id');
            $coddoc = $this->clp($request, 'coddoc');

            $mercurio01 = (new Mercurio01())->findFirst();
            $mercurio37 = (new Mercurio37())->findFirst("tipopc='{$this->tipopc}' and numero='{$numero}' and coddoc='{$coddoc}'");

            if ($mercurio01 && $mercurio37) {
                $filepath = base_path('') . '/' . ltrim($mercurio01->getPath() . $mercurio37->getArchivo(), '/');
                if (file_exists($filepath)) {
                    @unlink($filepath);
                }
                (new Mercurio37())->deleteAll("tipopc='{$this->tipopc}' and numero='{$numero}' and coddoc='{$coddoc}'");
            }

            return $this->renderObject(['success' => true, 'msj' => 'El archivo se borro de forma correcta']);
        } catch (\Exception $e) {
            return $this->renderObject(['success' => false, 'msj' => $e->getMessage()]);
        }
    }

    /**
     * POST /empresa/guardar_archivo
     */
    public function guardarArchivoAction(Request $request)
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

            return $this->renderObject([
                'success' => true,
                'msj' => 'Ok archivo procesado',
                'data' => method_exists($mercurio37, 'getArray') ? $mercurio37->getArray() : null,
            ]);
        } catch (\Exception $e) {
            return $this->renderObject(['success' => false, 'msj' => $e->getMessage()]);
        }
    }

    /**
     * GET /empresa/archivos_requeridos/{id}
     */
    public function archivosRequeridosAction($id)
    {
        $this->setResponse('ajax');
        try {
            $service = new EmpresaService();
            $solicitud = $service->findById($id);
            if (!$solicitud) throw new DebugException('No existe la solicitud', 404);
            $data = $service->dataArchivosRequeridos($solicitud);
            return $this->renderObject(['success' => true, 'data' => $data]);
        } catch (\Exception $e) {
            return $this->renderObject(['success' => false, 'msj' => $e->getMessage()]);
        }
    }

    /**
     * POST /empresa/enviar_caja
     */
    public function enviarCajaAction(Request $request)
    {
        $this->setResponse('ajax');
        try {
            $id = (int) $this->clp($request, 'id');
            $codciu = $this->clp($request, 'codciu');

            $asignarFuncionario = new AsignarFuncionario();
            $usuario = $asignarFuncionario->asignar($this->tipopc, $codciu);

            $service = new EmpresaService();
            $service->enviarCaja(new SenderValidationCaja(), $id, $usuario);

            return $this->renderObject(['success' => true, 'msj' => 'El envío de la solicitud se ha completado con éxito']);
        } catch (\Exception $e) {
            return $this->renderObject(['success' => false, 'msj' => $e->getMessage()]);
        }
    }

    /**
     * GET /empresa/seguimiento/{id}
     */
    public function seguimientoAction($id)
    {
        $this->setResponse('ajax');
        try {
            $service = new EmpresaService();
            $out = $service->consultaSeguimiento($id);
            return $this->renderObject(['success' => true, 'data' => $out]);
        } catch (\Exception $e) {
            return $this->renderObject(['success' => false, 'msj' => $e->getMessage()]);
        }
    }

    /**
     * POST /empresa/params
     */
    public function paramsAction()
    {
        $this->setResponse('ajax');
        try {
            $service = new EmpresaService();
            $service->paramsApi();
            return $this->renderObject(['success' => true]);
        } catch (DebugException $e) {
            return $this->renderObject(['success' => false, 'msj' => $e->getMessage()]);
        }
    }

    /**
     * GET /empresa/download_temp/{archivo}
     */
    public function downloadFileAction($archivo = '')
    {
        $this->setResponse('view');
        $fichero = public_path('temp/' . $archivo);
        if (!file_exists($fichero)) {
            throw new DebugException('Archivo no disponible', 404);
        }
        return $this->renderFile($fichero);
    }

    /**
     * GET /empresa/download_docs/{archivo}
     */
    public function downloadDocsAction($archivo = '')
    {
        $this->setResponse('view');
        $fichero = public_path('docs/formulario_mercurio/' . $archivo);
        if (!file_exists($fichero)) {
            throw new DebugException('Documento no disponible', 404);
        }
        return $this->renderFile($fichero);
    }

    /**
     * GET /empresa/digito_verification?nit=XXXX
     */
    public function digitoVerificationAction(Request $request)
    {
        $this->setResponse('ajax');
        try {
            $nit = $this->clp($request, 'nit');
            if (!$nit) throw new DebugException('El nit es requerido', 422);
            $service = new EmpresaService();
            $dv = $service->digver($nit);
            return $this->renderObject(['success' => true, 'dv' => $dv]);
        } catch (\Exception $e) {
            return $this->renderObject(['success' => false, 'msj' => $e->getMessage()]);
        }
    }

    /**
     * GET /empresa/search_request/{id}
     */
    public function searchRequestAction($id)
    {
        $this->setResponse('ajax');
        try {
            if (empty($id)) throw new DebugException('Error no hay solicitud a buscar', 422);
            $user = session()->has('user') ? session('user') : [];
            $documento = (string) ($user['documento'] ?? '');
            $coddoc = (string) ($user['coddoc'] ?? '');

            $solicitud = (new Mercurio30())->findFirst(" id='{$id}' AND documento='{$documento}' AND coddoc='{$coddoc}'");
            if ($solicitud == false) {
                throw new DebugException('Error la solicitud no está disponible para acceder.', 404);
            }
            $data = method_exists($solicitud, 'getArray') ? $solicitud->getArray() : [];
            return $this->renderObject(['success' => true, 'data' => $data, 'msj' => 'OK']);
        } catch (\Exception $e) {
            return $this->renderObject(['success' => false, 'msj' => $e->getMessage()]);
        }
    }

    /**
     * GET /empresa/consulta_documentos/{id}
     */
    public function consultaDocumentosAction($id)
    {
        $this->setResponse('ajax');
        try {
            $user = session()->has('user') ? session('user') : [];
            $documento = (string) ($user['documento'] ?? '');
            $coddoc = (string) ($user['coddoc'] ?? '');
            $service = new EmpresaService();

            $mempresa = (new Mercurio30())->findFirst(" id='{$id}' AND documento='{$documento}' AND coddoc='{$coddoc}' AND estado NOT IN('I','X')");
            if ($mempresa == false) throw new DebugException('Error no se puede identificar el propietario de la solicitud', 404);

            $salida = [
                'success' => true,
                'data' => $service->dataArchivosRequeridos($mempresa),
                'msj' => 'OK'
            ];
            return $this->renderObject($salida);
        } catch (\Exception $e) {
            return $this->renderObject(['success' => false, 'msj' => $e->getMessage()]);
        }
    }

    /**
     * POST /empresa/borrar
     */
    public function borrarAction(Request $request)
    {
        $this->setResponse('ajax');
        $generales = new GeneralService();
        $generales->startTrans('mercurio30');
        try {
            try {
                $user = session()->has('user') ? session('user') : [];
                $documento = (string) ($user['documento'] ?? '');
                $coddoc = (string) ($user['coddoc'] ?? '');
                $id = $this->clp($request, 'id');

                $m30 = (new Mercurio30())->findFirst("id='{$id}' and documento='{$documento}' and coddoc='{$coddoc}'");
                if ($m30) {
                    if ($m30->getEstado() != 'T') (new Mercurio10())->deleteAll("numero='{$id}' AND tipopc='{$this->tipopc}'");
                }
                (new Mercurio30())->deleteAll("id='{$id}' and documento='{$documento}' and coddoc='{$coddoc}'");
                $generales->finishTrans();
                return $this->renderObject(['success' => true, 'msj' => 'Ok']);
            } catch (\Exception $e) {
                $generales->errorTrans($e->getMessage());
            }
        } catch (\Exception $e) {
            return $this->renderObject(['success' => false, 'msj' => $e->getMessage()]);
        }
    }
}
