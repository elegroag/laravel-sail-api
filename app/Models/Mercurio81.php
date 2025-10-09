<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;

class Mercurio81 extends ModelBase
{
    protected $table = 'mercurio81';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $fillable = [
        'codinf',
        'coddan',
        'codare',
        'tipsec',
        'sede',
    ];

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setCodinf($codinf)
    {
        $this->codinf = $codinf;
    }

    public function setCoddan($coddan)
    {
        $this->coddan = $coddan;
    }

    public function setCodare($codare)
    {
        $this->codare = $codare;
    }

    public function setTipsec($tipsec)
    {
        $this->tipsec = $tipsec;
    }

    public function setSede($sede)
    {
        $this->sede = $sede;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getCodinf()
    {
        return $this->codinf;
    }

    public function getCoddan()
    {
        return $this->coddan;
    }

    public function getCodare()
    {
        return $this->codare;
    }

    public function getTipsec()
    {
        return $this->tipsec;
    }

    public function getSede()
    {
        return $this->sede;
    }

    public function getCoddanDetalle()
    {
        return $this->belongsTo(Xml4b085::class, 'coddan', 'codins')->first()->getDetins();
    }
}
