<?php 
namespace App\Models;

use App\Models\Adapter\ModelBase;

class PinesAfiliado extends ModelBase
{

    protected $table = 'pines_afiliados';
    public $timestamps = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'pin',
        'cedtra',
        'docben',
        'estado',
        'fecha',
        'codser',
    ];


    protected $id;
    protected $pin;
    protected $cedtra;
    protected $docben;
    protected $estado;
    protected $fecha;
    protected $codser;

    public function getDocben()
    {
        return $this->docben;
    }

    public function setDocben($docben)
    {
        $this->docben = $docben;
    }

    public function getCedtra()
    {
        return $this->cedtra;
    }

    public function setCedtra($cedtra)
    {
        $this->cedtra = $cedtra;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getPin()
    {
        return $this->pin;
    }

    public function setPin($pin)
    {
        $this->pin = $pin;
    }

    public function getEstado()
    {
        return $this->estado;
    }

    public function setEstado($estado)
    {
        $this->estado = $estado;
    }

    public function getFecha()
    {
        return $this->fecha;
    }

    public function setFecha($fecha)
    {
        $this->fecha = $fecha;
    }

    public function setCodser($codser)
    {
        $this->codser = $codser;
    }

    public function getCodser()
    {
        return $this->codser;
    }


}