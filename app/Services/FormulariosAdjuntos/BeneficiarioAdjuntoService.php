<?php

namespace App\Services\FormulariosAdjuntos;

use App\Library\Collections\ParamsBeneficiario;
use App\Library\Collections\ParamsTrabajador;
use App\Library\Tcpdf\KumbiaPDF;
use App\Models\Mercurio07;
use App\Models\Mercurio16;
use App\Models\Mercurio31;
use App\Models\Mercurio32;
use App\Models\Mercurio36;
use App\Models\Mercurio38;
use App\Models\Mercurio41;
use App\Services\Entidades\TrabajadorService;
use App\Services\Formularios\FactoryDocuments;
use App\Services\PreparaFormularios\CifrarDocumento;
use App\Services\Api\ApiSubsidio;
use App\Services\Formularios\Generation\DocumentGenerationManager;

class BeneficiarioAdjuntoService
{
    /**
     * request variable
     *
     * @var Mercurio34
     */
    private $request;

    private $lfirma;

    private $filename;

    private $outPdf;

    private $fhash;

    private $claveCertificado;

    private $user;

    private $tipo;

    public function __construct($request)
    {
        $this->user = session('user') ?? null;
        $this->tipo = session('tipo') ?? null;
        $this->request = $request;
        $this->initialize();
    }

    private function initialize()
    {
        $this->lfirma = Mercurio16::where([
            'documento' => $this->user['documento'],
            'coddoc' => $this->user['coddoc'],
        ])->first();

        $procesadorComando = new ApiSubsidio();
        $procesadorComando->send(
            [
                'servicio' => 'ComfacaAfilia',
                'metodo' => 'parametros_trabajadores',
            ]
        );

        $datos_captura = $procesadorComando->toArray();
        $paramsConyuge = new ParamsTrabajador;
        $paramsConyuge->setDatosCaptura($datos_captura);

        $procesadorComando = new ApiSubsidio();
        $procesadorComando->send(
            [
                'servicio' => 'ComfacaAfilia',
                'metodo' => 'parametros_beneficiarios',
            ]
        );
        $datos_captura = $procesadorComando->toArray();
        $paramsBeneficiario = new ParamsBeneficiario;
        $paramsBeneficiario->setDatosCaptura($datos_captura);
    }

    public function getTrabajador()
    {
        $trabajador = false;
        $mtrabajador = false;

        switch ($this->tipo) {
            case 'I':
                $mtrabajador = Mercurio41::where('cedtra', $this->request->cedtra)
                    ->where('documento', $this->request->documento)
                    ->where('coddoc', $this->request->coddoc)
                    ->first();
                break;
            case 'O':
                $mtrabajador = Mercurio38::where('cedtra', $this->request->cedtra)
                    ->where('documento', $this->request->documento)
                    ->where('coddoc', $this->request->coddoc)
                    ->first();
                break;
            case 'F':
                $mtrabajador = Mercurio36::where('cedtra', $this->request->cedtra)
                    ->where('documento', $this->request->documento)
                    ->where('coddoc', $this->request->coddoc)
                    ->first();
                break;
            case 'T':
            case 'E':
                $mtrabajador = Mercurio31::where('cedtra', $this->request->cedtra)
                    ->where('documento', $this->request->documento)
                    ->where('coddoc', $this->request->coddoc)
                    ->first();
                break;
            default:
                $trabajador = Mercurio31::where('cedtra', $this->request->cedtra)
                    ->where('documento', $this->request->documento)
                    ->where('coddoc', $this->request->coddoc)
                    ->whereIn('estado', ['A', 'P', 'T'])
                    ->first();
                break;
        }

        $trabajadorService = new TrabajadorService;
        $data = $trabajadorService->buscarTrabajadorSubsidio($this->request->cedtra);
        if ($data) {
            if ($mtrabajador) {
                $trabajador = clone $mtrabajador;
            } else {
                $trabajador = new Mercurio31;
                $trabajador->fill($data);
            }
        }

        return $trabajador;
    }

    public function formulario()
    {
        $solicitante = Mercurio07::where("documento", $this->request->documento)
            ->where("coddoc", $this->request->coddoc)
            ->where("tipo", $this->request->tipo)
            ->first();

        $this->filename = 'formulario-beneficiario-' . strtotime('now') . "_{$this->request->numdoc}.pdf";
        $manager = new DocumentGenerationManager();
        $manager->generate('api', 'beneficiario', [
            'categoria' => 'formulario',
            'output' => $this->filename,
            'template' => 'adicion-beneficiario.html',
            'beneficiario' => $this->request,
            'trabajador' => $this->getTrabajador(),
            'solicitante' => $solicitante,
            'bioconyu' => $this->getBiologioConyuge(),
        ]);

        $this->cifrarDocumento();
        return $this;
    }

    public function declaraJurament()
    {
        $parent = $this->request->parent;
        switch ($parent) {
            case '1':
                $template = 'declaracion-hijos.html';
                $this->filename = "declaracion_hijo_{$this->request->numdoc}.pdf";
                break;
            case '4':
                $template = 'declaracion-custodia.html';
                $this->filename = "declaracion_custodia_{$this->request->numdoc}.pdf";
                break;
            case '3': // padre
            case '2': // hermano
                $template = 'declaracion-padres.html';
                $this->filename = "declaracion_padres_{$this->request->numdoc}.pdf";
                break;
            case '5': // cuidador persona discapacitada
                $template = 'declaracion-cuidador.html';
                $this->filename = "declaracion_cuidador_{$this->request->numdoc}.pdf";
                break;
            default:
                break;
        }

        $manager = new DocumentGenerationManager();
        $manager->generate('api', 'beneficiario', [
            'categoria' => 'declaracion',
            'output' => $this->filename,
            'template' => $template,
            'beneficiario' => $this->request,
            'trabajador' => $this->getTrabajador(),
            'bioconyu' => $this->getBiologioConyuge(),
        ]);

        $this->cifrarDocumento();
        return $this;
    }

    public function getBiologioConyuge()
    {
        if (! $this->request->cedcon) {
            return false;
        }

        $mconyuge = Mercurio32::where('cedcon', $this->request->cedcon)
            ->where('documento', $this->request->documento)
            ->where('coddoc', $this->request->coddoc)
            ->first();

        if (!$mconyuge) {
            $mconyuge = new Mercurio32;
            $ps = new ApiSubsidio();
            $ps->send(
                [
                    'servicio' => 'ComfacaEmpresas',
                    'metodo' => 'informacion_conyuge',
                    'params' => $this->request->cedcon,
                ]
            );
            $data = false;
            $out = $ps->toArray();
            if ($out['success'] == true) {
                $data = ($out['data']) ? $out['data'] : false;
            }

            if ($data) {
                $mconyuge = new Mercurio32();
                $mconyuge->fill($data);
            }
        }
        if ($this->request->biocedu == $this->request->cedcon) {
            $mconyuge->cedcon = $this->request->biocedu;
            $mconyuge->tipdoc = $this->request->biotipdoc;
            $mconyuge->prinom = $this->request->bioprinom;
            $mconyuge->segnom = $this->request->biosegnom;
            $mconyuge->priape = $this->request->biopriape;
            $mconyuge->segape = $this->request->biosegape;
            $mconyuge->email = $this->request->bioemail;
            $mconyuge->telefono = $this->request->biophone;
            $mconyuge->ciures = $this->request->biocodciu;
            $mconyuge->direccion = $this->request->biodire;
            $mconyuge->zoneurbana = $this->request->biourbana;
        }

        return $mconyuge;
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
