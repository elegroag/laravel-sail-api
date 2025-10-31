<?php

namespace App\Http\Controllers\Cajas;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Library\Collections\ParamsIndependiente;
use App\Models\Adapter\DbBase;
use App\Models\Gener42;
use App\Models\Mercurio01;
use App\Models\Mercurio02;
use App\Models\Mercurio06;
use App\Models\Mercurio07;
use App\Models\Mercurio11;
use App\Models\Mercurio37;
use App\Models\Mercurio41;
use App\Services\Aprueba\ApruebaSolicitud;
use App\Services\CajaServices\IndependienteServices;
use App\Services\Reports\CsvReportStrategy;
use App\Services\Reports\ExcelReportStrategy;
use App\Services\Reports\ReportGenerator;
use App\Services\Srequest;
use App\Services\Utils\CalculatorDias;
use App\Services\Utils\Comman;
use App\Services\Utils\GeneralService;
use App\Services\Utils\NotifyEmailServices;
use App\Services\Utils\Pagination;
use Illuminate\Http\Request;

class ApruebaIndependienteController extends ApplicationController
{
    protected $tipopc = 13;

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
     * independienteServices variable
     *
     * @var IndependienteServices
     */
    protected $independienteServices;

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

    public function aplicarFiltro(Request $request, $estado = 'P')
    {
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

        set_flashdata('filter_independiente', $query, true);
        set_flashdata('filter_params', $pagination->filters, true);

        $response = $pagination->render(new IndependienteServices);

        return $this->renderObject($response, false);
    }

