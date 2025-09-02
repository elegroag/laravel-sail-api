<?php
require_service('Signup/SignupInterface');

class SignupEmpresas  implements SignupInterface
{
    /**
     * solicitud variable
     * @var Mercurio30
     */
    private $solicitud;
    private $tipopc = 2;


    public function __construct()
    {
        parent::__construct();
    }


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
        $id = (new Mercurio30())->maximum('id') + 1;
        $this->solicitud = new Mercurio30();
        $this->solicitud->setTransaction(self::$transaction);
        $this->solicitud->createAttributes($data);

        $this->solicitud->setId($id);
        $this->solicitud->setDocumento($data['documento']);
        $this->solicitud->setCoddoc($data['coddoc']);
        $this->solicitud->setTipo($data['tipo']);

        $this->solicitud->setEmailpri($data['email']);
        $this->solicitud->setCelular($data['telefono']);
        $this->solicitud->getCelpri($data['telefono']);
        $this->solicitud->setTelpri($data['telefono']);
        $this->solicitud->setDigver('0');
        $this->solicitud->setDireccion("CR");
        $this->solicitud->setSigla("");
        $this->solicitud->setTottra(1);
        $this->solicitud->setDirpri("");
        $this->solicitud->setPrinom("");
        $this->solicitud->setSegnom("");
        $this->solicitud->setPriape("");
        $this->solicitud->setSegape("");
        $this->solicitud->setMatmer("");
        $this->solicitud->setLog('0');
        $this->solicitud->setEstado("T");
        $this->solicitud->setFecini(date('Y-m-d'));
        $this->solicitud->setValnom(0);
        $this->solicitud->setCodact('0000');
        $this->solicitud->setFecini(date('Y-m-d'));
        $this->solicitud->setTottra(1);
        $this->solicitud->setCodcaj(13);
        $this->solicitud->setCodest(NULL);
        $this->solicitud->setTipemp('E');

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

        $this->solicitud->setPriape($priape);
        $this->solicitud->setSegape($segape);
        $this->solicitud->setPrinom($prinom);
        $this->solicitud->setSegnom($segnom);

        $this->salvar($this->solicitud, __LINE__);

        (new Mercurio37())->deleteAll(" tipopc='{$this->tipopc}' and numero='{$id}'");
        (new Mercurio10())->deleteAll(" tipopc='{$this->tipopc}' and numero='{$id}'");
        (new Tranoms())->deleteAll(" request='{$id}'");
    }

    public function getSolicitud()
    {
        return $this->solicitud;
    }
}
