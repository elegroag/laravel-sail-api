<?php

namespace App\Http\Controllers\Cajas;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use App\Models\Gener09;
use App\Models\Gener18;
use App\Models\Mercurio01;
use App\Models\Mercurio07;
use App\Models\Mercurio19;
use App\Models\Mercurio20;
use App\Models\Mercurio30;
use App\Models\Mercurio31;
use App\Models\Mercurio32;
use App\Models\Mercurio34;
use App\Models\Mercurio36;
use App\Services\CajaServices\UsuarioServices;
use App\Services\Utils\Generales;
use App\Services\Utils\Pagination;
use App\Services\Utils\SenderEmail;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UsuarioController extends ApplicationController
{

    protected $db;
    protected $user;
    protected $tipo;

    /**
     * services variable
     * @var Services
     */
    protected $services;

    /**
     * pagination variable
     * @var Pagination
     */
    protected $pagination;

    public function __construct()
    {
        $this->pagination = new Pagination();
        $this->db = DbBase::rawConnect();
        $this->user = session()->has('user') ? session('user') : null;
        $this->tipo = session()->has('tipo') ? session('tipo') : null;
    }


    public function indexAction()
    {
        $campo_field = array(
            "documento" => "Identificación",
            "nombre" => "Nombre usuario",
            "tipo" => "Tipo usuario",
            "email" => "Email",
        );
        $this->setParamToView("campo_filtro", $campo_field);
        $this->setParamToView("filters", get_flashdata_item("filter_params"));
        $this->setParamToView("title", "Perfil usuario");
    }

    public function paramsAction()
    {
        $this->setResponse("ajax");
        try {
            $coddoc = array();
            foreach ((new Gener18())->find() as $entity) {
                $coddoc["{$entity->getCoddoc()}"] = $entity->getDetdoc();
            }
            $tipo = (new Mercurio07())->getArrayTipos();

            $codciu = array();
            foreach ((new Gener09())->find("*", "conditions: codzon >='18000' and codzon <= '19000'") as $entity) {
                $codciu["{$entity->getCodzon()}"] = $entity->getDetzon();
            }

            $salida = array(
                "success" => true,
                "data" => array(
                    'coddoc' => $coddoc,
                    'tipo' => $tipo,
                    'codciu' => $codciu,
                    'estado' => (new Mercurio07)->getArrayEstados()
                ),
                "msj" => 'OK'
            );
        } catch (DebugException $err) {
            $salida = array(
                "success" => false,
                "msj" => $err->getMessage()
            );
        }
        return $this->renderObject($salida, false);
    }

    public function guardarAction(Request $request)
    {
        $this->setResponse("ajax");
        try {
            $tipo = $request->input('tipo');
            $coddoc = $request->input('coddoc');
            $nombre = $request->input('nombre');
            $codciu = $request->input('codciu');
            $newclave = $request->input('newclave');
            $email = $request->input('email');
            $documento = $request->input('documento');
            $old_coddoc = $request->input('old_coddoc');
            $estado = $request->input('estado');

            $hasUsuario = (new Mercurio07)->count(
                "*",
                "conditions: documento='{$documento}' AND tipo='{$tipo}' AND coddoc='{$old_coddoc}'"
            );

            if ($hasUsuario == 0) {
                throw new DebugException("Error el registro de usuario no existe registrado", 501);
            }

            $mercurio07 = (new Mercurio07)->findFirst("documento='{$documento}' AND tipo='{$tipo}' AND coddoc='{$old_coddoc}'");
            $clave = $mercurio07->getClave();

            $setclave = "";
            if (strlen($newclave) > 5 && strlen($newclave) < 80) {
                $hash = Generales::GeneraHashByClave($newclave);
                $clave = $hash;
                $setclave = ", clave='{$hash}'";
            }

            if ($old_coddoc != $coddoc) {
                $hasUsuario = (new Mercurio07)->count(
                    "*",
                    "conditions: documento='{$documento}' AND tipo='{$tipo}' AND coddoc='{$coddoc}'"
                );
                if ($hasUsuario == 0) {
                    $mercurio07 = new Mercurio07;
                    $mercurio07->setDocumento($documento);
                    $mercurio07->setTipo($tipo);
                    $mercurio07->setCoddoc($coddoc);
                    $mercurio07->setNombre($nombre);
                    $mercurio07->setEmail($email);
                    $mercurio07->setCodciu($codciu);
                    $mercurio07->setEstado($estado);
                    $mercurio07->setFecreg(date('Y-m-d'));
                    $mercurio07->setFeccla(date('Y-m-d'));
                    $mercurio07->setAutoriza('S');
                    $mercurio07->setClave($clave);
                    if ($mercurio07->save() == false) {
                        $msj = '';
                        foreach ($mercurio07->getMessages() as $message) $msj .= $message->getMessage() . "\n";
                        throw new DebugException($msj, 501);
                    }
                }
            } else {
                (new Mercurio07)
                    ->updateAll(
                        "tipo='{$tipo}', email='{$email}', codciu='{$codciu}', nombre='{$nombre}', coddoc='{$coddoc}' {$setclave}",
                        "conditions: documento='{$documento}' AND tipo='{$tipo}' AND coddoc='{$coddoc}'"
                    );
                $this->cambiarClave($newclave, $mercurio07);
            }

            $entity = (new Mercurio07)->findFirst("documento='{$documento}' AND tipo='{$tipo}' AND coddoc='{$coddoc}'");
            $response = array(
                "msj" => "Proceso se ha completado con éxito",
                "success" => true,
                "data" => $entity->getArray()
            );
        } catch (DebugException $err) {
            $response = array(
                'success' => false,
                'msj' => $err->getMessage()
            );
        }
        return $this->renderObject($response, false);
    }

    public function cambiarClave($clave, $usuario_externo)
    {
        $nombre = capitalize($usuario_externo->getNombre());
        $asunto = "Cambio de clave - Comfaca En Linea";
        $msj = "En respuesta a la solicitud de recuperación de cuenta, se ha realiza el cambio automatico de la clave para el inicio de sesión. " .
            "A continuación enviamos las credenciales de acceso.<br/><br/>
            Credenciales:<br/>
            <b>USUARIO {$usuario_externo->getDocumento()}</b><br/>
            <b>CLAVE {$clave}</b><br/>";

        $html = view(
            "templates/cambio_clave",
            array(
                "titulo" => "Cordial saludo, señor@ {$nombre}",
                "msj" => $msj,
                "url_activa" => "https://comfacaenlinea.com.co/Mercurio/Mercurio/login",
                "fecha" => date('Y-m-d'),
                "nombre" => $nombre,
                "razon" => $nombre,
                "tipo" => "",
                "asunto" => $asunto
            )
        )->render();

        $emailCaja = (new Mercurio01())->findFirst();
        $senderEmail = new SenderEmail();
        $senderEmail->setters(
            "emisor_email: {$emailCaja->getEmail()}",
            "emisor_clave: {$emailCaja->getClave()}",
            "asunto: {$asunto}"
        );

        $senderEmail->send(
            array(array(
                "email" => $usuario_externo->getEmail(),
                "nombre" => $nombre
            )),
            $html
        );
        return true;
    }

    /**
     * @name function Aplicar Filtro Usuarios
     * @description []
     * ? requeriments:
     * * *
     * @date update 2025/04/02
     * @author edwin <soportesistemas.comfaca@gmail.com>
     * @param string $estado
     * @return void
     */
    public function aplicarFiltroAction(Request $request)
    {
        $this->setResponse("ajax");
        $cantidad_pagina = ($request->input("numero")) ? $request->input("numero") : 10;

        $ftipo = ($request->input("tipo") == '') ? " 1=1 " : " tipo='{$request->input("tipo")}'";
        $festado = ($request->input("estado") == '') ? " 1=1 " : " estado='{$request->input("estado")}'";

        $this->pagination->setters(
            "cantidadPaginas: {$cantidad_pagina}",
            "query: {$ftipo}",
            "estado: {$festado}"
        );

        $query = $this->pagination->filter(
            $request->input('campo'),
            $request->input('condi'),
            $request->input('value')
        );

        set_flashdata("filter_usuarios", $query, true);
        set_flashdata("filter_params", $this->pagination->filters, true);

        $response = $this->pagination->render(new UsuarioServices());
        return $this->renderObject($response, false);
    }

    public function changeCantidadPaginaAction(Request $request)
    {
        $this->buscarAction($request->input("tipo"), $request->input("estado"));
    }

    /**
     * @name function Buscar Lista Usuarios
     * @description []
     * ? requeriments:
     * * *
     * @date update 2025/04/02
     * @author edwin <soportesistemas.comfaca@gmail.com>
     * @param string $estado
     * @return void
     */
    public function buscarAction(Request $request)
    {
        $this->setResponse("ajax");

        $pagina = ($request->input('pagina')) ? $request->input('pagina') : 1;
        $cantidad_pagina = ($request->input("numero")) ? $request->input("numero") : 10;
        $ftipo = ($request->input("tipo") == '') ? " 1=1 " : " tipo='{$request->input("tipo")}'";
        $festado = ($request->input("estado") == '') ? " 1=1 " : " estado='{$request->input("estado")}'";

        $this->pagination->setters(
            "cantidadPaginas: $cantidad_pagina",
            "pagina: {$pagina}",
            "query: {$ftipo}",
            "estado: {$festado}"
        );
        if (
            get_flashdata_item("filter_usuarios") != false
        ) {
            $query = $this->pagination->persistencia(get_flashdata_item("filter_params"));
        }
        set_flashdata("filter_usuarios", $query, true);
        set_flashdata("filter_params", $this->pagination->filters, true);

        $response = $this->pagination->render(new UsuarioServices());
        return $this->renderObject($response, false);
    }

    public function borrarFiltroAction()
    {
        $this->setResponse("ajax");
        set_flashdata("filter_usuarios", false, true);
        set_flashdata("filter_params", false, true);

        return $this->renderObject(array(
            'success' => true,
            'query' => get_flashdata_item("filter_usuarios"),
            'filter' => get_flashdata_item("filter_params"),
        ));
    }

    /**
     * @name function Show User
     * @description []
     * ? requeriments:
     * @date update 2025/04/11
     * @author edwin <soportesistemas.comfaca@gmail.com>
     * @return void
     */
    public function show_userAction(Request $request)
    {
        $this->setResponse("ajax");
        $documento = $request->input('documento');
        $tipo = $request->input('tipo');
        $coddoc = $request->input('coddoc');

        $user = (new Mercurio07)->findFirst(" documento='{$documento}' AND tipo='{$tipo}' AND coddoc='{$coddoc}'");
        $data = $user->getArray();
        $data['estado_detalle'] = $user->getEstadoDetalle();
        $data['coddoc_detalle'] = $user->getCoddocDetalle();
        $data['tipo_detalle'] = $user->getTipoDetalle();

        return $this->renderObject(array(
            "success" => true,
            "data" => $data
        ), false);
    }


    /**
     * @name function Borrar Usuario
     * @description []
     * ? requeriments:
     * @date update 2025/04/11
     * @author edwin <soportesistemas.comfaca@gmail.com>
     * @return void
     */
    public function borrarUsuarioAction(Request $request)
    {
        $this->setResponse("ajax");
        try {
            $documento = $request->input('documento');
            $tipo = $request->input('tipo');
            $coddoc = $request->input('coddoc');

            $hasUser = (new Mercurio07)->count(
                "*",
                "conditions: documento='{$documento}' AND tipo='{$tipo}' AND coddoc='{$coddoc}'"
            );
            if ($hasUser > 0) {
                $hasRequests = (new Mercurio30())->count(
                    "*",
                    "conditions: documento='{$documento}' AND tipo='{$tipo}' AND coddoc='{$coddoc}'"
                );
                if ($hasRequests > 0) {
                    (new Mercurio30)->deleteAll(" documento='{$documento}' AND tipo='{$tipo}' AND coddoc='{$coddoc}'");
                }

                $hasRequests = (new Mercurio31())->count(
                    "*",
                    "conditions: documento='{$documento}' AND tipo='{$tipo}' AND coddoc='{$coddoc}'"
                );
                if ($hasRequests > 0) {
                    (new Mercurio31)->deleteAll(" documento='{$documento}' AND tipo='{$tipo}' AND coddoc='{$coddoc}'");
                }

                $hasRequests = (new Mercurio32())->count(
                    "*",
                    "conditions: documento='{$documento}' AND tipo='{$tipo}' AND coddoc='{$coddoc}'"
                );
                if ($hasRequests > 0) {
                    (new Mercurio32)->deleteAll(" documento='{$documento}' AND tipo='{$tipo}' AND coddoc='{$coddoc}'");
                }

                $hasRequests = (new Mercurio36())->count(
                    "*",
                    "conditions: documento='{$documento}' AND tipo='{$tipo}' AND coddoc='{$coddoc}'"
                );
                if ($hasRequests > 0) {
                    (new Mercurio36)->deleteAll(" documento='{$documento}' AND tipo='{$tipo}' AND coddoc='{$coddoc}'");
                }

                $hasRequests = (new Mercurio19())->count(
                    "*",
                    "conditions: documento='{$documento}' AND tipo='{$tipo}' AND coddoc='{$coddoc}'"
                );
                if ($hasRequests > 0) {
                    (new Mercurio19)->deleteAll(" documento='{$documento}' AND tipo='{$tipo}' AND coddoc='{$coddoc}'");
                }

                $hasRequests = (new Mercurio20())->count(
                    "*",
                    "conditions: documento='{$documento}' AND tipo='{$tipo}' AND coddoc='{$coddoc}'"
                );
                if ($hasRequests > 0) {
                    (new Mercurio20)->deleteAll(" documento='{$documento}' AND tipo='{$tipo}' AND coddoc='{$coddoc}'");
                }

                $hasRequests = (new Mercurio34())->count(
                    "*",
                    "conditions: documento='{$documento}' AND tipo='{$tipo}' AND coddoc='{$coddoc}'"
                );
                if ($hasRequests > 0) {
                    (new Mercurio34)->deleteAll(" documento='{$documento}' AND tipo='{$tipo}' AND coddoc='{$coddoc}'");
                }

                (new Mercurio07)->deleteAll(" documento='{$documento}' AND tipo='{$tipo}' AND coddoc='{$coddoc}'", "limit: 1");
            } else {
                throw new DebugException("El usuario no existe.", 501);
            }

            $response = array(
                "success" => true,
                "msj" => "El usuario se ha eliminado exitosamente."
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
