<?php

namespace App\Http\Controllers\Cajas;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use Illuminate\Support\Facades\DB;
use App\Models\Mercurio01;
use App\Models\Mercurio26;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\ValidationException;

class Mercurio26Controller extends ApplicationController
{

    protected $user;

    protected $tipo;

    public function __construct()
    {
        $this->user = session()->has('user') ? session('user') : null;
        $this->tipo = session()->has('tipo') ? session('tipo') : null;
    }

    public function index()
    {
        return view('cajas.mercurio26.index', [
            'title' => 'Galería',
        ]);
    }

    public function galeria()
    {
        try {
            $mercurio01 = Mercurio01::first();
            if (! $mercurio01) {
                throw new DebugException('Configuración básica no encontrada.');
            }

            $path = $mercurio01->publicUrl('galeria');
            $galeria = Mercurio26::orderBy('orden', 'ASC')->get();

            $data = $galeria->map(function ($item) use ($path) {
                return [
                    'numero' => $item->numero,
                    'archivo' => $path.'/'.$item->archivo,
                    'tipo' => $item->tipo,
                    'nota' => $item->nota,
                ];
            });

            $response = parent::successFunc('Consulta exitosa');
            $response['data'] = $data;

            return $this->renderObject($response);
        } catch (DebugException $e) {
            return $this->renderObject(parent::errorFunc($e->getMessage()));
        } catch (\Throwable $e) {
            parent::setLogger($e->getMessage());

            return $this->renderObject(parent::errorFunc('No se pudo cargar la galería.'));
        }
    }

    public function guardar(Request $request)
    {
        try {
            $validated = $request->validate([
                'tipo' => 'required|in:F,V',
                'nota' => 'nullable|string|max:500',
                'archivo' => [
                    'required',
                    'file',
                    $request->input('tipo') === 'V'
                        ? 'mimetypes:video/mp4|max:20480'
                        : 'mimes:jpg,jpeg,png|max:5120',
                ],
            ]);

            DB::beginTransaction();

            $numero = (Mercurio26::max('numero') ?? 0) + 1;
            $orden = (Mercurio26::max('orden') ?? 0) + 1;
            $tipo = $validated['tipo'];

            $mercurio26 = new Mercurio26;
            $mercurio26->setNumero($numero);
            $mercurio26->setOrden($orden);
            $mercurio26->setTipo($tipo);
            $mercurio26->setEstado('A');
            $mercurio26->setNota($validated['nota'] ?? '');

            $mercurio01 = Mercurio01::first();
            if (! $mercurio01) {
                throw new DebugException('Configuración básica no encontrada.');
            }

            if (! $request->hasFile('archivo') || ! $request->file('archivo')->isValid()) {
                throw new DebugException('No se ha subido ningún archivo o el archivo no es válido.');
            }

            $file = $request->file('archivo');
            $extension = $file->getClientOriginalExtension();
            $fileName = 'promo_'.$numero.'.'.$extension;
            $destinationPath = public_path($mercurio01->getPath().'galeria');

            if (! File::isDirectory($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }

            $file->move($destinationPath, $fileName);
            $mercurio26->setArchivo($fileName);

            if (! $mercurio26->save()) {
                parent::setLogger($mercurio26->getMessages());
                DB::rollBack();
                throw new DebugException('Error al guardar el registro de la galería.');
            }

            DB::commit();

            return $this->renderObject(parent::successFunc('Creacion terminada Con Exito'));
        } catch (ValidationException $e) {
            $message = collect($e->errors())->flatten()->first() ?? 'Error de validación.';

            return $this->renderObject(parent::errorFunc($message));
        } catch (DebugException $e) {
            DB::rollBack();

            return $this->renderObject(parent::errorFunc('No se puede guardar el Registro: '.$e->getMessage()));
        } catch (\Throwable $e) {
            DB::rollBack();
            parent::setLogger($e->getMessage());

            return $this->renderObject(parent::errorFunc('No se puede guardar el Registro: '.$e->getMessage()));
        }
    }

    public function arriba(Request $request)
    {
        try {
            $numero = $request->input('numero');

            DB::beginTransaction();
            $objetivo = Mercurio26::where('numero', $numero)->first();
            if (! $objetivo) {
                throw new DebugException('Registro no encontrado.');
            }

            $orden_obj = $objetivo->getOrden();
            $minimo = Mercurio26::min('orden');

            if ($orden_obj != $minimo) {
                $superior = Mercurio26::where('orden', '<', $orden_obj)->orderBy('orden', 'desc')->first();
                if ($superior) {
                    $orden_sup = $superior->getOrden();
                    $objetivo->orden = $orden_sup;
                    $superior->orden = $orden_obj;
                    $objetivo->save();
                    $superior->save();
                }
            }
            DB::commit();

            return $this->renderObject(parent::successFunc('Ordenado Con Exito'));
        } catch (DebugException $e) {
            DB::rollBack();

            return $this->renderObject(parent::errorFunc('No se puede Ordenar el Registro: '.$e->getMessage()));
        } catch (\Throwable $e) {
            DB::rollBack();
            parent::setLogger($e->getMessage());

            return $this->renderObject(parent::errorFunc('No se puede Ordenar el Registro: '.$e->getMessage()));
        }
    }

    public function abajo(Request $request)
    {
        try {
            $numero = $request->input('numero');

            DB::beginTransaction();
            $objetivo = Mercurio26::where('numero', $numero)->first();
            if (! $objetivo) {
                throw new DebugException('Registro no encontrado.');
            }

            $orden_obj = $objetivo->getOrden();
            $maximo = Mercurio26::max('orden');

            if ($orden_obj != $maximo) {
                $inferior = Mercurio26::where('orden', '>', $orden_obj)->orderBy('orden', 'asc')->first();
                if ($inferior) {
                    $orden_inf = $inferior->getOrden();
                    $objetivo->orden = $orden_inf;
                    $inferior->orden = $orden_obj;
                    $objetivo->save();
                    $inferior->save();
                }
            }
            DB::commit();

            return $this->renderObject(parent::successFunc('Ordenado Con Exito'));
        } catch (DebugException $e) {
            DB::rollBack();

            return $this->renderObject(parent::errorFunc('No se puede Ordenar el Registro: '.$e->getMessage()));
        } catch (\Throwable $e) {
            DB::rollBack();
            parent::setLogger($e->getMessage());

            return $this->renderObject(parent::errorFunc('No se puede Ordenar el Registro: '.$e->getMessage()));
        }
    }

    public function borrar(Request $request)
    {
        try {
            $numero = $request->input('numero');

            DB::beginTransaction();
            $mercurio26 = Mercurio26::where('numero', $numero)->first();

            if (! $mercurio26) {
                throw new DebugException('El registro a borrar no existe.');
            }

            $archivo = $mercurio26->getArchivo();
            $mercurio01 = Mercurio01::first();

            if ($mercurio01 && ! empty($archivo)) {
                $filePath = public_path($mercurio01->getPath().'galeria/'.$archivo);
                if (File::exists($filePath)) {
                    File::delete($filePath);
                }
            }

            $mercurio26->delete();

            DB::commit();

            return $this->renderObject(parent::successFunc('Borrado Con Exito'));
        } catch (DebugException $e) {
            DB::rollBack();

            return $this->renderObject(parent::errorFunc('No se puede Borrar el Registro: '.$e->getMessage()));
        } catch (\Throwable $e) {
            DB::rollBack();
            parent::setLogger($e->getMessage());

            return $this->renderObject(parent::errorFunc('No se puede Borrar el Registro: '.$e->getMessage()));
        }
    }
}
