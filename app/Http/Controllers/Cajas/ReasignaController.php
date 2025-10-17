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
            $tipopc = $request->input('tipopc');
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
                'msj' => 'Asignacion de solicitudes con exito',
            ];
        } catch (Exception $e) {
            $response = [
                'success' => false,
                'msj' => $e->getMessage(),
            ];
        }

        return $this->renderObject($response, false);
    }

    function reasignaProceso($model, $usuori, $usudes, $fecini, $fecfin)
    {
        if (! $model) return;
        $tablaData = $model->whereRaw(" usuario='{$usuori}' AND fecsol BETWEEN '{$fecini}' AND '{$fecfin}' AND estado = 'P'")->get();
        foreach ($tablaData as $mtabla) {
            $mtabla->update([
                'usuario' => $usudes,
            ]);
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
        try {
            $tipopc = $request->input('tipopc');
            $id = $request->input('id');
            $generalService = new GeneralService;
            $out = $generalService->consultaTipopc($tipopc, 'info', $id, '');

            $data_usuarios = Gener02::join('mercurio08', 'gener02.usuario', '=', 'mercurio08.usuario')
                ->where('mercurio08.tipopc', $tipopc)
                ->pluck('gener02.nombre', 'gener02.usuario');

            $html = view('cajas.reasigna._info', [
                'tipopc' => $tipopc,
                'id' => $id,
                'consulta' => $out['consulta'],
                'data_usuarios' => $data_usuarios,
            ])->render();

            return response()->json([
                'success' => true,
                'html' => $html,
            ]);
        } catch (Exception $th) {
            return response()->json([
                'success' => false,
                'msj' => $th->getMessage(),
            ]);
        }
    }

    public function cambiarUsuario(Request $request)
    {
        try {

            $tipopc = $request->input('tipopc');
            $id = $request->input('id');
            $usuario = $request->input('usuario');

            $response = $this->db->begin();
            $generalService = new GeneralService;
            $out = $generalService->consultaTipopc($tipopc, 'one', $id, '');
            $solicitud = $out['datos'];
            if (! $solicitud) {
                throw new DebugException('No se encontro la solicitud', 501);
            }

            $solicitud->update([
                'usuario' => $usuario,
            ]);

            $this->db->commit();

            $response = [
                'success' => true,
                'msj' => 'Cambio de Usuario con Exito',
            ];
        } catch (DebugException $e) {
            $response = [
                'success' => false,
                'msj' => $e->getMessage(),
            ];
        }
        return response()->json($response);
    }
}
