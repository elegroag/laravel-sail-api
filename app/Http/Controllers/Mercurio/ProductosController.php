<?php

namespace App\Http\Controllers\Mercurio;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use App\Models\AfiliadoHabil;
use App\Models\PinesAfiliado;
use App\Models\ServiciosCupos;
use Illuminate\Http\Request;

class ProductosController extends ApplicationController
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

    public function indexAction() {}

    /**
     * complemento_nutricional function
     * codser = 27
     * @return void
     */
    public function complementoNutricionalAction()
    {
        $codser = '27';
        $cupos_disponibles = ServiciosCupos::where("estado", 'A')
            ->where("codser", $codser)
            ->first();

        if ($cupos_disponibles == false) {
            set_flashdata("error", array(
                "msj" => "El servicio no está disponible para el actual periodo.",
                "code" => '505'
            ));
            return redirect()->route("principal.index");
        }

        $cupos_disponibles = ($cupos_disponibles) ? $cupos_disponibles->getCupos() : 0;
        $cupos = $this->misCuposAplicados($codser);
        $beneficiarios = $this->afiliadosBeneficiarios($codser);

        return view("mercurio/productos/complemento_nutricional", [
            "title" => "Oferta De Productos y Servicios",
            "hide_header" => true,
            "cupos_disponibles" => $cupos_disponibles,
            "cupos" => $cupos,
            "beneficiarios" => $beneficiarios,
            "codser" => $codser,
        ]);
    }

    public function aplicarCupoAction(Request $request)
    {
        $this->setResponse('ajax');
        $habilId = $request->input("id", "striptags", "extraspaces");
        $docben = $request->input("docben", "striptags", "extraspaces");
        $codser = $request->input("codser", "striptags", "extraspaces");
        $cedtra =  parent::getActUser('documento');

        try {
            $pinesAfiliado = new PinesAfiliado();
            $has = $pinesAfiliado->count("*", "conditions: cedtra='{$cedtra}' and docben='{$docben}' and codser='{$codser}'");
            if ($has > 0) {
                throw new DebugException("El servicio ya está aplicado por el trabajador, no se requiere de más acciones.", 201);
            }

            $afiliadoHabiles = $this->AfiliadoHabil->findFirst(" cedtra='{$cedtra}' and docben='{$docben}' and codser='{$codser}' ");
            if ($afiliadoHabiles) {
                $serviciosCupos = new ServiciosCupos();
                $cupos_disponibles = $serviciosCupos->findFirst(" estado='A' and codser='{$codser}'");
                if ($cupos_disponibles->getCupos() == 0) {
                    $cupos_disponibles->setEstado('F');
                    $cupos_disponibles->save();
                    throw new DebugException("No es posible continuar, los cupos para el servicio ya están agotados.", 201);
                }

                $cupos_disponibles->setCupos($cupos_disponibles->getCupos() - 1);
                if ($cupos_disponibles->getCupos() == 0) {
                    $cupos_disponibles->setEstado('F');
                }

                $cupos_disponibles->save();

                $pinesAfiliado = new PinesAfiliado();
                $pinesAfiliado->setId(null);
                $pinesAfiliado->setFecha(date('Y-m-d'));
                $pinesAfiliado->setCodser($codser);
                $pinesAfiliado->setCedtra(parent::getActUser('documento'));
                $pinesAfiliado->setDocben($docben);
                $pinesAfiliado->setEstado('A');
                $pinesAfiliado->setPin($afiliadoHabiles->getPin());
                $pinesAfiliado->save();

                $salida = array(
                    "success" => true,
                    "data" =>  $pinesAfiliado->getArray(),
                    "beneficiarios" => $this->afiliadosBeneficiarios($codser),
                    "msj" => 'El producto ya se solicito de forma correcta, se requiere de realizar el pago mediante PIN, por medio de Davivienda. Dispones de 3 días para realizar el pago respectivo.<br/> ' .
                        "<p class='text-center'>" .
                        "<span class='text-pin'>{$afiliadoHabiles->getPin()}</span>
                        <button type='button' toggle='copy' data-cid='{$afiliadoHabiles->getPin()}' class='btn btn-light btn-sm' title='copiar'>" .
                        "<i class='ni ni-single-copy-04 fa-1x'></i>
                        </button>
                    </p>"
                );
            } else {
                throw new DebugException("El trabajador no dispone de beneficiarios activos para solicitar el producto.", 501);
            }
        } catch (DebugException $e) {
            $salida = array(
                "success" => false,
                "msj" => $e->getMessage(),
                "beneficiarios" => $this->afiliadosBeneficiarios($codser)
            );
        }
        return $this->renderObject($salida);
    }

    public function numeroCuposDisponiblesAction($codser)
    {
        $this->setResponse('ajax');
        $cupos_disponibles = (new ServiciosCupos())->findFirst(" estado='A' and codser='{$codser}'");
        return $this->renderObject(
            array(
                "success" => true,
                "cupos" => $cupos_disponibles->getCupos()
            )
        );
    }

    public function serviciosAplicadosAction($codser)
    {
        $this->setResponse('ajax');
        $data = $this->afiliadosBeneficiarios($codser);
        return $this->renderObject(
            array(
                "success" => true,
                "data" => $data
            )
        );
    }

    function misCuposAplicados($codser)
    {
        $pinesAfiliados = PinesAfiliado::where("cedtra", $this->user['documento'])
            ->where("codser", $codser);

        if ($pinesAfiliados->exists()) {
            return $pinesAfiliados->get()->toArray();
        }
        return [];
    }

    function afiliadosBeneficiarios($codser)
    {
        $cedtra =  parent::getActUser('documento');
        $afiliadoHabiles = (new AfiliadoHabil)->getFind("cedtra='{$cedtra}' and codser='{$codser}' ");
        $habiles = array();
        if ($afiliadoHabiles) {
            $ai = 0;
            foreach ($afiliadoHabiles as $habi) {
                $ai++;
                $pinActivo =  (new PinesAfiliado)->count(
                    "*",
                    "cedtra='{$habi->getCedtra()}' and docben='{$habi->getDocben()}' and estado='A' and codser='{$codser}'"
                );
                $habiles[$ai] = $habi->getArray();
                $habiles[$ai]['hasPin'] = $pinActivo;
            }
        }
        return $habiles;
    }

    public function buscarCupoAction(Request $request)
    {
        $this->setResponse('ajax');
        $docben = $request->input("docben", "striptags", "extraspaces");
        $codser = $request->input("codser", "striptags", "extraspaces");
        $cedtra = parent::getActUser('documento');

        $pines =  (new PinesAfiliado)->findFirst("cedtra='{$cedtra}' and docben='{$docben}' and estado='A' and codser='{$codser}'");
        $pinAfiliado = ($pines) ? $pines->getArray() : false;

        return $this->renderObject(
            array(
                "success" => true,
                "cupo" => $pinAfiliado,
                "msj" => ($pinAfiliado == false) ? 'No hay un pin activo para realizar el pago respectivo.' :
                    'El producto ya se solicito de forma correcta, se requiere de realizar el pago mediante PIN, por medio de Davivienda.<br/>Si ya realizaste el pago no se requiere de hacer más acciones.<br/>' .
                    "<p class='text-center'>
                    <span class='text-pin'>{$pinAfiliado['pin']}</span>" .
                    "<button type='button' toggle='copy' data-cid='{$pinAfiliado['pin']}' class='btn btn-light btn-sm' title='copiar'>" .
                    "<i class='ni ni-single-copy-04 fa-1x'></i>
                    </button>
                </p>"
            )
        );
    }
}
