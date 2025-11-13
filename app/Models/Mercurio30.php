<?php

namespace App\Models;

use App\Models\Adapter\DbBase;
use App\Models\Adapter\ModelBase;
use App\Models\Adapter\HasCustomUuid;
use Carbon\Carbon;

class Mercurio30 extends ModelBase
{
    use HasCustomUuid;

    protected $table = 'mercurio30';

    protected $primaryKey = 'id';

    public $incrementing = true;

    public $timestamps = false;

    protected $fillable = [
        'log',
        'nit',
        'tipdoc',
        'razsoc',
        'sigla',
        'digver',
        'calemp',
        'cedrep',
        'repleg',
        'direccion',
        'codciu',
        'codzon',
        'telefono',
        'celular',
        'fax',
        'email',
        'codact',
        'fecini',
        'tottra',
        'valnom',
        'tipsoc',
        'estado',
        'codest',
        'motivo',
        'fecest',
        'usuario',
        'dirpri',
        'ciupri',
        'telpri',
        'celpri',
        'emailpri',
        'tipo',
        'coddoc',
        'documento',
        'tipemp',
        'tipper',
        'prinom',
        'segnom',
        'priape',
        'segape',
        'matmer',
        'coddocrepleg',
        'priaperepleg',
        'segaperepleg',
        'prinomrepleg',
        'segnomrepleg',
        'codcaj',
        'sat_fecapr',
        'sat_cedrep',
        'sat_numtra',
        'fecapr',
        'fecsol',
        'ruuid',
        'barnotif',
        'barcomer'
    ];

    public function getFecsol()
    {
        return $this->fecsol;
    }

    public function setFecsol($fecsol)
    {
        $this->fecsol = $fecsol;
    }

    public function getNumero_transaccion()
    {
        return $this->numero_transaccion;
    }

    public function setNumero_transaccion($numero_transaccion)
    {
        $this->numero_transaccion = $numero_transaccion;
    }

    public function setFechaAprobacionSat($fecha)
    {
        $this->fecha_aprobacion_sat = $fecha;
    }

    public function getFechaAprobacionSat()
    {
        return $this->fecha_aprobacion_sat;
    }

    public function setDocumentoRepresentanteSat($documento)
    {
        $this->documento_representante_sat = $documento;
    }

    public function getDocumentoRepresentanteSat()
    {
        return $this->documento_representante_sat;
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

    public function setTipemp($tipemp)
    {
        $this->tipemp = $tipemp;
    }

    public function setTipdoc($tipdoc)
    {
        $this->tipdoc = $tipdoc;
    }

    public function setRazsoc($razsoc)
    {
        $this->razsoc = $razsoc;
    }

    public function setSigla($sigla)
    {
        $this->sigla = $sigla;
    }

    public function setDigver($digver)
    {
        $this->digver = $digver;
    }

    public function setCalemp($calemp)
    {
        $this->calemp = $calemp;
    }

    public function setCedrep($cedrep)
    {
        $this->cedrep = $cedrep;
    }

    public function setRepleg($repleg)
    {
        $this->repleg = $repleg;
    }

    public function setDireccion($direccion)
    {
        $this->direccion = $direccion;
    }

    public function setCodciu($codciu)
    {
        $this->codciu = $codciu;
    }

    public function setCodzon($codzon)
    {
        $this->codzon = $codzon;
    }

    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;
    }

    public function setCelular($celular)
    {
        $this->celular = $celular;
    }

