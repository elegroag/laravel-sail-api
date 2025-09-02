<?php
namespace App\Services\Autentications;
use App\Models\Mercurio07;

class AutenticaFoninnez extends AutenticaGeneral
{
    public function __construct()
    {
        parent::__construct();
        $this->tipo = 'N';
        $this->tipoName = 'Foniñez';
    }

    /**
     * comprobarSISU function
     * autenticar foniñez, en sesion consulta y gestion,
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
        $usuario = (new Mercurio07)->findFirst("tipo='N' AND documento='{$documento}' AND coddoc='{$coddoc}'");
        if ($usuario) {
            if ($usuario->getEstado() == 'I') {
                $usuario->setEstado('A');
                $usuario->save();
            }
        }
        $this->afiliado = $usuario;
        return true;
    }
}
