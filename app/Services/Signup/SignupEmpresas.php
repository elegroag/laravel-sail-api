<?php

namespace App\Services\Signup;

use App\Models\Mercurio10;
use App\Models\Mercurio30;
use App\Models\Mercurio37;
use App\Models\Tranoms;

class SignupEmpresas  implements SignupInterface
{
    /**
     * solicitud variable
     * @var Mercurio30
     */
    private $solicitud;
    private $tipopc = 2;


    public function __construct() {}


    public function getTipopc()
    {
        return $this->tipopc;
    }


    public function findByDocumentTemp($documento, $coddoc, $calemp = '')
    {
        $this->solicitud = (new Mercurio30())->findFirst(
            "coddoc='{$coddoc}' and " .
                "documento='{$documento}' and " .
                "nit='{$documento}' and " .
                "calemp='{$calemp}' and " .
                "estado='T'"
        );
        if ($this->solicitud == FALSE) {
            $this->solicitud = new Mercurio30();
        }
        return $this->solicitud;
    }

    /**
     * create function
     * @param array $data
     * @return void
     */
    public function createSignupService($data)
    {
        $solicitud = new Mercurio30($data);

        $solicitud->setDocumento($data['documento']);
        $solicitud->setCoddoc($data['coddoc']);
        $solicitud->setTipo($data['tipo']);
        $solicitud->setEmailpri($data['email']);
        $solicitud->setCelular($data['telefono']);
        $solicitud->getCelpri($data['telefono']);
        $solicitud->setTelpri($data['telefono']);
        $solicitud->setDigver('0');
        $solicitud->setDireccion("CR");
        $solicitud->setSigla("");
        $solicitud->setTottra(1);
        $solicitud->setDirpri("");
        $solicitud->setPrinom("");
        $solicitud->setSegnom("");
        $solicitud->setPriape("");
        $solicitud->setSegape("");
        $solicitud->setMatmer("");
        $solicitud->setLog('0');
        $solicitud->setEstado("T");
        $solicitud->setFecini(date('Y-m-d'));
        $solicitud->setValnom(0);
        $solicitud->setCodact('0000');
        $solicitud->setFecini(date('Y-m-d'));
        $solicitud->setTottra(1);
        $solicitud->setCodcaj(13);
        $solicitud->setCodest(NULL);
        $solicitud->setTipemp('E');

        $segnom = '';
        $segape = '';
        if (strlen($data['repleg']) > 0) {
            $exp = explode(" ", trim($data['repleg']));
            switch (count($exp)) {
                case 6:
                case 7:
                case 8:
                    $prinom = $exp[0];
                    $segnom = $exp[1];
                    $priape = $exp[2];
                    $segape = $exp[3] . ' ' . $exp[4] . ' ' . $exp[5];
                    break;
                case 5:
                    $prinom = $exp[0];
                    $segnom = $exp[1];
                    $priape = $exp[2];
                    $segape = $exp[3] . ' ' . $exp[4];
                    break;
                case 4:
                    $prinom = $exp[0];
                    $segnom = $exp[1];
                    $priape = $exp[2];
                    $segape = $exp[3];
                    break;
                case 3:
                    $prinom = $exp[0];
                    $priape = $exp[1] . ' ' . $exp[2];
                    break;
                case 2:
                    $prinom = $exp[0];
                    $priape = $exp[1];
                    break;
                case 1:
                    $prinom = $exp[0];
                    $priape = $exp[0];
                    break;
                default:
                    break;
            }
        }

        $solicitud->setPriape($priape);
        $solicitud->setSegape($segape);
        $solicitud->setPrinom($prinom);
        $solicitud->setSegnom($segnom);
        $solicitud->save();

        Mercurio37::where("tipopc", $this->tipopc)->where("numero", $solicitud->getId())->delete();
        Mercurio10::where("tipopc", $this->tipopc)->where("numero", $solicitud->getId())->delete();
        Tranoms::where("request", $solicitud->getId())->delete();

        $this->solicitud = $solicitud;
    }

    public function getSolicitud()
    {
        return $this->solicitud;
    }
}
