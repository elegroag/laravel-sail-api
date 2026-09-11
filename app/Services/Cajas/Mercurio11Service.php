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
        $mercurio11 = Mercurio11::firstOrNew(['codest' => $codest]);
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

            Mercurio11::updateOrCreate(
                ['codest' => $codest],
                ['detalle' => $detalle]
            );

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
