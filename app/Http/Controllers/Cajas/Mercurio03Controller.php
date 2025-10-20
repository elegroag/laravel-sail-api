<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use App\Models\Mercurio01;
use App\Models\Mercurio03;
use App\Services\Utils\GeneralService;
use App\Services\Utils\Paginate;
use App\Services\Utils\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Mercurio03Controller extends ApplicationController
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
        $table = new Table;
        $table->set_template(Table::TmpGeneral());
        $table->set_heading(
            'OPT',
            'Codigo',
            'Nombre',
            'Cargo',
            'Archivo',
            'Email'
        );

        if (count($paginate->items) > 0) {
            foreach ($paginate->items as $mtable) {
                $table->add_row(
                    "<a href='#!' class='table-action btn btn-xs btn-primary' title='Editar' data-cid='{$mtable->getCodfir()}' data-toggle='editar'>
                        <i class='fas fa-user-edit text-white'></i>
                    </a>
                    <a href='#!' class='table-action table-action-delete btn btn-xs btn-danger' title='Borrar' data-cid='{$mtable->getCodfir()}' data-toggle='borrar'>
                        <i class='fas fa-trash text-white'></i>
                    </a>",
                    $mtable->getCodfir(),
                    $mtable->getNombre(),
                    $mtable->getCargo(),
                    $mtable->getArchivo(),
                    $mtable->getEmail()
                );
            }
        } else {
            $table->add_row('');
            $table->set_empty("<tr><td colspan='6'> &nbsp; No hay registros que mostrar</td></tr>");
        }

        return $table->generate();
    }

    public function aplicarFiltro(Request $request)
    {
        $consultasOldServices = new GeneralService;
        $this->query = $consultasOldServices->converQuery($request);

        return $this->buscar($request);
    }

    public function changeCantidadPagina(Request $request)
    {
        $this->cantidad_pagina = $request->input('numero');

        return $this->buscar($request);
    }

    public function index()
    {
        $campo_field = [
            'codfir' => 'Código',
            'nombre' => 'Nombre',
            'cargo' => 'Cargo',
            'email' => 'Email',
        ];

        return view('cajas.mercurio03.index', [
            'title' => 'Gestión de Firmas',
            'campo_filtro' => $campo_field,
        ]);
    }

    public function buscar(Request $request)
    {
        try {
            $pagina = ($request->input('pagina') == '') ? 1 : $request->input('pagina');
            $query = Mercurio03::whereRaw($this->query);

            $paginate = Paginate::execute(
                $query->get(),
                $pagina,
                $this->cantidad_pagina
            );

            $html = $this->showTabla($paginate);
            $consultasOldServices = new GeneralService;
            $html_paginate = $consultasOldServices->showPaginate($paginate);

            $response['consulta'] = $html;
            $response['paginate'] = $html_paginate;

            return $this->renderObject($response, false);
        } catch (\Exception $e) {
            return $this->renderObject([
                'success' => false,
                'message' => $e->getMessage(),
            ], false);
        }
    }

    public function editar(Request $request)
    {
        try {
            $codfir = $request->input('codfir');
            $mercurio03 = Mercurio03::where('codfir', $codfir)->first();

            if (! $mercurio03) {
                $mercurio03 = new Mercurio03;
                $mercurio03->setCodfir($codfir);
            }

            return $this->renderObject($mercurio03->toArray(), false);
        } catch (\Exception $e) {
            parent::setLogger($e->getMessage());
            $response = parent::errorFunc('Error al obtener el registro: ' . $e->getMessage());

            return $this->renderObject($response, false);
        }
    }

    public function borrar(Request $request)
    {
        try {
            $codfir = $request->input('codfir');

            DB::beginTransaction();

            $mercurio03 = Mercurio03::where('codfir', $codfir)->first();

            if ($mercurio03) {
                // Eliminar archivo físico si existe
                $mercurio01 = Mercurio01::first();
                if ($mercurio01 && $mercurio03->getArchivo() && file_exists($mercurio01->getPath() . $mercurio03->getArchivo())) {
                    unlink($mercurio01->getPath() . $mercurio03->getArchivo());
                }

                if (! $mercurio03->delete()) {
                    throw new \Exception('No se pudo eliminar el registro');
                }

                DB::commit();
                $response = parent::successFunc('Registro eliminado correctamente');
            } else {
                $response = parent::errorFunc('El registro no existe');
            }

            return $this->renderObject($response, false);
        } catch (\Exception $e) {
            DB::rollBack();
            parent::setLogger($e->getMessage());
            $response = parent::errorFunc('Error al eliminar el registro: ' . $e->getMessage());

            return $this->renderObject($response, false);
        }
    }

    public function guardar(Request $request)
    {
        try {
            $codfir = $request->input('codfir');
            $nombre = $request->input('nombre');
            $cargo = $request->input('cargo');
            $email = $request->input('email');

            DB::beginTransaction();

            // Buscar o crear una nueva instancia
            $mercurio03 = Mercurio03::where('codfir', $codfir)->first();
            $isNew = ! $mercurio03;

            if ($isNew) {
                $mercurio03 = new Mercurio03;
                $mercurio03->setCodfir($codfir);
            }

            // Actualizar campos básicos
            $mercurio03->setNombre($nombre);
            $mercurio03->setCargo($cargo);
            $mercurio03->setEmail($email);

            // Manejo de archivo
            if ($request->hasFile('archivo') && $request->file('archivo')->isValid()) {
                $mercurio01 = Mercurio01::first();

                if (! $mercurio01 || ! $mercurio01->getPath()) {
                    throw new \Exception('No se ha configurado la ruta para guardar archivos');
                }

                // Eliminar archivo anterior si existe
                if (! $isNew && $mercurio03->getArchivo() && file_exists($mercurio01->getPath() . $mercurio03->getArchivo())) {
                    unlink($mercurio01->getPath() . $mercurio03->getArchivo());
                }

                $file = $request->file('archivo');
                $extension = $file->getClientOriginalExtension();
                $fileName = $codfir . '_firma.' . $extension;

                // Mover el archivo
                $file->move($mercurio01->getPath(), $fileName);
                $mercurio03->setArchivo($fileName);
            }

            // Guardar el registro
            if (! $mercurio03->save()) {
                throw new \Exception('Error al guardar el registro');
            }

            DB::commit();
            $response = parent::successFunc($isNew ? 'Registro creado correctamente' : 'Registro actualizado correctamente');

            return $this->renderObject($response, false);
        } catch (\Exception $e) {
            DB::rollBack();
            parent::setLogger($e->getMessage());
            $response = parent::errorFunc('Error al guardar el registro: ' . $e->getMessage());

            return $this->renderObject($response, false);
        }
    }

    public function validePk(Request $request)
    {
        try {
            $codfir = $request->input('codfir');
            $exists = Mercurio03::where('codfir', $codfir)->exists();

            if ($exists) {
                $response = parent::errorFunc('El código ya existe');
            } else {
                $response = parent::successFunc('');
            }

            return $this->renderObject($response, false);
        } catch (\Exception $e) {
            parent::setLogger($e->getMessage());
            $response = parent::errorFunc('Error al validar el código: ' . $e->getMessage());

            return $this->renderObject($response, false);
        }
    }

    public function reporte($format = 'P')
    {
        try {
            $_fields = [
                'codfir' => ['header' => 'Código', 'size' => '15', 'align' => 'C'],
                'nombre' => ['header' => 'Nombre', 'size' => '31', 'align' => 'L'],
                'cargo' => ['header' => 'Cargo', 'size' => '31', 'align' => 'L'],
                'archivo' => ['header' => 'Archivo', 'size' => '31', 'align' => 'L'],
                'email' => ['header' => 'Email', 'size' => '31', 'align' => 'L'],
            ];

            $consultasOldServices = new GeneralService;
            $file = $consultasOldServices->createReport(
                'mercurio03',
                $_fields,
                $this->query,
                'Reporte de Firmas',
                $format
            );

            return $this->renderObject($file, false);
        } catch (\Exception $e) {
            parent::setLogger($e->getMessage());
            $response = parent::errorFunc('Error al generar el reporte: ' . $e->getMessage());

            return $this->renderObject($response, false);
        }
    }
}
