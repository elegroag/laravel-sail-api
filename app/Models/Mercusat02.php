<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;
use Carbon\Carbon;

class Mercusat02 extends ModelBase
{
    protected $table = 'mercusat02';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $fillable = [
        'fecsol',
        'fecapr',
        'indeti',
        'numtrasat',
        'documento',
        'coddoc',
    ];

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setFecsol($fecsol)
    {
        $this->fecsol = $fecsol;
    }

    public function setFecapr($fecapr)
    {
        $this->fecapr = $fecapr;
    }

    public function setIndeti($indeti)
    {
        $this->indeti = $indeti;
    }

    public function setNumtrasat($numtrasat)
    {
        $this->numtrasat = $numtrasat;
    }

    public function setDocumento($documento)
    {
        $this->documento = $documento;
    }

    public function setCoddoc($coddoc)
    {
        $this->coddoc = $coddoc;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getFecsol()
    {
        return Carbon::parse($this->fecsol);
    }

    public function getFecapr()
    {
        return Carbon::parse($this->fecapr);
    }

    public function getIndeti()
    {
        return $this->indeti;
    }

    public function getNumtrasat()
    {
        return $this->numtrasat;
    }

    public function getDocumento()
    {
        return $this->documento;
    }

    public function getCoddoc()
    {
        return $this->coddoc;
    }
}
