<?php
require_once 'SignupInterface.php';

class SignupIndependientes  implements SignupInterface
{

    /**
     * solicitud variable
     * @var Mercurio41
     */
    protected $solicitud;
    private $tipopc = 13;

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
        $this->solicitud = $this->Mercurio41->findFirst(
            "coddoc='{$coddoc}' and " .
                "documento='{$documento}' and " .
                "cedtra='{$documento}' and " .
                "estado='T'"
        );
        if ($this->solicitud == FALSE) {
            $this->solicitud = new Mercurio41();
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
        $id = $this->Mercurio41->maximum('id') + 1;
        $this->solicitud = new Mercurio41();
        $this->solicitud->setTransaction(self::$transaction);
        $this->solicitud->createAttributes($data);

        $this->solicitud->setId($id);
        $this->solicitud->setCedtra($data['cedtra']);
        $this->solicitud->setDocumento($data['documento']);
        $this->solicitud->setUsuario($data['usuario']);
        $this->solicitud->setCoddocrepleg($data['coddocrepleg']);
        $this->solicitud->setCoddoc($data['coddoc']);
        $this->solicitud->setTipdoc($data['coddoc']);
        $this->solicitud->setCalemp($data['calemp']);
        $this->solicitud->setFecsol(date('Y-m-d'));
        $this->solicitud->setCodact('0000');
        $this->solicitud->setTipo('P');
        $this->solicitud->setCodcaj(13);
        $this->solicitud->setLog('0');
        $this->solicitud->setCodciu($data['codciu']);
        $this->solicitud->setTelefono($data['telefono']);
        $this->solicitud->setCelular($data['telefono']);
        $this->solicitud->setEmail($data['email']);
        $this->solicitud->setCodzon($data['codciu']);
        $this->solicitud->setTipdoc($data['coddoc']);
        $this->solicitud->setSexo('I');
        $this->solicitud->setFecnac(null);
        $this->solicitud->setCiunac($data['codciu']);
        $this->solicitud->setSalario('1160000');
        $this->solicitud->setFecini(date('Y-m-d'));
        $this->solicitud->setTipafi('3');
        $this->solicitud->setVivienda('N');
        $this->solicitud->setRural('N');
        $this->solicitud->setEstciv('1');
        $this->solicitud->setCabhog('N');
        $this->solicitud->setEstado('T');
        $this->solicitud->setCaptra('N');
        $this->solicitud->setTipdis('00');
        $this->solicitud->setNivedu(14);
        $this->solicitud->setAutoriza('S');
        $this->solicitud->setCodest(null);
        $this->solicitud->setPeretn(7);
        $this->solicitud->setResguardo_id(2);
        $this->solicitud->setPub_indigena_id(2);
        $this->solicitud->setFacvul(12);
        $this->solicitud->setOrisex(1);
        $this->solicitud->setTippag('T');
        $this->solicitud->setNumcue(0);
        $this->solicitud->setCargo(0);

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

        $this->Mercurio37->deleteAll(" tipopc='{$this->tipopc}' and numero='{$id}'");
        $this->Mercurio10->deleteAll(" tipopc='{$this->tipopc}' and numero='{$id}'");
    }

    public function getSolicitud()
    {
        return $this->solicitud;
    }
}
