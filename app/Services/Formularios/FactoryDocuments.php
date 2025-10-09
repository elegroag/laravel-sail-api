<?php

namespace App\Services\Formularios;

use App\Exceptions\DebugException;
use App\Services\Formularios\Afiliacion\FormularioActualizadatos;
use App\Services\Formularios\Afiliacion\FormularioBeneficiario;
use App\Services\Formularios\Afiliacion\FormularioConyuge;
use App\Services\Formularios\Afiliacion\FormularioEmpresa;
use App\Services\Formularios\Afiliacion\FormularioFacultativo;
use App\Services\Formularios\Afiliacion\FormularioIndependiente;
use App\Services\Formularios\Afiliacion\FormularioPensionado;
use App\Services\Formularios\Afiliacion\FormularioTrabajador;
use App\Services\Formularios\Declaration\JuramentadaBeneficiario;
use App\Services\Formularios\Declaration\JuramentadaConyuge;
use App\Services\Formularios\Oficios\SolicitudEmpresa;
use App\Services\Formularios\Oficios\SolicitudFacultativo;
use App\Services\Formularios\Oficios\SolicitudIndependiente;
use App\Services\Formularios\Oficios\SolicitudPensionado;
use App\Services\Formularios\Oficios\TrabajadoresNominaEmpresa;
use App\Services\Formularios\Politica\EmpresaDatosPersonales;
use App\Services\Formularios\Politica\FacultativoDatosPersonales;
use App\Services\Formularios\Politica\IndependienteDatoPersonales;
use App\Services\Formularios\Politica\PensionadoDatosPersonales;
use App\Services\Formularios\Politica\TrabajadorDatosPersonales;

class FactoryDocuments
{
    public function crearFormulario($tipo)
    {
        switch ($tipo) {
            case 'trabajador':
                return new FormularioTrabajador;
                break;
            case 'pensionado':
                return new FormularioPensionado;
                break;
            case 'facultativo':
                return new FormularioFacultativo;
                break;
            case 'actualizadatos':
                return new FormularioActualizadatos;
                break;
            case 'independiente':
                return new FormularioIndependiente;
                break;
            case 'empresa':
                return new FormularioEmpresa;
                break;
            case 'conyuge':
                return new FormularioConyuge;
                break;
            case 'beneficiario':
                return new FormularioBeneficiario;
                break;
            default:
                throw new DebugException("Tipo de documento no soportado {$tipo} ".__METHOD__);
                break;
        }
    }

    public function crearOficio($tipo)
    {
        switch ($tipo) {
            case 'pensionado':
                return new SolicitudPensionado;
                break;
            case 'facultativo':
                return new SolicitudFacultativo;
                break;
            case 'independiente':
                return new SolicitudIndependiente;
                break;
            case 'empresa':
                return new SolicitudEmpresa;
                break;
            case 'trabajador_nomina':
                return new TrabajadoresNominaEmpresa;
                break;
            default:
                throw new DebugException("Tipo de documento no soportado {$tipo} ".__METHOD__);
                break;
        }
    }

    public function crearPolitica($tipo)
    {
        switch ($tipo) {
            case 'trabajador':
                return new TrabajadorDatosPersonales;
                break;
            case 'empresa':
                return new EmpresaDatosPersonales;
                break;
            case 'independiente':
                return new IndependienteDatoPersonales;
                break;
            case 'pensionado':
                return new PensionadoDatosPersonales;
                break;
            case 'facultativo':
                return new FacultativoDatosPersonales;
                break;
            default:
                throw new DebugException("Tipo de documento no soportado {$tipo} ".__METHOD__);
                break;
        }
    }

    public function crearDeclaracion($tipo)
    {
        switch ($tipo) {
            case 'conyuge':
                return new JuramentadaConyuge;
                break;
            case 'beneficiario':
                return new JuramentadaBeneficiario;
                break;
            default:
                throw new DebugException("Tipo de documento no soportado {$tipo} ".__METHOD__);
                break;
        }
    }
}
