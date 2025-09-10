<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;

class Sat01 extends ModelBase
{
    protected $table = 'sat01';
    public $timestamps = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'password',
        'grant_type',
        'codapl',
        'control',
        'audtra',
        'nit',
        'path',
    ];

    // Setters
    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function setGrantType($grant_type)
    {
        $this->grant_type = $grant_type;
    }

    public function setCodapl($codapl)
    {
        $this->codapl = $codapl;
    }

    public function setControl($control)
    {
        $this->control = $control;
    }

    public function setAudtra($audtra)
    {
        $this->audtra = $audtra;
    }

    public function setNit($nit)
    {
        $this->nit = $nit;
    }

    public function setPath($path)
    {
        $this->path = $path;
    }

    // Getters
    public function getPassword()
    {
        return $this->password;
    }

    public function getGrantType()
    {
        return $this->grant_type;
    }

    public function getCodapl()
    {
        return $this->codapl;
    }

    public function getControl()
    {
        return $this->control;
    }

    public function getAudtra()
    {
        return $this->audtra;
    }

    public function getNit()
    {
        return $this->nit;
    }

    public function getPath()
    {
        return $this->path;
    }
}
