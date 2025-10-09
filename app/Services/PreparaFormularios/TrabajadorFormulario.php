<?php

namespace App\Services\PreparaFormularios;

use App\Exceptions\DebugException;
use App\Library\Collections\ParamsTrabajador;
use App\Models\Mercurio30;
use App\Models\Mercurio32;
use App\Models\Mercurio34;
use App\Services\Utils\Comman;

class TrabajadorFormulario
{
    private $documento;

    private $coddoc;

    public function __construct($argv)
    {
        $this->documento = $argv['documento'];
        $this->coddoc = $argv['coddoc'];
    }

    public function main($mercurio31)
    {
        $paramsTrabajador = new ParamsTrabajador;
        $pc = Comman::Api();
        $pc->runCli(1, [
            'servicio' => 'ComfacaAfilia',
            'metodo' => 'parametros_trabajadores',
        ], false);

        $datos_captura = $pc->toArray();
        $paramsTrabajador->setDatosCaptura($datos_captura);

        $pc = Comman::Api();
        $pc->runCli(
            [
                'servicio' => 'ComfacaEmpresas',
                'metodo' => 'informacion_empresa',
                'params' => [
                    'nit' => $mercurio31->getNit(),
                ],
            ]
        );

        $empresa = false;
        if ($out = $pc->toArray()) {
            $datos_empresa = ($out['success'] == true) ? $out['data'] : false;
            if ($datos_empresa) {

                $datos_empresa['telefono'] = ($datos_empresa['telr'] == '') ? $datos_empresa['telefono'] : $datos_empresa['telr'];
                $empresa = new Mercurio30($datos_empresa);
            }
        }

        if (! $empresa) {
            throw new DebugException('Error los datos de la empresa no estan disponibles', 505);
        }

        $pc = Comman::Api();
        $pc->runCli(
            [
                'servicio' => 'ComfacaAfilia',
                'metodo' => 'listar_conyuges_trabajador',
                'params' => [
                    'cedtra' => $mercurio31->getCedtra(),
                ],
            ]
        );
        $out = $pc->toArray();
        $trabajadorConyuges = ($out['success'] == true) ? $out['data'] : false;

        // solicitud en estado temporal
        $conyuge_comper = (new Mercurio32)->findFirst(" documento='{$this->documento}' and coddoc='{$this->coddoc}' and cedtra='{$mercurio31->getCedtra()}' and comper='S' and estado IN('T','P')");

        if ($conyuge_comper == false) {
            $data_conyuge = false;
            if ($trabajadorConyuges) {

                foreach ($trabajadorConyuges as $mconyuge) {
                    if ($mconyuge['comper'] == 'S') {
                        $data_conyuge = $mconyuge;
                        break;
                    }
                }

                if ($data_conyuge) {
                    $ps = Comman::Api();
                    $ps->runCli(
                        [
                            'servicio' => 'ComfacaEmpresas',
                            'metodo' => 'informacion_conyuge',
                            'params' => [
                                'cedcon' => $data_conyuge['cedcon'],
                            ],
                        ]
                    );

                    $out = $ps->toArray();
                    $data_conyuge = ($out['success'] == true) ? $out['data'] : false;
                    if ($data_conyuge) {
                        $conyuge_comper = new Mercurio32($data_conyuge);
                        $conyuge_comper->setTipdoc($data_conyuge['coddoc']);
                        $conyuge_comper->setCiures($data_conyuge['codzon']);
                    }
                }
            }
        }

        $conyuge_otra = false;
        $beneficiarios = (new Mercurio34)->getFind(" cedtra='{$mercurio31->getCedtra()}' and documento='{$this->documento}' and coddoc='{$this->coddoc}' and estado IN('T','P')");
        if ($beneficiarios) {
            foreach ($beneficiarios as $beneficiario) {
                if ($beneficiario->getCedcon() && $beneficiario->getParent() == '1') {

                    $data_other = false;
                    foreach ($trabajadorConyuges as $mconyuge) {
                        if ($mconyuge['cedcon'] == $beneficiario->getCedcon()) {
                            $data_other = $mconyuge;
                            break;
                        }
                    }
                    if ($data_other) {
                        $ps = Comman::Api();
                        $ps->runCli(
                            [
                                'servicio' => 'ComfacaEmpresas',
                                'metodo' => 'informacion_conyuge',
                                'params' => [
                                    'cedcon' => $beneficiario->getCedcon(),
                                ],
                            ]
                        );

                        $out = $ps->toArray();
                        if ($out['success'] == true) {
                            $data_other = $out['data'];
                            $conyuge_otra = new Mercurio32($data_other);
                            $conyuge_otra->tipdoc = $data_other['coddoc'];
                            $conyuge_otra->ciures = $data_other['codzon'];
                        } else {
                            $conyuge_otra = false;
                        }
                    }
                }
            }
        }

        return [
            'trabajador' => $mercurio31,
            'empresa' => $empresa,
            'conyuge_comper' => $conyuge_comper,
            'conyuge_otra' => $conyuge_otra,
            'beneficiarios' => $beneficiarios,
        ];
    }
}
