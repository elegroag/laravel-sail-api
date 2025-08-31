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
        return Inertia::render('Web/Dashboard');
    }

    public function empresas()
    {
        $empresas = Empresa::with('trabajadores')->get();
        return Inertia::render('Empresas/Index', [
            'empresas' => new EmpresaCollection($empresas)
        ]);
    }

    public function empresasCreate()
    {
        return Inertia::render('Empresas/Create');
    }

    public function empresasEdit($id)
    {
        return Inertia::render('Empresas/Edit', [
            'empresa' => Empresa::findOrFail($id)
        ]);
    }

    public function empresasShow($id)
    {
        return Inertia::render('Empresas/Show', [
            'empresa' => Empresa::findOrFail($id)
        ]);
    }

    public function trabajadores()
    {
        $trabajadores = Trabajador::with(['empresa', 'nucleosFamiliares'])->get();
        return Inertia::render('Trabajadores/Index', [
            'trabajadores' => new TrabajadorCollection($trabajadores)
        ]);
    }

    public function trabajadoresCreate()
    {
        return Inertia::render('Trabajadores/Create');
    }

    public function trabajadoresEdit($id)
    {
        return Inertia::render('Trabajadores/Edit', [
            'trabajador' => Trabajador::findOrFail($id)
        ]);
    }

    public function trabajadoresShow($id)
    {
        return Inertia::render('Trabajadores/Show', [
            'trabajador' => Trabajador::findOrFail($id)
        ]);
    }

    public function nucleosFamiliares()
    {
        $nucleosFamiliares = NucleoFamiliar::with('trabajador.empresa')->get();
        return Inertia::render('NucleosFamiliares/Index', [
            'nucleos_familiares' => new NucleoFamiliarCollection($nucleosFamiliares)
        ]);
    }

    public function nucleosFamiliaresCreate()
    {
        return Inertia::render('NucleosFamiliares/Create');
    }

    public function nucleosFamiliaresEdit($id)
    {
        return Inertia::render('NucleosFamiliares/Edit', [
            'nucleo_familiar' => NucleoFamiliar::findOrFail($id)
        ]);
    }

    public function nucleosFamiliaresShow($id)
    {
        return Inertia::render('NucleosFamiliares/Show', [
            'nucleo_familiar' => NucleoFamiliar::findOrFail($id)
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
