<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Controller;
use App\Models\Adapter\DbBase;
use App\Models\FormularioDinamico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class FormularioDinamicoController extends Controller
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
        $query = FormularioDinamico::query();

        $q = trim((string) $request->query('q', ''));
        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $like = '%' . str_replace(['%', '_'], ['\%', '\_'], $q) . '%';
                $sub->where('name', 'like', $like)
                    ->orWhere('title', 'like', $like)
                    ->orWhere('description', 'like', $like)
                    ->orWhere('module', 'like', $like);
            });
        }

        $module = $request->query('module');
        if ($module !== null && $module !== '') {
            $query->where('module', $module);
        }

        $isActive = $request->query('is_active');
        if ($isActive !== null && $isActive !== '') {
            $query->where('is_active', $isActive === '1');
        }

        $query->orderBy('created_at', 'DESC');

        $perPage = 10;
        $items = $query->paginate($perPage)->appends($request->only(['q', 'module', 'is_active', 'per_page']));

        $formularios_dinamicos = [
            'data' => $items->items(),
            'meta' => [
                'total_formularios' => $items->total(),
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

        return Inertia::render('Cajas/FormularioDinamico/Index', compact('formularios_dinamicos'));
    }

    public function create()
    {
        return Inertia::render('Cajas/FormularioDinamico/Create');
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'name' => 'required|string|max:255|unique:formularios_dinamicos,name',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'module' => 'required|string|max:100',
                'endpoint' => 'required|string|max:255',
                'method' => 'required|string|max:10',
                'is_active' => 'boolean',
                'layout_config' => 'nullable',
                'permissions' => 'nullable',
            ]);

            $formulario = FormularioDinamico::create($data);

            return redirect()->to('/cajas/formulario-dinamico/' . $formulario->id . '/show');
        } catch (\Exception $th) {
            return redirect()->back()->withErrors($th->getMessage());
        }
    }

    public function show(int $id)
    {
        $formulario = FormularioDinamico::findOrFail($id);

        return Inertia::render('Cajas/FormularioDinamico/Show', compact('formulario'));
    }

    public function edit(int $id)
    {
        $formulario = FormularioDinamico::findOrFail($id);
        return Inertia::render('Cajas/FormularioDinamico/Edit', compact('formulario'));
    }

    public function update(Request $request, int $id)
    {
        try {
            $data = $request->validate([
                'name' => 'required|string|max:255|unique:formularios_dinamicos,name,' . $id,
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'module' => 'required|string|max:100',
                'endpoint' => 'required|string|max:255',
                'method' => 'required|string|max:10',
                'is_active' => 'boolean',
                'layout_config' => 'nullable',
                'permissions' => 'nullable',
            ]);

            $formulario = FormularioDinamico::findOrFail($id);
            $formulario->update($data);

            return redirect()->to('/cajas/formulario-dinamico/' . $formulario->id . '/show');
        } catch (\Exception $th) {
            return redirect()->back()->withErrors($th->getMessage());
        }
    }

    public function destroy(int $id)
    {
        $formulario = FormularioDinamico::findOrFail($id);
        $formulario->delete();
        return redirect()->to('/cajas/formulario-dinamico');
    }

    public function options(Request $request)
    {
        $q = trim((string) $request->input('q', ''));
        $module = $request->input('module');
        $perPage = (int) $request->input('per_page', 10);

        $query = FormularioDinamico::query()
            ->when($q !== '', function ($query) use ($q) {
                $like = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $q) . '%';
                $query->where(function ($sub) use ($like) {
                    $sub->where('name', 'like', $like)
                        ->orWhere('title', 'like', $like);
                });
            })
            ->when($module !== null, function ($query) use ($module) {
                $query->where('module', $module);
            })
            ->where('is_active', true)
            ->orderBy('title');

        $items = $query->paginate($perPage, ['id', 'name', 'title', 'module']);

        return response()->json([
            'data' => $items->items(),
            'meta' => [
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
            ],
        ]);
    }

    public function toggleActive(Request $request, int $id)
    {
        $formulario = FormularioDinamico::findOrFail($id);
        $formulario->update(['is_active' => !$formulario->is_active]);

        return response()->json([
            'message' => 'Estado actualizado correctamente',
            'is_active' => $formulario->is_active,
        ]);
    }

    public function duplicate(Request $request, int $id)
    {
        $original = FormularioDinamico::with('componentes')->findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255|unique:formularios_dinamicos,name',
            'title' => 'required|string|max:255',
        ]);

        $duplicated = FormularioDinamico::create([
            'name' => $data['name'],
            'title' => $data['title'],
            'description' => $original->description,
            'module' => $original->module,
            'endpoint' => $original->endpoint,
            'method' => $original->method,
            'is_active' => false, // Duplicado inicia inactivo
            'layout_config' => $original->layout_config,
            'permissions' => $original->permissions,
        ]);

        // Duplicar componentes si existen
        foreach ($original->componentes as $componente) {
            $duplicated->componentes()->create([
                'name' => $componente->name . '_copy',
                'type' => $componente->type,
                'label' => $componente->label,
                'placeholder' => $componente->placeholder,
                'form_type' => $componente->form_type,
                'group_id' => $componente->group_id,
                'order' => $componente->order,
                'default_value' => $componente->default_value,
                'is_disabled' => $componente->is_disabled,
                'is_readonly' => $componente->is_readonly,
                'data_source' => $componente->data_source,
                'css_classes' => $componente->css_classes,
                'help_text' => $componente->help_text,
                'target' => $componente->target,
                'event_config' => $componente->event_config,
                'search_type' => $componente->search_type,
                'date_max' => $componente->date_max,
                'number_min' => $componente->number_min,
                'number_max' => $componente->number_max,
                'number_step' => $componente->number_step,
            ]);
        }

        return redirect()->to('/cajas/formulario-dinamico/' . $duplicated->id . '/show');
    }
}
