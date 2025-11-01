<?php

namespace App\Services\Aprueba;

use App\Exceptions\DebugException;
use App\Models\Mercurio01;
use App\Models\Mercurio07;
use App\Models\Mercurio30;
use App\Services\Entities\EmpresaEntity;
use App\Services\Entities\ListasEntity;
use App\Services\Entities\SucursalEntity;
use App\Services\SatApi\SatServices;
use App\Services\Srequest;
use App\Services\Api\ApiSubsidio;
use App\Services\Utils\CrearUsuario;
use App\Services\Utils\RegistroSeguimiento;
use App\Services\Utils\SenderEmail;
use Carbon\Carbon;
use DateTime;
use Exception;

class ApruebaEmpresa
{
    private $today;

    private $tipopc = '2';

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
        $mercurio30 = Mercurio30::where('id', $this->solicitud->id)->first();
        $hoy = $this->today->format('Y-m-d');
        /**
         * buscar registro de la empresa
         */
        $tipper = ($this->solicitud->tipdoc == 3) ? 'J' : 'N';
        $params = array_merge($this->solicitud->toArray(), $postData);

        /**
         * valida indice de aportes de empresas
         */
        if (
            $params['codind'] == '49' ||
            $params['codind'] == '07' ||
            $params['codind'] == '46' ||
            $params['codind'] == '50' ||
            $params['codind'] == '14'
        ) {
            throw new Exception('Error, el indice de aportes no es valido para empresas aportantes', 501);
        }

        $params['estado'] = 'A';
        $params['fecest'] = null;
        $params['codest'] = null;
        $params['tipper'] = $tipper;
        $params['coddoc'] = $mercurio30->tipdoc;
        $params['fecsis'] = $hoy; // fecha captura del sistema
        $params['feccam'] = $hoy; // fecha de actualizacion
        $params['estado'] = 'A';
        $params['telt'] = $mercurio30->celpri;
        $params['telr'] = $mercurio30->telefono;
        $params['mailr'] = $mercurio30->emailpri;
        $params['calsuc'] = $mercurio30->calemp;
        $params['nomcon'] = substr($mercurio30->priape . ' ' . $mercurio30->segape, 0, 40);
        $params['detalle'] = $mercurio30->razsoc;
        $params['nomemp'] = $mercurio30->razsoc;
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
        $params['traapo'] = '0';
        $params['valapo'] = '0';
        $params['tottra'] = '0';
        $params['tietra'] = '0';
        $params['tratot'] = '0';
        $params['subpla'] = $params['codsuc'];
        $params['codlis'] = $params['codsuc'];
        $params['coddiv'] = $params['codciu'];
        $params['fosfec'] = 'N';
        $params['codase'] = '09';
        $params['todmes'] = 'S';

        $empresa = new EmpresaEntity;
        $empresa->create($params);
        if (! $empresa->validate()) {
            throw new DebugException(
                'Error, no se puede crear la empresa por validación previa.',
                501,
                [
                    'errors' => $empresa->getValidationErrors(),
                    'attributes' => $empresa->getData(),
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

        /**
         * la empresa se debe registrar con el tipo de documento correspondiente y no con el tipo del registro de solicitud
         */
        $ps = new ApiSubsidio();
        $ps->send(
            [
                'servicio' => 'ComfacaAfilia',
                'metodo' => 'afilia_empresa',
                'params' => [
                    'post' => array_merge($empresa->getData(), $sucursal->getData(), $listas->getData()),
                ],
            ]
        );
        if ($ps->isJson() == false) {
            throw new DebugException('Error, no hay respuesta del servidor para validación del resultado.', 1);
        }
        $out = $ps->toArray();

        if (is_null($out)) {
            throw new DebugException('Error, no hay respuesta del servidor para validación del resultado.', 1);
        }

        if ($out['success'] == false) {
            throw new DebugException($out['message'], 501, ['out' => $out, 'command' => $ps->getLineaComando()]);
        }

        $registroSeguimiento = new RegistroSeguimiento;
        $registroSeguimiento->crearNota($this->tipopc, $this->solicitud->id, $postData['nota_aprobar'], 'A');
        /**
         * Crea de una vez e registro, permitiendo que el usuario entre con la misma password
         * como empresa sin tener que hacer la solicitud de clave
         */
        $empresa = Mercurio07::where('coddoc', $this->solicitud->tipdoc)
            ->where('documento', $this->solicitud->nit)
            ->where('tipo', 'E')->first();

        $feccla = $this->solicitante->feccla;
        $fecreg = $this->solicitante->fecreg;
        $fecapr = $postData['fecapr'];

        $crearUsuario = new CrearUsuario(
            new Srequest(
                [
                    'tipo' => 'E',
                    'coddoc' => $this->solicitud->tipdoc,
                    'documento' => $this->solicitud->nit,
                    'nombre' => $this->solicitud->razsoc,
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
         * Afiliación aceptada para el servicio sat, no disponible para independientes
         */
        $resultado_tramite = 1;
        $satServices = new SatServices;
        $satServices->notificaSatEmpresas($this->solicitud, $resultado_tramite, $postData['fecafi'], '');

        /**
         * actualiza la ficha de registro
         */
        $mercurio30->estado = 'A';
        $mercurio30->fecapr = $postData['fecapr'];
        $mercurio30->fecest = $hoy;
        $mercurio30->tipper = $tipper;
        $mercurio30->save();

        return true;
    }

    /**
     * enviarMail function
     *
     * @param [type] $mercurio30
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

        $data = $this->solicitud->toArray();
        $data['membrete'] = "{$this->dominio}Mercurio/public/img/membrete_aprueba.jpg";
        $data['ruta_firma'] = "{$this->dominio}Mercurio/public/img/Mercurio/firma_jefe_yenny.jpg";
        $data['actapr'] = $actapr;
        $data['dia'] = $dia;
        $data['mes'] = $mes;
        $data['anno'] = $anno;

        $emailCaja = Mercurio01::first();
        $sender = new SenderEmail(
            new Srequest(
                [
                    'emisor_email' => $emailCaja->getEmail(),
                    'emisor_clave' => $emailCaja->getClave(),
                    'asunto' => "Afiliación de la empresa realizada con éxito, NIT {$this->solicitud->nit}",
                ]
            )
        );
        $sender->send(
            $this->solicitante->email,
            view('cajas.layouts.aprobar', $data)->render()
        );

        return true;
    }

    public function findSolicitud($idSolicitud)
    {
        $this->solicitud = Mercurio30::where("id", $idSolicitud)->first();
        return $this->solicitud;
    }

    public function findSolicitante()
    {
        $this->solicitante = Mercurio07::whereRaw(
            "documento='{$this->solicitud->documento}' and coddoc='{$this->solicitud->coddoc}' and tipo='{$this->solicitud->tipo}'"
        )->first();
        return $this->solicitante;
    }
}
