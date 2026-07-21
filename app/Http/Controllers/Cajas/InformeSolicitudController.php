<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Adapter\ApplicationController;
use App\Http\Requests\Cajas\InformeSolicitudRequest;
use App\Models\Mercurio09;
use App\Services\Reports\InformeSolicitudPdfService;
use App\Support\AuditoriaSolicitudResolver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class InformeSolicitudController extends ApplicationController
{
    public function __construct(
        protected InformeSolicitudPdfService $informeSolicitudPdfService
    ) {}

    public function index()
    {
        $tipopcs = AuditoriaSolicitudResolver::tipopcsInforme();
        $labels = AuditoriaSolicitudResolver::labelsInforme();

        $mercurio09 = Mercurio09::whereIn('tipopc', $tipopcs)->get();
        if ($mercurio09->isEmpty()) {
            $mercurio09 = collect($labels)->map(fn (string $detalle, string $tipopc) => (object) [
                'tipopc' => $tipopc,
                'detalle' => $detalle,
            ]);
        } else {
            $mercurio09 = $mercurio09->map(function ($item) use ($labels) {
                $key = (string) $item->tipopc;
                if (isset($labels[$key]) && trim((string) $item->detalle) === '') {
                    $item->detalle = $labels[$key];
                }

                return $item;
            });
        }

        return view('cajas.informe_solicitud.index', [
            'title' => 'Informe de solicitud',
            'mercurio09' => $mercurio09,
        ]);
    }

    public function pdf(InformeSolicitudRequest $request): BinaryFileResponse|JsonResponse|Response
    {
        try {
            $result = $this->informeSolicitudPdfService->generate(
                (string) $request->validated('tipopc'),
                (string) $request->validated('ruuid')
            );
        } catch (NotFoundHttpException $e) {
            if ($request->expectsJson()) {
                return response()->json(['message' => $e->getMessage()], 404);
            }

            abort(404, $e->getMessage());
        }

        return response()->file($result['path'], [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$result['filename'].'"',
        ]);
    }
}
