<?php
namespace App\Models;

use App\Models\Adapter\ModelBase;
class Mercurio16 extends ModelBase
{

    protected $table = 'mercurio16';
    public $timestamps = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'documento',
        'fecha',
        'firma',
        'coddoc',
        'keyprivate',
        'keypublic',
    ];



    protected $id;
    protected $documento;
    protected $fecha;
    protected $firma;
    protected $coddoc;
    protected $keyprivate;
    protected $keypublic;

    public function __construct()
    {
        parent::__construct();
    }

    public function setKeypublic($keypublic)
    {
        $this->keypublic = $keypublic;
    }

    public function getKeypublic()
    {
        return $this->keypublic;
    }

    public function setKeyprivate($keyprivate)
    {
        $this->keyprivate = $keyprivate;
    }

    public function getKeyprivate()
    {
        return $this->keyprivate;
    }

    public function setCoddoc($coddoc)
    {
        $this->coddoc = $coddoc;
    }

    public function setDocumento($documento)
    {
        $this->documento = $documento;
    }

    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;
    }

    public function setFirma($firma)
    {
        $this->firma = $firma;
    }

    public function setFecha($fecha)
    {
        $this->fecha = $fecha;
    }

    public function getCoddoc()
    {
        return $this->coddoc;
    }

    public function getDocumento()
    {
        return $this->documento;
    }

    public function getFirma()
    {
        return $this->firma;
    }

    public function getFecha()
    {
        return $this->fecha;
    }

    public function createAttributes($data)
    {
        parent::setCreateAttributes($this, $data);
    }
}
