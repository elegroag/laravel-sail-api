<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Controller;
use App\Models\Gener18;
use App\Services\Reports\ReporteSolicitudesEmpresaService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class ReporteSolicitudesEmpresaController extends Controller
{
    public function __construct(
        protected ReporteSolicitudesEmpresaService $reporteSolicitudesEmpresaService
    ) {}

    public function index(Request $request): Response
    {
        $hoy = now()->toDateString();
        $defaultTipopcs = array_map('strval', array_keys(ReporteSolicitudesEmpresaService::tipopcLabels()));

        $queried = $request->boolean('queried');

        $rawTipopcs = $request->query('tipopcs', $defaultTipopcs);
        $tipopcsForForm = $this->normalizeTipopcs($rawTipopcs, filterAllowed: ! $queried);

        $filters = [
            'documento' => trim((string) $request->query('documento', '')),
            'coddoc' => trim((string) $request->query('coddoc', '')),
            'fecini' => (string) $request->query('fecini', now()->startOfMonth()->toDateString()),
            'fecfin' => (string) $request->query('fecfin', $hoy),
            'estado' => (string) $request->query('estado', ''),
            'tipopcs' => $tipopcsForForm,
            'per_page' => max(
                10,
                min(
                    ReporteSolicitudesEmpresaService::MAX_PER_PAGE,
                    (int) $request->query('per_page', ReporteSolicitudesEmpresaService::DEFAULT_PER_PAGE)
                )
            ),
            'refresh' => $request->boolean('refresh'),
        ];

        $tipopcOptions = collect(ReporteSolicitudesEmpresaService::tipopcLabels())
            ->map(fn (string $label, int|string $value) => [
                'value' => (string) $value,
                'label' => $label,
            ])
            ->values()
            ->all();

        $estadoOptions = collect(ReporteSolicitudesEmpresaService::estadosLabels())
            ->map(fn (string $label, string $value) => [
                'value' => $value,
                'label' => $label,
            ])
            ->values()
            ->all();

        $emptyItems = [
            'data' => [],
            'meta' => [
                'pagination' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => $filters['per_page'],
                    'from' => null,
                    'to' => null,
                    'total' => 0,
                ],
            ],
        ];

        if ($queried) {
            $request->merge([
                'tipopcs' => array_map('intval', $this->normalizeTipopcs($rawTipopcs, filterAllowed: false)),
                'estado' => $filters['estado'] !== '' ? $filters['estado'] : null,
            ]);

            $validated = $request->validate([
                'documento' => ['required', 'string', 'max:20'],
                'coddoc' => ['required', 'string', 'max:2'],
                'fecini' => ['required', 'date_format:Y-m-d'],
                'fecfin' => ['required', 'date_format:Y-m-d', 'after_or_equal:fecini'],
                'tipopcs' => ['required', 'array', 'min:1'],
                'tipopcs.*' => ['integer', Rule::in([1, 3, 4])],
                'estado' => ['nullable', 'string', Rule::in(['P', 'T', 'A', 'D', 'X', 'C', 'I'])],
                'page' => ['nullable', 'integer', 'min:1'],
                'per_page' => ['nullable', 'integer', 'min:10', 'max:100'],
            ], [
                'documento.required' => 'El documento de la empresa es obligatorio.',
                'coddoc.required' => 'El tipo de documento es obligatorio.',
                'fecini.required' => 'La fecha inicial es obligatoria.',
                'fecfin.required' => 'La fecha final es obligatoria.',
                'tipopcs.required' => 'Seleccione al menos un tipo de solicitud.',
                'tipopcs.min' => 'Seleccione al menos un tipo de solicitud.',
            ]);

            $filters['tipopcs'] = array_map('strval', $validated['tipopcs']);
        }

        $tiposDocumento = $this->tiposDocumentoOptions();

        if (! $queried) {
            return Inertia::render('Cajas/ReporteSolicitudesEmpresa/Index', [
                'filters' => $filters,
                'tiposDocumento' => $tiposDocumento,
                'tipopcOptions' => $tipopcOptions,
                'estadoOptions' => $estadoOptions,
                'empresa' => null,
                'fromCache' => false,
                'items' => $emptyItems,
                'queried' => false,
                'error' => null,
            ]);
        }

        try {
            $resultado = $this->reporteSolicitudesEmpresaService->consultar([
                'documento' => $filters['documento'],
                'coddoc' => $filters['coddoc'],
                'fecini' => $filters['fecini'],
                'fecfin' => $filters['fecfin'],
                'estado' => $filters['estado'] !== '' ? $filters['estado'] : null,
                'tipopcs' => array_map('intval', $filters['tipopcs']),
                'page' => max(1, (int) $request->query('page', 1)),
                'per_page' => $filters['per_page'],
                'refresh' => $filters['refresh'],
            ]);

            $pagination = $resultado['pagination'];

            return Inertia::render('Cajas/ReporteSolicitudesEmpresa/Index', [
                'filters' => $filters,
                'tiposDocumento' => $tiposDocumento,
                'tipopcOptions' => $tipopcOptions,
                'estadoOptions' => $estadoOptions,
                'empresa' => $resultado['empresa'],
                'fromCache' => (bool) ($resultado['from_cache'] ?? false),
                'items' => [
                    'data' => $resultado['rows'],
                    'meta' => [
                        'pagination' => [
                            'current_page' => $pagination['page'],
                            'last_page' => $pagination['last_page'],
                            'per_page' => $pagination['per_page'],
                            'from' => $pagination['from'] ?: null,
                            'to' => $pagination['to'] ?: null,
                            'total' => $pagination['total'],
                        ],
                    ],
                ],
                'queried' => true,
                'error' => null,
            ]);
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            return Inertia::render('Cajas/ReporteSolicitudesEmpresa/Index', [
                'filters' => $filters,
                'tiposDocumento' => $tiposDocumento,
                'tipopcOptions' => $tipopcOptions,
                'estadoOptions' => $estadoOptions,
                'empresa' => null,
                'fromCache' => false,
                'items' => $emptyItems,
                'queried' => true,
                'error' => 'Error al consultar el reporte: '.$e->getMessage(),
            ]);
        }
    }

    /**
     * @return list<array{value: string, label: string}>
     */
    private function tiposDocumentoOptions(): array
    {
        try {
            return Gener18::query()
                ->orderBy('coddoc')
                ->get(['coddoc', 'detdoc', 'codrua'])
                ->map(fn (Gener18 $tipo) => [
                    'value' => (string) $tipo->coddoc,
                    'label' => trim(($tipo->codrua ?: $tipo->coddoc).' — '.$tipo->detdoc),
                ])
                ->values()
                ->all();
        } catch (\Throwable) {
            return [];
        }
    }

    /**
     * @return list<string>
     */
    private function normalizeTipopcs(mixed $raw, bool $filterAllowed = true): array
    {
        if (is_array($raw)) {
            $values = array_map('intval', $raw);
        } elseif (is_string($raw) && $raw !== '') {
            $values = array_map('intval', explode(',', $raw));
        } else {
            $values = [];
        }

        if ($filterAllowed) {
            $allowed = [1, 3, 4];
            $values = array_values(array_unique(array_filter(
                $values,
                fn (int $v) => in_array($v, $allowed, true)
            )));
        } else {
            $values = array_values($values);
        }

        return array_map('strval', $values);
    }
}
