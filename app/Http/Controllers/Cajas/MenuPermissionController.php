<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Controller;
use App\Models\Gener21;
use App\Models\MenuItem;
use App\Models\MenuPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class MenuPermissionController extends Controller
{
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

        $perPage = $request->input('per_page', 10);
        $items = $query->paginate($perPage)->appends($request->only(['q', 'tipo', 'codapl', 'per_page']));

        $menu_items = [
            'data' => $items->items(),
            'meta' => [
                'total_menu_items' => $items->total(),
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

        return Inertia::render('Cajas/MenuPermission/Index', compact('menu_items'));
    }

    public function create()
    {
        return Inertia::render('Cajas/MenuPermission/Create', [
            'menu_items' => MenuItem::orderBy('title')->get(['id', 'title']),
            'tipos_funcionarios' => Gener21::orderBy('destipfun')->get(['tipfun', 'destipfun']),
        ]);
    }

    public function permissions(Request $request, int $menu_item_id)
    {
        $permissions = MenuPermission::where('menu_item', $menu_item_id)->with('tipfun')->get();
        $tipos_funcionarios = Gener21::orderBy('destipfun')->get();

        return response()->json([
            'permissions' => $permissions,
            'tipos_funcionarios' => $tipos_funcionarios,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'menu_item' => 'required|integer|exists:menu_items,id',
            'tipfun' => 'required|string|exists:gener21,tipfun',
            'can_view' => 'sometimes|boolean',
            'opciones' => 'nullable|string',
        ]);

        $data['can_view'] = $request->has('can_view');

        $existing = MenuPermission::where('menu_item', $data['menu_item'])
            ->where('tipfun', $data['tipfun'])
            ->first();

        if ($existing) {
            return redirect()->back()->withErrors(['tipfun' => 'Este permiso ya existe para el item del menú seleccionado.'])->withInput();
        }

        MenuPermission::create($data);

        return redirect()->to('/cajas/menu-permission')->with('success', 'Permiso creado correctamente.');
    }

    public function ajaxStore(Request $request)
    {
        $data = $request->validate([
            'menu_item' => 'required|integer|exists:menu_items,id',
            'tipfun' => 'required|string|exists:gener21,tipfun',
            'can_view' => 'required|boolean',
            'opciones' => 'nullable|string',
        ]);

        $permission = MenuPermission::updateOrCreate(
            ['menu_item' => $data['menu_item'], 'tipfun' => $data['tipfun']],
            ['can_view' => $data['can_view'], 'opciones' => $data['opciones']]
        );

        return response()->json($permission);
    }

    public function edit(int $id)
    {
        $permission = MenuPermission::with(['menuItem', 'tipfun'])->findOrFail($id);
        return Inertia::render('Cajas/MenuPermission/Edit', [
            'permission' => $permission,
        ]);
    }

    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'can_view' => 'sometimes|boolean',
            'opciones' => 'nullable|string',
        ]);
        
        $data['can_view'] = $request->has('can_view');

        $permission = MenuPermission::findOrFail($id);
        $permission->update($data);

        return redirect()->to('/cajas/menu-permission')->with('success', 'Permiso actualizado correctamente.');
    }

    public function destroy(int $id)
    {
        $permission = MenuPermission::findOrFail($id);
        $permission->delete();

        return response()->json(['message' => 'Permiso eliminado correctamente.']);
    }
}
