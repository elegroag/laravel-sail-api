<?php

namespace App\Http\Controllers\Cajas;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use App\Models\Gener02;
use App\Models\Mercurio09;
use App\Models\Mercurio30;
use App\Models\Mercurio31;
use App\Models\Mercurio32;
use App\Models\Mercurio34;
use App\Models\Mercurio35;
use App\Services\Srequest;
use App\Services\Tag;
use App\Services\Utils\GeneralService;
use Exception;
use Illuminate\Http\Request;

class ReasignaController extends ApplicationController
{
    protected $db;

    protected $user;

    protected $tipfun;

    public function __construct()
    {
        $this->db = DbBase::rawConnect();
        $this->user = session('user') ?? null;
        $this->tipfun = session('tipfun') ?? null;
    }

    public function index()
    {
        $gener02 = Gener02::where('estado', 'A')->join('mercurio08', 'gener02.usuario', '=', 'mercurio08.usuario')->get();
        $data_usuarios = $gener02->pluck('nombre', 'usuario');
        $data_mercurio09 = Mercurio09::all()->pluck('detalle', 'tipopc');
        $accion = array('C' => 'CONSULTA', 'P' => 'PROCESO');

        return view('cajas.reasigna.index', [
            'title' => 'Consulta Reasigna',
            'data_usuarios' => $data_usuarios->toArray(),
            'data_mercurio09' => $data_mercurio09->toArray(),
            'accion' => $accion
        ]);
    }

    public function procesoReasignarMasivo(Request $request)
    {
        $this->setResponse('ajax');
        try {
            $tipopc = $request->input('tipopc_proceso');
            $usuori = $request->input('usuori');
            $usudes = $request->input('usudes');
            $fecini = $request->input('fecini');
            $fecfin = $request->input('fecfin');
            $model = '';
            if ($tipopc == 1) {
                $model = new Mercurio31;
            }
            if ($tipopc == 2) {
                $model = new Mercurio30; // No tiene el campo fecsol
            }
            if ($tipopc == 3) {
                $model = new Mercurio32;
            }
            if ($tipopc == 4) {
                $model = new Mercurio34;
            }
            if ($tipopc == 7) {
                $model = new Mercurio35;
            }
            $this->reasignaProceso($model, $usuori, $usudes, $fecini, $fecfin);

            $response = [
                'success' => true,
                'flag' => true,
                'msj' => 'Asignacion de solicitudes con exito',
            ];
        } catch (Exception $e) {
            $response = [
                'success' => false,
                'flag' => false,
                'msj' => $e->getMessage(),
            ];
        }

        return $this->renderObject($response, false);
    }

    function reasignaProceso($model, $usuori, $usudes, $fecini, $fecfin)
    {
        if (! $model) {
            return;
        }
        $tablaData = $model->find(" usuario ='{$usuori}' AND fecsol between '{$fecini}' AND '{$fecfin}' AND estado = 'P'");
        foreach ($tablaData as $mtabla) {
            $mtabla->setUsuario($usudes);
            $mtabla->save();
        }
    }

    public function traerDatos(Request $request)
    {
        $tipopc = $request->input('tipopc');
        $usuario = $request->input('usuario');

        $generalService = new GeneralService;
        $mercurio = $generalService->consultaTipopc($tipopc, 'alluser', '', $usuario);
        $entidad = $mercurio['datos'];
        $solicitudes = $entidad->map(function ($item) use ($tipopc) {
            $data = $item->toArray();
            switch ($tipopc) {
                case '1':
                case '9':
                case '10':
                case '11':
                case '12':
                    $documento = $item->cedtra;
                    $nombre = $item->nombre;
                    break;
                case '2':
                    $documento = $item->nit;
                    $nombre = $item->razsoc;
                    break;
                case '3':
                    $documento = $item->cedcon;
                    $nombre = $item->nombre;
                    break;
                case '4':
                    $documento = $item->numdoc;
                    $nombre = $item->nombre;
                    break;
                case '5':
                    $documento = $item->documento;
                    $nombre = $item->nombre;
                    break;
                case '7':
                    $documento = $item->cedtra;
                    $nombre = $item->nomtra;
                    break;
                case '8':
                    $documento = $item->codben;
                    $nombre = $item->nombre;
                    break;
                default:
                    $documento = $item->documento;
                    $nombre = $item->nombre;
                    break;
            }
            $data['documento'] = $documento;
            $data['nombre'] = $nombre;
            return $data;
        });
        return view('cajas.reasigna._tabla', [
            'tipopc' => $tipopc,
            'solicitudes' => $solicitudes,
        ]);
    }

    public function infor(Request $request)
    {
        $this->setResponse('ajax');
        $tipopc = $request->input('tipopc');
        $id = $request->input('id');
        $response = '';
        $generalService = new GeneralService;
        $result = $generalService->consultaTipopc($tipopc, 'info', $id, '');

        $response = $result['consulta'];
        $response .= "<div class='jumbotron'>";
        $response .= "<h1 class='display-4'>Cambio Responsable!</h1>";
        $response .= "<p class='lead'>Esta opcion permite cambiar el responsable</p>";
        $response .= "<hr class='my-4'>";
        $response .= "<p class='lead'>";
        $response .= "<div class='form-group'>";
        $response .= Tag::selectStatic(
            new Srequest([
                'name' => 'usuario_rea',
                'options' => $this->Gener02->find("usuario in (select usuario from mercurio08 where tipopc='$tipopc')"),
                'using' => 'usuario,nombre',
                'use_dummy' => true,
                'dummyValue' => '',
                'class' => 'form-control',
            ])
        );
        $response .= '</div>';
        $response .= "<button type='button' class='btn btn-warning btn-lg btn-block' onclick='cambiar_usuario($tipopc,$id)'>Cambiar Usuario Responsable</button>";
        $response .= '</p>';
        $response .= '</div>';

        return $this->renderText($response);
    }

    public function cambiarUsuario(Request $request)
    {
        try {

            $this->setResponse('ajax');
            $tipopc = $request->input('tipopc');
            $id = $request->input('id');
            $usuario = $request->input('usuario');

            $response = $this->db->begin();
            $generalService = new GeneralService;
            $result = $generalService->consultaTipopc($tipopc, 'one', $id, '');

            $mercurio = $result['datos'];
            $mercurio->setUsuario($usuario);
            if (! $mercurio->save()) {
                parent::setLogger($mercurio->getMessages());
                $this->db->rollback();
            }
            $this->db->commit();
            $response = parent::successFunc('Cambio de Usuario con Exito');

            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $response = parent::errorFunc('No se pudo realizar la opcion');

            return $this->renderObject($response, false);
        }
    }
}
