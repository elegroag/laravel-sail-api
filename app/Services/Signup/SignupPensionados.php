<?php
require_once 'SignupInterface.php';

class SignupPensionados  implements SignupInterface
{

    /**
     * solicitud variable
     * @var Mercurio38
     */
    protected $solicitud;
    private $tipopc = 9;

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
        $this->solicitud = $this->Mercurio38->findFirst(
            "coddoc='{$coddoc}' and " .
                "documento='{$documento}' and " .
                "cedtra='{$documento}' and " .
                "estado='T'"
        );
        if ($this->solicitud == FALSE) {
            $this->solicitud = new Mercurio38();
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
        $id = $this->Mercurio38->maximum('id') + 1;
        $this->solicitud = new Mercurio38();
        $this->solicitud->setTransaction(self::$transaction);
        $this->solicitud->createAttributes($data);
        $this->solicitud->setId($id);

        $this->solicitud->setDocumento($data['documento']);
        $this->solicitud->setCoddoc($data['coddoc']);
        $this->solicitud->setTipo($data['tipo']);

        $this->solicitud->setCedtra($data['cedrep']);
        $this->solicitud->setTipdoc($data['coddoc']);

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
        $this->solicitud->setTelefono($data['telefono']);
        $this->solicitud->setCelular($data['telefono']);
        $this->solicitud->setCodzon($data['codciu']);
        $this->solicitud->setCodciu($data['codciu']);
        $this->solicitud->setUsuario($data['usuario']);
        $this->solicitud->setCoddocrepleg($data['coddocrepleg']);
        $this->solicitud->setEmail($data['email']);
        $this->solicitud->setRepleg($data['repleg']);
        $this->solicitud->setCalemp($data['calemp']);
        $this->solicitud->setTipafi('3');
        $this->solicitud->setFecnac(date('Y-m-d'));
        $this->solicitud->setFecing(date('Y-m-d'));
        $this->solicitud->setSalario(0);
        $this->solicitud->setCiunac($data['codciu']);
        $this->solicitud->setSexo('M');
        $this->solicitud->setEstciv(1);
        $this->solicitud->setCabhog('N');
        $this->solicitud->setVivienda('N');
        $this->solicitud->setRural('N');
        $this->solicitud->setAutoriza('S');
        $this->solicitud->setLog('0');
        $this->solicitud->setTipemp('P');
        $this->solicitud->setDireccion("CR");
        $this->solicitud->setEstado("T");
        $this->solicitud->setCodcaj(13);
        $this->solicitud->setTipper('N');
        $this->solicitud->setCodact('0000');
        $this->solicitud->setTipsoc('00');
        $this->solicitud->setCodest(NULL);
        $this->solicitud->setPeretn(7);
        $this->solicitud->setResguardo_id(2);
        $this->solicitud->setPub_indigena_id(2);
        $this->solicitud->setFacvul(12);
        $this->solicitud->setOrisex(1);
        $this->solicitud->setTippag('T');
        $this->solicitud->setNumcue(0);
        $this->solicitud->setCargo(0);
        $this->solicitud->setCaptra('N');

        $this->salvar($this->solicitud, __LINE__);

        $this->Mercurio37->deleteAll(" tipopc='{$this->tipopc}' and numero='{$id}'");
        $this->Mercurio10->deleteAll(" tipopc='{$this->tipopc}' and numero='{$id}'");
    }

    public function getSolicitud()
    {
        return $this->solicitud;
    }
}
