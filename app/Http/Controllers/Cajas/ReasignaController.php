<?php

namespace App\Http\Controllers\Cajas;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use App\Models\Mercurio30;
use App\Models\Mercurio31;
use App\Models\Mercurio32;
use App\Models\Mercurio34;
use App\Models\Mercurio35;
use App\Services\Request as ServicesRequest;
use App\Services\Tag;
use App\Services\Utils\GeneralService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ReasignaController extends ApplicationController
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
        $help = "Esta opcion permite manejar los ";
        $this->setParamToView("help", $help);
        $this->setParamToView("title", "Reasigna");
        #Tag::setDocumentTitle('Reasigna');
    }

    public function proceso_reasignar_masivoAction(Request $request)
    {
        $this->setResponse("ajax");
        try {
            $tipopc = $request->input("tipopc_proceso");
            $usuori = $request->input("usuori");
            $usudes = $request->input("usudes");
            $fecini = $request->input("fecini");
            $fecfin = $request->input("fecfin");
            $model = '';
            if ($tipopc == 1) {
                $model = new Mercurio31();
            }
            if ($tipopc == 2) {
                $model = new Mercurio30(); // No tiene el campo fecsol
            }
            if ($tipopc == 3) {
                $model = new Mercurio32();
            }
            if ($tipopc == 4) {
                $model = new Mercurio34();
            }
            if ($tipopc == 7) {
                $model = new Mercurio35();
            }
            $this->reasigna_proceso($model, $usuori, $usudes, $fecini, $fecfin);

            $response = array(
                'success' => true,
                'flag' => true,
                'msj' => "Asignacion de solicitudes con exito"
            );
        } catch (Exception $e) {
            $response = array(
                'success' => false,
                'flag' => false,
                'msj' => $e->getMessage()
            );
        }
        return $this->renderObject($response, false);
    }

    function reasigna_proceso($model, $usuori, $usudes, $fecini, $fecfin)
    {
        if (!$model) return;
        $tablaData = $model->find(" usuario ='{$usuori}' AND fecsol between '{$fecini}' AND '{$fecfin}' AND estado = 'P'");
        foreach ($tablaData as $mtabla) {
            $mtabla->setUsuario($usudes);
            $mtabla->save();
        }
    }

    public function traerDatosAction(Request $request)
    {
        $this->setResponse("ajax");
        $tipopc = $request->input("tipopc");
        $usuario = $request->input("usuario");
        $response  = "<table class='table table-hover align-items-center table-bordered'>";
        $response .= "<thead class='thead-dark'>";
        $response .= "<tr>";
        $response .= "<th scope='col'>Id</th>";
        $response .= "<th scope='col'>Documento</th>";
        $response .= "<th scope='col'>Nombre</th>";
        if ($tipopc == "8" || $tipopc == "5") $response .= "<th scope='col'></th>";
        $response .= "<th scope='col'></th>";
        $response .= "</tr>";
        $response .= "</thead>";
        $response .= "<tbody class='list'>";

        $consultasOldServices = new  GeneralService();
        $mercurio = $consultasOldServices->consultaTipopc($tipopc, "alluser", "", $usuario);

        foreach ($mercurio['datos'] as $mmercurio) {
            if ($tipopc == 1 || $tipopc == 9 || $tipopc == 10 || $tipopc == 11 || $tipopc == 12) { //trabajador
                $documento = "getCedtra";
                $nombre = "getNombre";
            }
            if ($tipopc == 2) { //empresa
                $documento = "getNit";
                $nombre = "getRazsoc";
            }
            if ($tipopc == 3) { //conyuge
                $documento = "getCedcon";
                $nombre = "getNombre";
            }
            if ($tipopc == 4) { //beneficiario
                $documento = "getDocumento";
                $nombre = "getNombre";
            }
            if ($tipopc == 5) { //basicos
                $documento = "getDocumento";
                $nombre = "getDocumentoDetalle";
                $extra = $mmercurio->getCampoDetalle() . " - " . $mmercurio->getAntval() . " - " . $mmercurio->getValor();
            }
            if ($tipopc == 7) { //retiro
                $documento = "getCedtra";
                $nombre = "getNomtra";
            }
            if ($tipopc == 8) { //certificiados
                $documento = "getCodben";
                $nombre = "getNombre";
                $extra = $mmercurio->getNomcer();
            }
            $response .= "<tr>";
            $response .= "<td>{$mmercurio->getId()}</td>";
            $response .= "<td>{$mmercurio->$documento()}</td>";
            $response .= "<td>{$mmercurio->$nombre()}</td>";
            if ($tipopc == "8" || $tipopc == "5") $response .= "<td>$extra</td>";
            $response .= "<td class='table-actions'>";
            $response .= "<a href='#!' class='table-action btn btn-xs btn-primary' title='Info' onclick=\"info('$tipopc','{$mmercurio->getId()}')\">";
            $response .= "<i class='fas fa-info'></i>";
            $response .= "</a>";
            $response .= "</td>";
            $response .= "</tr>";
        }
        $response .= "</tbody>";
        $response .= "</table>";
        return $this->renderObject($response, false);
    }

    public function inforAction(Request $request)
    {
        $this->setResponse("ajax");
        $tipopc = $request->input('tipopc');
        $id = $request->input('id');
        $response = "";
        $consultasOldServices = new  GeneralService();
        $result = $consultasOldServices->consultaTipopc($tipopc, "info", $id);

        $response = $result['consulta'];
        $response .= "<div class='jumbotron'>";
        $response .= "<h1 class='display-4'>Cambio Responsable!</h1>";
        $response .= "<p class='lead'>Esta opcion permite cambiar el responsable</p>";
        $response .= "<hr class='my-4'>";
        $response .= "<p class='lead'>";
        $response .= "<div class='form-group'>";
        $response .= Tag::selectStatic(new ServicesRequest([
            "name" => "usuario_rea",
            "options" => $this->Gener02->find("usuario in (select usuario from mercurio08 where tipopc='$tipopc')"),
            "using" => "usuario,nombre",
            "use_dummy" => true,
            "dummyValue" => "",
            "class" => "form-control"
        ]));
        $response .= "</div>";
        $response .= "<button type='button' class='btn btn-warning btn-lg btn-block' onclick='cambiar_usuario($tipopc,$id)'>Cambiar Usuario Responsable</button>";
        $response .= "</p>";
        $response .= "</div>";
        return $this->renderText($response);
    }

    public function cambiar_usuarioAction(Request $request)
    {
        try {

            $this->setResponse("ajax");
            $tipopc = $request->input('tipopc');
            $id = $request->input('id');
            $usuario = $request->input('usuario');
            $modelos = array("mercurio33", "Mercurio35");

            $response = $this->db->begin();
            $consultasOldServices = new  GeneralService();
            $result = $consultasOldServices->consultaTipopc($tipopc, "one", $id);

            $mercurio = $result['datos'];
            $mercurio->setUsuario($usuario);
            if (!$mercurio->save()) {
                parent::setLogger($mercurio->getMessages());
                $this->db->rollback();
            }
            $this->db->commit();
            $response = parent::successFunc("Cambio de Usuario con Exito");
            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $response = parent::errorFunc("No se pudo realizar la opcion");
            return $this->renderObject($response, false);
        }
    }
}
