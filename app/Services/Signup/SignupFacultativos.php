<?php

namespace App\Services\Signup;

use App\Models\Mercurio10;
use App\Models\Mercurio36;
use App\Models\Mercurio37;

class SignupFacultativos implements SignupInterface
{
    /**
     * solicitud variable
     *
     * @var Mercurio36
     */
    protected $solicitud;

    private $tipopc = 10;

    public function __construct() {}

    public function getTipopc()
    {
        return $this->tipopc;
    }

    public function findByDocumentTemp($documento, $coddoc, $calemp = '')
    {
        $this->solicitud = (new Mercurio36)->findFirst(
            "coddoc='{$coddoc}' and ".
                "documento='{$documento}' and ".
                "estado='T'"
        );
        if ($this->solicitud == false) {
            $this->solicitud = new Mercurio36;
        }

        return $this->solicitud;
    }

    /**
     * create function
     *
     * @param  array  $data
     * @return void
     */
    public function createSignupService($data)
    {
        $this->solicitud = new Mercurio36($data);
        $this->solicitud->setCedtra($data['cedrep']);
        $this->solicitud->setTipdoc($data['coddoc']);
        $this->solicitud->setDocumento($data['documento']);
        $this->solicitud->setCoddoc($data['coddoc']);
        $this->solicitud->setTipo($data['tipo']);
        $this->solicitud->setDireccion('CR');
        $this->solicitud->setCodact('0000');
        $this->solicitud->setTelefono($data['telefono']);
        $this->solicitud->setCelular($data['telefono']);
        $this->solicitud->setCodzon($data['codciu']);
        $this->solicitud->setCodciu($data['codciu']);
        $this->solicitud->setUsuario($data['usuario']);
        $this->solicitud->setCoddocrepleg($data['coddocrepleg']);
        $this->solicitud->setEmail($data['email']);
        $this->solicitud->setCalemp($data['calemp']);
        $this->solicitud->setTipafi('3');
        $this->solicitud->setFecnac(date('Y-m-d'));
        $this->solicitud->setFecini(date('Y-m-d'));
        $this->solicitud->setSalario(0);
        $this->solicitud->setCiunac($data['codciu']);
        $this->solicitud->setSexo('M');
        $this->solicitud->setEstciv(1);
        $this->solicitud->setNivedu('14');
        $this->solicitud->setCabhog('N');
        $this->solicitud->setCaptra('N');
        $this->solicitud->setTipdis('00');
        $this->solicitud->setVivienda('N');
        $this->solicitud->setRural('N');
        $this->solicitud->setAutoriza('S');
        $this->solicitud->setLog('0');
        $this->solicitud->setEstado('T');
        $this->solicitud->setCodest(null);
        $this->solicitud->setPeretn(7);
        $this->solicitud->setResguardo_id(2);
        $this->solicitud->setPub_indigena_id(2);
        $this->solicitud->setFacvul(12);
        $this->solicitud->setOrisex(1);
        $this->solicitud->setTippag('T');
        $this->solicitud->setNumcue(0);
        $this->solicitud->setCargo(0);
        $this->solicitud->setCodcaj(13);

        $segnom = '';
        $segape = '';
        if (strlen($data['repleg']) > 0) {
            $exp = explode(' ', trim($data['repleg']));
            switch (count($exp)) {
                case 6:
                case 7:
                case 8:
                    $prinom = $exp[0];
                    $segnom = $exp[1];
                    $priape = $exp[2];
                    $segape = $exp[3].' '.$exp[4].' '.$exp[5];
                    break;
                case 5:
                    $prinom = $exp[0];
                    $segnom = $exp[1];
                    $priape = $exp[2];
                    $segape = $exp[3].' '.$exp[4];
                    break;
                case 4:
                    $prinom = $exp[0];
                    $segnom = $exp[1];
                    $priape = $exp[2];
                    $segape = $exp[3];
                    break;
                case 3:
                    $prinom = $exp[0];
                    $priape = $exp[1].' '.$exp[2];
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
        $this->solicitud->save();
        $id = $this->solicitud->getId();

        Mercurio37::where('tipopc', $this->tipopc)->where('numero', $id)->delete();
        Mercurio10::where('tipopc', $this->tipopc)->where('numero', $id)->delete();
    }

    public function getSolicitud()
    {
        return $this->solicitud;
    }
}
