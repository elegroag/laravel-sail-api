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

            $mercurio01 = Mercurio01::first() ?? new Mercurio01;
            $mercurio01->fill($request->only([
                'codapl',
                'email',
                'clave',
                'path',
                'ftpserver',
                'pathserver',
                'userserver',
                'passserver',
            ]));
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
