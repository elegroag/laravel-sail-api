<?php

namespace App\Services\Signup;

use App\Models\Mercurio10;
use App\Models\Mercurio37;
use App\Models\Mercurio41;

class SignupIndependientes implements SignupInterface
{
    /**
     * solicitud variable
     *
     * @var Mercurio41
     */
    protected $solicitud;

    private $tipopc = 13;

    public function __construct() {}

    public function getTipopc()
    {
        return $this->tipopc;
    }

    public function findByDocumentTemp($documento, $coddoc, $calemp = '')
    {
        $this->solicitud = Mercurio41::where('coddoc', $coddoc)
            ->where('documento', $documento)
            ->where('cedtra', $documento)
            ->where('estado', 'T')
            ->first();

        if ($this->solicitud == false) {
            $this->solicitud = new Mercurio41;
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
        $solicitud = new Mercurio41($data);

        $solicitud->documento = $data['documento'];
        $solicitud->coddoc = $data['coddoc'];
        $solicitud->tipo = $data['tipo'];

        $solicitud->cedtra = $data['cedtra'];
        $solicitud->usuario = $data['usuario'];
        $solicitud->coddocrepleg = $data['coddocrepleg'];
        $solicitud->tipdoc = $data['coddoc'];
        $solicitud->calemp = $data['calemp'];
        $solicitud->fecsol = date('Y-m-d');
        $solicitud->codact = '0000';
        $solicitud->codcaj = 13;
        $solicitud->log = '0';
        $solicitud->codciu = $data['codciu'];
        $solicitud->telefono = $data['telefono'];
        $solicitud->celular = $data['telefono'];
        $solicitud->email = $data['email'];
        $solicitud->codzon = $data['codciu'];
        $solicitud->tipdoc = $data['coddoc'];
        $solicitud->sexo = 'I';
        $solicitud->fecnac = null;
        $solicitud->ciunac = $data['codciu'];
        $solicitud->salario = '1160000';
        $solicitud->fecini = date('Y-m-d');
        $solicitud->tipafi = '3';
        $solicitud->vivienda = 'N';
        $solicitud->rural = 'N';
        $solicitud->estciv = '1';
        $solicitud->cabhog = 'N';
        $solicitud->estado = 'T';
        $solicitud->captra = 'N';
        $solicitud->tipdis = '00';
        $solicitud->nivedu = 14;
        $solicitud->autoriza = 'S';
        $solicitud->codest = null;
        $solicitud->peretn = 7;
        $solicitud->resguardo_id = 2;
        $solicitud->pub_indigena_id = 2;
        $solicitud->facvul = 12;
        $solicitud->orisex = 1;
        $solicitud->tippag = 'T';
        $solicitud->numcue = 0;
        $solicitud->cargo = 0;

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
        $id = $solicitud->id;

        Mercurio37::where('tipopc', $this->tipopc)
            ->where('numero', $id)
            ->delete();

        Mercurio10::where('tipopc', $this->tipopc)
            ->where('numero', $id)
            ->delete();

        $this->solicitud = $solicitud;
    }

    public function getSolicitud()
    {
        return $this->solicitud;
    }
}
