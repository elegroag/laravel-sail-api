<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Controller;
use App\Services\Reports\ConsultaDocumentoSolicitudService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class ConsultaDocumentoSolicitudController extends Controller
{
    public function __construct(
        protected ConsultaDocumentoSolicitudService $consultaDocumentoSolicitudService
    ) {}

    public function index(Request $request): Response
    {
        $queried = $request->boolean('queried');
        $documento = trim((string) $request->query('documento', ''));

        $filters = [
            'documento' => $documento,
        ];

        $tipopcOptions = collect(ConsultaDocumentoSolicitudService::tipopcLabels())
            ->map(fn (string $label, int|string $value) => [
                'value' => (string) $value,
                'label' => $label,
            ])
            ->values()
            ->all();

        $empty = [
            'documento' => $documento,
            'total' => 0,
            'grupos' => [],
        ];

        if (! $queried) {
            return Inertia::render('Cajas/ConsultaDocumentoSolicitud/Index', [
                'filters' => $filters,
                'tipopcOptions' => $tipopcOptions,
                'resultado' => $empty,
                'queried' => false,
                'error' => null,
            ]);
        }

        try {
            $request->merge(['documento' => $documento]);
            $validated = $request->validate([
                'documento' => ['required', 'string', 'max:20'],
            ], [
                'documento.required' => 'El documento de identificación es obligatorio.',
            ]);

            $resultado = $this->consultaDocumentoSolicitudService->consultar([
                'documento' => $validated['documento'],
            ]);

            return Inertia::render('Cajas/ConsultaDocumentoSolicitud/Index', [
                'filters' => [
                    'documento' => $validated['documento'],
                ],
                'tipopcOptions' => $tipopcOptions,
                'resultado' => $resultado,
                'queried' => true,
                'error' => null,
            ]);
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            return Inertia::render('Cajas/ConsultaDocumentoSolicitud/Index', [
                'filters' => $filters,
                'tipopcOptions' => $tipopcOptions,
                'resultado' => $empty,
                'queried' => true,
                'error' => 'Error al consultar: '.$e->getMessage(),
            ]);
        }
    }
}
