<?php

namespace App\Services\FormulariosAdjuntos;

use App\Library\Collections\ParamsBeneficiario;
use App\Library\Collections\ParamsTrabajador;
use App\Library\Tcpdf\KumbiaPDF;
use App\Models\Mercurio16;
use App\Models\Mercurio31;
use App\Models\Mercurio32;
use App\Models\Mercurio36;
use App\Models\Mercurio38;
use App\Models\Mercurio41;
use App\Services\Entidades\TrabajadorService;
use App\Services\Formularios\FactoryDocuments;
use App\Services\PreparaFormularios\CifrarDocumento;
use App\Services\Api\ApiSubsidio;

class BeneficiarioAdjuntoService
{
    /**
     * request variable
     *
     * @var Mercurio34
     */
    private $request;

    private $lfirma;

    private $filename;

    private $outPdf;

    private $fhash;

    private $claveCertificado;

    private $user;

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
                'metodo' => 'parametros_trabajadores',
            ]
        );

        $datos_captura = $procesadorComando->toArray();
        $paramsConyuge = new ParamsTrabajador;
        $paramsConyuge->setDatosCaptura($datos_captura);

        $procesadorComando = new ApiSubsidio();
        $procesadorComando->send(
            [
                'servicio' => 'ComfacaAfilia',
                'metodo' => 'parametros_beneficiarios',
            ]
        );
        $datos_captura = $procesadorComando->toArray();
        $paramsBeneficiario = new ParamsBeneficiario;
        $paramsBeneficiario->setDatosCaptura($datos_captura);
    }

    public function getTrabajador()
    {
        $trabajador = false;
        $mtrabajador = false;
        switch (session('tipo')) {
            case 'I':
                $mtrabajador = Mercurio41::where('cedtra', $this->request->getCedtra())
                    ->where('documento', $this->request->getDocumento())
                    ->where('coddoc', $this->request->getCoddoc())
                    ->first();
                break;
            case 'O':
                $mtrabajador = Mercurio38::where('cedtra', $this->request->getCedtra())
                    ->where('documento', $this->request->getDocumento())
                    ->where('coddoc', $this->request->getCoddoc())
                    ->first();
                break;
            case 'F':
                $mtrabajador = Mercurio36::where('cedtra', $this->request->getCedtra())
                    ->where('documento', $this->request->getDocumento())
                    ->where('coddoc', $this->request->getCoddoc())
                    ->first();
                break;
            case 'T':
            case 'E':
                $mtrabajador = Mercurio31::where('cedtra', $this->request->getCedtra())
                    ->where('documento', $this->request->getDocumento())
                    ->where('coddoc', $this->request->getCoddoc())
                    ->first();
                break;
            default:
                $trabajador = Mercurio31::where('cedtra', $this->request->getCedtra())
                    ->where('documento', $this->request->getDocumento())
                    ->where('coddoc', $this->request->getCoddoc())
                    ->whereIn('estado', ['A', 'P', 'T'])
                    ->first();
                break;
        }

        $trabajadorService = new TrabajadorService;
        $data = $trabajadorService->buscarTrabajadorSubsidio($this->request->getCedtra());
        if ($data) {
            if ($mtrabajador) {
                $trabajador = clone $mtrabajador;
            } else {
                $trabajador = new Mercurio31;
            }
        }

        return $trabajador;
    }

    public function formulario()
    {
        $this->filename = "formulario_beneficiario_{$this->request->getNumdoc()}.pdf";
        KumbiaPDF::setBackgroundImage(public_path('img/form/beneficiarios/form_adicion_beneficiario.png'));
        $fabrica = new FactoryDocuments;
        $documento = $fabrica->crearFormulario('beneficiario');
        $documento->setParamsInit(
            [
                'beneficiario' => $this->request,
                'trabajador' => $this->getTrabajador(),
                'bioconyu' => $this->getBiologioConyuge(),
                'filename' => $this->filename,
            ]
        );

        $documento->main();
        $documento->outPut();
        $this->cifrarDocumento();

        return $this;
    }

    public function declaraJurament()
    {
        $this->filename = "declaracion_hijo_{$this->request->getNumdoc()}.pdf";
        $parent = $this->request->getParent();
        switch ($parent) {
            case '1':
                $page = public_path('img/form/declaraciones/declaracion_jura_hijo.png');
                break;
            case '4':
                $page = public_path('img/form/declaraciones/declaracion_jura_custodia.png');
                break;
            case '3': // padre
            case '2': // hermano
                $page = public_path('img/form/declaraciones/declaracion_jura_padres.png');
                break;
            case '5': // cuidador persona discapacitada
                $page = public_path('img/form/declaraciones/declaracion_jura_cuidador.png');
                break;
            default:
                break;
        }
        KumbiaPDF::setBackgroundImage($page);
        $fabrica = new FactoryDocuments;

        $documento = $fabrica->crearDeclaracion('beneficiario');
        $documento->setParamsInit(
            [
                'beneficiario' => $this->request,
                'trabajador' => $this->getTrabajador(),
                'firma' => $this->lfirma,
                'filename' => $this->filename,
            ]
        );

        $documento->main();
        $documento->outPut();
        $this->cifrarDocumento();

        return $this;
    }

    public function getBiologioConyuge()
    {
        if (! $this->request->getCedcon()) {
            return false;
        }

        $mconyuge = Mercurio32::where([
            'cedcon' => $this->request->getCedcon(),
            'documento' => $this->request->getDocumento(),
            'coddoc' => $this->request->getCoddoc(),
        ])->first();

        if (! $mconyuge) {
            $procesadorComando = new ApiSubsidio();
            $procesadorComando->send(
                [
                    'servicio' => 'ComfacaEmpresas',
                    'metodo' => 'informacion_conyuge',
                    'params' => $this->request->getCedcon(),
                ]
            );
            $data = false;
            $out = $procesadorComando->toArray();
            if ($out['success'] == true) {
                $data = ($out['data']) ? $out['data'] : false;
            }

            if ($data) {
                $mconyuge = new Mercurio32($data);
            } else {
                $mconyuge = new Mercurio32;
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
