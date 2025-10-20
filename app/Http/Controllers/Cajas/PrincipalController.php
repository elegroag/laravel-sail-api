<?php

namespace App\Http\Controllers\Cajas;

use App\Exceptions\DebugException;
use App\Http\Controllers\Controller;
use App\Models\Gener02;
use App\Models\Mercurio06;
use App\Models\Mercurio07;
use App\Models\Mercurio08;
use App\Models\Mercurio09;
use App\Models\Mercurio10;
use App\Models\Mercurio11;
use App\Models\Mercurio20;
use App\Models\Mercurio30;
use App\Models\Mercurio31;
use App\Models\Mercurio32;
use App\Models\Mercurio33;
use App\Models\Mercurio34;
use App\Models\Mercurio38;
use App\Models\Mercurio41;
use App\Models\Mercurio47;
use App\Services\Utils\GeneralService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrincipalController extends Controller
{
    protected $user;

    protected $tipfun;


    public function __construct()
    {
        $this->user = session('user');
        $this->tipfun = session('tipfun');
    }

    public function index()
    {
        $user = $this->user['usuario'];
        $servicios = [
            'afiliacion' => [
                [
                    'name' => 'Afiliación Empresas',
                    'cantidad' => [
                        'pendientes' => Mercurio30::where(['estado' => 'P', 'usuario' => $user])->count(),
                        'aprobados' => Mercurio30::where(['estado' => 'A', 'usuario' => $user])->count(),
                        'rechazados' => Mercurio30::where(['estado' => 'R', 'usuario' => $user])->count(),
                        'devueltos' => Mercurio30::where(['estado' => 'D', 'usuario' => $user])->count(),
                        'temporales' => Mercurio30::where(['estado' => 'T', 'usuario' => $user])->count(),
                    ],
                    'icon' => 'E',
                    'url' => 'aprobacionemp/index',
                    'imagen' => 'empresas.jpg',
                ],
                [
                    'name' => 'Afiliación Independientes',
                    'cantidad' => [
                        'pendientes' => Mercurio41::where(['estado' => 'P', 'usuario' => $user])->count(),
                        'aprobados' => Mercurio41::where(['estado' => 'A', 'usuario' => $user])->count(),
                        'rechazados' => Mercurio41::where(['estado' => 'R', 'usuario' => $user])->count(),
                        'devueltos' => Mercurio41::where(['estado' => 'D', 'usuario' => $user])->count(),
                        'temporales' => Mercurio41::where(['estado' => 'T', 'usuario' => $user])->count(),
                    ],
                    'icon' => 'I',
                    'url' => 'aprobaindepen/index',
                    'imagen' => 'independiente.jpg',
                ],
                [
                    'name' => 'Afiliación Pensionados',
                    'cantidad' => [
                        'pendientes' => Mercurio38::where(['estado' => 'P', 'usuario' => $user])->count(),
                        'aprobados' => Mercurio38::where(['estado' => 'A', 'usuario' => $user])->count(),
                        'rechazados' => Mercurio38::where(['estado' => 'R', 'usuario' => $user])->count(),
                        'devueltos' => Mercurio38::where(['estado' => 'D', 'usuario' => $user])->count(),
                        'temporales' => Mercurio38::where(['estado' => 'T', 'usuario' => $user])->count(),
                    ],
                    'icon' => 'P',
                    'url' => 'aprobacionpen/index',
                    'imagen' => 'pensionado.jpg',
                ],
                [
                    'name' => 'Afiliación Trabajadores',
                    'cantidad' => [
                        'pendientes' => Mercurio31::where(['estado' => 'P', 'usuario' => $user])->count(),
                        'aprobados' => Mercurio31::where(['estado' => 'A', 'usuario' => $user])->count(),
                        'rechazados' => Mercurio31::where(['estado' => 'R', 'usuario' => $user])->count(),
                        'devueltos' => Mercurio31::where(['estado' => 'D', 'usuario' => $user])->count(),
                        'temporales' => Mercurio31::where(['estado' => 'T', 'usuario' => $user])->count(),
                    ],
                    'icon' => 'T',
                    'url' => 'aprobaciontra/index',
                    'imagen' => 'trabajadores.jpg',
                ],
                [
                    'name' => 'Afiliación Conyuges',
                    'cantidad' => [
                        'pendientes' => Mercurio32::where(['estado' => 'P', 'usuario' => $user])->count(),
                        'aprobados' => Mercurio32::where(['estado' => 'A', 'usuario' => $user])->count(),
                        'rechazados' => Mercurio32::where(['estado' => 'R', 'usuario' => $user])->count(),
                        'devueltos' => Mercurio32::where(['estado' => 'D', 'usuario' => $user])->count(),
                        'temporales' => Mercurio32::where(['estado' => 'T', 'usuario' => $user])->count(),
                    ],
                    'icon' => 'C',
                    'url' => 'aprobacioncon/index',
                    'imagen' => 'conyuges.jpg',
                ],
                [
                    'name' => 'Afiliación Beneficiarios',
                    'cantidad' => [
                        'pendientes' => Mercurio34::where(['estado' => 'P', 'usuario' => $user])->count(),
                        'aprobados' => Mercurio34::where(['estado' => 'A', 'usuario' => $user])->count(),
                        'rechazados' => Mercurio34::where(['estado' => 'R', 'usuario' => $user])->count(),
                        'devueltos' => Mercurio34::where(['estado' => 'D', 'usuario' => $user])->count(),
                        'temporales' => Mercurio34::where(['estado' => 'T', 'usuario' => $user])->count(),
                    ],
                    'icon' => 'B',
                    'url' => 'aprobacionben/index',
                    'imagen' => 'beneficiarios.jpg',
                ],
                [
                    'name' => 'Actualización datos Empresas',
                    'cantidad' => [
                        'pendientes' => Mercurio33::where(['estado' => 'P', 'usuario' => $user])->count(),
                        'aprobados' => Mercurio33::where(['estado' => 'A', 'usuario' => $user])->count(),
                        'rechazados' => Mercurio33::where(['estado' => 'R', 'usuario' => $user])->count(),
                        'devueltos' => Mercurio33::where(['estado' => 'D', 'usuario' => $user])->count(),
                        'temporales' => Mercurio33::where(['estado' => 'T', 'usuario' => $user])->count(),
                    ],
                    'icon' => 'AE',
                    'url' => 'actualizaemp/index',
                    'imagen' => 'empresas.jpg',
                ],
                [
                    'name' => 'Actualización datos Trabajador',
                    'cantidad' => [
                        'pendientes' => Mercurio47::where(['estado' => 'P', 'usuario' => $user])->count(),
                        'aprobados' => Mercurio47::where(['estado' => 'A', 'usuario' => $user])->count(),
                        'rechazados' => Mercurio47::where(['estado' => 'R', 'usuario' => $user])->count(),
                        'devueltos' => Mercurio47::where(['estado' => 'D', 'usuario' => $user])->count(),
                        'temporales' => Mercurio47::where(['estado' => 'T', 'usuario' => $user])->count(),
                    ],
                    'icon' => 'AT',
                    'url' => 'actualizatra/index',
                    'imagen' => 'datos_basicos.jpg',
                ],
            ],
            'productos' => [
                [
                    'name' => 'Lista Productos',
                    'cantidad' => 0,
                    'icon' => 'L',
                    'url' => 'admproductos/lista',
                    'imagen' => 'registro_empresa.jpg',
                ],
                [
                    'name' => 'Complemento nutricional',
                    'cantidad' => 0,
                    'icon' => 'N',
                    'url' => 'admproductos/aplicados/27',
                    'imagen' => 'complemento.jpg',
                ],
            ],
        ];

        return view('cajas/principal/index', [
            'tipfun' => $this->tipfun,
            'usuario' => $this->user['usuario'] ?? '',
            'nombre' => $this->user['nombre'] ?? '',
            'servicios' => $servicios,
        ]);
    }

    public function dashboard()
    {
        return view('cajas/principal/dashboard', [
            'title' => 'Estadística',
        ]);
    }

    public function traerUsuariosRegistrados()
    {
        $data = [];
        $labels = [];

        $mercurio07 = Mercurio07::select('tipo', DB::raw('count(*) as documento'))->groupBy('tipo')->get();
        foreach ($mercurio07 as $mmercurio07) {
            $mercurio06 = Mercurio06::where('tipo', $mmercurio07->tipo)->first();
            $data[] = $mmercurio07->documento;
            $labels[] = $mercurio06->detalle;
        }
        $response['data'] = $data;
        $response['labels'] = $labels;

        return response()->json($response);
    }

    public function traerOpcionMasUsuada()
    {
        $data = [];
        $labels = [];
        $mercurio20 = Mercurio20::select('accion', DB::raw('count(*) as log'))->groupBy('accion')->get();
        foreach ($mercurio20 as $mmercurio20) {
            $data[] = $mmercurio20->log;
            $labels[] = $mmercurio20->accion;
        }
        $response['data'] = $data;
        $response['labels'] = $labels;

        return response()->json($response);
    }

    public function traerMotivoMasUsuada()
    {
        $out = Mercurio10::select('mercurio11.detalle', 'mercurio10.codest', DB::raw('count(*) as cantidad'))
            ->join('mercurio11', 'mercurio10.codest', '=', 'mercurio11.codest')
            ->groupBy('mercurio10.codest')
            ->get()
            ->map(function ($item) {
                return [
                    'data' => $item->cantidad,
                    'labels' => $item->detalle
                ];
            });

        $data = [];
        $labels = [];
        foreach ($out as $item) {
            $data[] = $item['data'];
            $labels[] = $item['labels'];
        }
        return response()->json([
            'data' => $data,
            'labels' => $labels,
        ]);
    }

    public function traerCargaLaboral()
    {
        try {
            $mercurio09 = Mercurio09::all();
            $out = Gener02::select('gener02.usuario', 'gener02.nombre')
                ->join('mercurio08', 'gener02.usuario', '=', 'mercurio08.usuario')
                ->distinct()
                ->get()
                ->map(function ($item) use ($mercurio09) {
                    $count = 0;
                    foreach ($mercurio09 as $m09) {
                        $count += Mercurio08::where('tipopc', $m09->tipopc)
                            ->where('usuario', $item->usuario)
                            ->count();
                    }
                    return [
                        'data' => $count,
                        'labels' => $item->nombre
                    ];
                });

            $data = [];
            $labels = [];
            foreach ($out as $item) {
                $data[] = $item['data'];
                $labels[] = $item['labels'];
            }
            return response()->json([
                'data' => $data,
                'labels' => $labels,
                'success' => true,
            ]);
        } catch (DebugException $err) {
            $response = [
                'success' => false,
                'msj' => $err->getMessage(),
            ];
        }

        return response()->json($response);
    }

    public function fileExisteGlobal(Request $request)
    {
        $file = $request->input('file');
        $id = $request->input('id');
        $coddoc = $request->input('coddoc');

        $archivo = base64_decode($file);
        $fichero = storage_path('temp/' . $archivo);
        if (file_exists($fichero)) {
            return response()->file($fichero, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $file . '"',
            ]);
        } else {
            return response()->json(null);
        }
    }
}
