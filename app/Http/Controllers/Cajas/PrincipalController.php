<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Controller;
use App\Services\Cajas\PrincipalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class PrincipalController extends Controller
{
    protected $user;

    protected $tipfun;

    public function __construct(
        protected PrincipalService $principalService
    ) {
        $this->user = session('user');
        $this->tipfun = session('tipfun');
    }

    public function index(): Response
    {
        $usuario = is_array($this->user) ? ($this->user['usuario'] ?? 0) : 0;

        return Inertia::render('Cajas/Principal/Index', [
            'servicios' => $this->principalService->buildServicios($usuario),
        ]);
    }

    public function dashboard(): Response
    {
        return Inertia::render('Cajas/Principal/Dashboard', [
            'charts' => $this->principalService->buildDashboardCharts(),
        ]);
    }

    public function traerUsuariosRegistrados(): JsonResponse
    {
        return response()->json($this->principalService->usuariosRegistrados());
    }

    public function traerOpcionMasUsuada(): JsonResponse
    {
        return response()->json($this->principalService->opcionMasUsada());
    }

    public function traerMotivoMasUsuada(): JsonResponse
    {
        return response()->json($this->principalService->motivoMasUsado());
    }

    public function traerCargaLaboral(): JsonResponse
    {
        return response()->json([
            ...$this->principalService->cargaLaboral(),
            'success' => true,
        ]);
    }

    public function fileExisteGlobal(Request $request)
    {
        try {
            $file = $request->input('file');
            $id = $request->input('id');
            $coddoc = $request->input('coddoc');

            // Log::info('file_existe_global: inicio', [
            //     'file_b64' => $file,
            //     'id' => $id,
            //     'coddoc' => $coddoc,
            // ]);

            if (! $file) {
                // Log::warning('file_existe_global: file vacío o ausente');

                return response()->json([
                    'success' => false,
                    'msj' => 'Parámetro file requerido',
                ], 422);
            }

            $archivo = base64_decode($file, true);
            if ($archivo === false || $archivo === '') {
                // Log::warning('file_existe_global: base64 inválido', [
                //     'file_b64' => $file,
                // ]);

                return response()->json([
                    'success' => false,
                    'msj' => 'Nombre de archivo inválido (base64)',
                ], 422);
            }

            $archivo = basename($archivo);
            $fichero = storage_path('temp/' . $archivo);

            // Log::info('file_existe_global: path resuelto', [
            //     'archivo' => $archivo,
            //     'fichero' => $fichero,
            //     'exists' => file_exists($fichero),
            // ]);

            if (file_exists($fichero)) {
                // Log::info('file_existe_global: archivo local encontrado', [
                //     'fichero' => $fichero,
                //     'size' => @filesize($fichero),
                // ]);

                return response()->file($fichero, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="' . $archivo . '"',
                ]);
            }

            // Log::warning('file_existe_global: archivo no encontrado', [
            //     'archivo' => $archivo,
            //     'fichero' => $fichero,
            // ]);

            return response()->json([
                'success' => false,
                'msj' => 'Archivo no encontrado ' . $archivo,
                'path' => $fichero,
            ], 404);
        } catch (\Throwable $e) {
            Log::error('file_existe_global: excepción', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'request' => $request->only(['file', 'id', 'coddoc']),
            ]);

            return response()->json([
                'success' => false,
                'msj' => 'Error al obtener el archivo: ' . $e->getMessage(),
            ], 500);
        }
    }
}
