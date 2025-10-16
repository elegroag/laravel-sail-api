<?php

namespace App\Http\Controllers\Cajas;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use App\Models\Mercurio02;
use App\Services\Utils\Comman;
use App\Services\Utils\GeneralService;
use App\Services\Utils\Paginate;
use Illuminate\Http\Request;

class Mercurio02Controller extends ApplicationController
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

    public function index()
    {
        if (Mercurio02::count() == 0) {
            $this->setParamToView('buttons', ['N']);
        }

        $apiRest = Comman::Api();
        $apiRest->runCli(
            [
                'servicio' => 'ComfacaAfilia',
                'metodo' => 'listar_ciudades_departamentos',
            ]
        );

        $data = $apiRest->toArray();
        $data = $data['ciudades'];
        $_codciu = [];
        if (is_array($data)) {
            foreach ($data as $mcodciu) {
                $_codciu[$mcodciu['codciu']] = $mcodciu['detciu'];
            }
        }

        return view('cajas.mercurio02.index', [
            'title' => 'Datos Caja',
            'ciudades' => $_codciu,
            'campo_filtro' => [
                'codcaj' => 'Caja',
                'nit' => 'Nit',
                'razsoc' => 'Razon Social',
            ]
        ]);
    }

    public function showTabla($paginate)
    {
        return view('cajas.mercurio02._tabla', [
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
        $pagina = $request->input('pagina', 1);
        $query = Mercurio02::whereRaw("{$this->query}");
        $paginate = Paginate::execute($query->get(), $pagina, $this->cantidad_pagina);

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
        $mercurio02 = Mercurio02::first();
        if ($mercurio02 == false) {
            $mercurio02 = new Mercurio02;
        }

        return $this->renderObject($mercurio02->toArray(), false);
    }

    public function guardar(Request $request)
    {
        try {
            $this->setResponse('ajax');

            $codcaj = $request->input('codcaj');
            $nit = $request->input('nit');
            $razsoc = $request->input('razsoc');
            $sigla = $request->input('sigla');
            $email = $request->input('email');
            $direccion = $request->input('direccion');
            $telefono = $request->input('telefono');
            $codciu = $request->input('codciu');
            $pagweb = $request->input('pagweb');
            $pagfac = $request->input('pagfac');
            $pagtwi = $request->input('pagtwi');
            $pagyou = $request->input('pagyou');

            $response = $this->db->begin();
            $mercurio02 = Mercurio02::firstOrNew(['codcaj' => $codcaj]);

            $mercurio02->setCodcaj($codcaj);
            $mercurio02->setNit($nit);
            $mercurio02->setRazsoc($razsoc);
            $mercurio02->setSigla($sigla);
            $mercurio02->setEmail($email);
            $mercurio02->setDireccion($direccion);
            $mercurio02->setTelefono($telefono);
            $mercurio02->setCodciu($codciu);
            $mercurio02->setPagweb($pagweb);
            $mercurio02->setPagfac($pagfac);
            $mercurio02->setPagtwi($pagtwi);
            $mercurio02->setPagyou($pagyou);

            if (! $mercurio02->save()) {
                $this->db->rollback();
                throw new DebugException('Error al guardar el registro');
            }

            $this->db->commit();
            $response = 'Operación realizada con éxito';

            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $this->db->rollback();
            $response = 'No se pudo guardar/editar el registro: ' . $e->getMessage();

            return $this->renderObject($response, false);
        }
    }
}
