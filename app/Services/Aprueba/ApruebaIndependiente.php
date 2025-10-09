<?php

namespace App\Services\Aprueba;

use App\Exceptions\DebugException;
use App\Models\Mercurio01;
use App\Models\Mercurio07;
use App\Models\Mercurio41;
use App\Services\Entities\IndependienteEntity;
use App\Services\Entities\ListasEntity;
use App\Services\Entities\SucursalEntity;
use App\Services\Entities\TrabajadorEntity;
use App\Services\Srequest;
use App\Services\Utils\Comman;
use App\Services\Utils\CrearUsuario;
use App\Services\Utils\RegistroSeguimiento;
use App\Services\Utils\SenderEmail;
use Carbon\Carbon;
use DateTime;
use Exception;

class ApruebaIndependiente
{
    private $today;

    private $tipopc = 13;

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
        $mercurio41 = (new Mercurio41)->findFirst("id='{$this->solicitud->getId()}'");
        $hoy = $this->today->format('Y-m-d');
        /**
         * buscar registro de la empresa
         */
        $repleg = $this->solicitud->getPriape().' '
            .$this->solicitud->getSegape().' '
            .$this->solicitud->getPrinom().' '
            .$this->solicitud->getSegnom();

        $tipper = 'N';
        $params = array_merge($this->solicitud->getArray(), $postData);
        /**
         * valida indice de aportes
         * 14 => aportes del 0.2% independiente
         * 50 => aportes del 0.6% independiente
         */
        if (($params['codind'] == '50' || $params['codind'] == '14') == false) {
            throw new Exception('Error, el indice de aportes no es valido para independientes', 501);
        }

        if ($this->solicitud->getTipdoc() == 3) {
            throw new Exception('Error, el tipo documento para independientes no puede ser tipo NIT.', 501);
        }

        if ($params['codind'] == '14') {
            $tipcot = 3;
        }
        if ($params['codind'] == '50') {
            $tipcot = 65;
        }

        $fullname = $this->solicitud->getPriape().' '.$this->solicitud->getSegape().' '.$this->solicitud->getPrinom().' '.$this->solicitud->getSegnom();
        $params['nit'] = $this->solicitud->getCedtra();
        $params['coddoc'] = $this->solicitud->getTipdoc();
        $params['estado'] = 'A';
        $params['fecest'] = null;
        $params['codest'] = null;
        $params['tipper'] = $tipper;
        $params['tipapo'] = 'I';
        $params['tipsoc'] = '08';
        $params['celpri'] = $this->solicitud->getCelular();
        $params['emailpri'] = $this->solicitud->getEmail();
        $params['repleg'] = $repleg;
        $params['razsoc'] = $params['repleg'];
        $params['fax'] = '0';
        $params['ofiafi'] = '13';
        $params['horas'] = '240';

        if (! $params['codsuc']) {
            $params['codsuc'] = '010';
        }
        if (! $params['codsuc']) {
            $params['codlis'] = '010';
        }
        if ($params['codsuc']) {
            $params['codlis'] = $params['codsuc'];
        }

        $params['digver'] = '0';
        $params['tottra'] = '1';
        $params['nomcon'] = substr($this->solicitud->getPriape().' '.$this->solicitud->getSegape(), 0, 39);
        $params['fecsis'] = $hoy; // fecha captura del sistema
        $params['feccam'] = $hoy; // fecha de actualizacion
        $params['codase'] = '1';
        $params['estado'] = 'A';
        $params['telt'] = $this->solicitud->getCelular();
        $params['telr'] = $this->solicitud->getTelefono();
        $params['mailr'] = $this->solicitud->getEmail();
        $params['calsuc'] = $this->solicitud->getCalemp();
        $params['detalle'] = $repleg;
        $params['nomemp'] = $repleg;
        $params['fecapr'] = $postData['fecapr'];
        $params['observacion'] = $postData['nota_aprobar'];
        $params['totapo'] = '0';
        $params['totcon'] = '0';
        $params['tothij'] = '0';
        $params['tother'] = '0';
        $params['totpad'] = '0';
        $params['jefper'] = null;
        $params['cedpro'] = null;
        $params['nompro'] = null;
        $params['feccer'] = $hoy;
        $params['resest'] = null;
        $params['fecmer'] = null;
        $params['feccor'] = null;
        $params['valmor'] = null;
        $params['permor'] = null;
        $params['giass'] = null;
        $params['actugp'] = null;
        $params['correo'] = 'N';
        $params['agro'] = 'N';
        $params['benef'] = 'N';
        $params['carnet'] = 'N';
        $params['empleador'] = 'N';
        $params['fecpre'] = $params['fecafi'];
        $params['ruaf'] = 'N';
        $params['tipcon'] = 'F';
        $params['tipcot'] = "{$tipcot}";
        $params['vendedor'] = 'N';
        $params['fecsal'] = $params['fecafi'];
        $params['subpla'] = $postData['codsuc'];
        $params['coddiv'] = $params['codciu'];

        $params['horas'] = '240';
        if (! $this->solicitud->getPub_indigena_id()) {
            $params['pub_indigena_id'] = 2;
        }
        if (! $this->solicitud->getResguardo_id()) {
            $params['resguardo_id'] = 2;
        }

        $params['traapo'] = '0';
        $params['valapo'] = '0';
        $params['tottra'] = '0';
        $params['tietra'] = '0';
        $params['tratot'] = '0';

