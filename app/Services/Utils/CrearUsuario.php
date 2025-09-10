<?php

namespace App\Services\Utils;

use App\Exceptions\DebugException;
use App\Models\Mercurio07;
use App\Models\Mercurio19;
use App\Services\Request;
use Carbon\Carbon;

class CrearUsuario
{
    private $today;
    private $tipo;
    private $coddoc;
    private $documento;
    private $nombre;
    private $email;
    private $codciu;
    private $clave;
    private $feccla;
    private $fecreg;
    private $autoriza = 'S';
    private $fecapr;

    public function __construct(Request|null $args = null)
    {
        $this->today = Carbon::parse('now');
        $this->codciu = '18001';
        if ($args instanceof Request) {
            $this->tipo = $args->getParam('tipo');
            $this->coddoc = $args->getParam('coddoc');
            $this->documento = $args->getParam('documento');
            $this->nombre = $args->getParam('nombre');
            $this->email = $args->getParam('email');
            $this->codciu = $args->getParam('codciu');
            $this->autoriza = $args->getParam('autoriza');
            $this->clave = $args->getParam('clave');
            $this->fecreg = $args->getParam('fecreg');
            $this->fecapr = $args->getParam('fecapr');
        }
    }

    public function setters(...$params)
    {
        $arguments = get_params_destructures($params);
        foreach ($arguments as $prop => $valor) if (property_exists($this, $prop)) $this->$prop = "{$valor}";
        return $this;
    }

    /**
     * crear_usuario function
     * @return Object
     */
    public function procesar()
    {
        $fecreg = ($this->fecreg) ? $this->fecreg : date('Y-m-d H:i:s');
        $feccla = ($this->feccla) ? $this->feccla : $this->today->addMonths(3);

        $mercurio07 = Mercurio07::where([
            "tipo" => $this->tipo,
            "coddoc" => $this->coddoc,
            "documento" => $this->documento
        ])->first();

        if ($mercurio07 == false) {
            $mercurio07 = new Mercurio07;
            $mercurio07->setTipo($this->tipo);
            $mercurio07->setCoddoc($this->coddoc);
            $mercurio07->setDocumento($this->documento);
            $mercurio07->setFecreg($fecreg);
            $mercurio07->setFeccla($feccla);
        }

        $mercurio07->setCodciu($this->codciu);
        $mercurio07->setEstado("A");
        $mercurio07->setFechaSyncron($this->today->format('Y-m-d'));
        $mercurio07->setAutoriza($this->autoriza);
        $mercurio07->setNombre($this->nombre);
        $mercurio07->setEmail($this->email);
        $mercurio07->setClave($this->clave);
        $mercurio07->save();
        return $mercurio07;
    }

    /**
     * crearOpcionesRecuperacion function
     * @param [type] $pregunta1
     * @param [type] $pregunta2
     * @param [type] $respuesta1
     * @param [type] $respuesta2
     * @return bool
     */
    public function crearOpcionesRecuperacion($codigo)
    {
        $mercurio19 = Mercurio19::where([
            "tipo" => $this->tipo,
            "coddoc" => $this->coddoc,
            "documento" => $this->documento
        ])->first();

        if (!$mercurio19) {
            $mercurio19 = new Mercurio19();
            $mercurio19->setTipo($this->tipo);
            $mercurio19->setCoddoc($this->coddoc);
            $mercurio19->setDocumento($this->documento);
            $mercurio19->setRespuesta('0');
        }
        $mercurio19->setInicio($this->today->format('Y-m-d'));
        $mercurio19->setIntentos(0);
        $mercurio19->setCodigo(1);
        $mercurio19->setCodver($codigo);
        $mercurio19->save();
        return true;
    }

    public static function generaCode()
    {
        $codigo_verify = "";
        $seed = str_split('1234567890');
        shuffle($seed);
        foreach (array_rand($seed, 4) as $k) $codigo_verify .= $seed[$k];
        return $codigo_verify;
    }
}
