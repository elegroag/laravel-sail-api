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
        $this->solicitud = Mercurio36::where("coddoc", $coddoc)
            ->where("documento", $documento)
            ->where("estado", 'T')
            ->first();

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
        $solicitud = new Mercurio36($data);
        $solicitud->documento = $data['documento'];
        $solicitud->coddoc = $data['coddoc'];
        $solicitud->tipo = $data['tipo'];

        $solicitud->cedtra = $data['cedrep'];
        $solicitud->tipdoc = $data['coddoc'];
        $solicitud->direccion = 'CR';
        $solicitud->codact = '0000';
        $solicitud->telefono = $data['telefono'];
        $solicitud->celular = $data['telefono'];
        $solicitud->codzon = $data['codciu'];
        $solicitud->codciu = $data['codciu'];
        $solicitud->usuario = $data['usuario'];
        $solicitud->coddocrepleg = $data['coddocrepleg'];
        $solicitud->email = $data['email'];
        $solicitud->calemp = $data['calemp'];
        $solicitud->tipafi = '3';
        $solicitud->fecnac = date('Y-m-d');
        $solicitud->fecini = date('Y-m-d');
        $solicitud->salario = 0;
        $solicitud->ciunac = $data['codciu'];
        $solicitud->sexo = 'M';
        $solicitud->estciv = 1;
        $solicitud->nivedu = '14';
        $solicitud->cabhog = 'N';
        $solicitud->captra = 'N';
        $solicitud->tipdis = '00';
        $solicitud->vivienda = 'N';
        $solicitud->rural = 'N';
        $solicitud->autoriza = 'S';
        $solicitud->log = '0';
        $solicitud->estado = 'T';
        $solicitud->codest = null;
        $solicitud->peretn = 7;
        $solicitud->resguardo_id = 2;
        $solicitud->pub_indigena_id = 2;
        $solicitud->facvul = 12;
        $solicitud->orisex = 1;
        $solicitud->tippag = 'T';
        $solicitud->numcue = 0;
        $solicitud->cargo = 0;
        $solicitud->codcaj = 13;

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
        $solicitud->priape = $priape;
        $solicitud->segape = $segape;
        $solicitud->prinom = $prinom;
        $solicitud->segnom = $segnom;
        $solicitud->save();

        Mercurio37::where('tipopc', $this->tipopc)->where('numero', $solicitud->id)->delete();
        Mercurio10::where('tipopc', $this->tipopc)->where('numero', $solicitud->id)->delete();
        $this->solicitud = $solicitud;
    }

    public function getSolicitud()
    {
        return $this->solicitud;
    }
}
