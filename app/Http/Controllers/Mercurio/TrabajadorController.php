<?php

namespace App\Http\Controllers\Mercurio;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use App\Models\Mercurio01;
use App\Models\Mercurio31;
use App\Models\Mercurio37;
use App\Services\Entidades\TrabajadorService;
use App\Services\Utils\GuardarArchivoService;
use App\Services\Utils\AsignarFuncionario;
use App\Services\Utils\Comman;
use App\Services\Utils\SenderValidationCaja;
use Illuminate\Http\Request;

class TrabajadorController extends ApplicationController
{

    protected $db;
    protected $user;
    protected $tipo;
    protected $tipopc = '1';

    public function __construct()
    {
        $this->db = DbBase::rawConnect();
        $this->user = session()->has('user') ? session('user') : null;
        $this->tipo = session()->has('tipo') ? session('tipo') : null;
    }

    /**
     * GET /trabajador/index (Opcional, placeholder)
     */
    public function indexAction()
    {
        return view('mercurio/trabajador/index', [
            'tipo' => $this->tipo,
            'documento' => $this->documento,
            'title' => 'Afiliación de trabajadores'
        ]);
    }

    /**
     * POST /trabajador/valide_nit
     * Valida existencia y estado de empresa vía API Comman
     */
    public function valideNitAction(Request $request)
    {
        $this->setResponse('ajax');
        try {
            $nit = $this->clp($request, 'nit');
            if (!$nit) throw new DebugException('El nit es requerido', 422);

            $ps = Comman::Api();
            $ps->runCli([
                'servicio' => 'ComfacaEmpresas',
                'metodo' => 'informacion_empresa',
                'params' => ['nit' => $nit]
            ]);
            $datos = $ps->toArray();

            if (!isset($datos['data']) || count($datos['data']) == 0) {
                $response = ['success' => false, 'msj' => 'El nit no existe'];
            } elseif (($datos['data']['estado'] ?? null) === 'I') {
                $response = ['success' => false, 'msj' => 'La empresa esta inactiva no puede crear nuevos trabajadores'];
            } else {
                $response = ['success' => true, 'msj' => '', 'data' => $datos['data']['razsoc'] ?? null];
            }
            return $this->renderObject($response);
        } catch (DebugException $e) {
            return $this->renderObject([
                'success' => false,
                'msj' => $e->getMessage()
            ]);
        }
    }

    /**
     * POST /trabajador/borrar_archivo
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

            $response = ['success' => true, 'msj' => 'El archivo se borro de forma correcta'];
        } catch (\Exception $e) {
            $response = ['success' => false, 'msj' => $e->getMessage()];
        }
        return $this->renderObject($response);
    }

    /**
     * POST /trabajador/guardar_archivo
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

            $response = [
                'success' => true,
                'msj' => 'Ok archivo procesado',
                'data' => method_exists($mercurio37, 'getArray') ? $mercurio37->getArray() : null,
            ];
        } catch (\Exception $e) {
            $response = ['success' => false, 'msj' => $e->getMessage()];
        }
        return $this->renderObject($response);
    }

    /**
     * POST /trabajador/traer_trabajador
     */
    public function traerTrabajadorAction(Request $request)
    {
        $this->setResponse('ajax');
        $cedtra = $this->clp($request, 'cedtra');
        $nit = $this->clp($request, 'nit');

        $datos_trabajador = [];

        $ps = Comman::Api();
        $ps->runCli([
            'servicio' => 'ComfacaEmpresas',
            'metodo' => 'informacion_trabajador',
            'params' => [
                'cedtra' => $cedtra
            ]
        ]);

        $out = $ps->toArray();
        if (($out['success'] ?? false) && isset($out['data'])) {
            $datos_trabajador = $out['data'];
        }

        $mercurio31 = new Mercurio31($datos_trabajador);
        $mercurio31->setLog('0');

        $response = [];
        $response['multi'] = false;
        if ($mercurio31->getNit() != $nit) $response['multi'] = true;

        $response['flag'] = true;
        if ($mercurio31->getNit() == $nit && $mercurio31->getEstado() == 'A') {
            $response['flag'] = false;
            $response['msg'] = 'El afiliado ya se encuentra registrado o Activo con la misma empresa.';
        }

        if ($mercurio31->getCedtra() == '') {
            $mercurio31 = Mercurio31::where('cedtra', $cedtra)->first() ?: new Mercurio31();
        }

        $response['data'] = $mercurio31->toArray();
        return $this->renderObject($response);
    }

    /**
     * POST /trabajador/enviar_caja
     */
    public function enviarCajaAction(Request $request)
    {
        $this->setResponse('ajax');
        try {
            $id = $this->clp($request, 'id');

            $trabajadorService = new TrabajadorService();


            $asignarFuncionario = new AsignarFuncionario();
            // Nota: getActUser reemplazado por datos del request o autent. Ajustar si hay SessionCookies
            $codciu = $this->clp($request, 'codciu');
            $usuario = $asignarFuncionario->asignar($this->tipopc, $codciu);

            $trabajadorService->enviarCaja(new SenderValidationCaja(), $id, $usuario); // TODO: importar/clase correcta si existe


            $salida = ['success' => true, 'msj' => 'El envio de la solicitud se ha completado con éxito'];
        } catch (\Exception $e) {
            $salida = ['success' => false, 'msj' => $e->getMessage()];
        }
        return $this->renderObject($salida);
    }

    /**
     * GET /trabajador/seguimiento/{id}
     */
    public function seguimientoAction($id)
    {
        $this->setResponse('ajax');
        try {
            $trabajadorService = new TrabajadorService();
            $out = $trabajadorService->consultaSeguimiento($id);
            $salida = ['success' => true, 'data' => $out];
        } catch (\Exception $e) {
            $salida = ['success' => false, 'msj' => $e->getMessage()];
        }
        return $this->renderObject($salida);
    }
}
