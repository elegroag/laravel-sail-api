<?php

namespace App\Services\Entidades;

use App\Exceptions\DebugException;
use App\Models\Adapter\DbBase;
use App\Models\Mercurio01;
use App\Models\Mercurio07;
use App\Models\Mercurio10;
use App\Models\Mercurio12;
use App\Models\Mercurio13;
use App\Models\Mercurio34;
use App\Models\Mercurio37;
use App\Services\Srequest;
use App\Services\Utils\Comman;

class BeneficiarioService
{
    private $tipopc = '4';

    private $user;

    private $tipo;

    private $db;

    public function __construct()
    {
        $this->user = session('user');
        $this->tipo = session('tipo');
        $this->db = DbBase::rawConnect();
    }

    /**
     * findAllByEstado function
     *
     * @param  string  $estado
     * @return array
     */
    public function findAllByEstado($estado = '')
    {
        // usuario empresa, unica solicitud de afiliación
        $documento = $this->user['documento'];
        $coddoc = $this->user['coddoc'];

        if ((new Mercurio34)->getCount(
            '*',
            "conditions: documento='{$documento}' AND coddoc='{$coddoc}'"
        ) == 0) {
            return [];
        }
        $conditions = (empty($estado)) ? " AND m34.estado NOT IN('I') " : " AND m34.estado='{$estado}' ";

        return $this->db->inQueryAssoc(
            "SELECT m34.*,
                (SELECT COUNT(*) FROM mercurio10 as me10 WHERE me10.tipopc='{$this->tipopc}' and m34.id = me10.numero) as 'cantidad_eventos',
                (SELECT MAX(fecsis) FROM mercurio10 as mr10 WHERE mr10.tipopc='{$this->tipopc}' and m34.id = mr10.numero) as 'fecha_ultima_solicitud',
                (CASE
                    WHEN m34.estado = 'T' THEN 'Temporal en edición'
                    WHEN m34.estado = 'D' THEN 'Devuelto'
                    WHEN m34.estado = 'A' THEN 'Aprobado'
                    WHEN m34.estado = 'X' THEN 'Rechazado'
                    WHEN m34.estado = 'P' THEN 'Pendiente De Validación CAJA'
                    WHEN m34.estado = 'I' THEN 'Inactiva'
                END) as estado_detalle,
                coddoc as tipo_documento
                FROM mercurio34 as m34
                WHERE m34.documento='{$documento}' and m34.coddoc='{$coddoc}' {$conditions}
                ORDER BY m34.fecsol ASC;"
        );
    }

    /**
     * buscarEmpresaSubsidio function
     * buscar empresa en subsidio sin importar el estado
     *
     * @param [type] $nit
     * @return void
     */
    public function buscarEmpresaSubsidio($nit)
    {
        $procesadorComando = Comman::Api();
        $procesadorComando->runCli(
            [
                'servicio' => 'ComfacaEmpresas',
                'metodo' => 'informacion_empresa',
                'params' => [
                    'nit' => $nit,
                ],
            ]
        );
        $salida = $procesadorComando->toArray();
        if ($salida['success']) {
            return $salida;
        } else {
            return false;
        }
    }

    public function archivosRequeridos($solicitud)
    {
        if ($solicitud == false) {
            return false;
        }
        $archivos = [];

        $mercurio10 = Mercurio10::where('numero', $solicitud->getId())
            ->where('tipopc', $this->tipopc)
            ->orderBy('item', 'desc')
            ->first();

        $corregir = false;
        if ($mercurio10 && $mercurio10->estado == 'D') {
            $campos = $mercurio10->campos_corregir;
            $corregir = explode(';', $campos);
        }

        $mercurio13 = Mercurio13::where('tipopc', $this->tipopc)->get();

        foreach ($mercurio13 as $m13) {

            $m12 = Mercurio12::where('coddoc', $m13->getCoddoc())->first();

            $mercurio37 = Mercurio37::where('tipopc', $this->tipopc)
                ->where('numero', $solicitud->getId())
                ->where('coddoc', $m13->getCoddoc())
                ->first();

            $corrige = false;
            if ($corregir) {
                if (in_array($m12->getCoddoc(), $corregir)) {
                    $corrige = true;
                }
            }

            $obliga = ($m13->getObliga() == 'S') ? "<br><small class='text-danger'>Obligatorio</small>" : '';
            $archivo = new \stdClass;
            $archivo->obliga = $obliga;
            $archivo->id = $solicitud->getId();
            $archivo->coddoc = $m13->getCoddoc();
            $archivo->detalle = $m12->getDetalle();
            $archivo->diponible = ($mercurio37) ? $mercurio37->getArchivo() : false;
            $archivo->corrige = $corrige;
            $archivos[] = $archivo;
        }

        $mercurio01 = Mercurio01::first();
        $html = view('partial/archivos_requeridos', [
            'load_archivos' => $archivos,
            'path' => $mercurio01->getPath(),
            'puede_borrar' => ($solicitud->getEstado() == 'P' || $solicitud->getEstado() == 'A') ? false : true,
        ])->render();

        return $html;
    }

