<?php

namespace App\Services\SftpTools;

class SftpClisisu
{
    public string $host;
    public int $port;
    public string $username;
    public ?string $password;
    public ?string $privateKey;
    public ?string $privateKeyPassphrase;
    public int $timeout;
    public string $storage;

    public function __construct()
    {
        $this->host = config('app.sftp_sisu.host') ?? '';
        $this->username = config('app.sftp_sisu.username') ?? '';
        $this->password = config('app.sftp_sisu.password') ?? '';
        $this->privateKey = config('app.sftp_sisu.privateKey') ?? '';
        $this->privateKeyPassphrase = config('app.sftp_sisu.privateKeyPassphrase') ?? '';
        $this->port = config('app.sftp_sisu.port') ?? 22;
        $this->timeout = config('app.sftp_sisu.timeout') ?? 30;
        $this->storage = config('app.sftp_sisu.storage') ?? '';
    }
}
