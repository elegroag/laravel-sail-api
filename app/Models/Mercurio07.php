<?php
namespace App\Models;

use App\Models\Adapter\ModelBase;
use Carbon\Carbon;

class Mercurio07 extends ModelBase
{

    protected $table = 'mercurio07';
    public $timestamps = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'tipo',
        'coddoc',
        'documento',
        'nombre',
        'email',
        'clave',
        'feccla',
        'autoriza',
        'codciu',
        'fecreg',
        'estado',
        'fecha_syncron',
    ];



    protected $tipo;
    protected $coddoc;
    protected $documento;
    protected $nombre;
    protected $email;
    protected $clave;
    protected $feccla;
    protected $autoriza;
    protected $codciu;
    protected $fecreg;
    protected $estado;
    protected $fecha_syncron;

    public function setFechaSyncron($fecha_syncron)
    {
        $this->fecha_syncron = $fecha_syncron;
    }

    public function getFechaSyncron()
    {
        return $this->fecha_syncron;
    }

    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    }

    public function setCoddoc($coddoc)
    {
        $this->coddoc = $coddoc;
    }

    public function setDocumento($documento)
    {
        $this->documento = $documento;
    }

    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function setClave($clave)
    {
        $this->clave = $clave;
    }

    public function setFeccla($feccla)
    {
        $this->feccla = $feccla;
    }

    public function setCodciu($codciu)
    {
        $this->codciu = $codciu;
    }

    public function setAutoriza($autoriza)
    {
        $this->autoriza = $autoriza;
    }

    public function setFecreg($fecreg)
    {
        $this->fecreg = $fecreg;
    }

    public function setEstado($estado)
    {
        $this->estado = $estado;
    }

    public function getTipo()
    {
        return $this->tipo;
    }

    public function getCoddoc()
    {
        return $this->coddoc;
    }

    public function getDocumento()
    {
        return $this->documento;
    }

    public function getNombre()
    {
        return $this->nombre;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getClave()
    {
        return $this->clave;
    }

    public function getFeccla()
    {
        return Carbon::parse($this->feccla);
    }

    public function getAutoriza()
    {
        return $this->autoriza;
    }

    public function getCodciu()
    {
        return $this->codciu;
    }

    public function getFecreg()
    {
        return Carbon::parse($this->fecreg);
    }

    public function getEstado()
    {
        return $this->estado;
    }

    public function getArrayEstados()
    {
        return array(
            'A' => 'ACTIVO',
            'I' => 'INACTIVO'
        );
    }

    public function getEstadoDetalle($estado = '')
    {
        if ($estado != '') {
            $this->estado = $estado;
        }
        switch ($this->estado) {
            case 'A':
                return 'ACTIVO';
                break;
            case 'I':
                return 'INACTIVO';
                break;
        }
        return false;
    }

    public function getArrayTipos()
    {
        return array(
            'P' => 'Particular',
            'T' => 'Trabajador',
            'E' => 'Empresa aportante',
            'I' => 'Independiente aportante',
            'O' => 'Pensionado',
            'F' => 'Facultativo',
            'S' => 'Servicio domestico'
        );
    }

    public function getTipoDetalle($tipo = '')
    {
        if ($tipo != '') {
            $this->tipo = $tipo;
        }
        switch ($this->tipo) {
            case 'P':
                return 'Particular';
                break;
            case 'T':
                return 'Trabajador';
                break;
            case 'E':
                return 'Empresa aportante';
                break;
            case 'I':
                return 'Independiente aportante';
                break;
            case 'O':
                return 'Pensionado aportante';
                break;
            case 'F':
                return 'Facultativo';
                break;
            case 'S':
                return 'Servicio domestico';
                break;
        }
        return false;
    }

    public function createAttributes($data)
    {
        parent::setCreateAttributes($this, $data);
    }
}
