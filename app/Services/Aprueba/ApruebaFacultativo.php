<?php

namespace App\Services\Aprueba;

use App\Exceptions\DebugException;
use App\Models\Mercurio01;
use App\Models\Mercurio07;
use App\Models\Mercurio36;
use App\Services\Entities\IndependienteEntity;
use App\Services\Entities\ListasEntity;
use App\Services\Entities\SucursalEntity;
use App\Services\Entities\TrabajadorEntity;
use App\Services\Srequest;
use App\Services\Api\ApiSubsidio;
use App\Services\Utils\CrearUsuario;
use App\Services\Utils\RegistroSeguimiento;
use App\Services\Utils\SenderEmail;
use Carbon\Carbon;
use DateTime;
use Exception;

class ApruebaFacultativo
{
    private $today;

    private $tipopc = '10';

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
     * @param [type] $solicitante
     * @param [type] $postData
     * @return bool
     */
    public function procesar($postData)
    {
        $mercurio36 = Mercurio36::where("id", $this->solicitud->id)->first();
        $hoy = $this->today->format('Y-m-d');

        /**
         * buscar registro de la empresa
         */
        $tipper = 'N';
        $params = array_merge($this->solicitud->toArray(), $postData);
        if ($params['codind'] != '46') {
            throw new Exception('Error, el indice de aportes no es valido para facultativos', 501);
        }

        if ($this->solicitud->tipdoc == 3) {
            throw new Exception('Error, el tipo documento para facultativos no puede ser tipo NIT.', 501);
        }

        $fullname = $this->solicitud->priape . ' ' . $this->solicitud->segape . ' ' . $this->solicitud->prinom . ' ' . $this->solicitud->segnom;
        $tipcot = 63;

        $params['tipsoc'] = '06';
        $params['digver'] = '0';
        $params['nit'] = $this->solicitud->cedtra;
        $params['coddoc'] = $this->solicitud->tipdoc;
        $params['calsuc'] = $this->solicitud->calemp;
        $params['estado'] = 'A';
        $params['fecest'] = null;
        $params['codest'] = null;
        $params['tipper'] = $tipper;
        $params['celpri'] = $this->solicitud->celular;
        $params['emailpri'] = $this->solicitud->email;
        $params['repleg'] = $fullname;
        $params['razsoc'] = $fullname;
        $params['fax'] = '0';
        $params['ofiafi'] = '13';
        $params['horas'] = '240';
        $params['calemp'] = 'F';
        $params['tipapo'] = 'F';

        if (! $params['codsuc']) {
            $params['codsuc'] = '030';
        }
        if (! $params['codsuc']) {
            $params['codlis'] = '030';
        }
        if ($params['codsuc']) {
            $params['codlis'] = $params['codsuc'];
        }
        $params['subpla'] = $postData['codsuc'];
        $params['nomcon'] = substr($this->solicitud->priape . ' ' . $this->solicitud->segape, 0, 39);
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
        $params['detalle'] = $fullname;
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

        $entity = new IndependienteEntity;
        $entity->create($params);
        if (! $entity->validate()) {
            throw new DebugException(
                'Error, no se puede crear el trabajador facultativo por validación previa.',
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
        $ps = new ApiSubsidio();
        $ps->send(
            [
                'servicio' => 'ComfacaAfilia',
                'metodo' => 'afilia_facultativo',
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
        $registroSeguimiento->crearNota($this->tipopc, $this->solicitud->id, $postData['nota_aprobar'], 'A');

        /**
         * Crea de una vez e registro, permitiendo que el usuario entre con la misma password
         * como empresa sin tener que hacer la solicitud de clave
         */
        $empresa = Mercurio07::where("coddoc", $this->solicitud->tipdoc)
            ->where("documento", $this->solicitud->cedtra)
            ->where("tipo", $this->solicitud->tipo)
            ->first();

        $feccla = $this->solicitante->feccla;
        $fecreg = $this->solicitante->fecreg;
        $fecapr = $postData['fecapr'];

        $crearUsuario = new CrearUsuario(
            new Srequest(
                [
                    'tipo' => 'F',
                    'coddoc' => $this->solicitud->tipdoc,
                    'documento' => $this->solicitud->cedtra,
                    'nombre' => $fullname,
                    'email' => $this->solicitud->email,
                    'codciu' => $this->solicitud->codciu,
                    'autoriza' => $this->solicitante->autoriza,
                    'clave' => $this->solicitante->clave,
                    'fecreg' => $fecreg,
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
        $mercurio36->estado = 'A';
        $mercurio36->fecest = $hoy;
        $mercurio36->fecapr = $postData['fecapr'];
        $mercurio36->save();

        return true;
    }

    /**
     * enviarMail function
     *
     * @param [type] $this->solicitud
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
        $data['repleg'] = $this->solicitante->nombre;
        $data['razsoc'] = $this->solicitante->nombre;

        $html = view('cajas.layouts.aprobar', $data)->render();
        $asunto = "Afiliación de facultativo realizada con éxito, identificación {$this->solicitud->cedtra}";
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
        $this->solicitud = Mercurio36::where("id", $idSolicitud)->first();
        return $this->solicitud;
    }

    public function findSolicitante()
    {
        $this->solicitante = Mercurio07::where("documento", $this->solicitud->cedtra)
            ->where("coddoc", $this->solicitud->tipdoc)
            ->where("tipo", $this->solicitud->tipo)
            ->first();

        return $this->solicitante;
    }
}
