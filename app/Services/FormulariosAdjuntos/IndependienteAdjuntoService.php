<?php

namespace App\Services\FormulariosAdjuntos;

use App\Library\Collections\ParamsEmpresa;
use App\Library\Tcpdf\KumbiaPDF;
use App\Models\Mercurio16;
use App\Models\Mercurio32;
use App\Services\Formularios\FactoryDocuments;
use App\Services\PreparaFormularios\CifrarDocumento;
use App\Services\Utils\Comman;

class IndependienteAdjuntoService
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
            array(
                "servicio" => "ComfacaAfilia",
                "metodo" => "parametros_empresa"
            )
        );

        $datos_captura =  $procesadorComando->toArray();
        $paramsEmpresa = new ParamsEmpresa();
        $paramsEmpresa->setDatosCaptura($datos_captura);
    }

    public function tratamientoDatos()
    {
        $this->filename = "tratamiento_datos_independiente_{$this->request->getCedtra()}.pdf";
        KumbiaPDF::setFooterImage(false);

        $fabrica = new FactoryDocuments();
        $documento = $fabrica->crearPolitica('independiente');

        $documento->setParamsInit([
            'independiente' => $this->request,
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
            array(
                "servicio" => "ComfacaEmpresas",
                "metodo" => "informacion_trabajador",
                "params" => array('cedtra' => $this->request->getCedtra())
            )
        );

        if ($procesadorComando->isJson() == False) {
            d("Se genero un error al buscar al trabajador usando el servicio CLI-Comando. ");
        }

        $out = $procesadorComando->toArray();
        $this->filename = "carta_solicitud_independiente_{$this->request->getCedtra()}.pdf";

        KumbiaPDF::setBackgroundImage(public_path('img/form/oficios/oficio_solicitud_afiliacion.jpg'));
        KumbiaPDF::setFooterImage(false);

        $fabrica = new FactoryDocuments();
        $documento = $fabrica->crearOficio('independiente');
        $documento->setParamsInit([
            'independiente' => $this->request,
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

        $this->filename = "formulario_independiente_{$this->request->getCedtra()}.pdf";
        KumbiaPDF::setBackgroundImage(public_path('img/form/trabajador/form-001-tra-p01.png'));

        $fabrica = new FactoryDocuments();
        $documento = $fabrica->crearFormulario('independiente');
        $documento->setParamsInit([
            'independiente' => $this->request,
            'conyuge' => $conyuge,
            'firma' => $this->lfirma,
            'filename' => $this->filename
        ]);

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
