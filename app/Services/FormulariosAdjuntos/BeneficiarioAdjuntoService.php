<?php

namespace App\Services\FormulariosAdjuntos;

use App\Exceptions\DebugException;
use App\Library\Collections\ParamsBeneficiario;
use App\Library\Collections\ParamsTrabajador;
use App\Models\Mercurio07;
use App\Models\Mercurio16;
use App\Models\Mercurio31;
use App\Models\Mercurio32;
use App\Models\Mercurio36;
use App\Models\Mercurio38;
use App\Models\Mercurio41;
use App\Services\Entidades\TrabajadorService;
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

    private const DOCUMENTOS = [
        [
            'method' => 'formulario',
            'coddoc' => 1,
        ],
        [
            'method' => 'declaraJurament',
            'coddoc' => 4,
        ]
    ];

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
                $mtrabajador = Mercurio31::where('cedtra', $this->request->cedtra)->first();
                break;
            default:
                $trabajador = Mercurio31::where('cedtra', $this->request->cedtra)
                    ->where('documento', $this->request->documento)
                    ->where('coddoc', $this->request->coddoc)
                    ->whereIn('estado', ['A', 'P', 'T'])
                    ->first();
                break;
        }

        $trabajador = new Mercurio31;
        if ($mtrabajador) {
            $trabajador->fill($mtrabajador->toArray());
        } else {
            $trabajadorService = new TrabajadorService;
            $data = $trabajadorService->buscarTrabajadorSubsidio($this->request->cedtra);
            if ($data) {
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
            'biologico' => $this->getBiologioConyuge(),
        ]);

        $this->cifrarDocumento();
        return $this;
    }

    public function declaraJurament()
    {
        switch (strval($this->request->parent)) {
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
            'biologico' => $this->getBiologioConyuge(),
        ]);

        $this->cifrarDocumento();
        return $this;
    }

    public function getBiologioConyuge()
    {
        if ($this->request->parent == '3') return false;

        $mconyuge = Mercurio32::where('cedcon', $this->request->cedcon)
            ->where('documento', $this->request->documento)
            ->where('coddoc', $this->request->coddoc)
            ->first();

        // si no existe en la base de datos, buscar api externa como conyuge
        if (!$mconyuge && $this->request->biocedu) {
            $mconyuge = null;
            $ps = new ApiSubsidio();
            $ps->send(
                [
                    'servicio' => 'ComfacaEmpresas',
                    'metodo' => 'informacion_conyuge',
                    'params' => $this->request->biocedu,
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

        // si no existe en la base de datos, buscar api externa como trabajador
        if (!$mconyuge) {
            $ps = new ApiSubsidio();
            $ps->send(
                [
                    'servicio' => 'ComfacaEmpresas',
                    'metodo' => 'informacion_trabajador',
                    'params' => [
                        'cedtra' => $this->request->biocedu,
                    ],
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

        //Para tipo hijastro o donde se este reportando conyuge biologico.
        if ((!$mconyuge && $this->request->biocedu) || $this->request->tiphij == '2') {
            $mconyuge = new Mercurio32();
            $mconyuge->fill([
                'cedcon' => $this->request->biocedu,
                'tipdoc' => $this->request->biotipdoc,
                'prinom' => $this->request->bioprinom,
                'segnom' => $this->request->biosegnom,
                'priape' => $this->request->biopriape,
                'segape' => $this->request->biosegape,
                'email' => $this->request->bioemail,
                'telefono' => $this->request->biophone,
                'ciures' => $this->request->biocodciu,
                'direccion' => $this->request->biodire,
                'zoneurbana' => $this->request->biourbana,
            ]);
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
        if ($this->lfirma->password !== $clave) {
            throw new DebugException('Error la clave no coincide con la de la firma digital', 501);
        }
        $this->claveCertificado = $clave;
    }

    public static function generarAdjuntos($request, string $tipopc, ?string $claveCertificado = null): void
    {
        $adjuntoService = new self($request);
        $adjuntoService->setClaveCertificado($claveCertificado);
        AdjuntosGenerator::generar($adjuntoService, $tipopc, $request, self::DOCUMENTOS);
    }
}
