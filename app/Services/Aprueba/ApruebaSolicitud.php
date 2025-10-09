<?php

namespace App\Services\Aprueba;

use App\Exceptions\DebugException;

class ApruebaSolicitud
{
    public function main($calemp, $idSolicitud, $postData)
    {
        /**
         * valida tipo empresa
         */
        switch ($calemp) {
            case 'E':
                $procesoAprobacion = new ApruebaEmpresa;
                break;
            case 'I':
                $procesoAprobacion = new ApruebaIndependiente;
                break;
            case 'P':
                $procesoAprobacion = new ApruebaPensionado;
                break;
            case 'F':
                $procesoAprobacion = new ApruebaFacultativo;
                break;
            case 'UE':
                $procesoAprobacion = new ApruebaDatosEmpresa;
                break;
            case 'T':
                $procesoAprobacion = new ApruebaTrabajador;
                break;
            case 'C':
                $procesoAprobacion = new ApruebaConyuge;
                break;
            default:
                throw new DebugException('La calidad de empresa no se puede identificar.', 501);
                break;
        }

        $solicitud = $procesoAprobacion->findSolicitud($idSolicitud);
        $solicitante = $procesoAprobacion->findSolicitante($solicitud);

        if ($solicitante == false) {
            throw new DebugException('El solicitante no está disponible para continuar el proceso.', 501);
        }

        /**
         * valida la ciudad de la empresa
         */
        if (intval($solicitud->getCodciu()) == 0) {
            throw new DebugException('Error código de ciudad es requerido para la aprobación de la afiliación.', 501);
        }

        /**
         * valida que tenga tipo documento el aportante
         */
        if (intval($solicitud->getTipdoc()) == 0) {
            throw new DebugException('Error el tipo de documento es requerido para la aprobación de la afiliación.', 501);
        }

        $procesoAprobacion->procesar($postData);

        return $procesoAprobacion;
    }
}
