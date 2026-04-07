<?php

namespace App\Services\FormulariosAdjuntos;

use App\Exceptions\DebugException;
use App\Library\Collections\ParamsEmpresa;
use App\Models\Mercurio07;
use App\Models\Mercurio16;
use App\Models\Mercurio30;
use App\Models\Mercurio32;
use App\Services\PreparaFormularios\CifrarDocumento;
use App\Services\Api\ApiSubsidio;
use App\Services\Formularios\Api\TrabajadoresDocuments;
use App\Services\Utils\GuardarArchivoService;

class TrabajadorAdjuntoService
{
    private $filename;

    private $outPdf;

    private $fhash;

    private $claveCertificado;
    private $user;

    /**
     * request variable
     *
     * @var Mercurio31
     */
    protected $request;

    /**
     * lfirma variable
     *
     * @var Mercurio16
     */
    protected $lfirma;

    public function __construct($request)
    {
        $this->user = session('user') ?? null;
        $this->request = $request;
        $this->initialize();
    }

    public function initialize()
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

        $this->filename = 'formulario-trabajador-' . strtotime('now') . "_{$this->request->cedtra}.pdf";
        $generator = new TrabajadoresDocuments();
        $generator->setParamsInit(
            [
                'categoria' => 'formulario',
                'output' => $this->filename,
                'templates' => [
                    'trabajador.html',
                    'politica-trabajador.html'
                ],
                'trabajador' => $this->request,
                'empresa' => $this->getBuscarEmpresa(),
                'conyuge' => $this->getBuscarConyuge(),
                'solicitante' => $this->getSolicitante()
            ]
        );
        $generator->main();
        $this->cifrarDocumento();
        return $this;
    }

    public function getSolicitante()
    {
        $solicitante = Mercurio07::where("documento", $this->request->documento)
            ->where("coddoc", $this->request->coddoc)
            ->where("tipo", $this->request->tipo)
            ->first();
        return $solicitante;
    }

    public function getBuscarEmpresa()
    {
        $procesadorComando = new ApiSubsidio();
        $procesadorComando->send(
            [
                'servicio' => 'ComfacaEmpresas',
                'metodo' => 'informacion_empresa',
                'params' => [
                    'nit' => $this->request->nit,
                ],
            ]
        );
        if ($procesadorComando->isJson() == false) {
            throw new DebugException('Error al consultar la empresa', 501);
        }
        $out = $procesadorComando->toArray();
        $model = new Mercurio30();
        return $model->fill($out['data']);
    }

    public function getBuscarConyuge()
    {
        $conyuge = Mercurio32::where([
            'documento' => $this->request->documento,
            'coddoc' => $this->request->coddoc,
            'cedtra' => $this->request->cedtra,
            'comper' => 'S',
        ])->first();
        return $conyuge;
    }

    public function cifrarDocumento()
    {
        $cifrarDocumento = new CifrarDocumento;
        $this->outPdf = $cifrarDocumento->cifrar(
            $this->filename,
            $this->lfirma->getKeyprivate(),
            $this->claveCertificado
        );
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
        $adjuntoService->formulario();
        (new GuardarArchivoService(
            [
                'tipopc' => $tipopc,
                'coddoc' => 1,
                'id' => $request->id,
            ]
        ))->salvarDatos($adjuntoService->getResult());
    }
}
