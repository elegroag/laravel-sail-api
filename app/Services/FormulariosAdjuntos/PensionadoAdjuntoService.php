<?php

namespace App\Services\FormulariosAdjuntos;

use App\Exceptions\DebugException;
use App\Library\Collections\ParamsPensionado;
use App\Library\Tcpdf\KumbiaPDF;
use App\Models\Mercurio07;
use App\Models\Mercurio16;
use App\Models\Mercurio32;
use App\Services\Formularios\FactoryDocuments;
use App\Services\PreparaFormularios\CifrarDocumento;
use App\Services\Api\ApiSubsidio;
use App\Services\Formularios\Generation\DocumentGenerationManager;

class PensionadoAdjuntoService
{
    private $request;

    private $lfirma;

    private $filename;

    private $outPdf;

    private $fhash;

    private $user;

    private $claveCertificado;

    public function __construct($request)
    {
        $this->user = session()->has('user') ? session('user') : null;
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
        $paramsEmpresa = new ParamsPensionado;
        $paramsEmpresa->setDatosCaptura($datos_captura);
    }

    public function tratamientoDatos()
    {
        $this->filename = 'tratamiento_datos_pensionado_' . strtotime('now') . "_{$this->request->cedtra}.pdf";
        $manager = new DocumentGenerationManager();
        $manager->generate('api', 'pensionado', [
            'categoria' => 'politica',
            'output' => $this->filename,
            'template' => 'politica-trabajador.html',
            'pensionado' => $this->request,
            'solicitante' => $this->getSolicitante()
        ]);
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
        $this->filename = "carta_solicitud_pensionado_{$this->request->getCedtra()}.pdf";
        $fabrica = new FactoryDocuments;
        $documento = $fabrica->crearOficio('pensionado');
        $documento->setParamsInit([
            'background' => 'img/form/oficios/oficio_solicitud_afiliacion.jpg',
            'pensionado' => $this->request,
            'firma' => $this->lfirma,
            'filename' => $this->filename,
            'previus' => $out['success'] ? $out['data'] : null,
        ]);

        $documento->main();
        $documento->outPut();
        $this->cifrarDocumento();

        return $this;
    }

    public function formulario()
    {
        if (! $this->lfirma) {
            throw new DebugException('Error no hay firma digital', 501);
        }

        $this->filename = 'formulario-trabajador-' . strtotime('now') . "_{$this->request->cedtra}.pdf";
        $manager = new DocumentGenerationManager();
        $manager->generate('api', 'pensionado', [
            'categoria' => 'formulario',
            'output' => $this->filename,
            'template' => 'trabajador.html',
            'pensionado' => $this->request,
            'solicitante' => $this->getSolicitante()
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
