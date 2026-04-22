<?php

namespace App\Services\SftpTools;

use Exception;
use phpseclib3\Crypt\PublicKeyLoader;
use phpseclib3\Net\SFTP;

class SftpService
{
	private $service;

	public function __construct(SftpClisisu $sftp_service)
	{
		$this->service = $sftp_service;
	}

	protected function connect(): SFTP
	{
		$sftp = new SFTP(
			$this->service->host,
			$this->service->port,
			$this->service->timeout
		);

		$auth = null;
		if (
			$this->service->privateKey !== null &&
			$this->service->privateKey !== ''
		) {
			$keyData = $this->service->privateKey;
			if (is_file($keyData)) {
				$keyData = file_get_contents($keyData);
				if ($keyData === false) {
					throw new Exception('No se pudo leer la llave privada');
				}
			}
			$auth = PublicKeyLoader::load(
				$keyData,
				$this->service->privateKeyPassphrase
			);
		} else {
			$auth = $this->service->password;
		}

		if (!$sftp->login($this->service->username, $auth)) {
			throw new Exception('No fue posible autenticarse por SFTP');
		}

		return $sftp;
	}

	public function upload(string $localPath, string $remotePath): bool
	{
		if (!is_file($localPath)) {
			throw new Exception('El archivo local no existe');
		}

		$sftp = $this->connect();
		$ok = $sftp->put(
			$this->service->storage . $remotePath,
			$localPath,
			SFTP::SOURCE_LOCAL_FILE
		);
		if (!$ok) {
			throw new Exception('Falló la carga por SFTP local:' . $localPath . ', remote:' . $this->service->storage . $remotePath);
		}
		return true;
	}

	public function download(string $remotePath, string $localPath): bool
	{
		$sftp = $this->connect();
		$data = $sftp->get($this->service->storage . $remotePath);
		if ($data === false) {
			throw new Exception('Falló la descarga por SFTP');
		}

		$dir = dirname($localPath);
		if (!is_dir($dir)) {
			if (!mkdir($dir, 0775, true) && !is_dir($dir)) {
				throw new Exception('No se pudo crear el directorio destino');
			}
		}

		if (file_put_contents($localPath, $data) === false) {
			throw new Exception('No se pudo escribir el archivo local');
		}

		return true;
	}

	public function list(string $remoteDir = '.'): array
	{
		$sftp = $this->connect();
		$list = $sftp->nlist($this->service->storage . $remoteDir);
		if ($list === false) {
			throw new Exception('No se pudo listar el directorio remoto');
		}
		return $list;
	}

	public function delete(string $remotePath): bool
	{
		$sftp = $this->connect();
		$ok = $sftp->delete($this->service->storage . $remotePath);
		if (!$ok) {
			throw new Exception('No se pudo eliminar el archivo remoto');
		}
		return true;
	}

	public function mkdir(string $remoteDir, int $mode = 0775, bool $recursive = true): bool
	{
		$sftp = $this->connect();
		$ok = $sftp->mkdir($this->service->storage . $remoteDir, $mode, $recursive);
		if (!$ok) {
			throw new Exception('No se pudo crear el directorio remoto');
		}
		return true;
	}
}
