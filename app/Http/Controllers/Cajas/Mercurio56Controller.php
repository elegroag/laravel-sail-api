<?php

namespace App\Http\Controllers\Cajas;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use App\Models\Mercurio01;
use App\Models\Mercurio56;
use App\Services\Utils\GeneralService;
use App\Services\Utils\Paginate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class Mercurio56Controller extends ApplicationController
{
    protected $query = '1=1';

    protected $cantidad_pagina = 10;

    protected $db;

    protected $user;

    protected $tipo;

    public function __construct()
    {
        $this->db = DbBase::rawConnect();
        $this->user = session()->has('user') ? session('user') : null;
        $this->tipo = session()->has('tipo') ? session('tipo') : null;
    }

    public function showTabla($paginate)
    {
        return view('cajas.mercurio56._table', [
            'paginate' => $paginate,
        ]);
    }

    public function aplicarFiltroAction(Request $request)
    {
        $consultasOldServices = new GeneralService;
        $this->query = $consultasOldServices->converQuery($request);

        return $this->buscarAction($request);
    }

    public function changeCantidadPaginaAction(Request $request)
    {
        $this->cantidad_pagina = $request->input('numero');

        return $this->buscarAction($request);
    }

    public function indexAction()
    {
        $campo_field = [
            'codinf' => 'Codigo',
            'estado' => 'Estado',
        ];
        $consultasOldServices = new GeneralService;
        $infraestructura = $consultasOldServices->webService('infraestructuras', []);
        $_infraestructura = [];
        if (isset($infraestructura['data']) && is_array($infraestructura['data'])) {
            foreach ($infraestructura['data'] as $data) {
                $_infraestructura[$data['codinf']] = $data['nomcom'];
            }
        }

        return view('cajas.mercurio56.index', [
            'title' => 'Infraestructura',
            'campo_filtro' => $campo_field,
            '_infraestructura' => $_infraestructura,
            'estados_array' => (new Mercurio56)->getEstadoArray()
        ]);
    }

    public function buscarAction(Request $request)
    {
        $pagina = ($request->input('pagina') == '') ? 1 : $request->input('pagina');

        $paginate = Paginate::execute(
            Mercurio56::whereRaw("{$this->query}")->get(),
            $pagina,
            $this->cantidad_pagina
        );

        $html = $this->showTabla($paginate);
        $consultasOldServices = new GeneralService;
        $html_paginate = $consultasOldServices->showPaginate($paginate);

        $response['consulta'] = $html;
        $response['paginate'] = $html_paginate;

        return $this->renderObject($response, false);
    }

    public function editarAction(Request $request)
    {
        try {
            $this->setResponse('ajax');
            $codinf = $request->input('codinf');
            $mercurio56 = Mercurio56::where('codinf', $codinf)->first();
            if ($mercurio56 == false) {
                $mercurio56 = new Mercurio56;
            }

            return $this->renderObject($mercurio56->toArray(), false);
        } catch (DebugException $e) {
            $response = parent::errorFunc('Error al obtener el registro');

            return $this->renderObject($response, false);
        }
    }

    public function borrarAction(Request $request)
    {
        try {
            $this->setResponse('ajax');
            $codinf = $request->input('codinf');

            $this->db->begin();
            $mercurio56 = Mercurio56::where('codinf', $codinf)->first();

            if ($mercurio56) {
                $archivo = $mercurio56->archivo;
                $mercurio01 = Mercurio01::first();
                if ($mercurio01 && ! empty($archivo)) {
                    $filePath = public_path($mercurio01->getPath() . 'galeria/' . $archivo);
                    if (File::exists($filePath)) {
                        File::delete($filePath);
                    }
                }
                $mercurio56->delete();
            } else {
                throw new DebugException('El registro a borrar no existe.');
            }

            $this->db->commit();
            $response = parent::successFunc('Borrado Con Exito');

            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $this->db->rollback();
            $response = parent::errorFunc('No se puede Borrar el Registro: ' . $e->getMessage());

            return $this->renderObject($response, false);
        }
    }

    public function guardarAction(Request $request)
    {
        try {
            $this->setResponse('ajax');
            $codinf = $request->input('codinf');
            $email = $request->input('email');
            $telefono = $request->input('telefono');
            $nota = $request->input('nota');
            $estado = $request->input('estado');

            $this->db->begin();
            $mercurio56 = Mercurio56::firstOrNew(['codinf' => $codinf]);

            $mercurio56->email = $email;
            $mercurio56->telefono = $telefono;
            $mercurio56->nota = $nota;
            $mercurio56->estado = $estado;

            $mercurio01 = Mercurio01::first();
            if (! $mercurio01) {
                throw new DebugException('Configuración básica no encontrada.');
            }

            if ($request->hasFile('archivo') && $request->file('archivo')->isValid()) {
                $file = $request->file('archivo');
                $extension = $file->getClientOriginalExtension();
                $fileName = $codinf . '_infracestructura.' . $extension;
                $destinationPath = public_path($mercurio01->getPath() . 'galeria');

                // Delete old file if it exists
                if ($mercurio56->exists && ! empty($mercurio56->archivo)) {
                    $oldFilePath = $destinationPath . '/' . $mercurio56->archivo;
                    if (File::exists($oldFilePath)) {
                        File::delete($oldFilePath);
                    }
                }

                $file->move($destinationPath, $fileName);
                $mercurio56->archivo = $fileName;
            }

            if (! $mercurio56->save()) {
                parent::setLogger($mercurio56->getMessages());
                $this->db->rollback();
                throw new DebugException('Error al guardar el registro');
            }

            $this->db->commit();
            $response = parent::successFunc('Creacion Con Exito');

            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $this->db->rollback();
            $response = parent::errorFunc('No se puede guardar/editar el Registro: ' . $e->getMessage());

            return $this->renderObject($response, false);
        }
    }

    public function validePkAction(Request $request)
    {
        try {
            $this->setResponse('ajax');
            $codinf = $request->input('codinf');
            $response = parent::successFunc('');
            $exists = Mercurio56::where('codinf', $codinf)->exists();
            if ($exists) {
                $response = parent::errorFunc('El Registro ya se encuentra Digitado');
            }

            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $response = parent::errorFunc('No se pudo validar la informacion');

            return $this->renderObject($response, false);
        }
    }

    public function reporteAction($format = 'P')
    {
        $this->setResponse('ajax');
        $_fields = [];
        $_fields['codinf'] = ['header' => 'Codigo', 'size' => '15', 'align' => 'C'];
        $_fields['email'] = ['header' => 'Email', 'size' => '31', 'align' => 'C'];
        $_fields['telefono'] = ['header' => 'Telefono', 'size' => '31', 'align' => 'C'];
        $_fields['nota'] = ['header' => 'Nota', 'size' => '31', 'align' => 'C'];
        $_fields['estado'] = ['header' => 'Estado', 'size' => '31', 'align' => 'C'];
        $_fields['archivo'] = ['header' => 'Archivo', 'size' => '31', 'align' => 'C'];
        $consultasOldServices = new GeneralService;
        $file = $consultasOldServices->createReport('mercurio56', $_fields, $this->query, 'Infraestructura', $format);

        return $this->renderObject($file, false);
    }
}
