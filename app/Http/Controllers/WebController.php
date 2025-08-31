<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Trabajador;
use App\Models\NucleoFamiliar;
use App\Http\Resources\EmpresaCollection;
use App\Http\Resources\TrabajadorCollection;
use App\Http\Resources\NucleoFamiliarCollection;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Http;

class WebController extends Controller
{
    public function dashboard()
    {
        return Inertia::render('Dashboard');
    }

    public function empresas()
    {
        $empresas = Empresa::with('trabajadores')->get();
        return Inertia::render('Empresas/Index', [
            'empresas' => new EmpresaCollection($empresas)
        ]);
    }

    public function trabajadores()
    {
        $trabajadores = Trabajador::with(['empresa', 'nucleosFamiliares'])->get();
        return Inertia::render('Trabajadores/Index', [
            'trabajadores' => new TrabajadorCollection($trabajadores)
        ]);
    }

    public function nucleosFamiliares()
    {
        $nucleosFamiliares = NucleoFamiliar::with('trabajador.empresa')->get();
        return Inertia::render('NucleosFamiliares/Index', [
            'nucleos_familiares' => new NucleoFamiliarCollection($nucleosFamiliares)
        ]);
    }

    public function pruebaApiEmpresas()
    {
        // 1. Obtenemos la URL base de nuestra propia aplicación.
        // Esto es útil para que funcione tanto en desarrollo como en producción.
        $url = config('app.url');

        // 2. Hacemos la petición GET a la ruta de nuestra API.
        $response = Http::get($url . '/api/empresas');

        // 3. Convertimos la respuesta JSON en un array de PHP.
        $empresas = $response->json();
        return response()->json($empresas);
    }
}
