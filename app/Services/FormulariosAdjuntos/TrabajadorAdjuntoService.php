<?php

namespace App\Services\FormulariosAdjuntos;

use App\Exceptions\DebugException;
use App\Library\Collections\ParamsEmpresa;
use App\Library\Tcpdf\KumbiaPDF;
use App\Models\Mercurio16;
use App\Models\Mercurio30;
use App\Models\Mercurio32;
use App\Services\Formularios\FactoryDocuments;
use App\Services\PreparaFormularios\CifrarDocumento;
use App\Services\Utils\Comman;

class TrabajadorAdjuntoService
{
    private $filename;

    private $outPdf;

    private $fhash;

    private $claveCertificado;

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

        $this->request = $request;
        $this->initialize();
    }

    public function initialize()
    {
        $this->lfirma = Mercurio16::where([
            'documento' => $this->request->getDocumento(),
            'coddoc' => $this->request->getCoddoc(),
        ])->first();

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

    public function tratamientoDatos()
    {
        $this->filename = "tratamiento_datos_trabajador_{$this->request->getCedtra()}.pdf";
        KumbiaPDF::setBackgroundImage(false);
        $fabrica = new FactoryDocuments;
        $documento = $fabrica->crearPolitica('trabajador');
        $documento->setParamsInit(
            [
                'trabajador' => $this->request,
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
        $procesadorComando = Comman::Api();
        $procesadorComando->runCli(
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
        $this->filename = "carta_solicitud_independiente_{$this->request->getCedtra()}.pdf";
        KumbiaPDF::setBackgroundImage(false);

        $fabrica = new FactoryDocuments;
        $documento = $fabrica->crearOficio('trabajador');
        $documento->setParamsInit([
            'trabajador' => $this->request,
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

        $conyuge = Mercurio32::where([
            'documento' => $this->request->getDocumento(),
            'coddoc' => $this->request->getCoddoc(),
            'cedtra' => $this->request->getCedtra(),
            'comper' => 'S',
        ])->first();

        $procesadorComando = Comman::Api();
        $procesadorComando->runCli(
            [
                'servicio' => 'ComfacaEmpresas',
                'metodo' => 'informacion_empresa',
                'params' => [
                    'nit' => $this->request->getNit(),
                ],
            ]
        );

        if ($procesadorComando->isJson() == false) {
            throw new DebugException('Error al consultar la empresa', 501);
        }
        $out = $procesadorComando->toArray();
        $empresa = new Mercurio30($out['data']);

        $this->filename = strtotime('now')."_{$this->request->getCedtra()}.pdf";
        $fabrica = new FactoryDocuments;
        $documento = $fabrica->crearFormulario('trabajador');
        $documento->setParamsInit([
            'background' => 'img/form/trabajador/form-001-tra-p01.png',
            'trabajador' => $this->request,
            'empresa' => $empresa,
            'conyuge' => $conyuge,
            'firma' => $this->lfirma,
            'filename' => $this->filename,
        ]);

        $documento->main();
        $documento->outPut();
        $this->cifrarDocumento();

        return $this;
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
        $this->claveCertificado = $clave;
    }
}