    public function setFax($fax)
    {
        $this->fax = $fax;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function setCodact($codact)
    {
        $this->codact = $codact;
    }

    public function setFecini($fecini)
    {
        $this->fecini = $fecini;
    }

    public function setTottra($tottra)
    {
        $this->tottra = $tottra;
    }

    public function setValnom($valnom)
    {
        $this->valnom = $valnom;
    }

    public function setTipsoc($tipsoc)
    {
        $this->tipsoc = $tipsoc;
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

    public function setUsuario($usuario)
    {
        $this->usuario = $usuario;
    }

    public function setDirpri($dirpri)
    {
        $this->dirpri = $dirpri;
    }

    public function setCiupri($ciupri)
    {
        $this->ciupri = $ciupri;
    }

    public function setTelpri($telpri)
    {
        $this->telpri = $telpri;
    }

    public function setCelpri($celpri)
    {
        $this->celpri = $celpri;
    }

    public function setEmailpri($emailpri)
    {
        $this->emailpri = $emailpri;
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

    public function getTipdoc()
    {
        return $this->tipdoc;
    }

    public function getRazsoc()
    {
        return $this->razsoc;
    }

    public function getSigla()
    {
        return $this->sigla;
    }

    public function getDigver()
    {
        return $this->digver;
    }

    public function getCalemp()
    {
        return $this->calemp;
    }

    public function getCedrep()
    {
        return $this->cedrep;
    }

    public function getRepleg()
    {
        return $this->repleg;
    }

    public function getDireccion()
    {
        return $this->direccion;
    }

    public function getCodciu()
    {
        return $this->codciu;
    }

    public function getCodzon()
    {
        return $this->codzon;
    }

    public function getTelefono()
    {
        return $this->telefono;
    }

    public function getCelular()
    {
        return $this->celular;
    }

    public function getFax()
    {
        return $this->fax;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getCodact()
    {
        return $this->codact;
    }

    public function getFecini()
    {
        if ($this->fecini) {
            return Carbon::parse($this->fecini);
        } else {
            return null;
        }
    }

    public function getTottra()
    {
        return $this->tottra;
    }

    public function getValnom()
    {
        return $this->valnom;
    }

    public function getTipsoc()
    {
        return $this->tipsoc;
    }

    public function getEstado()
    {
        return $this->estado;
    }

    public function getEstadoDetalle()
    {
        return estado_detalle_value($this->estado);
    }

    public function getCodest()
    {
        return $this->codest;
    }

    public function getMotivo()
    {
        return $this->motivo;
    }

    public function getFecest()
    {
        if ($this->fecest) {
            return Carbon::parse($this->fecest);
        } else {
            return null;
        }
    }

    public function getUsuario()
    {
        return $this->usuario;
    }

    public function getDirpri()
    {
        return $this->dirpri;
    }

    public function getCiupri()
    {
        return $this->ciupri;
    }

    public function getTelpri()
    {
        return $this->telpri;
    }

    public function getCelpri()
    {
        return $this->celpri;
    }

    public function getEmailpri()
    {
        return $this->emailpri;
    }

    public function getTipo()
    {
        return $this->tipo;
    }

    public function getCoddoc()
    {
        return $this->coddoc;
    }

    public function getDocumento()
    {
        return $this->documento;
    }

    public function getTipemp()
    {
        return $this->tipemp;
    }

    public function setPrinom($prinom)
    {
        $this->prinom = $prinom;
    }

    public function setSegnom($segnom)
    {
        $this->segnom = $segnom;
    }

    public function setPriape($priape)
    {
        $this->priape = $priape;
    }

    public function setSegape($segape)
    {
        $this->segape = $segape;
    }

    public function setMatmer($matmer)
    {
        $this->matmer = $matmer;
    }

    public function setTipper($tipper)
    {
        $this->tipper = $tipper;
    }

    public function setCoddocrepleg($coddocrepleg)
    {
        $this->coddocrepleg = $coddocrepleg;
    }

    public function setPriaperepleg($priaperepleg)
    {
        $this->priaperepleg = $priaperepleg;
    }

    public function setSegaperepleg($segaperepleg)
    {
        $this->segaperepleg = $segaperepleg;
    }

    public function setPrinomrepleg($prinomrepleg)
    {
        $this->prinomrepleg = $prinomrepleg;
    }

    public function setSegnomrepleg($segnomrepleg)
    {
        $this->segnomrepleg = $segnomrepleg;
    }

    public function setCodcaj($codcaj)
    {
        $this->codcaj = $codcaj;
    }

    public function getPrinom()
    {
        return trim($this->prinom);
    }

    public function getSegnom()
    {
        return trim($this->segnom);
    }

    public function getPriape()
    {
        return trim($this->priape);
    }

    public function getSegape()
    {
        return trim($this->segape);
    }

    public function getMatmer()
    {
        return trim($this->matmer);
    }

    public function getTipper()
    {
        return trim($this->tipper);
    }

    public function getTipperArray()
    {
        return tipper_array();
    }

    public function getCoddocrepleg()
    {
        return trim($this->coddocrepleg);
    }

    public function getPriaperepleg()
    {
        return trim($this->priaperepleg);
    }

    public function getSegaperepleg()
    {
        return trim($this->segaperepleg);
    }

    public function getPrinomrepleg()
    {
        return trim($this->prinomrepleg);
    }

    public function getSegnomrepleg()
    {
        return trim($this->segnomrepleg);
    }

    public function getCodcaj()
    {
        return trim($this->codcaj);
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

    public function getCalempDetalle()
    {
        return calemp_detalle_value($this->calemp);
    }

    public function getCalempArray()
    {
        return calemp_array();
    }

    public function getCoddocreplegArray()
    {
        return coddoc_repleg_array();
    }

    public function CamposDisponibleDetalle($campo)
    {
        $data = $this->CamposDisponibles();

        return $data["{$campo}"];
    }

    public function getFeciniString()
    {
        return (isset($this->fecini)) ? $this->fecini : null;
    }

    public function solicitante()
    {
        return $this->belongsTo(Mercurio07::class, 'documento', 'documento');
    }
}
