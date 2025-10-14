<?php

namespace App\Services\Signup;

use App\Exceptions\DebugException;
use App\Models\Mercurio07;
use App\Services\PreparaFormularios\GestionFirmaNoImage;
use App\Services\Srequest;
use App\Services\Utils\AsignarFuncionario;
use App\Services\Utils\Comman;

class SignupService
{
    private $cedrep;

    private $coddoc;

    private $repleg;

    private $email;

    private $codciu;

    private $tipper;

    private $telefono;

    private $tipo;

    private $calemp;

    private $tipsoc;

    private $razsoc;

    private $coddocrepleg;

    private $nit;

    private $password;

    public function execute(?SignupInterface $signupEntity, Srequest $request)
    {
        $this->coddoc = $request->getParam('coddoc');
        $this->email = $request->getParam('email');
        $this->codciu = $request->getParam('codciu');
        $this->tipper = $request->getParam('tipper');
        $this->telefono = $request->getParam('telefono');
        $this->tipo = $request->getParam('tipo');
        $this->calemp = $request->getParam('calemp');
        $this->tipsoc = $request->getParam('tipsoc');
        $this->razsoc = $request->getParam('razsoc');
        $this->password = $request->getParam('password');

        $this->nit = $request->getParam('nit');

        $str_coddocs = coddoc_repleg_array();
        if ($request->getParam('is_delegado') == true) {
            $this->coddocrepleg = $str_coddocs[$request->getParam('rep_coddoc')];
            $this->repleg = $request->getParam('rep_nombre');
            $this->cedrep = $request->getParam('rep_documento');
        } else {
            $this->coddocrepleg = $str_coddocs[$request->getParam('coddoc')];
            $this->repleg = $request->getParam('nombre');
            $this->cedrep = $request->getParam('documento');
        }

        if ($signupEntity == null) {
            if ($this->tipo == 'T') {
                $res = $this->validaTrabajadorEmpresa();
                if ($res == false) {
                    throw new DebugException('Error al validar el trabajador no está afiliado a la empresa con nit: ' . $this->nit, 501);
                }
            }
            $signupParticular = new SignupParticular(
                new Srequest(
                    [
                        'documento' => $this->cedrep,
                        'coddoc' => $this->coddoc,
                        'nombre' => $this->repleg,
                        'email' => $this->email,
                        'codciu' => $this->codciu,
                        'tipo' => $this->tipo, // aplica para particular o trabajador
                        'razsoc' => $this->razsoc,
                        'password' => $this->password,
                    ]
                )
            );
            $signupParticular->createUserMercurio();
            $solicitud = Mercurio07::where('coddoc', $this->coddoc)
                ->where('documento', $this->cedrep)
                ->where('tipo', $this->tipo)
                ->first();
        } else {
            // usa codciu para asignar funcionario
            $usuario = (new AsignarFuncionario)->asignar($signupEntity->getTipopc(), $this->codciu);

            $signupParticular = new SignupParticular(
                new Srequest(
                    [
                        'nit' => $this->nit,
                        'cedrep' => $this->cedrep,
                        'coddoc' => $this->coddoc,
                        'repleg' => $this->repleg,
                        'email' => $this->email,
                        'codciu' => $this->codciu,
                        'tipper' => $this->tipper,
                        'telefono' => $this->telefono,
                        'calemp' => $this->calemp,
                        'tipo' => $this->tipo,
                        'tipsoc' => $this->tipsoc,
                        'coddocrepleg' => $this->coddocrepleg,
                        'razsoc' => $this->razsoc,
                        'usuario' => $usuario,
                        'password' => $this->password,
                    ]
                )
            );
            $signupParticular->main();

            $request->setParam('usuario', $usuario);
            $request->setParam('repleg', $this->repleg);
            $request->setParam('coddocrepleg', $this->coddocrepleg);
            $request->setParam('tipo', $this->tipo);

            $this->crearSolicitud($signupEntity, $request);
            $solicitud = $signupEntity->getSolicitud();
        }

        $this->autoFirma($solicitud->getDocumento(), $solicitud->getCoddoc(), $this->password);

        return [
            'success' => true,
            'msj' => 'El proceso de registro como persona particular, se ha completado con éxito, ' .
                'las credenciales de acceso le serán enviadas al respectivo correo registrado. ' .
                "Vamos a continuar.\n",
            'documento' => $solicitud->getDocumento(),
            'coddoc' => $solicitud->getCoddoc(),
            'tipo' => $this->tipo,
            'tipafi' => $this->tipo,
            'id' => ($this->tipo == 'P' || $this->tipo == 'T') ? $solicitud->getDocumento() : $solicitud->getId(),
        ];
    }

