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

    private $filename;

    private $outPdf;

    private $fhash;

    private $claveCertificado;

    public function __construct($request)
    {
        $this->request = $request;
        $this->initialize();
    }

    private function initialize()
    {
        $this->lfirma = Mercurio16::where('documento', $this->request['documento'])
            ->where('coddoc', $this->request['coddoc'])
            ->first();

        $procesadorComando = Comman::Api();
        $procesadorComando->runCli(
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
        $this->filename = strtotime('now')."_{$this->request['nit']}.pdf";
        KumbiaPDF::setBackgroundImage(public_path('docs/form/empresa/form-empresa.jpg'));

        $fabrica = new FactoryDocuments;
        $documento = $fabrica->crearFormulario('actualizadatos');
        $documento->setParamsInit(
            [
                'empresa' => $this->request['empresa'],
                'campos' => $this->request['campos'],
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
}
