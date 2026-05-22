<?php

namespace App\Models;

use App\Models\Adapter\DbBase;
use App\Models\Adapter\HasCustomUuid;
use App\Models\Adapter\ModelBase;
// Agregado
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class Mercurio34 extends ModelBase
{
    use HasCustomUuid;

    protected $table = 'mercurio34';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $fillable = [
        'log',
        'nit',
        'cedtra',
        'cedcon',
        'numdoc',
        'tipdoc',
        'priape',
        'segape',
        'prinom',
        'segnom',
        'fecnac',
        'ciunac',
        'sexo',
        'parent',
        'huerfano',
        'tiphij',
        'nivedu',
        'captra',
        'tipdis',
        'calendario',
        'usuario',
        'estado',
        'codest',
        'motivo',
        'fecest',
        'codben',
        'tipo',
        'coddoc',
        'documento',
        'cedacu',
        'resguardo_id',
        'pub_indigena_id',
        'peretn',
        'celular',
        'codban',
        'tipcue',
        'numcue',
        'tippag',
        'biocedu',
        'biotipdoc',
        'bioprinom',
        'biosegnom',
        'biopriape',
        'biosegape',
        'bioemail',
        'biophone',
        'biocodciu',
        'biodire',
        'biourbana',
        'biodesco',
        'fecsol',
        'fecapr',
        'ruuid'
    ];

    public function setBiodesco($biodesco)
    {
        $this->biodesco = $biodesco;
    }

    public function getBiodesco()
    {
        return $this->biodesco;
    }

    public function setBiocedu($biocedu)
    {
        $this->biocedu = $biocedu;
    }

    public function getBiocedu()
    {
        return $this->biocedu;
    }

    public function setBiotipdoc($biotipdoc)
    {
        $this->biotipdoc = $biotipdoc;
    }

    public function getBiotipdoc()
    {
        return $this->biotipdoc;
    }

    public function setBioprinom($bioprinom)
    {
        $this->bioprinom = $bioprinom;
    }

    public function getBioprinom()
    {
        return $this->bioprinom;
    }

    public function setBiosegnom($biosegnom)
    {
        $this->biosegnom = $biosegnom;
    }

    public function getBiosegnom()
    {
        return $this->biosegnom;
    }

    public function setBiopriape($biopriape)
    {
        $this->biopriape = $biopriape;
    }

    public function getBiopriape()
    {
        return $this->biopriape;
    }

    public function setBiosegape($biosegape)
    {
        $this->biosegape = $biosegape;
    }

    public function getBiosegape()
    {
        return $this->biosegape;
    }

    public function setBioemail($bioemail)
    {
        $this->bioemail = $bioemail;
    }

    public function getBioemail()
    {
        return $this->bioemail;
    }

    public function setBiophone($biophone)
    {
        $this->biophone = $biophone;
    }

    public function getBiophone()
    {
        return $this->biophone;
    }

    public function setBiocodciu($biocodciu)
    {
        $this->biocodciu = $biocodciu;
    }

    public function getBiocodciu()
    {
        return $this->biocodciu;
    }

    public function setBiodire($biodire)
    {
        $this->biodire = $biodire;
    }

    public function getBiodire()
    {
        return $this->biodire;
    }

    public function setBiourbana($biourbana)
    {
        $this->biourbana = $biourbana;
    }

    public function getBiourbana()
    {
        return $this->biourbana;
    }

    public function getTippag()
    {
        return $this->tippag;
    }

    public function setTippag($tippag)
    {
        $this->tippag = $tippag;
    }

    public function getNumcue()
    {
        return $this->numcue;
    }

    public function setNumcue($numcue)
    {
        $this->numcue = $numcue;
    }

    public function setTipcue($tipcue)
    {
        $this->tipcue = $tipcue;
    }

    public function getTipcue()
    {
        return $this->tipcue;
    }

    public function setCodban($codban)
    {
        $this->codban = $codban;
    }

    public function getCodban()
    {
        return $this->codban;
    }

    public function setCelular($celular)
    {
        $this->celular = $celular;
    }

    public function getCelular()
    {
        return $this->celular;
    }

    public function setPeretn($peretn)
    {
        $this->peretn = $peretn;
    }

    public function getPeretn()
    {
        return $this->peretn;
    }

    public function getResguardo_id()
    {
        return $this->resguardo_id;
    }

    public function setResguardo_id($id)
    {
        $this->resguardo_id = $id;
    }

    public function getPub_indigena_id()
    {
        return $this->pub_indigena_id;
    }

    public function setPub_indigena_id($id)
    {
        $this->pub_indigena_id = $id;
    }

    public function setFecsol($fecsol)
    {
        $this->fecsol = $fecsol;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setLog($log)
    {
        $this->log = $log;
    }

    public function setNit($nit)
    {
        $this->nit = $nit;
    }

    public function setCedtra($cedtra)
    {
        $this->cedtra = $cedtra;
    }

    public function setCedcon($cedcon)
    {
        $this->cedcon = $cedcon;
    }

    public function setNumdoc($numdoc)
    {
        $this->numdoc = $numdoc;
    }

    public function setTipdoc($tipdoc)
    {
        $this->tipdoc = $tipdoc;
    }

    public function setPriape($priape)
    {
        $this->priape = $priape;
    }

    public function setSegape($segape)
    {
        $this->segape = $segape;
    }

    public function setPrinom($prinom)
    {
        $this->prinom = $prinom;
    }

    public function setSegnom($segnom)
    {
        $this->segnom = $segnom;
    }

    public function setFecnac($fecnac)
    {
        $this->fecnac = $fecnac;
    }

    public function setCiunac($ciunac)
    {
        $this->ciunac = $ciunac;
    }

    public function setSexo($sexo)
    {
        $this->sexo = $sexo;
    }

    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    public function setHuerfano($huerfano)
    {
        $this->huerfano = $huerfano;
    }

    public function setTiphij($tiphij)
    {
        $this->tiphij = $tiphij;
    }

    public function setNivedu($nivedu)
    {
        $this->nivedu = $nivedu;
    }

    public function setCaptra($captra)
    {
        $this->captra = $captra;
    }

    public function setTipdis($tipdis)
    {
        $this->tipdis = $tipdis;
    }

    public function setCalendario($calendario)
    {
        $this->calendario = $calendario;
    }

    public function setUsuario($usuario)
    {
        $this->usuario = $usuario;
    }

    public function setEstado($estado)
    {
        $this->estado = $estado;
    }

    public function setCodest($codest)
    {
        $this->codest = $codest;
    }

    public function setMotivo($motivo)
    {
        $this->motivo = $motivo;
    }

    public function setFecest($fecest)
    {
        $this->fecest = $fecest;
    }

    public function setCodben($codben)
    {
        $this->codben = $codben;
    }

    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    }

    public function setCoddoc($coddoc)
    {
        $this->coddoc = $coddoc;
    }

    public function setDocumento($documento)
    {
        $this->documento = $documento;
    }

    public function setCedacu($cedacu)
    {
        $this->cedacu = $cedacu;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getLog()
    {
        return $this->log;
    }

    public function getNit()
    {
        return $this->nit;
    }

    public function getCedtra()
    {
        return $this->cedtra;
    }

    public function getCedcon()
    {
        return $this->cedcon;
    }

    public function getNumdoc()
    {
        return $this->numdoc;
    }

    public function getTipdoc()
    {
        return $this->tipdoc;
    }

    public function getPriape()
    {
        return $this->priape;
    }

    public function getSegape()
    {
        return $this->segape;
    }

    /**
     * Devuelve el valor del campo prinom
     *
     * @return string
     */
    public function getPrinom()
    {
        return $this->prinom;
    }

    /**
     * Devuelve el valor del campo segnom
     *
     * @return string
     */
    public function getSegnom()
    {
        return $this->segnom;
    }

    /**
     * Devuelve el valor del campo fecnac
     */
    public function getFecnac()
    {
        return Carbon::parse($this->fecnac);
    }

    /**
     * Devuelve el valor del campo ciunac
     *
     * @return string
     */
    public function getCiunac()
    {
        return $this->ciunac;
    }

    /**
     * Devuelve el valor del campo sexo
     *
     * @return string
     */
    public function getSexo()
    {
        return $this->sexo;
    }

    /**
     * Devuelve el valor del campo parent
     *
     * @return string
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Devuelve el valor del campo huerfano
     *
     * @return string
     */
    public function getHuerfano()
    {
        return $this->huerfano;
    }

    /**
     * Devuelve el valor del campo tiphij
     *
     * @return string
     */
    public function getTiphij()
    {
        return $this->tiphij;
    }

    /**
     * Devuelve el valor del campo nivedu
     *
     * @return string
     */
    public function getNivedu()
    {
        return $this->nivedu;
    }

    /**
     * Devuelve el valor del campo captra
     *
     * @return string
     */
    public function getCaptra()
    {
        return $this->captra;
    }

    /**
     * Devuelve el valor del campo tipdis
     *
     * @return string
     */
    public function getTipdis()
    {
        return $this->tipdis;
    }

    /**
     * Devuelve el valor del campo calendario
     *
     * @return string
     */
    public function getCalendario()
    {
        return $this->calendario;
    }

    /**
     * Devuelve el valor del campo usuario
     *
     * @return int
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * Devuelve el valor del campo estado
     *
     * @return string
     */
    public function getEstado()
    {
        return $this->estado;
    }

    public function getEstadoDetalle()
    {
        return solicitud_estado_detalle($this->estado);
    }

    /**
     * Devuelve el valor del campo codest
     *
     * @return string
     */
    public function getCodest()
    {
        return $this->codest;
    }

    /**
     * Devuelve el valor del campo motivo
     *
     * @return string
     */
    public function getMotivo()
    {
        return $this->motivo;
    }

    /**
     * Devuelve el valor del campo fecest
     */
    public function getFecest()
    {
        return Carbon::parse($this->fecest);
    }

    /**
     * Devuelve el valor del campo codben
     *
     * @return int
     */
    public function getCodben()
    {
        return $this->codben;
    }

    /**
     * Devuelve el valor del campo tipo
     *
     * @return string
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Devuelve el valor del campo coddoc
     *
     * @return string
     */
    public function getCoddoc()
    {
        return $this->coddoc;
    }

    /**
     * Devuelve el valor del campo documento
     *
     * @return string
     */
    public function getDocumento()
    {
        return $this->documento;
    }

    public function getCedacu()
    {
        return $this->cedacu;
    }

    public function getFecsol()
    {
        return $this->fecsol;
    }

    public function getConvive()
    {
        return convive_array();
    }

    public function CamposDisponibles()
    {
        $db = DbBase::rawConnect();
        $rqs = $db->inQueryAssoc('SELECT * FROM mercurio12');
        $data = [];
        foreach ($rqs as $ai => $row) {
            $data[$row['coddoc']] = $row['detalle'];
        }

        return $data;
    }

    public function CamposDisponibleDetalle($campo)
    {
        $data = $this->CamposDisponibles();

        return $data["{$campo}"];
    }

    public function createAttributes($data)
    {
        parent::setCreateAttributes($this, $data);
    }

    public function solicitante()
    {
        return $this->belongsTo(Mercurio07::class, 'documento', 'documento');
    }

    public function rulesValiation()
    {
        return [
            // integer
            "log"               => "required|integer|min:0",
            "usuario"           => "required|integer|min:0",
            "codben"            => "nullable|min:0",
            // char
            "nit"               => "max:15",
            "cedtra"            => "required|max:15",
            "cedcon"            => "max:15",
            "numdoc"            => "required|max:15",
            "tipdoc"            => "required|max:2",
            "priape"            => "required|max:20",
            "prinom"            => "required|max:20",
            "segape"            => "max:20",
            "segnom"            => "max:20",
            "ciunac"            => "required|max:5",
            "sexo"              => "required|max:1",
            "parent"            => "required|max:13",
            "huerfano"          => "required|max:1",
            "tiphij"            => "max:1",
            "nivedu"            => "max:3",
            "captra"            => "max:1",
            "tipdis"            => "max:2",
            "calendario"        => "max:1",
            "estado"            => "required|max:1",
            "codest"            => "max:2",
            "tipo"              => "required|max:2",
            "coddoc"            => "required|max:2",
            "documento"         => "required|max:15",
            "cedacu"            => "max:20",
            "resguardo_id"      => "max:4",
            "pub_indigena_id"   => "max:4",
            "peretn"            => "max:2",
            "codban"            => "max:4",
            "tipcue"            => "max:1",
            "tippag"            => "max:1",
            "biocedu"           => "max:18",
            "biotipdoc"         => "max:3",
            "biophone"          => "max:12",
            "biocodciu"         => "max:5",
            "biourbana"         => "max:1",
            "biodesco"          => "max:1",
            // unsignedBigInteger
            "numcue"            => "nullable|min:0",
            // string
            "bioprinom"         => "max:34",
            "biosegnom"         => "max:34",
            "biopriape"         => "max:34",
            "biosegape"         => "max:34",
            "bioemail"          => "nullable|email|max:142",
            "biodire"           => "max:142",
            // date
            "fecnac"            => "required|date",
            "fecest"            => "nullable|date",
            "fecsol"            => "date",
            "fecapr"            => "nullable|date",
            // uuid
            "ruuid"             => "required",
        ];
    }

    public function isValid(?array $rules = null)
    {
        $rules = (!$rules) ? $this->rulesValiation() : $rules;
        return Validator::make($this->attributes, $rules, [
            'required' => 'El :attribute campo es requirido.',
            'same'     => 'El :attribute and :other must match.',
            'size'     => 'El :attribute must be exactly :size.',
            'between'  => 'El :attribute valor :input no esta entre :min - :max.',
            'in'       => 'El :attribute must be one of the following types: :values.',
            'email.required' => 'Se requiere la dirección de email!',
        ]);
    }
}
