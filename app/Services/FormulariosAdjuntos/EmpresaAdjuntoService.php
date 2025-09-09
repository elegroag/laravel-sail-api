<?php

namespace App\Services\FormulariosAdjuntos;

use App\Models\Mercurio16;
use App\Services\Utils\Comman;

use App\Library\Collections\ParamsEmpresa;
use App\Library\Tcpdf\KumbiaPDF;
use App\Models\Tranoms;
use App\Services\Formularios\FactoryDocuments;
use App\Services\PreparaFormularios\CifrarDocumento;


class EmpresaAdjuntoService
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
        $this->lfirma = Mercurio16::where('documento', $this->request->getDocumento())
            ->where('coddoc', $this->request->getCoddoc())
            ->first();

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
        $this->filename = "tratamiento_datos_empresa_{$this->request->getNit()}.pdf";
        KumbiaPDF::setFooterImage(false);

        $fabrica = new FactoryDocuments();
        $documento = $fabrica->crearPolitica('empresa');

        $documento->setParamsInit(
            array(
                'empresa' => $this->request,
                'firma' => $this->lfirma,
                'filename' => $this->filename,
                'background' => false,
                'rfirma' => false
            )
        );
        $documento->main();
        $documento->outPut();
        $this->cifrarDocumento();
        return $this;
    }

    public function cartaSolicitud()
    {
        $this->filename = "carta_solicitud_empresa_{$this->request->getNit()}.pdf";
        $background = 'img/form/oficios/oficio_solicitud_empresa.jpg';
        KumbiaPDF::setFooterImage(false);

        $fabrica = new FactoryDocuments();
        $documento = $fabrica->crearOficio('empresa');
        $documento->setParamsInit(
            array(
                'background' => $background,
                'empresa' => $this->request,
                'firma' => $this->lfirma,
                'filename' => $this->filename
            )
        );
        $documento->main();
        $documento->outPut();
        $this->cifrarDocumento();
        return $this;
    }

    public function formulario()
    {
        $this->filename = "formulario_empresa_{$this->request->getNit()}.pdf";
        $background = 'img/form/empresa/form-empresa.jpg';
        $fabrica = new FactoryDocuments();
        $documento = $fabrica->crearFormulario('empresa');
        $documento->setParamsInit([
            'background' => $background,
            'empresa' => $this->request,
            'firma' => $this->lfirma,
            'filename' => $this->filename
        ]);
        $documento->main();
        $documento->outPut();
        $this->cifrarDocumento();
        return $this;
    }

    public function trabajadoresNomina()
    {
        KumbiaPDF::setBackgroundImage(false);
        KumbiaPDF::setFooterImage(public_path('img/firmas/sello-firma.png'));

        $tranoms = Tranoms::where('request', $this->request->getId())->get();

        $this->filename = "tranom_empresa_{$this->request->getNit()}.pdf";

        $fabrica = new FactoryDocuments();
        $documento = $fabrica->crearOficio('trabajador_nomina');

        $documento->setParamsInit([
            'empresa' => $this->request,
            'firma' => $this->lfirma,
            'filename' => $this->filename,
            'tranoms' => $tranoms
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
