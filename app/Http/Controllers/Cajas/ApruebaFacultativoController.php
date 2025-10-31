<?php

namespace App\Http\Controllers\Cajas;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Library\Collections\ParamsFacultativo;
use App\Library\Collections\ParamsPensionado;
use App\Models\Adapter\DbBase;
use App\Models\Gener42;
use App\Models\Mercurio01;
use App\Models\Mercurio06;
use App\Models\Mercurio11;
use App\Models\Mercurio36;
use App\Models\Mercurio37;
use App\Models\Mercurio41;
use App\Services\Reports\CsvReportStrategy;
use App\Services\Reports\ExcelReportStrategy;
use App\Services\Reports\ReportGenerator;
use App\Services\Aprueba\ApruebaSolicitud;
use App\Services\CajaServices\FacultativoServices;
use App\Services\Srequest;
use App\Services\Utils\Comman;
use App\Services\Utils\NotifyEmailServices;
use App\Services\Utils\Pagination;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class ApruebaFacultativoController extends ApplicationController
{
    protected $tipopc = 10;

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
     * facultativoServices variable
     *
     * @var FacultativoServices
     */
    protected $facultativoServices;

    /**
     * apruebaSolicitud variable
     *
     * @var ApruebaSolicitud
     */
    protected $apruebaSolicitud;

    public function __construct()
    {
        $this->db = DbBase::rawConnect();
        $this->user = session('user');
        $this->tipfun = session('tipfun');
    }

    /**
     * aplicarFiltro function
     *
     * @changed [2023-12-00]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     *
     * @return void
     */
    public function aplicarFiltro(Request $request, $estado = 'P')
    {
        $this->setResponse('ajax');
        $cantidad_pagina = ($request->input('numero')) ? $request->input('numero') : 10;
        $usuario = $this->user['usuario'];
        $query_str = ($estado == 'T') ? " estado='{$estado}'" : "usuario='{$usuario}' and estado='{$estado}'";
        $pagination = new Pagination(
            new Srequest(
                [
                    'cantidadPaginas' => $cantidad_pagina,
                    'query' => $query_str,
                    'estado' => $estado,
                ]
            )
        );

        $query = $pagination->filter(
            $request->input('campo'),
            $request->input('condi'),
            $request->input('value')
        );

        set_flashdata('filter_facultativo', $query, true);

        set_flashdata('filter_params', $pagination->filters, true);

        $response = $pagination->render(
            new FacultativoServices
        );

        return $this->renderObject($response, false);
    }

    /**
     * export function
     * Descargar reporte de facultativos según filtros del aplicativo
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

            // Columnas de Mercurio36
            $columns = [
                'Cédula' => 'cedtra',
                'Nombres' => fn($r) => trim(($r->prinom ?? '') . ' ' . ($r->segnom ?? '')),
                'Apellidos' => fn($r) => trim(($r->priape ?? '') . ' ' . ($r->segape ?? '')),
                'Nit Empresa' => 'cedtra',
                'Estado' => 'estado',
                'Fecha Solicitud' => 'fecsol',
                'Usuario' => 'usuario',
            ];

            $gen = (new ReportGenerator($strategy))
                ->for(Mercurio36::query())
                ->columns($columns)
                ->filename('mercurio36_' . now()->format('Ymd_His') . '.' . $ext)
                ->filter(function ($q) use ($filtro) {
                    if (is_string($filtro) && trim($filtro) !== '') {
                        $q->whereRaw($filtro);
                    }
                });

            return $gen->download();
        } catch (\Throwable $th) {
            return $this->renderObject([
                'success' => false,
                'message' => $th->getMessage(),
            ], false);
        }
    }

    public function changeCantidadPagina(Request $request, $estado = 'P')
    {
        return $this->buscar($request, $estado);
    }

    public function index()
    {
        $this->setParamToView('hide_header', true);
        $campo_field = [
            'cedtra' => 'Cedula',
            'priape' => 'Primer Apellido',
            'segape' => 'Segundo Apellido',
            'prinom' => 'Primer Nombre',
            'segnom' => 'Segundo Nombre',
        ];

        $params = $this->loadParametrosView();

        return view('cajas.aprobacionfac.index', [
            ...$params,
            'campo_filtro' => $campo_field,
            'filters' => get_flashdata_item('filter_params'),
            'title' => 'Aprueba Facultativo',
            'mercurio11' => Mercurio11::get(),
        ]);
    }

    public function buscar(Request $request, string $estado = 'P')
    {
        $this->setResponse('ajax');
        $pagina = $request->input('pagina', 1);
        $cantidad_pagina = $request->input('numero', 10);
        $usuario = $this->user['usuario'];
        $query_str = ($estado == 'T') ? " estado='{$estado}'" : "usuario='{$usuario}' and estado='{$estado}'";

        $pagination = new Pagination(
            new Srequest([
                'cantidadPaginas' => $cantidad_pagina,
                'pagina' => $pagina,
                'query' => $query_str,
                'estado' => $estado,
            ])
        );

        if (
            get_flashdata_item('filter_facultativo') != false
        ) {
            $query = $this->pagination->persistencia(get_flashdata_item('filter_params'));
        } else {
            $query = $pagination->filter(
                $request->input('campo'),
                $request->input('condi'),
                $request->input('value')
            );
        }

        set_flashdata('filter_facultativo', $query, true);
        set_flashdata('filter_params', $pagination->filters, true);

        $response = $pagination->render(
            new FacultativoServices
        );

        return $this->renderObject($response, false);
    }

    /**
     * infor function
     *
     * @return void
     */
    public function infor(Request $request)
    {
        try {
            $id = $request->input('id');
            if (! $id) {
                throw new DebugException('Error se requiere del id independiente', 501);
            }
            $facultativoServices = new FacultativoServices;
            $mercurio36 = Mercurio36::where("id", $id)->first();
            $ps = Comman::Api();
            $ps->runCli(
                [
                    'servicio' => 'ComfacaAfilia',
                    'metodo' => 'parametros_facultativo',
                ]
            );

            $datos_captura = $ps->toArray();
            $paramsfac = new ParamsFacultativo;
            $paramsfac->setDatosCaptura($datos_captura);

            $htmlEmpresa = view('cajas/aprobacionfac/tmp/consulta', [
                'mercurio36' => $mercurio36,
                'mercurio01' => Mercurio01::first(),
                'det_tipo' => Mercurio06::where("tipo", $mercurio36->tipo)->first()->detalle,
                '_coddoc' => ParamsFacultativo::getTipoDocumentos(),
                '_calemp' => ParamsFacultativo::getCalidadEmpresa(),
                '_codciu' => ParamsFacultativo::getCiudades(),
                '_codzon' => ParamsFacultativo::getZonas(),
                '_codact' => ParamsFacultativo::getActividades(),
                '_tipsoc' => ParamsFacultativo::getTipoSociedades(),
                '_tipdur' => ParamsFacultativo::getTipoDuracion(),
                '_codind' => ParamsFacultativo::getCodigoIndice(),
                '_todmes' => ParamsFacultativo::getPagaMes(),
                '_forpre' => ParamsFacultativo::getFormaPresentacion(),
                '_tippag' => ParamsFacultativo::getTipoPago(),
                '_tipcue' => ParamsFacultativo::getTipoCuenta(),
                '_giro' => ParamsFacultativo::getGiro(),
                '_codgir' => ParamsFacultativo::getCodigoGiro(),
            ])->render();

            $ps = Comman::Api();
            $ps->runCli(
                [
                    'servicio' => 'ComfacaEmpresas',
                    'metodo' => 'informacion_empresa',
                    'params' => [
                        'nit' => $mercurio36->cedtra,
                    ],
                ]
            );
            $out = $ps->toArray();

            if ($out['success']) {
                $empresa_sisuweb = $out['data'];
            } else {
                $empresa_sisuweb = false;
            }
            $response = [
                'success' => true,
                'data' => $mercurio36->toArray(),
                'mercurio11' => Mercurio11::all(),
                'consulta_empresa' => $htmlEmpresa,
                'adjuntos' => $facultativoServices->adjuntos($mercurio36),
                'seguimiento' => $facultativoServices->seguimiento($mercurio36),
                'campos_disponibles' => $mercurio36->CamposDisponibles(),
                'empresa_sisuweb' => $empresa_sisuweb,
            ];
        } catch (Exception $err) {
            $response = [
                'success' => false,
                'msj' => $err->getMessage(),
            ];
        }

        return $this->renderObject($response, false);
    }

    /**
     * aprueba function
     *
     * @changed [2023-12-00]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     *
     * @return void
     */
    public function aprueba(Request $request)
    {
        $this->setResponse('ajax');
        $user = session()->get('user');
        $debuginfo = [];

        $acceso = (new Gener42)->count("permiso='62' AND usuario='{$user['usuario']}'");
        if ($acceso == 0) {
            return $this->renderObject(['success' => false, 'msj' => 'El usuario no dispone de permisos de aprobación'], false);
        }
        $apruebaSolicitud = new ApruebaSolicitud;
        $this->db->begin();
        try {
            try {
                $postData = $_POST;
                $idSolicitud = $request->input('id', 'addslaches', 'alpha', 'extraspaces', 'striptags');
                $calemp = 'F';
                $solicitud = $apruebaSolicitud->main(
                    $calemp,
                    $idSolicitud,
                    $postData
                );

                $this->db->commit();
                $solicitud->enviarMail($request->input('actapr'), $request->input('feccap'));
                $salida = [
                    'success' => true,
                    'msj' => 'El registro se completo con éxito',
                ];
            } catch (DebugException $err) {

                $this->db->rollback();
                $salida = [
                    'success' => false,
                    'msj' => $err->getMessage(),
                ];
            }
        } catch (DebugException $e) {
            $salida = [
                'success' => false,
                'msj' => $e->getMessage(),
            ];
        }
        if ($debuginfo) {
            $salida['info'] = $debuginfo;
        }

        return $this->renderObject($salida, false);
    }

    public function borrarFiltro()
    {
        $this->setResponse('ajax');
        set_flashdata('filter_facultativo', false, true);
        set_flashdata('filter_params', false, true);

        return $this->renderObject([
            'success' => true,
            'query' => get_flashdata_item('filter_facultativo'),
            'filter' => get_flashdata_item('filter_params'),
        ]);
    }

    public function editarView($id)
    {
        if (! $id) {
            return redirect('aprobacionfac/index');
            exit;
        }
        $facultativoServices = new FacultativoServices;
        $this->setParamToView('hide_header', true);
        $mercurio36 = (new Mercurio36)->findFirst("id='{$id}'");
        $this->setParamToView('mercurio36', $mercurio36);
        $this->setParamToView('tipopc', 2);
        $this->setParamToView('seguimiento', $facultativoServices->seguimiento($mercurio36));

        $mercurio01 = Mercurio01::first();
        $this->setParamToView('mercurio01', $mercurio01);
        $mercurio37 = Mercurio37::where(" tipopc=2 AND numero='{$mercurio36->getId()}'")->first();
        $this->setParamToView('mercurio37', $mercurio37);
        $this->setParamToView('idModel', $id);
        $this->setParamToView('det_tipo', Mercurio06::where("tipo = '{$mercurio36->getTipo()}'")->first()->getDetalle());

        $procesadorComando = Comman::Api();
        $procesadorComando->runCli(
            [
                'servicio' => 'ComfacaAfilia',
                'metodo' => 'parametros_pensionado',
            ],
            false
        );

        $paramsPensionado = new ParamsPensionado;
        $paramsPensionado->setDatosCaptura($procesadorComando->toArray());

        $this->loadParametrosView();
        $facultativoServices->loadDisplay($mercurio36);
        $this->setParamToView('title', 'Editar Ficha Pensionado ' . $mercurio36->getCedtra());
    }

    public function editaEmpresa(Request $request)
    {
        $this->setResponse('ajax');
        $nit = $request->input('nit');
        $id = $request->input('id');
        try {
            $mercurio36 = Mercurio36::where("nit='{$nit}' AND id='{$id}'")->first();
            if (! $mercurio36) {
                throw new DebugException('La empresa no está disponible para notificar por email', 501);
            } else {
                $tipsoc = $request->input('tipsoc');
                if (strlen($tipsoc) == 1) {
                    $tipsoc = str_pad($tipsoc, 2, '0', STR_PAD_LEFT);
                }
                $data = [
                    'razsoc' => $request->input('razsoc'),
                    'codact' => $request->input('codact'),
                    'digver' => $request->input('digver'),
                    'calemp' => $request->input('calemp'),
                    'cedrep' => $request->input('cedrep'),
                    'repleg' => $request->input('repleg'),
                    'direccion' => $request->input('direccion'),
                    'codciu' => $request->input('codciu'),
                    'codzon' => $request->input('codzon'),
                    'telefono' => $request->input('telefono'),
                    'celular' => $request->input('celular'),
                    'fax' => $request->input('fax'),
                    'email' => $request->input('email'),
                    'sigla' => $request->input('sigla'),
                    'fecini' => $request->input('fecini'),
                    'tottra' => $request->input('tottra'),
                    'valnom' => $request->input('valnom'),
                    'tipsoc' => $tipsoc,
                    'dirpri' => $request->input('dirpri'),
                    'ciupri' => $request->input('ciupri'),
                    'celpri' => $request->input('celpri'),
                    'tipemp' => $request->input('tipemp'),
                    'emailpri' => $request->input('emailpri'),
                    'tipper' => $request->input('tipper'),
                    'matmer' => $request->input('matmer'),
                    'coddocrepleg' => (! $request->input('coddocrepleg')) ? '1' : $request->input('coddocrepleg'),
                    'prinom' => ($request->input('tipper') == 'N') ? $request->input('prinom') : $request->input('prinomrepleg'),
                    'priape' => ($request->input('tipper') == 'N') ? $request->input('priape') : $request->input('priaperepleg'),
                    'segnom' => ($request->input('tipper') == 'N') ? $request->input('segnom') : $request->input('segnomrepleg'),
                    'segape' => ($request->input('tipper') == 'N') ? $request->input('segape') : $request->input('segaperepleg'),
                    'prinomrepleg' => ($request->input('tipper') == 'J') ? $request->input('prinomrepleg') : '',
                    'priaperepleg' => ($request->input('tipper') == 'J') ? $request->input('priaperepleg') : '',
                    'segnomrepleg' => ($request->input('tipper') == 'J') ? $request->input('segnomrepleg') : '',
                    'segaperepleg' => ($request->input('tipper') == 'J') ? $request->input('segaperepleg') : '',
                    'telpri' => $request->input('telpri'),
                ];
                $setters = '';
                foreach ($data as $ai => $row) {
                    $setters .= " $ai='{$row}',";
                }
                $setters = trim($setters, ',');
                Mercurio36::whereRaw("id='{$id}' AND nit='{$nit}'")->update($setters);
                $salida = [
                    'msj' => 'Proceso se ha completado con éxito',
                    'success' => true,
                ];
            }
        } catch (Exception $err) {
            $salida = [
                'success' => false,
                'msj' => $err->getMessage(),
            ];
        }

        return $this->renderObject($salida, false);
    }

    public function loadParametrosView($datos_captura = '')
    {
        $ps = Comman::Api();
        if ($datos_captura == '') {
            $ps->runCli(
                [
                    'servicio' => 'ComfacaAfilia',
                    'metodo' => 'parametros_facultativo',
                ]
            );

            $datos_captura = $ps->toArray();
            $paramsEmpresa = new ParamsFacultativo;
            $paramsEmpresa->setDatosCaptura($datos_captura);
        }

        $_coddocrepleg = [];
        foreach (ParamsFacultativo::getCodruaDocumentos() as $ai => $valor) {
            if ($valor == 'TI' || $valor == 'RC') {
                continue;
            }
            $_coddocrepleg[$ai] = $valor;
        }

        return [
            '_tipdur' => ParamsFacultativo::getTipoDuracion(),
            '_codind' => ParamsFacultativo::getCodigoIndice(),
            '_todmes' => ParamsFacultativo::getPagaMes(),
            '_forpre' => ParamsFacultativo::getFormaPresentacion(),
            '_tipsoc' => ParamsFacultativo::getTipoSociedades(),
            '_tipemp' => ParamsFacultativo::getTipoEmpresa(),
            '_tipapo' => ParamsFacultativo::getTipoAportante(),
            '_tipper' => ParamsFacultativo::getTipoPersona(),
            '_codzon' => ParamsFacultativo::getZonas(),
            '_calemp' => ParamsFacultativo::getCalidadEmpresa(),
            '_codciu' => ParamsFacultativo::getCiudades(),
            '_codact' => ParamsFacultativo::getActividades(),
            '_coddoc' => ParamsFacultativo::getTipoDocumentos(),
            '_tippag' => ParamsFacultativo::getTipoPago(),
            '_bancos' => ParamsFacultativo::getBancos(),
            '_tipcue' => ParamsFacultativo::getTipoCuenta(),
            '_giro' => ParamsFacultativo::getGiro(),
            '_codgir' => ParamsFacultativo::getCodigoGiro(),
            '_coddocrepleg' => $_coddocrepleg,
        ];
    }

    public function rechazar(Request $request)
    {
        $this->setResponse('ajax');
        $notifyEmailServices = new NotifyEmailServices;
        $this->facultativoServices = new FacultativoServices;
        try {
            $id = $request->input('id', 'addslaches', 'alpha', 'extraspaces', 'striptags');
            $nota = sanetizar($request->input('nota'));
            $codest = $request->input('codest', 'addslaches', 'alpha', 'extraspaces', 'striptags');

            $mercurio41 = Mercurio41::whereRaw(" id='{$id}'")->first();

            if ($mercurio41->getEstado() == 'X') {
                throw new DebugException('El registro ya se encuentra rechazado, no se requiere de repetir la acción.', 201);
            }

            $this->facultativoServices->rechazar($mercurio41, $nota, $codest);

            $notifyEmailServices->emailRechazar($mercurio41, $this->facultativoServices->msjRechazar($mercurio41, $nota));

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

    public function devolver(Request $request)
    {
        $this->setResponse('ajax');
        $this->facultativoServices = $this->services->get('facultativoServices');
        $notifyEmailServices = new NotifyEmailServices;
        try {
            $id = $request->input('id', 'addslaches', 'alpha', 'extraspaces', 'striptags');
            $codest = $request->input('codest', 'addslaches', 'alpha', 'extraspaces', 'striptags');
            $nota = sanetizar($request->input('nota'));
            $array_corregir = $request->input('campos_corregir');
            $campos_corregir = implode(';', $array_corregir);

            $mercurio41 = Mercurio41::whereRaw("id='{$id}'")->first();
            if ($mercurio41->getEstado() == 'D') {
                throw new DebugException('El registro ya se encuentra devuelto, no se requiere de repetir la acción.', 201);
            }

            $this->facultativoServices->devolver($mercurio41, $nota, $codest, $campos_corregir);

            $notifyEmailServices->emailDevolver(
                $mercurio41,
                $this->facultativoServices->msjDevolver($mercurio41, $nota)
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
}
