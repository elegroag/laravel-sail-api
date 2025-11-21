<?php

namespace App\Services\FormulariosAdjuntos;

use App\Exceptions\DebugException;
use App\Library\Collections\ParamsEmpresa;
use App\Library\Tcpdf\KumbiaPDF;
use App\Models\Mercurio16;
use App\Services\Formularios\FactoryDocuments;
use App\Services\PreparaFormularios\CifrarDocumento;
use App\Services\Api\ApiSubsidio;
use App\Services\Formularios\Generation\DocumentGenerationManager;

class DatosEmpresaService
{
    private $empresa;

    private $solicitud;

    private $lfirma;

    private $filename;

    private $outPdf;

    private $fhash;

    private $claveCertificado;

    private $user;

    private const DOCUMENTOS = [
        [
            'method' => 'formulario',
            'coddoc' => 27,
        ]
    ];

    public function __construct($argv)
    {
        $this->user = session('user') ?? null;
        $this->empresa = $argv[0];
        $this->solicitud = $argv[1];
        $this->initialize();
    }

    private function initialize()
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

    public function formulario()
    {
        if (! $this->lfirma) {
            throw new DebugException('Error no hay firma digital', 501);
        }
        $nit = $this->empresa->nit;
        $this->filename = 'formulario-datos-empresa-' . strtotime('now') . "_{$nit}.pdf";
        $manager = new DocumentGenerationManager();
        $manager->generate('api', 'actualizadatos', [
            'categoria' => 'formulario',
            'output' => $this->filename,
            'template' => 'actualiza-empresa.html',
            'empresa' => $this->empresa,
            'solicitud' => $this->solicitud,
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
