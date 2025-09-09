<?php

namespace App\Http\Controllers\Mercurio;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use App\Models\Mercurio30;
use App\Models\Mercurio36;
use App\Models\Mercurio38;
use App\Models\Mercurio39;
use App\Models\Mercurio40;

class ParticularController extends ApplicationController
{

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
        return view("mercurio/particular/index", [
            "title" => "Subsidio Empresa"
        ]);
    }

    public function historialAction()
    {
        $documento = $this->user['documento'];

        $mercurio30 = Mercurio30::where('nit', $documento)->orderBy('id', 'DESC')->get();
        $mercurio36 = Mercurio36::where('cedtra', $documento)->orderBy('id', 'DESC')->get();
        $mercurio38 = Mercurio38::where('cedtra', $documento)->orderBy('id', 'DESC')->get();
        $mercurio39 = Mercurio39::where('cedtra', $documento)->orderBy('id', 'DESC')->get();
        $mercurio40 = Mercurio40::where('cedtra', $documento)->orderBy('id', 'DESC')->get();

        $html_empresa  = "<table class='table table-hover align-items-center table-bordered'>";
        $html_empresa .= "<thead >";
        $html_empresa .= "<tr>";
        $html_empresa .= "<th scope='col'>Nit</th>";
        $html_empresa .= "<th scope='col'>Razon Social </th>";
        $html_empresa .= "<th scope='col'>Estado</th>";
        $html_empresa .= "<th scope='col'>Fecha Estado</th>";
        $html_empresa .= "<th scope='col'>Motivo</th>";
        $html_empresa .= "</tr>";
        $html_empresa .= "</thead>";
        $html_empresa .= "<tbody class='list'>";
        if (count($mercurio30) == 0) {
            $html_empresa .= "<tr align='center'>";
            $html_empresa .= "<td colspan=5><label>No hay datos para mostrar</label></td>";
            $html_empresa .= "<tr>";
            $html_empresa .= "</tr>";
        }
        foreach ($mercurio30 as $mmercurio30) {
            $html_empresa .= "<tr>";
            $html_empresa .= "<td>{$mmercurio30->getNit()}</td>";
            $html_empresa .= "<td>{$mmercurio30->getRazsoc()}</td>";
            $html_empresa .= "<td>{$mmercurio30->getEstado()}</td>";
            $html_empresa .= "<td>{$mmercurio30->getFecest()}</td>";
            $html_empresa .= "<td>{$mmercurio30->getMotivo()}</td>";
            $html_empresa .= "</tr>";
        }
        $html_empresa .= "</tbody>";
        $html_empresa .= "</table>";

        $html_facultativo  = "<table class='table table-hover align-items-center table-bordered'>";
        $html_facultativo .= "<thead >";
        $html_facultativo .= "<tr>";
        $html_facultativo .= "<th scope='col'>Cedula</th>";
        $html_facultativo .= "<th scope='col'>Nombre </th>";
        $html_facultativo .= "<th scope='col'>Estado</th>";
        $html_facultativo .= "<th scope='col'>Fecha Estado</th>";
        $html_facultativo .= "<th scope='col'>Motivo</th>";
        $html_facultativo .= "</tr>";
        $html_facultativo .= "</thead>";
        $html_facultativo .= "<tbody class='list'>";
        if (count($mercurio36) == 0) {
            $html_facultativo .= "<tr align='center'>";
            $html_facultativo .= "<td colspan=5><label>No hay datos para mostrar</label></td>";
            $html_facultativo .= "<tr>";
            $html_facultativo .= "</tr>";
        }
        foreach ($mercurio36 as $mmercurio36) {
            $html_facultativo .= "<tr>";
            $html_facultativo .= "<td>{$mmercurio36->getCedtra()}</td>";
            $html_facultativo .= "<td>{$mmercurio36->getPriape()} {$mmercurio36->getPrinom()}</td>";
            $html_facultativo .= "<td>{$mmercurio36->getEstado()}</td>";
            $html_facultativo .= "<td>{$mmercurio36->getFecest()}</td>";
            $html_facultativo .= "<td>{$mmercurio36->getMotivo()}</td>";
            $html_facultativo .= "</tr>";
        }
        $html_facultativo .= "</tbody>";
        $html_facultativo .= "</table>";

        $html_pensionado  = "<table class='table table-hover align-items-center table-bordered'>";
        $html_pensionado .= "<thead >";
        $html_pensionado .= "<tr>";
        $html_pensionado .= "<th scope='col'>Cedula</th>";
        $html_pensionado .= "<th scope='col'>Nombre </th>";
        $html_pensionado .= "<th scope='col'>Estado</th>";
        $html_pensionado .= "<th scope='col'>Fecha Estado</th>";
        $html_pensionado .= "<th scope='col'>Motivo</th>";
        $html_pensionado .= "</tr>";
        $html_pensionado .= "</thead>";
        $html_pensionado .= "<tbody class='list'>";
        if (count($mercurio38) == 0) {
            $html_pensionado .= "<tr align='center'>";
            $html_pensionado .= "<td colspan=5><label>No hay datos para mostrar</label></td>";
            $html_pensionado .= "<tr>";
            $html_pensionado .= "</tr>";
        }
        foreach ($mercurio38 as $mmercurio38) {
            $html_pensionado .= "<tr>";
            $html_pensionado .= "<td>{$mmercurio38->getCedtra()}</td>";
            $html_pensionado .= "<td>{$mmercurio38->getPriape()} {$mmercurio38->getPrinom()}</td>";
            $html_pensionado .= "<td>{$mmercurio38->getEstado()}</td>";
            $html_pensionado .= "<td>{$mmercurio38->getFecest()}</td>";
            $html_pensionado .= "<td>{$mmercurio38->getMotivo()}</td>";
            $html_pensionado .= "</tr>";
        }
        $html_pensionado .= "</tbody>";
        $html_pensionado .= "</table>";

        $html_comunitaria  = "<table class='table table-hover align-items-center table-bordered'>";
        $html_comunitaria .= "<thead >";
        $html_comunitaria .= "<tr>";
        $html_comunitaria .= "<th scope='col'>Cedula</th>";
        $html_comunitaria .= "<th scope='col'>Nombre </th>";
        $html_comunitaria .= "<th scope='col'>Estado</th>";
        $html_comunitaria .= "<th scope='col'>Fecha Estado</th>";
        $html_comunitaria .= "<th scope='col'>Motivo</th>";
        $html_comunitaria .= "</tr>";
        $html_comunitaria .= "</thead>";
        $html_comunitaria .= "<tbody class='list'>";
        if (count($mercurio39) == 0) {
            $html_comunitaria .= "<tr align='center'>";
            $html_comunitaria .= "<td colspan=5><label>No hay datos para mostrar</label></td>";
            $html_comunitaria .= "<tr>";
            $html_comunitaria .= "</tr>";
        }
        foreach ($mercurio39 as $mmercurio39) {
            $html_comunitaria .= "<tr>";
            $html_comunitaria .= "<td>{$mmercurio39->getCedtra()}</td>";
            $html_comunitaria .= "<td>{$mmercurio39->getPriape()} {$mmercurio39->getPrinom()}</td>";
            $html_comunitaria .= "<td>{$mmercurio39->getEstado()}</td>";
            $html_comunitaria .= "<td>{$mmercurio39->getFecest()}</td>";
            $html_comunitaria .= "<td>{$mmercurio39->getMotivo()}</td>";
            $html_comunitaria .= "</tr>";
        }
        $html_comunitaria .= "</tbody>";
        $html_comunitaria .= "</table>";

        $html_domestico  = "<table class='table table-hover align-items-center table-bordered'>";
        $html_domestico .= "<thead >";
        $html_domestico .= "<tr>";
        $html_domestico .= "<th scope='col'>Cedula</th>";
        $html_domestico .= "<th scope='col'>Nombre </th>";
        $html_domestico .= "<th scope='col'>Estado</th>";
        $html_domestico .= "<th scope='col'>Fecha Estado</th>";
        $html_domestico .= "<th scope='col'>Motivo</th>";
        $html_domestico .= "</tr>";
        $html_domestico .= "</thead>";
        $html_domestico .= "<tbody class='list'>";
        if (count($mercurio40) == 0) {
            $html_domestico .= "<tr align='center'>";
            $html_domestico .= "<td colspan=5><label>No hay datos para mostrar</label></td>";
            $html_domestico .= "<tr>";
            $html_domestico .= "</tr>";
        }
        foreach ($mercurio40 as $mmercurio40) {
            $html_domestico .= "<tr>";
            $html_domestico .= "<td>{$mmercurio40->getCedtra()}</td>";
            $html_domestico .= "<td>{$mmercurio40->getPriape()} {$mmercurio40->getPrinom()}</td>";
            $html_domestico .= "<td>{$mmercurio40->getEstado()}</td>";
            $html_domestico .= "<td>{$mmercurio40->getFecest()}</td>";
            $html_domestico .= "<td>{$mmercurio40->getMotivo()}</td>";
            $html_domestico .= "</tr>";
        }
        $html_domestico .= "</tbody>";
        $html_domestico .= "</table>";

        return view("mercurio/particular/historial", [
            "title" => "Historial",
            "html_empresa" => $html_empresa,
            "html_facultativo" => $html_facultativo,
            "html_pensionado" => $html_pensionado,
            "html_domestico" => $html_domestico,
            "html_comunitaria" => $html_comunitaria
        ]);
    }
}
