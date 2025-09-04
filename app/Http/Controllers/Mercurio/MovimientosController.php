<?php
namespace App\Http\Controllers\Mercurio;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Library\Auth\AuthJwt;
use App\Library\Auth\SessionCookies;
use App\Models\Adapter\DbBase;
use App\Models\Mercurio01;
use App\Models\Mercurio02;
use App\Models\Mercurio07;
use App\Services\Utils\Comman;
use App\Services\Utils\GeneralService;
use App\Services\Utils\SenderEmail;
use Illuminate\Http\Request;

class MovimientosController extends ApplicationController
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
        return view('mercurio.movimientos.index', [
            'hide_header' => true,
            'title' => 'Movimientos'
        ]);
    }

    public function historialAction()
    {
        $generalService = new GeneralService();
        $generalService->registrarLog(false, "Historial", "");
        if ($this->tipo == "E") {
            return redirect("subsidioemp.historial");
        }
        if ($this->tipo  == "T") {
            return redirect("subsidio.historial");
        }
        if ($this->tipo == "P") {
            return redirect("particular.historial");
        }
    }

    public function cambio_email_viewAction()
    {
        return view("perfil.cambio_email", [
            "title" => "Cambio Email",
            "email" => "email"
        ]);
    }

    public function cambio_emailAction(Request $request)
    {
        $this->setResponse("ajax");

        try {
            
                $email = $request->input('email', "addslaches", "extraspaces", "striptags");
                $modelos = array("mercurio07");
                # $Transaccion = parent::startTrans($modelos);
                # $response = parent::startFunc();
                $this->Mercurio07->updateAll("email='$email'", "conditions: tipo='" . parent::getActUser("tipo") . "' and documento = '" . parent::getActUser("documento") . "'");
                $asunto = "Cambio de Email";
                $msj  = "acabas de utilizar nuestro servicio de cambio de email de aviso. Te informamos que fue exitoso";
                $generalService = new GeneralService();
                $generalService->sendEmail(parent::getActUser("email"), parent::getActUser("nombre"), $asunto, $msj, "");
                # parent::finishTrans();

                $response = "Cambio de Email de Aviso con Exito";

                $generalService->registrarLog(false, "Cambio de Email", "");
                return $this->renderText(json_encode($response));
            
        } catch (DebugException $e) {
            $response = "No se pudo realizar la accion";
        }

        return $this->renderObject($response);
    }

    public function cambio_clave_viewAction()
    {
        return view('movimientos.cambio_clave', [
            "title", "Cambio Clave"
        ]);
    }

    public function cambio_claveAction(Request $request)
    {
        $this->setResponse("ajax");
        try {
            $claant = $request->input('claant', "addslaches", "extraspaces", "striptags");
            $clave = $request->input('clave', "addslaches", "extraspaces", "striptags");
            $clacon = $request->input('clacon', "addslaches", "extraspaces", "striptags");
            # $response = parent::startFunc();

            $tipo = parent::getActUser("tipo");
            $documento = parent::getActUser("documento");
            $coddoc = parent::getActUser("coddoc");

            $mercurio07 = (new Mercurio07)->findFirst("tipo='{$tipo}' AND documento='{$documento}' AND coddoc='{$coddoc}'");

            $claant = password_hash_old($claant);
            $claant = md5("" . $claant);

            if ($claant != $mercurio07->getClave()) {
                $response = "La clave no coincide con la actual";
                return $this->renderText(json_encode($response));
            }

            if ($clave != $clacon) {
                $response = "Las claves no coinciden";
                return $this->renderText(json_encode($response));
            }

            $mclave = password_hash_old($clave);
            $mclave = md5("" . $mclave);

            (new Mercurio07)->updateAll("clave='{$mclave}'", "conditions: tipo='{$tipo}' AND documento='{$documento}' AND coddoc='{$coddoc}'");

            $ps = Comman::Api();
            $ps->runCli(
                array(
                    "servicio" => "ComfacaAfilia",
                    "metodo" => "parametros_empresa"
                )
            );
            $datos_captura = $ps->toArray();
            $coddoc_detalle = "";
            foreach ($datos_captura['coddoc'] as $data) {
                if ($data['coddoc'] == $coddoc) {
                    $coddoc_detalle = $data['detalle'];
                    break;
                }
            }

            $mercurio02 = (new Mercurio02)->findFirst();
            $params = array(
                "titulo" => "Cordial saludo, señor@ {$mercurio07->getNombre()}",
                "msj" => "Bienvenido a La Caja de Compensación Familiar del Caqueta COMFACA, " .
                    "Acabas de utilizar nuestro servicio de cambio de clave. Le informamos que fue exitosa.<br/>Las siguientes son las credeciales de acceso",
                "rutaImg" => "https://comfacaenlinea.com.co/public/img/header_reporte_ugpp.png",
                "url_activa" => "https://comfacaenlinea.com.co/Mercurio/Mercurio/login/index",
                "tipo_documento" => $coddoc_detalle,
                "documento" => $documento,
                "clave" => $clave,
                "mercurio02" => array(
                    "razsoc"    => $mercurio02->getRazsoc(),
                    "direccion" => $mercurio02->getDireccion(),
                    "email"     => $mercurio02->getEmail(),
                    "telefono"  => $mercurio02->getTelefono(),
                    "pagweb"    => $mercurio02->getPagweb()
                )
            );

            $html = view("login/tmp/mail", $params)->render();

            $asunto = "Cambio De Clave Portal Comfaca En Línea";
            $email_caja = (new Mercurio01)->findFirst();
            
            $sender_email = new SenderEmail();
            $sender_email->setters(
                "asunto:".$asunto, 
                "emisor_email:".$email_caja->getEmail(), 
                "emisor_clave:".$email_caja->getClave()
            );
            $sender_email->send($mercurio07->getEmail(), $html);
            $generalService = new GeneralService();
            $generalService->registrarLog(false, "Cambio de Clave", "");
            $response = "Cambio de clave se ha realizado con éxito.";
                
        } catch (DebugException $e) {
            $response = "No se pudo realizar la accion";
        }
        return $this->renderObject($response);
    }
}
