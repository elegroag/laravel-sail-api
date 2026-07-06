<?php

namespace App\Services\FormulariosAdjuntos;

use App\Exceptions\DebugException;
use App\Library\Collections\ParamsPensionado;
use App\Models\Mercurio07;
use App\Models\Mercurio16;
use App\Models\Mercurio38;
use App\Services\Api\ApiSubsidio;
use App\Services\Formularios\Generation\DocumentGenerationManager;
use App\Services\PreparaFormularios\CifrarDocumento;

class PensionadoAdjuntoService
{
    private ?object $request;

    private ?Mercurio16 $lfirma;

    private ?string $filename;

    private ?string $outPdf;

    private ?string $fhash;

    private ?array $user;

    private ?string $claveCertificado;

    public function __construct(object $request)
    {
        $this->user = session('user') ?? null;
        $this->request = $request;
        $this->initialize();
    }

    private function initialize(): void
    {
        $this->lfirma = Mercurio16::where([
            'documento' => $this->user['documento'],
            'coddoc' => $this->user['coddoc'],
        ])->first();

        $procesadorComando = new ApiSubsidio;
        $procesadorComando->send(
            [
                'servicio' => 'ComfacaAfilia',
                'metodo' => 'parametros_pensionado',
            ]
        );

        $datos_captura = $procesadorComando->toArray();
        $paramsEmpresa = new ParamsPensionado;
        $paramsEmpresa->setDatosCaptura($datos_captura);
    }

    public function getSolicitante()
    {
        $solicitante = Mercurio07::where('documento', $this->request->documento)
            ->where('coddoc', $this->request->coddoc)
            ->where('tipo', $this->request->tipo)
            ->first();

        return $solicitante;
    }

    public function formulario()
    {
        if (! $this->lfirma) {
            throw new DebugException('Error no hay firma digital', 501);
        }

        $this->filename = 'formulario-trabajador-'.strtotime('now')."_{$this->request->cedtra}.pdf";
        $manager = new DocumentGenerationManager;
        $manager->generate(
            'api',
            'pensionado',
            [
                'categoria' => 'formulario',
                'output' => $this->filename,
                'templates' => [
                    'trabajador.html',
                    'oficio-empresa.html',
                    'politica-trabajador.html',
                ],
                'pensionado' => $this->request,
                'solicitante' => $this->getSolicitante(),
            ]
        );

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

    public function setClaveCertificado(string $clave): void
    {
        if ($this->lfirma->password !== $clave) {
            throw new DebugException('Error la clave no coincide con la de la firma digital', 501);
        }
        $this->claveCertificado = $clave;
    }

    public static function generarAdjuntos(object $request, string $tipopc, ?string $claveCertificado = null): void
    {
        $adjuntoService = new self($request);
        $adjuntoService->setClaveCertificado($claveCertificado);
        AdjuntosGenerator::generar($adjuntoService, $tipopc, $request, [
            [
                'method' => 'formulario',
                'coddoc' => 1,
            ],
        ]);
    }

    public static function generarComprobanteRadicacion(Mercurio38 $mercurio38): string
    {
        $procesadorComando = new ApiSubsidio;
        $procesadorComando->send([
            'servicio' => 'ComfacaAfilia',
            'metodo' => 'parametros_pensionado',
        ]);
        $paramsPensionado = new ParamsPensionado;
        $paramsPensionado->setDatosCaptura($procesadorComando->toArray());

        $filename = 'comprobante-pensionado-'.$mercurio38->ruuid.'.pdf';
        $manager = new DocumentGenerationManager;
        $documento = $manager->generate('local', 'comprobante', [
            'categoria' => 'formulario',
            'filename' => $filename,
            'tipo' => 'pensionado',
            'solicitud' => $mercurio38,
        ]);
        $filePath = $documento->outPut();

        Mercurio38::where('id', $mercurio38->id)->update([
            'comprobante_path' => basename($filePath),
        ]);

        return basename($filePath);
    }
}
