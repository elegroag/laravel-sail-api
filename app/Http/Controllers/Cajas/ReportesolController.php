<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Controller;
use App\Models\Mercurio09;
use App\Services\Reportes\ReporteSolicitudes;
use App\Services\Reportes\SolicitudesExcelExporter;
use App\Services\Srequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportesolController extends Controller
{
    /** Tipos soportados por ReporteSolicitudes / Excel. */
    public const TIPOS_SOPORTADOS = ['1', '2', '3', '4'];

    public function __construct(
        protected ReporteSolicitudes $reporteSolicitudes
    ) {}

    public function index(): Response
    {
        return Inertia::render('Cajas/Reportesol/Index', [
            'title' => 'Reportes de Solicitudes',
            'tipoOptions' => $this->tipoOptions(),
            'estadoOptions' => $this->estadoOptions(),
            'procesarUrl' => route('cajas.reportesol.procesar'),
        ]);
    }

    public function procesar(Request $request): StreamedResponse
    {
        $validated = $request->validate([
            'tipo' => 'required|string|in:'.implode(',', self::TIPOS_SOPORTADOS),
            'estado' => 'nullable|string|max:5',
            'fecha_solicitud' => 'nullable|date_format:Y-m-d',
            'fecha_aprueba' => 'nullable|date_format:Y-m-d',
        ], [
            'tipo.required' => 'Seleccione el tipo de solicitud.',
            'tipo.in' => 'El tipo de solicitud no es válido.',
        ]);

        if (($validated['estado'] ?? '') === '') {
            $validated['estado'] = null;
        }
        if (($validated['fecha_solicitud'] ?? '') === '') {
            $validated['fecha_solicitud'] = null;
        }
        if (($validated['fecha_aprueba'] ?? '') === '') {
            $validated['fecha_aprueba'] = null;
        }

        $dataset = $this->reporteSolicitudes->buildDataset(new Srequest($validated));

        if ($dataset === null) {
            abort(400, 'Tipo de solicitud no soportado');
        }

        $filename = sprintf(
            'reporte_solicitudes_%s_%s.xlsx',
            $validated['tipo'],
            now()->format('Ymd_His')
        );

        return SolicitudesExcelExporter::stream($dataset, $filename);
    }

    /**
     * @return list<array{value: string, label: string}>
     */
    private function tipoOptions(): array
    {
        $fallbacks = [
            '1' => 'Trabajador',
            '2' => 'Empresa',
            '3' => 'Cónyuge',
            '4' => 'Beneficiario',
        ];

        try {
            $rows = Mercurio09::query()
                ->whereIn('tipopc', self::TIPOS_SOPORTADOS)
                ->get(['tipopc', 'detalle']);

            $labels = [];
            foreach ($rows as $row) {
                $key = (string) $row->tipopc;
                $detalle = trim((string) ($row->detalle ?? ''));
                $labels[$key] = $detalle !== '' ? $detalle : ($fallbacks[$key] ?? $key);
            }

            foreach (self::TIPOS_SOPORTADOS as $tipo) {
                if (! isset($labels[$tipo])) {
                    $labels[$tipo] = $fallbacks[$tipo] ?? $tipo;
                }
            }

            return collect(self::TIPOS_SOPORTADOS)
                ->map(fn (string $value) => [
                    'value' => $value,
                    'label' => $labels[$value],
                ])
                ->values()
                ->all();
        } catch (\Throwable) {
            return collect($fallbacks)
                ->map(fn (string $label, string $value) => [
                    'value' => $value,
                    'label' => $label,
                ])
                ->values()
                ->all();
        }
    }

    /**
     * @return list<array{value: string, label: string}>
     */
    private function estadoOptions(): array
    {
        return [
            ['value' => 'A', 'label' => 'Activos'],
            ['value' => 'D', 'label' => 'Devueltos'],
            ['value' => 'R', 'label' => 'Rechazados'],
            ['value' => 'C', 'label' => 'Cancelados'],
            ['value' => 'I', 'label' => 'Inactivos'],
        ];
    }
}
