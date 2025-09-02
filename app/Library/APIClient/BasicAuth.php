<?php
namespace App\Library\APIClient;

class BasicAuth implements AuthClientInterface
{
    private $username;
    private $password;
    private $token;

    public function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    public function encript($encryption, $portal_clave)
    {
        $iv = "";
        $seed = str_split('1234567890123456');
        shuffle($seed);
        foreach (array_rand($seed, 16) as $k) $iv .= $seed["$k"];
        $encryptedData = openssl_encrypt($this->password, $encryption, $portal_clave, 0, $iv);
        $this->password = "{$encryptedData}||{$iv}";
    }

    public function authenticate()
    {
        $this->token = base64_encode("$this->username:$this->password");
        return $this->token;
    }

    /**
     * procesaRequest function
     * @param [string] $result
     * @return array
     */
    public function procesaRequest($result)
    {
        if ($result == '' || is_null($result)) return false;

        $macth = preg_match('/^\{(.*)\:(.*)\}$/', trim($result));
        if ($macth === 0 || $macth === false) {
            $macth = preg_match('/^\[(.*)\:(.*)\]$/', trim($result));
        }
        if ($macth === 0 || $macth === false) {
            return array(
                "success" => false,
                "response" => array(),
                "errors" => "Error:\n{$result}"
            );
        } else {
            return json_decode($result, true);
        }
    }

    public function getHeader($autenticar = 0)
    {
        if ($autenticar == true) {
            return array('Content-Type: application/x-www-form-urlencoded');
        } else {
            return array(
                "Content-Type: application/json",
                "Authorization: Basic {$this->token}"
            );
        }
    }
}
