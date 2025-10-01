<?php

namespace App\Services\Autentications;

use App\Models\Mercurio07;

class AutenticaParticular extends AutenticaGeneral
{
    public function __construct()
    {
        parent::__construct();
        $this->tipo = 'P';
        $this->tipoName = 'Particular';
    }

    /**
     * comprobarSISU function
     * autenticar empresas, en sesion consulta y gestion,
     * comprobar que la empresa este registrada en SISU
     * comprueba que este el usuario de la empresa en mercurio
     * hace los registro de forma automatica
     * @param [type] $documento
     * @param [type] $coddoc
     * @return bool
     */
    public function comprobarSISU($documento, $coddoc)
    {
        /**
         * buscar usuario particular en mercurio
         */
        $usuarioParticular = Mercurio07::where("tipo", $this->tipo)
            ->where("documento", $documento)
            ->where("coddoc", $coddoc)
            ->first();

        if ($usuarioParticular) {
            if ($usuarioParticular->getEstado() == 'I') {
                $usuarioParticular->setEstado('A');
                $usuarioParticular->save();
            }
        }
        $this->afiliado = $usuarioParticular;
        return true;
    }
}
