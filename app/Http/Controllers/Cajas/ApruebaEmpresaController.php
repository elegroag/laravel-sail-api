<?php

namespace App\Http\Controllers\Cajas;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Library\Collections\ParamsEmpresa;
use App\Models\Adapter\DbBase;
use App\Models\Gener42;
use App\Models\Mercurio01;
use App\Models\Mercurio02;
use App\Models\Mercurio06;
use App\Models\Mercurio07;
use App\Models\Mercurio10;
use App\Models\Mercurio11;
use App\Models\Mercurio30;
use App\Models\Mercurio37;
use App\Services\Aprueba\ApruebaSolicitud;
use App\Services\CajaServices\EmpresaServices;
use App\Services\Reports\CsvReportStrategy;
use App\Services\Reports\ExcelReportStrategy;
use App\Services\Reports\ReportGenerator;
use App\Services\Srequest;
use App\Services\Utils\CalculatorDias;
use App\Services\Utils\Comman;
use App\Services\Utils\GeneralService;
use App\Services\Utils\NotifyEmailServices;
use App\Services\Utils\Pagination;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class ApruebaEmpresaController extends ApplicationController
{
    protected $tipopc = 2;

    protected $services;

    protected $db;

    protected $user;

    protected $tipfun;

    /**
     * independienteServices variable
     *
     * @var EmpresaServices
     */
    protected $empresaServices;

    public function __construct()
    {
        $this->db = DbBase::rawConnect();
        $this->user = session('user');
        $this->tipfun = session('tipfun');
    }

    /**
     * aplicarFiltro function
     *
     * @changed [2023-12-19]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     *
     * @return void
     */
    public function aplicarFiltro(Request $request, string $estado = 'P')
    {
        $cantidad_pagina = $request->input('numero', 10);
        $usuario = $this->user['usuario'];
        $query_str = ($estado == 'T') ? " estado='{$estado}'" : "usuario='{$usuario}' and estado='{$estado}'";

        $pagination = new Pagination(
            new Srequest([
                'query' => $query_str,
                'estado' => $estado,
                'cantidadPaginas' => $cantidad_pagina,
            ])
        );

        $query = $pagination->filter(
            $request->input('campo'),
            $request->input('condi'),
            $request->input('value')
        );

        set_flashdata('filter_empresa', $query, true);
        set_flashdata('filter_params', $pagination->filters, true);

        $response = $pagination->render(new EmpresaServices);

        return response()->json($response);
    }

    public function listar(Request $request, $estado = 'P')
    {
        try {
            $pagination = new Pagination;
            $filtro = $pagination->filter(
                $request->input('campo'),
                $request->input('condi'),
                $request->input('value')
            );

            $empresaServices = new EmpresaServices;
            $out = $empresaServices->findByUserAndEstado(
                new Srequest([
                    'usuario' => $this->user['usuario'],
                    'estado' => $estado,
                    'filtro' => $filtro,
                ])
            );

            $response = [
                'success' => true,
                'msj' => 'Consulta realizada con exito',
                'data' => $out,
            ];
        } catch (DebugException $err) {
            $response = [
                'success' => false,
                'msj' => $err->getMessage(),
            ];
        }

        return response()->json($response);
    }

    /**
     * changeCantidadPagina function
     *
     * @changed [2023-12-19]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     *
     * @param  string  $estado
     * @return void
     */
    public function changeCantidadPagina(Request $request, string $estado = 'P')
    {
        return $this->buscar($request, $estado);
    }

    /**
     * index function
     *
     * @changed [2023-12-19]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     *
     * @param  string  $estado
     * @return void
     */
    public function index()
    {
        $campo_field = [
            'nit' => 'NIT',
            'razsoc' => 'Razon social',
            'codzon' => 'Codigo zona',
            'documento' => 'ID',
            'fecini' => 'Fecha inicio',
            'cedrep' => 'Cedula representante',
        ];

        $params = $this->loadParametrosView();

        return view('cajas.aprobacionemp.index', [
            ...$params,
            'campo_filtro' => $campo_field,
            'filters' => get_flashdata_item('filter_params'),
            'title' => 'Aprueba Empresa',
            'mercurio11' => Mercurio11::get(),
        ]);
    }

    /**
     * opcional function
     *
     * @changed [2023-12-19]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     *
     * @param  string  $estado
     * @return void
     */
    public function opcional($estado = 'P')
    {
        $this->setParamToView('hide_header', true);
        $campo_field = [
            'nit' => 'Nit',
            'razsoc' => 'Razon Social',
        ];
        $help = 'Esta opcion permite manejar los ';
        $this->setParamToView('help', $help);
        $this->setParamToView('title', 'Aprueba Empresa');

        $collection = Mercurio30::where('estado', $estado)
            ->where('usuario', $this->user['usuario'])
            ->orderBy('fecini', 'ASC')
            ->get();

        $empresaServices = new EmpresaServices;
        $data = $empresaServices->dataOptional($collection, $estado);

        $this->setParamToView('empresas', $data);
        $this->setParamToView('buttons', ['F']);
        $this->setParamToView('campo_filtro', $campo_field);
        $this->setParamToView('pagina_con_estado', $estado);
    }

    /**
     * buscar function
     *
     * @param  string  $estado
     * @return void
     */
    public function buscar(Request $request, $estado = 'P')
    {
        $pagina = $request->input('pagina', 1);
        $cantidad_pagina = $request->input('numero', 10);
        $usuario = $this->user['usuario'];
        $query_str = ($estado == 'T') ? " estado='{$estado}'" : "usuario='{$usuario}' and estado='{$estado}'";

        $pagination = new Pagination(
            new Srequest(
                [
                    'query' => $query_str,
                    'estado' => $estado,
                    'cantidadPaginas' => $cantidad_pagina,
                    'pagina' => $pagina,
                ]
            )
        );

        if (
            get_flashdata_item('filter_empresa') != false
        ) {
            $query = $pagination->persistencia(get_flashdata_item('filter_params'));
        } else {
            $query = $pagination->filter(
                $request->input('campo'),
                $request->input('condi'),
                $request->input('value')
            );
        }

        set_flashdata('filter_empresa', $query, true);
        set_flashdata('filter_params', $pagination->filters, true);

        $response = $pagination->render(new EmpresaServices);

        return $this->renderObject($response, false);
    }

    /**
     * devolver function
     *
     * @changed [2023-12-00]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     *
     * @return void
     */
    public function devolver(Request $request)
    {
        $this->setResponse('ajax');
        try {
            $empresaServices = new EmpresaServices;
            $notifyEmailServices = new NotifyEmailServices;

            $validated = $request->validate([
                'id' => 'required|integer',
                'codest' => 'required|string|max:10',
                'nota' => 'nullable|string|max:5000',
                'campos_corregir' => 'sometimes|array',
                'campos_corregir.*' => 'string|max:100',
            ]);

            $id = $validated['id'];
            $codest = $validated['codest'];
            $nota = $validated['nota'] ?? null;
            $array_corregir = $validated['campos_corregir'] ?? [];
            $campos_corregir = $array_corregir ? implode(';', $array_corregir) : '';

            $mercurio30 = Mercurio30::where('id', $id)->first();
            if ($mercurio30->getEstado() == 'D') {
                throw new DebugException('El registro ya se encuentra devuelto, no se requiere de repetir la acción.', 201);
            }

            $empresaServices->devolver($mercurio30, $nota, $codest, $campos_corregir);
            $notifyEmailServices->emailDevolver(
                $mercurio30,
                $empresaServices->msjDevolver($mercurio30, $nota)
            );

            $salida = [
                'success' => true,
                'msj' => 'El proceso se ha completado con éxito',
            ];
        } catch (DebugException $err) {
            $salida = [
                'success' => false,
                'msj' => $err->getMessage(),
                'code' => $err->getCode(),
            ];
        }

        return $this->renderObject($salida, false);
    }

    /**
     * rechazar function
     *
     * @changed [2023-12-19]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     *
     * @return void
     */
    public function rechazar(Request $request)
    {
        $this->setResponse('ajax');
        $notifyEmailServices = new NotifyEmailServices;
        $empresaServices = new EmpresaServices;
        try {
            $validated = $request->validate([
                'id' => 'required|integer',
                'codest' => 'required|string|max:10',
                'nota' => 'nullable|string|max:5000',
            ]);
            $id = $validated['id'];
            $nota = $validated['nota'] ?? null;
            $codest = $validated['codest'];

            $mercurio30 = Mercurio30::where('id', $id)->first();

            if ($mercurio30->getEstado() == 'X') {
                throw new DebugException('El registro ya se encuentra rechazado, no se requiere de repetir la acción.', 201);
            }

            $empresaServices->rechazar($mercurio30, $nota, $codest);

            $notifyEmailServices->emailRechazar(
                $mercurio30,
                $empresaServices->msjRechazar($mercurio30, $nota)
            );

            $salida = [
                'success' => true,
                'msj' => 'El proceso se ha completado con éxito',
            ];
        } catch (DebugException $err) {
            $salida = [
                'success' => false,
                'msj' => $err->getMessage(),
                'code' => $err->getCode(),
            ];
        }

        return $this->renderObject($salida, false);
    }

    /**
     * pendiente_email function
     * metodo vista
     * autor edwin andres legro agudelo
     * fecha 24-08-2021
     *
     * @return void
     */
    public function pendienteEmail()
    {
        $this->setParamToView('title', 'Procesar Notificación Pendiente');
    }

    /**
     * rezagoCorreo function
     * metodo ajax
     * autor edwin andres legro agudelo
     * fecha 24-08-2021
     *
     * @return void
     */
    public function rezagoCorreo(Request $request) {}

    /**
     * empresa_search function
     * metodo ajax
     * autor edwin andres legro agudelo
     * fecha 24-08-2021
     *
     * @return void
     */
    public function empresaSearch(Request $request)
    {
        $this->setResponse('ajax');
        $nit = $request->input('nit');
        try {
            $mercurio30 = Mercurio30::where('nit', $nit)->where('estado', 'A')->first();
            if (! $mercurio30) {
                throw new DebugException('La empresa no está disponible para notificar por email', 501);
            } else {
                $data07 = Mercurio07::where('documento', $mercurio30->getDocumento())->get();
                $consultasOldServices = new GeneralService;
                $servicio = $consultasOldServices->webService('datosEmpresa', $_POST);
                if ($servicio['flag'] == false) {
                    throw new DebugException('Error al buscar la empresa en SISUWEB.', 502);
                }
                if (! $servicio['data']) {
                    throw new DebugException('Los datos de la empresa no está disponible en SISUWEB.', 503);
                }

                $mercurio07 = [];
                foreach ($data07 as $ai => $row) {
                    $mercurio07[] = [
                        'tipo' => $row->getTipo(),
                        'estado' => $row->getEstado(),
                        'coddoc' => $row->getCoddoc(),
                        'fecreg' => $row->getFecreg(),
                    ];
                }

                $salida = [
                    'success' => true,
                    'mercurio30' => [
                        'id' => $mercurio30->getId(),
                        'nit' => $mercurio30->getNit(),
                        'tipdoc' => $mercurio30->getTipdoc(),
                        'razsoc' => $mercurio30->getRazsoc(),
                        'email' => $mercurio30->getEmail(),
                        'estado' => $mercurio30->getEstado(),
                        'fecest' => $mercurio30->getFecest(),
                        'fecini' => $mercurio30->getFecini(),
                    ],
                    'mercurio07' => $mercurio07,
                    'subsi02' => $servicio['data'],
                ];
            }
        } catch (\Exception $err) {
            $salida = [
                'success' => false,
                'msj' => $err->getMessage(),
            ];
        }

        return $this->renderObject($salida, false);
    }

    /**
     * info_empresa function
     * mostrar la ficha de afiliación de la empresa
     *
     * @return void
     */
    public function infor(Request $request)
    {
        $this->setResponse('ajax');
        try {
            $empresaServices = new EmpresaServices;
            $validated = $request->validate([
                'id' => 'required|integer',
            ]);
            $id = $validated['id'];

            $mercurio30 = Mercurio30::where('id', $id)->first();
            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
                [
                    'servicio' => 'ComfacaAfilia',
                    'metodo' => 'parametros_empresa',
                ]
            );

            $datos_captura = $procesadorComando->toArray();
            $paramsEmpresa = new ParamsEmpresa;
            $paramsEmpresa->setDatosCaptura($datos_captura);

            $adjuntos = $empresaServices->adjuntos($mercurio30);
            $seguimiento = $empresaServices->seguimiento($mercurio30);

            $mercurio06 = Mercurio06::where("tipo", $mercurio30->tipo)->first();
            $_tipsoc = ParamsEmpresa::getTipoSociedades();
            $tipsoc_detalle = $_tipsoc[$mercurio30->tipsoc];

            $htmlEmpresa = view(
                'cajas.aprobacionemp.tmp.consulta',
                [
                    'mercurio30' => $mercurio30,
                    'mercurio01' => Mercurio01::first(),
                    'det_tipo' => $mercurio06->getDetalle(),
                    '_coddoc' => ParamsEmpresa::getTipoDocumentos(),
                    '_calemp' => ParamsEmpresa::getCalidadEmpresa(),
                    '_codciu' => ParamsEmpresa::getCiudades(),
                    '_codzon' => ParamsEmpresa::getZonas(),
                    '_codact' => ParamsEmpresa::getActividades(),
                    'tipsoc_detalle' => $tipsoc_detalle,
                ]
            )->render();

            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
                [
                    'servicio' => 'ComfacaEmpresas',
                    'metodo' => 'informacion_empresa',
                    'params' => [
                        'nit' => $mercurio30->nit,
                    ],
                ]
            );

            $out = $procesadorComando->toArray();
            $empresa_sisuweb = ($out['success']) ? $out['data'] : false;

            $campos_disponibles = $mercurio30->CamposDisponibles();
            $response = [
                'success' => true,
                'data' => $mercurio30->toArray(),
                'empresa_sisuweb' => $empresa_sisuweb,
                'mercurio11' => Mercurio11::all(),
                'consulta_empresa' => $htmlEmpresa,
                'adjuntos' => $adjuntos,
                'seguimiento' => $seguimiento,
                'campos_disponibles' => $campos_disponibles,
            ];
        } catch (DebugException $err) {
            $response = [
                'success' => false,
                'msj' => $err->getMessage(),
            ];
        }

        return $this->renderObject($response, false);
    }

    public function loadParametrosView()
    {
        $procesadorComando = Comman::Api();
        $procesadorComando->runCli(
            [
                'servicio' => 'ComfacaAfilia',
                'metodo' => 'parametros_empresa',
            ]
        );

        $datos_captura = $procesadorComando->toArray();
        $paramsEmpresa = new ParamsEmpresa;
        $paramsEmpresa->setDatosCaptura($datos_captura);

        $_coddocrepleg = [];
        foreach (ParamsEmpresa::getCodruaDocumentos() as $ai => $valor) {
            if ($valor == 'TI' || $valor == 'RC') {
                continue;
            }
            $_coddocrepleg[$ai] = $valor;
        }

        return [
            '_tipdur' => ParamsEmpresa::getTipoDuracion(),
            '_codind' => ParamsEmpresa::getCodigoIndice(),
            '_contratista' => ParamsEmpresa::getContratista(),
            '_todmes' => ParamsEmpresa::getPagaMes(),
            '_forpre' => ParamsEmpresa::getFormaPresentacion(),
            '_tipsoc' => ParamsEmpresa::getTipoSociedades(),
            '_pymes' => ParamsEmpresa::getPymes(),
            '_tipemp' => ParamsEmpresa::getTipoEmpresa(),
            '_tipapo' => ParamsEmpresa::getTipoAportante(),
            '_ofiafi' => ParamsEmpresa::getOficina(),
            '_colegio' => ParamsEmpresa::getColegio(),
            '_tipper' => ParamsEmpresa::getTipoPersona(),
            '_codzon' => ParamsEmpresa::getZonas(),
            '_calemp' => ParamsEmpresa::getCalidadEmpresa(),
            '_codciu' => ParamsEmpresa::getCiudades(),
            '_codact' => ParamsEmpresa::getActividades(),
            '_coddoc' => ParamsEmpresa::getTipoDocumentos(),
            '_ciupri' => ParamsEmpresa::getCiudadesComerciales(),
            '_coddocrepleg' => $_coddocrepleg,
        ];
    }

    public function editaEmpresa(Request $request)
    {
        $this->setResponse('ajax');
        $validated = $request->validate([
            'id' => 'required|integer',
            'nit' => 'required|string',
            'razsoc' => 'sometimes|string|max:255',
            'codact' => 'sometimes|string|max:10',
            'digver' => 'sometimes|string|max:2',
            'calemp' => 'sometimes|string|max:5',
            'cedrep' => 'sometimes|string|max:50',
            'repleg' => 'sometimes|string|max:150',
            'direccion' => 'sometimes|string|max:255',
            'codciu' => 'sometimes|string|max:10',
            'codzon' => 'sometimes|string|max:10',
            'telefono' => 'sometimes|string|max:50',
            'celular' => 'sometimes|string|max:50',
            'fax' => 'sometimes|string|max:50',
            'email' => 'sometimes|email|max:255',
            'sigla' => 'sometimes|string|max:50',
            'fecini' => 'sometimes|date',
            'tottra' => 'sometimes|numeric',
            'valnom' => 'sometimes|numeric',
            'tipsoc' => 'sometimes|string|max:2',
            'dirpri' => 'sometimes|string|max:255',
            'ciupri' => 'sometimes|string|max:10',
            'celpri' => 'sometimes|string|max:50',
            'tipemp' => 'sometimes|string|max:5',
            'emailpri' => 'sometimes|email|max:255',
            'tipper' => 'sometimes|in:N,J',
            'matmer' => 'sometimes|string|max:50',
            'coddocrepleg' => 'sometimes|string|max:5',
            'prinom' => 'sometimes|string|max:50',
            'priape' => 'sometimes|string|max:50',
            'segnom' => 'sometimes|nullable|string|max:50',
            'segape' => 'sometimes|nullable|string|max:50',
            'prinomrepleg' => 'sometimes|nullable|string|max:50',
            'priaperepleg' => 'sometimes|nullable|string|max:50',
            'segnomrepleg' => 'sometimes|nullable|string|max:50',
            'segaperepleg' => 'sometimes|nullable|string|max:50',
            'telpri' => 'sometimes|string|max:50',
        ]);
        $nit = $validated['nit'];
        $id = $validated['id'];
        try {
            $mercurio30 = Mercurio30::where('nit', $nit)->where('id', $id)->first();
            if (! $mercurio30) {
                throw new DebugException('La empresa no está disponible para notificar por email', 501);
            } else {
                $tipsoc = $validated['tipsoc'] ?? $request->input('tipsoc');
                if (strlen($tipsoc) == 1) {
                    $tipsoc = str_pad($tipsoc, 2, '0', STR_PAD_LEFT);
                }
                $data = [
                    'razsoc' => $validated['razsoc'] ?? null,
                    'codact' => $validated['codact'] ?? null,
                    'digver' => $validated['digver'] ?? null,
                    'calemp' => $validated['calemp'] ?? null,
                    'cedrep' => $validated['cedrep'] ?? null,
                    'repleg' => $validated['repleg'] ?? null,
                    'direccion' => $validated['direccion'] ?? null,
                    'codciu' => $validated['codciu'] ?? null,
                    'codzon' => $validated['codzon'] ?? null,
                    'telefono' => $validated['telefono'] ?? null,
                    'celular' => $validated['celular'] ?? null,
                    'fax' => $validated['fax'] ?? null,
                    'email' => $validated['email'] ?? null,
                    'sigla' => $validated['sigla'] ?? null,
                    'fecini' => $validated['fecini'] ?? null,
                    'tottra' => $validated['tottra'] ?? null,
                    'valnom' => $validated['valnom'] ?? null,
                    'tipsoc' => $tipsoc,
                    'dirpri' => $validated['dirpri'] ?? null,
                    'ciupri' => $validated['ciupri'] ?? null,
                    'celpri' => $validated['celpri'] ?? null,
                    'tipemp' => $validated['tipemp'] ?? null,
                    'emailpri' => $validated['emailpri'] ?? null,
                    'tipper' => $validated['tipper'] ?? null,
                    'matmer' => $validated['matmer'] ?? null,
                    'coddocrepleg' => $validated['coddocrepleg'] ?? '1',
                    'prinom' => (($validated['tipper'] ?? $request->input('tipper')) == 'N') ? ($validated['prinom'] ?? $request->input('prinom')) : ($validated['prinomrepleg'] ?? $request->input('prinomrepleg')),
                    'priape' => (($validated['tipper'] ?? $request->input('tipper')) == 'N') ? ($validated['priape'] ?? $request->input('priape')) : ($validated['priaperepleg'] ?? $request->input('priaperepleg')),
                    'segnom' => (($validated['tipper'] ?? $request->input('tipper')) == 'N') ? ($validated['segnom'] ?? $request->input('segnom')) : ($validated['segnomrepleg'] ?? $request->input('segnomrepleg')),
                    'segape' => (($validated['tipper'] ?? $request->input('tipper')) == 'N') ? ($validated['segape'] ?? $request->input('segape')) : ($validated['segaperepleg'] ?? $request->input('segaperepleg')),
                    'prinomrepleg' => (($validated['tipper'] ?? $request->input('tipper')) == 'J') ? ($validated['prinomrepleg'] ?? $request->input('prinomrepleg')) : '',
                    'priaperepleg' => (($validated['tipper'] ?? $request->input('tipper')) == 'J') ? ($validated['priaperepleg'] ?? $request->input('priaperepleg')) : '',
                    'segnomrepleg' => (($validated['tipper'] ?? $request->input('tipper')) == 'J') ? ($validated['segnomrepleg'] ?? $request->input('segnomrepleg')) : '',
                    'segaperepleg' => (($validated['tipper'] ?? $request->input('tipper')) == 'J') ? ($validated['segaperepleg'] ?? $request->input('segaperepleg')) : '',
                    'telpri' => $validated['telpri'] ?? null,
                ];
                // evita sobreescribir con null: solo campos enviados
                $data = array_filter($data, function ($v) {
                    return ! is_null($v);
                });
                Mercurio30::where('id', $id)->where('nit', $nit)->update($data);
                $salida = [
                    'msj' => 'Proceso se ha completado con éxito',
                    'success' => true,
                ];
            }
        } catch (DebugException $err) {
            $salida = [
                'success' => false,
                'msj' => $err->getMessage(),
                'errors' => $err->render($request),
            ];
        }

        return response()->json($salida);
    }

    /**
     * empresaSisuweb function
     * Datos de la empresa en sisuweb, si ya está registrada. pruebas 98588506
     *
     * @param [type] $nit
     * @return void
     */
    public function buscarEnSisuView($id, $nit)
    {
        $mercurio30 = Mercurio30::where("nit", $nit)->first();
        if (! $mercurio30) {
            set_flashdata('error', [
                'msj' => 'La empresa no se encuentra registrada.',
                'code' => 201,
            ]);

            return redirect('aprobacionemp/index');
            exit();
        }

        $procesadorComando = Comman::Api();
        $procesadorComando->runCli(
            [
                'servicio' => 'ComfacaEmpresas',
                'metodo' => 'informacion_empresa',
                'params' => [
                    'nit' => $nit,
                ],
            ]
        );
        $response = $procesadorComando->toArray();
        if (! $response['success']) {
            set_flashdata('error', [
                'msj' => 'La empresa no se encuentra registrada.',
                'code' => 201,
            ]);

            return redirect('aprobacionemp/index');
            exit();
        }

        return response()->json([
            'success' => true,
            'idEmpresa' => $id,
            'empresa' => $response['data'],
            'trayectoria' => $response['trayectoria'],
            'sucursales' => $response['sucursales'],
            'listas' => $response['listas']
        ]);
    }

    /**
     * excel_reporte function
     * pendientes, devueltos y rechazados
     *
     * @return void
     */
    public function export(Request $request)
    {
        try {
            $format = $request->query('format', 'csv');
            $strategy = $format === 'excel' ? new ExcelReportStrategy() : new CsvReportStrategy();
            $ext = $format === 'excel' ? 'xlsx' : 'csv';

            // Base del filtro igual que en listar/aplicarFiltro
            $estado = (string) $request->input('estado', 'P');
            $usuario = $this->user['usuario'] ?? null;
            $query_str = ($estado === 'T') ? " estado='{$estado}'" : "usuario='{$usuario}' and estado='{$estado}'";

            $pagination = new Pagination(new Srequest([
                'query' => $query_str,
                'estado' => $estado,
            ]));

            // Construir filtro del aplicativo (cadena SQL completa)
            $filtro = $pagination->filter(
                $request->input('campo'),
                $request->input('condi'),
                $request->input('value')
            );

            // Columnas exportadas (campos válidos de Mercurio30)
            $columns = [
                'NIT' => 'nit',
                'Razón Social' => 'razsoc',
                'Zona' => 'codzon',
                'Documento' => 'documento',
                'Fecha Inicio' => 'fecini',
                'Estado' => 'estado',
                'Usuario' => 'usuario',
            ];

            $gen = (new ReportGenerator($strategy))
                ->for(Mercurio30::query())
                ->columns($columns)
                ->filename('mercurio30_' . now()->format('Ymd_His') . '.' . $ext)
                ->filter(function ($q) use ($filtro) {
                    if (is_string($filtro) && trim($filtro) !== '') {
                        $q->whereRaw($filtro);
                    }
                });

            return $gen->download();
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ]);
        }
    }

    public function aprueba(Request $request)
    {
        $this->db->begin();
        try {
            try {
                $apruebaSolicitud = new ApruebaSolicitud();
                $postData = $request->all();
                $idSolicitud = $request->input('id');
                $calemp = 'E';
                $solicitud = $apruebaSolicitud->main(
                    $calemp,
                    $idSolicitud,
                    $postData
                );

                $this->db->commit();
                $solicitud->enviarMail($request->input('actapr'), $request->input('fecapr'));
                $salida = [
                    'success' => true,
                    'msj' => 'El registro se completo con éxito',
                ];
            } catch (DebugException $err) {
                $this->db->rollback();
                $salida = $err->render($request);
            }
        } catch (\Exception $e) {
            $this->db->rollback();
            $salida = [
                'success' => false,
                'msj' => $e->getMessage(),
            ];
        }
        return response()->json($salida);
    }

    public function borrarFiltro()
    {
        set_flashdata('filter_independiente', false, true);
        set_flashdata('filter_params', false, true);

        return response()->json([
            'success' => true,
            'query' => get_flashdata_item('filter_independiente'),
            'filter' => get_flashdata_item('filter_params'),
        ]);
    }

    public function aportes(Request $request, int $id)
    {
        $comando = '';
        try {

            $mercurio30 = Mercurio30::where('id', $id)->first();
            if (! $mercurio30) {
                throw new DebugException('La empresa no se encuentra registrada.', 201);
            }

            $ps = Comman::Api();
            $ps->runCli(
                [
                    'servicio' => 'AportesEmpresas',
                    'metodo' => 'buscarAportesEmpresa',
                    'params' => $mercurio30->getNit(),
                ]
            );

            if ($ps->isJson() == false) {
                throw new DebugException('Error procesando la consulta de aportes', 501);
            }

            $salida = $ps->toArray();
            $salida['solicitud'] = $mercurio30->getArray();
        } catch (DebugException $err) {
            $salida = [
                'success' => false,
                'msj' => 'No se pudo realizar el movimiento ' . "\n" . $err->getMessage() . "\n " . $err->getLine(),
                'comando' => $comando,
                'errors' => $err->render($request),
            ];
        }

        return response()->json($salida);
    }

    /**
     * infoAprobadoView function
     * Detalle de la aprobacion de la empresa, traer los datos de SISU de la empresa
     *
     * @param [type] $id
     * @return void
     */
    public function infoAprobadoView($id)
    {
        try {
            $mercurio30 = Mercurio30::where('id', $id)->first();
            if (! $mercurio30) {
                throw new DebugException('La empresa no se encuentra aprobada para consultar sus datos.', 501);
            }

            $ps = Comman::Api();
            $ps->runCli(
                [
                    'servicio' => 'ComfacaAfilia',
                    'metodo' => 'parametros_empresa',
                ]
            );
            $datos_captura = $ps->toArray();
            $paramsEmpresa = new ParamsEmpresa;
            $paramsEmpresa->setDatosCaptura($datos_captura);

            $ps = Comman::Api();
            $ps->runCli(
                [
                    'servicio' => 'ComfacaEmpresas',
                    'metodo' => 'informacion_empresa',
                    'params' => [
                        'nit' => $mercurio30->getNit(),
                    ],
                ]
            );

            if ($ps->isJson() == false) {
                throw new DebugException('Error al buscar la empresa en Sisuweb', 501);
            }

            $out = $ps->toArray();
            if ($out['success'] == false) {
                throw new DebugException('Los datos de la empresa no se encuentra disponibles.', 501);
            }

            $empresa = $out['data'];

            $mercurio01 = Mercurio01::first();
            $det_tipo = Mercurio06::where("tipo = '{$mercurio30->getTipo()}'")->first()->getDetalle();

            $mercurio30 = new Mercurio30;
            $mercurio30->createAttributes($empresa);

            $htmlEmpresa = view('cajas.aprobacionemp.tmp.consulta', [
                'mercurio30' => $mercurio30,
                'mercurio01' => $mercurio01,
                'det_tipo' => $det_tipo,
                '_coddoc' => ParamsEmpresa::getTipoDocumentos(),
                '_calemp' => ParamsEmpresa::getCalidadEmpresa(),
                '_codciu' => ParamsEmpresa::getCiudades(),
                '_codzon' => ParamsEmpresa::getZonas(),
                '_codact' => ParamsEmpresa::getActividades(),
                '_tipsoc' => ParamsEmpresa::getTipoSociedades(),
            ])->render();

            $code_estados = [];
            $query = Mercurio11::all();
            foreach ($query as $row) {
                $code_estados[$row->getCodest()] = $row->getDetalle();
            }

            $this->setParamToView('code_estados', $code_estados);
            $this->setParamToView('mercurio30', $mercurio30);
            $this->setParamToView('consulta_empresa', $htmlEmpresa);
            $this->setParamToView('hide_header', true);
            $this->setParamToView('idModel', $id);
            $this->setParamToView('nit', $mercurio30->getNit());
            $this->setParamToView('title', 'Empresa Aprobada ' . $mercurio30->getNit());
        } catch (DebugException $err) {
            set_flashdata('error', [
                'msj' => $err->getMessage(),
                'code' => 201,
            ]);

            return redirect('aprobacionemp/index/A');
            exit;
        }
    }

    /**
     * deshacer function
     * metodo ajax permite deshacer la afiliacion de la empresa
     *
     * @param [type] $id
     * @return void
     */
    public function deshacer(Request $request)
    {
        $this->setResponse('ajax');

        $procesadorComando = Comman::Api();
        $empresaServices = new EmpresaServices;
        $notifyEmailServices = new NotifyEmailServices;
        $action = $request->input('action');
        $codest = $request->input('codest');
        $sendEmail = $request->input('send_email');
        $nota = $request->input('nota');
        $comando = '';
        try {

            $id = $request->input('id');

            $mercurio30 = Mercurio30::where('id', $id)->where('estado', 'A')->first();
            if (! $mercurio30) {
                throw new DebugException('Los datos de la empresa no son validos para procesar.', 501);
            }

            $procesadorComando->runCli(
                [
                    'servicio' => 'ComfacaEmpresas',
                    'metodo' => 'informacion_empresa',
                    'params' => [
                        'nit' => $mercurio30->getNit(),
                    ],
                ]
            );

            $out = $procesadorComando->toArray();
            $empresaSisu = $out['data'];

            $procesadorComando->runCli(
                [
                    'servicio' => 'ComfacaAfilia',
                    'metodo' => 'deshacer_aprobacion_empresa',
                    'params' => [
                        'nit' => $mercurio30->getNit(),
                        'documento' => $mercurio30->getDocumento(),
                        'tipo_documento' => $mercurio30->getTipdoc(),
                        'fecha_aprueba' => $mercurio30->getFecest(),
                        'nota' => $nota,
                    ],
                ]
            );

            if ($procesadorComando->isJson() == false) {
                throw new DebugException('Error al procesar el deshacer la aprobación en SisuWeb.', 501);
            }

            $resdev = $procesadorComando->toArray();
            if ($resdev['success'] !== true) {
                throw new DebugException($resdev['message'], 501);
            }

            $datos = $resdev['data'];
            if ($datos['noAction']) {
                $salida = [
                    'success' => false,
                    'msj' => 'No se realizo ninguna acción, el estado de la empresa no es valido para realizar la acción requerida.',
                    'data' => $empresaSisu,
                ];
            } else {

                // procesar
                if ($action == 'D') {
                    $campos_corregir = '';
                    $empresaServices->devolver($mercurio30, $nota, $codest, $campos_corregir);
                    if ($sendEmail == 'S') {
                        $notifyEmailServices->emailDevolver($mercurio30, $empresaServices->msjDevolver($mercurio30, $nota));
                    }
                }

                if ($action == 'R') {
                    $empresaServices->rechazar($mercurio30, $nota, $codest);
                    if ($sendEmail == 'S') {
                        $notifyEmailServices->emailRechazar($mercurio30, $empresaServices->msjRechazar($mercurio30, $nota));
                    }
                }

                if ($action == 'I') {
                    $mercurio30->setEstado('I');
                    $mercurio30->setFecest(date('Y-m-d'));
                    $mercurio30->save();
                }

                $salida = [
                    'data' => $empresaSisu,
                    'success' => ($datos['isDelete'] || $datos['isDeleteTrayecto']) ? true : false,
                    'msj' => ($datos['isDelete'] || $datos['isDeleteTrayecto']) ? 'Se completo el proceso con éxito.' : 'No se realizo el cambio requerido, se debe comunicar al área de soporte de las TICS.',
                    'isDeleteTrayecto' => $datos['isDeleteTrayecto'],
                    'noAction' => $datos['noAction'],
                    'isDelete' => $datos['isDelete'],
                ];
            }
        } catch (DebugException $err) {
            $salida = [
                'success' => false,
                'msj' => 'Error no se pudo realizar el movimiento, ' . $err->getMessage(),
                'comando' => $comando,
                'file' => $err->getFile(),
                'line' => $err->getLine(),
                'isDeleteTrayecto' => false,
                'noAction' => false,
                'isDelete' => false,
            ];
        }

        return response()->json($salida);
    }

    /**
     * reaprobar function
     *
     * @return void
     */
    public function reaprobar(Request $request)
    {
        try {
            $id = $request->input('id');
            $nota = $request->input('nota');
            $today = Carbon::now();

            Mercurio30::where("id", $id)->update([
                'estado' => 'A',
                'fecest' => $today->format('Y-m-d'),
            ]);

            $item = Mercurio10::where("tipopc", $this->tipopc)->where("numero", $id)->max('item') + 1;
            $mercurio10 = new Mercurio10;
            $mercurio10->tipopc = $this->tipopc;
            $mercurio10->numero = $id;
            $mercurio10->item = $item;
            $mercurio10->estado = 'A';
            $mercurio10->nota = $nota;
            $mercurio10->fecsis = $today->format('Y-m-d');
            $mercurio10->save();

            $response = [
                'success' => true,
                'msj' => 'Movimiento realizado con éxito',
            ];
        } catch (DebugException $e) {
            $response = [
                'success' => false,
                'msj' => 'No se pudo realizar el movimiento ' . "\n" . $e->getMessage() . "\n " . $e->getLine(),
            ];
        }
        return response()->json($response);
    }
}
