<?php

namespace App\Services\Cajas;

use App\Models\Mercurio02;
use App\Services\Utils\Comman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Mercurio02Service
{
    public function buscar(Request $request, int $cantidad_pagina)
    {
        $query = $this->buildQuery($request);

        return Mercurio02::whereRaw($query)->paginate($cantidad_pagina);
    }

    public function guardar(Request $request)
    {
        try {
            DB::beginTransaction();

            $codcaj = $request->input('codcaj');
            $mercurio02 = Mercurio02::firstOrNew(['codcaj' => $codcaj]);

            $mercurio02->fill($request->all());
            $mercurio02->save();

            DB::commit();

            return ['flag' => true, 'msg' => 'Operación realizada con éxito'];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['flag' => false, 'msg' => 'No se pudo guardar/editar el registro: ' . $e->getMessage()];
        }
    }

    public function editar()
    {
        $mercurio02 = Mercurio02::first();
        if (!$mercurio02) {
            return ['success' => false, 'data' => null];
        }

        return ['success' => true, 'data' => $mercurio02->toArray()];
    }

    public function obtenerCiudades()
    {
        $apiRest = Comman::Api();
        $apiRest->runCli([
            'servicio' => 'ComfacaAfilia',
            'metodo' => 'listar_ciudades_departamentos',
        ]);

        $data = $apiRest->toArray();
        $ciudades = $data['ciudades'] ?? [];
        
        $_codciu = [];
        if (is_array($ciudades)) {
            foreach ($ciudades as $mcodciu) {
                $_codciu[$mcodciu['codciu']] = $mcodciu['detciu'];
            }
        }
        return $_codciu;
    }

    private function buildQuery(Request $request): string
    {
        $filtro = $request->input('filtro');
        $campo = $request->input('campo');
        $query = '1=1';

        if ($filtro && $campo) {
            $query = "{$campo} LIKE '%{$filtro}%'";
            session()->put('filter_mercurio02', $query);
        } elseif (session()->has('filter_mercurio02')) {
            $query = session('filter_mercurio02');
        }

        return $query;
    }
}
