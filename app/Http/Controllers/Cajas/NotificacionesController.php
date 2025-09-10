<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class NotificacionesController extends ApplicationController
{
    public function __construct()
    {
       
        
        $this->setParamToView("instancePath", env('APP_URL') . 'Cajas/');
        
    }

    public function beforeFilter($permisos = array())
    {
        parent::beforeFilter($permisos);
        if (!Auth::getActiveIdentity()) {
            set_flashdata("error", array(
                "message" => "No dispone de acceso a la ruta indicada principal.",
                "code" => 404
            ));
            if (is_ajax()) {
                return redirect('login/error_access_rest');
            } else {
                return redirect('login/error_access');
            }
            exit;
        }
    }

    public function refreshAction()
    {
        $this->setResponse("ajax");
        try {
            $notificacionService = new NotificacionService();
            $usuario = parent::getActUser();
            $notificaciones = $notificacionService->getNotificacionesByUser($usuario);
            $num = $this->Notificaciones->count("*", "conditions: user='{$usuario}' AND estado='P'");
            $salida = array(
                'success' => true,
                'msj' => "Proceso de consulta exitoso de las notificaciones",
                "data" => $notificaciones,
                'badgenum' => ($num) ? $num : 0
            );
        } catch (DebugException $e) {
            $salida = array(
                'success' => false,
                'msj' => $e->getMessage()
            );
        }
        return $this->renderObject($salida);
    }

    public function refresh_paginationAction()
    {
        $this->setResponse("ajax");
        try {
            $pagina = ($request->input('pagina')) ? $request->input('pagina') : 1;
            $porPagina = ($request->input('porPagina')) ? $request->input('porPagina') : 10;

            $notificacionService = new NotificacionService();
            $usuario = parent::getActUser();
            $salida = $notificacionService->getPaginatedByUser($usuario, $pagina, $porPagina);
            $salida['success'] = true;
            $salida['msj'] = "Proceso de consulta exitoso de las notificaciones";
        } catch (DebugException $e) {
            $salida = array(
                'success' => false,
                'msj' => $e->getMessage()
            );
        }
        return $this->renderObject($salida);
    }

    public function indexAction()
    {
        $this->setParamToView("title", "Notificaciones");
    }

    public function detalleAction($id = '')
    {
        if ($id == '') {
            set_flashdata("error", array(
                "message" => "No se ha seleccionado una notificación.",
                "code" => 404
            ));
            if (is_ajax()) {
                return redirect('login/error_access_rest');
            } else {
                return redirect('login/error_access');
            }
            exit;
        }
        $this->setParamToView("title", "Detalle Notificación");
        Tag::setDocumentTitle('Detalle Notificación');
    }

    public function createAction()
    {
        $this->setResponse("ajax");
        $html = "Se requiere de actualizar el correo electronico de la cuenta de usuario<br/>
		Correo electronico anterior:<br/>
		Correo electronico nuevo:<br/>
		Novedad: <br/>
		Telefono: <br/>";

        $notificacion = new NotificacionService();
        $notificacion->createNotificacion(
            array(
                'titulo' => 'Solicitud de cambio de correo',
                'descripcion' => $html,
                'user'   => parent::getActUser(),
            )
        );
        $notificaciones = $notificacion->getNotificacionesByUser(parent::getActUser());
        $data = array();
        foreach ($notificaciones as $notificacion) {
            $data[] = $notificacion->getArray();
        }
        $salida = array(
            'success' => true,
            'notificaciones' => $data,
            'msj' => "Proceso de creación exitoso de la notificación",
        );
        return $this->renderObject($salida, false);
    }

    public function change_stateAction()
    {
        $this->setResponse("ajax");
        try {
            $notificacion = (new Notificaciones)->findFirst(" id={$request->input('id')}");
            $notificacion->setEstado($request->input('estado'));
            $notificacion->save();
            $salida = array(
                'success' => true,
                'msj' => "Proceso de actualización exitoso de la notificación",
            );
        } catch (DebugException $e) {
            $salida = array(
                'success' => false,
                'msj' => $e->getMessage()
            );
        }
        return $this->renderObject($salida);
    }
}
