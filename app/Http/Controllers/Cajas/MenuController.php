<?php

namespace App\Http\Controllers\Cajas;

use App\Exceptions\DebugException;
use App\Http\Controllers\Controller;
use App\Models\Adapter\DbBase;
use App\Models\Gener42;
use App\Models\MenuItem;
use App\Models\MenuTipo;
use App\Models\Mercurio06;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class MenuController extends Controller
{
    protected ?DbBase $db;

    protected mixed $user;

    protected mixed $tipfun;

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
                $like = '%'.str_replace(['%', '_'], ['\%', '\_'], $q).'%';
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

        $perPage = (int) $request->query('per_page', 5);
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

        $tipos = $this->tiposCatalog();

        return Inertia::render('Cajas/Menu/Index', compact('menu_items', 'tipos'));
    }

    public function create()
    {
        $tipos = $this->tiposCatalog();

        return Inertia::render('Cajas/Menu/Create', compact('tipos'));
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
            'tipos' => ['required', 'array', 'min:1'],
            'tipos.*.tipo' => ['required', 'string', 'max:5', Rule::exists('mercurio06', 'tipo')],
            'tipos.*.is_visible' => ['boolean'],
            'tipos.*.position' => ['integer', 'min:0'],
        ]);

        $tipos = $data['tipos'];
        unset($data['tipos']);

        $item = DB::transaction(function () use ($data, $tipos) {
            $item = MenuItem::create($data);
            $this->syncMenuTipos($item->id, $tipos);

            return $item;
        });

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Item de menú creado correctamente',
                'redirect' => '/cajas/menu/'.$item->id.'/show',
            ]);
        }

        return redirect()->to('/cajas/menu/'.$item->id.'/show');
    }

    public function show(int $id)
    {
        $menu_item = MenuItem::findOrFail($id);
        $parent = $menu_item->parent_id
            ? MenuItem::query()->whereKey($menu_item->parent_id)->first(['id', 'title'])
            : null;

        $itemTipos = MenuTipo::query()
            ->where('menu_item', $id)
            ->orderBy('position')
            ->orderBy('id')
            ->get(['id', 'tipo', 'is_visible', 'position']);

        $tipoLabels = Mercurio06::query()
            ->whereIn('tipo', $itemTipos->pluck('tipo'))
            ->pluck('detalle', 'tipo');

        $menu_tipos = $itemTipos->map(fn ($row) => [
            'id' => $row->id,
            'tipo' => $row->tipo,
            'detalle' => $tipoLabels[$row->tipo] ?? $row->tipo,
            'is_visible' => (bool) $row->is_visible,
            'position' => (int) $row->position,
        ])->values()->all();

        return Inertia::render('Cajas/Menu/Show', compact('menu_item', 'menu_tipos', 'parent'));
    }

    public function edit(int $id)
    {
        $menu_item = MenuItem::findOrFail($id);
        $parent = $menu_item->parent_id
            ? MenuItem::query()->whereKey($menu_item->parent_id)->first(['id', 'title'])
            : null;

        $tipos = $this->tiposCatalog();
        $menu_tipos = MenuTipo::query()
            ->where('menu_item', $id)
            ->orderBy('position')
            ->orderBy('id')
            ->get(['tipo', 'is_visible', 'position'])
            ->map(fn ($row) => [
                'tipo' => $row->tipo,
                'is_visible' => (bool) $row->is_visible,
                'position' => (int) $row->position,
            ])
            ->values()
            ->all();

        return Inertia::render('Cajas/Menu/Edit', compact('menu_item', 'parent', 'tipos', 'menu_tipos'));
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
            'tipos' => ['required', 'array', 'min:1'],
            'tipos.*.tipo' => ['required', 'string', 'max:5', Rule::exists('mercurio06', 'tipo')],
            'tipos.*.is_visible' => ['boolean'],
            'tipos.*.position' => ['integer', 'min:0'],
        ]);

        $tipos = $data['tipos'];
        unset($data['tipos']);

        $item = MenuItem::findOrFail($id);

        DB::transaction(function () use ($item, $data, $tipos) {
            $item->update($data);
            $this->syncMenuTipos($item->id, $tipos);
        });

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Item de menú actualizado correctamente',
                'redirect' => '/cajas/menu/'.$item->id.'/show',
            ]);
        }

        return redirect()->to('/cajas/menu/'.$item->id.'/show');
    }

    public function destroy(int $id)
    {
        $item = MenuItem::findOrFail($id);
        $item->delete();

        return redirect()->to('/cajas/menu');
    }

    public function children(Request $request)
    {
        $validated = $request->validate([
            'id' => ['required', 'integer', 'exists:menu_items,id'],
            'codapl' => ['required', 'string', 'max:5'],
            'tipo' => ['required', 'string', 'max:5'],
        ]);

        $id = (int) $validated['id'];
        $codapl = $validated['codapl'];
        $tipo = $validated['tipo'];

        $children = MenuItem::select(
            DB::raw('menu_items.*'),
            'menu_tipos.is_visible',
            'menu_tipos.tipo',
            'menu_tipos.position'
        )
            ->join('menu_tipos', 'menu_tipos.menu_item', '=', 'menu_items.id')
            ->where('menu_items.parent_id', $id)
            ->where('menu_items.codapl', $codapl)
            ->where('menu_tipos.tipo', $tipo)
            ->orderBy('menu_tipos.position')
            ->orderBy('menu_items.id', 'ASC')
            ->get();

        $itemTipos = MenuTipo::query()
            ->where('menu_item', $id)
            ->orderBy('position')
            ->orderBy('id')
            ->get(['id', 'tipo', 'is_visible', 'position']);

        $tipoLabels = Mercurio06::query()
            ->whereIn('tipo', $itemTipos->pluck('tipo'))
            ->pluck('detalle', 'tipo');

        $tipos = $itemTipos->map(fn ($row) => [
            'id' => $row->id,
            'tipo' => $row->tipo,
            'detalle' => $tipoLabels[$row->tipo] ?? $row->tipo,
            'is_visible' => (bool) $row->is_visible,
            'position' => $row->position,
        ])->values();

        return response()->json([
            'success' => true,
            'data' => $children,
            'tipos' => $tipos,
            'message' => 'Items hijos cargados correctamente',
        ]);
    }

    public function parentOptions(Request $request)
    {
        $q = trim((string) $request->input('q', ''));
        $codapl = trim((string) $request->input('codapl', ''));
        $excludeId = $request->input('exclude_id');

        $query = MenuItem::query()
            ->when($codapl !== '', fn ($builder) => $builder->where('codapl', $codapl))
            ->when($excludeId, fn ($builder) => $builder->where('id', '!=', $excludeId))
            ->when($q !== '', function ($builder) use ($q) {
                $like = '%'.str_replace(['%', '_'], ['\%', '\_'], $q).'%';
                $builder->where(function ($sub) use ($like, $q) {
                    $sub->where('title', 'like', $like)
                        ->orWhere('controller', 'like', $like)
                        ->orWhere('action', 'like', $like);

                    if (ctype_digit($q)) {
                        $sub->orWhere('id', (int) $q);
                    }
                });
            })
            ->orderBy('title')
            ->limit(100)
            ->get(['id', 'title', 'controller', 'action', 'parent_id', 'default_url']);

        return response()->json([
            'success' => true,
            'data' => $query,
        ]);
    }

    public function options(Request $request)
    {
        $q = trim((string) $request->input('q', ''));
        $id = $request->input('id');
        $codapl = $request->input('codapl');
        $tipo = trim((string) $request->input('tipo', ''));

        $options = MenuItem::query()
            ->where('menu_items.id', '!=', $id)
            ->where('menu_items.codapl', $codapl)
            ->where(function ($sub) use ($id) {
                $sub->whereNull('menu_items.parent_id')->orWhere('menu_items.parent_id', '!=', $id);
            })
            ->when($tipo !== '', function ($query) use ($tipo) {
                $query->whereExists(function ($sub) use ($tipo) {
                    $sub->selectRaw('1')
                        ->from('menu_tipos')
                        ->whereColumn('menu_tipos.menu_item', 'menu_items.id')
                        ->where('menu_tipos.tipo', $tipo);
                });
            })
            ->when($q !== '', function ($query) use ($q) {
                $like = '%'.str_replace(['%', '_'], ['\%', '\_'], $q).'%';
                $query->where(function ($sub) use ($like) {
                    $sub->where('title', 'like', $like)
                        ->orWhere('controller', 'like', $like)
                        ->orWhere('action', 'like', $like);
                });
            })
            ->selectRaw('MIN(menu_items.id) as id, MIN(menu_items.title) as title, menu_items.controller, menu_items.action')
            ->groupBy('menu_items.controller', 'menu_items.action')
            ->orderBy('title')
            ->limit(100)
            ->get();

        return response()->json([
            'data' => $options,
        ]);
    }

    public function attachChild(Request $request)
    {
        $validated = $request->validate([
            'id' => ['required', 'integer', 'exists:menu_items,id'],
            'child_id' => ['required', 'integer', 'exists:menu_items,id', 'different:id'],
            'codapl' => ['required', 'string', 'max:5'],
            'tipo' => ['required', 'string', 'max:5'],
        ]);

        $id = (int) $validated['id'];
        $childId = (int) $validated['child_id'];
        $codapl = $validated['codapl'];
        $tipo = $validated['tipo'];

        if ($childId === $id) {
            return response()->json(['message' => 'No puedes adjuntar el mismo elemento como hijo.'], 422);
        }

        $parent = MenuItem::where('id', $id)->where('codapl', $codapl)->first();
        if (! $parent) {
            return response()->json(['message' => 'El item padre no pertenece a la aplicación indicada (codapl).'], 422);
        }

        $child = MenuItem::where('id', $childId)->where('codapl', $codapl)->first();
        if (! $child) {
            return response()->json(['message' => 'El item hijo no pertenece a la misma aplicación (codapl).'], 422);
        }

        $childParent = MenuItem::where('id', $childId)->where('parent_id', $id)->first();
        if (! $childParent) {
            $childParent = MenuItem::create(
                [
                    'parent_id' => $parent->id,
                    'codapl' => $codapl,
                    'controller' => $child->controller,
                    'action' => $child->action,
                    'title' => $child->title,
                    'default_url' => $child->default_url,
                    'icon' => $child->icon,
                    'color' => $child->color,
                    'nota' => $child->nota,
                ]
            );
        }

        $hasTipo = DB::table('menu_tipos')->where('menu_item', $childId)->where('tipo', $tipo)->exists();
        if (! $hasTipo) {
            return response()->json(['message' => 'El hijo no tiene configuración para el tipo seleccionado.'], 422);
        }

        // crea el tipo para el hijo si no existe
        $menuTipo = MenuTipo::where('menu_item', $childParent->id)->where('tipo', $tipo)->first();
        if (! $menuTipo) {
            $menuTipo = new MenuTipo;
            $menuTipo->menu_item = $childParent->id;
            $menuTipo->tipo = $tipo;
            $menuTipo->is_visible = true;
            $menuTipo->position = 1;
            $menuTipo->save();
        }

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

            $this->db->begin();
            if ($tipo == 'A') {
                foreach ($permisos as $permiso) {
                    if (empty($permiso)) {
                        continue;
                    }

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
                    if (empty($permiso)) {
                        continue;
                    }
                    Gener42::whereRaw("usuario='{$usuario}' and permiso='{$permiso}'")->delete();
                }
            }
            $this->db->commit();
            $response = [
                'flag' => true,
                'msg' => 'Operación realizada correctamente',
            ];
        } catch (DebugException $e) {
            $this->db->rollback();
            $response = [
                'flag' => false,
                'msg' => $e->getMessage(),
            ];
        }

        return response()->json($response);
    }

    public function borrar(Request $request)
    {
        $this->db->begin();
        $response = null;
        try {
            $tipo = $request->input('tipo');
            $usuario = $request->input('usuario');
            $permisos = $request->input('permisos');
            $permisos = explode(';', $permisos);
        } catch (DebugException $e) {
            $this->db->rollback();
            $response = [
                'flag' => false,
                'msg' => $e->getMessage(),
            ];
        }

        return response()->json($response);
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    private function tiposCatalog(): array
    {
        return Mercurio06::orderBy('detalle')
            ->get(['tipo', 'detalle'])
            ->map(fn ($row) => [
                'value' => $row->tipo,
                'label' => $row->detalle,
            ])
            ->all();
    }

    /**
     * @param  array<int, array{tipo: string, is_visible?: bool, position?: int}>  $tipos
     */
    private function syncMenuTipos(int $menuItemId, array $tipos): void
    {
        $validTipos = Mercurio06::pluck('tipo')->all();

        $normalized = collect($tipos)
            ->filter(fn ($row) => in_array($row['tipo'] ?? '', $validTipos, true))
            ->unique('tipo')
            ->values();

        $incomingTipos = $normalized->pluck('tipo')->all();

        MenuTipo::query()
            ->where('menu_item', $menuItemId)
            ->whereNotIn('tipo', $incomingTipos)
            ->delete();

        foreach ($normalized as $row) {
            MenuTipo::updateOrCreate(
                [
                    'menu_item' => $menuItemId,
                    'tipo' => $row['tipo'],
                ],
                [
                    'is_visible' => (bool) ($row['is_visible'] ?? true),
                    'position' => (int) ($row['position'] ?? 1),
                ]
            );
        }
    }
}
