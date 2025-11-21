<?php

namespace App\Services\FormulariosAdjuntos;

use App\Exceptions\DebugException;
use App\Library\Collections\ParamsEmpresa;
use App\Library\Tcpdf\KumbiaPDF;
use App\Models\Mercurio16;
use App\Models\Tranoms;
use App\Services\Formularios\FactoryDocuments;
use App\Services\PreparaFormularios\CifrarDocumento;
use App\Services\Api\ApiSubsidio;
use App\Services\Formularios\Generation\DocumentGenerationManager;
use App\Services\Utils\GuardarArchivoService;

class EmpresaAdjuntoService
{
    private $request;

    private $lfirma;

    private $filename;

    private $outPdf;

    private $fhash;

    private $user;

    private $claveCertificado;

    private const DOCUMENTOS = [
        [
            'method' => 'formulario',
            'coddoc' => 1,
        ],
        [
            'method' => 'tratamientoDatos',
            'coddoc' => 25,
        ],
        [
            'method' => 'cartaSolicitud',
            'coddoc' => 24,
        ],
        [
            'method' => 'trabajadoresNomina',
            'coddoc' => 11,
        ],
    ];

    public function __construct($request)
    {
        $this->user = session('user') ?? null;
        $this->request = $request;
        $this->initialize();
    }

    private function initialize(): void
    {
        $this->lfirma = Mercurio16::where('documento', $this->user['documento'])
            ->where('coddoc', $this->user['coddoc'])
            ->first();

        $procesadorComando = new ApiSubsidio();
        $procesadorComando->send(
            [
                'servicio' => 'ComfacaAfilia',
                'metodo' => 'parametros_empresa',
            ]
        );

        $datos_captura = $procesadorComando->toArray();
        $paramsEmpresa = new ParamsEmpresa;
        $paramsEmpresa->setDatosCaptura($datos_captura);
    }

    public function tratamientoDatos(): self
    {
        $this->filename = 'tratamiento_datos_empresa_' . strtotime('now') . "_{$this->request->nit}.pdf";
        $manager = new DocumentGenerationManager();
        $manager->generate('api', 'empresa', [
            'categoria' => 'politica',
            'output' => $this->filename,
            'template' => 'politica-empresa.html',
            'empresa' => $this->request,
        ]);
        $this->cifrarDocumento();
        return $this;
    }

    public function cartaSolicitud(): self
    {
        $this->filename = "carta_solicitud_empresa_{$this->request->getNit()}.pdf";
        $background = 'img/form/oficios/oficio_solicitud_empresa.jpg';
        KumbiaPDF::setFooterImage(false);

        $fabrica = new FactoryDocuments;
        $documento = $fabrica->crearOficio('empresa');
        $documento->setParamsInit(
            [
                'background' => $background,
                'empresa' => $this->request,
                'firma' => $this->lfirma,
                'filename' => $this->filename,
            ]
        );
        $documento->main();
        $documento->outPut();
        $this->cifrarDocumento();

        return $this;
    }

    public function formulario(): self
    {
        if (! $this->lfirma) {
            throw new DebugException('Error no hay firma digital', 501);
        }
        $this->filename = 'formulario-empresa-' . strtotime('now') . "_{$this->request->nit}.pdf";
        $manager = new DocumentGenerationManager();
        $manager->generate('api', 'empresa', [
            'categoria' => 'formulario',
            'output' => $this->filename,
            'template' => 'empresa.html',
            'empresa' => $this->request
        ]);

        $this->cifrarDocumento();
        return $this;
    }

    public function trabajadoresNomina(): self
    {
        KumbiaPDF::setBackgroundImage(false);
        KumbiaPDF::setFooterImage(false);

        $tranoms = Tranoms::where('request', $this->request->getId())->get();

        $this->filename = "tranom_empresa_{$this->request->getNit()}.pdf";

        $fabrica = new FactoryDocuments;
        $documento = $fabrica->crearOficio('trabajador_nomina');

        $documento->setParamsInit([
            'empresa' => $this->request,
            'firma' => $this->lfirma,
            'filename' => $this->filename,
            'tranoms' => $tranoms,
        ]);
        $documento->main();
        $documento->outPut();
        $this->cifrarDocumento();

        return $this;
    }

    public function cifrarDocumento(): void
    {
        $cifrarDocumento = new CifrarDocumento;
        $this->outPdf = $cifrarDocumento->cifrar(
            $this->filename,
            $this->lfirma->getKeyprivate(),
            $this->claveCertificado
        );
        $this->fhash = $cifrarDocumento->getFhash();
    }

    public function getResult(): array
    {
        return [
            'name' => $this->filename,
            'file' => basename($this->outPdf),
            'out' => $this->outPdf,
            'fhash' => $this->fhash,
        ];
    }

    public function setClaveCertificado($clave): void
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
