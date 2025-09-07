<?php

namespace App\Services\Entidades;

use App\Exceptions\DebugException;
use App\Library\Collections\ParamsIndependiente;
use App\Library\Collections\ParamsTrabajador;
use App\Models\Adapter\DbBase;
use App\Models\Mercurio01;
use App\Models\Mercurio07;
use App\Models\Mercurio10;
use App\Models\Mercurio12;
use App\Models\Mercurio14;
use App\Models\Mercurio37;
use App\Models\Mercurio41;
use App\Services\Utils\AsignarFuncionario;
use App\Services\Utils\Comman;

class IndependienteService
{
    private $tipopc = 13;
    private $tipsoc = '08';
    private $user;
    private $db;

    /**
     * __construct function
     * @param bool $init
     * @param Services $servicios
     */
    public function __construct()
    {
        $this->user = session('user');
        $this->db = DbBase::rawConnect();
    }

    /**
     * findAllByEstado function
     * @param string $estado
     * @return array
     */
    public function findAllByEstado($estado = '')
    {
        //usuario empresa, unica solicitud de afiliación
        $documento = $this->user['documento'];
        $coddoc = $this->user['coddoc'];

        if (empty($estado)) {
            $conditions = "and m41.estado NOT IN('I') ";
        } else {
            $conditions = "and m41.estado='{$estado}' ";
        }

        $sql = "SELECT
            m41.*,
            concat_ws(' ', m41.prinom, m41.segnom, m41.priape, m41.segape) as razsoc,
            concat_ws(' ', m41.prinom, m41.segnom, m41.priape, m41.segape) as repleg,
            m41.codzon,
            m41.codciu,
            (SELECT COUNT(*) FROM mercurio10 as me10 WHERE me10.tipopc='{$this->tipopc}' and m41.id = me10.numero) as cantidad_eventos,
            (SELECT MAX(fecsis) FROM mercurio10 as mr10 WHERE mr10.tipopc='{$this->tipopc}' and m41.id = mr10.numero) as fecha_ultima_solicitud,
            (CASE
                WHEN m41.estado = 'T' THEN 'Temporal'
                WHEN m41.estado = 'D' THEN 'Devuelto'
                WHEN m41.estado = 'A' THEN 'Aprobado'
                WHEN m41.estado = 'X' THEN 'Rechazado'
                WHEN m41.estado = 'P' THEN 'Pendiente de validación'
                WHEN m41.estado = 'I' THEN 'Inactiva'
            END) as estado_detalle,
            'NATURAL' as tipo_persona,
            m41.coddoc as tipo_documento,
            gener09.detzon as detalle_zona
            FROM mercurio41 as m41
            LEFT JOIN gener09 ON gener09.codzon = m41.codzon
            WHERE m41.documento='{$documento}' and
            m41.coddoc='{$coddoc}' {$conditions}
            ORDER BY m41.fecini ASC;";

        return $this->db->inQueryAssoc($sql);
    }

    /**
     * buscarEmpresaSubsidio function
     * buscar empresa en subsidio sin importar el estado
     * @param [type] $nit
     * @return void
     */
    public function buscarEmpresaSubsidio($nit)
    {

        $procesadorComando = Comman::Api();
        $procesadorComando->runCli(
            array(
                "servicio" => "ComfacaEmpresas",
                "metodo" => "informacion_empresa",
                "params" => array(
                    "nit" => $nit
                )
            )
        );
        $salida =  $procesadorComando->toArray();
        if ($salida['success']) {
            return $salida;
        } else {
            return false;
        }
    }