    /**
     * export function
     * Descargar reporte de independientes según filtros del aplicativo
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

            // Columnas de Mercurio41
            $columns = [
                'Documento' => 'documento',
                'Nit' => 'cedtra',
                'Razón Social' => 'razsoc',
                'Estado' => 'estado',
                'Fecha Solicitud' => 'fecsol',
                'Usuario' => 'usuario',
            ];

            $gen = (new ReportGenerator($strategy))
                ->for(Mercurio41::query())
                ->columns($columns)
                ->filename('mercurio41_' . now()->format('Ymd_His') . '.' . $ext)
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
        $this->buscar($request, $estado);
    }

    public function index()
    {
        $this->setParamToView('hide_header', true);
        $campo_field = [
            'cedtra' => 'Cedula',
            'prinom' => 'Nombre',
            'priape' => 'Apellido',
            'fecini' => 'Fecha inicio',
            'fecsol' => 'Fecha solicitud',
        ];

        $params = $this->loadParametrosView();

        return view('cajas.aprobaindepen.index', [
            ...$params,
            'campo_filtro' => $campo_field,
            'filters' => get_flashdata_item('filter_params'),
            'title' => 'Aprueba Independientes',
            'mercurio11' => Mercurio11::get(),
        ]);
    }

    public function opcional($estado = 'P')
    {
        $this->setParamToView('hide_header', true);
        $campo_field = [
            'nit' => 'Nit',
            'razsoc' => 'Razon Social',
        ];
        $help = 'Esta opcion permite manejar los ';
        $this->setParamToView('help', $help);
        $this->setParamToView('title', 'Aprobacion Empresa');
        $mercurio41 = Mercurio41::whereRaw("estado='{$estado}' AND usuario=" . $this->user['usuario'])->orderBy('fecini', 'ASC')->get();
        $empresas = [];
        foreach ($mercurio41 as $ai => $mercurio) {
            $background = '';
            $dias_vencidos = CalculatorDias::calcular($this->tipopc, $mercurio->getId(), $mercurio->getFecsol());

            if ($estado == 'P') {
                if ($dias_vencidos == 3) {
                    $background = '#f1f1ad';
                } elseif ($dias_vencidos > 3) {
                    $background = '#f5b2b2';
                }
            }

            if ($mercurio->getEstado() == 'A') {
                $url = env('APP_URL') . 'Cajas/aprobaindepen/infoAprobadoView/' . $mercurio->getId();
            } else {
                $url = env('APP_URL') . 'Cajas/aprobaindepen/info_empresa/' . $mercurio->getId();
            }

            $sat = 'NORMAL';
            $empresas[] = [
                'estado' => $mercurio->getEstadoDetalle(),
                'recepcion' => $sat,
                'nit' => $mercurio->getCedtra(),
                'background' => $background,
                'razsoc' => $mercurio->getRazsoc(),
                'dias_vencidos' => $dias_vencidos,
                'id' => $mercurio->getId(),
                'url' => $url,
            ];
        }

        $this->setParamToView('empresas', $empresas);
        $this->setParamToView('buttons', ['F']);
        $this->setParamToView('campo_filtro', $campo_field);
        $this->setParamToView('pagina_con_estado', $estado);
    }

    public function buscar(Request $request, $estado = 'P')
    {
        $pagina = ($request->input('pagina')) ? $request->input('pagina') : 1;
        $cantidad_pagina = ($request->input('numero')) ? $request->input('numero') : 10;
        $usuario = $this->user['usuario'];
        $query_str = ($estado == 'T') ? " estado='{$estado}'" : "usuario='{$usuario}' and estado='{$estado}'";

        $pagination = new Pagination(
            new Srequest(
                [
                    'cantidadPaginas' => $cantidad_pagina,
                    'pagina' => $pagina,
                    'query' => $query_str,
                    'estado' => $estado,
                ]
            )
        );

        if (
            get_flashdata_item('filter_independiente') != false
        ) {
            $query = $pagination->persistencia(get_flashdata_item('filter_params'));
        } else {
            $query = $pagination->filter(
                $request->input('campo'),
                $request->input('condi'),
                $request->input('value')
            );
        }

        set_flashdata('filter_independiente', $query, true);
        set_flashdata('filter_params', $pagination->filters, true);

        $response = $pagination->render(
            new IndependienteServices
        );

        return $this->renderObject($response, false);
    }

    /**
     * devolver function
     *
     * @return void
     */
    public function devolver(Request $request)
    {
        $independienteServices = new IndependienteServices;
        $notifyEmailServices = new NotifyEmailServices;
        try {
            $id = $request->input('id', 'addslaches', 'alpha', 'extraspaces', 'striptags');
            $codest = $request->input('codest', 'addslaches', 'alpha', 'extraspaces', 'striptags');
            $nota = $request->input('nota');
            $array_corregir = $request->input('campos_corregir');
            $campos_corregir = implode(';', $array_corregir);

            $mercurio41 = (new Mercurio41)->findFirst("id='{$id}'");
            if ($mercurio41->getEstado() == 'D') {
                throw new DebugException('El registro ya se encuentra devuelto, no se requiere de repetir la acción.', 201);
            }

            $independienteServices->devolver($mercurio41, $nota, $codest, $campos_corregir);

            $notifyEmailServices->emailDevolver(
                $mercurio41,
                $independienteServices->msjDevolver($mercurio41, $nota)
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
     * rechazarion
     *
     * @return void
     */
    public function rechazar(Request $request)
    {
        $this->setResponse('ajax');
        $request = request();
        $notifyEmailServices = new NotifyEmailServices;
        $indeServices = new IndependienteServices;
        try {
            $id = $request->input('id', 'addslaches', 'alpha', 'extraspaces', 'striptags');
            $nota = $request->input('nota');
            $codest = $request->input('codest', 'addslaches', 'alpha', 'extraspaces', 'striptags');

            $mercurio41 = (new Mercurio41)->findFirst(" id='{$id}'");

            if ($mercurio41->getEstado() == 'X') {
                throw new DebugException('El registro ya se encuentra rechazado, no se requiere de repetir la acción.', 201);
            }

            $indeServices->rechazar($mercurio41, $nota, $codest);

            $notifyEmailServices->emailRechazar($mercurio41, $indeServices->msjRechazar($mercurio41, $nota));

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
        /* $flash_mensaje = SESSION::getData("flash_mensaje");
        SESSION::setData("flash_mensaje", null);
        $this->setParamToView("flash_mensaje", $flash_mensaje);
        $this->setParamToView("title", "Procesar Notificación Pendiente"); */
    }

    /**
     * rezagoCorreo function
     * metodo ajax
     * autor edwin andres legro agudelo
     * fecha 24-08-2021
     *
     * @return void
     */
    public function rezagoCorreo()
    {
        $this->setResponse('view');
        $request = request();
        $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        $nit = $request->input('nit');
        $anexo_final = $request->input('anexo_final');
        $anexo_inicial = $request->input('anexo_inicial');
        $feccap = $request->input('feccap');
        $feccap = new \DateTime($feccap);

        try {
            $mercurio41 = Mercurio41::whereRaw("nit='{$nit}' AND estado='A'")->first();
            if (! $mercurio41) {
                throw new DebugException('Error la empresa no es valida para envio de correo.', 501);
            }
            $consultasOldServices = new GeneralService;
            $servicio = $consultasOldServices->webService('datosEmpresa', $_POST);
            if ($servicio['flag'] == false) {
                throw new DebugException('Error al buscar la empresa en SISUWEB.', 502);
            }
            if (! $servicio['data']) {
                throw new DebugException('Los datos de la empresa no está disponible en SISUWEB.', 503);
            }

            $asunto = 'Afiliacion de la empresa realizada con Exito. Nit: ' . $mercurio41->getCedtra();
            $mercurio07 = Mercurio07::whereRaw("tipo='{$mercurio41->getTipo()}' and coddoc='{$mercurio41->getCoddoc()}' and documento='{$mercurio41->getDocumento()}'")->first();
            if (! $mercurio07) {
                throw new DebugException('Error no hay usuario empresa para el servicio de autogestión de comfaca en línea.', 504);
            }
            $mercurio07->setTipo('E');
            $mercurio07->save();
            $mercurio01 = Mercurio01::first();
            $mercurio02 = Mercurio02::first();
            $_email = trim($mercurio01->getEmail());
            $_clave = trim($mercurio01->getClave());

            // Prueba
            // $_email = "soporte_sistemas@comfaca.com";
            // $_clave = "";

            $mensaje = '';
            ob_start();
            $this->setParamToView('rutaImg', 'https://comfacaenlinea.com.co/Mercurio/public/img/Mercurio/logob.png');
            $this->setParamToView('mercurio41', $mercurio41);
            $this->setParamToView('actapr', $request->input('actapr'));
            $this->setParamToView('dia', $feccap->format('d'));
            $this->setParamToView('mes', $meses[intval($feccap->format('m') - 1)]);
            $this->setParamToView('anno', $feccap->format('Y'));
            $this->setParamToView('ruta_firma', 'https://comfacaenlinea.com.co/Mercurio/public/img/Mercurio/firma_jefe_yenny.jpg');
            $this->setParamToView('mercurio02', $mercurio02);
            $this->setParamToView('anexo_final', $anexo_final);
            $this->setParamToView('anexo_inicial', $anexo_inicial);
            /* echo View::renderView("aprobaindepen/mail/aprobar");
            $mensaje = ob_get_contents();
            ob_end_clean();

            Core::importFromLibrary("Swift", "Swift.php");
            Core::importFromLibrary("Swift", "Swift/Connection/SMTP.php");
            $smtp = new Swift_Connection_SMTP(
                "smtp.gmail.com",
                Swift_Connection_SMTP::PORT_SECURE,
                Swift_Connection_SMTP::ENC_TLS
            );

            $smtp->setUsername($_email);
            $smtp->setPassword($_clave);
            $smsj = new Swift_Message();
            $smsj->setSubject($asunto);
            $smsj->setContentType("text/html");
            $smsj->setBody($mensaje);
            $swift = new Swift($smtp);
            $recip = new Swift_RecipientList();

            $email = $mercurio07->getEmail();
            $nombre = $mercurio07->getNombre();

            $recip->addTo($email, $nombre);
            $swift->send($smsj, $recip, new Swift_Address($_email));
            SESSION::setData("flash_mensaje", "El envío se ha completado a la dirección de email: " . $mercurio07->getEmail() . " nombre: " . $mercurio07->getNombre());

        Router::redirectToApplication('Cajas/aprobaindepen/pendiente_email'); */
        } catch (\Exception $err) {
            // SESSION::setData("flash_mensaje", $err->getMessage());
        }
    }

    /**
     * empresa_search function
     * metodo ajax
     * autor edwin andres legro agudelo
     * fecha 24-08-2021
     *
     * @return void
     */
    public function empresaSearch()
    {
        $this->setResponse('ajax');
        $request = request();
        $nit = $request->input('nit');
        try {
            $mercurio41 = Mercurio41::whereRaw("nit='{$nit}' AND estado='A'")->first();
            if (! $mercurio41) {
                throw new DebugException('La empresa no está disponible para notificar por email', 501);
            } else {
                $data07 = Mercurio07::whereRaw("documento='{$mercurio41->getDocumento()}'")->get();
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
                    'mercurio41' => [
                        'id' => $mercurio41->getId(),
                        'nit' => $mercurio41->getCedtra(),
                        'tipdoc' => $mercurio41->getTipdoc(),
                        'razsoc' => $mercurio41->getRazsoc(),
                        'email' => $mercurio41->getEmail(),
                        'estado' => $mercurio41->getEstado(),
                        'fecest' => $mercurio41->getFecest(),
                        'fecini' => $mercurio41->getFecini(),
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
     * infor function
     * mostrar la ficha de afiliación de la empresa
     *
     * @return void
     */
    public function infor(Request $request)
    {
        try {
            $independienteServices = new IndependienteServices;
            $id = $request->input('id');
            if (! $id) {
                throw new DebugException('Error se requiere del id independiente', 501);
            }

            $mercurio41 = Mercurio41::where("id", $id)->first();
            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
                [
                    'servicio' => 'ComfacaAfilia',
                    'metodo' => 'parametros_independiente',
                ]
            );

            $datos_captura = $procesadorComando->toArray();
            $paramsIndependiente = new ParamsIndependiente;
            $paramsIndependiente->setDatosCaptura($datos_captura);

            $htmlEmpresa = view('cajas/aprobaindepen/tmp/consulta', [
                'mercurio41' => $mercurio41,
                'mercurio01' => Mercurio01::first(),
                'det_tipo' => Mercurio06::where("tipo", $mercurio41->tipo)->first()->getDetalle(),
                '_coddoc' => ParamsIndependiente::getTipoDocumentos(),
                '_calemp' => ParamsIndependiente::getCalidadEmpresa(),
                '_codciu' => ParamsIndependiente::getCiudades(),
                '_codzon' => ParamsIndependiente::getZonas(),
                '_codact' => ParamsIndependiente::getActividades(),
                '_tipsoc' => ParamsIndependiente::getTipoSociedades(),
                '_tipdoc' => ParamsIndependiente::getTipoDocumentos(),
                '_cargos' => ParamsIndependiente::getOcupaciones(),
                '_sexos' => ParamsIndependiente::getSexos(),
                '_estciv' => ParamsIndependiente::getEstadoCivil(),
                '_tipdis' => ParamsIndependiente::getTipoDiscapacidad(),
                '_nivedu' => ParamsIndependiente::getNivelEducativo(),
                '_tipafi' => ParamsIndependiente::getTipoAfiliado(),
            ])->render();

            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
                [
                    'servicio' => 'ComfacaEmpresas',
                    'metodo' => 'informacion_empresa',
                    'params' => [
                        'nit' => $mercurio41->cedtra,
                    ],
                ]
            );
            $out = $procesadorComando->toArray();

            if ($out['success']) {
                $this->setParamToView('empresa_sisuweb', $out['data']);
            }
            $response = [
                'success' => true,
                'data' => $mercurio41->toArray(),
                'mercurio11' => Mercurio11::all(),
                'consulta_empresa' => $htmlEmpresa,
                'adjuntos' => $independienteServices->adjuntos($mercurio41),
                'seguimiento' => $independienteServices->seguimiento($mercurio41),
                'campos_disponibles' => $mercurio41->CamposDisponibles(),
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
                'metodo' => 'parametros_independiente',
            ]
        );

        $paramsIndependiente = new ParamsIndependiente;
        $paramsIndependiente->setDatosCaptura($procesadorComando->toArray());

        $_coddocrepleg = [];
        foreach (ParamsIndependiente::getCodruaDocumentos() as $ai => $valor) {
            if ($valor == 'TI' || $valor == 'RC') {
                continue;
            }
            $_coddocrepleg[$ai] = $valor;
        }
        $_tippag = [
            'T' => 'PENDIENTE DEFINIR FORMA DE PAGO',
            'A' => 'ABONO A CUNETA DE BANCO',
            'D' => 'DAVIPLATA',
        ];

        return [
            '_tipdur' => ParamsIndependiente::getTipoDuracion(),
            '_codind' => ParamsIndependiente::getCodigoIndice(),
            '_contratista' => ['estado' => 'N', 'detalle' => 'NO'],
            '_todmes' => ParamsIndependiente::getPagaMes(),
            '_forpre' => ParamsIndependiente::getFormaPresentacion(),
            '_tipsoc' => ParamsIndependiente::getTipoSociedades(),
            '_pymes' => ['estado' => 'N', 'detalle' => 'NO'],
            '_tipemp' => ParamsIndependiente::getTipoEmpresa(),
            '_tipapo' => ParamsIndependiente::getTipoAportante(),
            '_ofiafi' => ['estado' => '13', 'detalle' => '13'],
            '_colegio' => ['estado' => 'N', 'detalle' => 'NO'],
            '_tipper' => ParamsIndependiente::getTipoPersona(),
            '_codzon' => ParamsIndependiente::getZonas(),
            '_calemp' => ParamsIndependiente::getCalidadEmpresa(),
            '_codciu' => ParamsIndependiente::getCiudades(),
            '_codact' => ParamsIndependiente::getActividades(),
            '_coddoc' => ParamsIndependiente::getTipoDocumentos(),
            '_coddocrepleg' => $_coddocrepleg,
            '_tippag' => $_tippag,
            '_bancos' => ParamsIndependiente::getBancos(),
            '_tipcue' => ParamsIndependiente::getTipoCuenta(),
            '_giro' => ParamsIndependiente::getGiro(),
            '_codgir' => ParamsIndependiente::getCodigoGiro(),
        ];
    }

    /**
     * editarView function
     *
     * @param  int  $id
     * @return void
     */
    public function editarView($id)
    {
        if (! $id) {
            return redirect('aprobaindepen/index');
            exit;
        }
        $this->independienteServices = new IndependienteServices;
        $this->setParamToView('hide_header', true);
        $mercurio41 = Mercurio41::whereRaw("id='{$id}'")->first();
        $this->setParamToView('mercurio41', $mercurio41);
        $this->setParamToView('tipopc', $this->tipopc);
        $this->setParamToView('seguimiento', $this->independienteServices->seguimiento($mercurio41));

        $mercurio01 = Mercurio01::first();
        $procesadorComando = Comman::Api();
        $procesadorComando->runCli(
            [
                'servicio' => 'ComfacaAfilia',
                'metodo' => 'parametros_independiente',
            ],
            false
        );
        $paramsEmpresa = new ParamsIndependiente;
        $paramsEmpresa->setDatosCaptura($procesadorComando->toArray());

        $this->loadParametrosView();
        $mercurio37 = Mercurio37::whereRaw(" tipopc=2 AND numero='{$mercurio41->getId()}'")->first();
        $this->independienteServices->loadDisplay($mercurio41);
        $this->setParamToView('mercurio37', $mercurio37);
        $this->setParamToView('idModel', $id);
        $this->setParamToView('det_tipo', Mercurio06::whereRaw("tipo = '{$mercurio41->getTipo()}'")->first()->getDetalle());
        $this->setParamToView('mercurio01', $mercurio01);
        $this->setParamToView('title', 'Editar Ficha Independiente ' . $mercurio41->getCedtra());
    }

    public function editaEmpresa()
    {
        $this->setResponse('ajax');
        $request = request();
        $nit = $request->input('nit');
        $id = $request->input('id');
        try {
            $mercurio41 = Mercurio41::whereRaw("nit='{$nit}' AND id='{$id}'")->first();
            if (! $mercurio41) {
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
                Mercurio41::whereRaw("id='{$id}' AND nit='{$nit}'")->update($setters);
                $salida = [
                    'msj' => 'Proceso se ha completado con éxito',
                    'success' => true,
                ];
            }
        } catch (DebugException $err) {
            $salida = [
                'success' => false,
                'msj' => $err->getMessage(),
            ];
        }

        return $this->renderObject($salida, false);
    }

    /**
     * buscarEnSisuView function
     * Datos de la empresa en sisuweb, si ya está registrada. pruebas 98588506
     *
     * @param [type] $nit
     * @return void
     */
    public function buscarEnSisuView($id)
    {

        $mercurio41 = Mercurio41::whereRaw("id='{$id}'")->first();
        if (! $mercurio41) {
            set_flashdata('error', [
                'msj' => 'La empresa no se encuentra registrada.',
                'code' => 201,
            ]);

            return redirect('aprobaindepen/index');
            exit();
        }

        $procesadorComando = Comman::Api();
        $procesadorComando->runCli(
            [
                'servicio' => 'ComfacaEmpresas',
                'metodo' => 'informacion_empresa',
                'params' => [
                    'nit' => $mercurio41->getCedtra(),
                ],
            ]
        );
        $response = $procesadorComando->toArray();
        if (! $response['success']) {
            set_flashdata('error', [
                'msj' => 'La empresa no se encuentra registrada.',
                'code' => 201,
            ]);

            return redirect('aprobaindepen/index');
            exit();
        }

        $this->setParamToView('idEmpresa', $id);
        $this->setParamToView('empresa', $response['data']);
        $this->setParamToView('trayectoria', $response['trayectoria']);
        $this->setParamToView('sucursales', $response['sucursales']);
        $this->setParamToView('listas', $response['listas']);
        $this->setParamToView('title', "Independiente SisuWeb - {$mercurio41->getCedtra()}");
    }

    /**
     * excel_reporte function
     * pendientes, devueltos y rechazados
     *
     * @return void
     */
    public function excelReporte($estado = 'P')
    {
        /*  $this->setResponse('view');
        $fecha = new Date();
        $file = "public/temp/" . "reporte_solicitudes_" . $fecha . ".xls";
        require_once "Library/Excel/Main.php";
        $excels = new Spreadsheet_Excel_Writer($file);
        $excel = $excels->addWorksheet();
        $column_title = $excels->addFormat(array(
            'fontfamily' => 'Verdana',
            'size' => 12,
            'fgcolor' => 12,
            'border' => 1,
            'bordercolor' => 'black',
            "halign" => 'center'
        ));
        $title = $excels->addFormat(array(
            'fontfamily' => 'Verdana',
            'size' => 13,
            'border' => 0,
            'bordercolor' => 'black',
            "halign" => 'center'
        ));
        $column_style = $excels->addFormat(array(
            'fontfamily' => 'Verdana',
            'size' => 11,
            'border' => 1,
            'bordercolor' => 'black',
        ));
        $excel->setMerge(0, 1, 0, 6);
        $excel->write(0, 1, 'Reporte De Solicitudes Afiliacion Empresas', $title);
        $columns = array('Documento', 'Nit', 'Razon social', 'Cedula Representante', 'Cod documento', 'Dias vencidos', 'Estado', 'Tipsoc', 'Tipper', 'Email', 'Cod actividad', 'Telefono');
        $excel->setColumn(0, 0, 16);
        $excel->setColumn(1, 1, 16);
        $excel->setColumn(2, 2, 35);
        $excel->setColumn(3, 3, 25);
        $excel->setColumn(4, 4, 25);
        $excel->setColumn(5, 5, 25);
        $excel->setColumn(6, 6, 30);
        $excel->setColumn(7, 7, 10);
        $excel->setColumn(8, 8, 10);
        $excel->setColumn(9, 9, 50);
        $excel->setColumn(10, 10, 20);
        $excel->setColumn(11, 11, 20);
        $i = 0;
        $j = 2;
        foreach ($columns as $column) {
            $value = ucfirst($column);
            $excel->write($j, $i++, $value, $column_title);
        }
        $usuario = parent::getActUser();
        $solicitudes = Mercurio41::where(" estado='{$estado}' AND usuario='{$usuario}' ORDER BY fecini DESC")->get();
        $j++;
        foreach ($solicitudes as $solicitud) {
            $i = 0;
            $dias_vencidos = CalculatorDias::calcular($this->tipopc, $solicitud->getId(), $solicitud->getFecsol());
            $excel->write($j, $i++, $solicitud->getDocumento(), $column_style);
            $excel->write($j, $i++, $solicitud->getCedtra(), $column_style);
            $excel->write($j, $i++, $solicitud->getRazsoc(), $column_style);
            $excel->write($j, $i++, $solicitud->getCedrep(), $column_style);
            $excel->write($j, $i++, $solicitud->getCoddoc(), $column_style);
            $excel->write($j, $i++, $dias_vencidos, $column_style);
            $excel->write($j, $i++, $solicitud->getEstadoDetalle(), $column_style);
            $excel->write($j, $i++, $solicitud->getTipsoc(), $column_style);
            $excel->write($j, $i++, $solicitud->getTipper(), $column_style);
            $excel->write($j, $i++, $solicitud->getEmail(), $column_style);
            $excel->write($j, $i++, '#' . $solicitud->getCodact(), $column_style);
            $excel->write($j, $i++, $solicitud->getTelefono(), $column_style);
            $j++;
        }
        $excels->close();
        header("location: " . env('APP_URL') . "/{$file}"); */
    }

    /**
     * aprobar function
     * Aprobación de empresa
     *
     * @return void
     */
    public function aprueba(Request $request)
    {
        $this->db->begin();
        try {
            try {
                $apruebaSolicitud = new ApruebaSolicitud();
                $postData = $request->all();
                $idSolicitud = $request->input('id');
                $calemp = 'I';
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

    public function borrarFiltro()
    {
        $this->setResponse('ajax');
        $request = request();
        set_flashdata('filter_independiente', false, true);
        set_flashdata('filter_params', false, true);

        return $this->renderObject([
            'success' => true,
            'query' => get_flashdata_item('filter_independiente'),
            'filter' => get_flashdata_item('filter_params'),
        ]);
    }

    /**
     * aportesView function
     *
     * @param  int  $id
     * @return void
     */
    public function aportesView($id)
    {
        $mercurio41 = Mercurio41::whereRaw(" id='{$id}'")->first();
        if (! $mercurio41) {
            set_flashdata('error', [
                'msj' => 'La empresa no se encuentra registrada.',
                'code' => 201,
            ]);

            return redirect('aprobaindepen/info/' . $id);
            exit();
        }

        $this->setParamToView('hide_header', true);
        $this->setParamToView('idModel', $id);
        $this->setParamToView('cedtra', $mercurio41->getCedtra());
        $this->setParamToView('title', 'Aportes de empresa ' . $mercurio41->getCedtra());
    }

    /**
     * aportes function
     *
     * @param [type] $id
     * @return void
     */
    public function aportes($id)
    {
        $this->setResponse('ajax');
        $request = request();
        try {
            try {
                $mercurio41 = (new Mercurio41)->findFirst(" id='{$id}'");
                if (! $mercurio41) {
                    throw new DebugException('La empresa no se encuentra registrada.', 201);
                }

                $procesadorComando = Comman::Api();
                $procesadorComando->runCli(
                    [
                        'servicio' => 'AportesEmpresas',
                        'metodo' => 'buscarAportesEmpresa',
                        'params' => $mercurio41->getCedtra(),
                    ]
                );

                if ($procesadorComando->isJson() == false) {
                    throw new DebugException('Error procesando la consulta de aportes', 501);
                }

                $salida = $procesadorComando->toArray();
                $salida['solicitud'] = $mercurio41->getArray();
            } catch (DebugException $e) {
                throw new DebugException($e->getMessage(), 501);
            }
        } catch (DebugException $err) {
            $salida = [
                'success' => false,
                'msj' => 'No se pudo realizar el movimiento ' . "\n" . $err->getMessage() . "\n " . $err->getLine(),
            ];
        }

        return $this->renderObject($salida);
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
            $mercurio41 = (new Mercurio41)->findFirst(" id='{$id}' and estado='A' ");
            if (! $mercurio41) {
                throw new DebugException('La empresa no se encuentra aprobada para consultar sus datos.', 501);
            }

            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
                [
                    'servicio' => 'ComfacaAfilia',
                    'metodo' => 'parametros_empresa',
                ]
            );
            $datos_captura = $procesadorComando->toArray();
            $paramsEmpresa = new ParamsIndependiente;
            $paramsEmpresa->setDatosCaptura($datos_captura);

            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
                [
                    'servicio' => 'ComfacaEmpresas',
                    'metodo' => 'informacion_empresa',
                    'params' => [
                        'nit' => $mercurio41->getCedtra(),
                    ],
                ]
            );

            if ($procesadorComando->isJson() == false) {
                throw new DebugException('Error al buscar la empresa en Sisuweb', 501);
            }

            $out = $procesadorComando->toArray();
            $empresa = $out['data'];

            $mercurio01 = Mercurio01::first();
            $det_tipo = Mercurio06::whereRaw("tipo = '{$mercurio41->getTipo()}'")->first()->getDetalle();

            $mercurio41 = new Mercurio41;
            $mercurio41->createAttributes($empresa);

            $htmlEmpresa = view('aprobaindepen/tmp/consulta', [
                'mercurio41' => $mercurio41,
                'mercurio01' => $mercurio01,
                'det_tipo' => $det_tipo,
                '_coddoc' => ParamsIndependiente::getTipoDocumentos(),
                '_calemp' => ParamsIndependiente::getCalidadEmpresa(),
                '_codciu' => ParamsIndependiente::getCiudades(),
                '_codzon' => ParamsIndependiente::getZonas(),
                '_codact' => ParamsIndependiente::getActividades(),
                '_tipsoc' => ParamsIndependiente::getTipoSociedades(),
            ])->render();

            $code_estados = [];
            $query = Mercurio11::all();
            foreach ($query as $row) {
                $code_estados[$row->getCodest()] = $row->getDetalle();
            }

            $this->setParamToView('code_estados', $code_estados);
            $this->setParamToView('mercurio41', $mercurio41);
            $this->setParamToView('consulta_empresa', $htmlEmpresa);
            $this->setParamToView('hide_header', true);
            $this->setParamToView('idModel', $id);
            $this->setParamToView('nit', $mercurio41->getCedtra());
            $this->setParamToView('title', 'Empresa Aprobada ' . $mercurio41->getCedtra());
        } catch (DebugException $err) {
            set_flashdata('error', [
                'msj' => $err->getMessage(),
                'code' => 201,
            ]);

            return redirect('aprobaindepen/index/A');
            exit;
        }
    }

    public function deshacer(Request $request)
    {
        $indepeServices = new IndependienteServices;
        $notifyEmailServices = new NotifyEmailServices;

        $action = $request->input('action');
        $codest = $request->input('codest');
        $sendEmail = $request->input('send_email');
        $nota = $request->input('nota');

        try {
            $id = $request->input('id');
            $mercurio41 = Mercurio41::where("id", $id)->first();
            if (! $mercurio41) {
                throw new DebugException('Los datos de la empresa no son validos para procesar.', 501);
            }

            $ps = Comman::Api();
            $ps->runCli(
                [
                    'servicio' => 'ComfacaEmpresas',
                    'metodo' => 'informacion_empresa',
                    'params' => [
                        'nit' => $mercurio41->cedtra,
                        'coddoc' => $mercurio41->tipdoc,
                    ],
                ]
            );

            if ($ps->isJson() == false) {
                throw new DebugException('Error al buscar la empresa en Sisuweb', 501);
            }

            $out = $ps->toArray();
            $empresaSisu = $out['data'];

            $ps->runCli(
                [
                    'servicio' => 'ComfacaAfilia',
                    'metodo' => 'deshacer_aprobacion_independiente',
                    'params' => [
                        'cedtra' => $mercurio41->cedtra,
                        'coddoc' => $mercurio41->tipdoc,
                        'fecafi' => $mercurio41->fecapr,
                        'fecapr' => $mercurio41->fecapr,
                        'nota' => $nota,
                    ],
                ]
            );

            if ($ps->isJson() == false) {
                throw new DebugException('Error al procesar el deshacer la aprobación en SisuWeb.', 501);
            }

            $resdev = $ps->toArray();
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
                    $indepeServices->devolver($mercurio41, $nota, $codest, $campos_corregir);
                    if ($sendEmail == 'S') {
                        $notifyEmailServices->emailDevolver($mercurio41, $indepeServices->msjDevolver($mercurio41, $nota));
                    }
                }

                if ($action == 'R') {
                    $indepeServices->rechazar($mercurio41, $nota, $codest);
                    if ($sendEmail == 'S') {
                        $notifyEmailServices->emailRechazar($mercurio41, $indepeServices->msjRechazar($mercurio41, $nota));
                    }
                }

                if ($action == 'I') {
                    $mercurio41->setEstado('I');
                    $mercurio41->setFecest(date('Y-m-d'));
                    $mercurio41->save();
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
