<?php

namespace App\Services\FormulariosAdjuntos;

use App\Models\Mercurio16;
use App\Library\Collections\ParamsTrabajador;
use App\Library\Collections\ParamsBeneficiario;
use App\Models\Mercurio31;
use App\Models\Mercurio32;
use App\Models\Mercurio36;
use App\Models\Mercurio38;
use App\Models\Mercurio41;
use App\Services\Entidades\TrabajadorService;
use App\Services\Formularios\FactoryDocuments;
use App\Services\PreparaFormularios\CifrarDocumento;
use App\Services\Utils\Comman;

class BeneficiarioAdjuntoService
{
    /**
     * request variable
     * @var Mercurio34
     */
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
                "metodo" => "parametros_trabajadores"
            )
        );

        $datos_captura =  $procesadorComando->toArray();
        $paramsConyuge = new ParamsTrabajador();
        $paramsConyuge->setDatosCaptura($datos_captura);

        $procesadorComando = Comman::Api();
        $procesadorComando->runCli(
            array(
                "servicio" => "ComfacaAfilia",
                "metodo" => "parametros_beneficiarios"
            )
        );
        $datos_captura =  $procesadorComando->toArray();
        $paramsBeneficiario = new ParamsBeneficiario();
        $paramsBeneficiario->setDatosCaptura($datos_captura);
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
                $trabajador = (new Mercurio31())->findFirst(
                    " documento='{$this->request->getDocumento()}' and " .
                        " coddoc='{$this->request->getCoddoc()}' and " .
                        " cedtra='{$this->request->getCedtra()}' and " .
                        " estado IN('A','P','T')"
                );
                break;
        }

        if (!$trabajador && $mtrabajador) {
            $trabajador = $mtrabajador->findFirst(
                " cedtra='{$this->request->getCedtra()}' AND documento='{$this->request->getDocumento()}' AND coddoc='{$this->request->getCoddoc()}'"
            );
        }

        if (!$trabajador && $mtrabajador) {
            $trabajadorService = new TrabajadorService();
            $data = $trabajadorService->buscarTrabajadorSubsidio($this->request->getCedtra());
            if ($data) {
                $trabajador = clone $mtrabajador;
                $trabajador->fill($data);
            }
        }
        return $trabajador;
    }


    public function formulario()
    {
        $this->filename = "formulario_beneficiario_{$this->request->getNumdoc()}.pdf";
        $fabrica = new FactoryDocuments();
        $documento = $fabrica->crearFormulario('beneficiario');
        $documento->setParamsInit(
            array(
                'beneficiario' => $this->request,
                'trabajador' => $this->getTrabajador(),
                'bioconyu' => $this->getBiologioConyuge(),
                'filename' => $this->filename
            )
        );

        $documento->main();
        $documento->outPut();
        $this->cifrarDocumento();
        return $this;
    }

    public function declaraJurament()
    {
        $this->filename = "declaracion_hijo_{$this->request->getNumdoc()}.pdf";
        $fabrica = new FactoryDocuments();
        $documento = $fabrica->crearDeclaracion('beneficiario');
        $documento->setParamsInit(
            array(
                'beneficiario' => $this->request,
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

    public function getBiologioConyuge()
    {
        if (!$this->request->getCedcon()) return false;

        $mconyuge = Mercurio32::where([
            'cedcon' => $this->request->getCedcon(),
            'documento' => $this->request->getDocumento(),
            'coddoc' => $this->request->getCoddoc()
        ])->first();

        if (!$mconyuge) {
            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
                array(
                    "servicio" => "ComfacaEmpresas",
                    "metodo" => "informacion_conyuge",
                    "params" => $this->request->getCedcon()
                )
            );
            $data = false;
            $out = $procesadorComando->toArray();
            if ($out['success'] == true) {
                $data = ($out['data']) ? $out['data'] : false;
            }

            if ($data) {
                $mconyuge = new Mercurio32($data);
            } else {
                $mconyuge = new Mercurio32();
            }
        }
        if ($this->request->getBiocedu() == $this->request->getCedcon()) {
            $mconyuge->setCedcon($this->request->getBiocedu());
            $mconyuge->setTipdoc($this->request->getBiotipdoc());
            $mconyuge->setPrinom($this->request->getBioprinom());
            $mconyuge->setSegnom($this->request->getBiosegnom());
            $mconyuge->setPriape($this->request->getBiopriape());
            $mconyuge->setSegape($this->request->getBiosegape());
            $mconyuge->setEmail($this->request->getBioemail());
            $mconyuge->setTelefono($this->request->getBiophone());
            $mconyuge->setCiures($this->request->getBiocodciu());
            $mconyuge->setDireccion($this->request->getBiodire());
            $mconyuge->setZoneurbana($this->request->getBiourbana());
        }
        return $mconyuge;
    }

    function cifrarDocumento()
    {
        $cifrarDocumento = new CifrarDocumento();
        $this->outPdf = $cifrarDocumento->cifrar($this->filename, $this->lfirma->getKeyprivate());
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
