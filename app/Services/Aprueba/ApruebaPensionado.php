<?php

namespace App\Services\Aprueba;

use App\Exceptions\DebugException;
use App\Models\Mercurio01;
use App\Models\Mercurio07;
use App\Models\Mercurio38;
use App\Services\Entities\ListasEntity;
use App\Services\Entities\PensionadoEntity;
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

class ApruebaPensionado
{
    private $today;

    private $tipopc = 9;

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
        $mercurio38 = (new Mercurio38)->findFirst("id='{$this->solicitud->getId()}'");
        $hoy = $this->today->format('Y-m-d');
        /**
         * buscar registro de la empresa
         */
        $fullname = $this->solicitud->getPriape().' '.$this->solicitud->getSegape().' '.$this->solicitud->getPrinom().' '.$this->solicitud->getSegnom();
        $tipper = 'N';
        $params = array_merge($this->solicitud->getArray(), $postData);

        /**
         * valida indice de aportes
         * 07 => aportes del 0.2% pensionados
         * 49 => aportes del 0.6% pensionados
         * 48 => aportes del 0% pensionados
         * 47 => aportes del pensionado Fidelidad
         */
        if ((
            $params['codind'] == '49' ||
            $params['codind'] == '47' ||
            $params['codind'] == '48' ||
            $params['codind'] == '07') == false) {
            throw new Exception('Error, el indice de aportes no es valido para pensionados', 501);
        }

        if ($this->solicitud->getTipdoc() == 3) {
            throw new Exception('Error, el tipo documento para pensionado no puede ser tipo NIT.', 501);
        }

        if ($params['codind'] == '07') {
            $tipcot = 10;
        }
        if ($params['codind'] == '47') {
            $tipcot = 66;
        }
        if ($params['codind'] == '48') {
            $tipcot = 67;
        }
        if ($params['codind'] == '49') {
            $tipcot = 64;
        }

        $params['estado'] = 'A';
        $params['fecest'] = null;
        $params['codest'] = null;
        $params['tipper'] = $tipper;
        $params['tipapo'] = 'O';
        $params['calsuc'] = $this->solicitud->getCalemp();
        $params['celpri'] = $this->solicitud->getCelular();
        $params['emailpri'] = $this->solicitud->getEmail();

        $repleg = $this->solicitud->getPriape().' '
            .$this->solicitud->getSegape().' '
            .$this->solicitud->getPrinom().' '
            .$this->solicitud->getSegnom();

        $params['repleg'] = $repleg;
        $params['razsoc'] = $repleg;

        $params['nit'] = $this->solicitud->getCedtra();
        $params['coddoc'] = $this->solicitud->getTipdoc();
        $params['digver'] = '0';
        $params['tottra'] = '1';
        $params['fax'] = '0';

        if (! $params['codsuc']) {
            $params['codsuc'] = '020';
        }
        if (! $params['codsuc']) {
            $params['codlis'] = '020';
        }
        if ($params['codsuc']) {
            $params['codlis'] = $params['codsuc'];
        }

        $params['nomcon'] = substr($this->solicitud->getPriape().' '.$this->solicitud->getSegape(), 0, 39);
        $params['codase'] = '1';
        $params['resest'] = null;
        $params['fecmer'] = null;
        $params['feccor'] = null;
        $params['valmor'] = null;
        $params['permor'] = null;
        $params['giass'] = null;
        $params['actugp'] = null;
        $params['jefper'] = null;
        $params['cedpro'] = null;
        $params['nompro'] = null;
        $params['totapo'] = '0';
        $params['totcon'] = '0';
        $params['tothij'] = '0';
        $params['tother'] = '0';
        $params['totpad'] = '0';
        $params['correo'] = 'N';
        $params['agro'] = 'N';
        $params['benef'] = 'N';
        $params['carnet'] = 'N';
        $params['empleador'] = 'N';
        $params['feccer'] = $hoy;
        $params['fecpre'] = $params['fecafi'];

        $params['giro'] = (isset($params['giro']) && $params['giro'] != '') ? $params['giro'] : 'N';
        $params['giro2'] = $params['giro'];
        $params['codgir'] = (isset($params['codgir'])) ? $params['codgir'] : 'NU';
        $params['codgir2'] = $params['codgir'];

        $params['ruaf'] = 'N';
        $params['tipcon'] = 'F';
        $params['tipcot'] = "{$tipcot}";
        $params['vendedor'] = 'N';
        $params['fecsis'] = $hoy;
        $params['horas'] = '240';
        $params['ofiafi'] = '13';
        $params['detalle'] = $repleg;
        $params['fecsal'] = $params['fecafi'];

        $params['traapo'] = '0';
        $params['valapo'] = '0';
        $params['tottra'] = '0';
        $params['tietra'] = '0';
        $params['tratot'] = '0';
        $params['coddiv'] = $params['codciu'];