        $params['giro'] = (isset($params['giro']) && $params['giro'] != '') ? $params['giro'] : 'N';
        $params['giro2'] = $params['giro'];
        $params['codgir'] = (isset($params['codgir'])) ? $params['codgir'] : 'NU';
        $params['codgir2'] = $params['codgir'];

        if (! $params['tippag'] || $params['tippag'] == 'T') {
            $params['numcue'] = '0';
            $params['tippag'] = 'T';
            $params['codban'] = null;
            $params['tipcue'] = null;
        }

        $entity = new IndependienteEntity;
        $entity->create($params);
        if (! $entity->validate()) {
            throw new DebugException(
                'Error, no se puede crear el trabajador independiente por validación previa.',
                501,
                [
                    'errors' => $entity->getValidationErrors(),
                    'attributes' => $entity->getData(),
                ]
            );
        }

        $sucursal = new SucursalEntity;
        $sucursal->create($params);
        if (! $sucursal->validate()) {
            throw new DebugException(
                'Error, no se puede crear la sucursal por validación previa.',
                501,
                [
                    'errors' => $sucursal->getValidationErrors(),
                    'attributes' => $sucursal->getData(),
                ]
            );
        }

        $listas = new ListasEntity;
        $listas->create($params);
        if (! $listas->validate()) {
            throw new DebugException(
                'Error, no se puede crear la lista por validación previa.',
                501,
                [
                    'errors' => $listas->getValidationErrors(),
                    'attributes' => $listas->getData(),
                ]
            );
        }

        $trabajador = new TrabajadorEntity;
        $trabajador->create($params);
        if (! $trabajador->validate()) {
            throw new DebugException(
                'Error, no se puede crear el trabajador por validación previa.',
                501,
                [
                    'errors' => $trabajador->getValidationErrors(),
                    'attributes' => $trabajador->getData(),
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
                'metodo' => 'afilia_independiente',
                'params' => [
                    'post' => array_merge($entity->getData(), $sucursal->getData(), $listas->getData(), $trabajador->getData()),
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
         * Crea de una vez e registro, permitiendo que el usuario entre con la misma password
         * como empresa sin tener que hacer la solicitud de clave
         */
        $empresa = (new Mercurio07)->findFirst("coddoc='{$this->solicitud->getTipdoc()}' and documento='{$this->solicitud->getCedtra()}' and tipo='I'");

        $fecreg = $this->solicitante->getFecreg();
        $feccla = $this->solicitante->getFeccla();
        $fecapr = $postData['fecapr'];

        $crearUsuario = new CrearUsuario(
            new Srequest(
                [
                    'tipo' => 'I',
                    'coddoc' => $this->solicitud->getTipdoc(),
                    'documento' => $this->solicitud->getCedtra(),
                    'nombre' => $fullname,
                    'email' => $this->solicitud->getEmail(),
                    'codciu' => $this->solicitud->getCodciu(),
                    'autoriza' => $this->solicitante->getAutoriza(),
                    'clave' => $this->solicitante->getClave(),
                    'fecreg' => $fecreg->getUsingFormatDefault(),
                    'feccla' => $feccla,
                    'fecapr' => $fecapr,
                ]
            )
        );

        $crearUsuario->procesar();
        if ($empresa == false) {
            $code_verify = $crearUsuario->generaCode();
            $crearUsuario->crearOpcionesRecuperacion($code_verify);
        }

        /**
         * actualiza la ficha de registro
         */
        $mercurio41->setEstado('A');
        $mercurio41->setFecest($hoy);
        $mercurio41->setFecapr($postData['fecapr']);
        $mercurio41->save();

        return true;
    }

    /**
     * enviarMail function
     *
     * @param [type] $Mercurio41
     * @param [type] $actapr
     * @param [type] $feccap
     * @return bool
     */
    public function enviarMail($actapr, $feccap)
    {
        $feccap = new DateTime($feccap);
        $dia = $feccap->format('d');
        $mes = get_mes_name($feccap->format('m'));
        $anno = $feccap->format('Y');

        $data = $this->solicitud->getArray();
        $data['membrete'] = "{$this->dominio}public/img/membrete_aprueba.jpg";
        $data['ruta_firma'] = "{$this->dominio}Mercurio/public/img/Mercurio/firma_jefe_yenny.jpg";
        $data['actapr'] = $actapr;
        $data['dia'] = $dia;
        $data['mes'] = $mes;
        $data['anno'] = $anno;
        $data['repleg'] = $this->solicitante->getNombre();
        $data['razsoc'] = $this->solicitante->getNombre();

        $html = view('layouts/aprobar', $data)->render();
        $asunto = "Afiliación trabajador independiente realizada con éxito, identificación {$this->solicitud->getCedtra()}";

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

    /**
     * findSolicitud function
     *
     * @changed [2023-12-00]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     *
     * @param  int  $id
     * @return void
     */
    public function findSolicitud($id)
    {
        $this->solicitud = (new Mercurio41)->findFirst(" id='{$id}'");

        return $this->solicitud;
    }

    public function findSolicitante()
    {
        $this->solicitante = (new Mercurio07)->findFirst(
            "documento='{$this->solicitud->getDocumento()}' and coddoc='{$this->solicitud->getCoddoc()}' and tipo='{$this->solicitud->getTipo()}'"
        );

        return $this->solicitante;
    }
}
