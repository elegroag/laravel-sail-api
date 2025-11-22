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
        $this->user = session('user') ?? null;
        $this->tipo = session('tipo') ?? null;
    }

    public function index() {}

    /**
     * complemento_nutricional function
     * codser = 27
     *
     * @return void
     */
    public function complementoNutricional(Request $request)
    {
        try {
            $codser = '27';
            $cupos_disponibles = ServiciosCupos::where('estado', 'A')
                ->where('codser', $codser)
                ->first();

            if ($cupos_disponibles == false) {
                set_flashdata('error', [
                    'msj' => 'El servicio no está disponible para el actual periodo.',
                    'code' => '505',
                ]);

                return redirect()->route('principal.index');
            }

            $cupos_disponibles = ($cupos_disponibles) ? $cupos_disponibles->getCupos() : 0;
            $cupos = $this->misCuposAplicados($codser);
            $beneficiarios = $this->afiliadosBeneficiarios($codser);

            return view('mercurio/productos/complemento_nutricional', [
                'title' => 'Oferta De Productos y Servicios',
                'hide_header' => true,
                'cupos_disponibles' => $cupos_disponibles,
                'cupos' => $cupos,
                'beneficiarios' => $beneficiarios,
                'codser' => $codser,
            ]);
        } catch (\Throwable $th) {
            $salida = $this->handleException($th, $request);
            set_flashdata('error', [
                'msj' => $salida['msj'],
                'code' => $salida['code'],
            ]);
            return redirect()->route('principal.index');
        }
    }

    public function aplicarCupo(Request $request)
    {
        $habilId = $request->input('id');
        $docben = $request->input('docben');
        $codser = $request->input('codser');
        $cedtra = $this->user['documento'];

        try {
            // Verificar si ya existe un PIN activo para este trabajador/beneficiario/servicio
            $has = PinesAfiliado::where('cedtra', $cedtra)
                ->where('docben', $docben)
                ->where('codser', $codser)
                ->count();
            if ($has > 0) {
                throw new DebugException('El servicio ya está aplicado por el trabajador, no se requiere de más acciones.', 201);
            }

            $afiliadoHabiles = AfiliadoHabil::where('cedtra', $cedtra)
                ->where('docben', $docben)
                ->where('codser', $codser)
                ->first();
            if ($afiliadoHabiles) {
                $cupos_disponibles = ServiciosCupos::where('estado', 'A')
                    ->where('codser', $codser)
                    ->first();

                if (! $cupos_disponibles) {
                    throw new DebugException('El servicio no está disponible para el actual periodo.', 201);
                }
                if ($cupos_disponibles->getCupos() == 0) {
                    $cupos_disponibles->setEstado('F');
                    $cupos_disponibles->save();
                    throw new DebugException('No es posible continuar, los cupos para el servicio ya están agotados.', 201);
                }

                $cupos_disponibles->setCupos($cupos_disponibles->getCupos() - 1);
                if ($cupos_disponibles->getCupos() == 0) {
                    $cupos_disponibles->setEstado('F');
                }

                $cupos_disponibles->save();

                $pinesAfiliado = new PinesAfiliado;
                $pinesAfiliado->setId(null);
                $pinesAfiliado->setFecha(date('Y-m-d'));
                $pinesAfiliado->setCodser($codser);
                $pinesAfiliado->setCedtra($cedtra);
                $pinesAfiliado->setDocben($docben);
                $pinesAfiliado->setEstado('A');
                $pinesAfiliado->setPin($afiliadoHabiles->getPin());
                $pinesAfiliado->save();

                $salida = [
                    'success' => true,
                    'data' => $pinesAfiliado->getArray(),
                    'beneficiarios' => $this->afiliadosBeneficiarios($codser),
                    'msj' => 'El producto ya se solicito de forma correcta, se requiere de realizar el pago mediante PIN, por medio de Davivienda. Dispones de 3 días para realizar el pago respectivo.<br/> ' .
                        "<p class='text-center'>" .
                        "<span class='text-pin'>{$afiliadoHabiles->getPin()}</span>
                        <button type='button' toggle='copy' data-cid='{$afiliadoHabiles->getPin()}' class='btn btn-light btn-sm' title='copiar'>" .
                        "<i class='ni ni-single-copy-04 fa-1x'></i>
                        </button>
                    </p>",
                ];
            } else {
                throw new DebugException('El trabajador no dispone de beneficiarios activos para solicitar el producto.', 501);
            }
        } catch (\Throwable $e) {
            $salida = $this->handleException($e, $request);
            $salida['beneficiarios'] = $this->afiliadosBeneficiarios($codser);
        }

        return response()->json($salida);
    }

    public function numeroCuposDisponibles($codser)
    {
        $cupos_disponibles = ServiciosCupos::where('estado', 'A')
            ->where('codser', $codser)
            ->first();

        return response()->json(
            [
                'success' => true,
                'cupos' => $cupos_disponibles->getCupos(),
            ]
        );
    }

    public function serviciosAplicados($codser)
    {
        $data = $this->afiliadosBeneficiarios($codser);
        return response()->json(
            [
                'success' => true,
                'data' => $data,
            ]
        );
    }

    public function misCuposAplicados($codser)
    {
        $pinesAfiliados = PinesAfiliado::where('cedtra', $this->user['documento'])
            ->where('codser', $codser);

        if ($pinesAfiliados->exists()) {
            return $pinesAfiliados->get()->toArray();
        }

        return [];
    }

    public function afiliadosBeneficiarios($codser)
    {
        $cedtra = $this->user['documento'];
        $afiliadoHabiles = AfiliadoHabil::where('cedtra', $cedtra)
            ->where('codser', $codser)
            ->get();
        $habiles = [];
        if ($afiliadoHabiles) {
            $ai = 0;
            foreach ($afiliadoHabiles as $habi) {
                $ai++;
                $pinActivo = PinesAfiliado::where('cedtra', $habi->getCedtra())
                    ->where('docben', $habi->getDocben())
                    ->where('estado', 'A')
                    ->where('codser', $codser)
                    ->count();
                $habiles[$ai] = $habi->getArray();
                $habiles[$ai]['hasPin'] = $pinActivo;
            }
        }

        return $habiles;
    }

    public function buscarCupo(Request $request)
    {
        try {

            $docben = $request->input('docben');
            $codser = $request->input('codser');
            $cedtra = $this->user['documento'];

            $pines = PinesAfiliado::where('cedtra', $cedtra)
                ->where('docben', $docben)
                ->where('estado', 'A')
                ->where('codser', $codser)
                ->first();
            $pinAfiliado = ($pines) ? $pines->getArray() : false;

            $salida = [
                'success' => true,
                'cupo' => $pinAfiliado,
                'msj' => ($pinAfiliado == false) ? 'No hay un pin activo para realizar el pago respectivo.' :
                    'El producto ya se solicito de forma correcta, se requiere de realizar el pago mediante PIN, por medio de Davivienda.<br/>Si ya realizaste el pago no se requiere de hacer más acciones.<br/>' .
                    "<p class='text-center'>
                    <span class='text-pin'>{$pinAfiliado['pin']}</span>" .
                    "<button type='button' toggle='copy' data-cid='{$pinAfiliado['pin']}' class='btn btn-light btn-sm' title='copiar'>" .
                    "<i class='ni ni-single-copy-04 fa-1x'></i>
                    </button>
                </p>",
            ];
        } catch (\Throwable $e) {
            $salida = $this->handleException($e, $request);
        }

        return response()->json($salida);
    }
}
