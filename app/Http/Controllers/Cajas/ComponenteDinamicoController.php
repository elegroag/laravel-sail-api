<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Controller;
use App\Models\Adapter\DbBase;
use App\Models\ComponenteDinamico;
use App\Models\FormularioDinamico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ComponenteDinamicoController extends Controller
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
        $query = ComponenteDinamico::with('validacion');

        $q = trim((string) $request->query('q', ''));
        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $like = '%' . str_replace(['%', '_'], ['\%', '\_'], $q) . '%';
                $sub->where('name', 'like', $like)
                    ->orWhere('label', 'like', $like)
                    ->orWhere('type', 'like', $like);
            });
        }

        $type = $request->query('type');
        if ($type !== null && $type !== '') {
            $query->where('type', $type);
        }

        $formularioId = $request->query('formulario_id');
        if ($formularioId !== null && $formularioId !== '') {
            $query->where('formulario_id', $formularioId);
        }

        $groupId = $request->query('group_id');
        if ($groupId !== null && $groupId !== '') {
            $query->where('group_id', $groupId);
        }

        $query->orderBy('group_id', 'ASC')
            ->orderBy('order', 'ASC')
            ->orderBy('created_at', 'DESC');

        $perPage = 15;
        $items = $query->paginate($perPage)->appends($request->only(['q', 'type', 'formulario_id', 'group_id', 'per_page']));

        $componentes_dinamicos = [
            'data' => $items->items(),
            'meta' => [
                'total_componentes' => $items->total(),
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

        return Inertia::render('Cajas/ComponenteDinamico/Index', compact('componentes_dinamicos'));
    }

    public function create(Request $request)
    {
        $formularioId = $request->query('formulario_id');
        $formulario = null;
        if ($formularioId) {
            $formulario = FormularioDinamico::find($formularioId);
        }

        return Inertia::render('Cajas/ComponenteDinamico/Create', compact('formulario'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:componentes_dinamicos,name',
            'type' => 'required|in:input,select,textarea,dialog,date,number',
            'label' => 'required|string|max:255',
            'placeholder' => 'nullable|string|max:255',
            'form_type' => 'required|string|max:50',
            'group_id' => 'required|integer|min:1',
            'order' => 'required|integer|min:1',
            'default_value' => 'nullable|string',
            'is_disabled' => 'boolean',
            'is_readonly' => 'boolean',
            'data_source' => 'nullable|array',
            'css_classes' => 'nullable|string|max:255',
            'help_text' => 'nullable|string',
            'target' => 'integer|min:-1',
            'event_config' => 'nullable|array',
            'search_type' => 'nullable|string|max:50',
            'date_max' => 'nullable|date',
            'number_min' => 'nullable|numeric',
            'number_max' => 'nullable|numeric',
            'number_step' => 'numeric|min:0.01',
        ]);

        $componente = ComponenteDinamico::create($data);

        return redirect()->to('/cajas/componente-dinamico/' . $componente->id . '/show');
    }

    public function show(int $id)
    {
        $componente = ComponenteDinamico::with(['validacion', 'formulario'])->findOrFail($id);

        return Inertia::render('Cajas/ComponenteDinamico/Show', compact('componente'));
    }

    public function edit(int $id)
    {
        $componente = ComponenteDinamico::findOrFail($id);
        return Inertia::render('Cajas/ComponenteDinamico/Edit', compact('componente'));
    }

    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:componentes_dinamicos,name,' . $id,
            'type' => 'required|in:input,select,textarea,dialog,date,number',
            'label' => 'required|string|max:255',
            'placeholder' => 'nullable|string|max:255',
            'form_type' => 'required|string|max:50',
            'group_id' => 'required|integer|min:1',
            'order' => 'required|integer|min:1',
            'default_value' => 'nullable|string',
            'is_disabled' => 'boolean',
            'is_readonly' => 'boolean',
            'data_source' => 'nullable|array',
            'css_classes' => 'nullable|string|max:255',
            'help_text' => 'nullable|string',
            'target' => 'integer|min:-1',
            'event_config' => 'nullable|array',
            'search_type' => 'nullable|string|max:50',
            'date_max' => 'nullable|date',
            'number_min' => 'nullable|numeric',
            'number_max' => 'nullable|numeric',
            'number_step' => 'numeric|min:0.01',
        ]);

        $componente = ComponenteDinamico::findOrFail($id);
        $componente->update($data);

        return redirect()->to('/cajas/componente-dinamico/' . $componente->id . '/show');
    }

    public function destroy(int $id)
    {
        $componente = ComponenteDinamico::findOrFail($id);
        $componente->delete();
        return redirect()->to('/cajas/componente-dinamico');
    }

    public function options(Request $request)
    {
        $q = trim((string) $request->input('q', ''));
        $type = $request->input('type');
        $formularioId = $request->input('formulario_id');

        $options = ComponenteDinamico::query()
            ->when($q !== '', function ($query) use ($q) {
                $like = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $q) . '%';
                $query->where(function ($sub) use ($like) {
                    $sub->where('name', 'like', $like)
                        ->orWhere('label', 'like', $like);
                });
            })
            ->when($type !== null, function ($query) use ($type) {
                $query->where('type', $type);
            })
            ->when($formularioId !== null, function ($query) use ($formularioId) {
                $query->where('formulario_id', $formularioId);
            })
            ->orderBy('group_id')
            ->orderBy('order')
            ->orderBy('label')
            ->limit(100)
            ->get(['id', 'name', 'label', 'type', 'group_id']);

        return response()->json([
            'data' => $options,
        ]);
    }

    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'componentes' => 'required|array',
            'componentes.*.id' => 'required|integer|exists:componentes_dinamicos,id',
            'componentes.*.order' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated['componentes'] as $componenteData) {
                ComponenteDinamico::where('id', $componenteData['id'])
                    ->update(['order' => $componenteData['order']]);
            }
            DB::commit();

            return response()->json([
                'message' => 'Orden actualizado correctamente',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Error al actualizar el orden',
            ], 500);
        }
    }

    public function duplicate(Request $request, int $id)
    {
        $original = ComponenteDinamico::with('validacion')->findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255|unique:componentes_dinamicos,name',
            'label' => 'required|string|max:255',
            'group_id' => 'integer|min:1',
            'order' => 'integer|min:1',
        ]);

        $duplicated = ComponenteDinamico::create([
            'name' => $data['name'],
            'type' => $original->type,
            'label' => $data['label'],
            'placeholder' => $original->placeholder,
            'form_type' => $original->form_type,
            'group_id' => $data['group_id'] ?? $original->group_id,
            'order' => $data['order'] ?? ($original->order + 1),
            'default_value' => $original->default_value,
            'is_disabled' => $original->is_disabled,
            'is_readonly' => $original->is_readonly,
            'data_source' => $original->data_source,
            'css_classes' => $original->css_classes,
            'help_text' => $original->help_text,
            'target' => $original->target,
            'event_config' => $original->event_config,
            'search_type' => $original->search_type,
            'date_max' => $original->date_max,
            'number_min' => $original->number_min,
            'number_max' => $original->number_max,
            'number_step' => $original->number_step,
        ]);

        // Duplicar validación si existe
        if ($original->validacion) {
            $duplicated->validacion()->create([
                'pattern' => $original->validacion->pattern,
                'default_value' => $original->validacion->default_value,
                'max_length' => $original->validacion->max_length,
                'min_length' => $original->validacion->min_length,
                'numeric_range' => $original->validacion->numeric_range,
                'field_size' => $original->validacion->field_size,
                'detail_info' => $original->validacion->detail_info,
                'is_required' => $original->validacion->is_required,
                'custom_rules' => $original->validacion->custom_rules,
                'error_messages' => $original->validacion->error_messages,
            ]);
        }

        return redirect()->to('/cajas/componente-dinamico/' . $duplicated->id . '/show');
    }

    public function byFormulario(Request $request, int $formularioId)
    {
        $componentes = ComponenteDinamico::with('validacion')
            ->where('formulario_id', $formularioId)
            ->orderBy('group_id')
            ->orderBy('order')
            ->get();

        return response()->json([
            'data' => $componentes,
        ]);
    }
}
