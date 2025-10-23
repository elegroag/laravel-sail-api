<?php

namespace App\Services\Signup;

use App\Models\Mercurio10;
use App\Models\Mercurio30;
use App\Models\Mercurio37;
use App\Models\Tranoms;

class SignupEmpresas implements SignupInterface
{
    /**
     * solicitud variable
     *
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
        $this->solicitud = Mercurio30::where("coddoc", $coddoc)
            ->where("documento", $documento)
            ->where("nit", $documento)
            ->where("calemp", $calemp)
            ->where("estado", 'T')
            ->first();

        if ($this->solicitud == false) {
            $this->solicitud = new Mercurio30;
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
        $solicitud = new Mercurio30($data);

        $solicitud->documento = $data['documento'];
        $solicitud->coddoc = $data['coddoc'];
        $solicitud->tipo = $data['tipo'];

        $solicitud->emailpri = $data['email'];
        $solicitud->celular = $data['telefono'];
        $solicitud->celpri = $data['telefono'];
        $solicitud->telpri = $data['telefono'];
        $solicitud->digver = '0';
        $solicitud->direccion = 'CR';
        $solicitud->sigla = '';
        $solicitud->tottra = 1;
        $solicitud->dirpri = '';
        $solicitud->prinom = '';
        $solicitud->segnom = '';
        $solicitud->priape = '';
        $solicitud->segape = '';
        $solicitud->matmer = '';
        $solicitud->log = '0';
        $solicitud->estado = 'T';
        $solicitud->fecini = date('Y-m-d');
        $solicitud->valnom = 0;
        $solicitud->codact = '0000';
        $solicitud->fecini = date('Y-m-d');
        $solicitud->tottra = 1;
        $solicitud->codcaj = 13;
        $solicitud->codest = null;
        $solicitud->tipemp = 'E';

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
        Tranoms::where('request', $solicitud->id)->delete();

        $this->solicitud = $solicitud;
    }

    public function getSolicitud()
    {
        return $this->solicitud;
    }
}
