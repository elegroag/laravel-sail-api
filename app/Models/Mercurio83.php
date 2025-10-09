<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;
use Carbon\Carbon;

class Mercurio83 extends ModelBase
{
    protected $table = 'mercurio83';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $fillable = [
        'tipben',
        'tipideben',
        'numideben',
        'prinomben',
        'segnomben',
        'priapeben',
        'segapeben',
        'tipgenben',
        'fecnacben',
        'codpaiben',
        'ciunacben',
        'fecafiben',
        'ciuresben',
        'codareresben',
        'direccionben',
        'codgru',
        'codpob',
        'facvul',
        'tipjor',
        'idacudiente',
        'nivedu',
        'codgra',
    ];

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setTipben($tipben)
    {
        $this->tipben = $tipben;
    }

    public function setTipideben($tipideben)
    {
        $this->tipideben = $tipideben;
    }

    public function setNumideben($numideben)
    {
        $this->numideben = $numideben;
    }

    public function setPrinomben($prinomben)
    {
        $this->prinomben = $prinomben;
    }

    public function setSegnomben($segnomben)
    {
        $this->segnomben = $segnomben;
    }

    public function setPriapeben($priapeben)
    {
        $this->priapeben = $priapeben;
    }

    public function setSegapeben($segapeben)
    {
        $this->segapeben = $segapeben;
    }

    public function setTipgenben($tipgenben)
    {
        $this->tipgenben = $tipgenben;
    }

    public function setFecnacben($fecnacben)
    {
        $this->fecnacben = $fecnacben;
    }

    public function setCodpaiben($codpaiben)
    {
        $this->codpaiben = $codpaiben;
    }

    public function setCiunacben($ciunacben)
    {
        $this->ciunacben = $ciunacben;
    }

    public function setFecafiben($fecafiben)
    {
        $this->fecafiben = $fecafiben;
    }

    public function setCiuresben($ciuresben)
    {
        $this->ciuresben = $ciuresben;
    }

    public function setCodareresben($codareresben)
    {
        $this->codareresben = $codareresben;
    }

    public function setDireccionben($direccionben)
    {
        $this->direccionben = $direccionben;
    }

    public function setCodgru($codgru)
    {
        $this->codgru = $codgru;
    }

    public function setCodpob($codpob)
    {
        $this->codpob = $codpob;
    }

    public function setFacvul($facvul)
    {
        $this->facvul = $facvul;
    }

    public function setTipjor($tipjor)
    {
        $this->tipjor = $tipjor;
    }

    public function setIdacudiente($idacudiente)
    {
        $this->idacudiente = $idacudiente;
    }

    public function setNivedu($nivedu)
    {
        $this->nivedu = $nivedu;
    }

    public function setCodgra($codgra)
    {
        $this->codgra = $codgra;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTipben()
    {
        return $this->tipben;
    }

    public function getTipideben()
    {
        return $this->tipideben;
    }

    public function getNumideben()
    {
        return $this->numideben;
    }

    public function getPrinomben()
    {
        return $this->prinomben;
    }

    public function getSegnomben()
    {
        return $this->segnomben;
    }

    public function getPriapeben()
    {
        return $this->priapeben;
    }

    public function getSegapeben()
    {
        return $this->segapeben;
    }

    public function getTipgenben()
    {
        return $this->tipgenben;
    }

    public function getFecnacben()
    {
        return Carbon::parse($this->fecnacben);
    }

    public function getCodpaiben()
    {
        return $this->codpaiben;
    }

    public function getCiunacben()
    {
        return $this->ciunacben;
    }

    public function getFecafiben()
    {
        return $this->fecafiben;
    }

    public function getCiuresben()
    {
        return $this->ciuresben;
    }

    public function getCodareresben()
    {
        return $this->codareresben;
    }

    public function getDireccionben()
    {
        return $this->direccionben;
    }

    public function getCodgru()
    {
        return $this->codgru;
    }

    public function getCodpob()
    {
        return $this->codpob;
    }

    public function getFacvul()
    {
        return $this->facvul;
    }

    public function getTipjor()
    {
        return $this->tipjor;
    }

    public function getIdacudiente()
    {
        return $this->idacudiente;
    }

    public function getNivedu()
    {
        return $this->nivedu;
    }

    public function getCodgra()
    {
        return $this->codgra;
    }

    public function getNombreCompleto()
    {
        return trim($this->priapeben.' '.$this->segapeben.' '.$this->prinomben.' '.$this->segnomben);
    }
}
