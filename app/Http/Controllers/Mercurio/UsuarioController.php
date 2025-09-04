<?php
namespace App\Http\Controllers\Mercurio;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Library\Auth\AuthJwt;
use App\Library\Auth\SessionCookies;
use App\Models\Adapter\DbBase;
use App\Models\Gener09;
use App\Models\Gener18;
use App\Services\Utils\Generales;
use Illuminate\Http\Request;

class UsuarioController extends ApplicationController
{
    protected $db;
    protected $user;
    protected $tipo;

    public function __construct()
    {   
        $this->db = DbBase::rawConnect();
        $this->user = session()->has('user') ? session('user') : null;
        $this->tipo = session()->has('tipo') ? session('tipo') : null;
    }

    public function indexAction()
    {
        return view('mercurio.usuario.index', [
            'title' => 'Perfil usuario'
        ]);
    }

    public function paramsAction()
    {
        $this->setResponse("ajax");
        try {
            $mtipoDocumentos = new Gener18();
            $coddoc = array();
            foreach ($mtipoDocumentos->find() as $entity) {
                if ($entity->getCoddoc() == '7' || $entity->getCoddoc() == '2') continue;
                $coddoc["{$entity->getCoddoc()}"] = $entity->getDetdoc();
            }

            $coddocrepleg = array();
            foreach ($mtipoDocumentos->find() as $entity) {
                if ($entity->getCodrua() == 'TI' || $entity->getCodrua() == 'RC') continue;
                $coddocrepleg["{$entity->getCodrua()}"] = $entity->getDetdoc();
            }

            $codciu = array();
            $mgener09 = new Gener09();
            foreach ($mgener09->find("*", "conditions: codzon >='18000' and codzon <= '19000'") as $entity) {
                $codciu["{$entity->getCodzon()}"] = $entity->getDetzon();
            }

            $tipo = $this->Mercurio07->getArrayTipos();
            $salida = array(
                "success" => true,
                "data" => array(
                    'coddoc' => $coddoc,
                    'tipo' => $tipo,
                    'codciu' => $codciu,
                    'coddocrepleg' => $coddocrepleg
                ),
                "msj" => 'OK'
            );
        } catch (DebugException $e) {
            $salida = array(
                "success" => false,
                "msj" => $e->getMessage()
            );
        }
        return $this->renderObject($salida, false);
    }

    public function show_perfilAction()
    {
        $this->setResponse("ajax");
        try {
            $mtipoDocumentos = new Gener18();
            $mcoddoc = array();
            foreach ($mtipoDocumentos->find() as $entity) {
                if ($entity->getCoddoc() == '7' || $entity->getCoddoc() == '2') continue;
                $mcoddoc["{$entity->getCoddoc()}"] = $entity->getDetdoc();
            }

            $mcodciu = array();
            $mgener09 = new Gener09();
            foreach ($mgener09->find("*", "conditions: codzon >='18000' and codzon <= '19000'") as $entity) {
                $mcodciu["{$entity->getCodzon()}"] = $entity->getDetzon();
            }

            $mtipos = $this->Mercurio07->getArrayTipos();
            $documento = parent::getActUser("documento");
            $tipo = parent::getActUser("tipo");
            $coddoc = parent::getActUser("coddoc");

            $msubsi07 = $this->Mercurio07->findFirst(" documento='{$documento}' AND tipo='{$tipo}' AND coddoc='{$coddoc}' ");
            $entity = $msubsi07->getArray();

            $entity['coddoc_detalle'] = $mcoddoc[$coddoc];
            $entity['tipo_detalle'] = $mtipos[$tipo];
            $entity['codciu_detalle'] = $mcodciu[$msubsi07->getCodciu()];
            $entity['estado_detalle'] = $msubsi07->getEstadoDetalle();

            $salida = array(
                "success" => true,
                "data" => $entity,
                "msj" => 'OK'
            );
        } catch (DebugException $e) {
            $salida = array(
                "success" => false,
                "msj" => $e->getMessage()
            );
        }
        return $this->renderObject($salida, false);
    }

    public function guardarAction(Request $request)
    {
        $this->setResponse("ajax");
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

            $msubsi07 = $this->Mercurio07->findFirst(" documento='{$documento}' AND tipo='{$tipo}' AND coddoc='{$coddoc}' AND (tipo != '{$old_tipo}' AND coddoc != '{$old_coddoc}')");
            if ($msubsi07) {
                throw new DebugException("Error el registro de usuario ya existe registrado", 501);
            }

            $msubsi07 = $this->Mercurio07->findFirst(" documento='{$documento}' AND tipo='{$old_tipo}' AND coddoc='{$old_coddoc}'");
            $msubsi07->setTipo($tipo);
            $msubsi07->setEmail($email);
            $msubsi07->setCodciu($codciu);
            $msubsi07->setNombre($nombre);
            $msubsi07->setCoddoc($coddoc);

            if (strlen($newclave) > 5 && strlen($newclave) < 80) {
                $hash = Generales::GeneraClave($newclave);
                $msubsi07->setClave($hash[0]);
            }

            $msubsi07->save();
            $entity = $msubsi07->getArray();
            $response = array(
                "msj" => "Proceso se ha completado con éxito",
                "success" => true,
                "data" => $entity
            );
        } catch (DebugException $e) {
            $response = array(
                'success' => false,
                'msj' => $e->getMessage()
            );
        }
        return $this->renderObject($response, false);
    }
}