    /**
     * archivosRequeridos function
     * @param Mercurio41 $solicitud
     * @return string
     */
    public function archivosRequeridos($solicitud)
    {
        if ($solicitud == false) return false;
        $archivos = array();

        $mercurio10 = Mercurio10::where('numero', $solicitud->getId())
            ->where('tipopc', $this->tipopc)
            ->orderBy('item', 'desc')
            ->first();

        $corregir = false;
        if ($mercurio10 && $mercurio10->estado == 'D') {
            $campos = $mercurio10->campos_corregir;
            $corregir = explode(";", $campos);
        }

        $mercurio14 = Mercurio14::where('tipopc', $this->tipopc)
            ->where('tipsoc', $this->tipsoc)
            ->orderBy('auto_generado', 'desc')
            ->get();

        foreach ($mercurio14 as $m14) {
            $m12 = Mercurio12::where('coddoc', $m14->getCoddoc())->first();

            $mercurio37 = Mercurio37::where('tipopc', $this->tipopc)
                ->where('numero', $solicitud->getId())
                ->where('coddoc', $m14->getCoddoc())
                ->first();

            $corrige = false;
            if ($corregir) {
                if (in_array($m12->getCoddoc(), $corregir)) {
                    $corrige = true;
                }
            }
            $obliga = ($m14->getObliga() == "S") ? "<br><small class='text-danger'>Obligatorio</small>" : "";
            $archivo = new \stdClass;
            $archivo->obliga = $obliga;
            $archivo->id = $solicitud->getId();
            $archivo->coddoc = $m14->getCoddoc();
            $archivo->detalle = $m12->getDetalle();
            $archivo->diponible = ($mercurio37) ? $mercurio37->getArchivo() : false;
            $archivo->corrige = $corrige;
            $archivos[] = $archivo;
        }

        $mercurio01 = Mercurio01::first();
        $html = view("partials/archivos_requeridos", array(
            "load_archivos" => $archivos,
            "path" => $mercurio01->getPath(),
            "puede_borrar" => ($solicitud->getEstado() == 'P' || $solicitud->getEstado() == 'A') ? false : true
        ))->render();

        return $html;
    }


    /**
     * dataArchivosRequeridos function
     * @param Mercurio41 $solicitud
     * @return array
     */
    public function dataArchivosRequeridos($solicitud)
    {
        if ($solicitud == false || is_null($solicitud)) return false;
        $archivos = array();

        $mercurio10 = Mercurio10::where('numero', $solicitud->getId())
            ->where('tipopc', $this->tipopc)
            ->orderBy('item', 'desc')
            ->first();

        $corregir = false;
        if ($mercurio10 && $mercurio10->estado == 'D') {
            $campos = $mercurio10->campos_corregir;
            $corregir = explode(";", $campos);
        }

        $mercurio14 = Mercurio14::where('tipopc', $this->tipopc)
            ->where('tipsoc', $this->tipsoc)
            ->orderBy('auto_generado', 'desc')
            ->get();

        foreach ($mercurio14 as $m14) {

            $m12 = Mercurio12::where('coddoc', $m14->getCoddoc())->first();

            $mercurio37 = Mercurio37::where('tipopc', $this->tipopc)
                ->where('numero', $solicitud->getId())
                ->where('coddoc', $m14->getCoddoc())
                ->first();
            $corrige = false;
            if ($corregir) {
                if (in_array($m12->getCoddoc(), $corregir)) $corrige = true;
            }

            $archivo = $m14->getArray();
            $archivo['obliga'] = ($m14->getObliga() == "S") ? "<br><small class='text-danger'>Obligatorio</small>" : "";
            $archivo['id'] = $solicitud->getId();
            $archivo['detalle'] = capitalize($m12->getDetalle());
            $archivo['diponible'] = ($mercurio37) ? $mercurio37->getArchivo() : false;
            $archivo['corrige'] = $corrige;
            $archivos[] = $archivo;
        }

        $mercurio01 = Mercurio01::first();
        $archivos_descargar = oficios_requeridos('I');
        return array(
            "disponibles" => $archivos_descargar,
            "archivos" => $archivos,
            "path" => $mercurio01->getPath(),
            "puede_borrar" => ($solicitud->getEstado() == 'P' || $solicitud->getEstado() == 'A') ? false : true
        );
    }

    /**
     * update function
     * @param integer $id
     * @param array $data
     * @return Mercurio41
     */
    public function update($id, $data)
    {
        $independiente = $this->findById($id);
        if ($independiente) {
            $independiente->fill($data);
            return $independiente->save();
        }
        return false;
    }

