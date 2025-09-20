<?php

namespace App\Services\Signup;

use App\Exceptions\DebugException;
use App\Models\Adapter\DbBase;
use App\Models\Mercurio07;
use App\Services\PreparaFormularios\GestionFirmaNoImage;
use App\Services\Request;
use App\Services\Signup\SignupEmpresas;
use App\Services\Signup\SignupFacultativos;
use App\Services\Signup\SignupIndependientes;
use App\Services\Signup\SignupPensionados;
use App\Services\Signup\SignupDomestico;
use App\Services\Signup\SignupParticular;
use App\Services\Utils\AsignarFuncionario;

class SignupService
{

    public function execute(Request $request)
    {
        $cedrep = $request->getParam('cedrep');
        $coddoc = $request->getParam('coddoc');
        $repleg = $request->getParam('repleg');
        $email = $request->getParam('email');
        $codciu = $request->getParam('codciu');
        $tipper = $request->getParam('tipper');
        $telefono = $request->getParam('telefono');
        $tipo = $request->getParam('tipo');
        $calemp = $request->getParam('calemp');
        $tipsoc = $request->getParam('tipsoc');
        $razsoc = $request->getParam('razsoc');
        $coddocrepleg = $request->getParam('coddocrepleg');
        $nit = $request->getParam('nit');

        switch ($tipo) {
            case 'E':
                $signupEntity = new SignupEmpresas();
                break;
            case 'I':
                $signupEntity = new SignupIndependientes();
                break;
            case 'F':
                $signupEntity = new SignupFacultativos();
                break;
            case 'O':
                $signupEntity = new SignupPensionados();
                break;
            case 'S':
                $signupEntity = new SignupDomestico();
                break;
            case 'P':
            case 'T':
                $signupParticular = new SignupParticular();
                $signupParticular->settings(
                    new Request(
                        array(
                            "documento" => $cedrep,
                            "coddoc" => $coddoc,
                            "nombre" => $repleg,
                            "email" => $email,
                            "codciu" => $codciu,
                            "tipo" => $tipo,
                            "razsoc" => $razsoc
                        )
                    )
                );
                $signupParticular->createUserMercurio();
                $solicitud = Mercurio07::where('coddoc', $coddoc)
                    ->where('documento', $cedrep)
                    ->where('tipo', $tipo)
                    ->first();

                break;
            default:
                throw new DebugException("Error el tipo de afiliación es requerido", 1);
                break;
        }

        if ($tipo !== 'P' && $tipo !== 'T') {
            //usa codciu para asignar funcionario
            $usuario = (new AsignarFuncionario())->asignar($signupEntity->getTipopc(), $codciu);

            $signupParticular = new SignupParticular($signupEntity);
            $signupParticular->main(
                new Request(
                    array(
                        "nit" => $nit,
                        "cedrep" => $cedrep,
                        "coddoc" => $coddoc,
                        "repleg" => $repleg,
                        "email" => $email,
                        "codciu" => $codciu,
                        "tipper" => $tipper,
                        "telefono" => $telefono,
                        "calemp" => $calemp,
                        "tipo" => $tipo,
                        "tipsoc" => $tipsoc,
                        "coddocrepleg" => $coddocrepleg,
                        "razsoc" => $razsoc,
                        "usuario" => $usuario
                    )
                )
            );
            $solicitud = $signupEntity->getSolicitud();
        }

        $this->autoFirma($solicitud->getDocumento(), $solicitud->getCoddoc());
        return [
            "success" => true,
            "msj" => "El proceso de registro como persona particular, se ha completado con éxito, " .
                "las credenciales de acceso le serán enviadas al respectivo correo registrado. " .
                "Vamos a continuar.\n",
            "documento" => $solicitud->getDocumento(),
            "coddoc" => $solicitud->getCoddoc(),
            "tipo" => 'P',
            "tipafi" => $tipo,
            "id" => ($tipo == 'P') ? $solicitud->getDocumento() : $solicitud->getId()
        ];
    }

    function autoFirma($documento, $coddoc)
    {
        $gestionFirmas = new GestionFirmaNoImage(
            array(
                "documento" => $documento,
                "coddoc" => $coddoc
            )
        );
        if ($gestionFirmas->hasFirma() == False) {
            $gestionFirmas->guardarFirma();
            $gestionFirmas->generarClaves();
        } else {
            $firma = $gestionFirmas->getFirma();
            if (is_null($firma->getKeypublic()) || is_null($firma->getKeyprivate())) {
                $gestionFirmas->guardarFirma();
                $gestionFirmas->generarClaves();
            }
        }
    }
}
