<?php

namespace App\Library\Auth;

use App\Helpers\Core;
use App\Helpers\Router;
use App\Helpers\Session as SESSION;
use App\Exceptions\AuthException;
use App\Models\Adapter\DbBase;
use App\Models\Gener02;

use Carbon\Carbon;

class AuthCajas
{
	private $month_enable = 2;
	private $db;
	private $comfirmar;
	private $usuario;
	private $resultado = '';
	public static $adapter;

	public function __construct()
	{
		$this->db = DbBase::rawConnect();
	}

	public function principal($clave, $comfirmar)
	{
		if (!$this->usuario) {
			throw new AuthException("El usuario es requerido para la autenticación. 2", 2);
		}
		if (!$clave) {
			throw new AuthException("La clave es requerida para la autenticación. 3", 3);
		}
		$this->comfirmar = $comfirmar;
		$fecha  = $this->usuario->getUpdate_at();
		$criptada = $this->usuario->getCriptada();
		$this->resultado = $confirma_politica = $this->usuario->getConfirmaPolitica();

		if (!clave_verify($clave, $criptada)) {
			throw new AuthException("La clave no es correcta para continuar con la autenticación. 6", 6);
		}
		$this->validarVigenciaClave($fecha);
		$this->confirmaPolitica($confirma_politica);
		$this->resetearIntentos();
		$this->crearSession();
	}

	public function validarVigenciaClave($fecha)
	{
		$now = Carbon::now();
		$parsedDate = Carbon::parse($fecha);
		$interval = $now->diffInMonths($parsedDate);
		if ($interval >= $this->month_enable) {
			throw new AuthException("El cambio de clave es requerido para continuar con la autenticación. Han pasado {$interval} días desde el ultimo cambio.", 8);
		}
	}

	public function confirmaPolitica($confirma_politica)
	{
		$this->resultado = "!Bienvenido su autenticación es exitosa¡";
		if ($confirma_politica == null || $confirma_politica == 0) {
			if ($this->comfirmar == 'S') {
				Gener02::where("usuario", $this->usuario->getUsuario())->update(["confirma_politica" => 'S']);

				$this->resultado = "La autenticación es exitosa. \n" .
					"Gracias por aceptar la política de tratamiento de datos. Es un compromiso de todos los trabajadores de COMFACA.";
			} else {
				$this->resultado = "La autenticación es exitosa. \n" .
					"Recomendamos que acepté la política de tratamiento de datos. Es un compromiso de todos los trabajadores de COMFACA.";
			}
		}
	}

	public function resetearIntentos()
	{
		if ($this->usuario->getIntentos() > 0) {
			$update_at = date('Y-m-d H:i:s');
			Gener02::where("usuario", $this->usuario->getUsuario())->update([
				"intentos" => '0',
				"estado" => 'A',
				"update_at" => $update_at
			]);
		}
	}

	public function crearSession()
	{
		$auth = new SessionCookies(
			"model: gener02",
			"tipo: CAJAS",
			"usuario: {$this->usuario->getUsuario()}",
			"cedtra: {$this->usuario->getCedtra()}",
			"estado: A"
		);

		if (!$auth->authenticate()) {
			throw new AuthException("Error acceso incorrecto. No se logra completar la autenticación", 504);
		} else {
			$msj = "La autenticación se ha completado con éxito.";
			$response = [
				"success" => true,
				"location" => 'principal/index',
				"msj" => $msj
			];
		}
		return $response;
	}

	public function getResultado()
	{
		return $this->resultado;
	}

	public function cargarIntentos($usuario)
	{
		$user = $this->db->fetchOne("SELECT * FROM gener02 WHERE estado='A' AND usuario='{$usuario}' LIMIT 1");
		if ($user) {
			$intentos = $user['intentos'] + 1;
			if ($intentos >= 3) {
				Gener02::where("usuario", $usuario)->update([
					"estado" => "B",
					"intentos" => $intentos
				]);
				$this->resultado = "El usuario se ha bloqueado, por fallar en la autenticación con más de 3 intentos.";
			} else {
				Gener02::where("usuario", $usuario)->update([
					"intentos" => $intentos
				]);
			}
		}
	}

	public function buscarUsuario($user)
	{
		$this->usuario = Gener02::where([
			"estado IN('A','B')",
			"usuario" => $user
		])->first();

		if (!$this->usuario) {
			throw new AuthException("El usuario no es correcto para continuar con la autenticación. 4", 4);
		}
		if ($this->usuario->getEstado() == 'B') {
			throw new AuthException("El usuario se encuentra bloqueado, por fallar en la autenticación con más de 3 intentos." .
				" Para poder desbloquear su cuenta puede recuperar la cuenta de usuario o solicitar el desbloqueo de su cuenta, " .
				"al aréa de sistemas, soporte_sistemas@comfaca.com.", 5);
		}
		return $this->usuario;
	}


	/**
	 * Obtener el usuario actual después de la autenticación
	 */
	public function getUsuario()
	{
		return $this->usuario;
	}
}
