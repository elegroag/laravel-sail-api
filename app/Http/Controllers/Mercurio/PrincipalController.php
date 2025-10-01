<?php

namespace App\Http\Controllers\Mercurio;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Library\Auth\SessionCookies;
use App\Models\Adapter\DbBase;
use App\Models\Mercurio07;
use App\Models\Mercurio30;
use App\Models\Mercurio31;
use App\Models\Mercurio32;
use App\Models\Mercurio34;
use App\Models\Mercurio36;
use App\Models\Mercurio38;
use App\Models\Mercurio41;
use App\Models\Mercurio45;
use App\Models\Mercurio47;
use App\Services\Utils\Comman;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PrincipalController extends ApplicationController
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
        if ($this->user == null) {
            return redirect()->route('login');
        }
        return view('mercurio/principal/index', [
            'tipo' => $this->tipo,
            'documento' => $this->user['documento'],
            'nombre' => $this->user['nombre']
        ]);
    }

    public function dashboardEmpresaAction()
    {
        return view('principal.dashboard_empresa', [
            'help' => false,
            'title' => "Dashboard Empresas",
            'hide_header' => true,
            'tipo' => $this->tipo,
            'documento' => $this->user['documento'],
            'nombre' => $this->user['nombre'],
        ]);
    }

    public function dashboardTrabajadorAction()
    {
        return view('principal.dashboard_trabajador', [
            'help' => false,
            'title' => "Dashboard Trabajadores",
            'hide_header' => true,
            'tipo' => $this->tipo,
            'documento' => $this->user['documento'],
            'nombre' => $this->user['nombre'],
        ]);
    }

    public function traerAportesEmpresaAction()
    {
        $this->setResponse("ajax");

        $response['labels'] = array(
            "Enero",
            "Febrero",
            "Marzo",
            "Abril",
            "Mayo",
            "Junio",
            "Julio",
            "Agosto",
            "Septiembre",
            "Octubre",
            "Noviembre",
            "Diciembre",
        );
        $data = array();

        $ps = Comman::Api();
        $ps->runCli(
            array(
                "servicio" => "AportesEmpresas",
                "metodo" => "aportes_empresa_mensual",
                "params" => array(
                    "nit" => $this->user['documento'],
                    "vigencia" => date("Y")
                )
            )
        );

        $subsi11 = $ps->toArray();
        foreach ($subsi11['data'] as $msubsi11) {
            $data[] = $msubsi11['valcon'];
        }

        $response['data'] = $data;

        return $this->renderObject($response, false);
    }

    public function traerCategoriasEmpresaAction()
    {
        $this->setResponse("ajax");
        $data = array();
        $labels = array();

        $ps = Comman::Api();
        $ps->runCli(
            array(
                "servicio" => "PoblacionAfiliada",
                "metodo" => "categoria_trabajador_empresa",
                "params" => array(
                    "nit" => $this->user['documento']
                )
            )
        );
        $subsi11 = $ps->toArray();
        if (!$subsi11['success']) {
            return $this->renderObject([
                'success' => false,
                'msj' => "No se pudo traer las categorias"
            ]);
        }

        foreach ($subsi11['data'] as $msubsi11) {
            $data[] = $msubsi11['cantidad'];
            $labels[] = $msubsi11['codcat'];
        }

        $response['data'] = $data;
        $response['labels'] = $labels;

        return $this->renderObject($response, false);
    }

    public function traerGiroEmpresaAction()
    {
        $this->setResponse("ajax");
        $data = array();
        $response['labels'] = array(
            "Enero",
            "Febrero",
            "Marzo",
            "Abril",
            "Mayo",
            "Junio",
            "Julio",
            "Agosto",
            "Septiembre",
            "Octubre",
            "Noviembre",
            "Diciembre",
        );

        $ps = Comman::Api();
        $ps->runCli(
            array(
                "servicio" => "CuotaMonetaria",
                "metodo" => "giro_trabajador_empresa",
                "params" => array(
                    "nit" => $this->user['documento']
                )
            )
        );
        $subsi09 = $ps->toArray();
        if (!$subsi09['success']) {
            return $this->renderObject([
                'success' => false,
                'msj' => "No se pudo traer el giro"
            ]);
        }

        foreach ($subsi09['data'] as $msubsi09) {
            $data[] = $msubsi09['valor'];
        }
        $response['data'] = $data;

        return $this->renderObject($response, false);
    }

    public function fileExisteGlobalAction(Request $request, Response $response, string $filepath)
    {
        $archivo = base64_decode($filepath);
        if (preg_match('/(storage)(\/)(temp)/i', $archivo) == false) {
            $fichero = storage_path('temp/' . $archivo);
        } else {
            $fichero = storage_path($archivo);
        }
        if (file_exists($fichero)) {
            return $this->renderObject(array("success" => true));
        } else {
            return $this->renderObject(array("success" => false));
        }
    }

    public function actualizaEstadoSolicitudesAction()
    {
        try {
            $this->setResponse("ajax");

            if (get_flashdata_item("Syncron") == true) {
                return $this->renderObject(array(
                    "success" => true,
                    "msj" => "Y se realizo la actualización de las solicitudes",
                ), false);
            }
            $tipo = $this->tipo;

            $coddoc = $this->user['coddoc'];
            $documento = $this->user['documento'];

            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
                array(
                    "servicio" => "ComfacaEmpresas",
                    "metodo" => "actualiza_empresa_enlinea",
                    "params" => $documento
                )
            );
            $out = $procesadorComando->toArray();
            $salida_empresas = $out;


            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
                array(
                    "servicio" => "ComfacaEmpresas",
                    "metodo" => "actualiza_trabajador_enlinea",
                    "params" => $documento
                )
            );
            $out = $procesadorComando->toArray();
            $salida_trabajadores = $out;

            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
                array(
                    "servicio" => "ComfacaEmpresas",
                    "metodo" => "actualiza_conyuge_enlinea",
                    "params" => $documento
                )
            );
            $out = $procesadorComando->toArray();
            $salida_conyuges = $out;

            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
                array(
                    "servicio" => "ComfacaEmpresas",
                    "metodo" => "actualiza_beneficiario_enlinea",
                    "params" => $documento
                )
            );
            $out = $procesadorComando->toArray();
            $salida_beneficiarios = $out;

            $hoy = Carbon::now()->format('Y-m-d');
            Mercurio07::where('documento', $documento)
                ->where('coddoc', $coddoc)
                ->where('tipo', $tipo)
                ->update(['fecha_syncron' => $hoy]);

            $salida = array(
                "success" => true,
                "msj" => "El proceso de actualización se ha completado con éxito",
                "empresas" => $salida_empresas,
                "trabajadores" => $salida_trabajadores,
                "conyuges" => $salida_conyuges,
                "beneficiarios" => $salida_beneficiarios
            );

            set_flashdata("Syncron", true, true);
        } catch (DebugException $tf) {
            $salida = array(
                "success" => false,
                "msj" => $tf->getMessage()
            );
        }
        return $this->renderObject($salida);
    }

    public function upAction()
    {
        $this->setResponse("view");
        get_flashdata_item("Syncron", true);
    }

    public function listaAdressAction()
    {
        try {
            $this->setResponse("ajax");
            $adress =  $this->db->inQueryAssoc("SELECT * FROM mercurio15 WHERE 1=1");
            $salida = array(
                "success" => true,
                "data" => $adress,
                "msj" => "El proceso de consulta completo con éxito"
            );
        } catch (DebugException $tf) {
            $salida = array(
                "success" => false,
                "msj" => $tf->getMessage()
            );
        }
        return $this->renderObject($salida);
    }


    public function serviciosAction()
    {
        $this->setResponse("ajax");
        try {
            session()->put('estado_afiliado', NULL);

            $documento = $this->user['documento'];
            $coddoc = $this->user['coddoc'];
            $tipo = session('tipo');
            $servicios = array();
            switch ($tipo) {
                case 'E':
                    $servicios = [
                        'afiliacion' => [
                            [
                                'name' => 'Solicitudes Trabajadores',
                                'cantidad' => array(
                                    'pendientes' => Mercurio31::where(["estado" => 'P', "coddoc" => $coddoc, "tipo" => $tipo, "documento" => $documento])->count(),
                                    'aprobados' => Mercurio31::where(["estado" => 'A', "coddoc" => $coddoc, "tipo" => $tipo, "documento" => $documento])->count(),
                                    'rechazados' => Mercurio31::where(["estado" => 'R', "coddoc" => $coddoc, "tipo" => $tipo, "documento" => $documento])->count(),
                                    'devueltos' => Mercurio31::where(["estado" => 'D', "coddoc" => $coddoc, "tipo" => $tipo, "documento" => $documento])->count(),
                                    'temporales' => Mercurio31::where(["estado" => 'T', "coddoc" => $coddoc, "tipo" => $tipo, "documento" => $documento])->count()
                                ),
                                'icon' => 'T',
                                'url' => 'trabajador/index',
                                'imagen' => 'trabajadores.jpg',
                            ],
                            [
                                'name' => 'Solicitudes Cónyuges',
                                'cantidad' => array(
                                    'pendientes' => Mercurio32::where(["estado" => 'P', "coddoc" => $coddoc, "tipo" => $tipo, "documento" => $documento])->count(),
                                    'aprobados' => Mercurio32::where(["estado" => 'A', "coddoc" => $coddoc, "tipo" => $tipo, "documento" => $documento])->count(),
                                    'rechazados' => Mercurio32::where(["estado" => 'R', "coddoc" => $coddoc, "tipo" => $tipo, "documento" => $documento])->count(),
                                    'devueltos' => Mercurio32::where(["estado" => 'D', "coddoc" => $coddoc, "tipo" => $tipo, "documento" => $documento])->count(),
                                    'temporales' => Mercurio32::where(["estado" => 'T', "coddoc" => $coddoc, "tipo" => $tipo, "documento" => $documento])->count()
                                ),
                                'icon' => 'C',
                                'url' => 'conyuge/index',
                                'imagen' => 'conyuges.jpg',
                            ],
                            [
                                'name' => 'Solicitudes Beneficiarios',
                                'cantidad' => array(
                                    'pendientes' => Mercurio34::where(["estado" => 'P', "coddoc" => $coddoc, "tipo" => $tipo, "documento" => $documento])->count(),
                                    'aprobados' => Mercurio34::where(["estado" => 'A', "coddoc" => $coddoc, "tipo" => $tipo, "documento" => $documento])->count(),
                                    'rechazados' => Mercurio34::where(["estado" => 'R', "coddoc" => $coddoc, "tipo" => $tipo, "documento" => $documento])->count(),
                                    'devueltos' => Mercurio34::where(["estado" => 'D', "coddoc" => $coddoc, "tipo" => $tipo, "documento" => $documento])->count(),
                                    'temporales' => Mercurio34::where(["estado" => 'T', "coddoc" => $coddoc, "tipo" => $tipo, "documento" => $documento])->count()
                                ),
                                'icon' => 'B',
                                'url' => 'beneficiario/index',
                                'imagen' => 'beneficiarios.jpg',
                            ],
                            [
                                'name' => 'Solicitud Actualiza Datos',
                                'cantidad' => array(
                                    'pendientes' => Mercurio47::where(["estado" => 'P', "coddoc" => $coddoc, "tipo" => $tipo, "documento" => $documento])->count(),
                                    'aprobados' => Mercurio47::where(["estado" => 'A', "coddoc" => $coddoc, "tipo" => $tipo, "documento" => $documento])->count(),
                                    'rechazados' => Mercurio47::where(["estado" => 'R', "coddoc" => $coddoc, "tipo" => $tipo, "documento" => $documento])->count(),
                                    'devueltos' => Mercurio47::where(["estado" => 'D', "coddoc" => $coddoc, "tipo" => $tipo, "documento" => $documento])->count(),
                                    'temporales' => Mercurio47::where(["estado" => 'T', "coddoc" => $coddoc, "tipo" => $tipo, "documento" => $documento])->count()
                                ),
                                'icon' => 'B',
                                'url' => 'actualizadatos/index',
                                'imagen' => 'datos_basicos.jpg',
                            ]
                        ],
                        'productos' => false,
                        'consultas' => [
                            [
                                'name' => 'Consulta Trabajadores',
                                'url' => 'subsidioemp/consulta_trabajadores_view',
                                'imagen' => 'consulta_trabajadores.jpg',
                            ],
                            [
                                'name' => 'Consulta de gíro',
                                'url' => 'subsidioemp/consulta_giro_view',
                                'imagen' => 'consulta_giro.jpg',
                            ],
                            [
                                'name' => 'Consulta de aportes',
                                'url' => 'subsidioemp/consulta_aportes_view',
                                'imagen' => 'consulta_aportes.jpg',
                            ],
                            [
                                'name' => 'Consulta de nominas',
                                'url' => 'subsidioemp/consulta_nomina_view',
                                'imagen' => 'consulta_aportes.jpg',
                            ]
                        ]
                    ];
                    break;
                case 'P':
                    $servicios = [
                        'afiliacion' => [
                            [
                                'name' => 'Solicitudes Empresas',
                                'cantidad' => [
                                    'pendientes' => Mercurio30::where(["estado" => 'P', "coddoc" => $coddoc, "tipo" => $tipo, "documento" => $documento])->count(),
                                    'aprobados' => Mercurio30::where(["estado" => 'A', "coddoc" => $coddoc, "tipo" => $tipo, "documento" => $documento])->count(),
                                    'rechazados' => Mercurio30::where(["estado" => 'R', "coddoc" => $coddoc, "tipo" => $tipo, "documento" => $documento])->count(),
                                    'devueltos' => Mercurio30::where(["estado" => 'D', "coddoc" => $coddoc, "tipo" => $tipo, "documento" => $documento])->count(),
                                    'temporales' => Mercurio30::where(["estado" => 'T', "coddoc" => $coddoc, "tipo" => $tipo, "documento" => $documento])->count()
                                ],
                                'icon' => 'E',
                                'url' => 'empresa/index',
                                'imagen' => 'empresas.jpg',
                            ],
                            [
                                'name' => 'Solicitud Trabajador independiente',
                                'cantidad' => [
                                    'pendientes' => Mercurio41::where(["estado" => 'P', "coddoc" => $coddoc, "tipo" => $tipo, "documento" => $documento])->count(),
                                    'aprobados' => Mercurio41::where(["estado" => 'A', "coddoc" => $coddoc, "tipo" => $tipo, "documento" => $documento])->count(),
                                    'rechazados' => Mercurio41::where(["estado" => 'R', "coddoc" => $coddoc, "tipo" => $tipo, "documento" => $documento])->count(),
                                    'devueltos' => Mercurio41::where(["estado" => 'D', "coddoc" => $coddoc, "tipo" => $tipo, "documento" => $documento])->count(),
                                    'temporales' => Mercurio41::where(["estado" => 'T', "coddoc" => $coddoc, "tipo" => $tipo, "documento" => $documento])->count()
                                ],
                                'icon' => 'I',
                                'url' => 'independiente/index',
                                'imagen' => 'independiente.jpg',
                            ],
                            [
                                'name' => 'Solicitud Pensionado',
                                'cantidad' => [
                                    'pendientes' => Mercurio38::where(["estado" => 'P', "coddoc" => $coddoc, "tipo" => $tipo, "documento" => $documento])->count(),
                                    'aprobados' => Mercurio38::where(["estado" => 'A', "coddoc" => $coddoc, "tipo" => $tipo, "documento" => $documento])->count(),
                                    'rechazados' => Mercurio38::where(["estado" => 'R', "coddoc" => $coddoc, "tipo" => $tipo, "documento" => $documento])->count(),
                                    'devueltos' => Mercurio38::where(["estado" => 'D', "coddoc" => $coddoc, "tipo" => $tipo, "documento" => $documento])->count(),
                                    'temporales' => Mercurio38::where(["estado" => 'T', "coddoc" => $coddoc, "tipo" => $tipo, "documento" => $documento])->count()
                                ],
                                'icon' => 'P',
                                'url' => 'pensionado/index',
                                'imagen' => 'pensionado.jpg',
                            ],
                            [
                                'name' => 'Solicitud Facultativo',
                                'cantidad' => [
                                    'pendientes' => Mercurio36::where(["estado" => 'P', "coddoc" => $coddoc, "tipo" => $tipo, "documento" => $documento])->count(),
                                    'aprobados' => Mercurio36::where(["estado" => 'A', "coddoc" => $coddoc, "tipo" => $tipo, "documento" => $documento])->count(),
                                    'rechazados' => Mercurio36::where(["estado" => 'R', "coddoc" => $coddoc, "tipo" => $tipo, "documento" => $documento])->count(),
                                    'devueltos' => Mercurio36::where(["estado" => 'D', "coddoc" => $coddoc, "tipo" => $tipo, "documento" => $documento])->count(),
                                    'temporales' => Mercurio36::where(["estado" => 'T', "coddoc" => $coddoc, "tipo" => $tipo, "documento" => $documento])->count()
                                ],
                                'icon' => 'F',
                                'url' => 'facultativo/index',
                                'imagen' => 'facultativo.jpg',
                            ],
                        ],
                        'productos' => [
                            [
                                'name' => 'P. Complemento_nutricional',
                                'url' => 'productos/complemento_nutricional',
                                'imagen' => 'complemento.jpg',
                            ]
                        ],
                        'consultas' => false,
                    ];
                    break;
                case 'I':
                case 'F':
                case 'O':
                    $servicios = [
                        'afiliacion' => [
                            [
                                'name' => 'Solicitudes Cónyuges',
                                'cantidad' => [
                                    'pendientes' => Mercurio32::where(["estado" => 'P', "coddoc" => $coddoc, "tipo" => $tipo, "documento" => $documento])->count(),
                                    'aprobados' => Mercurio32::where(["estado" => 'A', "coddoc" => $coddoc, "tipo" => $tipo, "documento" => $documento])->count(),
                                    'rechazados' => Mercurio32::where(["estado" => 'R', "coddoc" => $coddoc, "tipo" => $tipo, "documento" => $documento])->count(),
                                    'devueltos' => Mercurio32::where(["estado" => 'D', "coddoc" => $coddoc, "tipo" => $tipo, "documento" => $documento])->count(),
                                    'temporales' => Mercurio32::where(["estado" => 'T', "coddoc" => $coddoc, "tipo" => $tipo, "documento" => $documento])->count()
                                ],
                                'icon' => 'C',
                                'url' => 'conyuge/index',
                                'imagen' => 'conyuges.jpg',
                            ],
                            [
                                'name' => 'Solicitudes Beneficiarios',
                                'cantidad' => [
                                    'pendientes' => Mercurio34::where(["estado" => 'P', "coddoc" => $coddoc, "tipo" => $tipo, "documento" => $documento])->count(),
                                    'aprobados' => Mercurio34::where(["estado" => 'A', "coddoc" => $coddoc, "tipo" => $tipo, "documento" => $documento])->count(),
                                    'rechazados' => Mercurio34::where(["estado" => 'R', "coddoc" => $coddoc, "tipo" => $tipo, "documento" => $documento])->count(),
                                    'devueltos' => Mercurio34::where(["estado" => 'D', "coddoc" => $coddoc, "tipo" => $tipo, "documento" => $documento])->count(),
                                    'temporales' => Mercurio34::where(["estado" => 'T', "coddoc" => $coddoc, "tipo" => $tipo, "documento" => $documento])->count()
                                ],
                                'icon' => 'B',
                                'url' => 'beneficiario/index',
                                'imagen' => 'beneficiarios.jpg',
                            ],
                            [
                                'name' => 'Actualización de  datos',
                                'cantidad' => array(
                                    'pendientes' => Mercurio47::where(["estado" => 'P', "coddoc" => $coddoc, "tipo" => $tipo, "documento" => $documento])->count(),
                                    'aprobados' => Mercurio47::where(["estado" => 'A', "coddoc" => $coddoc, "tipo" => $tipo, "documento" => $documento])->count(),
                                    'rechazados' => Mercurio47::where(["estado" => 'R', "coddoc" => $coddoc, "tipo" => $tipo, "documento" => $documento])->count(),
                                    'devueltos' => Mercurio47::where(["estado" => 'D', "coddoc" => $coddoc, "tipo" => $tipo, "documento" => $documento])->count(),
                                    'temporales' => Mercurio47::where(["estado" => 'T', "coddoc" => $coddoc, "tipo" => $tipo, "documento" => $documento])->count()
                                ),
                                'icon' => 'B',
                                'url' => 'actualizadatos/index',
                                'imagen' => 'datos_basicos.jpg',
                            ]
                        ],
                        'productos' => [
                            [
                                'name' => 'P. Complemento_nutricional',
                                'url' => 'productos/complemento_nutricional',
                                'imagen' => 'complemento.jpg',
                            ]
                        ],
                        'consultas' => [
                            [
                                'name' => 'Consulta Trabajadores',
                                'url' => 'subsidio/consulta_trabajadores_view',
                                'imagen' => 'consulta_trabajadores.jpg',
                            ],
                            [
                                'name' => 'Consulta de gíro',
                                'url' => 'subsidio/consulta_giro_view',
                                'imagen' => 'consulta_giro.jpg',
                            ],
                            [
                                'name' => 'Consulta de aportes',
                                'url' => 'subsidio/consulta_aportes_view',
                                'imagen' => 'consulta_aportes.jpg',
                            ]
                        ]
                    ];
                    break;
                case 'T':
                    $servicios = [
                        'afiliacion' => [
                            [
                                'name' => 'Solicitudes Cónyuges',
                                'cantidad' => [
                                    'pendientes' => Mercurio32::where(["estado" => 'P', "coddoc" => $coddoc, "tipo" => $tipo, "documento" => $documento])->count(),
                                    'aprobados' => Mercurio32::where(["estado" => 'A', "coddoc" => $coddoc, "tipo" => $tipo, "documento" => $documento])->count(),
                                    'rechazados' => Mercurio32::where(["estado" => 'R', "coddoc" => $coddoc, "tipo" => $tipo, "documento" => $documento])->count(),
                                    'devueltos' => Mercurio32::where(["estado" => 'D', "coddoc" => $coddoc, "tipo" => $tipo, "documento" => $documento])->count(),
                                    'temporales' => Mercurio32::where(["estado" => 'T', "coddoc" => $coddoc, "tipo" => $tipo, "documento" => $documento])->count()
                                ],
                                'icon' => 'C',
                                'url' => 'conyuge/index',
                                'imagen' => 'conyuges.jpg',
                            ],
                            [
                                'name' => 'Solicitudes Beneficiarios',
                                'cantidad' => [
                                    'pendientes' => Mercurio34::where(["estado" => 'P', "coddoc" => $coddoc, "tipo" => $tipo, "documento" => $documento])->count(),
                                    'aprobados' => Mercurio34::where(["estado" => 'A', "coddoc" => $coddoc, "tipo" => $tipo, "documento" => $documento])->count(),
                                    'rechazados' => Mercurio34::where(["estado" => 'R', "coddoc" => $coddoc, "tipo" => $tipo, "documento" => $documento])->count(),
                                    'devueltos' => Mercurio34::where(["estado" => 'D', "coddoc" => $coddoc, "tipo" => $tipo, "documento" => $documento])->count(),
                                    'temporales' => Mercurio34::where(["estado" => 'T', "coddoc" => $coddoc, "tipo" => $tipo, "documento" => $documento])->count()
                                ],
                                'icon' => 'B',
                                'url' => 'beneficiario/index',
                                'imagen' => 'beneficiarios.jpg',
                            ],
                            [
                                'name' => 'Actualización de datos',
                                'cantidad' => [
                                    'pendientes' => Mercurio47::where(["estado" => 'P', "coddoc" => $coddoc, "tipo" => $tipo, "documento" => $documento])->count(),
                                    'aprobados' => Mercurio47::where(["estado" => 'A', "coddoc" => $coddoc, "tipo" => $tipo, "documento" => $documento])->count(),
                                    'rechazados' => Mercurio47::where(["estado" => 'R', "coddoc" => $coddoc, "tipo" => $tipo, "documento" => $documento])->count(),
                                    'devueltos' => Mercurio47::where(["estado" => 'D', "coddoc" => $coddoc, "tipo" => $tipo, "documento" => $documento])->count(),
                                    'temporales' => Mercurio47::where(["estado" => 'T', "coddoc" => $coddoc, "tipo" => $tipo, "documento" => $documento])->count()
                                ],
                                'icon' => 'A',
                                'url' => 'actualizadatostra/index',
                                'imagen' => 'datos_basicos.jpg',
                            ],
                            [
                                'name' => 'Presentar Certificados',
                                'cantidad' => [
                                    'pendientes' => Mercurio45::where(["estado" => 'P', "coddoc" => $coddoc, "tipo" => $tipo, "documento" => $documento])->count(),
                                    'aprobados' => Mercurio45::where(["estado" => 'A', "coddoc" => $coddoc, "tipo" => $tipo, "documento" => $documento])->count(),
                                    'rechazados' => Mercurio45::where(["estado" => 'R', "coddoc" => $coddoc, "tipo" => $tipo, "documento" => $documento])->count(),
                                    'devueltos' => Mercurio45::where(["estado" => 'D', "coddoc" => $coddoc, "tipo" => $tipo, "documento" => $documento])->count(),
                                    'temporales' => Mercurio45::where(["estado" => 'T', "coddoc" => $coddoc, "tipo" => $tipo, "documento" => $documento])->count()
                                ],
                                'icon' => 'D',
                                'url' => 'certificados/index',
                                'imagen' => 'presentar_certificado.jpg',
                            ]
                        ],
                        'productos' => [
                            [
                                'name' => 'P. Complemento_nutricional',
                                'url' => 'productos/complemento_nutricional',
                                'imagen' => 'complemento.jpg',
                            ]
                        ],
                        'consultas' => [
                            [
                                'name' => 'Consulta de gíro',
                                'url' => 'subsidio/consulta_giro_view',
                                'imagen' => 'consulta_giro.jpg',
                            ],
                            [
                                'name' => 'Consulta nucleo familiar',
                                'url' => 'subsidio/consulta_nucleo',
                                'imagen' => 'conyuges.jpg',
                            ],
                            [
                                'name' => 'Consulta planilla',
                                'url' => 'subsidio/consulta_planilla_trabajador_view',
                                'imagen' => 'consulta_trabajadores.jpg',
                            ]
                        ]
                    ];
                    break;
                default:
                    break;
            }

            $salida = [
                'success' => true,
                'msj' => 'Proceso completado con éxito',
                'data' => $servicios
            ];
        } catch (DebugException $e) {
            $salida = [
                'success' => false,
                'msj' => $e->getMessage()
            ];
        }
        return $this->renderObject($salida, false);
    }

    public function validaSyncroAction()
    {
        $this->setResponse("ajax");

        try {
            $documento = $this->user['documento'];
            $coddoc = $this->user['coddoc'];
            $tipo = $this->tipo;

            $hoy = date('Y-m-d');
            $solicitante =  (new Mercurio07)->findFirst(" documento='{$documento}' and coddoc='{$coddoc}' and tipo='{$tipo}'");
            if ($solicitante->getFechaSyncron() == '' || is_null($solicitante->getFechaSyncron())) {
                $solicitante->setFechaSyncron($hoy);
                $solicitante->save();
            }

            $hoy = Carbon::now();
            $dif = $hoy->diff(Carbon::parse($solicitante->getFechaSyncron()));
            $interval = $dif->days;
            $salida = array(
                'success' => true,
                'msj' => 'Consulta realizada con éxito',
                'data' => array(
                    'ultimo_syncron' => Carbon::parse($solicitante->getFechaSyncron())->format('d - M - Y'),
                    'syncron' => ($interval >= 10) ? true : false
                )
            );
        } catch (DebugException $e) {
            $salida = array(
                'success' => false,
                'msj' => $e->getMessage()
            );
        }
        return $this->renderObject($salida, false);
    }

    /**
     * ingresoDirigidoAction function
     * aplica para los particulares que hacen su primer registro al sistema
     * @param string $id
     * @param string $documento
     * @param string $coddoc
     * @param string $calemp
     * @return void
     */
    public function ingresoDirigidoAction(Request $request)
    {
        $this->setResponse("view");
        try {
            $dataVerify = $request->input('dataVerify');
            $tk = explode('|', base64_decode($dataVerify));

            if (count($tk) !== 2) {
                throw new DebugException("El identificador de la empresa no es correcto", 404);
            }

            $data = Kdecrypt($tk[0], $tk[1]);
            if ($data == false) {
                throw new DebugException("El identificador de la empresa no es correcto", 404);
            }

            $token = json_decode($data);
            if ($token == false || is_null($token) || is_object($token) == false) {
                throw new DebugException("El identificador de la empresa no es correcto", 404);
            }

            if (
                isset($token->documento) == false ||
                isset($token->tipo) == false ||
                isset($token->coddoc) == false ||
                isset($token->tipafi) == false
            ) {
                throw new DebugException("El identificador de la empresa no es correcto", 404);
            }

            $solicitud = false;
            switch ($token->tipafi) {
                case 'E':
                    if ($token->documento == '' || $token->tipo == '' || $token->coddoc == '') {
                        throw new DebugException("El identificador de la empresa no es correcto", 404);
                    }
                    if ($token->id == '' || is_null($token->id)) {
                        $solicitud = (new Mercurio07)->findFirst(" documento='{$token->documento}' and coddoc='{$token->coddoc}' and tipo='{$token->tipo}'");
                        $url = "mercurio/empresa/index";
                    } else {
                        $solicitud = (new Mercurio30)->findFirst(" id='{$token->id}' and documento='{$token->documento}' and coddoc='{$token->coddoc}'");
                        $url = "mercurio/empresa/index#proceso/{$token->id}";
                    }
                    break;
                case 'I':
                    if ($token->id == '' || $token->documento == '' || $token->tipo == '' || $token->coddoc == '') {
                        throw new DebugException("El identificador de la empresa no es correcto", 404);
                    }
                    $solicitud = (new Mercurio41)->findFirst(" id='{$token->id}' and documento='{$token->documento}' and coddoc='{$token->coddoc}'");
                    $url = "mercurio/independiente/index#proceso/{$token->id}";
                    break;
                case 'O':
                    if ($token->id == '' || $token->documento == '' || $token->tipo == '' || $token->coddoc == '') {
                        throw new DebugException("El identificador de la empresa no es correcto", 404);
                    }
                    $solicitud = (new  Mercurio38)->findFirst(" id='{$token->id}' and documento='{$token->documento}' and coddoc='{$token->coddoc}'");
                    $url = "mercurio/pensionado/index#proceso/{$token->id}";
                    break;
                case 'F':
                    if ($token->id == '' || $token->documento == '' || $token->tipo == '' || $token->coddoc == '') {
                        throw new DebugException("El identificador de la empresa no es correcto", 404);
                    }
                    $solicitud = (new  Mercurio36)->findFirst(" id='{$token->id}' and documento='{$token->documento}' and coddoc='{$token->coddoc}'");
                    $url = "mercurio/facultativo/index#proceso/{$token->id}";
                    break;
                default:
                    // Ingreso usuario particular
                    $solicitud = (new Mercurio07)->findFirst(" documento='{$token->documento}' and coddoc='{$token->coddoc}' and tipo='{$token->tipo}'");
                    $url = "mercurio/principal/index";
                    break;
            }

            if ($solicitud == false) {
                throw new DebugException("La identificación de la solicitud no es correcto", 404);
            }

            $auth = new SessionCookies(
                "model: mercurio07",
                "tipo: {$token->tipo}",
                "coddoc: {$token->coddoc}",
                "documento: {$token->documento}",
                "estado: A"
            );

            if (!$auth->authenticate()) {
                throw new DebugException("Error en la autenticación del usuario", 501);
            }

            set_flashdata(
                "success",
                array(
                    "type" => "html",
                    "msj" => "<p style='font-size:1rem' class='text-left'>El usuario ha realizado el pre-registro de forma correcta</p>" .
                        "<p style='font-size:1rem' class='text-left'>El registro realizado es de tipo \"Particular\", ahora puedes realizar las afiliaciones de modo seguro.<br/>" .
                        "Las credenciales de acceso le seran enviadas a la respectiva dirección de correo registrado.<br/></p>"
                )
            );

            return redirect()->to($url);
        } catch (DebugException $e) {
            set_flashdata("error", array("msj" => $e->getMessage()));
            return redirect()->to("mercurio/login");
        }
    }

    public function estado_actualAction()
    {
        $this->setResponse("ajax");

        try {
            $tipo = $this->user['tipo'];
            $documento = $this->user['documento'];

            switch ($tipo) {
                case 'T':
                    $procesadorComando = Comman::Api();
                    $procesadorComando->runCli(
                        array(
                            "servicio" => "ComfacaEmpresas",
                            "metodo" => "informacion_trabajador",
                            "params" => array(
                                "cedtra" => $documento
                            )
                        )
                    );
                    $out = $procesadorComando->toArray();
                    break;
                case 'E':
                case 'I':
                case 'F':
                case 'O':
                    $procesadorComando = Comman::Api();
                    $procesadorComando->runCli(
                        array(
                            "servicio" => "ComfacaEmpresas",
                            "metodo" => "informacion_empresa",
                            "params" => array(
                                "nit" => $documento
                            )
                        )
                    );
                    $out = $procesadorComando->toArray();
                    break;
                default:
                    $out = false;
                    break;
            }

            $salida = array(
                'success' => true,
                'msj' => 'Proceso completado con éxito',
                'data' => $out
            );
        } catch (DebugException $e) {
            $salida = array(
                "success" => false,
                "msj" => $e->getMessage()
            );
        }
        return $this->renderObject($salida);
    }
}
