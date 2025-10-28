<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;

class Mercurio84 extends ModelBase
{
    protected $table = 'mercurio84';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $fillable = [
        'evento',
        'beneficiario',
    ];

    public function setEvento($evento)
    {
        $this->evento = $evento;
    }

    public function setBeneficiario($beneficiario)
    {
        $this->beneficiario = $beneficiario;
    }

    public function getEvento()
    {
        return $this->evento;
    }

    public function getBeneficiario()
    {
        return $this->beneficiario;
    }
}
