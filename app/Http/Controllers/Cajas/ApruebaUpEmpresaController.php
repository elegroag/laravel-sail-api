<?php

namespace App\Http\Controllers\Cajas;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use App\Services\Utils\Pagination;
use App\Services\CajaServices\UpDatosEmpresaServices;
use App\Services\Tag;
use App\Services\View;
use App\Services\Utils\CalculatorDias;
use App\Services\Utils\Comman;
use App\Library\Collections\ParamsEmpresa;
use App\Models\Mercurio10;
use App\Models\Mercurio33;
use App\Models\Mercurio47;
use App\Models\Gener42;
use App\Models\Mercurio11;
use App\Services\Aprueba\ApruebaSolicitud;
use App\Services\Srequest;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ApruebaUpEmpresaController extends ApplicationController
{

    protected $tipopc = 5;
    protected $db;
    protected $user;
    protected $tipo;

    public function __construct()
    {
        $this->db = DbBase::rawConnect();
        $this->user = session()->has('user') ? session('user') : null;
        $this->tipo = session()->has('tipo') ? session('tipo') : null;
    }

    public function aplicarFiltroAction(Request $request, string $estado = 'P')
    {
        $this->setResponse("ajax");
        $cantidad_pagina = $request->input("numero", 10);
        $usuario = $this->user['usuario'];
        $query_str = ($estado == 'T') ? " estado='{$estado}'" : "usuario='{$usuario}' and estado='{$estado}'";

        $pagination = new Pagination(
            new Srequest([
                "cantidadPaginas" => $cantidad_pagina,
                "query" => $query_str,
                "estado" => $estado
            ])
        );

        $query = $pagination->filter(
            $request->input('campo'),
            $request->input('condi'),
            $request->input('value')
        );

        set_flashdata("filter_datos_empresa", $query, true);
        set_flashdata("filter_params", $pagination->filters, true);

        $response = $pagination->render(new UpDatosEmpresaServices());
        return $this->renderObject($response);
    }

    public function changeCantidadPaginaAction(Request $request, string $estado = 'P')
    {
        return $this->buscarAction($request, $estado);
    }

    public function indexAction()
    {
        $campo_field = array(
            "nit" => "NIT",
            "priape" => "Primer apellido",
            "segape" => "Segundo apellido",
            "prinom" => "Primer nombre",
            "segnom" => "Segundo nombre",
            "cedrep" => "Cedula representante",
            "fecsol" => "Fecha solicitud",
        );

        $params = $this->loadParametrosView();
        return view('cajas.actualizaemp.index', [
            ...$params,
            "campo_filtro" => $campo_field,
            "filters" => get_flashdata_item("filter_params"),
            "title" => "Aprueba actualización",
            "buttons" => array("F"),
            "mercurio11" => Mercurio11::get()
        ]);
    }

    public function opcionalAction($estado = 'P')
    {
        $this->setParamToView("hide_header", true);
        $campo_field = array(
            "nit" => "Nit",
            "razsoc" => "Razon Social"
        );
        $help = "Esta opcion permite manejar los ";
        $this->setParamToView("help", $help);
        $this->setParamToView("title", "Aprobacion Empresa");
        $mercurio30 = $this->Mercurio30->find("estado='{$estado}' AND usuario=" . parent::getActUser() . " ORDER BY fecini ASC");
        $empresas = array();
        foreach ($mercurio30 as $ai => $mercurio) {
            $background = '';
            $dias_vencidos = CalculatorDias::calcular($this->tipopc, $mercurio->getId(), $mercurio->getFecini());
            if ($estado == 'P') {
                if ($dias_vencidos == 3) {
                    $background = '#f1f1ad';
                } else if ($dias_vencidos > 3) {
                    $background = '#f5b2b2';
                }
            }
            $url = env('APP_URL') . "Cajas/aprobacionemp/info_empresa/" . $mercurio->getId();
            $sat = "NORMAL";
            if ($mercurio->getDocumentoRepresentanteSat() > 0) {
                $sat = "SAT";
            }
            $empresas[] = array(
                "estado" => $mercurio->getEstadoDetalle(),
                "recepcion" => $sat,
                "nit" => $mercurio->getNit(),
                "background" => $background,
                "razsoc" => $mercurio->getRazsoc(),
                "dias_vencidos" => $dias_vencidos,
                "id" => $mercurio->getId(),
                "url" => $url
            );
        }

        $this->setParamToView("empresas", $empresas);
        $this->setParamToView("buttons", array("F"));
        $this->setParamToView("campo_filtro", $campo_field);
        $this->setParamToView("pagina_con_estado", $estado);
    }

    public function buscarAction(Request $request, $estado = 'P')
    {
        $this->setResponse("ajax");
        $pagina = $request->input('pagina', 1);
        $cantidad_pagina = $request->input("numero", 10);
        $usuario = $this->user['usuario'];
        $query_str = ($estado == 'T') ? " estado='{$estado}'" : "usuario='{$usuario}' and estado='{$estado}'";

        $pagination = new Pagination(
            new Srequest([
                "cantidadPaginas" => $cantidad_pagina,
                "pagina" => $pagina,
                "query" => $query_str,
                "estado" => $estado
            ])
        );

        if (
            get_flashdata_item("filter_empresa") != false
        ) {
            $query = $pagination->persistencia(get_flashdata_item("filter_params"));
        } else {
            $query = $pagination->filter(
                $request->input('campo'),
                $request->input('condi'),
                $request->input('value')
            );
        }

        set_flashdata("filter_empresa", $query, true);
        set_flashdata("filter_params", $pagination->filters, true);

        $response = $pagination->render(new UpDatosEmpresaServices());
        return $this->renderObject($response, false);
    }

    /**
     * devolverAction function
     * @return void
     */
    public function devolverAction(Request $request)
    {
        $this->setResponse("ajax");
        $modelos = array("mercurio10", "mercurio47");

        $this->db->begin();
        try {
            $id = $request->input('id', "addslaches", "alpha", "extraspaces", "striptags");
            $codest = $request->input('codest', "addslaches", "alpha", "extraspaces", "striptags");
            $nota = $request->input('nota');
            $array_corregir = $request->input('campos_corregir');
            $campos_corregir = implode(";", $array_corregir);

            $today = Carbon::now();
            $mercurio47 = $this->Mercurio47->findFirst("id='{$id}'");
            if ($mercurio47->getEstado() == 'D') {
                throw new DebugException("El registro ya se encuentra devuelto, no se requiere de repetir la acción.", 201);
            }

            Mercurio47::where('id', $id)->update([
                'estado' => 'D',
                'fecha_estado' => $today->format('Y-m-d H:i:s')
            ]);


            $item = $this->Mercurio10->maximum("item", "conditions: tipopc='$this->tipopc' and numero='$id'") + 1;

            $mercurio10 = new Mercurio10;

            $mercurio10->setTipopc($this->tipopc);
            $mercurio10->setNumero($id);
            $mercurio10->setItem($item);
            $mercurio10->setEstado("D");
            $mercurio10->setNota($nota);
            $mercurio10->setCodest($codest);
            $mercurio10->setFecsis($today->format('Y-m-d H:i:s'));
            if (!$mercurio10->save()) {
                $msj = "";
                foreach ($mercurio10->getMessages() as $key => $message) $msj .= $message . "<br/>";
                throw new DebugException("Error " . $msj, 501);
            }
            $this->Mercurio10->updateAll("campos_corregir='{$campos_corregir}'", "conditions: item='{$item}' AND numero='{$id}' AND tipopc='{$this->tipopc}'");

            $this->db->commit();

            $salida = array(
                "success" => true,
                "msj" => "El proceso se ha completado con éxito"
            );
        } catch (DebugException $e) {
            $salida = array(
                "success" => false,
                "msj" => $e->getMessage(),
                "code" => 500
            );
        }
        return $this->renderObject($salida, false);
    }

    /**
     * rechazarAction function
     * @return void
     */
    public function rechazarAction(Request $request)
    {
        $this->setResponse("ajax");
        $modelos = array("mercurio10", "mercurio47");

        $this->db->begin();
        try {

            $id = $request->input('id', "addslaches", "alpha", "extraspaces", "striptags");
            $nota = $request->input('nota');
            $codest = $request->input('codest', "addslaches", "alpha", "extraspaces", "striptags");

            $today = Carbon::now();
            $mercurio47 = $this->Mercurio47->findFirst("id='$id'");
            if ($mercurio47->getEstado() == 'X') {
                throw new DebugException("El registro ya se encuentra rechazado, no se requiere de repetir la acción.", 201);
            }
            $this->db->inQueryAssoc("UPDATE mercurio47 SET estado='X', fecha_estado='" . $today->format('Y-m-d H:i:s') . "' WHERE id='{$id}'");
            $item = $this->Mercurio10->maximum("item", "conditions: tipopc='{$this->tipopc}' and numero='{$id}'") + 1;

            $mercurio10 = new Mercurio10();

            $mercurio10->setTipopc($this->tipopc);
            $mercurio10->setNumero($id);
            $mercurio10->setItem($item);
            $mercurio10->setEstado("X");
            $mercurio10->setNota($nota);
            $mercurio10->setCodest($codest);
            $mercurio10->setFecsis($today->format('Y-m-d H:i:s'));

            if (!$mercurio10->save()) {
                $msj = "";
                foreach ($mercurio10->getMessages() as $key => $mess) $msj .= $mess->getMessage() . "<br/>";
                throw new DebugException("Error " . $msj, 501);
            }

            $this->db->commit();
            $salida = array(
                "success" => true,
                "msj" => "El proceso se ha completado con éxito"
            );
        } catch (DebugException $e) {
            $salida = array(
                "success" => false,
                "msj" => $e->getMessage(),
                "code" => 500
            );
        }
        return $this->renderObject($salida, false);
    }

    /**
     * info_empresaAction function
     * mostrar la ficha de afiliación de la empresa
     * @return void
     */
    public function info_actualizaAction($id = 0)
    {
        if (!$id) {
            return redirect("actualizardatos/index");
            exit;
        }
        $this->setParamToView("hide_header", true);
        $mercurio47 = $this->Mercurio47->findFirst("id='{$id}'");
        if ($mercurio47->getEstado() == "A") {
            set_flashdata("success", array(
                "msj" => "La empresa {$mercurio47->getDocumento()}, ya se encuentra aprobada su afiliación. Y no requiere de más acciones.",
                "code" => 200
            ));
        }
        $mercurio28 = $this->db->inQueryAssoc("SELECT * FROM mercurio28 WHERE tipo='E'");
        $mercurio33 = $this->db->inQueryAssoc("SELECT * FROM mercurio33 WHERE actualizacion='{$id}'");
        $mercurio37 = $this->db->inQueryAssoc("SELECT * FROM  mercurio37 WHERE tipopc='{$this->tipopc}' and numero='{$mercurio47->getId()}'");
        $mercurio12 = $this->db->inQueryAssoc("SELECT * FROM mercurio12");
        $_mercurio12 = array();
        foreach ($mercurio12  as $ai => $m12) $_mercurio12["{$m12['coddoc']}"] = $m12['detalle'];

        $this->setParamToView("_mercurio12", $_mercurio12);
        $this->setParamToView("mercurio28", $mercurio28);
        $this->setParamToView("mercurio33", $mercurio33);
        $this->setParamToView("mercurio37", $mercurio37);
        $this->setParamToView("mercurio47", $mercurio47);
        $this->setParamToView("mercurio11", $this->Mercurio11->find());

        $this->loadParametrosView();
        $this->loadDisplay($mercurio33);
        $this->setParamToView("title", "Solicitud actualizar datos empresa");
    }

    function loadParametrosView()
    {
        $ps = Comman::Api();
        $ps->runCli(
            array(
                "servicio" => "ComfacaAfilia",
                "metodo" => "parametros_empresa"
            )
        );
        $datos_captura =  $ps->toArray();
        $_tipdur = array();
        foreach ($datos_captura['tipo_duracion'] as $data) $_tipdur[$data['estado']] = $data['detalle'];
        $_codind = array();
        foreach ($datos_captura['codigo_indice'] as $data) $_codind[$data['codind']] = $data['detalle'];
        $_todmes = array();
        foreach ($datos_captura['paga_mes'] as $data) $_todmes[$data['estado']] = $data['detalle'];
        $_forpre = array();
        foreach ($datos_captura['forma_presentacion'] as $data) $_forpre[$data['estado']] = $data['detalle'];
        $_pymes = array();
        foreach ($datos_captura['pymes'] as $data) $_pymes[$data['estado']] = $data['detalle'];
        $_contratista = array();
        foreach ($datos_captura['contratista'] as $data) $_contratista[$data['estado']] = $data['detalle'];
        $_tipemp = array();
        foreach ($datos_captura['tipo_empresa'] as $data) $_tipemp[$data['estado']] = $data['detalle'];
        $_tipsoc = array();
        foreach ($datos_captura['tipo_sociedades'] as $data) $_tipsoc[$data['tipsoc']] = $data['detalle'];
        $_tipapo = array();
        foreach ($datos_captura['tipo_aportante'] as $data) $_tipapo[$data['estado']] = $data['detalle'];
        $_ofiafi = array();
        foreach ($datos_captura['oficina'] as $data) $_ofiafi["{$data['ofiafi']}"] = $data['detalle'];
        $_colegio = array();
        foreach ($datos_captura['colegio'] as $data) $_colegio["{$data['estado']}"] = $data['detalle'];
        $_tipper = array();
        foreach ($datos_captura['tipo_persona'] as $data) $_tipper[$data['estado']] = $data['detalle'];
        $_coddoc = array();
        foreach ($datos_captura['tipo_documentos'] as $data) $_coddoc[$data['coddoc']] = $data['detdoc'];
        $_calemp = array();
        foreach ($datos_captura['calidad_empresa'] as $data) $_calemp[$data['estado']] = $data['detalle'];
        $_codciu = array();
        foreach ($datos_captura['ciudades'] as $data) $_codciu[$data['codciu']] = $data['detciu'];
        $_ciupri = array();
        foreach ($datos_captura['ciudad_comercial'] as $data) $_ciupri["{$data['codciu']}"] = $data['detciu'];
        $_codzon = array();
        foreach ($datos_captura['zonas'] as $data) $_codzon["{$data['codzon']}"] = $data['detzon'];
        $_codact = array();
        foreach ($datos_captura['actividades'] as $data) $_codact["{$data['codact']}"] = $data['codact'] . ' - ' . $data['detalle'];

        $_coddocrepleg = array();
        foreach ($datos_captura['tipo_documentos'] as $data) {
            if ($data['codrua'] == 'TI' || $data['codrua'] == 'RC') continue;
            $_coddocrepleg["{$data['codrua']}"] = $data['detdoc'];
        }

        return [
            "_tipdur" => $_tipdur,
            "_codind" => $_codind,
            "_contratista" => $_contratista,
            "_todmes" => $_todmes,
            "_forpre" => $_forpre,
            "_tipsoc" => $_tipsoc,
            "_pymes" => $_pymes,
            "_tipemp" => $_tipemp,
            "_tipapo" => $_tipapo,
            "_ofiafi" => $_ofiafi,
            "_colegio" => $_colegio,
            "_tipper" => $_tipper,
            "_codzon" => $_codzon,
            "_calemp" => $_calemp,
            "_codciu" => $_codciu,
            "_codact" => $_codact,
            "_coddoc" => $_coddoc,
            "_ciupri" => $_ciupri,
            "_coddocrepleg" => $_coddocrepleg,
        ];
    }

    public function loadDisplay($mercurio33)
    {
        foreach ($mercurio33 as $aj => $row) {
            $campo = $row['campo'];
            Tag::displayTo("{$campo}", $row['valor']);
        }
    }

    function send_email($emisor, $asunto, $mensaje, $destinatarios)
    {
        /* Core::importFromLibrary("Swift", "Swift.php");
        Core::importFromLibrary("Swift", "Swift/Connection/SMTP.php");
        $smtp = new Swift_Connection_SMTP(
            "smtp.gmail.com",
            Swift_Connection_SMTP::PORT_SECURE,
            Swift_Connection_SMTP::ENC_TLS
        );
        $smtp->setUsername($emisor['email']);
        $smtp->setPassword($emisor['clave']);
        $smsj = new Swift_Message();
        $smsj->setSubject($asunto);
        $smsj->setContentType("text/html");
        $smsj->setBody($mensaje);
        $swift = new Swift($smtp);
        $email = new Swift_RecipientList();
        foreach ($destinatarios as $ai => $destinatario) {
            if ($this->production == false) {
                $destinatario['email'] = $this->email_pruebas;
            }
            $email->addTo($destinatario['email'], $destinatario['nombre']);
        }
        $swift->send($smsj, $email, new Swift_Address($emisor['email'])); */
    }

    /**
     * apruebaAction function
     * @return void
     */
    public function apruebaAction(Request $request)
    {
        $this->setResponse("ajax");
        $debuginfo = array();
        try {
            try {
                $user = session()->get('user');
                $acceso = (new Gener42)->count("*", "conditions: permiso='62' AND usuario='{$user['usuario']}'");
                if ($acceso == 0) {
                    return $this->renderObject(array("success" => false, "msj" => "El usuario no dispone de permisos de aprobación"), false);
                }
                $apruebaSolicitud = new ApruebaSolicitud();
                $this->db->begin();

                $postData = $_POST;
                $idSolicitud = $request->input('id', "addslaches", "alpha", "extraspaces", "striptags");
                $calemp = 'UE';
                $solicitud = $apruebaSolicitud->main(
                    $calemp,
                    $idSolicitud,
                    $postData
                );

                $this->db->commit();
                $solicitud->enviarMail($request->input('actapr'), $request->input('fecapr'));
                $salida = array(
                    'success' => true,
                    'msj' => 'El registro se completo con éxito'
                );
            } catch (DebugException $err) {
                $this->db->rollback();
                $salida = array(
                    "success" => false,
                    "msj" => $err->getMessage()
                );
            }
        } catch (DebugException $e) {
            $salida = array(
                "success" => false,
                "msj" => $e->getMessage(),
            );
        }

        if ($debuginfo) $salida['info'] = $debuginfo;
        return $this->renderObject($salida, false);
    }

    public function borrarFiltroAction()
    {
        $this->setResponse("ajax");
        if (get_flashdata_item("filter_actualizacion")) {
            set_flashdata("filter_actualizacion", "1=1", true);
        }
        echo "{\"success\":true}";
    }

    /**
     * inforAction function
     * mostrar la ficha de afiliación de la empresa
     * @return void
     */
    public function inforAction(Request $request)
    {
        $this->setResponse("ajax");
        try {
            $upServices = new UpDatosEmpresaServices();
            $id = $request->input('id');
            if (!$id) {
                throw new DebugException("Error se requiere del id independiente", 501);
            }

            $mercurio47 = (new Mercurio47)->findFirst("id='{$id}'");
            $mercurio33 = (new Mercurio33)->find("actualizacion='{$id}'");
            $dataItems = array();

            foreach ($mercurio33 as $row) {
                $campo = $row->getCampo();
                $dataItems["{$campo}"] = $row->getValor();
            }

            $ps = Comman::Api();
            $ps->runCli(
                array(
                    "servicio" => "ComfacaAfilia",
                    "metodo" => "parametros_empresa"
                )
            );

            $datos_captura =  $ps->toArray();
            $paramsIndependiente = new ParamsEmpresa();
            $paramsIndependiente->setDatosCaptura($datos_captura);

            $htmlEmpresa = View::render('actualizardatos/tmp/consulta', array(
                'mercurio47' => $mercurio47,
                'dataItems' => $dataItems,
                'mercurio01' => $this->Mercurio01->findFirst(),
                'det_tipo' => $this->Mercurio06->findFirst("tipo = '{$mercurio47->getTipo()}'")->getDetalle(),
                '_coddoc' => ParamsEmpresa::getTipoDocumentos(),
                '_calemp' => ParamsEmpresa::getCalidadEmpresa(),
                '_codciu' => ParamsEmpresa::getCiudades(),
                '_codzon' => ParamsEmpresa::getZonas(),
                '_codact' => ParamsEmpresa::getActividades(),
                '_tipsoc' => ParamsEmpresa::getTipoSociedades(),
                '_tipdoc' => ParamsEmpresa::getTipoDocumentos()
            ));

            $ps = Comman::Api();
            $ps->runCli(
                array(
                    "servicio" => "ComfacaEmpresas",
                    "metodo" => "informacion_empresa",
                    "params" => array(
                        "nit" => $mercurio47->getDocumento()
                    )
                )
            );
            $out =  $ps->toArray();

            if ($out['success']) {
                $this->setParamToView("empresa_sisuweb", $out['data']);
            }
            $response = array(
                'success' => true,
                'data' => $mercurio47->getArray(),
                'mercurio11' => $this->Mercurio11->find(),
                "consulta_empresa" => $htmlEmpresa,
                'adjuntos' => $upServices->adjuntos($mercurio47),
                'seguimiento' => $upServices->seguimiento($mercurio47),
                'campos_disponibles' => $mercurio47->CamposDisponibles()
            );
        } catch (DebugException $err) {
            $response = array(
                'success' => false,
                'msj' => $err->getMessage()
            );
        }
        return $this->renderObject($response, false);
    }
}
