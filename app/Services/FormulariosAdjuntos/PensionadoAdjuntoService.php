<?php

namespace App\Services\FormulariosAdjuntos;

use App\Library\Collections\ParamsPensionado;
use App\Library\Tcpdf\KumbiaPDF;
use App\Models\Mercurio16;
use App\Models\Mercurio32;
use App\Services\Formularios\FactoryDocuments;
use App\Services\PreparaFormularios\CifrarDocumento;
use App\Services\Utils\Comman;

class PensionadoAdjuntoService
{
    private $request;
    private $lfirma;
    private $filename;
    private $outPdf;
    private $fhash;


    public function __construct($request)
    {
        $this->request = $request;

        $this->initialize();
    }

    private function initialize()
    {
        $this->lfirma = Mercurio16::where([
            'documento' => $this->request->getDocumento(),
            'coddoc' => $this->request->getCoddoc()
        ])->first();

        $procesadorComando = Comman::Api();
        $procesadorComando->runCli(
            [
                "servicio" => "ComfacaAfilia",
                "metodo" => "parametros_empresa"
            ]
        );

        $datos_captura =  $procesadorComando->toArray();
        $paramsEmpresa = new ParamsPensionado();
        $paramsEmpresa->setDatosCaptura($datos_captura);
    }

    public function tratamientoDatos()
    {
        $this->filename = "tratamiento_datos_pensionado_{$this->request->getCedtra()}.pdf";
        KumbiaPDF::setFooterImage(false);
        KumbiaPDF::setBackgroundImage(false);

        $fabrica = new FactoryDocuments();
        $documento = $fabrica->crearPolitica('pensionado');
        $documento->setParamsInit([
            'pensionado' => $this->request,
            'firma' => $this->lfirma,
            'filename' => $this->filename,
            'background' => false,
            'rfirma' => false
        ]);
        $documento->main();
        $documento->outPut();
        $this->cifrarDocumento();
        return $this;
    }

    public function cartaSolicitud()
    {
        $procesadorComando = Comman::Api();
        $procesadorComando->runCli(
            [
                "servicio" => "ComfacaEmpresas",
                "metodo" => "informacion_trabajador",
                "params" => ['cedtra' => $this->request->getCedtra()]
            ]
        );

        if ($procesadorComando->isJson() == False) {
            d("Se genero un error al buscar al trabajador usando el servicio CLI-Comando. ");
        }

        $out = $procesadorComando->toArray();
        $this->filename = "carta_solicitud_pensionado_{$this->request->getCedtra()}.pdf";
        $fabrica = new FactoryDocuments();
        $documento = $fabrica->crearOficio('pensionado');
        $documento->setParamsInit([
            'background' => 'img/form/oficios/oficio_solicitud_afiliacion.jpg',
            'pensionado' => $this->request,
            'firma' => $this->lfirma,
            'filename' => $this->filename,
            'previus' => $out['success'] ? $out['data'] : null
        ]);

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
            'comper' => 'S'
        ])->first();

        $this->filename = "formulario_pensionado_{$this->request->getCedtra()}.pdf";
        $fabrica = new FactoryDocuments();
        $documento = $fabrica->crearFormulario('pensionado');
        $documento->setParamsInit(
            [
                'background' => 'img/form/trabajador/form-001-tra-p01.png',
                'pensionado' => $this->request,
                'conyuge' => $conyuge,
                'firma' => $this->lfirma,
                'filename' => $this->filename
            ]
        );

        $documento->main();
        $documento->outPut();
        $this->cifrarDocumento();
        return $this;
    }

    function cifrarDocumento()
    {
        $cifrarDocumento = new CifrarDocumento();
        $this->outPdf = $cifrarDocumento->cifrar($this->filename, $this->lfirma->getKeyprivate());
        $this->fhash = $cifrarDocumento->getFhash();
    }

    public function getResult()
    {
        return [
            "name" => $this->filename,
            "file" => basename($this->outPdf),
            'out' => $this->outPdf,
            'fhash' => $this->fhash
        ];
    }
}
