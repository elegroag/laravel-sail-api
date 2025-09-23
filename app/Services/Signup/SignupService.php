<?php

namespace App\Services\Signup;

use App\Exceptions\DebugException;
use App\Models\Mercurio07;
use App\Services\PreparaFormularios\GestionFirmaNoImage;
use App\Services\Request;
use App\Services\Signup\SignupParticular;
use App\Services\Utils\AsignarFuncionario;
use App\Services\Utils\Comman;

class SignupService
{

    public function execute(SignupInterface|null $signupEntity, Request $request)
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

        if ($signupEntity == null) {
            $signupParticular = new SignupParticular(
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
        } else {
            //usa codciu para asignar funcionario
            $usuario = (new AsignarFuncionario())->asignar($signupEntity->getTipopc(), $codciu);

            $signupParticular = new SignupParticular(
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
            $signupParticular->main();
            $this->crearSolicitud($signupEntity, $signupParticular, $request);
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

    /**
     * crearSolicitud function
     * @return object
     */
    function crearSolicitud(
        SignupInterface $signupEntity,
        SignupParticular $signupParticular,
        Request $request
    ) {
        $empresaSisuweb = $this->buscaEmpresaSisu($request->getParam('nit'));
        $entity = $signupEntity->findByDocumentTemp(
            $signupParticular->documento,
            $signupParticular->coddoc,
            $signupParticular->calemp
        );


        //si no existe ninguna solicitud
        if ($entity->getId() == null) {
            if ($empresaSisuweb) {
                $empresaSisuweb['coddoc'] = $signupParticular->coddoc;
                $empresaSisuweb['documento'] = $signupParticular->documento;
                $empresaSisuweb['tipo'] = $signupParticular->tipo;
                $empresaSisuweb['cedtra'] = $signupParticular->documento;
                $empresaSisuweb['usuario'] = $request->getParam('usuario');
                $empresaSisuweb['tipdoc'] = $request->getParam('tipdoc');

                $signupEntity->createSignupService($empresaSisuweb);
            } else {
                $signupEntity->createSignupService(
                    array(
                        'coddoc' => $signupParticular->coddoc,
                        'documento' => $signupParticular->documento,
                        'tipo' => $signupParticular->tipo,
                        'cedrep' => $request->getParam('cedrep'),
                        'cedtra' => $request->getParam('cedrep'),
                        'tipdoc' => $request->getParam('tipdoc'),
                        'repleg' => $request->getParam('repleg'),
                        'email' => $request->getParam('email'),
                        'codciu' => $request->getParam('codciu'),
                        'tipper' => $request->getParam('tipper'),
                        'telefono' => $request->getParam('telefono'),
                        'calemp' => $request->getParam('calemp'),
                        'tipsoc' => $request->getParam('tipsoc'),
                        'coddocrepleg' => $request->getParam('coddocrepleg'),
                        'razsoc' => $request->getParam('razsoc'),
                        'usuario' => $request->getParam('usuario'),
                        'tipemp' => $request->getParam('tipemp'),
                        'nit' => $request->getParam('nit')
                    )
                );
            }
        } else {
            throw new DebugException("Error la cuenta ya está registrada, y dispone de una solicitud en estado temporal.", 1);
        }
        $solicitud = $signupEntity->getSolicitud();
        $solicitud->save();
        return $solicitud;
    }

    /**
     * buscaEmpresaSisu function
     * @param integer $nit
     * @return object
     */
    function buscaEmpresaSisu($nit)
    {
        $ps = Comman::Api();
        $ps->runCli(
            array(
                "servicio" => "ComfacaEmpresas",
                "metodo" => "informacion_empresa",
                "params" => array(
                    "nit" => $nit
                )
            )
        );
        if ($ps->isJson() == False) {
            return false;
        }
        $out = $ps->toArray();
        if ($out['success'] == false) return false;
        return $out['data'];
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