        if (! $params['tippag'] || $params['tippag'] == 'T') {
            $params['numcue'] = '0';
            $params['tippag'] = 'T';
            $params['codban'] = null;
            $params['tipcue'] = null;
        }

        /**
         * tipo de sociedad por defecto es persona natural para pensionados
         */
        $params['tipsoc'] = ($params['tipsoc'] == '') ? '06' : $params['tipsoc'];

        $entity = new PensionadoEntity;
        $entity->create($params);
        if (! $entity->validate()) {
            throw new DebugException(
                'Error, no se puede crear el trabajador pensionado por validación previa.',
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
                'metodo' => 'afilia_pensionado',
                'params' => [
                    'post' => array_merge($entity->getData(), $sucursal->getData(), $listas->getData(), $trabajador->getData()),
                ],
            ]
        );

        if ($ps->isJson() == false) {
            throw new DebugException('Error, no hay respuesta del servidor para validación del resultado.', 501);
        }
        $out = $ps->toArray();

        if (is_null($out)) {
            throw new DebugException('Error, no hay respuesta del servidor para validación del resultado.', 501);
        }

        if ($out['success'] == false) {
            throw new DebugException($out['msj'], 501);
        }

        $registroSeguimiento = new RegistroSeguimiento;
        $registroSeguimiento->crearNota($this->tipopc, $this->solicitud->getId(), $postData['nota_aprobar'], 'A');

        /**
         * Crea de una vez e registro, permitiendo que el usuario entre con la misma password
         * como empresa sin tener que hacer la solicitud de clave
         */
        $empresa = (new Mercurio07)->findFirst("coddoc='{$this->solicitud->getTipdoc()}' and documento='{$this->solicitud->getNit()}' and tipo='P'");
        $feccla = $this->solicitante->getFeccla();
        $fecreg = $this->solicitante->getFecreg();
        $fecapr = $postData['fecapr'];

        $crearUsuario = new CrearUsuario(
            new Srequest(
                [
                    'tipo' => 'O',
                    'coddoc' => $this->solicitud->getTipdoc(),
                    'documento' => $this->solicitud->getNit(),
                    'nombre' => $fullname,
                    'email' => $this->solicitud->getEmail(),
                    'codciu' => $this->solicitud->getCodciu(),
                    'autoriza' => $this->solicitante->getAutoriza(),
                    'clave' => $this->solicitante->getClave(),
                    'fecreg' => $fecreg->getUsingFormatDefault(),
                    'feccla' => $feccla->getUsingFormatDefault(),
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
        $mercurio38->setEstado('A');
        $mercurio38->setFecest($hoy);
        $mercurio38->setTipper($tipper);
        $mercurio38->setFecapr($postData['fecapr']);
        $mercurio38->save();

        return true;
    }

    /**
     * enviarMail function
     *
     * @param [type] $Mercurio38
     * @param [type] $actapr
     * @param [type] $feccap
     * @return bool
     */
    public function enviarMail($actapr, $feccap)
    {
        $meses = [
            'Enero',
            'Febrero',
            'Marzo',
            'Abril',
            'Mayo',
            'Junio',
            'Julio',
            'Agosto',
            'Septiembre',
            'Octubre',
            'Noviembre',
            'Diciembre',
        ];

        $feccap = new DateTime($feccap);
        $dia = $feccap->format('d');
        $mes = $meses[intval($feccap->format('m') - 1)];
        $anno = $feccap->format('Y');

        $data = $this->solicitud->getArray();
        $data['membrete'] = "{$this->dominio}public/img/membrete_aprueba.jpg";
        $data['ruta_firma'] = "{$this->dominio}Mercurio/public/img/Mercurio/firma_jefe_yenny.jpg";
        $data['actapr'] = $actapr;
        $data['dia'] = $dia;
        $data['mes'] = $mes;
        $data['anno'] = $anno;

        $html = view('layouts/aprobar', $data)->render();
        $asunto = "Afiliación trabajador pensionado realizada con éxito, identificación {$this->solicitud->getNit()}";
        $emailCaja = (new Mercurio01)->findFirst();
        $senderEmail = new SenderEmail;

        $senderEmail->setters(
            "emisor_email: {$emailCaja->getEmail()}",
            "emisor_clave: {$emailCaja->getClave()}",
            "asunto: {$asunto}"
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
        $this->solicitud = (new Mercurio38)->findFirst("id='{$idSolicitud}'");

        return $this->solicitud;
    }

    public function findSolicitante()
    {
        $this->solicitante = (new Mercurio07)->findFirst("documento='{$this->solicitud->getDocumento()}' and coddoc='{$this->solicitud->getCoddoc()}' and tipo='{$this->solicitud->getTipo()}'");

        return $this->solicitante;
    }
}
