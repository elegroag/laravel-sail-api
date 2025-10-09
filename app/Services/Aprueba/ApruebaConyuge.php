<?php

namespace App\Services\Aprueba;

use App\Exceptions\DebugException;
use App\Models\Mercurio01;
use App\Models\Mercurio07;
use App\Models\Mercurio32;
use App\Services\CajaServices\TrabajadorServices;
use App\Services\Entities\ConyugeEntity;
use App\Services\Srequest;
use App\Services\Utils\Comman;
use App\Services\Utils\NotifyEmailServices;
use App\Services\Utils\RegistroSeguimiento;
use App\Services\Utils\SenderEmail;
use Carbon\Carbon;

class ApruebaConyuge
{
    private $today;

    private $tipopc = 3;

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
     * @param [type] $postData
     * @return bool
     */
    public function procesar($postData)
    {
        $conyuge = (new Mercurio32)->findFirst("id='{$this->solicitud->getId()}'");

        $hoy = $this->today->format('Y-m-d');

        $params = array_merge($this->solicitud->getArray(), $postData);
        $params['estado'] = 'A';
        $params['fecest'] = null;
        $params['codest'] = null;
        $params['coddoc'] = $this->solicitud->getTipdoc();
        $params['direccion'] = 'CR';
        $params['fecsal'] = null;
        $params['ruaf'] = 'N';
        $params['salario'] = '0';

        if ($this->solicitud->getTipdoc() == 3) {
            throw new DebugException('Error, el tipo documento para independientes no puede ser tipo NIT.', 501);
        }

        $trabajador_sisu = false;
        $ps = Comman::Api();
        $ps->runCli(
            [
                'servicio' => 'ComfacaAfilia',
                'metodo' => 'trabajador',
                'params' => [
                    'cedtra' => $conyuge->getCedtra(),
                    'estado' => 'A',
                ],
            ]
        );
        $rqs = $ps->toArray();
        if (! empty($rqs)) {
            $trabajador_sisu = ($rqs['success']) ? $rqs['data'] : false;
        }

        if (! $trabajador_sisu) {
            throw new DebugException('El trabajador aun no está activo en el sistema principal de subsidio.', 505);
        }
        /**
         * la empresa se debe registrar con el tipo de documento correspondiente y no con el tipo del registro de solicitud
         */
        $entity = new ConyugeEntity;
        $entity->create($params);
        if (! $entity->validate()) {
            throw new DebugException(
                'Error, no se puede crear el conyuge por validación previa.',
                501
            );
        }

        $ps = Comman::Api();
        $ps->runCli(
            [
                'servicio' => 'ComfacaAfilia',
                'metodo' => 'afilia_conyuge',
                'params' => [
                    'post' => $params,
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
        $registroSeguimiento->crearNota($this->tipopc, $this->solicitud->getId(), $postData['nota_aprobar'], 'A');
        /**
         * actualiza la ficha de registro
         */
        $conyuge->setMotivo($postData['nota_aprobar']);
        $conyuge->setEstado('A');
        $conyuge->setFecest($hoy);
        $conyuge->save();

        return true;
    }

    /**
     * enviarMail function
     *
     * @param [type] $Mercurio32
     * @param [type] $actapr
     * @param [type] $feccap
     * @return bool
     */
    public function enviarMail($actapr = '', $feccap = '')
    {
        $nombre = $this->solicitud->getPrinom().' '.$this->solicitud->getSegnom().' '.$this->solicitud->getPriape().' '.$this->solicitud->getSegape();
        $data = [];
        $data['razsoc'] = $this->solicitante->getNombre();
        $data['email'] = $this->solicitante->getEmail();
        $data['membrete'] = "{$this->dominio}/public/img/header_reporte_ugpp.png";
        $data['ruta_firma'] = "{$this->dominio}Mercurio/public/img/Mercurio/firma_jefe_yenny.jpg";
        $data['actapr'] = $actapr;
        $data['url_activa'] = '';
        $data['msj'] = "Se informa que el beneficiario {$nombre}, con número de documento de indetificación {$this->solicitud->getCedcon()} fue afiliado con éxito.";

        $html = view('layouts/mail_aprobar', $data)->render();
        $asunto = "Afiliación beneficiario realizada con éxito, identificación {$this->solicitud->getCedcon()}";
        $emailCaja = (new Mercurio01)->findFirst();

        $senderEmail = new SenderEmail(
            new Srequest(
                [
                    'emisor_email' => $emailCaja->getEmail(),
                    'emisor_clave' => $emailCaja->getClave(),
                    'asunto' => $asunto,
                ]
            )
        );

        $senderEmail->send([
            [
                'email' => $this->solicitante->getEmail(),
                'nombre' => $this->solicitante->getNombre(),
            ],
        ], $html);

        return true;
    }

    public function findSolicitud($idSolicitud)
    {
        $this->solicitud = (new Mercurio32)->findFirst(" id='{$idSolicitud}'");

        return $this->solicitud;
    }

    public function findSolicitante()
    {
        $this->solicitante = (new Mercurio07)->findFirst(
            "documento='{$this->solicitud->getDocumento()}' and coddoc='{$this->solicitud->getCoddoc()}' and tipo='{$this->solicitud->getTipo()}'"
        );

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

        $mercurio32 = $this->findSolicitud($id);

        $ps = Comman::Api();
        $ps->runCli(
            [
                'servicio' => 'ComfacaEmpresas',
                'metodo' => 'informacion_conyuge',
                'params' => [
                    'cedcon' => $mercurio32->getCedcon(),
                ],
            ]
        );
        if ($ps->isJson() == false) {
            throw new DebugException('Error al buscar al conyuge en Sisuweb', 501);
        }

        $out = $ps->toArray();
        if ($out['success'] == false) {
            throw new DebugException($out['error'], 501);
        }

        $dataSisu = $out['data'];

        $ps = Comman::Api();
        $ps->runCli(
            [
                'servicio' => 'DeshacerAfiliaciones',
                'metodo' => 'deshacerAprobacionTrabajador',
                'params' => [
                    'nit' => $mercurio32->getNit(),
                    'cedtra' => $mercurio32->getCedtra(),
                    'documento' => $mercurio32->getCedcon(),
                    'tipo_documento' => $mercurio32->getTipdoc(),
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
            $trabajadorServices->devolver($mercurio32, $nota, $codest, $campos_corregir);
            if ($sendEmail == 'S') {
                $notifyEmailServices->emailDevolver($mercurio32, $nota);
            }
        }

        if ($action == 'R') {
            $trabajadorServices->rechazar($mercurio32, $nota, $codest);
            if ($sendEmail == 'S') {
                $notifyEmailServices->emailRechazar($mercurio32, $nota);
            }
        }

        if ($action == 'I') {
            $mercurio32->setEstado('I');
            $mercurio32->setFecest(date('Y-m-d'));
            $mercurio32->save();
        }

        if ($out['noAction']) {
            $salida = [
                'success' => false,
                'msj' => 'No se realizo ninguna acción, el estado del trabajador no es valido para realizar la acción requerida.',
                'data' => $dataSisu,
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
