<?php

namespace App\Http\Controllers\Mercurio;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use App\Models\Mercurio01;
use App\Models\Mercurio07;
use App\Services\Entidades\NotificacionService;
use App\Services\Utils\AsignarFuncionario;
use App\Services\Utils\SenderEmail;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class NotificacionesController extends ApplicationController
{
    protected $db;

    protected $user;

    protected $tipo;

    public function __construct()
    {
        $this->db = DbBase::rawConnect();
        $this->user = session('user') ?? null;
        $this->tipo = session('tipo') ?? null;
    }

    public function index()
    {
        return view('mercurio.notificaciones.index', [
            'hide_header' => true,
            'title' => 'Reportar errores y problemas',
            'documento' => $this->user['documento'],
            'codciu' => $this->user['codciu'],
            'tipo' => $this->tipo,
        ]);
    }

    public function procesarNotificacion(Request $request, Response $response)
    {
        try {
            $documento = $this->user['documento'];
            $servicio = $request->input('servicio');
            $telefono = $request->input('telefono');
            $nota = $request->input('nota');
            $novedad = $request->input('novedad');

            $solicitante = Mercurio07::where('documento', $documento)->first();
            $notificacion = new NotificacionService;

            $filepath = '';
            if (isset($_FILES['file'])) {
                $file = $_FILES['file'];
                $tmp_name = $file['tmp_name'];
                $name = basename($file['name']);

                $rename = $documento . '_' . date('YmdHis') . '_' . sanetizar($name);
                $filepath = storage_path("temp/{$rename}");

                if (file_exists($filepath)) {
                    unlink($filepath);
                }

                if (! move_uploaded_file($tmp_name, $filepath)) {
                    throw new DebugException('Error no es posible el cargue del archivo', 1);
                }
            }

            $novedades = $this->leerNovedades();
            $valor_novedad = $novedades["{$novedad}"];

            $servicios = $this->leerServicios();
            $valor_servicio = $servicios["{$servicio}"];

            $fecha = date('Y-m-d');
            $html = "
                <h4>Notificaciones para soporte técnico del sistema Comfaca En Línea</h4>
                <p>
                Documento: {$documento}<br/>
                Telefono: {$telefono}<br/>
                Email: {$solicitante->getEmail()}<br/>
                Nombre: {$solicitante->getNombre()}<br/>
                TIPO: {$solicitante->getTipo()}<br/>
                </p>
                <p>
                Fecha: {$fecha}<br/>
                Novedad: {$valor_novedad}<br/>
                Servicios: {$valor_servicio}<br/>
                </p>
                <b>Nota:</b> {$nota}
            ";
            $asunto = "[COMFACAONLINE][NOTA] notificación reportada {$documento}";

            $emailCaja = Mercurio01::first();

            $senderEmail = new SenderEmail;
            $senderEmail->setters(
                "emisor_email: {$emailCaja->getEmail()}",
                "emisor_clave: {$emailCaja->getClave()}",
                "asunto: {$asunto}"
            );

            $senderEmail->send(
                'soportesistemas.comfaca@gmail.com',
                $html,
                [$filepath]
            );

            $tipo = $solicitante->getTipo();
            if ($tipo == 'T') {
                $funcionario = (new AsignarFuncionario)->asignar('1', $solicitante->getCodciu());
            } else {
                $funcionario = (new AsignarFuncionario)->asignar('2', $solicitante->getCodciu());
            }
            $notificacion->createNotificacion(
                [
                    'titulo' => 'Feedback, se reporta una novedad en el sistema, para soporte',
                    'descripcion' => "Documento: {$documento}<br/>Email: {$solicitante->getEmail()}<br/>Nombre: {$solicitante->getNombre()}<br/>NOTA: $nota",
                    'user' => $funcionario,
                ]
            );

            $salida = [
                'msj' => 'Proceso se ha completado con éxito',
                'success' => true,

            ];
        } catch (\Throwable $e) {
            $salida = $this->handleException($e, $request);
        }

        return response()->json($salida);
    }

    public function leerServicios()
    {
        return [
            '' => 'Ninguno',
            '1' => 'Solicitud afiliación de empresas',
            '2' => 'Solicitud afiliación de trabajadores',
            '3' => 'Solicitud afiliación de conyuges',
            '4' => 'Solicitud afiliación de beneficiarios',
            '5' => 'Solicitud actualización de datos',
        ];
    }

    public function leerNovedades()
    {
        return [
            '' => 'Ninguno',
            '1' => 'Recomendación',
            '2' => 'Inconsistencia en la información',
            '3' => 'Error en al ingresar a una funcionalidad',
            '4' => 'Error en respuesta de un envío para validación',
            '5' => 'Error en los datos de la empresa',
            '6' => 'Error en los datos del trabajador',
            '7' => 'Error en los datos del conyuge',
            '8' => 'Error en los datos del beneficiario',
            '9' => 'Error en la solicitud actualización de datos',
        ];
    }
}
