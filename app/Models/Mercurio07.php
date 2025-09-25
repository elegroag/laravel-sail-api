<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;
use Carbon\Carbon;
use App\Models\Adapter\ValidateWithRules;
use Illuminate\Validation\Rule;
use Thiagoprz\CompositeKey\HasCompositeKey;

class Mercurio07 extends ModelBase
{
    use HasCompositeKey;
    use ValidateWithRules;

    protected $table = 'mercurio07';
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = ['documento', 'coddoc', 'tipo'];

    protected $fillable = [
        'tipo',
        'coddoc',
        'documento',
        'nombre',
        'email',
        'clave',
        'feccla',
        'autoriza',
        'codciu',
        'fecreg',
        'estado',
        'whatsapp',
        'fecha_syncron',
    ];

    protected function rules()
    {
        return [
            'documento' => 'required|numeric|min:5',
            'coddoc' => 'required|numeric|min:1',
            'tipo' => 'required|string|min:0',
            '_id' => [
                'required|string',
                Rule::unique('mercurio07')->where(function ($query) {
                    return $query->where('documento', $this->documento)
                        ->where('coddoc', $this->coddoc)
                        ->where('tipo', $this->tipo);
                }),
            ],
        ];
    }

    public function setFechaSyncron($fecha_syncron)
    {
        $this->fecha_syncron = $fecha_syncron;
    }

    public function getFechaSyncron()
    {
        return $this->fecha_syncron;
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

    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function setClave($clave)
    {
        $this->clave = $clave;
    }

    public function setFeccla($feccla)
    {
        $this->feccla = $feccla;
    }

    public function setCodciu($codciu)
    {
        $this->codciu = $codciu;
    }

    public function setAutoriza($autoriza)
    {
        $this->autoriza = $autoriza;
    }

    public function setFecreg($fecreg)
    {
        $this->fecreg = $fecreg;
    }

    public function setEstado($estado)
    {
        $this->estado = $estado;
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

    public function getNombre()
    {
        return $this->nombre;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getClave()
    {
        return $this->clave;
    }

    public function getFeccla()
    {
        return Carbon::parse($this->feccla);
    }

    public function getAutoriza()
    {
        return $this->autoriza;
    }

    public function getCodciu()
    {
        return $this->codciu;
    }

    public function getFecreg()
    {
        return Carbon::parse($this->fecreg);
    }

    public function getEstado()
    {
        return $this->estado;
    }

    public function getArrayEstados()
    {
        return array(
            'A' => 'ACTIVO',
            'I' => 'INACTIVO'
        );
    }

    public function getEstadoDetalle($estado = '')
    {
        if ($estado != '') {
            $this->estado = $estado;
        }
        switch ($this->estado) {
            case 'A':
                return 'ACTIVO';
                break;
            case 'I':
                return 'INACTIVO';
                break;
        }
        return false;
    }

    public function getArrayTipos()
    {
        return array(
            'P' => 'Particular',
            'T' => 'Trabajador',
            'E' => 'Empresa aportante',
            'I' => 'Independiente aportante',
            'O' => 'Pensionado',
            'F' => 'Facultativo',
            'S' => 'Servicio domestico'
        );
    }

    public function getTipoDetalle($tipo = '')
    {
        if ($tipo != '') {
            $this->tipo = $tipo;
        }
        switch ($this->tipo) {
            case 'P':
                return 'Particular';
                break;
            case 'T':
                return 'Trabajador';
                break;
            case 'E':
                return 'Empresa aportante';
                break;
            case 'I':
                return 'Independiente aportante';
                break;
            case 'O':
                return 'Pensionado aportante';
                break;
            case 'F':
                return 'Facultativo';
                break;
            case 'S':
                return 'Servicio domestico';
                break;
        }
        return false;
    }

    public function getWhatsapp()
    {
        return $this->whatsapp;
    }

    public function setWhatsapp($whatsapp)
    {
        $this->whatsapp = $whatsapp;
    }

    public function createAttributes($data)
    {
        parent::setCreateAttributes($this, $data);
    }

    public function empresas()
    {
        return $this->hasMany(Mercurio30::class, 'documento', 'documento');
    }

    public function trabajadores()
    {
        return $this->hasMany(Mercurio31::class, 'documento', 'documento');
    }

    public function conyuges()
    {
        return $this->hasMany(Mercurio32::class, 'documento', 'documento');
    }

    public function beneficiarios()
    {
        return $this->hasMany(Mercurio34::class, 'documento', 'documento');
    }
}
