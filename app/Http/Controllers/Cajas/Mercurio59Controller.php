<?php

namespace App\Http\Controllers\Cajas;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use App\Models\Mercurio01;
use App\Models\Mercurio59;
use App\Services\Utils\GeneralService;
use App\Services\Utils\Paginate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class Mercurio59Controller extends ApplicationController
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
        return view('cajas.mercurio59._table', [
            'paginate' => $paginate,
        ]);
    }

    public function aplicarFiltro(Request $request)
    {
        $consultasOldServices = new GeneralService;
        $this->query = $consultasOldServices->converQuery($request);

        return $this->buscar($request, $request->input('codinf'));
    }

    public function changeCantidadPagina(Request $request)
    {
        $this->cantidad_pagina = $request->input('numero');

        return $this->buscar($request, $request->input('codinf'));
    }

    public function index($codinf = '')
    {
        $campo_field = [
            'codser' => 'Servicio',
            'email' => 'Email',
        ];
        $consultasOldServices = new GeneralService;
        $codser = $consultasOldServices->webService('servicios', []);
        $_codser = ['' => 'Seleccione un servicio...'];
        if (isset($codser['data']) && is_array($codser['data'])) {
            foreach ($codser['data'] as $data) {
                $_codser[$data['codser']] = $data['detalle'];
            }
        }

        return view('cajas.mercurio59.index', [
            'title' => 'Servicios',
            'campo_filtro' => $campo_field,
            'codinf' => $codinf,
            '_codser' => $_codser,
        ]);
    }

    public function traerApertura(Request $request)
    {
        try {
            $this->setResponse('ajax');
            $codser = $request->input('codser');
            $consultasOldServices = new GeneralService;
            $servi29 = $consultasOldServices->webService('aperturas_servicios', ['codser' => $codser]);
            $_servi29 = [];
            if (isset($servi29['data']) && is_array($servi29['data'])) {
                foreach ($servi29['data'] as $data) {
                    $_servi29[$data['numero']] = $data['detalle'];
                }
            }

            return $this->renderObject($_servi29, false);
        } catch (DebugException $e) {
            $response = parent::errorFunc('No se pudo obtener las aperturas');

            return $this->renderObject($response, false);
        }
    }

    public function buscar(Request $request, $codinf)
    {
        $pagina = ($request->input('pagina') == '') ? 1 : $request->input('pagina');

        $paginate = Paginate::execute(
            Mercurio59::where('codinf', $codinf)->whereRaw("{$this->query}")->get(),
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

    public function editar(Request $request)
    {
        try {
            $this->setResponse('ajax');
            $codinf = $request->input('codinf');
            $codser = $request->input('codser');
            $numero = $request->input('numero');
            $mercurio59 = Mercurio59::where('codinf', $codinf)
                ->where('codser', $codser)
                ->where('numero', $numero)
                ->first();

            if ($mercurio59 == false) {
                $mercurio59 = new Mercurio59;
            }

            return $this->renderObject($mercurio59->toArray(), false);
        } catch (DebugException $e) {
            $response = parent::errorFunc('Error al obtener el registro');

            return $this->renderObject($response, false);
        }
    }

    public function borrar(Request $request)
    {
        try {
            $this->setResponse('ajax');
            $codinf = $request->input('codinf');
            $codser = $request->input('codser');
            $numero = $request->input('numero');

            $this->db->begin();
            $mercurio59 = Mercurio59::where('codinf', $codinf)
                ->where('codser', $codser)
                ->where('numero', $numero)
                ->first();

            if ($mercurio59) {
                $archivo = $mercurio59->archivo;
                $mercurio01 = Mercurio01::first();
                if ($mercurio01 && ! empty($archivo)) {
                    $filePath = public_path($mercurio01->getPath() . 'galeria/' . $archivo);
                    if (File::exists($filePath)) {
                        File::delete($filePath);
                    }
                }
                $mercurio59->delete();
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

    public function guardar(Request $request)
    {
        try {
            $this->setResponse('ajax');
            $codinf = $request->input('codinf');
            $codser = $request->input('codser');
            $numero = $request->input('numero');
            $nota = $request->input('nota');
            $email = $request->input('email');
            $precan = $request->input('precan');
            $autser = $request->input('autser');
            $consumo = $request->input('consumo');
            $estado = $request->input('estado');

            $this->db->begin();
            $mercurio59 = Mercurio59::firstOrNew([
                'codinf' => $codinf,
                'codser' => $codser,
                'numero' => $numero,
            ]);

            $mercurio59->nota = $nota;
            $mercurio59->email = $email;
            $mercurio59->precan = $precan;
            $mercurio59->autser = $autser;
            $mercurio59->consumo = $consumo;
            $mercurio59->estado = $estado;

            $mercurio01 = Mercurio01::first();
            if (! $mercurio01) {
                throw new DebugException('Configuración básica no encontrada.');
            }

            if ($request->hasFile('archivo') && $request->file('archivo')->isValid()) {
                $file = $request->file('archivo');
                $extension = $file->getClientOriginalExtension();
                $fileName = $codinf . $codser . '_infraservi.' . $extension;
                $destinationPath = public_path($mercurio01->getPath() . 'galeria');

                if ($mercurio59->exists && ! empty($mercurio59->archivo)) {
                    $oldFilePath = $destinationPath . '/' . $mercurio59->archivo;
                    if (File::exists($oldFilePath)) {
                        File::delete($oldFilePath);
                    }
                }

                $file->move($destinationPath, $fileName);
                $mercurio59->archivo = $fileName;
            }

            if (! $mercurio59->save()) {
                parent::setLogger($mercurio59->getMessages());
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

    public function validePk(Request $request)
    {
        try {
            $this->setResponse('ajax');
            $codinf = $request->input('codinf');
            $codser = $request->input('codser');
            $numero = $request->input('numero');
            $response = parent::successFunc('');
            $exists = Mercurio59::where('codinf', $codinf)
                ->where('codser', $codser)
                ->where('numero', $numero)
                ->exists();
            if ($exists) {
                $response = parent::errorFunc('El Registro ya se encuentra Digitado');
            }

            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $response = parent::errorFunc('No se pudo validar la informacion');

            return $this->renderObject($response, false);
        }
    }

    public function reporte($format = 'P')
    {
        $this->setResponse('ajax');
        $_fields = [];
        $_fields['codinf'] = ['header' => 'Codigo', 'size' => '15', 'align' => 'C'];
        $_fields['codser'] = ['header' => 'Servicio', 'size' => '15', 'align' => 'C'];
        $_fields['numero'] = ['header' => 'Numero', 'size' => '15', 'align' => 'C'];
        $_fields['nota'] = ['header' => 'Nota', 'size' => '31', 'align' => 'C'];
        $_fields['email'] = ['header' => 'Email', 'size' => '31', 'align' => 'C'];
        $_fields['precan'] = ['header' => 'Presenta Cantidad', 'size' => '31', 'align' => 'C'];
        $_fields['autser'] = ['header' => 'Automatico Servicio', 'size' => '31', 'align' => 'C'];
        $_fields['estado'] = ['header' => 'Estado', 'size' => '31', 'align' => 'C'];
        $_fields['archivo'] = ['header' => 'Archivo', 'size' => '31', 'align' => 'C'];
        $consultasOldServices = new GeneralService;
        $file = $consultasOldServices->createReport('mercurio59', $_fields, $this->query, 'Servicios', $format);

        return $this->renderObject($file, false);
    }
}
