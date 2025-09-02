<?php
class ConyugeAdjuntoService 
{
    /**
     * request variable
     * @var Mercurio32
     */
    private $request;
    private $filename;
    private $outPdf;
    private $fhash;

    /**
     * lfirma variable
     * @var Mercurio16
     */
    private $lfirma;

    public function __construct($request)
    {
        parent::__construct();
        $this->request = $request;
        Core::importLibrary("ParamsConyuge", "Collections");
        Core::importLibrary("FactoryDocuments", "Formularios");
        $this->initialize();
    }

    private function initialize()
    {
        $this->lfirma = (new Mercurio16)->findFirst("documento='{$this->request->getDocumento()}' AND coddoc='{$this->request->getCoddoc()}'");

        $procesadorComando = Comman::Api();
        $procesadorComando->runCli(
            array(
                "servicio" => "ComfacaAfilia",
                "metodo" => "parametros_conyuges"
            )
        );

        $datos_captura =  $procesadorComando->toArray();
        $paramsConyuge = new ParamsConyuge();
        $paramsConyuge->setDatosCaptura($datos_captura);
    }

    public function formulario()
    {
        $solicitante = $this->Mercurio07->findFirst(
            "documento='{$this->request->getDocumento()}' and " .
                "coddoc='{$this->request->getCoddoc()}' and " .
                "tipo='{$this->request->getTipo()}'"
        );

        $this->filename = strtotime('now') . "_{$this->request->getCedcon()}.pdf";
        $fabrica = new FactoryDocuments();
        $documento = $fabrica->crearFormulario('conyuge');
        $documento->setParamsInit(
            array(
                'conyuge' => $this->request,
                'trabajador' => $this->getTrabajador(),
                'solicitante' => $solicitante,
                'firma' => $this->lfirma,
                'filename' => $this->filename
            )
        );

        $documento->main();
        $documento->outPut();
        $this->cifrarDocumento();
        return $this;
    }

    public function getTrabajador()
    {
        $trabajador = false;
        $mtrabajador = false;
        switch (trim($this->request->getTipo())) {
            case 'I':
                $mtrabajador = new Mercurio41();
                break;
            case 'O':
                $mtrabajador = new Mercurio38();
                break;
            case 'F':
                $mtrabajador = new Mercurio36();
                break;
            case 'T':
            case 'E':
                $mtrabajador = new Mercurio31();
                break;
            default:
                $trabajador = $this->Mercurio31->findFirst(
                    " documento='{$this->request->getDocumento()}' and " .
                        " coddoc='{$this->request->getCoddoc()}' and " .
                        " cedtra='{$this->request->getCedtra()}' and " .
                        " estado NOT IN('X','I')"
                );
                break;
        }

        if (!$trabajador && $mtrabajador) {
            $trabajador = $mtrabajador->findFirst(
                " cedtra='{$this->request->getCedtra()}' AND  documento='{$this->request->getDocumento()}' AND coddoc='{$this->request->getCoddoc()}'"
            );
        }

        if (!$trabajador && $mtrabajador) {
            $trabajadorService = new TrabajadorService();
            $out = $trabajadorService->buscarTrabajadorSubsidio($this->request->getCedtra());
            if ($out) {
                $trabajador = clone $mtrabajador;
                $trabajador->createAttributes($out);
            }
        }

        if (!$trabajador) {
            throw new DebugException("Error el trabajador no está registrado previamente", 501);
        }

        return $trabajador;
    }

    public function declaraJurament()
    {
        $this->filename = strtotime('now') . "_{$this->request->getCedcon()}.pdf";
        $fabrica = new FactoryDocuments();
        $documento = $fabrica->crearDeclaracion('conyuge');

        $documento->setParamsInit(
            array(
                'conyuge' => $this->request,
                'trabajador' => $this->getTrabajador(),
                'firma' => $this->lfirma,
                'filename' => $this->filename
            )
        );

        $documento->main();
        $documento->outPut();
        $this->cifrarDocumento();
        return $this;
    }

    function cifrarDocumento()
    {
        $cifrarDocumento = new CifrarDocumento();
        $this->outPdf = $cifrarDocumento->cifrar(Core::getInitialPath() . 'public/temp/' . $this->filename, $this->lfirma->getKeyprivate());
        $this->fhash = $cifrarDocumento->getFhash();
    }

    public function getResult()
    {
        return array(
            "name" => $this->filename,
            "file" => basename($this->outPdf),
            'out' => $this->outPdf,
            'fhash' => $this->fhash
        );
    }
}
