<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;

class AfiliadoHabil extends ModelBase
{

    protected $table = 'afiliado_habil';
    public $timestamps = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'cedtra',
        'docben',
        'categoria',
        'nomben',
        'pin',
        'codser',
    ];

    public function getPin()
    {
        return $this->pin;
    }

    public function setPin($pin)
    {
        $this->pin = $pin;
    }

    public function geNomben()
    {
        return $this->nomben;
    }

    public function setNomben($nomben)
    {
        $this->nomben = $nomben;
    }

    public function getCategoria()
    {
        return $this->categoria;
    }

    public function setCategoria($categoria)
    {
        $this->categoria = $categoria;
    }

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

    public function setCodser($codser)
    {
        $this->codser = $codser;
    }

    public function getCodser()
    {
        return $this->codser;
    }
}
