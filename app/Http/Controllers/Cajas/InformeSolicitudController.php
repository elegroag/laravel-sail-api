<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cajas\InformeSolicitudRequest;
use App\Models\Mercurio09;
use App\Services\Reports\InformeSolicitudPdfService;
use App\Support\AuditoriaSolicitudResolver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class InformeSolicitudController extends Controller
{
    public function __construct(
        protected InformeSolicitudPdfService $informeSolicitudPdfService
    ) {}

    public function index(): InertiaResponse
    {
        $tipopcs = AuditoriaSolicitudResolver::tipopcsInforme();
        $labels = AuditoriaSolicitudResolver::labelsInforme();

        $mercurio09 = Mercurio09::whereIn('tipopc', $tipopcs)->get();
        if ($mercurio09->isEmpty()) {
            $tipopcOptions = collect($labels)
                ->map(fn (string $detalle, string $tipopc) => [
                    'value' => (string) $tipopc,
                    'label' => $detalle,
                ])
                ->values()
                ->all();
        } else {
            $tipopcOptions = $mercurio09->map(function ($item) use ($labels) {
                $key = (string) $item->tipopc;
                $detalle = trim((string) $item->detalle);
                if ($detalle === '' && isset($labels[$key])) {
                    $detalle = $labels[$key];
                }

                return [
                    'value' => $key,
                    'label' => $detalle !== '' ? $detalle : $key,
                ];
            })->values()->all();
        }

        $legacyFlash = function_exists('get_flashdata') ? get_flashdata() : null;
        $flashError = null;
        if (is_array($legacyFlash) && isset($legacyFlash['error'])) {
            $flashError = is_array($legacyFlash['error'])
                ? (string) ($legacyFlash['error']['msj'] ?? '')
                : (string) $legacyFlash['error'];
            $flashError = $flashError !== '' ? $flashError : null;
        }

        return Inertia::render('Cajas/InformeSolicitud/Index', [
            'title' => 'Informe de solicitud',
            'tipopcOptions' => $tipopcOptions,
            'pdfUrl' => route('cajas.informe-solicitud.pdf'),
            'flashError' => $flashError,
        ]);
    }

    public function pdf(InformeSolicitudRequest $request): BinaryFileResponse|JsonResponse|Response|RedirectResponse
    {
        try {
            $result = $this->informeSolicitudPdfService->generate(
                (string) $request->validated('tipopc'),
                (string) $request->validated('ruuid')
            );
        } catch (NotFoundHttpException $e) {
            $message = $e->getMessage() !== ''
                ? $e->getMessage()
                : 'No se encontró la solicitud con el RUUID y tipo indicados.';

            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 404);
            }

            set_flashdata('error', [
                'msj' => $message,
                'code' => 404,
            ]);

            return redirect()->route('cajas.informe-solicitud.index');
        }

        return response()->file($result['path'], [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$result['filename'].'"',
        ]);
    }
}
