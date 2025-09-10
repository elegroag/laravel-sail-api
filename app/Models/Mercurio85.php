<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;

class Mercurio85 extends ModelBase
{
    protected $table = 'mercurio85';
    public $timestamps = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'tipideacu',
        'numideacu',
        'prinomacu',
        'segnomacu',
        'priapeacu',
        'segapeacu',
        'telacu'
    ];

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setTipideacu($tipideacu)
    {
        $this->tipideacu = $tipideacu;
    }

    public function setNumideacu($numideacu)
    {
        $this->numideacu = $numideacu;
    }

    public function setPrinomacu($prinomacu)
    {
        $this->prinomacu = $prinomacu;
    }

    public function setSegnomacu($segnomacu)
    {
        $this->segnomacu = $segnomacu;
    }

    public function setPriapeacu($priapeacu)
    {
        $this->priapeacu = $priapeacu;
    }

    public function setSegapeacu($segapeacu)
    {
        $this->segapeacu = $segapeacu;
    }

    public function setTelacu($telacu)
    {
        $this->telacu = $telacu;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTipideacu()
    {
        return $this->tipideacu;
    }

    public function getNumideacu()
    {
        return $this->numideacu;
    }

    public function getPrinomacu()
    {
        return $this->prinomacu;
    }

    public function getSegnomacu()
    {
        return $this->segnomacu;
    }

    public function getPriapeacu()
    {
        return $this->priapeacu;
    }

    public function getSegapeacu()
    {
        return $this->segapeacu;
    }

    public function getTelacu()
    {
        return $this->telacu;
    }

    public function getNombreCompleto()
    {
        return trim($this->priapeacu.' '.$this->segapeacu.' '.$this->prinomacu.' '.$this->segnomacu);
    }
}
