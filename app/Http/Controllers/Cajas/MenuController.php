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

    public function index(Request $request)
    {
        $query = MenuItem::select(
            DB::raw('menu_items.*'),
            'menu_tipos.is_visible',
            'menu_tipos.tipo',
            'menu_tipos.position'
        )
            ->join('menu_tipos', 'menu_tipos.menu_item', '=', 'menu_items.id');

        $q = trim((string) $request->query('q', ''));
        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $like = '%' . str_replace(['%', '_'], ['\%', '\_'], $q) . '%';
                $sub->where('menu_items.title', 'like', $like)
                    ->orWhere('menu_items.controller', 'like', $like)
                    ->orWhere('menu_items.action', 'like', $like)
                    ->orWhere('menu_items.default_url', 'like', $like);
            });
        }

        $tipo = $request->query('tipo');
        if ($tipo !== null && $tipo !== '') {
            $query->where('menu_tipos.tipo', $tipo);
        }

        $codapl = $request->query('codapl');
        if ($codapl !== null && $codapl !== '') {
            $query->where('menu_items.codapl', $codapl);
        }

        $query->orderBy('menu_tipos.position', 'ASC');

        $perPage = 5;
        $items = $query->paginate($perPage)->appends($request->only(['q', 'tipo', 'codapl', 'per_page']));

        $menu_items = [
            'data' => $items->items(),
            'meta' => [
                'total_menu_items' => $items->total(),
                'menu_permisos' => [],
                'pagination' => [
                    'current_page' => $items->currentPage(),
                    'last_page' => $items->lastPage(),
                    'per_page' => $items->perPage(),
                    'from' => $items->firstItem(),
                    'to' => $items->lastItem(),
                    'total' => $items->total(),
                ],
            ],
        ];

        return Inertia::render('Cajas/Menu/Index', compact('menu_items'));
    }

    public function create()
    {
        return Inertia::render('Cajas/Menu/Create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'default_url' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:50',
            'nota' => 'nullable|string',
            'parent_id' => 'nullable|integer',
            'codapl' => 'required|string|max:5',
            'controller' => 'required|string|max:150',
            'action' => 'required|string|max:150',
        ]);

        $item = MenuItem::create($data);

        return redirect()->to('/cajas/menu/' . $item->id . '/show');
    }

    public function show(int $id)
    {
        $menu_item = MenuItem::select(
            DB::raw('menu_items.*'),
            'menu_tipos.is_visible',
            'menu_tipos.tipo',
            'menu_tipos.position'
        )
            ->leftJoin('menu_tipos', 'menu_tipos.menu_item', '=', 'menu_items.id')
            ->where('menu_items.id', $id)
            ->firstOrFail();

        return Inertia::render('Cajas/Menu/Show', compact('menu_item'));
    }

    public function edit(int $id)
    {
        $menu_item = MenuItem::findOrFail($id);
        return Inertia::render('Cajas/Menu/Edit', compact('menu_item'));
    }

    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'default_url' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:50',
            'nota' => 'nullable|string',
            'parent_id' => 'nullable|integer',
            'codapl' => 'required|string|max:5',
            'controller' => 'required|string|max:150',
            'action' => 'required|string|max:150',
        ]);

        $item = MenuItem::findOrFail($id);
        $item->update($data);

        return redirect()->to('/cajas/menu/' . $item->id . '/show');
    }

    public function destroy(int $id)
    {
        $item = MenuItem::findOrFail($id);
        $item->delete();
        return redirect()->to('/cajas/menu');
    }

    public function children(int $id)
    {
        $children = MenuItem::select(
            DB::raw('menu_items.*'),
            'menu_tipos.is_visible',
            'menu_tipos.tipo',
            'menu_tipos.position'
        )
            ->join('menu_tipos', 'menu_tipos.menu_item', '=', 'menu_items.id')
            ->where('menu_items.parent_id', $id)
            ->orderBy('menu_tipos.position')
            ->orderBy('menu_items.id', 'ASC')
            ->get();

        return response()->json([
            'data' => $children,
        ]);
    }

    public function options(Request $request)
    {
        $q = trim((string) $request->input('q', ''));
        $id = $request->input('id');
        $codapl = $request->input('codapl');
        $alreadyChildrenIds = MenuItem::where('codapl', $codapl)->pluck('id');

        $options = MenuItem::query()
            ->where('id', '!=', $id)
            ->whereIn('id', $alreadyChildrenIds)
            ->when($q !== '', function ($query) use ($q) {
                $like = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $q) . '%';
                $query->where(function ($sub) use ($like) {
                    $sub->where('title', 'like', $like)
                        ->orWhere('controller', 'like', $like)
                        ->orWhere('action', 'like', $like);
                });
            })
            ->groupBy('controller', 'action')
            ->orderBy('title')
            ->limit(100)
            ->get(['id', 'title', 'controller', 'action']);

        return response()->json([
            'data' => $options,
        ]);
    }

    public function attachChild(int $id, Request $request)
    {
        $validated = $request->validate([
            'child_id' => ['required', 'integer', 'exists:menu_items,id', 'different:id'],
        ]);

        $childId = (int) $validated['child_id'];

        if ($childId === $id) {
            return response()->json(['message' => 'No puedes adjuntar el mismo elemento como hijo.'], 422);
        }

        $child = MenuItem::findOrFail($childId);
        $child->parent_id = $id;
        $child->save();

        return response()->json([
            'message' => 'Hijo agregado correctamente',
        ]);
    }

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
        return response()->json($response);
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
        return response()->json($response);
    }
}
