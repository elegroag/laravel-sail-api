<?php
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
        parent::__construct();
        $this->request = $request;
        Core::importLibrary("ParamsEmpresa", "Collections");
        Core::importLibrary("FactoryDocuments", "Formularios");
        $this->initialize();
    }

    private function initialize()
    {
        $this->lfirma = $this->Mercurio16->findFirst("documento='{$this->request['documento']}' AND  coddoc='{$this->request['coddoc']}'");
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
        KumbiaPDF::setBackgroundImage(Core::getInitialPath() . 'public/docs/form/empresa/form-empresa.jpg');

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
            Core::getInitialPath() . 'public/temp/' . $file,
            $this->lfirma->getKeyprivate()
        );

        return array(
            "name" => $file,
            "file" => basename($outPdf),
            'out' => $outPdf
        );
    }
}
