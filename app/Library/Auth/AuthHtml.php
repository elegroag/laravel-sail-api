<?php
namespace App\Library\Auth;

use App\Helpers\Core;
use App\Helpers\Router;
use App\Helpers\Flash;
use App\Helpers\Session as SESSION;
use Securimage;
use Securimage_Color;
use Swift_Connection_SMTP;
use Swift_Message;
use Swift;
use Swift_RecipientList;
use Swift_Address;
use App\Exceptions\AuthException;

class AuthHtml
{
	private $day_enable = 60;
	private $db;
	private $comfirmar;
	private $usuario;
	private $resultado='';
	private $Gener02;
	public static $adapter;

	
	public function principal($clave, $comfirmar)
	{
		if(!$this->usuario){
			throw new AuthException("El usuario es requerido para la autenticación. 2", 2);
		}
		if(!$clave){
			throw new AuthException("La clave es requerida para la autenticación. 3", 3);
		}
		$this->comfirmar = $comfirmar;
		$fecha  = $this->usuario->getUpdate_at();
		$criptada = $this->usuario->getCriptada();
		$this->resultado = $confirma_politica = $this->usuario->getConfirmaPolitica();
		
		if(!clave_verify($clave, $criptada))
		{
			throw new AuthException("La clave no es correcta para continuar con la autenticación. 6", 6);
		}
		$this->validar_vigencia_clave($fecha);
		$this->confirma_politica($confirma_politica);
		$this->resetear_intentos();
		$this->crear_session();
	}

	public function validar_vigencia_clave($fecha)
	{
		$hoy = date('Y-m-d');
		$dif =  $this->db->fetchOne("SELECT DATEDIFF('{$hoy}', '{$fecha}') AS 'dias'");
		$interval = $dif['dias']; 
		if($interval >= $this->day_enable)
		{
			throw new AuthException("El cambio de clave es requerido para continuar con la autenticación. Han pasado {$interval} días desde el ultimo cambio.", 8);
		}
	}

	public function confirma_politica($confirma_politica)
	{
		$this->resultado = "!Bienvenido su autenticación es exitosa¡";
		if($confirma_politica == null || $confirma_politica == 0)
		{
			if($this->comfirmar != 0)
			{
				$this->Gener02->updateAll("confirma_politica='1'", "conditions: usuario='{$this->usuario->getUsuario()}'");
				$this->resultado = "La autenticación es exitosa. \n".
				"Gracias por aceptar la política de tratamiento de datos. Es un compromiso de todos los trabajadores de COMFACA.";
			}else{
				$this->resultado = "La autenticación es exitosa. \n".
				"Recomendamos que acepté la política de tratamiento de datos. Es un compromiso de todos los trabajadores de COMFACA.";
			}
		}
	}

	public function resetear_intentos()
	{
		if($this->usuario->getIntentos() > 0)
		{
			$update_at = date('Y-m-d H:i:s');
			$this->Gener02->updateAll("intentos='0', estado='A', update_at='{$update_at}'", "conditions: usuario='{$this->usuario->getUsuario()}'");
		}
	}

	public function crear_session()
	{
		$path = Core::getInitialPath().'apps/'.Router::getApplication().'/config';
		$config = parse_ini_file($path."/config.ini", true);
		$environment = parse_ini_file($path."/environment.ini", true);
		$mode = $config['application']['mode'];
		$dbdate = $config['application']['dbdate'];
		$type = $environment[$mode]['database.type'];
		SESSION::setData("ano", date('Y'));
		SESSION::setData("numdoc","");
		SESSION::setData("login","gener02");
		SESSION::setData("mmotor", $type);
		SESSION::setData("mdate", $dbdate);
		SESSION::setData("merror","1451");
		SESSION::setData("online", true);
		$adapter  = new Auth('model', "class: Gener02", "usuario: {$this->usuario->getUsuario()}", "cedtra: {$this->usuario->getCedtra()}", "estado: A"); 
		$data = array(
			"usuario"  => $this->usuario->getUsuario(), 
			"email"	  => $this->usuario->getEstacion(),
			"nombre"  => $this->usuario->getNombre()
		);
		self::$adapter = $adapter->setActiveIdentity($data); 
	}

	public function getResultado()
	{
		return $this->resultado;
	}

	public function cargar_intentos($usuario)
	{
		$user = $this->db->fetchOne("SELECT * FROM gener02 WHERE estado='A' AND usuario='{$usuario}' LIMIT 1");  
		if($user){
			$intentos = $user['intentos'] + 1;
			if($intentos >= 3)
			{
				$this->Gener02->updateAll("estado='B', intentos='{$intentos}'", "conditions: usuario='{$user['usuario']}'");
				$this->resultado = "El usuario se ha bloqueado, por fallar en la autenticación con más de 3 intentos.";
				$code = 5;
			}else{
				$this->Gener02->updateAll("intentos='{$intentos}'", "conditions: usuario='{$user['usuario']}'");
			}
		}
	}

	public function buscar_usuario($user)
	{
		$this->usuario = $this->Gener02->findFirst("gener02.*", "conditions: estado IN('A','B') AND usuario='{$user}'");
		if(!$this->usuario){
			throw new AuthException("El usuario no es correcto para continuar con la autenticación. 4", 4);
		}
		if($this->usuario->getEstado() == 'B')
		{
			throw new AuthException("El usuario se encuentra bloqueado, por fallar en la autenticación con más de 3 intentos.".
			" Para poder desbloquear su cuenta puede recuperar la cuenta de usuario o solicitar el desbloqueo de su cuenta, ".
			"al aréa de sistemas, soporte_sistemas@comfaca.com.", 5);
		}
		return $this->usuario;
	}

	public function setConnection($db, $gener02)
	{
		$this->db = $db; 
		$this->Gener02 = $gener02;
	}

    /**
     * Obtener el usuario actual después de la autenticación
     */
    public function getUsuario()
    {
        return $this->usuario;
    }
}