    /**
     * crearSolicitud function
     *
     * @return object
     */
    public function crearSolicitud(
        SignupInterface $signupEntity,
        Srequest $request
    ) {
        $empresaSisuweb = $this->buscaEmpresaSisu($request->getParam('nit'));
        $documentoSolicitud = $request->getParam('tipper') === 'J'
            ? $request->getParam('nit')
            : $this->cedrep;

        $entity = $signupEntity->findByDocumentTemp(
            $documentoSolicitud,
            $this->coddoc,
            $this->calemp
        );

        // si no existe ninguna solicitud
        if ($entity->getId() == null) {
            if ($empresaSisuweb) {
                $empresaSisuweb['coddoc'] = $this->coddoc;
                $empresaSisuweb['documento'] = $documentoSolicitud;
                $empresaSisuweb['cedtra'] = $this->cedrep;
                $empresaSisuweb['tipo'] = $request->getParam('tipo');
                $empresaSisuweb['usuario'] = $request->getParam('usuario');
                $empresaSisuweb['tipdoc'] = $request->getParam('tipdoc');

                $signupEntity->createSignupService($empresaSisuweb);
            } else {
                $signupEntity->createSignupService(
                    [
                        'coddoc' => $this->coddoc,
                        'documento' => $documentoSolicitud,
                        'cedrep' => $this->cedrep,
                        'cedtra' => $this->cedrep,
                        'tipo' => $request->getParam('tipo'),
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
                        'nit' => $request->getParam('nit'),
                    ]
                );
            }
        } else {
            throw new DebugException('Error la cuenta ya está registrada, y dispone de una solicitud en estado temporal.', 1);
        }
        $solicitud = $signupEntity->getSolicitud();
        $solicitud->save();

        return $solicitud;
    }

    /**
     * buscaEmpresaSisu function
     *
     * @param  int  $nit
     * @return object
     */
    public function buscaEmpresaSisu($nit)
    {
        $ps = Comman::Api();
        $ps->runCli(
            [
                'servicio' => 'ComfacaEmpresas',
                'metodo' => 'informacion_empresa',
                'params' => [
                    'nit' => $nit,
                ],
            ]
        );
        if ($ps->isJson() == false) {
            return false;
        }
        $out = $ps->toArray();
        if ($out['success'] == false) {
            return false;
        }

        return $out['data'];
    }

    public function autoFirma($documento, $coddoc, $password)
    {
        $gestionFirmas = new GestionFirmaNoImage(
            [
                'documento' => $documento,
                'coddoc' => $coddoc,
                'password' => $password,
            ]
        );
        if ($gestionFirmas->hasFirma() == false) {
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

    public function validaTrabajadorEmpresa()
    {
        $ps = Comman::Api();
        $ps->runCli(
            [
                'servicio' => 'ComfacaEmpresas',
                'metodo' => 'informacion_empresa',
                'params' => [
                    'nit' => $this->nit,
                ],
            ]
        );
        if ($ps->isJson() == false) {
            return false;
        }
        $out = $ps->toArray();
        if ($out['success'] == false) {
            return false;
        }

        $ps->runCli(
            [
                'servicio' => 'ComfacaEmpresas',
                'metodo' => 'informacion_trabajador',
                'params' => [
                    'cedtra' => $this->cedrep,
                ],
            ]
        );
        if ($ps->isJson() == false) {
            return false;
        }
        $out = $ps->toArray();
        if ($out['success'] == false) {
            return false;
        }

        return ($out['data']['nit'] == $this->nit) ? $out['data'] : false;
    }
}
