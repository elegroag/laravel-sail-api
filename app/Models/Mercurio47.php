<?php

namespace App\Models;

use App\Models\Adapter\DbBase;
use App\Models\Adapter\ModelBase;
use App\Models\Adapter\HasCustomUuid;

class Mercurio47 extends ModelBase
{
    use HasCustomUuid;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    protected $table = 'mercurio47';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $fillable = [
        'documento',
        'tipo',
        'coddoc',
        'fecsol',
        'fecest',
        'estado',
        'tipact',
        'usuario',
        'ruuid',
        'fecapr',
        'codest'
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
     *
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
     *
     * @return void
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * setDocumento function
     * campos documento realacionado con el documento mercurio07 solicitante
     *
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
     *
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
     *
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
     *
     * @return @documento
     */
    public function getDocumento()
    {
        return $this->documento;
    }

    /**
     * getTipo function
     * campos tipo realacionado con el tipo mercurio07 solicitante
     *
     * @return @tipo
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * getCoddoc function
     * campos coddoc realacionado con el coddoc mercurio07 solicitante
     *
     * @return @coddoc
     */
    public function getCoddoc()
    {
        return $this->coddoc;
    }

    public function setFecsol($fecsol)
    {
        $this->fecsol = $fecsol;
    }

    public function setFecest($fecest)
    {
        $this->fecest = $fecest;
    }

    public function setEstado($estado)
    {
        $this->estado = $estado;
    }

    public function setTipact($tipact)
    {
        $this->tipact = $tipact;
    }

    public function getFecsol()
    {
        return $this->fecsol;
    }

    public function getFecest()
    {
        return $this->fecest;
    }

    public function getEstado()
    {
        return $this->estado;
    }

    public function getTipact()
    {
        return $this->tipact;
    }

    public function getEstadosArray()
    {
        return solicitud_estados_array();
    }

    public function getEstadoInArray($estado = '')
    {
        if (! empty($estado)) {
            $this->estado = $estado;
        }
        return solicitud_estado_detalle($this->estado);
    }

    public function getTipActArray()
    {
        return solicitud_tipo_actualizacion_array();
    }

    public function getTipActInArray($tipact = '')
    {
        if (! empty($tipact)) {
            $this->tipact = $tipact;
        }
        return solicitud_tipo_actualizacion_detalle($this->tipact);
    }

    public function createAttributes($data)
    {
        parent::setCreateAttributes($this, $data);
    }

    public function CamposDisponibles()
    {
        $db = DbBase::rawConnect();
        $rqs = $db->inQueryAssoc('SELECT * FROM mercurio12');
        $data = [];
        foreach ($rqs as $ai => $row) {
            $data[$row['coddoc']] = $row['detalle'];
        }

        return $data;
    }

    public function CamposDisponibleDetalle($campo)
    {
        $data = $this->CamposDisponibles();

        return $data["{$campo}"];
    }

    public function getRuuid()
    {
        $this->regenerateUuid();
        return $this->ruuid;
    }
}
