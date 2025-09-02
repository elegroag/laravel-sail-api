<?php

namespace App\Services\FormulariosAdjuntos;
use App\Library\Collections\ParamsEmpresa;
use App\Library\Tcpdf\KumbiaPDF;
use App\Models\Mercurio16;
use App\Services\Formularios\FactoryDocuments;
use App\Services\PreparaFormularios\CifrarDocumento;
use App\Services\Utils\Comman;

class DatosEmpresaService
{
    /**
     * request variable
     *
     * @var array
     */
    private $request;
    private $lfirma;

    public function __construct($request)
    {
        $this->request = $request;
        $this->initialize();
    }

    private function initialize()
    {
        $this->lfirma = (new Mercurio16)->findFirst("documento='{$this->request['documento']}' AND  coddoc='{$this->request['coddoc']}'");
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

    public function formulario()
    {
        $file = strtotime('now') . "_{$this->request['nit']}.pdf";
        KumbiaPDF::setBackgroundImage(public_path('docs/form/empresa/form-empresa.jpg'));

        $fabrica = new FactoryDocuments();
        $documento = $fabrica->crearFormulario('actualizadatos');
        $documento->setParamsInit(
            array(
                'empresa' => $this->request['empresa'],
                'campos' => $this->request['campos'],
                'filename' => $file
            )
        );
        $documento->main();
        $documento->outPut();

        $cifrarDocumento = new CifrarDocumento();
        $outPdf = $cifrarDocumento->cifrar(
            storage_path('temp/' . $file),
            $this->lfirma->getKeyprivate()
        );

        return array(
            "name" => $file,
            "file" => basename($outPdf),
            'out' => $outPdf
        );
    }
}
