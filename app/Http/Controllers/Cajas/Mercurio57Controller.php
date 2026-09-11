<?php

namespace App\Http\Controllers\Cajas;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use Illuminate\Support\Facades\DB;
use App\Models\Mercurio01;
use App\Models\Mercurio57;
use Illuminate\Http\Request;

class Mercurio57Controller extends ApplicationController
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
        return view('cajas.mercurio57.index', [
            'title' => 'Promociones Movil',
            'help' => 'Esta opcion permite manejar las promociones del carrusel móvil.',
        ]);
    }

    public function galeria()
    {
        try {
            $mercurio01 = $this->resolveMercurio01();

            $galeria = Mercurio57::orderBy('orden', 'ASC')->get();

            $data = $galeria->map(function ($item) use ($mercurio01) {
                if (empty($item->getUrl()) && ! empty($item->getArchivo())) {
                    $this->syncImageUrl($item, $mercurio01);
                    $item->save();
                }

                return $this->mapRecord($item, $mercurio01);
            });

            $response = parent::successFunc('Consulta exitosa');
            $response['data'] = $data;

            return $this->renderObject($response);
        } catch (DebugException $e) {
            return $this->renderObject(parent::errorFunc($e->getMessage()));
        } catch (\Throwable $e) {
            parent::setLogger($e->getMessage());

            return $this->renderObject(parent::errorFunc('No se pudo cargar las promociones.'));
        }
    }

    public function editar(Request $request)
    {
        try {
            $this->setResponse('ajax');
            $numpro = $request->input('numpro');
            $mercurio01 = $this->resolveMercurio01();

            $mercurio57 = Mercurio57::where('numpro', $numpro)->first();
            if (! $mercurio57) {
                throw new DebugException('Registro no encontrado.');
            }

            return $this->renderObject($this->mapRecord($mercurio57, $mercurio01), false);
        } catch (DebugException $e) {
            $response = parent::errorFunc($e->getMessage());

            return $this->renderObject($response, false);
        }
    }

    public function guardar(Request $request)
    {
        try {
            $this->setResponse('ajax');
            DB::beginTransaction();

            $mercurio01 = $this->resolveMercurio01();
            $numpro = $request->input('numpro');
            $isUpdate = ! empty($numpro);
            $estado = $request->input('estado', 'A');

            if (! in_array($estado, ['A', 'I'], true)) {
                throw new DebugException('Estado no válido.');
            }

            if ($isUpdate) {
                $mercurio57 = Mercurio57::where('numpro', $numpro)->first();
                if (! $mercurio57) {
                    throw new DebugException('Registro no encontrado.');
                }
            } else {
                $numpro = (Mercurio57::max('numpro') ?? 0) + 1;
                $mercurio57 = new Mercurio57;
                $mercurio57->setNumpro($numpro);
                $mercurio57->setOrden((Mercurio57::max('orden') ?? 0) + 1);
            }

            $mercurio57->setEstado($estado);

            if ($request->hasFile('archivo') && $request->file('archivo')->isValid()) {
                $this->replaceArchivo($request, $mercurio57, $mercurio01, (int) $numpro, $isUpdate);
            } elseif (! $isUpdate) {
                throw new DebugException('No se ha subido ningún archivo o el archivo no es válido.');
            } elseif (empty($mercurio57->getArchivo())) {
                throw new DebugException('El registro no tiene imagen asociada.');
            }

            $this->syncImageUrl($mercurio57, $mercurio01);

            if (! $mercurio57->save()) {
                parent::setLogger($mercurio57->getMessages());
                DB::rollBack();
                throw new DebugException('Error al guardar la promoción.');
            }

            DB::commit();
            $response = parent::successFunc($isUpdate ? 'Actualización terminada con éxito' : 'Creacion terminada Con Exito');

            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            DB::rollBack();
            $response = parent::errorFunc('No se puede guardar el Registro: '.$e->getMessage());

            return $this->renderObject($response, false);
        }
    }

    public function arriba(Request $request)
    {
        try {
            $this->setResponse('ajax');
            $numpro = $request->input('numpro');

            DB::beginTransaction();
            $objetivo = Mercurio57::where('numpro', $numpro)->first();
            if (! $objetivo) {
                throw new DebugException('Registro no encontrado.');
            }

            $orden_obj = $objetivo->getOrden();
            $minimo = Mercurio57::min('orden');

            if ($orden_obj != $minimo) {
                $superior = Mercurio57::where('orden', '<', $orden_obj)->orderBy('orden', 'desc')->first();
                if ($superior) {
                    $orden_sup = $superior->getOrden();
                    $objetivo->orden = $orden_sup;
                    $superior->orden = $orden_obj;
                    $objetivo->save();
                    $superior->save();
                }
            }
            DB::commit();
            $response = parent::successFunc('Ordenado Con Exito');

            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            DB::rollBack();
            $response = parent::errorFunc('No se puede Ordenar el Registro: '.$e->getMessage());

            return $this->renderObject($response, false);
        }
    }

    public function abajo(Request $request)
    {
        try {
            $this->setResponse('ajax');
            $numpro = $request->input('numpro');

            DB::beginTransaction();
            $objetivo = Mercurio57::where('numpro', $numpro)->first();
            if (! $objetivo) {
                throw new DebugException('Registro no encontrado.');
            }

            $orden_obj = $objetivo->getOrden();
            $maximo = Mercurio57::max('orden');

            if ($orden_obj != $maximo) {
                $inferior = Mercurio57::where('orden', '>', $orden_obj)->orderBy('orden', 'asc')->first();
                if ($inferior) {
                    $orden_inf = $inferior->getOrden();
                    $objetivo->orden = $orden_inf;
                    $inferior->orden = $orden_obj;
                    $objetivo->save();
                    $inferior->save();
                }
            }
            DB::commit();
            $response = parent::successFunc('Ordenado Con Exito');

            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            DB::rollBack();
            $response = parent::errorFunc('No se puede Ordenar el Registro: '.$e->getMessage());

            return $this->renderObject($response, false);
        }
    }

    public function borrar(Request $request)
    {
        try {
            $this->setResponse('ajax');
            $numpro = $request->input('numpro');

            DB::beginTransaction();
            $mercurio57 = Mercurio57::where('numpro', $numpro)->first();

            if ($mercurio57) {
                $archivo = $mercurio57->getArchivo();
                $mercurio01 = $this->resolveMercurio01();

                if (! empty($archivo)) {
                    $filePath = public_path($mercurio01->getPath().'galeria/'.$archivo);
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }
                $mercurio57->delete();
            } else {
                throw new DebugException('El registro a borrar no existe.');
            }

            DB::commit();
            $response = parent::successFunc('Borrado Con Exito');

            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            DB::rollBack();
            $response = parent::errorFunc('No se puede Borrar el Registro: '.$e->getMessage());

            return $this->renderObject($response, false);
        }
    }

    protected function resolveMercurio01(): Mercurio01
    {
        $mercurio01 = Mercurio01::where('codapl', 'MO')->first();
        if (! $mercurio01) {
            $mercurio01 = Mercurio01::first();
        }
        if (! $mercurio01) {
            throw new DebugException('Configuración básica no encontrada.');
        }

        return $mercurio01;
    }

    protected function mapRecord(Mercurio57 $item, Mercurio01 $mercurio01): array
    {
        $archivo = $item->getArchivo();
        $imageUrl = $this->buildImageUrl($mercurio01, $archivo);

        return [
            'numpro' => $item->getNumpro(),
            'orden' => $item->getOrden(),
            'estado' => $item->getEstado(),
            'estado_label' => $item->getEstado() === 'A' ? 'Activo' : 'Inactivo',
            'url' => $item->getUrl() ?: $imageUrl,
            'archivo' => $imageUrl,
            'archivo_nombre' => $archivo,
        ];
    }

    protected function syncImageUrl(Mercurio57 $mercurio57, Mercurio01 $mercurio01): void
    {
        $imageUrl = $this->buildImageUrl($mercurio01, $mercurio57->getArchivo());
        if ($imageUrl !== '') {
            $mercurio57->setUrl($imageUrl);
        }
    }

    protected function buildImageUrl(Mercurio01 $mercurio01, ?string $fileName): string
    {
        if (empty($fileName)) {
            return '';
        }

        return $mercurio01->dominioUrl('galeria/'.$fileName);
    }

    protected function replaceArchivo(Request $request, Mercurio57 $mercurio57, Mercurio01 $mercurio01, int $numpro, bool $isUpdate): void
    {
        $file = $request->file('archivo');
        $extension = $file->getClientOriginalExtension();
        $fileName = 'ME57_'.$numpro.'.'.$extension;
        $destinationPath = public_path($mercurio01->getPath().'galeria');

        if ($isUpdate) {
            $previousFile = $mercurio57->getArchivo();
            if (! empty($previousFile) && $previousFile !== $fileName) {
                $previousPath = public_path($mercurio01->getPath().'galeria/'.$previousFile);
                if (file_exists($previousPath)) {
                    unlink($previousPath);
                }
            }
        }

        $file->move($destinationPath, $fileName);
        $mercurio57->setArchivo($fileName);
    }
}
