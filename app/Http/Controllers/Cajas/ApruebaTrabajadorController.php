<?php

namespace App\Http\Controllers\Cajas;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Library\Collections\ParamsTrabajador;
use App\Models\Adapter\DbBase;
use App\Models\Mercurio01;
use App\Models\Mercurio02;
use App\Models\Mercurio07;
use App\Models\Mercurio10;
use App\Models\Mercurio11;
use App\Models\Mercurio30;
use App\Models\Mercurio31;
use App\Services\Aprueba\ApruebaTrabajador;
use App\Services\CajaServices\TrabajadorServices;
use App\Services\Srequest;
use App\Services\Tag;
use App\Services\Utils\CalculatorDias;
use App\Services\Utils\Comman;
use App\Services\Utils\NotifyEmailServices;
use App\Services\Utils\Pagination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class ApruebaTrabajadorController extends ApplicationController
{
    protected $tipopc = 1;

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
     * @var TrabajadorServices
     */
    protected $trabajadorServices;

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

        set_flashdata('filter_trabajador', $query, true);
        set_flashdata('filter_params', $pagination->filters, true);

        $response = $pagination->render(
            new TrabajadorServices
        );

        return $this->renderObject($response, false);
    }

    public function changeCantidadPagina(Request $request, string $estado = 'P')
    {
        $this->buscar($request, $estado);
    }

    /**
     * index function
     *
     * @changed [2023-12-00]
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
            'cedtra' => 'Cedula',
            'priape' => 'Primer Apellido',
            'segape' => 'Segundo Apellido',
            'prinom' => 'Primer Nombre',
            'segnom' => 'Segundo Nombre',
            'fecest' => 'Fecha estado',
            'fecsol' => 'Fecha Solicitud',
            'nit' => 'Nit Empresa',
        ];

        $params = $this->loadParametrosView();

        return view('cajas.aprobaindepen.index', [
            ...$params,
            'campo_filtro' => $campo_field,
            'filters' => get_flashdata_item('filter_params'),
            'title' => 'Aprueba Trabajador',
            'mercurio11' => Mercurio11::get(),
        ]);
    }

    public function buscar(Request $request, string $estado = 'P')
    {
        $this->setResponse('ajax');
        $pagina = ($request->input('pagina')) ? $request->input('pagina') : 1;
        $cantidad_pagina = ($request->input('numero')) ? $request->input('numero') : 10;
        $usuario = $this->user['usuario'];
        $query_str = ($estado == 'T') ? " estado='{$estado}'" : "usuario='{$usuario}' and estado='{$estado}'";

        $pagination = new Pagination(
            new Srequest(
                [
                    'cantidadPaginas' => $cantidad_pagina,
                    'query' => $query_str,
                    'estado' => $estado,
                    'pagina' => $pagina,
                ]
            )
        );

        if (get_flashdata_item('filter_trabajador') != false) {
            $query = $pagination->persistencia(get_flashdata_item('filter_params'));
        } else {
            $query = $pagination->filter(
                $request->input('campo'),
                $request->input('condi'),
                $request->input('value')
            );
        }

        set_flashdata('filter_trabajador', $query, true);
        set_flashdata('filter_params', $pagination->filters, true);

        $response = $pagination->render(new TrabajadorServices);

        return $this->renderObject($response, false);
    }

    /**
     * infor function
     *
     * @changed [2023-12-19]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     *
     * @param  string  $nit
     * @param  string  $cedtra
     * @param  string  $id
     * @return void
     */
    public function infor(Request $request)
    {
        try {
            $id = $request->input('id');
            if (! $id) {
                throw new DebugException('Error no se puede identificar el identificador de la solicitud.', 501);
            }

            $this->trabajadorServices = new TrabajadorServices;
            $mercurio31 = Mercurio31::where("id", $id)->first();

            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
                [
                    'servicio' => 'ComfacaAfilia',
                    'metodo' => 'parametros_trabajadores',
                ]
            );

            $paramsTrabajador = new ParamsTrabajador;
            $paramsTrabajador->setDatosCaptura($procesadorComando->toArray());

            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
                [
                    'servicio' => 'ComfacaEmpresas',
                    'metodo' => 'informacion_empresa',
                    'params' => $mercurio31->nit,
                ]
            );

            $datos_captura = $procesadorComando->toArray();
            $empresa_sisu = false;
            if ($datos_captura) {
                $empresa_sisu = ($datos_captura['success']) ? $datos_captura['data'] : false;
            }

            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
                [
                    'servicio' => 'ComfacaAfilia',
                    'metodo' => 'trabajador',
                    'params' => [
                        'cedtra' => $mercurio31->cedtra,
                    ],
                ]
            );

            $trabajador_sisuweb = false;
            $rqs = $procesadorComando->toArray();
            if ($rqs) {
                if ($rqs['success']) {
                    $trabajador_sisuweb = $rqs['data'];
                }
            }

            $html = view(
                'cajas/aprobaciontra/tmp/consulta',
                [
                    'mercurio01' => Mercurio01::first(),
                    'trabajador' => $mercurio31,
                    '_coddoc' => ParamsTrabajador::getTiposDocumentos(),
                    '_codciu' => ParamsTrabajador::getCiudades(),
                    '_codzon' => ParamsTrabajador::getZonas(),
                    '_sexos' => ParamsTrabajador::getSexos(),
                    '_estciv' => ParamsTrabajador::getEstadoCivil(),
                    '_cabhog' => ParamsTrabajador::getCabezaHogar(),
                    '_captra' => ParamsTrabajador::getCapacidadTrabajar(),
                    '_tipdis' => ParamsTrabajador::getTipoDiscapacidad(),
                    '_nivedu' => ParamsTrabajador::getNivelEducativo(),
                    '_rural' => ParamsTrabajador::getRural(),
                    '_tipcon' => ParamsTrabajador::getTipoContrato(),
                    '_vivienda' => ParamsTrabajador::getVivienda(),
                    '_tipafi' => ParamsTrabajador::getTipoAfiliado(),
                    '_trasin' => ParamsTrabajador::getSindicalizado(),
                    '_bancos' => ParamsTrabajador::getBancos(),
                ]
            )->render();

            $adjuntos = $this->trabajadorServices->adjuntos($mercurio31);
            $seguimiento = $this->trabajadorServices->seguimiento($mercurio31);

            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
                [
                    'servicio' => 'ComfacaEmpresas',
                    'metodo' => 'buscar_sucursales_en_empresa',
                    'params' => $mercurio31->nit,
                ]
            );

            $sucursales = $procesadorComando->toArray();
            $_codsuc = [];
            if ($sucursales['success']) {
                foreach ($sucursales['data'] as $data) {
                    $_codsuc["{$data['codsuc']}"] = $data['codsuc'] . ' ' . $data['detalle'];
                }
            }

            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
                [
                    'servicio' => 'ComfacaEmpresas',
                    'metodo' => 'buscar_listas_en_empresa',
                    'params' => $mercurio31->nit,
                ]
            );

            $listas = $procesadorComando->toArray();
            $_codlis = [];
            if ($listas['success']) {
                foreach ($listas['data'] as $data) {
                    $_codlis[$data['codlis']] = $data['codlis'];
                }
            }

            $componente_codsuc = Tag::selectStatic(
                new Srequest([
                    'name' => 'codsuc',
                    'options' => $_codsuc,
                    'use_dummy' => true,
                    'dummyValue' => '',
                    'class' => 'form-control',
                ])
            );

            $componente_codlis = Tag::selectStatic(
                new Srequest([
                    'name' => 'codlis',
                    'options' => $_codlis,
                    'class' => 'form-control',
                ])
            );

            $campos_disponibles = $mercurio31->CamposDisponibles();
            $response = [
                'success' => true,
                'data' => $mercurio31->toArray(),
                'trabajador_sisu' => $trabajador_sisuweb,
                'mercurio11' => Mercurio11::all(),
                'consulta' => $html,
                'adjuntos' => $adjuntos,
                'seguimiento' => $seguimiento,
                'campos_disponibles' => $campos_disponibles,
                'empresa_sisu' => $empresa_sisu,
                'componente_codsuc' => $componente_codsuc,
                'componente_codlis' => $componente_codlis,
            ];
        } catch (DebugException $err) {
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
     * @changed [2023-12-19]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     *
     * @return void
     */
    public function aprueba(Request $request)
    {
        $this->setResponse('ajax');
        $debuginfo = [];
        try {
            try {
                $user = session()->get('user');
                $acceso = $this->Gener42->count("permiso='92' AND usuario='{$user['usuario']}'");
                if ($acceso == 0) {
                    return $this->renderObject(['success' => false, 'msj' => 'El usuario no dispone de permisos de aprobación'], false);
                }
                $apruebaSolicitud = new ApruebaTrabajador;
                $this->db->begin();
                $idSolicitud = $request->input('id', 'addslaches', 'alpha', 'extraspaces', 'striptags');
                $solicitud = $apruebaSolicitud->findSolicitud($idSolicitud);
                $apruebaSolicitud->findSolicitante($solicitud);
                $apruebaSolicitud->procesar($_POST);

                $this->db->commit();
                $apruebaSolicitud->enviarMail($request->input('actapr'), $request->input('fecapr'));
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

    /**
     * devolver function
     *
     * @changed [2023-12-19]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     *
     * @return void
     */
    public function devolver(Request $request)
    {
        $this->trabajadorServices = $this->services->get('TrabajadorServices');
        $notifyEmailServices = new NotifyEmailServices;
        $this->setResponse('ajax');
        try {
            $id = $request->input('id', 'addslaches', 'alpha', 'extraspaces', 'striptags');
            $nota = $request->input('nota');
            $codest = $request->input('codest', 'addslaches', 'alpha', 'extraspaces', 'striptags');
            $array_corregir = $request->input('campos_corregir');
            $campos_corregir = implode(';', $array_corregir);

            $mercurio31 = Mercurio31::where("id", $id)->first();
            $this->trabajadorServices->devolver($mercurio31, $nota, $codest, $campos_corregir);

            $notifyEmailServices->emailDevolver(
                $mercurio31,
                $this->trabajadorServices->msjDevolver($mercurio31, $nota)
            );

            $salida = [
                'success' => true,
                'msj' => 'El proceso se ha realizado con éxito',
            ];
        } catch (DebugException $err) {
            $salida = [
                'success' => false,
                'msj' => $err->getMessage(),
                'code' => $err->getCode(),
            ];
        }

        return $this->renderObject($salida);
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
        $this->trabajadorServices = $this->services->get('TrabajadorServices');

        try {
            $id = $request->input('id', 'addslaches', 'alpha', 'extraspaces', 'striptags');
            $nota = $request->input('nota');
            $codest = $request->input('codest', 'addslaches', 'alpha', 'extraspaces', 'striptags');

            $mercurio31 = Mercurio31::where("id", $id)->first();

            $this->trabajadorServices->rechazar($mercurio31, $nota, $codest);

            $notifyEmailServices->emailRechazar(
                $mercurio31,
                $this->trabajadorServices->msjRechazar($mercurio31, $nota)
            );

            $salida = [
                'success' => true,
                'msj' => 'Movimiento Realizado Con Exito',
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

    public function validarMultiafiliacion(Request $request)
    {
        $this->setResponse('ajax');
        $id = $request->input('id');
        $mercurio31 = Mercurio31::where("id", $id)->first();
        $nit = $mercurio31->getNit();
        $cedtra = $mercurio31->getCedtra();

        $procesadorComando = Comman::Api();
        $procesadorComando->runCli(
            [
                'servicio' => 'ComfacaEmpresas',
                'metodo' => 'informacion_trabajador',
                'params' => $cedtra,
            ]
        );

        $out = $procesadorComando->toArray();
        if ($out['success']) {
            $datos_trabajador = $out['data'];
            foreach ($datos_trabajador as $key => $value) {
                if (is_numeric($key)) {
                    continue;
                }
                if ($mercurio31->isAttribute($key)) {
                    $mercurio31->writeAttribute($key, "{$value}");
                }
            }
        }

        $response['multi'] = false;
        if ($mercurio31->getNit() != $nit && $mercurio31->getEstado() == 'A') {
            $response['multi'] = true;
        }

        return $this->renderObject($response);
    }

    public function pendienteEmail()
    {
        /* $flash_mensaje = SESSION::getData("flash_mensaje");
        SESSION::setData("flash_mensaje", null);
        $this->setParamToView("flash_mensaje", $flash_mensaje);
        $this->setParamToView("title", "Procesar Notificación Pendiente"); */
    }

    public function rezagoCorreo(Request $request)
    {
        $this->setResponse('view');
        $cedtra = $request->input('cedtra');
        $anexo_final = $request->input('anexo_final');
        $anexo_inicial = $request->input('anexo_inicial');

        $asunto = 'Afiliacion con Exito - Comfaca En Linea';
        $mercurio31 = Mercurio31::where("documento", $cedtra)->first();
        $mercurio07 = Mercurio07::whereRaw("tipo='{$mercurio31->getTipo()}' and coddoc='{$mercurio31->getCoddoc()}' and documento = '{$mercurio31->getDocumento()}'")->first();
        $mercurio01 = Mercurio01::first();
        $mercurio02 = Mercurio02::first();

        $_email = trim($mercurio01->getEmail());
        $_clave = trim($mercurio01->getClave());
        $mensaje = '';
        ob_start();
        $this->setParamToView('rutaImg', 'http://186.119.116.228:8091/Mercurio/public/img/Mercurio/logob.png');
        $this->setParamToView('mercurio31', $mercurio31);
        $this->setParamToView('mercurio02', $mercurio02);
        $this->setParamToView('anexo_final', $anexo_final);
        $this->setParamToView('anexo_inicial', $anexo_inicial);
        echo View::renderView('aprobaciontra/mail/aprobar');
        $mensaje = ob_get_contents();
        ob_end_clean();
        /*
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
        Router::redirectToApplication('Cajas/aprobaciontra/pendiente_email'); */
    }

    public function loadParametrosView()
    {
        $procesadorComando = Comman::Api();
        $procesadorComando->runCli(
            [
                'servicio' => 'ComfacaAfilia',
                'metodo' => 'parametros_trabajadores',
            ]
        );

        $paramsTrabajador = new ParamsTrabajador;
        $paramsTrabajador->setDatosCaptura($procesadorComando->toArray());

        $_codciu = ParamsTrabajador::getCiudades();
        $_ciunac = $_codciu;
        foreach (ParamsTrabajador::getZonas() as $ai => $valor) {
            if ($ai < 19001 && $ai >= 18001) {
                $_codzon[$ai] = $valor;
            }
        }
        $_tipsal = (new Mercurio31)->getTipsalArray();

        return [
            '_ciunac' => $_ciunac,
            '_tipsal' => $_tipsal,
            '_codciu' => $_codciu,
            '_codzon' => $_codzon,
            '_codind' => [],
            '_todmes' => [],
            '_tipapo' => [],
            '_tipsoc' => [],
            '_coddoc' => ParamsTrabajador::getTiposDocumentos(),
            '_sexo' => ParamsTrabajador::getSexos(),
            '_estciv' => ParamsTrabajador::getEstadoCivil(),
            '_cabhog' => ParamsTrabajador::getCabezaHogar(),
            '_captra' => ParamsTrabajador::getCapacidadTrabajar(),
            '_tipdis' => ParamsTrabajador::getTipoDiscapacidad(),
            '_nivedu' => ParamsTrabajador::getNivelEducativo(),
            '_rural' => ParamsTrabajador::getRural(),
            '_tipcon' => ParamsTrabajador::getTipoContrato(),
            '_trasin' => ParamsTrabajador::getSindicalizado(),
            '_vivienda' => ParamsTrabajador::getVivienda(),
            '_tipafi' => ParamsTrabajador::getTipoAfiliado(),
            '_cargo' => ParamsTrabajador::getOcupaciones(),
            '_orisex' => ParamsTrabajador::getOrientacionSexual(),
            '_facvul' => ParamsTrabajador::getVulnerabilidades(),
            '_peretn' => ParamsTrabajador::getPertenenciaEtnicas(),
            '_vendedor' => ParamsTrabajador::getVendedor(),
            '_empleador' => ParamsTrabajador::getEmpleador(),
            '_tippag' => ParamsTrabajador::getTipoPago(),
            '_tipcue' => ParamsTrabajador::getTipoCuenta(),
            '_giro' => ParamsTrabajador::getGiro(),
            '_codgir' => ParamsTrabajador::getCodigoGiro(),
            '_bancos' => ParamsTrabajador::getBancos(),
            'tipo' => 'T',
            'tipopc' => $this->tipopc,
        ];
    }

    public function editarView($id = 0)
    {
        $this->setParamToView('hide_header', true);

        if (! $id) {
            return redirect('aprobaciontra/index');
            exit;
        }
        $trabajador = Mercurio31::where("id", $id)->first();
        $empresa = Mercurio30::where("nit", $trabajador->getNit())->first();

        $procesadorComando = Comman::Api();
        $procesadorComando->runCli(
            [
                'servicio' => 'ComfacaAfilia',
                'metodo' => 'parametros_trabajadores',
            ],
            false
        );
        $paramsTrabajador = new ParamsTrabajador;
        $paramsTrabajador->setDatosCaptura($procesadorComando->toArray());

        $this->loadParametrosView($trabajador->getNit());

        $this->setParamToView('mercurio31', $trabajador);
        $this->setParamToView('idModel', $trabajador->getId());
        $this->setParamToView('mercurio30', $empresa);
        $this->setParamToView('mercurio11', Mercurio11::all());
        $this->setParamToView('title', "Solicitud Trabajador - {$trabajador->getCedtra()}");
    }

    public function editarSolicitud(Request $request)
    {
        $this->setResponse('ajax');
        try {
            $id = $request->input('id', 'addslaches', 'alpha', 'extraspaces', 'striptags');
            $cedtra = $request->input('cedtra', 'addslaches', 'alpha', 'extraspaces', 'striptags');
            $mercurio31 = Mercurio31::whereRaw(" id='{$id}' and cedtra='{$cedtra}'")->first();
            if (! $mercurio31) {
                throw new DebugException('El trabajador no está disponible para notificar por email', 501);
            } else {
                $data = [
                    'razsoc' => $request->input('razsoc'),
                    'priape' => $request->input('priape'),
                    'segape' => $request->input('segape'),
                    'prinom' => $request->input('prinom'),
                    'segnom' => $request->input('segnom'),
                    'fecnac' => $request->input('fecnac'),
                    'ciunac' => $request->input('ciunac'),
                    'sexo' => $request->input('sexo'),
                    'estciv' => $request->input('estciv'),
                    'cabhog' => $request->input('cabhog'),
                    'codciu' => $request->input('codciu'),
                    'codzon' => $request->input('codzon'),
                    'direccion' => $request->input('direccion'),
                    'barrio' => $request->input('barrio'),
                    'telefono' => $request->input('telefono'),
                    'celular' => $request->input('celular'),
                    'email' => $request->input('email'),
                    'fecing' => $request->input('fecing'),
                    'salario' => $request->input('salario'),
                    'tipsal' => $request->input('tipsal'),
                    'captra' => $request->input('captra'),
                    'tipdis' => $request->input('tipdis'),
                    'nivedu' => $request->input('nivedu'),
                    'rural' => $request->input('rural'),
                    'horas' => $request->input('horas'),
                    'tipcon' => $request->input('tipcon'),
                    'trasin' => $request->input('trasin'),
                    'vivienda' => $request->input('vivienda'),
                    'tipafi' => $request->input('tipafi'),
                    'profesion' => $request->input('profesion'),
                    'cargo' => $request->input('cargo'),
                    'orisex' => $request->input('orisex'),
                    'facvul' => $request->input('facvul'),
                    'peretn' => $request->input('peretn'),
                    'dirlab' => $request->input('dirlab'),
                    'autoriza' => $request->input('autoriza'),
                    'tipjor' => $request->input('tipjor'),
                    'ruralt' => $request->input('ruralt'),
                    'comision' => $request->input('comision'),
                    'codsuc' => "{$request->input('codsuc')}",
                ];
                $setters = '';
                foreach ($data as $ai => $row) {
                    if (strlen($row) > 0) {
                        $setters .= " $ai='{$row}',";
                    }
                }
                $setters = trim($setters, ',');
                Mercurio31::whereRaw("id='{$id}' AND cedtra='{$cedtra}'")->update($setters);

                $db = DbBase::rawConnect();

                $data = $db->fetchOne("SELECT max(id), mercurio31.* FROM mercurio31 WHERE cedtra='{$cedtra}'");
                $salida = [
                    'msj' => 'Proceso se ha completado con éxito',
                    'success' => true,
                    'data' => $data,
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
     * buscar_sisu function
     * Datos de la empresa en sisuweb, si ya está registrada. pruebas 98588506
     *
     * @param [type] $nit
     * @return void
     */
    public function buscarSisu(Request $request)
    {
        try {
            $this->setResponse('ajax');
            $id = $request->input('id', 'addslaches', 'alpha', 'extraspaces', 'striptags');

            $mercurio31 = Mercurio31::whereRaw("id='{$id}'")->first();
            if (! $mercurio31) {
                throw new DebugException('Error el trabajador no se encuentra registrado', 501);
            }

            $ps = Comman::Api();
            $ps->runCli(
                [
                    'servicio' => 'ComfacaEmpresas',
                    'metodo' => 'informacion_trabajador',
                    'params' => $mercurio31->getCedtra(),
                ]
            );

            $out = $ps->toArray();
            if (! $out['success']) {
                throw new DebugException('Error el trabajador no se encuentra registrado', 501);
            }

            $this->renderObject(
                [
                    'success' => true,
                    'data' => [
                        'trabajador' => $out['data'],
                        'solicitud' => $mercurio31->getArray(),
                        'trayectorias' => $out['data']['trayectoria'],
                        'salarios' => $out['data']['salarios'],
                        'title' => 'Trabajador SisuWeb ' . $mercurio31->getCedtra(),
                    ],
                ],
                false
            );
        } catch (DebugException $err) {
            $this->renderObject(
                [
                    'success' => false,
                    'msj' => $err->getMessage(),
                ]
            );
        }
    }

    public function opcional($estado = 'P')
    {
        $this->setParamToView('hide_header', true);
        $this->setParamToView('title', 'Aprobación Trabajadores');
        $mercurio31 = Mercurio31::whereRaw("estado='{$estado}' AND usuario=" . parent::getActUser() . ' ORDER BY fecsol ASC')->get();
        $trabajadores = [];
        foreach ($mercurio31 as $ai => $mercurio) {
            $background = '';

            $dias_vencidos = CalculatorDias::calcular($this->tipopc, $mercurio->getId(), $mercurio->getFecsol());
            if ($estado == 'P') {
                if ($dias_vencidos == 3) {
                    $background = '#f1f1ad';
                } elseif ($dias_vencidos > 3) {
                    $background = '#f5b2b2';
                }
            }
            $url = env('APP_URL') . 'Cajas/aprobaciontra/info_trabajador/' . $mercurio->getNit() . '/' . $mercurio->getCedtra() . '/' . $mercurio->getId();
            $sat = 'NORMAL';
            $trabajadores[] = [
                'estado' => $mercurio->getEstadoDetalle(),
                'recepcion' => $sat,
                'cedtra' => $mercurio->getCedtra(),
                'nit' => $mercurio->getNit(),
                'prinom' => $mercurio->getPrinom(),
                'segnom' => $mercurio->getSegnom(),
                'priape' => $mercurio->getPriape(),
                'segape' => $mercurio->getSegape(),
                'background' => $background,
                'razsoc' => $mercurio->getRazsoc(),
                'dias_vencidos' => $dias_vencidos,
                'id' => $mercurio->getId(),
                'fecsol' => $mercurio->getFecsol(),
                'url' => $url,
            ];
        }

        $this->setParamToView('trabajadores', $trabajadores);
        $this->setParamToView('buttons', ['F']);
        $this->setParamToView('pagina_con_estado', $estado);
    }

    public function reaprobar(Request $request)
    {
        $this->setResponse('ajax');
        $id = $request->input('id', 'addslaches', 'alpha', 'extraspaces', 'striptags');
        $nota = sanetizar($request->input('nota'));
        $today = new \DateTime;
        try {
            Mercurio31::whereRaw("id='$id'")->update([
                'estado' => 'A',
                'fecest' => $today->format('Y-m-d'),
            ]);

            $item = (new Mercurio10)->maximum('item', "conditions: tipopc='$this->tipopc' and numero='$id'") + 1;
            $mercurio10 = new Mercurio10;
            $mercurio10->setTipopc($this->tipopc);
            $mercurio10->setNumero($id);
            $mercurio10->setItem($item);
            $mercurio10->setEstado('A');
            $mercurio10->setNota($nota);
            $mercurio10->setFecsis($today->format('Y-m-d'));
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
        $this->renderObject($response);
    }

    public function borrarFiltro()
    {
        $this->setResponse('ajax');
        set_flashdata('filter_trabajador', false, true);
        set_flashdata('filter_params', false, true);

        return $this->renderObject([
            'success' => true,
            'query' => get_flashdata_item('filter_trabajador'),
            'filter' => get_flashdata_item('filter_params'),
        ]);
    }

    public function infoAprobadoView($id)
    {
        $this->tipopc = '1';
        try {
            $mercurio31 = Mercurio31::whereRaw(" id='{$id}' and estado='A'")->first();
            if (! $mercurio31) {
                throw new DebugException('El trabajador no se encuentra aprobado para consultar sus datos.', 501);
            }
            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
                [
                    'servicio' => 'ComfacaAfilia',
                    'metodo' => 'parametros_trabajadores',
                    'params' => true,
                ],
                false
            );

            $datos_captura = $procesadorComando->toArray();
            $paramsTrabajador = new ParamsTrabajador;
            $paramsTrabajador->setDatosCaptura($datos_captura);

            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
                [
                    'servicio' => 'ComfacaAfilia',
                    'metodo' => 'trabajador',
                    'params' => [
                        'cedtra' => $mercurio31->getCedtra(),
                    ],
                ]
            );

            if ($procesadorComando->isJson() == false) {
                throw new DebugException('Error al buscar la empresa en Sisuweb', 501);
            }

            $out = $procesadorComando->toArray();
            $trabajador = $out['data'];

            $mercurio01 = Mercurio01::first();
            $mercurio31 = new Mercurio31;
            $mercurio31->createAttributes($trabajador);
            $mercurio31->setTipo('E');
            $mercurio31->setTipafi($trabajador['tipcot']);

            $html = View::render('aprobaciontra/tmp/consulta', [
                'trabajador' => $mercurio31,
                'mercurio01' => $mercurio01,
                '_coddoc' => ParamsTrabajador::getTiposDocumentos(),
                '_codciu' => ParamsTrabajador::getCiudades(),
                '_codzon' => ParamsTrabajador::getZonas(),
                '_sexos' => ParamsTrabajador::getSexos(),
                '_estciv' => ParamsTrabajador::getEstadoCivil(),
                '_cabhog' => ParamsTrabajador::getCabezaHogar(),
                '_captra' => ParamsTrabajador::getCapacidadTrabajar(),
                '_tipdis' => ParamsTrabajador::getTipoDiscapacidad(),
                '_nivedu' => ParamsTrabajador::getNivelEducativo(),
                '_rural' => ParamsTrabajador::getRural(),
                '_tipcon' => ParamsTrabajador::getTipoContrato(),
                '_vivienda' => ParamsTrabajador::getVivienda(),
                '_tipafi' => ParamsTrabajador::getTipoAfiliado(),
                '_ocupaciones' => ParamsTrabajador::getOcupaciones(),
            ]);

            $code_estados = [];
            $query = Mercurio11::all();
            foreach ($query as $row) {
                $code_estados[$row->getCodest()] = $row->getDetalle();
            }

            $this->setParamToView('code_estados', $code_estados);
            $this->setParamToView('mercurio31', $mercurio31);
            $this->setParamToView('consulta_trabajador', $html);
            $this->setParamToView('hide_header', true);
            $this->setParamToView('idModel', $id);
            $this->setParamToView('cedtra', $mercurio31->getCedtra());
            $this->setParamToView('title', 'Trabajador Aprobada ' . $mercurio31->getCedtra());
        } catch (DebugException $err) {
            set_flashdata('error', [
                'msj' => $err->getMessage(),
                'code' => 201,
            ]);

            return redirect('aprobaciontra/index/A');
            exit;
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
        $this->setResponse('ajax');
        $trabajadorServices = new TrabajadorServices;
        $notifyEmailServices = new NotifyEmailServices;
        $action = $request->input('action');
        $codest = $request->input('codest');
        $sendEmail = $request->input('send_email');
        $nota = $request->input('nota');
        $comando = '';
        try {

            $id = $request->input('id');

            $mercurio31 = Mercurio31::whereRaw(" id='{$id}' and estado='A' ")->first();
            if (! $mercurio31) {
                throw new DebugException('Los datos del trabajador no son validos para procesar.', 501);
            }

            $ps = Comman::Api();
            $ps->runCli(
                [
                    'servicio' => 'ComfacaTrabajadores',
                    'metodo' => 'informacion_trabajador',
                    'params' => [
                        'cedtra' => $mercurio31->getCedtra(),
                    ],
                ]
            );

            $out = $ps->toArray();
            $trabajadorSisu = $out['data'];

            $ps = Comman::Api();
            $ps->runCli(
                [
                    'servicio' => 'ComfacaAfilia',
                    'metodo' => 'deshacer_aprobacion_trabajador',
                    'params' => [
                        'nit' => $mercurio31->getNit(),
                        'cedtra' => $mercurio31->getCedtra(),
                        'coddoc' => $mercurio31->getTipdoc(),
                        'fecafi' => $mercurio31->getFecafi(),
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
                    'msj' => 'No se realizo ninguna acción, el estado del trabajador no es valido para realizar la acción requerida.',
                    'data' => $trabajadorSisu,
                ];
            } else {

                // procesar
                if ($action == 'D') {
                    $campos_corregir = '';
                    $trabajadorServices->devolver($mercurio31, $nota, $codest, $campos_corregir);
                    if ($sendEmail == 'S') {
                        $notifyEmailServices->emailDevolver($mercurio31, $trabajadorServices->msjDevolver($mercurio31, $nota));
                    }
                }

                if ($action == 'R') {
                    $trabajadorServices->rechazar($mercurio31, $nota, $codest);
                    if ($sendEmail == 'S') {
                        $notifyEmailServices->emailRechazar($mercurio31, $trabajadorServices->msjRechazar($mercurio31, $nota));
                    }
                }

                if ($action == 'I') {
                    $mercurio31->setEstado('I');
                    $mercurio31->setFecest(date('Y-m-d'));
                    $mercurio31->save();
                }

                $salida = [
                    'data' => $trabajadorSisu,
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

        return $this->renderObject($salida);
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
        try {
            try {
                $mercurio31 = (new Mercurio31)->findFirst(" id='{$id}'");
                if (! $mercurio31) {
                    throw new DebugException('La empresa no se encuentra registrada.', 201);
                }

                $procesadorComando = Comman::Api();
                $procesadorComando->runCli(
                    [
                        'servicio' => 'AportesEmpresas',
                        'metodo' => 'buscarAportesEmpresa',
                        'params' => $mercurio31->getNit(),
                    ]
                );

                if ($procesadorComando->isJson() == false) {
                    throw new DebugException('Error procesando la consulta de aportes', 501);
                }

                $salida = $procesadorComando->toArray();
                $salida['solicitud'] = $mercurio31->getArray();
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
}
