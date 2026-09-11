<?php

namespace App\Http\Controllers\Cajas;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\EpaycoCuenta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EpaycoCuentaController extends ApplicationController
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
        return view('cajas.epayco_cuentas.index', [
            'title' => 'Cuentas ePayco',
            'help' => 'Administra las llaves y comercios ePayco por ambiente (development / production).',
        ]);
    }

    public function buscarCuenta()
    {
        try {
            $data = EpaycoCuenta::query()
                ->orderByDesc('id')
                ->get()
                ->map(fn (EpaycoCuenta $item) => $this->mapRecord($item, false));

            $response = parent::successFunc('Consulta exitosa');
            $response['data'] = $data;

            return $this->renderObject($response);
        } catch (DebugException $e) {
            return $this->renderObject(parent::errorFunc($e->getMessage()));
        } catch (\Throwable $e) {
            parent::setLogger($e->getMessage());

            return $this->renderObject(parent::errorFunc('No se pudieron cargar las cuentas ePayco.'));
        }
    }

    public function editar(Request $request)
    {
        try {
            $this->setResponse('ajax');
            $id = $request->input('id');

            $cuenta = EpaycoCuenta::where('id', $id)->first();
            if (! $cuenta) {
                throw new DebugException('Registro no encontrado.');
            }

            return $this->renderObject($this->mapRecord($cuenta, true));
        } catch (DebugException $e) {
            return $this->renderObject(parent::errorFunc($e->getMessage()));
        }
    }

    public function guardar(Request $request)
    {
        try {
            $this->setResponse('ajax');
            DB::beginTransaction();

            $id = $request->input('id');
            $isUpdate = ! empty($id);

            $envMode = trim((string) $request->input('env_mode', ''));
            $account = trim((string) $request->input('account', ''));
            $publicKey = trim((string) $request->input('public_key', ''));
            $privateKey = trim((string) $request->input('private_key', ''));
            $pKey = trim((string) $request->input('p_key', ''));
            $pIdCustomer = trim((string) $request->input('p_id_customer', ''));

            if ($account === '' || $envMode === '' || $publicKey === '' || $pIdCustomer === '') {
                throw new DebugException('account, env_mode, public_key y p_id_customer son requeridos.');
            }

            if (! in_array($envMode, ['development', 'production'], true)) {
                throw new DebugException('env_mode debe ser development o production.');
            }

            if ($isUpdate) {
                $cuenta = EpaycoCuenta::where('id', $id)->first();
                if (! $cuenta) {
                    throw new DebugException('Registro no encontrado.');
                }
            } else {
                if ($privateKey === '' || $pKey === '') {
                    throw new DebugException('private_key y p_key son requeridos al crear.');
                }
                $cuenta = new EpaycoCuenta;
            }

            $duplicateAccount = EpaycoCuenta::query()
                ->where('account', $account)
                ->when($isUpdate, fn ($q) => $q->where('id', '!=', $id))
                ->exists();
            if ($duplicateAccount) {
                throw new DebugException('Ya existe una cuenta con ese nombre (account).');
            }

            $duplicateCustomer = EpaycoCuenta::query()
                ->where('p_id_customer', $pIdCustomer)
                ->when($isUpdate, fn ($q) => $q->where('id', '!=', $id))
                ->exists();
            if ($duplicateCustomer) {
                throw new DebugException('Ya existe una cuenta con ese p_id_customer.');
            }

            $cuenta->account = $account;
            $cuenta->env_mode = $envMode;
            $cuenta->public_key = $publicKey;
            $cuenta->p_id_customer = $pIdCustomer;

            if ($privateKey !== '') {
                $cuenta->private_key = $privateKey;
            }
            if ($pKey !== '') {
                $cuenta->p_key = $pKey;
            }

            if (! $cuenta->save()) {
                DB::rollBack();
                throw new DebugException('Error al guardar la cuenta ePayco.');
            }

            DB::commit();
            $response = parent::successFunc($isUpdate ? 'Actualización terminada con éxito' : 'Creación terminada con éxito');

            return $this->renderObject($response);
        } catch (DebugException $e) {
            DB::rollBack();

            return $this->renderObject(parent::errorFunc('No se puede guardar el registro: '.$e->getMessage()));
        } catch (\Throwable $e) {
            DB::rollBack();
            parent::setLogger($e->getMessage());

            return $this->renderObject(parent::errorFunc('No se puede guardar el registro.'));
        }
    }

    public function borrar(Request $request)
    {
        try {
            $this->setResponse('ajax');
            $id = $request->input('id');

            DB::beginTransaction();
            $cuenta = EpaycoCuenta::where('id', $id)->first();

            if (! $cuenta) {
                throw new DebugException('El registro a borrar no existe.');
            }

            $cuenta->delete();
            DB::commit();

            return $this->renderObject(parent::successFunc('Borrado con éxito'));
        } catch (DebugException $e) {
            DB::rollBack();

            return $this->renderObject(parent::errorFunc('No se puede borrar el registro: '.$e->getMessage()));
        } catch (\Throwable $e) {
            DB::rollBack();
            parent::setLogger($e->getMessage());

            return $this->renderObject(parent::errorFunc('No se puede borrar el registro.'));
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function mapRecord(EpaycoCuenta $item, bool $withSecrets): array
    {
        $data = [
            'id' => $item->id,
            'account' => $item->account,
            'env_mode' => $item->env_mode,
            'public_key' => $item->public_key,
            'p_id_customer' => $item->p_id_customer,
            'created_at' => optional($item->created_at)?->format('Y-m-d H:i'),
            'updated_at' => optional($item->updated_at)?->format('Y-m-d H:i'),
        ];

        if ($withSecrets) {
            $item->makeVisible(['private_key', 'p_key']);
            $data['private_key'] = $item->private_key;
            $data['p_key'] = $item->p_key;
        } else {
            $data['private_key_mask'] = $this->maskSecret((string) $item->private_key);
            $data['p_key_mask'] = $this->maskSecret((string) $item->p_key);
        }

        return $data;
    }

    protected function maskSecret(?string $value): string
    {
        $value = (string) $value;
        if ($value === '') {
            return '—';
        }

        $len = strlen($value);
        if ($len <= 4) {
            return str_repeat('*', $len);
        }

        return str_repeat('*', max(4, $len - 4)).substr($value, -4);
    }
}
