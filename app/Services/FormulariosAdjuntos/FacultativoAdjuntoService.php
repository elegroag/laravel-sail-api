<?php

namespace App\Services\FormulariosAdjuntos;

use App\Library\Collections\ParamsFacultativo;
use App\Library\Tcpdf\KumbiaPDF;
use App\Models\Mercurio16;
use App\Models\Mercurio32;
use App\Services\Formularios\FactoryDocuments;
use App\Services\PreparaFormularios\CifrarDocumento;
use App\Services\Api\ApiSubsidio;

class FacultativoAdjuntoService
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
            'coddoc' => 24
        ]
    ];

    public function __construct($request)
    {
        $this->user = session('user') ?? null;
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
                'metodo' => 'parametros_empresa',
            ]
        );

        $datos_captura = $procesadorComando->toArray();
        $paramsEmpresa = new ParamsFacultativo;
        $paramsEmpresa->setDatosCaptura($datos_captura);
    }

    public function tratamientoDatos()
    {
        $this->filename = "tratamiento_datos_facultativo_{$this->request->getCedtra()}.pdf";
        KumbiaPDF::setFooterImage(false);
        KumbiaPDF::setBackgroundImage(false);

        $fabrica = new FactoryDocuments;
        $documento = $fabrica->crearPolitica('facultativo');
        $documento->setParamsInit(
            [
                'facultativo' => $this->request,
                'firma' => $this->lfirma,
                'filename' => $this->filename,
                'background' => false,
                'rfirma' => false,
            ]
        );
        $documento->main();
        $documento->outPut();

        $this->cifrarDocumento();

        return $this;
    }

    public function cartaSolicitud()
    {
        $procesadorComando = new ApiSubsidio();
        $procesadorComando->send(
            [
                'servicio' => 'ComfacaEmpresas',
                'metodo' => 'informacion_trabajador',
                'params' => ['cedtra' => $this->request->getCedtra()],
            ]
        );

        if ($procesadorComando->isJson() == false) {
            d('Se genero un error al buscar al trabajador usando el servicio CLI-Comando. ');
        }

        $out = $procesadorComando->toArray();
        $this->filename = "carta_solicitud_facultativo_{$this->request->getCedtra()}.pdf";
        $fabrica = new FactoryDocuments;

        $documento = $fabrica->crearOficio('facultativo');
        $documento->setParamsInit(
            [
                'background' => 'img/form/oficios/oficio_solicitud_afiliacion.jpg',
                'facultativo' => $this->request,
                'firma' => $this->lfirma,
                'filename' => $this->filename,
                'previus' => $out['success'] ? $out['data'] : null,
            ]
        );

        $documento->main();
        $documento->outPut();

        $this->cifrarDocumento();

        return $this;
    }

    public function formulario()
    {
        $conyuge = Mercurio32::where([
            'documento' => $this->request->getDocumento(),
            'coddoc' => $this->request->getCoddoc(),
            'cedtra' => $this->request->getCedtra(),
            'comper' => 'S',
        ])->first();

        $this->filename = "formulario_facultativo_{$this->request->getCedtra()}.pdf";

        $fabrica = new FactoryDocuments;
        $documento = $fabrica->crearFormulario('facultativo');
        $documento->setParamsInit(
            [
                'background' => 'img/form/trabajador/form-001-tra-p01.png',
                'facultativo' => $this->request,
                'conyuge' => $conyuge,
                'firma' => $this->lfirma,
                'filename' => $this->filename,
            ]
        );

        $documento->main();
        $documento->outPut();

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

    public static function generarAdjuntos($request, string $tipopc, ?string $claveCertificado = null): void
    {
        $adjuntoService = new self($request);
        $adjuntoService->setClaveCertificado($claveCertificado);
        AdjuntosGenerator::generar($adjuntoService, $tipopc, $request, self::DOCUMENTOS);
    }
}
