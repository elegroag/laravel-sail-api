<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;
use Illuminate\Validation\Rule;

class Xml4d088 extends ModelBase
{
    protected $table = 'xml4d088';

    public $timestamps = false;

    protected $primaryKey = 'codinf';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'codinf',
        'divpol',
        'tipinf',
        'nomcom',
        'direccion',
        'tenencia',
        'capacidad',
        'estado',
        'geolat',
        'geolon',
        'codare',
        'version',
    ];

    protected function rules()
    {
        return [
            'estado' => [
                'required',
                Rule::in(['A', 'I']),
            ],
        ];
    }

    // Setters
    public function setCodinf($codinf)
    {
        $this->codinf = $codinf;
    }

    public function setDivpol($divpol)
    {
        $this->divpol = $divpol;
    }

    public function setTipinf($tipinf)
    {
        $this->tipinf = $tipinf;
    }

    public function setNomcom($nomcom)
    {
        $this->nomcom = $nomcom;
    }

    public function setDireccion($direccion)
    {
        $this->direccion = $direccion;
    }

    public function setTenencia($tenencia)
    {
        $this->tenencia = $tenencia;
    }

    public function setCapacidad($capacidad)
    {
        $this->capacidad = $capacidad;
    }

    public function setEstado($estado)
    {
        $this->estado = $estado;
    }

    public function setGeolat($geolat)
    {
        $this->geolat = $geolat;
    }

    public function setGeolon($geolon)
    {
        $this->geolon = $geolon;
    }

    public function setCodare($codare)
    {
        $this->codare = $codare;
    }

    public function setVersion($version)
    {
        $this->version = $version;
    }

    // Getters
    public function getCodinf()
    {
        return $this->codinf;
    }

    public function getDivpol()
    {
        return $this->divpol;
    }

    public function getTipinf()
    {
        return $this->tipinf;
    }

    public function getNomcom()
    {
        return $this->nomcom;
    }

    public function getDireccion()
    {
        return $this->direccion;
    }

    public function getTenencia()
    {
        return $this->tenencia;
    }

    public function getCapacidad()
    {
        return $this->capacidad;
    }

    public function getEstado()
    {
        return $this->estado;
    }

    public function getGeolat()
    {
        return $this->geolat;
    }

    public function getGeolon()
    {
        return $this->geolon;
    }

    public function getCodare()
    {
        return $this->codare;
    }

    public function getVersion()
    {
        return $this->version;
    }

    public function getNcodareDetalle()
    {
        $area = $this->area();

        return $area ? $area->getNombre() : '';
    }

    // Relaciones Eloquent
    public function ciudad()
    {
        return $this->belongsTo(Gener08::class, 'divpol', 'codciu');
    }

    public function tipoInformacion()
    {
        return $this->belongsTo(Xml4b010::class, 'tipinf', 'tipinf');
    }

    public function tipoTenencia()
    {
        return $this->belongsTo(Xml4b011::class, 'tenencia', 'tenencia');
    }

    public function area()
    {
        return $this->belongsTo(Xml4b064::class, 'codare', 'codare');
    }
}
