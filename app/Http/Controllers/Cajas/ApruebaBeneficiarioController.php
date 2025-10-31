<?php

namespace App\Http\Controllers\Cajas;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Library\Collections\ParamsBeneficiario;
use App\Models\Adapter\DbBase;
use App\Models\Gener42;
use App\Models\Mercurio06;
use App\Models\Mercurio07;
use App\Models\Mercurio10;
use App\Models\Mercurio11;
use App\Models\Mercurio31;
use App\Models\Mercurio34;
use App\Services\Aprueba\ApruebaBeneficiario;
use App\Services\CajaServices\BeneficiarioServices;
use App\Services\Reports\CsvReportStrategy;
use App\Services\Reports\ExcelReportStrategy;
use App\Services\Reports\ReportGenerator;
use App\Services\Srequest;
use App\Services\Utils\AsignarFuncionario;
use App\Services\Utils\Comman;
use App\Services\Utils\NotifyEmailServices;
use App\Services\Utils\Pagination;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ApruebaBeneficiarioController extends ApplicationController
{
    protected $tipopc = 4;

    protected $db;

    protected $user;

    protected $tipfun;

    /**
     * services variable
     *
     * @var Services
     */
    protected $services;

    /**
     * trabajadorServices variable
     *
     * @var BeneficiarioServices
     */
    protected $beneficiarioServices;

    public function __construct()
    {
        $this->db = DbBase::rawConnect();
        $this->user = session('user');
        $this->tipfun = session('tipfun');
    }

    /**
     * aplicarFiltro function
     *
     * @changed [2023-12-20]
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
                'cantidadPaginas' => $cantidad_pagina,
                'query' => $query_str,
                'estado' => $estado,
            ])
        );
        $query = $pagination->filter(
            $request->input('campo'),
            $request->input('condi'),
            $request->input('value')
        );

        set_flashdata('filter_beneficiario', $query, true);
        set_flashdata('filter_params', $pagination->filters, true);

        $response = $pagination->render(new BeneficiarioServices);

        return $this->renderObject($response, false);
    }

    public function changeCantidadPagina(Request $request, string $estado = 'P')
    {
        return $this->buscar($request, $estado);
    }

    /**
     * index function
     *
     * @changed [2023-12-20]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     *
     * @param  string  $estado
     * @return void
     */
    public function index()
    {
        $this->setParamToView('hide_header', true);
        $campo_field = [
            'numdoc' => 'Identificación',
            'priape' => 'Primer apellido',
            'segape' => 'Segundo apellido',
            'prinom' => 'Primer nombre',
            'segnom' => 'Segundo nombre',
            'cedtra' => 'Cedula trabajador',
            'fecsol' => 'Fecha solicitud',
        ];

        $params = $this->loadParametrosView();

        return view('cajas.aprobacionben.index', [
            ...$params,
            'campo_filtro' => $campo_field,
            'filters' => get_flashdata_item('filter_params'),
            'title' => 'Aprueba Beneficiario',
            'mercurio11' => Mercurio11::get(),
        ]);
    }

    /**
     * buscar function
     *
     * @changed [2023-12-20]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     *
     * @param  string  $estado
     * @return void
     */
    public function buscar(Request $request, string $estado = 'P')
    {
        $pagina = ($request->input('pagina')) ? $request->input('pagina') : 1;
        $cantidad_pagina = ($request->input('numero')) ? $request->input('numero') : 10;
        $usuario = $this->user['usuario'];
        $query_str = ($estado == 'T') ? " estado='{$estado}'" : "usuario='{$usuario}' and estado='{$estado}'";

        $pagination = new Pagination(
            new Srequest([
                'cantidadPaginas' => $cantidad_pagina,
                'query' => $query_str,
                'estado' => $estado,
                'pagina' => $pagina,
            ])
        );
        if (get_flashdata_item('filter_beneficiario') != false) {
            $query = $pagination->persistencia(get_flashdata_item('filter_params'));
        } else {
            $query = $pagination->filter(
                $request->input('campo'),
                $request->input('condi'),
                $request->input('value')
            );
        }
        set_flashdata('filter_beneficiario', $query, true);
        set_flashdata('filter_params', $pagination->filters, true);

        $response = $pagination->render(new BeneficiarioServices);

        return response()->json($response);
    }

    /**
     * export function
     * Descargar reporte de beneficiarios según filtros del aplicativo
     */
    public function export(Request $request)
    {
        try {
            $format = $request->query('format', 'csv');
            $strategy = $format === 'excel' ? new ExcelReportStrategy() : new CsvReportStrategy();
            $ext = $format === 'excel' ? 'xlsx' : 'csv';

            // Base del filtro igual que en buscar/aplicarFiltro
            $estado = (string) $request->input('estado', 'P');
            $usuario = $this->user['usuario'] ?? null;
            $query_str = ($estado === 'T') ? " estado='{$estado}'" : "usuario='{$usuario}' and estado='{$estado}'";

            $pagination = new Pagination(new Srequest([
                'query' => $query_str,
                'estado' => $estado,
            ]));

            // Construir filtro del aplicativo (cadena SQL)
            $filtro = $pagination->filter(
                $request->input('campo'),
                $request->input('condi'),
                $request->input('value')
            );

            // Columnas de Mercurio34
            $columns = [
                'Identificación' => 'numdoc',
                'Nombres' => fn($r) => trim(($r->prinom ?? '') . ' ' . ($r->segnom ?? '')),
                'Apellidos' => fn($r) => trim(($r->priape ?? '') . ' ' . ($r->segape ?? '')),
                'Cédula Trabajador' => 'cedtra',
                'Estado' => 'estado',
                'Fecha Solicitud' => 'fecsol',
                'Usuario' => 'usuario',
            ];

            $gen = (new ReportGenerator($strategy))
                ->for(Mercurio34::query())
                ->columns($columns)
                ->filename('mercurio34_' . now()->format('Ymd_His') . '.' . $ext)
                ->filter(function ($q) use ($filtro) {
                    if (is_string($filtro) && trim($filtro) !== '') {
                        $q->whereRaw($filtro);
                    }
                });

            return $gen->download();
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ]);
        }
    }

    /**
     * aprueba function
     *
     * @changed [2023-12-19]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     *
     * @return void
     */
    public function aprueba(Request $request)
    {
        $this->db->begin();
        try {
            try {
                $aprueba = new ApruebaBeneficiario;
                $postData = $request->all();
                $idSolicitud = $request->input('id');
                $aprueba->findSolicitud($idSolicitud);
                $aprueba->findSolicitante();
                $aprueba->procesar($postData);
                $this->db->commit();
                $aprueba->enviarMail($request->input('actapr'), $request->input('feccap'));
                $salida = [
                    'success' => true,
                    'msj' => 'El registro se completo con éxito',
                ];
            } catch (DebugException $err) {
                $this->db->rollback();
                $salida = [
                    'success' => false,
                    'msj' => $err->getMessage(),
                    'errors' => $err->render($request),
                ];
            }
        } catch (\Exception $e) {
            $salida = [
                'success' => false,
                'msj' => $e->getMessage(),
            ];
        }

        return response()->json($salida);
    }

    public function devolver(Request $request)
    {
        try {
            $this->beneficiarioServices = new BeneficiarioServices;
            $notifyEmailServices = new NotifyEmailServices;
            $validated = $request->validate([
                'id' => 'required|integer',
                'nota' => 'nullable|string|max:5000',
                'codest' => 'required|string|max:10',
                'campos_corregir' => 'sometimes|array',
                'campos_corregir.*' => 'string|max:100',
            ]);

            $id = $validated['id'];
            $nota = $validated['nota'] ?? null;
            $codest = $validated['codest'];
            $array_corregir = $validated['campos_corregir'] ?? [];
            $campos_corregir = $array_corregir ? implode(';', $array_corregir) : '';
            $mercurio34 = Mercurio34::where("id", $id)->first();

            $this->beneficiarioServices->devolver($mercurio34, $nota, $codest, $campos_corregir);
            $notifyEmailServices->emailDevolver(
                $mercurio34,
                $this->beneficiarioServices->msjDevolver($mercurio34, $nota)
            );

            $response = [
                'success' => true,
                'msj' => 'Movimiento realizado con exito',
            ];
        } catch (DebugException $err) {
            $response = [
                'success' => false,
                'msj' => $err->getMessage(),
                'errors' => $err->render($request),
            ];
        }
        return response()->json($response);
    }

    public function rechazar(Request $request)
    {
        try {
            $notifyEmailServices = new NotifyEmailServices;
            $this->beneficiarioServices = new BeneficiarioServices;
            $validated = $request->validate([
                'id' => 'required|integer',
                'nota' => 'nullable|string|max:5000',
                'codest' => 'required|string|max:10',
            ]);
            $id = $validated['id'];
            $nota = $validated['nota'] ?? null;
            $codest = $validated['codest'];

            $mercurio34 = Mercurio34::where("id", $id)->first();
            $this->beneficiarioServices->rechazar($mercurio34, $nota, $codest);
            $notifyEmailServices->emailRechazar(
                $mercurio34,
                $this->beneficiarioServices->msjRechazar($mercurio34, $nota)
            );

            $response = [
                'success' => true,
                'msj' => 'Movimiento Realizado Con Exito',
            ];
        } catch (DebugException $e) {
            $response = [
                'success' => false,
                'msj' => $e->getMessage(),
                'errors' => $e->render($request),
            ];
        }
        return response()->json($response);
    }

    /**
     * infor function
     * @changed [2023-12-20]
     * @author elegroag <elegroag@ibero.edu.co
     * @return void
     */
    public function infor(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer',
            ]);
            $id = $validated['id'];
            
            $beneficiarioServices = new BeneficiarioServices;
            $solicitud = Mercurio34::where("id", $id)->first();
            if ($solicitud == false) {
                throw new DebugException('La solicitud de afiliación de beneficiario no es valida.', 501);
            }

            $trabajador_sisu = false;
            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
                [
                    'servicio' => 'ComfacaAfilia',
                    'metodo' => 'trabajador',
                    'params' => ['cedtra' => $solicitud->cedtra, 'estado' => 'A'],
                ]
            );

            $rqs = $procesadorComando->toArray();
            if (! empty($rqs)) {
                $trabajador_sisu = ($rqs['success']) ? $rqs['data'] : false;
            }

            $trabajador = new \stdClass;
            if (! $trabajador_sisu) {
                $tr = Mercurio31::where("cedtra", $solicitud->cedtra)->where("estado", 'A')->first();
                if (!$tr) $trabajador->estado = 'I';
            } else {
                $trabajador = new Mercurio31;
                $trabajador->fill($trabajador_sisu);
            }

            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
                [
                    'servicio' => 'ComfacaEmpresas',
                    'metodo' => 'informacion_beneficiario',
                    'params' => $solicitud->numdoc,
                ]
            );

            $rqs = $procesadorComando->toArray();
            $relacion_multiple = false;

            if ($rqs) {
                if ($rqs['success']) {
                    $sys_beneficiario = $rqs['data'];
                    $this->setParamToView('beneficiario_sisuweb', $sys_beneficiario);
                    $giro = $sys_beneficiario['giro'];
                    $vinculo_trabajador = false;

                    if ($rqs['relaciones']) {
                        $relacion_multiple = $rqs['relaciones'];
                        foreach ($rqs['relaciones'] as $ai => $relacion) {
                            if ($relacion['cedtra'] == $solicitud->cedtra) {
                                $vinculo_trabajador = true;
                                break;
                            }
                        }
                    }
                    $estado = ($vinculo_trabajador == false) ? 'I' : $sys_beneficiario['estado'];
                }
            }

            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
                [
                    'servicio' => 'ComfacaAfilia',
                    'metodo' => 'parametros_beneficiarios',
                ]
            );

            $datos_captura = $procesadorComando->toArray();
            $paramsBeneficiario = new ParamsBeneficiario;
            $paramsBeneficiario->setDatosCaptura($datos_captura);

            $html = view(
                'cajas/aprobacionben/tmp/consulta',
                [
                    'beneficiario' => $solicitud,
                    'detTipo' => Mercurio06::where("tipo", $solicitud->tipo)->first()->detalle,
                    '_coddoc' => ParamsBeneficiario::getTiposDocumentos(),
                    '_codciu' => ParamsBeneficiario::getCiudades(),
                    '_sexo' => ParamsBeneficiario::getSexos(),
                    '_estciv' => ParamsBeneficiario::getEstadoCivil(),
                    '_parent' => ParamsBeneficiario::getParentesco(),
                    '_captra' => ParamsBeneficiario::getCapacidadTrabajar(),
                    '_tipdis' => ParamsBeneficiario::getTipoDiscapacidad(),
                    '_nivedu' => ParamsBeneficiario::getNivelEducativo(),
                    '_giro' => ParamsBeneficiario::getTieneGiro(),
                    '_pago' => ParamsBeneficiario::getPago(),
                    '_ciunac' => ParamsBeneficiario::getCiudades(),
                    '_huerfano' => ParamsBeneficiario::getHuerfano(),
                    '_tiphij' => ParamsBeneficiario::getTipoHijo(),
                    '_nivedu' => ParamsBeneficiario::getNivelEducativo(),
                    '_calendario' => ParamsBeneficiario::getCalendario(),
                    '_codgir' => ParamsBeneficiario::getCodigoGiro(),
                ]
            )->render();

            $response = [
                'success' => true,
                'data' => $solicitud->toArray(),
                'mercurio11' => Mercurio11::all(),
                'consulta' => $html,
                'adjuntos' => $beneficiarioServices->adjuntos($solicitud),
                'seguimiento' => $beneficiarioServices->seguimiento($solicitud),
                'campos_disponibles' => (new Mercurio34)->CamposDisponibles(),
                'relacion_multiple' => $relacion_multiple,
                'trabajador' => $trabajador,
            ];
        } catch (DebugException $err) {
            $response = [
                'success' => false,
                'msj' => $err->getMessage(),
                'errors' => $err->render($request),
            ];
        }

        return response()->json($response);
    }

    public function loadParametrosView()
    {
        $procesadorComando = Comman::Api();
        $procesadorComando->runCli(
            [
                'servicio' => 'ComfacaAfilia',
                'metodo' => 'parametros_beneficiarios',
            ]
        );

        $paramsBeneficiario = new ParamsBeneficiario;
        $paramsBeneficiario->setDatosCaptura($procesadorComando->toArray());

        return [
            '_cedcon' => [],
            '_giro' => ParamsBeneficiario::getTieneGiro(),
            '_pago' => ParamsBeneficiario::getPago(),
            '_coddoc' => ParamsBeneficiario::getTiposDocumentos(),
            '_sexo' => ParamsBeneficiario::getSexos(),
            '_estciv' => ParamsBeneficiario::getEstadoCivil(),
            '_ciunac' => ParamsBeneficiario::getCiudades(),
            '_captra' => ParamsBeneficiario::getCapacidadTrabajar(),
            '_parent' => ParamsBeneficiario::getParentesco(),
            '_huerfano' => ParamsBeneficiario::getHuerfano(),
            '_tiphij' => ParamsBeneficiario::getTipoHijo(),
            '_nivedu' => ParamsBeneficiario::getNivelEducativo(),
            '_tipdis' => ParamsBeneficiario::getTipoDiscapacidad(),
            '_calendario' => ParamsBeneficiario::getCalendario(),
            '_codgir' => ParamsBeneficiario::getCodigoGiro(),
            'tipo' => '',
        ];
    }

    public function editarSolicitud(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer',
                'numdoc' => 'required|string|max:50',
                'tipdoc' => 'sometimes|string|max:5',
                'priape' => 'sometimes|string|max:50',
                'segape' => 'sometimes|nullable|string|max:50',
                'prinom' => 'sometimes|string|max:50',
                'segnom' => 'sometimes|nullable|string|max:50',
                'fecnac' => 'sometimes|date',
                'ciunac' => 'sometimes|string|max:10',
                'sexo' => 'sometimes|string|max:1',
                'parent' => 'sometimes|string|max:3',
                'huerfano' => 'sometimes|string|max:1',
                'tiphij' => 'sometimes|string|max:3',
                'nivedu' => 'sometimes|string|max:3',
                'captra' => 'sometimes|string|max:3',
                'tipdis' => 'sometimes|string|max:3',
                'calendario' => 'sometimes|string|max:3',
                'cedacu' => 'sometimes|nullable|string|max:50',
            ]);
            $id = $validated['id'];
            $numdoc = $validated['numdoc'];

            $mercurio34 = Mercurio34::where("id", $id)->where("numdoc", $numdoc)->first();
            if (! $mercurio34) {
                throw new DebugException('El beneficiario no está disponible para notificar por email', 501);
            } else {
                $mercurio07 = Mercurio07::where("documento", $mercurio34->getDocumento())->where("coddoc", $mercurio34->getCoddoc())->first();
                if (! $mercurio07) {
                    throw new DebugException('El usuario no está disponible para notificar por email', 501);
                }
                $asignarFuncionario = new AsignarFuncionario;
                $usuario = $asignarFuncionario->asignar($this->tipopc, $mercurio07->getCodciu());

                if (empty($usuario)) {
                    throw new DebugException('No se puede realizar el registro, no hay usuario disponible para la atención de la solicitud, Comuniquese con la Atencion al cliente', 505);
                }
                $data = [
                    'tipdoc' => $validated['tipdoc'] ?? null,
                    'numdoc' => $validated['numdoc'] ?? null,
                    'priape' => $validated['priape'] ?? null,
                    'segape' => $validated['segape'] ?? null,
                    'prinom' => $validated['prinom'] ?? null,
                    'segnom' => $validated['segnom'] ?? null,
                    'fecnac' => $validated['fecnac'] ?? null,
                    'ciunac' => $validated['ciunac'] ?? null,
                    'sexo' => $validated['sexo'] ?? null,
                    'parent' => $validated['parent'] ?? null,
                    'huerfano' => $validated['huerfano'] ?? null,
                    'tiphij' => $validated['tiphij'] ?? null,
                    'nivedu' => $validated['nivedu'] ?? null,
                    'captra' => $validated['captra'] ?? null,
                    'tipdis' => $validated['tipdis'] ?? null,
                    'calendario' => $validated['calendario'] ?? null,
                    'cedacu' => $validated['cedacu'] ?? null,
                ];
                $data = array_filter($data, function ($v) { return !is_null($v) && $v !== ''; });

                Mercurio34::where('id', $id)
                    ->where('numdoc', $numdoc)
                    ->update($data);


                $salida = [
                    'msj' => 'Proceso se ha completado con éxito',
                    'success' => true,
                    'data' => $mercurio34->toArray(),
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
    public function buscarEnSisuView($id = 0)
    {

        if (! $id) {
            return redirect('aprobacionben/index');
            exit;
        }

        $mercurio34 = Mercurio34::where("id", $id)->first();

        if (! $mercurio34) {
            set_flashdata('error', [
                'msj' => 'El beneficiario no se encuentra registrado.',
                'code' => 201,
            ]);

            return redirect('aprobacionben/index');
            exit;
        }

        $procesadorComando = Comman::Api();
        $procesadorComando->runCli(
            [
                'servicio' => 'ComfacaEmpresas',
                'metodo' => 'informacion_beneficiario',
                'params' => $mercurio34->numdoc,
            ]
        );
        $rqs = $procesadorComando->toArray();

        if (! $rqs['success']) {
            set_flashdata('error', [
                'msj' => 'El beneficiario no se encuentra registrado.',
                'code' => 201,
            ]);

            return redirect('aprobacionben/index');
        }

        $relaciones = [];
        if ($rqs['data']) {
            $beneficiario = $rqs['data'];
            $relaciones = $rqs['relaciones'];
        }

        return response()->json([
            'success' => true,
            'mercurio34' => $mercurio34,
            'cedtra' => $mercurio34->cedtra,
            'relaciones' => $relaciones,
            'beneficiario' => $beneficiario,
            'title' => "Beneficiario SISU - {$mercurio34->numdoc}",
        ]);
    }

    public function opcional($estado = 'P')
    {
        $collection = Mercurio34::where("estado", $estado)
            ->where("usuario", $this->user['usuario'])
            ->orderBy("fecsol", 'ASC')
            ->get();

        $beneficiarioServices = new BeneficiarioServices;
        $data = $beneficiarioServices->dataOptional($collection, $estado);

        return response()->json([
            'success' => true,
            'beneficiarios' => $data,
            'pagina_con_estado' => $estado,
        ]);
    }

    public function reaprobar(Request $request)
    {
        $this->db->begin();
        $comando = '';
        try {
            $validated = $request->validate([
                'id' => 'required|integer',
                'giro' => 'required|string|max:1',
                'codgir' => 'nullable|string|max:10',
                'nota' => 'nullable|string|max:5000',
            ]);
            $id = $validated['id'];
            $giro = $validated['giro'];
            $codgir = $validated['codgir'] ?? null;
            $nota = $validated['nota'] ?? null;
            $today = Carbon::now();

            Mercurio34::where("id", $id)->update([
                "estado" => "A",
                "fecest" => $today->format('Y-m-d H:i:s'),
            ]);

            $item = Mercurio10::whereRaw("tipopc='{$this->tipopc}' and numero='{$id}'")->max('item') + 1;

            $mercurio10 = new Mercurio10;
            $mercurio10->tipopc = $this->tipopc;
            $mercurio10->numero = $id;
            $mercurio10->item = $item;
            $mercurio10->estado = 'A';
            $mercurio10->nota = $nota;
            $mercurio10->fecsis = $today->format('Y-m-d H:i:s');
            $mercurio10->save();

            $beneficiario = Mercurio34::where("id", $id)->first();

            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
                [
                    'servicio' => 'ComfacaAfilia',
                    'metodo' => 'actualiza_beneficiario',
                    'params' => [
                        'numdoc' => $beneficiario->getNumdoc(),
                        'modelo' => [
                            'prinom' => $beneficiario->getPrinom(),
                            'segnom' => $beneficiario->getSegnom(),
                            'priape' => $beneficiario->getPriape(),
                            'giro' => $giro,
                            'codgir' => $codgir,
                        ],
                    ],
                ]
            );

            $comando = $procesadorComando->getLineaComando();

            $result = $procesadorComando->toArray();
            if (! $result) {
                throw new DebugException('Error, no hay respuesta del servidor para validación del resultado.', 1);
            } else {

                $this->db->commit();
                $response = [
                    'success' => true,
                    'msj' => 'Movimiento realizado con éxito',
                    'result' => $result,
                ];
            }
        } catch (DebugException $e) {
            $response = [
                'success' => false,
                'msj' => 'No se pudo realizar el movimiento ' . "\n" . $e->getMessage() . "\n " . $e->getLine(),
                'comando' => $comando,
                'errors' => $e->render($request),
            ];
        }
        return response()->json($response);
    }

    public function borrarFiltro(Request $request)
    {
        $this->setResponse('ajax');
        set_flashdata('filter_beneficiario', false, true);
        set_flashdata('filter_params', false, true);

        return response()->json([
            'success' => true,
            'query' => get_flashdata_item('filter_trabajador'),
            'filter' => get_flashdata_item('filter_params'),
        ]);
    }

    /**
     * infoAprobadoView function
     * datos del solicitud aprobada enn sisu
     *
     * @param [type] $id
     * @return void
     */
    public function infoAprobadoView($id)
    {
        $this->tipopc = '1';
        try {
            $mercurio34 = Mercurio34::where("id", $id)->where("estado", 'A')->first();
            if (! $mercurio34) {
                throw new DebugException('Error al buscar la beneficiario', 501);
            }

            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
                [
                    'servicio' => 'ComfacaAfilia',
                    'metodo' => 'parametros_beneficiarios',
                ]
            );

            $datos_captura = $procesadorComando->toArray();
            $paramsBeneficiario = new ParamsBeneficiario;
            $paramsBeneficiario->setDatosCaptura($datos_captura);

            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
                [
                    'servicio' => 'ComfacaEmpresas',
                    'metodo' => 'informacion_beneficiario',
                    'params' => $mercurio34->numdoc,
                ]
            );

            if ($procesadorComando->isJson() == false) {
                throw new DebugException('Error al buscar la beneficiario en Sisuweb', 501);
            }

            $out = $procesadorComando->toArray();
            $beneSisu = $out['data'];

            $beneficiario = new Mercurio34;
            $beneficiario->createAttributes($beneSisu);
            $beneficiario->setTipo('E');
            $beneficiario->setNumdoc($beneSisu['documento']);
            $html = view(
                'cajas/aprobacionben/tmp/consulta',
                [
                    'beneficiario' => $beneficiario,
                    'detTipo' => Mercurio06::where("tipo", $beneficiario->getTipo())->first()->getDetalle(),
                    '_coddoc' => ParamsBeneficiario::getTiposDocumentos(),
                    '_codciu' => ParamsBeneficiario::getCiudades(),
                    '_sexo' => ParamsBeneficiario::getSexos(),
                    '_estciv' => ParamsBeneficiario::getEstadoCivil(),
                    '_parent' => ParamsBeneficiario::getParentesco(),
                    '_captra' => ParamsBeneficiario::getCapacidadTrabajar(),
                    '_tipdis' => ParamsBeneficiario::getTipoDiscapacidad(),
                    '_nivedu' => ParamsBeneficiario::getNivelEducativo(),
                    '_giro' => ParamsBeneficiario::getTieneGiro(),
                    '_pago' => ParamsBeneficiario::getPago(),
                    '_ciunac' => ParamsBeneficiario::getCiudades(),
                    '_huerfano' => ParamsBeneficiario::getHuerfano(),
                    '_tiphij' => ParamsBeneficiario::getTipoHijo(),
                    '_calendario' => ParamsBeneficiario::getCalendario(),
                    '_codgir',
                    ParamsBeneficiario::getCodigoGiro(),
                ]
            )->render();

            $code_estados = [];
            $query = Mercurio11::all();
            foreach ($query as $row) {
                $code_estados[$row->getCodest()] = $row->getDetalle();
            }

            $this->setParamToView('code_estados', $code_estados);
            $this->setParamToView('mercurio34', $beneficiario);
            $this->setParamToView('consulta_trabajador', $html);
            $this->setParamToView('hide_header', true);
            $this->setParamToView('idModel', $id);
            $this->setParamToView('cedtra', $mercurio34->cedtra);
            $this->setParamToView('title', "Beneficiario Aprobado {$beneficiario->getNumdoc()}");
        } catch (DebugException $err) {
            set_flashdata('error', [
                'msj' => $err->getMessage(),
                'code' => 201,
            ]);
            return redirect('aprobacionben/index');
        }
    }

    /**
     * deshacerAprobado function
     * metodo para deshacer una afilación, dado que se presente algun error por parte de los analistas encargados
     *
     * @param [type] $id
     * @return void
     */
    public function deshacer(Request $request)
    {
        $comando = '';
        try {
            $beneficiarioServices = new BeneficiarioServices();
            $notifyEmailServices = new NotifyEmailServices;

            $validated = $request->validate([
                'action' => 'required|in:D,R,I',
                'codest' => 'required|string|max:10',
                'send_email' => 'nullable|in:S,N',
                'nota' => 'nullable|string|max:5000',
                'id' => 'required|integer',
            ]);

            $action = $validated['action'];
            $codest = $validated['codest'];
            $sendEmail = $validated['send_email'] ?? 'N';
            $nota = $validated['nota'] ?? null;
            $id = $validated['id'];

            $mercurio34 = Mercurio34::where("id", $id)->where("estado", "A")->first();
            if (! $mercurio34) {
                throw new DebugException('Los datos del beneficiario no son validos para procesar.', 501);
            }

            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
                [
                    'servicio' => 'ComfacaEmpresas',
                    'metodo' => 'informacion_beneficiario',
                    'params' => $mercurio34->numdoc,
                ]
            );

            if ($procesadorComando->isJson() == false) {
                throw new DebugException('Error al buscar al beneficiario en Sisuweb', 501);
            }

            $out = $procesadorComando->toArray();
            $beneficiarioSisu = $out['data'];

            $procesadorComando->runCli(
                [
                    'servicio' => 'ComfacaAfilia',
                    'metodo' => 'deshacer_aprobacion_beneficiario',
                    'params' => [
                        'cedtra' => $mercurio34->cedtra,
                        'numdoc' => $mercurio34->numdoc,
                        'tipo_documento' => $mercurio34->tipdoc,
                        'nota' => $nota,
                    ],
                ]
            );

            $comando = $procesadorComando->getLineaComando();
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
                    'msj' => 'No se realizo ninguna acción, el estado del beneficiario no es valido para realizar la acción requerida.',
                    'data' => $beneficiarioSisu,
                ];
            } else {
                // procesar
                if ($action == 'D') {
                    $campos_corregir = '';
                    $beneficiarioServices->devolver($mercurio34, $nota, $codest, $campos_corregir);
                    if ($sendEmail == 'S') {
                        $notifyEmailServices->emailDevolver($mercurio34, $beneficiarioServices->msjDevolver($mercurio34, $nota));
                    }
                }

                if ($action == 'R') {
                    $beneficiarioServices->rechazar($mercurio34, $nota, $codest);
                    if ($sendEmail == 'S') {
                        $notifyEmailServices->emailRechazar($mercurio34, $beneficiarioServices->msjRechazar($mercurio34, $nota));
                    }
                }

                if ($action == 'I') {
                    $mercurio34->estado = 'I';
                    $mercurio34->fecest = Carbon::now()->format('Y-m-d');
                    $mercurio34->save();
                }

                $salida = [
                    'data' => $beneficiarioSisu,
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
}
