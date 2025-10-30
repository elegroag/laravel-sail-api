<?php

namespace App\Services\Aprueba;

use App\Exceptions\DebugException;
use App\Models\Mercurio01;
use App\Models\Mercurio07;
use App\Models\Mercurio31;
use App\Services\CajaServices\TrabajadorServices;
use App\Services\Entities\TrabajadorEntity;
use App\Services\Srequest;
use App\Services\Utils\Comman;
use App\Services\Utils\NotifyEmailServices;
use App\Services\Utils\RegistroSeguimiento;
use App\Services\Utils\SenderEmail;
use Carbon\Carbon;
use Exception;

class ApruebaTrabajador
{
    private $today;

    private $tipopc = '1';

    private $solicitante;

    private $solicitud;

    private $dominio;

    public function __construct()
    {
        $this->today = Carbon::now();
        $this->dominio = env('APP_URL', 'http://localhost:8000');
    }

    /**
     * procesar function
     *
     * @param  array  $postData
     * @return bool
     */
    public function procesar($postData)
    {
        $mercurio31 = Mercurio31::where("id", $this->solicitud->id)->first();
        $hoy = $this->today->format('Y-m-d');
        /**
         * buscar registro de la empresa
         */
        if ($this->solicitud->tipdoc == 3) {
            throw new Exception('Error, el tipo documento para independientes no puede ser tipo NIT.', 501);
        }
        $params = array_merge($this->solicitud->toArray(), $postData);
        $params['estado'] = 'A';
        $params['fecest'] = null;
        $params['codest'] = null;
        $params['coddoc'] = $this->solicitud->tipdoc;
        $params['estado'] = 'A';
        $params['horas'] = '240';
        $params['fecsal'] = $params['fecafi'];
        $params['fecpre'] = $params['fecsol'];
        $params['ciulab'] = $params['codciu'];

        if (! $params['tippag'] || $params['tippag'] == 'T') {
            $params['numcue'] = '0';
            $params['tippag'] = 'T';
            $params['codban'] = null;
            $params['tipcue'] = null;
        }

        $params['tipcot'] = $params['tipafi'];
        $params['fecsis'] = $hoy;
        $params['fecest'] = null;
        $params['codest'] = null;
        $params['benef'] = 'S';
        $params['ruaf'] = 'N';
        $params['totcon'] = 0;
        $params['tothij'] = 0;
        $params['tother'] = 0;
        $params['totpad'] = 0;
        $params['fosfec'] = 'N';
        $params['tottra'] = 0;
        $params['vendedor'] = 'N';
        $params['tipcon'] = 'F';
        $params['empleador'] = 'N';

        $params['sexo'] = (isset($params['sexo']) && $params['sexo'] != '') ? $params['sexo'] : 'I';
        $params['ciulab'] = $params['codciu'];
        $params['pais'] = '170';
        $params['estado'] = 'A';

        $params['giro'] = (isset($params['giro']) && $params['giro'] != '') ? $params['giro'] : 'N';
        $params['giro2'] = $params['giro'];
        $params['codgir'] = (isset($params['codgir'])) ? $params['codgir'] : 'NU';
        $params['codgir2'] = $params['codgir'];
        /**
         * la empresa se debe registrar con el tipo de documento correspondiente y no con el tipo del registro de solicitud
         */
        $entity = new TrabajadorEntity;
        $entity->create($params);
        if (! $entity->validate()) {
            throw new DebugException(
                'Error, no se puede crear el trabajador pensionado por validación previa.',
                501,

            );
        }

        $ps = Comman::Api();
        $ps->runCli(
            [
                'servicio' => 'ComfacaAfilia',
                'metodo' => 'afilia_trabajador',
                'params' => [
                    'post' => $entity->getData(),
                ],
            ]
        );

        if ($ps->isJson() == false) {
            throw new DebugException('Error, no hay respuesta del servidor para validación del resultado.', 501);
        }

        $out = $ps->toArray();

        if (is_null($out) || $out == false) {
            throw new DebugException('Error, no hay respuesta del servidor para validación del resultado.', 501);
        }

        if ($out['success'] == false) {
            throw new DebugException($out['message'], 501);
        }

        $registroSeguimiento = new RegistroSeguimiento;
        $registroSeguimiento->crearNota($this->tipopc, $this->solicitud->id, $postData['nota_aprobar'], 'A');
        /**
         * actualiza la ficha de registro
         */
        $mercurio31->motivo = $postData['nota_aprobar'];
        $mercurio31->estado = 'A';
        $mercurio31->fecest = $hoy;
        $mercurio31->save();

        return true;
    }

