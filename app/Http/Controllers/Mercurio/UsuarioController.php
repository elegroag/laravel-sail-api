<?php

namespace App\Http\Controllers\Mercurio;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use App\Models\FormularioDinamico;
use App\Models\Gener09;
use App\Models\Gener18;
use App\Models\Mercurio07;
use Illuminate\Http\Request;

class UsuarioController extends ApplicationController
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
        return view('mercurio.usuario.index', [
            'title' => 'Perfil usuario',
            'documento' => $this->user['documento'],
            'coddoc' => $this->user['coddoc'],
            'tipo' => $this->tipo,
        ]);
    }

    public function params()
    {
        try {
            $mtipoDocumentos = new Gener18;
            $coddoc = [];
            foreach ($mtipoDocumentos->all() as $entity) {
                if ($entity->getCoddoc() == '7' || $entity->getCoddoc() == '2') {
                    continue;
                }
                $coddoc["{$entity->getCoddoc()}"] = $entity->getDetdoc();
            }

            $coddocrepleg = [];
            foreach ($mtipoDocumentos->all() as $entity) {
                if ($entity->getCodrua() == 'TI' || $entity->getCodrua() == 'RC') {
                    continue;
                }
                $coddocrepleg["{$entity->getCodrua()}"] = $entity->getDetdoc();
            }

            $codciu = [];
            $mgener09 = new Gener09;
            foreach ($mgener09->getFind("conditions: codzon >='18000' and codzon <= '19000'") as $entity) {
                $codciu["{$entity->getCodzon()}"] = $entity->getDetzon();
            }

            $data = [
                'coddoc' => $coddoc,
                'tipo' => $this->tipo,
                'codciu' => $codciu,
                'estado' => get_user_estados(),
            ];

            $formulario = FormularioDinamico::where('name', 'mercurio07')->first();
            $componentes = $formulario->componentes()->get();

            $componentes = $componentes->map(function ($componente) use ($data) {
                $_componente = $componente->toArray();
                $_componente['id'] = $componente->name;
                if (isset($data[$componente->name])) {
                    $_componente['data_source'] = $data[$componente->name];
                }
                return $_componente;
            });

            $salida = [
                'success' => true,
                'data' => $componentes,
                'msj' => 'OK',
            ];
        } catch (\Throwable $e) {
            $salida = $this->handleException($e, request());
        }

        return response()->json($salida);
    }

    public function showPerfil()
    {
        try {
            $documento = $this->user['documento'];
            $coddoc = $this->user['coddoc'];
            $tipo = $this->tipo;

            $mtipoDocumentos = Gener18::all();
            $mcoddoc = [];
            foreach ($mtipoDocumentos as $entity) {
                if ($entity->getCoddoc() == '7' || $entity->getCoddoc() == '2') {
                    continue;
                }
                $mcoddoc["{$entity->getCoddoc()}"] = $entity->getDetdoc();
            }

            $mcodciu = [];
            $mgener09 = Gener09::whereBetween('codzon', ['18000', '19000'])->get();
            foreach ($mgener09 as $entity) {
                $mcodciu["{$entity->codzon}"] = $entity->detzon;
            }

            $mtipos = (new Mercurio07)->getArrayTipos();

            $msubsi07 = Mercurio07::where('documento', $documento)
                ->where('tipo', $tipo)
                ->where('coddoc', $coddoc)
                ->first();

            $entity = $msubsi07->toArray();

            $entity['coddoc_detalle'] = $mcoddoc[$coddoc];
            $entity['tipo_detalle'] = $mtipos[$tipo];
            $entity['codciu_detalle'] = $mcodciu[$msubsi07->getCodciu()];
            $entity['estado_detalle'] = $msubsi07->getEstadoDetalle();

            $salida = [
                'success' => true,
                'data' => $entity,
                'msj' => 'OK',
            ];
        } catch (\Throwable $e) {
            $salida = $this->handleException($e, request());
        }

        return response()->json($salida);
    }

    public function guardar(Request $request)
    {
        try {
            $tipo = $request->input('tipo');
            $coddoc = $request->input('coddoc');
            $nombre = $request->input('nombre');
            $codciu = $request->input('codciu');
            $newclave = $request->input('newclave');
            $email = $request->input('email');

            $documento = $this->user['documento'];
            $old_coddoc = $this->user['coddoc'];
            $old_tipo = $this->tipo;

            $msubsi07 = Mercurio07::where('documento', $documento)
                ->where('tipo', $tipo)
                ->where('coddoc', $coddoc)
                ->where('tipo', '!=', $old_tipo)
                ->where('coddoc', '!=', $old_coddoc)
                ->first();

            if ($msubsi07) {
                throw new DebugException('Error el registro de usuario ya existe registrado', 501);
            }

            $msubsi07 = Mercurio07::where('documento', $documento)
                ->where('tipo', $old_tipo)
                ->where('coddoc', $old_coddoc)
                ->first();

            $msubsi07->setTipo($tipo);
            $msubsi07->setEmail($email);
            $msubsi07->setCodciu($codciu);
            $msubsi07->setNombre($nombre);
            $msubsi07->setCoddoc($coddoc);

            if (strlen($newclave) > 5 && strlen($newclave) < 80) {
                $hash = clave_hash($newclave);
                $msubsi07->setClave($hash);
            }

            $msubsi07->save();
            $entity = $msubsi07->toArray();
            $response = [
                'msj' => 'Proceso se ha completado con éxito',
                'success' => true,
                'data' => $entity,
            ];
        } catch (\Throwable $e) {
            $response = $this->handleException($e, request());
        }

        return response()->json($response);
    }
}