    /**
     * loadDisplaySubsidio function
     *
     * @param  array  $trabajador
     * @return void
     */
    public function loadDisplaySubsidio($trabajador)
    {
        /* Tag::displayTo("nit", $trabajador['nit']);
        Tag::displayTo("telefono", $trabajador['telefono']);
        Tag::displayTo("email", $trabajador['email']);
        Tag::displayTo("segnom", $trabajador['segnom']);
        Tag::displayTo("priape", $trabajador['priape']);
        Tag::displayTo("segape", $trabajador['segape']);
        Tag::displayTo("matmer", $trabajador['matmer']);
        Tag::displayTo("direccion", $trabajador['direccion']);
        Tag::displayTo("celular", $trabajador['telr']);
        Tag::displayTo("celpri", $trabajador['telt']);
        Tag::displayTo("dirpri", $trabajador['dirpri']);
        Tag::displayTo("emailpri", $trabajador['mailr']);
        */
    }

    /**
     * updateByFormData function
     *
     * @param  int  $id
     * @param  array  $data
     * @return bool
     */
    public function updateByFormData($id, $data)
    {
        $solicitud = $this->findById($id);
        if ($solicitud) {
            $solicitud->fill($data);

            return $solicitud->save();
        } else {
            return false;
        }
    }

    /**
     * create function
     *
     * @param  array  $data
     * @return Mercurio34
     */
    public function create($data)
    {
        $beneficiario = new Mercurio34($data);
        $beneficiario->save();
        $id = $beneficiario->getId();

        Mercurio37::where('tipopc', $this->tipopc)->where('numero', $id)->delete();
        Mercurio10::where('tipopc', $this->tipopc)->where('numero', $id)->delete();

        return $beneficiario;
    }

    /**
     * createByFormData function
     *
     * @param  array  $data
     * @return Mercurio34
     */
    public function createByFormData($data)
    {
        $data['estado'] = 'T';
        $data['log'] = '0';
        $beneficiario = $this->create($data);

        return $beneficiario;
    }

    /**
     * findById function
     *
     * @param  int  $id
     * @return Mercurio34
     */
    public function findById($id)
    {
        return Mercurio34::where('id', $id)->first();
    }

    /**
     * enviarCaja function
     *
     * @param  SenderValidationCaja  $senderValidationCaja
     * @param  int  $id
     * @param  int  $documento
     * @param  int  $coddoc
     * @return void
     */
    public function enviarCaja($senderValidationCaja, $id, $usuario)
    {
        $solicitud = $this->findById($id);

        $cm37 = (new Mercurio37)->getCount(
            '*',
            "conditions: tipopc='{$this->tipopc}' AND " .
                "numero='{$id}' AND " .
                "coddoc IN(SELECT coddoc FROM mercurio13 WHERE tipopc='{$this->tipopc}' AND obliga='S')"
        );

        $cm13 = (new Mercurio13)->getCount(
            '*',
            "conditions: tipopc='{$this->tipopc}' AND obliga='S'"
        );
        if ($cm37 < $cm13) {
            throw new DebugException('Adjunte los archivos obligatorios', 500);
        }

        Mercurio34::where('id', $id)
            ->update([
                'usuario' => (string) $usuario,
                'estado' => 'P',
            ]);

        $ai = Mercurio10::where('tipopc', $this->tipopc)->where('numero', $id)->max('item') + 1;

        $solicitud->item = $ai;
        $solicitante = Mercurio07::where('documento', $solicitud->getDocumento())
            ->where('coddoc', $solicitud->getCoddoc())
            ->where('tipo', $solicitud->getTipo())
            ->first();

        $solicitud->repleg = $solicitante->getNombre();
        $solicitud->razsoc = $solicitante->getNombre();
        $solicitud->nit = $solicitante->getDocumento();
        $solicitud->email = $solicitante->getEmail();
        $senderValidationCaja->send($this->tipopc, $solicitud);
    }

    public function buscarBeneficiarioSubsidio($numdoc)
    {
        $procesadorComando = Comman::Api();
        $procesadorComando->runCli(
            [
                'servicio' => 'ComfacaEmpresas',
                'metodo' => 'informacion_beneficiario',
                'params' => $numdoc,
            ]
        );

        $datos_captura = $procesadorComando->toArray();
        if ($datos_captura['success']) {
            return $datos_captura;
        } else {
            return false;
        }
    }