    /**
     * updateByFormData function
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateByFormData($id, $data)
    {
        $independiente = $this->findById($id);
        if ($independiente) {
            $independiente->fill($data);
            return $independiente->save();
        }
        return false;
    }

    /**
     * create function
     * @param array $data
     * @return Mercurio41
     */
    public function create($data)
    {
        $independiente = new Mercurio41($data);
        $independiente->setCoddoc($this->user['coddoc']);
        $independiente->setDocumento($this->user['documento']);
        $independiente->setUsuario((new AsignarFuncionario())->asignar($this->tipopc, $data['codzon']));
        $independiente->save();
        $id = $independiente->getId();

        Mercurio37::where('tipopc', $this->tipopc)->where('numero', $id)->delete();
        Mercurio10::where('tipopc', $this->tipopc)->where('numero', $id)->delete();
        return $independiente;
    }


    /**
     * createByFormData function
     * @param array $data
     * @return Mercurio41
     */
    public function createByFormData($data)
    {
        $data['estado'] = "T";
        $data['log'] = "0";
        $independiente = $this->create($data);
        return $independiente;
    }

    /**
     * findById function
     * @param integer $id
     * @return Mercurio41
     */
    public function findById($id)
    {
        return Mercurio41::where('id', $id)->first();
    }

    public function consultaSeguimiento(int $id)
    {
        $seguimientos = Mercurio10::where('numero', $id)
            ->where('tipopc', $this->tipopc)
            ->orderByDesc('item')
            ->get()
            ->map(function ($seguimiento) {
                $seguimiento->corregir = explode(';', $seguimiento->campos_corregir);
                return $seguimiento;
            });

        return array(
            'seguimientos' => $seguimientos,
            'campos_disponibles' => (new Mercurio41)->CamposDisponibles(),
            'estados_detalles' => (new Mercurio10)->getArrayEstados()
        );
    }

    /**
     * enviarCaja function
     * @param SenderValidationCaja $senderValidationCaja
     * @param integer $id
     * @param integer $documento
     * @param integer $coddoc
     * @return void
     */
    public function enviarCaja($senderValidationCaja, $id, $usuario)
    {
        $mercurio41 = $this->findById($id);

        $cm37 = (new Mercurio37)->getCount(
            "*",
            "conditions: tipopc='{$this->tipopc}' AND " .
                "numero='{$id}' AND " .
                "coddoc IN(SELECT coddoc FROM mercurio14 WHERE tipopc='{$this->tipopc}' AND tipsoc='{$this->tipsoc}' AND obliga='S')"
        );

        $cm14 = (new Mercurio14)->getCount(
            "*",
            "conditions: tipopc='{$this->tipopc}' and tipsoc='{$this->tipsoc}' and obliga='S'"
        );

        if ($cm37 < $cm14) {
            throw new DebugException("Adjunte los archivos obligatorios", 500);
        }

        Mercurio41::where('id', $id)->update([
            'usuario' => $usuario,
            'estado' => 'P',
        ]);

        $ai = Mercurio10::where('tipopc', $this->tipopc)
            ->where('numero', $id)
            ->max('item') + 1;

        $mercurio41->item = $ai;
        $solicitante = Mercurio07::where('documento', $mercurio41->getDocumento())
            ->where('coddoc', $mercurio41->getCoddoc())
            ->where('tipo', $mercurio41->getTipo())
            ->first();

        $mercurio41->repleg = $solicitante->getNombre();
        $mercurio41->razsoc = $solicitante->getNombre();
        $mercurio41->nit = $solicitante->getDocumento();
        $mercurio41->email = $solicitante->getEmail();

        $senderValidationCaja->send($this->tipopc, $mercurio41);
    }

    public function paramsApi()
    {
        $procesadorComando = Comman::Api();
        $procesadorComando->runCli(
            array(
                "servicio" => "ComfacaAfilia",
                "metodo" => "parametros_empresa"
            )
        );

        $paramsPensionado = new ParamsIndependiente();
        $paramsPensionado->setDatosCaptura($procesadorComando->toArray());

        $procesadorComando = Comman::Api();
        $procesadorComando->runCli(
            array(
                "servicio" => "ComfacaAfilia",
                "metodo" => "parametros_trabajadores"
            )
        );
        $paramsTrabajador = new ParamsTrabajador();
        $paramsTrabajador->setDatosCaptura($procesadorComando->toArray());
    }
}
