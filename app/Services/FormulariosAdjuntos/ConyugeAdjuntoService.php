<?php

namespace App\Services\FormulariosAdjuntos;

use App\Exceptions\DebugException;
use App\Library\Collections\ParamsConyuge;
use App\Library\Tcpdf\KumbiaPDF;
use App\Models\Mercurio07;
use App\Models\Mercurio16;
use App\Models\Mercurio31;
use App\Models\Mercurio36;
use App\Models\Mercurio38;
use App\Models\Mercurio41;
use App\Services\Entidades\TrabajadorService;
use App\Services\Formularios\Generation\DocumentGenerationManager;
use App\Services\PreparaFormularios\CifrarDocumento;
use App\Services\Api\ApiSubsidio;

class ConyugeAdjuntoService
{
    /**
     * request variable
     *
     * @var Mercurio32
     */
    private $request;

    private $filename;

    private $outPdf;

    private $fhash;

    private $claveCertificado;

    private $user;

    /**
     * lfirma variable
     *
     * @var Mercurio16
     */
    private $lfirma;

    public function __construct($request)
    {
        $this->user = session('user') ?? null;
        $this->request = $request;
        $this->initialize();
    }

    private function initialize()
    {
        $this->lfirma = Mercurio16::where("documento", $this->user['documento'])
            ->where("coddoc", $this->user['coddoc'])->first();
        $procesadorComando = new ApiSubsidio();
        $procesadorComando->send(
            [
                'servicio' => 'ComfacaAfilia',
                'metodo' => 'parametros_conyuges',
            ]
        );

        $datos_captura = $procesadorComando->toArray();
        $paramsConyuge = new ParamsConyuge;
        $paramsConyuge->setDatosCaptura($datos_captura);
    }

    public function formulario()
    {
        $solicitante = Mercurio07::where("documento", $this->request->documento)
            ->where("coddoc", $this->request->coddoc)
            ->where("tipo", $this->request->tipo)
            ->first();

        $this->filename = strtotime('now') . "_{$this->request->cedcon}.pdf";
        $manager = new DocumentGenerationManager();
        $manager->generate('api', 'conyuge', [
            'categoria' => 'formulario',
            'output' => $this->filename,
            'template' => 'adicion-conyuge.html',
            'conyuge' => $this->request,
            'trabajador' => $this->getTrabajador(),
            'solicitante' => $solicitante,
        ]);
        $this->cifrarDocumento();
        return $this;
    }

    public function getTrabajador()
    {
        $trabajador = false;
        $mtrabajador = false;
        switch (trim($this->request->tipo)) {
            case 'I':
                $mtrabajador = new Mercurio41;
                break;
            case 'O':
                $mtrabajador = new Mercurio38;
                break;
            case 'F':
                $mtrabajador = new Mercurio36;
                break;
            case 'T':
            case 'E':
                $mtrabajador = new Mercurio31;
                break;
            default:
                $trabajador = Mercurio31::where("documento", $this->request->documento)
                    ->where("coddoc", $this->request->coddoc)
                    ->where("cedtra", $this->request->cedtra)
                    ->where("estado", "NOT IN('X','I')")
                    ->first();
                break;
        }

        if (! $trabajador && $mtrabajador) {
            $trabajador = $mtrabajador::where("cedtra", $this->request->cedtra)
                ->where("documento", $this->request->documento)
                ->where("coddoc", $this->request->coddoc)
                ->first();
        }

        if (! $trabajador && $mtrabajador) {
            $trabajadorService = new TrabajadorService;
            $out = $trabajadorService->buscarTrabajadorSubsidio($this->request->cedtra);
            if ($out) {
                $trabajador = clone $mtrabajador;
                $trabajador->fill($out);
            }
        }

        if (! $trabajador) {
            throw new DebugException('Error el trabajador no está registrado previamente', 501);
        }

        return $trabajador;
    }

    public function declaraJurament()
    {
        $this->filename = strtotime('now') . "_{$this->request->cedcon}.pdf";
        $manager = new DocumentGenerationManager();
        $manager->generate('api', 'conyuge', [
            'categoria' => 'declaracion',
            'conyuge' => $this->request,
            'trabajador' => $this->getTrabajador(),
            'template' => 'declaracion-conyuge.html',
            'output' => $this->filename,
        ]);
        $this->cifrarDocumento();
        return $this;
    }

    public function cifrarDocumento()
    {
        $cifrarDocumento = new CifrarDocumento;
        $this->outPdf = $cifrarDocumento->cifrar($this->filename, $this->lfirma->getKeyprivate(), $this->claveCertificado);
        $this->fhash = $cifrarDocumento->getFhash();
    }

    public function getResult()
    {
        return [
            'name' => $this->filename,
            'file' => basename($this->outPdf),
            'out' => $this->outPdf,
            'fhash' => $this->fhash,
        ];
    }

    public function setClaveCertificado($clave)
    {
        $this->claveCertificado = $clave;
    }
}
