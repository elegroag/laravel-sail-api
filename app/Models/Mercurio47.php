<?php

namespace App\Models;

use App\Models\Adapter\DbBase;
use App\Models\Adapter\ModelBase;

class Mercurio47 extends ModelBase
{

    protected $table = 'mercurio47';
    public $timestamps = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'documento',
        'tipo',
        'coddoc',
        'fecha_solicitud',
        'fecha_estado',
        'estado',
        'tipo_actualizacion',
        'usuario',
    ];

    public function setUsuario($usuario)
    {
        $this->usuario = $usuario;
    }

    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * setId function
     * campos id key
     * @param [type] $id
     * @return void
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * getId function
     * campos id key
     * @return void
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * setDocumento function
     * campos documento realacionado con el documento mercurio07 solicitante
     * @param [type] $documento
     * @return void
     */
    public function setDocumento($documento)
    {
        $this->documento = $documento;
    }

    /**
     * setTipo function
     * campos tipo realacionado con el tipo mercurio07 solicitante
     * @param [type] $tipo
     * @return void
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    }

    /**
     * setCoddoc function
     * campos coddoc realacionado con el coddoc mercurio07 solicitante
     * @param [type] $coddoc
     * @return void
     */
    public function setCoddoc($coddoc)
    {
        $this->coddoc = $coddoc;
    }

    /**
     * getDocumento function
     * campos documento realacionado con el documento mercurio07 solicitante
     * @return @documento
     */
    public function getDocumento()
    {
        return $this->documento;
    }

    /**
     * getTipo function
     * campos tipo realacionado con el tipo mercurio07 solicitante
     * @return @tipo
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * getCoddoc function
     * campos coddoc realacionado con el coddoc mercurio07 solicitante
     * @return @coddoc
     */
    public function getCoddoc()
    {
        return $this->coddoc;
    }

    public function setFechaSolicitud($fecha_solicitud)
    {
        $this->fecha_solicitud = $fecha_solicitud;
    }

    public function setFechaEstado($fecha_estado)
    {
        $this->fecha_estado = $fecha_estado;
    }

    public function setEstado($estado)
    {
        $this->estado = $estado;
    }

    public function setTipoActualizacion($tipo_actualizacion)
    {
        $this->tipo_actualizacion = $tipo_actualizacion;
    }

    public function getFechaSolicitud()
    {
        return $this->fecha_solicitud;
    }

    public function getFechaEstado()
    {
        return $this->fecha_estado;
    }

    public function getEstado()
    {
        return $this->estado;
    }

    public function getTipoActualizacion()
    {
        return $this->tipo_actualizacion;
    }

    public function getEstadosArray()
    {
        return array(
            'A' => 'Aprobado',
            'P' => 'Pendiente',
            'D' => 'Devuelto',
            'X' => 'Rechazado',
            'T' => 'Temporal'
        );
    }

    public function getEstadoInArray($estado = '')
    {
        if (!empty($estado)) {
            $this->estado = $estado;
        }
        $estados = $this->getEstadosArray();
        return (isset($estados["{$this->estado}"])) ? $estados["{$this->estado}"] : false;
    }

    public function getTipoActualizacionArray()
    {
        return array(
            'E' => 'Empresa',
            'I' => 'Independiente',
            'T' => 'Trabajador',
            'P' => 'Pensionado',
            'B' => 'Beneficiario',
            'C' => 'Conyuge'
        );
    }

    public function getTipoActualizacionInArray($tipo_actualizacion = '')
    {
        if (!empty($tipo_actualizacion)) {
            $this->tipo_actualizacion = $tipo_actualizacion;
        }
        $tipo_actualizaciones = $this->getTipoActualizacionArray();
        return (isset($tipo_actualizaciones["{$this->tipo_actualizacion}"])) ? $tipo_actualizaciones["{$this->tipo_actualizacion}"] : false;
    }

    public function createAttributes($data)
    {
        parent::setCreateAttributes($this, $data);
    }

    public function CamposDisponibles()
    {
        $db = DbBase::rawConnect();
        $rqs = $db->fetchAll("SELECT * FROM mercurio12");
        $data = array();
        foreach ($rqs as $ai => $row) $data[$row['coddoc']] = $row['detalle'];
        return $data;
    }

    public function CamposDisponibleDetalle($campo)
    {
        $data = $this->CamposDisponibles();
        return $data["{$campo}"];
    }
}
