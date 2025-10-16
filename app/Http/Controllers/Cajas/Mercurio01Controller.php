<?php

namespace App\Http\Controllers\Cajas;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use App\Models\Mercurio01;
use App\Services\Utils\GeneralService;
use App\Services\Utils\Paginate;
use Illuminate\Http\Request;

class Mercurio01Controller extends ApplicationController
{
    protected $query = '1=1';

    protected $cantidad_pagina = 10;

    protected $db;

    protected $user;

    protected $tipfun;

    public function __construct()
    {
        $this->db = DbBase::rawConnect();
        $this->user = session('user');
        $this->tipfun = session('tipfun');
    }

    public function index()
    {
        return view('cajas.mercurio01.index', [
            'title' => 'Configuración basica',
            'campo_filtro' => [
                'codapl' => 'Aplicativo',
            ],
        ]);
    }

    public function showTabla($paginate)
    {
        return view('cajas.mercurio01._tabla', [
            'paginate' => $paginate,
        ])->render();
    }

    public function aplicarFiltro(Request $request)
    {
        $consultasOldServices = new GeneralService;
        $this->query = $consultasOldServices->converQuery($request);

        return $this->buscar($request);
    }

    public function changeCantidadPagina(Request $request)
    {
        $this->cantidad_pagina = $request->input('numero');

        return $this->buscar($request);
    }

    public function buscar(Request $request)
    {
        $pagina = ($request->input('pagina') == '') ? 1 : $request->input('pagina');
        $paginate = Paginate::execute(
            Mercurio01::whereRaw("{$this->query}")->get(),
            $pagina,
            $this->cantidad_pagina
        );
        $html = $this->showTabla($paginate);

        $consultasOldServices = new GeneralService;
        $html_paginate = $consultasOldServices->showPaginate($paginate);
        $response = [
            'consulta' => $html,
            'paginate' => $html_paginate,
        ];

        return $this->renderObject($response, false);
    }

    public function editar(Request $request)
    {
        $mercurio01 = Mercurio01::first();
        if ($mercurio01 == false) {
            $mercurio01 = new Mercurio01;
        }

        return $this->renderObject([
            'success' => true,
            'data' => $mercurio01->toArray(),
        ], false);
    }

    public function guardar(Request $request)
    {
        try {
            $this->setResponse('ajax');
            $codapl = $request->input('codapl');
            $email = $request->input('email');
            $clave = $request->input('clave');
            $path = $request->input('path');
            $ftpserver = $request->input('ftpserver');
            $pathserver = $request->input('pathserver');
            $userserver = $request->input('userserver');
            $passserver = $request->input('passserver');

            $response = $this->db->begin();
            $mercurio01 = new Mercurio01;

            $mercurio01->setCodapl($codapl);
            $mercurio01->setEmail($email);
            $mercurio01->setClave($clave);
            $mercurio01->setPath($path);
            $mercurio01->setFtpserver($ftpserver);
            $mercurio01->setPathserver($pathserver);
            $mercurio01->setUserserver($userserver);
            $mercurio01->setPassserver($passserver);

            if (! $mercurio01->save()) {
                $this->db->rollback();
            }
            $this->db->commit();
            $response = 'Creacion Con Exito';

            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $response = 'No se puede guardar/editar el Registro';
            return $this->renderObject($response, false);
        }
    }

    public function borrarFiltro(Request $request)
    {
        set_flashdata('filter_mercurio01', false, true);
        set_flashdata('filter_params', false, true);

        return $this->renderObject([
            'success' => true,
            'query' => get_flashdata_item('filter_mercurio01'),
            'filter' => get_flashdata_item('filter_params'),
        ]);
    }
}
