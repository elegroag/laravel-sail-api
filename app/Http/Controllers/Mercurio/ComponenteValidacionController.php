<?php

namespace App\Http\Controllers\Mercurio;

use App\Http\Controllers\Controller;
use App\Models\ComponenteDinamico;
use App\Models\ComponenteValidacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ComponenteValidacionController extends Controller
{
    protected $db;

    protected $user;

    protected $tipfun;

    public function __construct()
    {
        $this->db = \App\Models\Adapter\DbBase::rawConnect();
        $this->user = session('user') ?? null;
        $this->tipfun = session('tipfun') ?? null;
    }

    public function index(Request $request)
    {
        $query = ComponenteValidacion::with('componente');

        $q = trim((string) $request->query('q', ''));
        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $like = '%' . str_replace(['%', '_'], ['\%', '\_'], $q) . '%';
                $sub->where('pattern', 'like', $like)
                    ->orWhere('detail_info', 'like', $like)
                    ->orWhereHas('componente', function ($componenteQuery) use ($q) {
                        $componenteQuery->where('name', 'like', '%' . $q . '%')
                            ->orWhere('label', 'like', '%' . $q . '%');
                    });
            });
        }

        $componenteId = $request->query('componente_id');
        if ($componenteId !== null && $componenteId !== '') {
            $query->where('componente_id', $componenteId);
        }

        $isRequired = $request->query('is_required');
        if ($isRequired !== null && $isRequired !== '') {
            $query->where('is_required', $isRequired === '1');
        }

        $query->orderBy('created_at', 'DESC');

        $perPage = 15;
        $items = $query->paginate($perPage)->appends($request->only(['q', 'componente_id', 'is_required', 'per_page']));

        $componentes_validaciones = [
            'data' => $items->items(),
            'meta' => [
                'total_validaciones' => $items->total(),
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

        return Inertia::render('Mercurio/ComponenteValidacion/Index', compact('componentes_validaciones'));
    }

    public function create(Request $request)
    {
        $componenteId = $request->query('componente_id');
        $componente = null;
        if ($componenteId) {
            $componente = ComponenteDinamico::find($componenteId);
        }

        $componentes = ComponenteDinamico::orderBy('name')->get(['id', 'name', 'label']);

        return Inertia::render('Mercurio/ComponenteValidacion/Create', compact('componente', 'componentes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'componente_id' => 'required|integer|exists:componentes_dinamicos,id',
            'pattern' => 'nullable|string|max:255',
            'default_value' => 'nullable|string',
            'max_length' => 'nullable|integer|min:1',
            'min_length' => 'nullable|integer|min:0',
            'numeric_range' => 'nullable|string|max:50',
            'field_size' => 'integer|min:1|max:100',
            'detail_info' => 'nullable|string',
            'is_required' => 'boolean',
            'custom_rules' => 'nullable|array',
            'error_messages' => 'nullable|array',
        ]);

        // Verificar que no exista ya una validación para este componente
        $existing = ComponenteValidacion::where('componente_id', $data['componente_id'])->first();
        if ($existing) {
            return back()->withErrors(['componente_id' => 'Este componente ya tiene reglas de validación definidas.']);
        }

        $validacion = ComponenteValidacion::create($data);

        return redirect()->to('/mercurio/componente-validacion/' . $validacion->id . '/show');
    }

    public function show(int $id)
    {
        $validacion = ComponenteValidacion::with('componente')->findOrFail($id);

        return Inertia::render('Mercurio/ComponenteValidacion/Show', compact('validacion'));
    }

    public function edit(int $id)
    {
        $validacion = ComponenteValidacion::findOrFail($id);
        $componentes = ComponenteDinamico::orderBy('name')->get(['id', 'name', 'label']);

        return Inertia::render('Mercurio/ComponenteValidacion/Edit', compact('validacion', 'componentes'));
    }

    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'componente_id' => 'required|integer|exists:componentes_dinamicos,id',
            'pattern' => 'nullable|string|max:255',
            'default_value' => 'nullable|string',
            'max_length' => 'nullable|integer|min:1',
            'min_length' => 'nullable|integer|min:0',
            'numeric_range' => 'nullable|string|max:50',
            'field_size' => 'integer|min:1|max:100',
            'detail_info' => 'nullable|string',
            'is_required' => 'boolean',
            'custom_rules' => 'nullable|array',
            'error_messages' => 'nullable|array',
        ]);

        // Verificar que no exista ya una validación para este componente (excluyendo la actual)
        $existing = ComponenteValidacion::where('componente_id', $data['componente_id'])
            ->where('id', '!=', $id)
            ->first();
        if ($existing) {
            return back()->withErrors(['componente_id' => 'Este componente ya tiene reglas de validación definidas.']);
        }

        $validacion = ComponenteValidacion::findOrFail($id);
        $validacion->update($data);

        return redirect()->to('/mercurio/componente-validacion/' . $validacion->id . '/show');
    }

    public function destroy(int $id)
    {
        $validacion = ComponenteValidacion::findOrFail($id);
        $validacion->delete();
        return redirect()->to('/mercurio/componente-validacion');
    }

    public function options(Request $request)
    {
        $q = trim((string) $request->input('q', ''));
        $componenteId = $request->input('componente_id');

        $options = ComponenteValidacion::with('componente')
            ->when($q !== '', function ($query) use ($q) {
                $like = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $q) . '%';
                $query->where(function ($sub) use ($like) {
                    $sub->where('pattern', 'like', $like)
                        ->orWhere('detail_info', 'like', $like);
                });
            })
            ->when($componenteId !== null, function ($query) use ($componenteId) {
                $query->where('componente_id', $componenteId);
            })
            ->orderBy('created_at', 'DESC')
            ->limit(100)
            ->get(['id', 'componente_id', 'pattern', 'is_required']);

        return response()->json([
            'data' => $options,
        ]);
    }

    public function byComponente(Request $request, int $componenteId)
    {
        $validacion = ComponenteValidacion::where('componente_id', $componenteId)->first();

        return response()->json([
            'data' => $validacion,
        ]);
    }

    public function duplicate(Request $request, int $id)
    {
        $original = ComponenteValidacion::findOrFail($id);

        $data = $request->validate([
            'componente_id' => 'required|integer|exists:componentes_dinamicos,id',
        ]);

        // Verificar que el componente destino no tenga validación
        $existing = ComponenteValidacion::where('componente_id', $data['componente_id'])->first();
        if ($existing) {
            return back()->withErrors(['componente_id' => 'Este componente ya tiene reglas de validación definidas.']);
        }

        $duplicated = ComponenteValidacion::create([
            'componente_id' => $data['componente_id'],
            'pattern' => $original->pattern,
            'default_value' => $original->default_value,
            'max_length' => $original->max_length,
            'min_length' => $original->min_length,
            'numeric_range' => $original->numeric_range,
            'field_size' => $original->field_size,
            'detail_info' => $original->detail_info,
            'is_required' => $original->is_required,
            'custom_rules' => $original->custom_rules,
            'error_messages' => $original->error_messages,
        ]);

        return redirect()->to('/mercurio/componente-validacion/' . $duplicated->id . '/show');
    }

    public function validateRules(Request $request)
    {
        $validated = $request->validate([
            'componente_id' => 'required|integer|exists:componentes_dinamicos,id',
            'value' => 'required|string',
        ]);

        $validacion = ComponenteValidacion::where('componente_id', $validated['componente_id'])->first();

        if (!$validacion) {
            return response()->json([
                'valid' => true,
                'message' => 'No hay reglas de validación definidas',
            ]);
        }

        $errors = [];
        $value = $validated['value'];

        // Validar requerido
        if ($validacion->is_required && empty($value)) {
            $errors[] = 'Este campo es requerido';
        }

        // Validar patrón regex
        if ($validacion->pattern && !empty($value)) {
            if (!preg_match('/' . $validacion->pattern . '/', $value)) {
                $errors[] = $validacion->error_messages['pattern'] ?? 'El formato no es válido';
            }
        }

        // Validar longitud máxima
        if ($validacion->max_length && strlen($value) > $validacion->max_length) {
            $errors[] = $validacion->error_messages['max_length'] ?? "Máximo {$validacion->max_length} caracteres";
        }

        // Validar longitud mínima
        if ($validacion->min_length && strlen($value) < $validacion->min_length) {
            $errors[] = $validacion->error_messages['min_length'] ?? "Mínimo {$validacion->min_length} caracteres";
        }

        // Validar rango numérico
        if ($validacion->numeric_range && is_numeric($value)) {
            [$min, $max] = explode('-', $validacion->numeric_range);
            if ($value < $min || $value > $max) {
                $errors[] = $validacion->error_messages['numeric_range'] ?? "Valor debe estar entre {$min} y {$max}";
            }
        }

        return response()->json([
            'valid' => empty($errors),
            'errors' => $errors,
        ]);
    }
}