    /**
     * enviarMail function
     *
     * @param [type] $Mercurio31
     * @param [type] $actapr
     * @param [type] $feccap
     * @return bool
     */
    public function enviarMail($actapr, $feccap)
    {
        $nombre = $this->solicitud->prinom . ' ' . $this->solicitud->segnom . ' ' . $this->solicitud->priape . ' ' . $this->solicitud->segape;
        $data = [];
        $data['razsoc'] = $this->solicitante->nombre;
        $data['email'] = $this->solicitante->email;
        $data['membrete'] = "{$this->dominio}/public/img/header_reporte_ugpp.png";
        $data['ruta_firma'] = "{$this->dominio}Mercurio/public/img/Mercurio/firma_jefe_yenny.jpg";
        $data['actapr'] = $actapr;
        $data['url_activa'] = '';
        $data['msj'] = "Se informa que el trabajador {$nombre}, con número de documento de indetificación {$this->solicitud->cedtra} fue afiliado con éxito.";

        $html = view('layouts/mail_aprobar', $data)->render();

        $asunto = "Afiliación trabajador realizada con éxito, identificación {$this->solicitud->cedtra}";
        $emailCaja = Mercurio01::first();
        $senderEmail = new SenderEmail(
            new Srequest(
                [
                    'emisor_email' => $emailCaja->email,
                    'emisor_clave' => $emailCaja->clave,
                    'asunto' => $asunto,
                ]
            )
        );
        $senderEmail->send(
            [
                [
                    'email' => $this->solicitante->email,
                    'nombre' => $this->solicitante->nombre,
                ],
            ],
            $html
        );

        return true;
    }

    public function findSolicitud($idSolicitud)
    {
        $this->solicitud = Mercurio31::where("id", $idSolicitud)->first();

        return $this->solicitud;
    }

    public function findSolicitante()
    {
        $this->solicitante = Mercurio07::where(
            "documento",
            $this->solicitud->documento
        )
            ->where("coddoc", $this->solicitud->coddoc)
            ->where("tipo", $this->solicitud->tipo)
            ->first();

        return $this->solicitante;
    }

    /**
     * deshacerAprobacion function
     *
     * @changed [2023-12-19]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     *
     * @param  int  $id
     * @param  string  $action
     * @param  string  $nota
     * @param  string  $codest
     * @param  string  $sendEmail
     * @return bool
     */
    public function deshacerAprobacion($id, $action, $nota, $codest, $sendEmail)
    {
        $trabajadorServices = new TrabajadorServices;
        $notifyEmailServices = new NotifyEmailServices;

        $mercurio31 = $this->findSolicitud($id);

        $ps = Comman::Api();
        $ps->runCli(
            [
                'servicio' => 'ComfacaEmpresas',
                'metodo' => 'informacion_trabajador',
                'params' => $mercurio31->cedtra,
            ]
        );
        if ($ps->isJson() == false) {
            throw new DebugException('Error al buscar al trabajador en Sisuweb', 501);
        }

        $out = $ps->toArray();
        if ($out['success'] == false) {
            throw new DebugException($out['error'], 501);
        }

        $trabajadorSisu = $out['data'];

        $ps = Comman::Api();
        $ps->runCli(
            [
                'servicio' => 'DeshacerAfiliaciones',
                'metodo' => 'deshacer_aprobacion_trabajador',
                'params' => [
                    'nit' => $mercurio31->nit,
                    'cedtra' => $mercurio31->cedtra,
                    'documento' => $mercurio31->documento,
                    'tipo_documento' => $mercurio31->tipdoc,
                    'fecha_aprobacion' => $mercurio31->fecest,
                    'nota' => $nota,
                ],
            ]
        );

        if ($ps->isJson() == false) {
            throw new DebugException('Error al procesar el deshacer la aprobación en SisuWeb.', 501);
        }

        $out = $ps->toArray();
        if ($out['success'] == false) {
            throw new DebugException($out['error'], 501);
        }

        $out = $out['data'];
        if ($action == 'D') {
            $campos_corregir = '';
            $trabajadorServices->devolver($mercurio31, $nota, $codest, $campos_corregir);
            if ($sendEmail == 'S') {
                $notifyEmailServices->emailDevolver($mercurio31, $nota);
            }
        }

        if ($action == 'R') {
            $trabajadorServices->rechazar($mercurio31, $nota, $codest);
            if ($sendEmail == 'S') {
                $notifyEmailServices->emailRechazar($mercurio31, $nota);
            }
        }

        if ($action == 'I') {
            $mercurio31->estado = 'I';
            $mercurio31->fecest = date('Y-m-d');
            $mercurio31->save();
        }

        if ($out['noAction']) {
            $salida = [
                'success' => false,
                'msj' => 'No se realizo ninguna acción, el estado del trabajador no es valido para realizar la acción requerida.',
                'data' => $trabajadorSisu,
            ];
        } else {
            // procesar
            $salida = [
                'data' => $out['trabajador'],
                'success' => ($out['isDelete'] || $out['isDeleteTrayecto']) ? true : false,
                'msj' => ($out['isDelete'] || $out['isDeleteTrayecto']) ? 'Se completo el proceso con éxito.' : 'No se realizo el cambio requerido, se debe comunicar al área de soporte de las TICS.',
            ];
        }

        return $salida;
    }
}
