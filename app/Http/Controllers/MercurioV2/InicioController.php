<?php

namespace App\Http\Controllers\MercurioV2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class InicioController extends Controller
{
    /**
     * Página principal del dashboard Mercurio v2
     */
    public function index(Request $request)
    {
        try {
            return inertia('MercurioV2/Inicio/Index', [
                'title' => 'Inicio',
                'user' => session('user'),
                // Stats del dashboard — estructura anidada para MovementCard
                'stats' => [
                    'total' => 0,
                    'aprobadas' => 0,
                    'rechazadas' => 0,
                    'pendientes' => 0,
                    // MovementCard espera esta estructura
                    'solicitudes_conyuges' => [
                        'pendientes' => 0,
                        'aprobados' => 0,
                        'rechazados' => 0,
                        'devueltos' => 0,
                        'temporales' => 0,
                    ],
                    'solicitudes_beneficiarios' => [
                        'pendientes' => 0,
                        'aprobados' => 0,
                        'rechazados' => 0,
                        'devueltos' => 0,
                        'temporales' => 0,
                    ],
                    'actualizacion_datos' => [
                        'pendientes' => 0,
                        'aprobados' => 0,
                        'rechazados' => 0,
                        'devueltos' => 0,
                        'temporales' => 0,
                    ],
                    'presentar_certificados' => [
                        'pendientes' => 0,
                        'aprobados' => 0,
                        'rechazados' => 0,
                        'devueltos' => 0,
                        'temporales' => 0,
                    ],
                ],
                // Consultas rápidas
                'consultas' => [
                    [
                        'id' => 1,
                        'title' => 'Consulta de giro',
                        'description' => 'Consulta el estado de tus giros',
                        'route' => '/mercurio-v2/consultas/giro',
                    ],
                    [
                        'id' => 2,
                        'title' => 'Núcleo familiar',
                        'description' => 'Consulta tu núcleo familiar',
                        'route' => '/mercurio-v2/consultas/nucleo',
                    ],
                    [
                        'id' => 3,
                        'title' => 'Consulta planilla',
                        'description' => 'Consulta tu planilla de pago',
                        'route' => '/mercurio-v2/consultas/planilla',
                    ],
                ],
                // Productos y servicios
                'productos' => [
                    [
                        'id' => 1,
                        'title' => 'P. Complemento nutricional',
                        'description' => 'Suplemento alimenticio',
                        'route' => '/mercurio-v2/productos/nutricional',
                    ],
                    [
                        'id' => 2,
                        'title' => 'P. Bienestar',
                        'description' => 'Plan de bienestar',
                        'route' => '/mercurio-v2/productos/bienestar',
                    ],
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('MercurioV2 InicioController@index: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar el dashboard.');
        }
    }
}