    public function dataArchivosRequeridos($solicitud)
    {

        if ($solicitud == false) {
            return false;
        }
        $archivos = [];

        $mercurio10 = Mercurio10::where('numero', $solicitud->getId())
            ->where('tipopc', $this->tipopc)
            ->orderBy('item', 'desc')
            ->first();

        $corregir = false;
        if ($mercurio10 && $mercurio10->estado == 'D') {
            $campos = $mercurio10->campos_corregir;
            $corregir = explode(';', $campos);
        }

        $mercurio13 = Mercurio13::where('tipopc', $this->tipopc)->orderBy('auto_generado', 'desc')->get();

        foreach ($mercurio13 as $m13) {

            $m12 = Mercurio12::where('coddoc', $m13->getCoddoc())->first();

            $mercurio37 = Mercurio37::where('tipopc', $this->tipopc)
                ->where('numero', $solicitud->getId())
                ->where('coddoc', $m13->getCoddoc())
                ->first();

            $corrige = false;
            if ($corregir) {
                if (in_array($m12->getCoddoc(), $corregir)) {
                    $corrige = true;
                }
            }

            $archivo = $m13->getArray();
            $archivo['obliga'] = ($m13->getObliga() == 'S') ? "<br><small class='text-danger'>Obligatorio</small>" : '';
            $archivo['id'] = $solicitud->getId();
            $archivo['detalle'] = capitalize($m12->getDetalle());
            $archivo['diponible'] = ($mercurio37) ? $mercurio37->getArchivo() : false;
            $archivo['corrige'] = $corrige;
            $archivos[] = $archivo;
        }

        $mercurio01 = Mercurio01::first();
        $archivos_descargar = oficios_requeridos('B');

        return [
            'disponibles' => $archivos_descargar,
            'archivos' => $archivos,
            'path' => $mercurio01->getPath(),
            'puede_borrar' => ($solicitud->getEstado() == 'P' || $solicitud->getEstado() == 'A') ? false : true,
        ];
    }

    public function consultaSeguimiento($id)
    {
        $seguimientos = Mercurio10::where('numero', $id)
            ->where('tipopc', $this->tipopc)
            ->orderBy('item', 'desc')
            ->get()
            ->map(function ($row) {
                $campos = explode(';', $row->campos_corregir);
                $row->corregir = $campos;

                return $row;
            })
            ->toArray();

        return [
            'seguimientos' => $seguimientos,
            'campos_disponibles' => (new Mercurio34)->CamposDisponibles(),
            'estados_detalles' => (new Mercurio10)->getArrayEstados(),
        ];
    }

    public function consultaTipopc(Srequest $request): array|bool
    {
        $tipo_consulta = $request->getParam('tipo_consulta');
        $tipopc = $request->getParam('tipopc');
        $condi_extra = $request->getParam('condi_extra');
        $usuario = $request->getParam('usuario');
        $numero = $request->getParam('numero');

        switch ($tipo_consulta) {
            case 'all':
                $response["datos"] = Mercurio34::query()
                    ->join('mercurio10', function ($join) use ($tipopc) {
                        $join->on('mercurio34.id', '=', 'mercurio10.numero')
                            ->where('mercurio10.tipopc', '=', $tipopc);
                    })
                    ->select([
                        'mercurio34.*',
                        'mercurio10.estado as estado',
                        'mercurio10.fecsis as fecest',
                    ])
                    ->when($condi_extra, function ($q) use ($condi_extra) {
                        $q->whereRaw($condi_extra);
                    })
                    ->get();
                break;
            case 'alluser':
                $response["datos"] = Mercurio34::where("usuario='{$usuario}' and estado='P'")->get();
                break;
            case 'count':
                $response["count"] = Mercurio34::whereRaw("mercurio34.usuario='$usuario' $condi_extra ")
                    ->join('mercurio20', 'mercurio34.log', 'mercurio20.log')
                    ->getId();

                $response["all"] = Mercurio34::whereRaw("mercurio34.usuario='$usuario' $condi_extra")
                    ->join('mercurio20', 'mercurio34.log', 'mercurio20.log')
                    ->get();
                break;
            case 'one':
                $response["datos"] = Mercurio34::where("id='$numero' and estado='P'")->get();
                break;
            case 'info':
                $mercurio = Mercurio34::where("id='$numero' ")->get();
                $response["consulta"] = $this->buscarBeneficiarioSubsidio($mercurio->getNumdoc());
                break;
            default:
                $response = false;
                break;
        }
        return $response;
    }
}
