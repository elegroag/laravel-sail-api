<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;

class Mercurio50 extends ModelBase
{
    protected $table = 'mercurio50';
    public $timestamps = false;
    protected $primaryKey = 'codapl';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'codapl',
        'webser',
        'path',
        'urlonl',
        'puncom',
    ];

    // Setters
    public function setPuncom($puncom)
    {
        $this->puncom = $puncom;
    }

    public function setCodapl($codapl)
    {
        $this->codapl = $codapl;
    }

    public function setWebser($webser)
    {
        $this->webser = $webser;
    }

    public function setPath($path)
    {
        $this->path = $path;
    }

    public function setUrlonl($urlonl)
    {
        $this->urlonl = $urlonl;
    }

    // Getters
    public function getPuncom()
    {
        return $this->puncom;
    }

    public function getCodapl()
    {
        return $this->codapl;
    }

    public function getWebser()
    {
        return $this->webser;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getUrlonl()
    {
        return $this->urlonl;
    }
}
