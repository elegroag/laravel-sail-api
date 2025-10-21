<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Controller;
use App\Exceptions\DebugException;
use App\Models\Adapter\DbBase;
use App\Models\Gener02;
use App\Models\Gener40;
use App\Models\Gener42;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class MenuController extends Controller
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
        $menu_items = MenuItem::select(
            DB::raw("menu_items.*"),
            'menu_tipos.is_visible',
            'menu_tipos.tipo',
            'menu_tipos.position'
        )->join('menu_tipos', 'menu_tipos.menu_item', 'menu_items.id')
            ->where('menu_items.codapl', 'CA');

        return Inertia::render('Cajas/Menu/Index', ['menu_items' => $menu_items]);
    }

    public function show(Request $request, string $tipo) {}

    public function guardar(Request $request)
    {
        try {
            $tipo = $request->input('tipo');
            $usuario = $request->input('usuario');
            $permisos = $request->input('permisos');
            $permisos = explode(';', $permisos);

            $response = $this->db->begin();
            if ($tipo == 'A') {
                foreach ($permisos as $permiso) {
                    if (empty($permiso)) continue;

                    $table = new Gener42;
                    $table->setUsuario($usuario);
                    $table->setPermiso($permiso);
                    if (! $table->save()) {
                        $this->db->rollback();
                    }
                }
            }
            if ($tipo == 'E') {
                foreach ($permisos as $permiso) {
                    if (empty($permiso)) continue;
                    Gener42::whereRaw("usuario='{$usuario}' and permiso='{$permiso}'")->delete();
                }
            }
            $this->db->commit();
            $response = [
                'flag' => true,
                'msg' => 'Operación realizada correctamente'
            ];
        } catch (DebugException $e) {
            $this->db->rollback();
            $response = [
                'flag' => false,
                'msg' => $e->getMessage()
            ];
        }
        return $this->renderObject($response, false);
    }

    public function borrar(Request $request)
    {
        $this->db->begin();
        try {
            $tipo = $request->input('tipo');
            $usuario = $request->input('usuario');
            $permisos = $request->input('permisos');
            $permisos = explode(';', $permisos);
        } catch (DebugException $e) {
            $this->db->rollback();
            $response = [
                'flag' => false,
                'msg' => $e->getMessage()
            ];
        }
        return $this->renderObject($response, false);
    }
}
