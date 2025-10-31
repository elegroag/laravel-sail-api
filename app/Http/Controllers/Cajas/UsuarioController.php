<?php

namespace App\Http\Controllers\Cajas;

use App\Exceptions\DebugException;
use App\Http\Controllers\Controller;
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

class UsuarioController extends Controller
{
    protected $user;

    protected $tipfun;

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
        $this->user = session('user');
        $this->tipfun = session('tipfun');
    }

    public function index()
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
            'mercurio11' => [],
        ]);
    }

    /**
     * Obtiene los parámetros necesarios para el formulario de usuarios
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function params()
    {
        try {
            // Obtener tipos de documento usando Eloquent
            $coddoc = Gener18::pluck('detdoc', 'coddoc')->toArray();

            // Obtener tipos de usuario
            $tipo = get_array_tipos();

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
                    'estado' => get_user_estados(),
                ],
                'msj' => 'Parámetros obtenidos correctamente',
            ];
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'msj' => 'Error al obtener los parámetros: ' . $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null,
            ];
        }

        return response()->json($response);
    }

    public function guardar(Request $request)
    {
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

            // Verificar si existe el usuario con las condiciones anteriores
            $hasUsuario = Mercurio07::where('documento', $documento)
                ->where('tipo', $tipo)
                ->where('coddoc', $old_coddoc)
                ->exists();

            if (!$hasUsuario) {
                throw new DebugException('Error el registro de usuario no existe registrado', 501);
            }

            $mercurio07 = Mercurio07::where("documento", $documento)
                ->where("tipo", $tipo)
                ->where("coddoc", $old_coddoc)
                ->first();


            $hash = clave_hash($newclave);

            if ($old_coddoc != $coddoc) {
                $hasUsuario = Mercurio07::where('documento', $documento)
                    ->where('tipo', $tipo)
                    ->where('coddoc', $coddoc)
                    ->exists();

                if (!$hasUsuario) {
                    Mercurio07::create(
                        [
                            'documento' => $documento,
                            'tipo' => $tipo,
                            'coddoc' => $coddoc,
                            'nombre' => $nombre,
                            'email' => $email,
                            'codciu' => $codciu,
                            'estado' => $estado,
                            'fecreg' => date('Y-m-d'),
                            'feccla' => date('Y-m-d'),
                            'autoriza' => 'S',
                            'clave' => $hash,
                        ]
                    );
                }
            } else {
                // Actualizar usuario existente con las nuevas condiciones
                Mercurio07::where('documento', $documento)
                    ->where('tipo', $tipo)
                    ->where('coddoc', $coddoc)
                    ->update([
                        'tipo' => $tipo,
                        'email' => $email,
                        'codciu' => $codciu,
                        'nombre' => $nombre,
                        'coddoc' => $coddoc,
                        'clave' => $hash,
                    ]);
                $this->cambiarClave($newclave, $mercurio07);
            }

            $entity = Mercurio07::where("documento", $documento)
                ->where("tipo", $tipo)
                ->where("coddoc", $coddoc)
                ->first();

            $response = [
                'msj' => 'Proceso se ha completado con éxito',
                'success' => true,
                'data' => $entity->toArray(),
            ];
        } catch (DebugException $err) {
            $response = [
                'success' => false,
                'msj' => $err->getMessage(),
            ];
        }

        return response()->json($response);
    }

    public function cambiarClave(string $clave = '', Mercurio07 $usuario_externo)
    {
        $nombre = capitalize($usuario_externo->nombre);
        $asunto = 'Cambio de clave - Comfaca En Linea';
        $msj = 'En respuesta a la solicitud de recuperación de cuenta, se ha realiza el cambio automatico de la clave para el inicio de sesión. ' .
            "A continuación enviamos las credenciales de acceso.<br/><br/>
            Credenciales:<br/>
            <b>USUARIO {$usuario_externo->documento}</b><br/>
            <b>CLAVE {$clave}</b><br/>";

        $html = view(
            'cajas.templates.cambio_clave',
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

        $emailCaja = Mercurio01::first();
        $senderEmail = new SenderEmail;
        $senderEmail->setters(
            "emisor_email: {$emailCaja->email}",
            "emisor_clave: {$emailCaja->clave}",
            "asunto: {$asunto}"
        );

        $senderEmail->send(
            $usuario_externo->email,
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
    public function aplicarFiltro(Request $request, string $tipo = '')
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

        return response()->json($response);
    }

    public function changeCantidadPagina(Request $request)
    {
        $this->buscar($request->input('tipo'), $request->input('estado'));
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
    public function buscar(Request $request, string $estado = '')
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

        return response()->json($response);
    }

    public function borrarFiltro()
    {
        set_flashdata('filter_usuarios', false, true);
        set_flashdata('filter_params', false, true);

        return response()->json([
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
    public function showUser(Request $request)
    {
        $documento = $request->input('documento');
        $tipo = $request->input('tipo');
        $coddoc = $request->input('coddoc');

        $user = Mercurio07::where('documento', $documento)
            ->where('tipo', $tipo)
            ->where('coddoc', $coddoc)
            ->first();

        $data = $user->toArray();
        $data['estado_detalle'] = get_user_estado_detalle($user->estado);
        $data['coddoc_detalle'] = coddoc_repleg_detalle($user->coddoc);
        $data['tipo_detalle'] = get_tipo_detalle($user->tipo);

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
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
    public function borrarUsuario(Request $request)
    {
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

        return response()->json($response);
    }
}
