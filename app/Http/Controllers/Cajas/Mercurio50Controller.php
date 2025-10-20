<?php

namespace App\Http\Controllers\Cajas;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use App\Models\Mercurio50;
use App\Services\Utils\GeneralService;
use App\Services\Utils\Paginate;
use Illuminate\Http\Request;

class Mercurio50Controller extends ApplicationController
{
    protected $query = '1=1';

    protected $cantidad_pagina = 10;

    protected $db;

    protected $user;

    protected $tipo;

    public function __construct()
    {
        $this->db = DbBase::rawConnect();
        $this->user = session()->has('user') ? session('user') : null;
        $this->tipo = session()->has('tipo') ? session('tipo') : null;
    }

    public function showTabla($paginate)
    {
        return view('cajas.mercurio50._tabla', compact('paginate'));
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

    public function index()
    {
        return view('cajas.mercurio50.index', [
            'title' => 'Basica',
            'showNewButton' => Mercurio50::count() == 0,
        ]);
    }

    public function buscar(Request $request)
    {
        $pagina = ($request->input('pagina') == '') ? 1 : $request->input('pagina');

        $paginate = Paginate::execute(
            Mercurio50::whereRaw("{$this->query}")->get(),
            $pagina,
            $this->cantidad_pagina
        );

        $html = $this->showTabla($paginate);
        $consultasOldServices = new GeneralService;
        $html_paginate = $consultasOldServices->showPaginate($paginate);

        $response['consulta'] = $html;
        $response['paginate'] = $html_paginate;

        return $this->renderObject($response, false);
    }

    public function editar()
    {
        try {
            $this->setResponse('ajax');
            $mercurio50 = Mercurio50::first();
            if ($mercurio50 == false) {
                $mercurio50 = new Mercurio50;
            }

            return $this->renderObject($mercurio50->toArray(), false);
        } catch (DebugException $e) {
            $response = parent::errorFunc('Error al obtener el registro');

            return $this->renderObject($response, false);
        }
    }

    public function guardar(Request $request)
    {
        try {
            $this->setResponse('ajax');
            $codapl = $request->input('codapl');
            $webser = $request->input('webser');
            $path = $request->input('path');
            $urlonl = $request->input('urlonl');
            $puncom = $request->input('puncom');

            $this->db->begin();
            $mercurio50 = Mercurio50::first();
            if (! $mercurio50) {
                $mercurio50 = new Mercurio50;
            }

            $mercurio50->setCodapl($codapl);
            $mercurio50->setWebser($webser);
            $mercurio50->setPath($path);
            $mercurio50->setUrlonl($urlonl);
            $mercurio50->setPuncom($puncom);

            if (! $mercurio50->save()) {
                parent::setLogger($mercurio50->getMessages());
                $this->db->rollback();
                throw new DebugException('Error al guardar el registro');
            }

            $this->db->commit();
            $response = parent::successFunc('Creacion Con Exito');

            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $this->db->rollback();
            $response = parent::errorFunc('No se puede guardar/editar el Registro: ' . $e->getMessage());

            return $this->renderObject($response, false);
        }
    }
}
