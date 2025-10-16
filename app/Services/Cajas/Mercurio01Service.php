<?php

namespace App\Services\Cajas;

use App\Models\Mercurio01;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Mercurio01Service
{
    public function buscar(Request $request, int $cantidad_pagina)
    {
        $query = $this->buildQuery($request);

        return Mercurio01::whereRaw($query)->paginate($cantidad_pagina);
    }

    public function guardar(Request $request)
    {
        try {
            DB::beginTransaction();

            $mercurio01 = Mercurio01::first() ?? new Mercurio01();
            
            $mercurio01->codapl = $request->input('codapl');
            $mercurio01->email = $request->input('email');
            $mercurio01->clave = $request->input('clave');
            $mercurio01->path = $request->input('path');
            $mercurio01->ftpserver = $request->input('ftpserver');
            $mercurio01->pathserver = $request->input('pathserver');
            $mercurio01->userserver = $request->input('userserver');
            $mercurio01->passserver = $request->input('passserver');
            
            $mercurio01->save();

            DB::commit();

            return ['flag' => true, 'msg' => 'Creación Con Éxito'];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['flag' => false, 'msg' => 'No se puede guardar/editar el Registro: ' . $e->getMessage()];
        }
    }

    public function editar()
    {
        $mercurio01 = Mercurio01::first();
        if (!$mercurio01) {
            return ['success' => false, 'data' => null];
        }

        return ['success' => true, 'data' => $mercurio01->toArray()];
    }

    private function buildQuery(Request $request): string
    {
        $filtro = $request->input('filtro');
        $campo = $request->input('campo');
        $query = '1=1';

        if ($filtro && $campo) {
            $query = "{$campo} LIKE '%{$filtro}%'";
            session()->put('filter_mercurio01', $query);
        } elseif (session()->has('filter_mercurio01')) {
            $query = session('filter_mercurio01');
        }

        return $query;
    }
}
