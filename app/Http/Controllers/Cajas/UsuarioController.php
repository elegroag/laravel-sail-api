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
use App\Services\Srequest;
use App\Services\Utils\Generales;
use App\Services\Utils\Pagination;
use App\Services\Utils\SenderEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UsuarioController extends ApplicationController
{
    protected $db;

    protected $user;

    protected $tipo;

    /**
     * services variable
     *
     * @var Services
     */
    protected $services;

    /**
     * pagination variable
     *
     * @var Pagination
     */
    protected $pagination;

    public function __construct()
    {
        $this->pagination = new Pagination;
        $this->db = DbBase::rawConnect();
        $this->user = session()->has('user') ? session('user') : null;
        $this->tipo = session()->has('tipo') ? session('tipo') : null;
    }

    public function indexAction()
    {
        $campo_field = [
            'documento' => 'Identificación',
            'nombre' => 'Nombre usuario',
            'tipo' => 'Tipo usuario',
            'email' => 'Email',
        ];

        return view('cajas.usuario.index', [
            'campo_filtro' => $campo_field,
            'filters' => get_flashdata_item('filter_params'),
            'title' => 'Perfil usuario',
        ]);
    }

    /**
     * Obtiene los parámetros necesarios para el formulario de usuarios
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function paramsAction()
    {
        $this->setResponse('ajax');

        try {
            // Obtener tipos de documento usando Eloquent
            $coddoc = Gener18::pluck('detdoc', 'coddoc')->toArray();

            // Obtener tipos de usuario
            $tipo = (new Mercurio07)->getArrayTipos();

            // Obtener ciudades usando Eloquent con whereBetween
            $codciu = Gener09::whereBetween('codzon', ['18000', '19000'])
                ->pluck('detzon', 'codzon')
                ->toArray();

            $response = [
                'success' => true,
                'data' => [
                    'coddoc' => $coddoc,
                    'tipo' => $tipo,
                    'codciu' => $codciu,
                    'estado' => (new Mercurio07)->getArrayEstados(),
                ],
                'msj' => 'Parámetros obtenidos correctamente',
            ];
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'msj' => 'Error al obtener los parámetros: '.$e->getMessage(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null,
            ];
        }

        return $this->renderObject($response, false);
    }

    public function guardarAction(Request $request)
    {
        $this->setResponse('ajax');
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
                '*',
                "conditions: documento='{$documento}' AND tipo='{$tipo}' AND coddoc='{$old_coddoc}'"
            );

            if ($hasUsuario == 0) {
                throw new DebugException('Error el registro de usuario no existe registrado', 501);
            }

            $mercurio07 = (new Mercurio07)->findFirst("documento='{$documento}' AND tipo='{$tipo}' AND coddoc='{$old_coddoc}'");
            $clave = $mercurio07->getClave();

            $setclave = '';
            if (strlen($newclave) > 5 && strlen($newclave) < 80) {
                $hash = Generales::GeneraHashByClave($newclave);
                $clave = $hash;
                $setclave = ", clave='{$hash}'";
            }

            if ($old_coddoc != $coddoc) {
                $hasUsuario = (new Mercurio07)->count(
                    '*',
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
                        foreach ($mercurio07->getMessages() as $message) {
                            $msj .= $message->getMessage()."\n";
                        }
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
            $response = [
                'msj' => 'Proceso se ha completado con éxito',
                'success' => true,
                'data' => $entity->getArray(),
            ];
        } catch (DebugException $err) {
            $response = [
                'success' => false,
                'msj' => $err->getMessage(),
            ];
        }

        return $this->renderObject($response, false);
    }

    public function cambiarClave($clave, $usuario_externo)
    {
        $nombre = capitalize($usuario_externo->getNombre());
        $asunto = 'Cambio de clave - Comfaca En Linea';
        $msj = 'En respuesta a la solicitud de recuperación de cuenta, se ha realiza el cambio automatico de la clave para el inicio de sesión. '.
            "A continuación enviamos las credenciales de acceso.<br/><br/>
            Credenciales:<br/>
            <b>USUARIO {$usuario_externo->getDocumento()}</b><br/>
            <b>CLAVE {$clave}</b><br/>";

        $html = view(
            'templates/cambio_clave',
            [
                'titulo' => "Cordial saludo, señor@ {$nombre}",
                'msj' => $msj,
                'url_activa' => 'https://comfacaenlinea.com.co/Mercurio/Mercurio/login',
                'fecha' => date('Y-m-d'),
                'nombre' => $nombre,
                'razon' => $nombre,
                'tipo' => '',
                'asunto' => $asunto,
            ]
        )->render();

        $emailCaja = (new Mercurio01)->findFirst();
        $senderEmail = new SenderEmail;
        $senderEmail->setters(
            "emisor_email: {$emailCaja->getEmail()}",
            "emisor_clave: {$emailCaja->getClave()}",
            "asunto: {$asunto}"
        );

        $senderEmail->send(
            [[
                'email' => $usuario_externo->getEmail(),
                'nombre' => $nombre,
            ]],
            $html
        );

        return true;
    }

    /**
     * @name function Aplicar Filtro Usuarios
     *
     * @description []
     * ? requeriments:
     * * *
     * @date update 2025/04/02
     *
     * @author edwin <soportesistemas.comfaca@gmail.com>
     *
     * @param  string  $estado
     * @return void
     */
    public function aplicarFiltroAction(Request $request, string $tipo = '')
    {
        $cantidad_pagina = ($request->input('numero')) ? $request->input('numero') : 10;
        if ($tipo == '') {
            $query_str = ($request->input('tipo') == '') ? '1=1' : "tipo='{$request->input('tipo')}'";
        } else {
            $query_str = ($tipo == '') ? '1=1' : "tipo='{$tipo}'";
        }

        $this->pagination->setters(
            new Srequest(
                [
                    'cantidadPaginas' => $cantidad_pagina,
                    'query' => $query_str,
                    'estado' => 'A',
                ]
            )
        );

        $query = $this->pagination->filter(
            $request->input('campo'),
            $request->input('condi'),
            $request->input('value')
        );

        set_flashdata('filter_usuarios', $query, true);
        set_flashdata('filter_params', $this->pagination->filters, true);

        $response = $this->pagination->render(new UsuarioServices);

        return $this->renderObject($response, false);
    }

    public function changeCantidadPaginaAction(Request $request)
    {
        $this->buscarAction($request->input('tipo'), $request->input('estado'));
    }

    /**
     * @name function Buscar Lista Usuarios
     *
     * @description []
     * ? requeriments:
     * * *
     * @date update 2025/04/02
     *
     * @author edwin <soportesistemas.comfaca@gmail.com>
     *
     * @return void
     */
    public function buscarAction(Request $request, string $estado = '')
    {
        $pagina = ($request->input('pagina')) ? $request->input('pagina') : 1;
        $cantidad_pagina = ($request->input('numero')) ? $request->input('numero') : 10;
        $ftipo = ($request->input('tipo') == '') ? ' 1=1 ' : " tipo='{$request->input('tipo')}'";
        $festado = ($request->input('estado') == '') ? ' 1=1 ' : " estado='{$request->input('estado')}'";

        $this->pagination->setters(
            new Srequest(
                [
                    'cantidadPaginas' => $cantidad_pagina,
                    'pagina' => $pagina,
                    'query' => $ftipo,
                    'estado' => $festado,
                ]
            )
        );
        if (
            get_flashdata_item('filter_usuarios') != false
        ) {
            $query = $this->pagination->persistencia(get_flashdata_item('filter_params'));
        }
        set_flashdata('filter_usuarios', $query, true);
        set_flashdata('filter_params', $this->pagination->filters, true);

        $response = $this->pagination->render(new UsuarioServices);

        return $this->renderObject($response, false);
    }

    public function borrarFiltroAction()
    {
        $this->setResponse('ajax');
        set_flashdata('filter_usuarios', false, true);
        set_flashdata('filter_params', false, true);

        return $this->renderObject([
            'success' => true,
            'query' => get_flashdata_item('filter_usuarios'),
            'filter' => get_flashdata_item('filter_params'),
        ]);
    }

    /**
     * @name function Show User
     *
     * @description []
     * ? requeriments:
     *
     * @date update 2025/04/11
     *
     * @author edwin <soportesistemas.comfaca@gmail.com>
     *
     * @return void
     */
    public function showUserAction(Request $request)
    {
        $documento = $request->input('documento');
        $tipo = $request->input('tipo');
        $coddoc = $request->input('coddoc');

        $user = Mercurio07::whereRaw("documento='{$documento}' AND tipo='{$tipo}' AND coddoc='{$coddoc}'")->first();
        $data = $user->getArray();
        $coddoc_array = coddoc_repleg_array();
        $data['estado_detalle'] = $user->getEstadoDetalle();
        $data['coddoc_detalle'] = $coddoc_array[$user->getCoddoc()];
        $data['tipo_detalle'] = $user->getTipoDetalle();

        return $this->renderObject([
            'success' => true,
            'data' => $data,
        ], false);
    }

    /**
     * @name function Borrar Usuario
     *
     * @description []
     * ? requeriments:
     *
     * @date update 2025/04/11
     *
     * @author edwin <soportesistemas.comfaca@gmail.com>
     *
     * @return void
     */
    public function borrarUsuarioAction(Request $request)
    {
        $this->setResponse('ajax');
        try {
            $documento = $request->input('documento');
            $tipo = $request->input('tipo');
            $coddoc = $request->input('coddoc');

            $user = Mercurio07::where('documento', $documento)
                ->where('tipo', $tipo)
                ->where('coddoc', $coddoc)
                ->first();

            if (! $user) {
                throw new DebugException('El usuario no existe.', 404);
            }

            DB::transaction(function () use ($documento, $tipo, $coddoc, $user) {
                $whereClause = [
                    'documento' => $documento,
                    'tipo' => $tipo,
                    'coddoc' => $coddoc,
                ];

                Mercurio30::where($whereClause)->delete();
                Mercurio31::where($whereClause)->delete();
                Mercurio32::where($whereClause)->delete();
                Mercurio36::where($whereClause)->delete();
                Mercurio19::where($whereClause)->delete();
                Mercurio20::where($whereClause)->delete();
                Mercurio34::where($whereClause)->delete();

                // Finally delete the user
                $user->delete();
            });

            $response = [
                'success' => true,
                'msj' => 'El usuario se ha eliminado exitosamente.',
            ];
        } catch (DebugException $err) {
            $response = [
                'success' => false,
                'msj' => $err->getMessage(),
            ];
        } catch (\Exception $e) {
            // Catch potential transaction failures
            $response = [
                'success' => false,
                'msj' => 'Ocurrió un error al eliminar el usuario.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ];
        }

        return $this->renderObject($response, false);
    }
}
