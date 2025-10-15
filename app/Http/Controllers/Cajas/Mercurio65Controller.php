<?php

namespace App\Http\Controllers\Cajas;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Mercurio65;
use App\Models\Mercurio67;
use App\Services\Utils\GeneralService;
use App\Services\Utils\Paginate;
use Illuminate\Http\Request;

class Mercurio65Controller extends ApplicationController
{
    protected $query = '1=1';

    protected $cantidad_pagina = 10;

    protected $db;

    protected $user;

    protected $tipo;

    public function __construct()
    {
        $this->user = session('user');
        $this->tipo = session('tipo');
    }

    public function showTabla($paginate)
    {
        return view('cajas.mercurio65._table', [
            'paginate' => $paginate,
        ]);
    }

    public function aplicarFiltroAction(Request $request)
    {
        $consultasOldServices = new GeneralService;
        $this->query = $consultasOldServices->converQuery($request);

        return $this->buscarAction($request);
    }

    public function changeCantidadPagina(Request $request)
    {
        $this->cantidad_pagina = $request->input('numero');

        return $this->buscarAction($request);
    }

    public function indexAction()
    {
        $campo_field = [
            'nit' => 'Nit',
            'razsoc' => 'Razón Social',
            'estado' => 'Estado',
        ];


        return view('cajas.mercurio65.index', [
            'title' => 'Comercios',
            'campo_filtro' => $campo_field,
            'mercurio67' => Mercurio67::all(),
            'estado_array' => (new Mercurio65)->getEstadoArray(),
        ]);
    }

    public function buscarAction(Request $request)
    {
        $pagina = ($request->input('pagina') == '') ? 1 : $request->input('pagina');
        $query = Mercurio65::whereRaw($this->query);

        $paginate = Paginate::execute(
            $query->get(),
            $pagina,
            $this->cantidad_pagina
        );

        $html = $this->showTabla($paginate);

        $consultasOldServices = new GeneralService;
        $html_paginate = $consultasOldServices->showPaginate($paginate);

        return $this->renderObject([
            'consulta' => $html,
            'paginate' => $html_paginate,
        ], false);
    }

    public function editarAction()
    {
        $mercurio65 = Mercurio65::first();
        if (! $mercurio65) {
            $mercurio65 = new Mercurio65;
        }

        return $this->renderObject($mercurio65->toArray(), false);
    }

    public function borrarAction(Request $request)
    {
        try {
            $codsed = $request->input('codsed');

            $deleted = Mercurio65::where('codsed', $codsed)->delete();

            if ($deleted) {
                $response = parent::successFunc('Borrado con éxito');
            } else {
                $response = parent::errorFunc('No se pudo eliminar el registro');
            }

            return $this->renderObject($response, false);
        } catch (\Exception $e) {
            parent::setLogger($e->getMessage());
            $response = parent::errorFunc('Error al intentar eliminar el registro');

            return $this->renderObject($response, false);
        }
    }

    public function guardarAction(Request $request)
    {
        try {
            $this->setResponse('ajax');

            $validatedData = $request->validate([
                'codsed' => 'required|string|max:50',
                'nit' => 'required|string|max:20',
                'razsoc' => 'required|string|max:255',
                'direccion' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:100',
                'celular' => 'nullable|string|max:20',
                'codcla' => 'nullable|string|max:10',
                'detalle' => 'nullable|string',
                'estado' => 'required|string|max:1',
                'lat' => 'nullable|numeric',
                'log' => 'nullable|numeric',
            ]);

            $response = $this->db->begin();

            // Buscar o crear un nuevo registro
            $mercurio65 = Mercurio65::firstOrNew(['codsed' => $request->input('codsed')]);

            // Actualizar atributos
            $mercurio65->fill($validatedData);

            // Manejo de archivo si existe
            if ($request->hasFile('archivo')) {
                $file = $request->file('archivo');
                $extension = $file->getClientOriginalExtension();
                $fileName = $request->input('codsed') . '_comercios.' . $extension;

                // Guardar el archivo usando el almacenamiento de Laravel
                $path = $file->storeAs('uploads', $fileName, 'public');
                $mercurio65->archivo = $fileName;
            }

            if (! $mercurio65->save()) {
                parent::setLogger($mercurio65->getMessages());
                $this->db->rollback();
                $response = parent::errorFunc('Error al guardar los datos');
            } else {
                $this->db->commit();
                $response = parent::successFunc('Datos guardados correctamente');
            }

            return $this->renderObject($response, false);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $response = parent::errorFunc('Error de validación: ' . $e->getMessage());

            return $this->renderObject($response, false);
        } catch (\Exception $e) {
            if (isset($this->db)) {
                $this->db->rollback();
            }
            parent::setLogger($e->getMessage());
            $response = parent::errorFunc('Error al procesar la solicitud: ' . $e->getMessage());

            return $this->renderObject($response, false);
        }
    }

    public function validePkAction(Request $request)
    {
        try {
            $nit = $request->input('nit');
            $response = '';
            $l = (new Mercurio65)->getCount('*', "conditions: nit = '$nit'");
            if ($l > 0) {
                $response = 'El Registro ya se encuentra Digitado';
            }
            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $response = 'No se pudo validar la informacion';
            return $this->renderObject($response, false);
        }
    }

    public function reporteAction($format = 'P')
    {
        $this->setResponse('ajax');
        $_fields = [];
        $_fields['codsed'] = ['header' => 'Codigo', 'size' => '15', 'align' => 'C'];
        $_fields['nit'] = ['header' => 'Nit', 'size' => '15', 'align' => 'C'];
        $_fields['razsoc'] = ['header' => 'Razon Social', 'size' => '15', 'align' => 'C'];
        $_fields['email'] = ['header' => 'Email', 'size' => '31', 'align' => 'C'];
        $_fields['celular'] = ['header' => 'Celular', 'size' => '31', 'align' => 'C'];
        $_fields['codcla'] = ['header' => 'Clasificacion', 'size' => '31', 'align' => 'C'];
        $_fields['detalle'] = ['header' => 'Detalle', 'size' => '31', 'align' => 'C'];
        $_fields['lat'] = ['header' => 'Latitud', 'size' => '31', 'align' => 'C'];
        $_fields['log'] = ['header' => 'Longitud', 'size' => '31', 'align' => 'C'];
        $_fields['estado'] = ['header' => 'Estado', 'size' => '31', 'align' => 'C'];
        $consultasOldServices = new GeneralService;
        $file = $consultasOldServices->createReport('mercurio65', $_fields, $this->query, 'Firmas', $format);

        return $this->renderObject($file, false);
    }
}
