<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;

class Tranoms extends ModelBase
{

    protected $table = 'tranoms';
    public $timestamps = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'cedtra',
        'nomtra',
        'apetra',
        'saltra',
        'fectra',
        'cartra',
        'request',
    ];

    public function getCedtra()
    {
        return $this->cedtra;
    }

    public function setCedtra($cedtra)
    {
        $this->cedtra = $cedtra;
    }

    public function getNomtra()
    {
        return $this->nomtra;
    }

    public function setNomtra($nomtra)
    {
        $this->nomtra = $nomtra;
    }

    public function getApetra()
    {
        return $this->apetra;
    }

    public function setApetra($apetra)
    {
        $this->apetra = $apetra;
    }

    public function getSaltra()
    {
        return $this->saltra;
    }

    public function setSaltra($saltra)
    {
        $this->saltra = $saltra;
    }

    public function getFectra()
    {
        return $this->fectra;
    }

    public function setFectra($fectra)
    {
        $this->fectra = $fectra;
    }

    public function getCartra()
    {
        return $this->cartra;
    }

    public function setCartra($cartra)
    {
        $this->cartra = $cartra;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function setRequest($request)
    {
        $this->request = $request;
    }

    public function createAttributes($data)
    {
        parent::setCreateAttributes($this, $data);
    }
}
