<?php

namespace App\Http\Controllers\Cajas;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use App\Models\Mercurio01;
use App\Services\Tag;
use App\Services\Utils\GeneralService;
use App\Services\Utils\Paginate;
use App\Services\Utils\Pagination;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class Mercurio01Controller extends ApplicationController
{

    protected $query = "1=1";
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

    public function indexAction()
    {
        return view('cajas.mercurio01.index', [
            'title' => "Configuración basica"
        ]);
    }

    public function showTabla($paginate)
    {
        $html  = '<table border="0" cellpadding="0" cellspacing="0" class="table table-bordered">';
        $html .= "<thead class='thead-light'>";
        $html .= "<tr>";
        $html .= "<th scope='col'>Aplicativo</th>";
        $html .= "<th scope='col'>Email</th>";
        $html .= "<th scope='col'>Path</th>";
        $html .= "<th scope='col'>Server</th>";
        $html .= "<th scope='col'>Option</th>";
        $html .= "</tr>";
        $html .= "</thead>";
        $html .= "<tbody class='list'>";
        foreach ($paginate->items as $mtable) {
            $html .= "<tr>";
            $html .= "<td>{$mtable->getCodapl()}</td>";
            $html .= "<td>{$mtable->getEmail()}</td>";
            $html .= "<td>{$mtable->getPath()}</td>";
            $html .= "<td>{$mtable->getFtpserver()}</td>";
            $html .= "<td class='table-actions'>";
            $html .= "<a href='#!' class='table-action btn btn-xs btn-primary' title='editar' data-cid='{$mtable->getCodapl()}' data-toggle='editar'>";
            $html .= "<i class='fas fa-user-edit text-white'></i>";
            $html .= "</a>";
            $html .= "</td>";
            $html .= "</tr>";
        }
        $html .= "</tbody>";
        $html .= "</table>";
        return $html;
    }

    public function aplicarFiltroAction(Request $request)
    {
        $consultasOldServices = new GeneralService();
        $this->query = $consultasOldServices->converQuery();
        return $this->buscarAction($request);
    }

    public function changeCantidadPagina(Request $request)
    {
        $this->cantidad_pagina = $request->input("numero");
        return $this->buscarAction($request);
    }

    public function buscarAction(Request $request)
    {
        $pagina = ($request->input('pagina') == "") ? 1 : $request->input('pagina');
        $paginate = Paginate::execute(
            Mercurio01::whereRaw("{$this->query}")->get(),
            $pagina,
            $this->cantidad_pagina
        );
        $html = $this->showTabla($paginate);

        $consultasOldServices = new GeneralService();
        $html_paginate = $consultasOldServices->showPaginate($paginate);
        $response['consulta'] = $html;
        $response['paginate'] = $html_paginate;
        return $this->renderObject($response, false);
    }

    public function editarAction()
    {
        $mercurio01 = Mercurio01::first();
        if ($mercurio01 == false) $mercurio01 = new Mercurio01();
        return $this->renderObject($mercurio01->toArray(), false);
    }

    public function guardarAction(Request $request)
    {
        try {
            $this->setResponse("ajax");
            $codapl = $request->input('codapl');
            $email = $request->input('email');
            $clave = $request->input('clave');
            $path = $request->input('path');
            $ftpserver = $request->input('ftpserver');
            $pathserver = $request->input('pathserver');
            $userserver = $request->input('userserver');
            $passserver = $request->input('passserver');


            $response = $this->db->begin();
            $mercurio01 = new Mercurio01();

            $mercurio01->setCodapl($codapl);
            $mercurio01->setEmail($email);
            $mercurio01->setClave($clave);
            $mercurio01->setPath($path);
            $mercurio01->setFtpserver($ftpserver);
            $mercurio01->setPathserver($pathserver);
            $mercurio01->setUserserver($userserver);
            $mercurio01->setPassserver($passserver);
            if (!$mercurio01->save()) {
                parent::setLogger($mercurio01->getMessages());
                $this->db->rollback();
            }
            $this->db->commit();
            $response = parent::successFunc("Creacion Con Exito");
            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $response = parent::errorFunc("No se puede guardar/editar el Registro");
            return $this->renderObject($response, false);
        }
    }
}
