<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;
use Carbon\Carbon;

class Mercurio45 extends ModelBase
{
    protected $table = 'mercurio45';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $fillable = [
        'log',
        'cedtra',
        'codben',
        'nombre',
        'fecha',
        'codcer',
        'nomcer',
        'archivo',
        'usuario',
        'estado',
        'motivo',
        'fecest',
        'codest',
        'tipo',
        'coddoc',
        'documento',
        'fecsol'
    ];

    /**
     * Metodo para establecer el valor del campo id
     *
     * @param  int  $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Metodo para establecer el valor del campo log
     *
     * @param  int  $log
     */
    public function setLog($log)
    {
        $this->log = $log;
    }

    /**
     * Metodo para establecer el valor del campo cedtra
     *
     * @param  string  $cedtra
     */
    public function setCedtra($cedtra)
    {
        $this->cedtra = $cedtra;
    }

    /**
     * Metodo para establecer el valor del campo codben
     *
     * @param  int  $codben
     */
    public function setCodben($codben)
    {
        $this->codben = $codben;
    }

    /**
     * Metodo para establecer el valor del campo nombre
     *
     * @param  string  $nombre
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    /**
     * Metodo para establecer el valor del campo fecha
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;
    }

    /**
     * Metodo para establecer el valor del campo codcer
     *
     * @param  string  $codcer
     */
    public function setCodcer($codcer)
    {
        $this->codcer = $codcer;
    }

    /**
     * Metodo para establecer el valor del campo nomcer
     *
     * @param  string  $nomcer
     */
    public function setNomcer($nomcer)
    {
        $this->nomcer = $nomcer;
    }

    /**
     * Metodo para establecer el valor del campo archivo
     *
     * @param  string  $archivo
     */
    public function setArchivo($archivo)
    {
        $this->archivo = $archivo;
    }

    /**
     * Metodo para establecer el valor del campo usuario
     *
     * @param  int  $usuario
     */
    public function setUsuario($usuario)
    {
        $this->usuario = $usuario;
    }

    /**
     * Metodo para establecer el valor del campo estado
     *
     * @param  string  $estado
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;
    }

    /**
     * Metodo para establecer el valor del campo motivo
     *
     * @param  string  $motivo
     */
    public function setMotivo($motivo)
    {
        $this->motivo = $motivo;
    }

    /**
     * Metodo para establecer el valor del campo fecest
     */
    public function setFecest($fecest)
    {
        $this->fecest = $fecest;
    }

    /**
     * Metodo para establecer el valor del campo codest
     *
     * @param  string  $codest
     */
    public function setCodest($codest)
    {
        $this->codest = $codest;
    }

    /**
     * Metodo para establecer el valor del campo tipo
     *
     * @param  string  $tipo
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    }

    /**
     * Metodo para establecer el valor del campo coddoc
     *
     * @param  string  $coddoc
     */
    public function setCoddoc($coddoc)
    {
        $this->coddoc = $coddoc;
    }

    /**
     * Metodo para establecer el valor del campo documento
     *
     * @param  string  $documento
     */
    public function setDocumento($documento)
    {
        $this->documento = $documento;
    }

    /**
     * Devuelve el valor del campo id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Devuelve el valor del campo log
     *
     * @return int
     */
    public function getLog()
    {
        return $this->log;
    }

    /**
     * Devuelve el valor del campo cedtra
     *
     * @return string
     */
    public function getCedtra()
    {
        return $this->cedtra;
    }

    /**
     * Devuelve el valor del campo codben
     *
     * @return int
     */
    public function getCodben()
    {
        return $this->codben;
    }

    /**
     * Devuelve el valor del campo nombre
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Devuelve el valor del campo fecha
     */
    public function getFecha()
    {
        return Carbon::parse($this->fecha);
    }

    /**
     * Devuelve el valor del campo codcer
     *
     * @return string
     */
    public function getCodcer()
    {
        return $this->codcer;
    }

    /**
     * Devuelve el valor del campo nomcer
     *
     * @return string
     */
    public function getNomcer()
    {
        return $this->nomcer;
    }

    /**
     * Devuelve el valor del campo archivo
     *
     * @return string
     */
    public function getArchivo()
    {
        return $this->archivo;
    }

    /**
     * Devuelve el valor del campo usuario
     *
     * @return int
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * Devuelve el valor del campo estado
     *
     * @return string
     */
    public function getEstado()
    {
        return $this->estado;
    }

    public function getEstadoDetalle()
    {
        $return = '';
        if ($this->estado == 'T') {
            $return = 'TEMPORAL';
        }
        if ($this->estado == 'D') {
            $return = 'DEVUELTO';
        }
        if ($this->estado == 'A') {
            $return = 'APROBADO';
        }
        if ($this->estado == 'X') {
            $return = 'RECHAZADO';
        }
        if ($this->estado == 'P') {
            $return = 'PENDIENTE';
        }

        return $return;
    }

    /**
     * Devuelve el valor del campo motivo
     *
     * @return string
     */
    public function getMotivo()
    {
        return $this->motivo;
    }

    /**
     * Devuelve el valor del campo fecest
     */
    public function getFecest()
    {
        return Carbon::parse($this->fecest);
    }

    /**
     * Devuelve el valor del campo codest
     *
     * @return string
     */
    public function getCodest()
    {
        return $this->codest;
    }

    /**
     * Devuelve el valor del campo tipo
     *
     * @return string
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Devuelve el valor del campo coddoc
     *
     * @return string
     */
    public function getCoddoc()
    {
        return $this->coddoc;
    }

    /**
     * Devuelve el valor del campo documento
     *
     * @return string
     */
    public function getDocumento()
    {
        return $this->documento;
    }
}
