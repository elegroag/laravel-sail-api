<?php

namespace App\Http\Controllers\Cajas;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Gener02;
use App\Models\Gener40;
use App\Models\Gener42;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Gener42Controller extends ApplicationController
{
    protected $query = '1=1';

    protected $cantidad_pagina = 0;


    protected $user;

    protected $tipo;

    protected $tipfun;

    public function __construct()
    {
        $this->user = session('user') ?? null;
        $this->tipfun = session('tipfun') ?? null;
        $this->tipo = 'A';
    }

    public function tablePermisos($table, $tipo)
    {
        return view('cajas/gener42/asigna_permisos', ['table' => $table, 'tipo' => $tipo])->render();
    }

    public function buscar(Request $request)
    {
        $usuario = $request->input('usuario');
        $buscar = $request->input('buscar');
        $tipo = $request->input('tipo');
        $response['flag'] = true;

        $likes = '';
        if ($tipo == 'S') {
            $likes = " and detalle like '%{$buscar}%'";
        }
        $table = Gener40::whereRaw("codigo IN(select permiso from gener42 where usuario={$usuario}) {$likes}")->orderBy('orden', 'ASC')->get();
        $response['permite'] = $this->tablePermisos($table, 'S');

        $liken = '';
        if ($tipo == 'N') {
            $liken = " and detalle like '%{$buscar}%'";
        }
        $table = Gener40::whereRaw("codigo NOT IN(select permiso from gener42 where usuario={$usuario}) {$liken}")->orderBy('orden', 'ASC')->get();
        $response['nopermite'] = $this->tablePermisos($table, 'N');
        $this->renderObject($response, false);
    }

    public function index()
    {
        return view('cajas.gener42.index', [
            'title' => 'Permisos por usuario',
            'gener02' => Gener02::all(),
            'campo_filtro' => [
                'usuario' => 'Usuario',
                'tipfun' => 'Tipo funcionario',
            ]
        ]);
    }

    public function guardar(Request $request)
    {
        try {
            $tipo = $request->input('tipo');
            $usuario = $request->input('usuario');
            $permisos = $request->input('permisos');
            $permisos = explode(';', $permisos);

            DB::beginTransaction();
            if ($tipo == 'A') {
                foreach ($permisos as $permiso) {
                    if (empty($permiso)) continue;

                    $table = new Gener42;
                    $table->setUsuario($usuario);
                    $table->setPermiso($permiso);
                    if (! $table->save()) {
                        DB::rollBack();
                    }
                }
            }
            if ($tipo == 'E') {
                foreach ($permisos as $permiso) {
                    if (empty($permiso)) continue;
                    Gener42::whereRaw("usuario='{$usuario}' and permiso='{$permiso}'")->delete();
                }
            }
            DB::commit();
            $response = [
                'flag' => true,
                'msg' => 'Operación realizada correctamente'
            ];
        } catch (DebugException $e) {
            DB::rollBack();
            $response = [
                'flag' => false,
                'msg' => $e->getMessage()
            ];
        }
        return $this->renderObject($response, false);
    }
}
