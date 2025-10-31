<?php

namespace App\Services\Aprueba;

use App\Exceptions\DebugException;
use App\Models\Mercurio01;
use App\Models\Mercurio07;
use App\Models\Mercurio34;
use App\Services\CajaServices\TrabajadorServices;
use App\Services\Entities\BeneficiarioEntity;
use App\Services\Srequest;
use App\Services\Utils\Comman;
use App\Services\Utils\NotifyEmailServices;
use App\Services\Utils\RegistroSeguimiento;
use App\Services\Utils\SenderEmail;
use Carbon\Carbon;

class ApruebaBeneficiario
{
    private $today;

    private $tipopc = '4';

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
        $benefi = Mercurio34::where('id', $this->solicitud->id)->first();
        $hoy = $this->today->format('Y-m-d');
        $trabajador_sisu = false;
        $ps = Comman::Api();
        $ps->runCli(
            [
                'servicio' => 'ComfacaAfilia',
                'metodo' => 'trabajador',
                'params' => [
                    'cedtra' => $benefi->cedtra,
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

        if (is_null($benefi->cedcon) == false && $benefi->cedcon != '') {
            $apiRest = Comman::Api();
            $apiRest->dispatch(
                [
                    'servicio' => 'ComfacaAfilia',
                    'metodo' => 'conyuge',
                    'params' => [
                        'cedcon' => $benefi->cedcon,
                    ]
                ]
            );

            $datos_conyuge = $apiRest->toArray();
            if ($benefi->cedcon != null) {
                if (! isset($datos_conyuge['data']['estado'])) {
                    throw new DebugException('El conyuge del trabajador aún no esta afiliado.', 500);
                }
            }
        }

        /**
         * buscar registro de la empresa
         */
        $params = array_merge($benefi->toArray(), $postData);
        $params['estado'] = 'A';
        $params['documento'] = $benefi->numdoc;
        $params['coddoc'] = $benefi->tipdoc;
        $params['fecest'] = null;
        $params['codest'] = null;
        $params['fecsis'] = $hoy;
        $params['calendario'] = 'N';
        $params['pago'] = 'C';
        $params['ruaf'] = 'N';
        $params['numhij'] = (! $postData['numhij']) ? 0 : $postData['numhij'];

        if ($benefi->tipdoc == 3) {
            throw new DebugException('Error, el tipo documento para independientes no puede ser tipo NIT.', 501);
        }

        $entity = new BeneficiarioEntity;
        $entity->create($params);
        if (! $entity->validate()) {
            throw new DebugException(
                'Error, no se puede crear el beneficiario por validación previa.',
                501,
                [
                    'errors' => $entity->getValidationErrors(),
                    'attributes' => $entity->getData(),
                ]
            );
        }
        /**
         * la empresa se debe registrar con el tipo de documento correspondiente y no con el tipo del registro de solicitud
         */
        $ps = Comman::Api();
        $ps->runCli(
            [
                'servicio' => 'ComfacaAfilia',
                'metodo' => 'afilia_beneficiario',
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
            throw new DebugException($out['message'], 501, [
                'command' => $ps->getLineaComando(),
            ]);
        }

        $registroSeguimiento = new RegistroSeguimiento;
        $registroSeguimiento->crearNota($this->tipopc, $this->solicitud->id, $postData['nota_aprobar'], 'A');
        /**
         * actualiza la ficha de registro
         */
        $benefi->motivo = $postData['nota_aprobar'];
        $benefi->estado = 'A';
        $benefi->fecest = $hoy;
        $benefi->save();

        return true;
    }

    /**
     * enviarMail function
     *
     * @param [type] $Mercurio34
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
        $data['msj'] = "Se informa que el beneficiario {$nombre}, con número de documento de indetificación {$this->solicitud->numdoc} fue afiliado con éxito.";

        $html = view('emails.mail_aprobar', $data)->render();
        $asunto = "Afiliación beneficiario realizada con éxito, identificación {$this->solicitud->numdoc}";

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
            $this->solicitante->email,
            $html
        );

        return true;
    }

    public function findSolicitud($idSolicitud)
    {
        $this->solicitud = Mercurio34::where('id', $idSolicitud)->first();
        return $this->solicitud;
    }

    public function findSolicitante()
    {
        $this->solicitante = Mercurio07::where("documento", $this->solicitud->documento)
            ->where("coddoc", $this->solicitud->coddoc)
            ->where("tipo", $this->solicitud->tipo)
            ->first();
        return $this->solicitante;
    }

    /**
     * deshacerAprobacion function
     * @changed [2023-12-19]
     * @author elegroag <elegroag@ibero.edu.co>
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

        $mercurio34 = $this->findSolicitud($id);

        $ps = Comman::Api();
        $ps->runCli(
            [
                'servicio' => 'ComfacaEmpresas',
                'metodo' => 'informacion_beneficiario',
                'params' => $mercurio34->numdoc,
            ],
            false
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
                'metodo' => 'deshacerAprobacionTrabajador',
                'params' => [
                    'nit' => $mercurio34->nit,
                    'cedtra' => $mercurio34->cedtra,
                    'documento' => $mercurio34->numdoc,
                    'tipo_documento' => $mercurio34->tipdoc,
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
            $trabajadorServices->devolver($mercurio34, $nota, $codest, $campos_corregir);
            if ($sendEmail == 'S') {
                $notifyEmailServices->emailDevolver($mercurio34, $nota);
            }
        }

        if ($action == 'R') {
            $trabajadorServices->rechazar($mercurio34, $nota, $codest);
            if ($sendEmail == 'S') {
                $notifyEmailServices->emailRechazar($mercurio34, $nota);
            }
        }

        if ($action == 'I') {
            $mercurio34->estado = 'I';
            $mercurio34->fecest = date('Y-m-d');
            $mercurio34->save();
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
