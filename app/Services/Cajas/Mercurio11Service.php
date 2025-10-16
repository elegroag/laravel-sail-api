<?php

namespace App\Services\Cajas;

use App\Models\Mercurio11;
use App\Services\Utils\Paginate;
use App\Services\Utils\QueryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class Mercurio11Service
{
    protected $queryService;

    public function __construct(QueryService $queryService)
    {
        $this->queryService = $queryService;
    }

    public function buscar(Request $request, $cantidad_pagina = 10)
    {
        $query = $this->queryService->converQuery($request);
        $pagina = $request->input('pagina', 1);

        $paginate = Paginate::execute(
            Mercurio11::whereRaw($query)->get(),
            $pagina,
            $cantidad_pagina
        );

        $response = [
            'consulta' => view('cajas.mercurio11._table', compact('paginate'))->render(),
            'query' => $query,
            'paginate' => view('templates/paginate_traditional', compact('paginate'))->render(),
        ];

        return $response;
    }

    public function editar($codest)
    {
        $mercurio11 = Mercurio11::where('codest', $codest)->first();
        if ($mercurio11 == false) {
            $mercurio11 = new Mercurio11;
        }
        return [
            'success' => true,
            'data' => $mercurio11->toArray(),
        ];
    }

    public function borrar($codest)
    {
        DB::beginTransaction();
        try {
            Mercurio11::where('codest', $codest)->delete();
            DB::commit();
            return [
                'success' => true,
                'msj' => 'Proceso completado con éxito.',
            ];
        } catch (\Exception $e) {
            DB::rollback();
            return [
                'success' => false,
                'msj' => $e->getMessage(),
            ];
        }
    }

    public function guardar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'codest' => 'required|string|max:255',
            'detalle' => 'required|string',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'msj' => $validator->errors()->first(),
            ];
        }

        DB::beginTransaction();
        try {
            $codest = $request->input('codest');
            $detalle = $request->input('detalle');

            $mercurio11 = Mercurio11::where('codest', $codest)->first();

            if (! $mercurio11) {
                $mercurio11 = new Mercurio11;
                $mercurio11->setCodest($codest);
            }
            
            $mercurio11->setDetalle($detalle);
            $mercurio11->save();
            
            DB::commit();

            return [
                'success' => true,
                'msj' => 'Proceso completado con éxito.',
            ];
        } catch (\Exception $e) {
            DB::rollback();
            return [
                'success' => false,
                'msj' => $e->getMessage(),
            ];
        }
    }
}
