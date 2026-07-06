<?php

namespace App\Services\FormulariosAdjuntos;

use App\Exceptions\DebugException;
use App\Library\Collections\ParamsEmpresa;
use App\Models\Mercurio16;
use App\Models\Mercurio30;
use App\Models\Tranoms;
use App\Services\Api\ApiSubsidio;
use App\Services\Formularios\Api\EmpresasDocuments;
use App\Services\Formularios\Generation\DocumentGenerationManager;
use App\Services\PreparaFormularios\CifrarDocumento;
use App\Services\Utils\GuardarArchivoService;

class EmpresaAdjuntoService
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
        $this->lfirma = Mercurio16::where('documento', $this->user['documento'])
            ->where('coddoc', $this->user['coddoc'])
            ->first();

        $procesadorComando = new ApiSubsidio;
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

    public function formulario(): self
    {
        if (! $this->lfirma) {
            throw new DebugException('Error no hay firma digital', 501);
        }

        $this->filename = 'formulario-empresa-'.strtotime('now')."_{$this->request->nit}.pdf";
        $generator = new EmpresasDocuments;
        $generator->setParamsInit(
            [
                'categoria' => 'formulario',
                'output' => $this->filename,
                'templates' => [
                    'empresa.html',
                    'oficio-empresa.html',
                    'politica-empresa.html',
                    'relacion-nomina.html',
                ],
                'empresa' => $this->request,
                'tranoms' => Tranoms::where('request', $this->request->id)->get(),
            ]
        );
        $generator->main();
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
        $adjuntoService->formulario();
        (new GuardarArchivoService(
            [
                'tipopc' => $tipopc,
                'coddoc' => 1,
                'id' => $request->id,
            ]
        ))->salvarDatos($adjuntoService->getResult());
    }

    public static function generarComprobanteRadicacion(Mercurio30 $mercurio30): string
    {
        $filename = 'comprobante-empresa-'.$mercurio30->ruuid.'.pdf';
        $manager = new DocumentGenerationManager;
        $documento = $manager->generate('local', 'comprobante', [
            'categoria' => 'formulario',
            'filename' => $filename,
            'tipo' => 'empresa',
            'solicitud' => $mercurio30,
            'empresa' => $mercurio30,
        ]);
        $filePath = $documento->outPut();

        Mercurio30::where('id', $mercurio30->id)->update([
            'comprobante_path' => basename($filePath),
        ]);

        return basename($filePath);
    }
}
