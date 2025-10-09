<?php

require_once __DIR__.'/Firebase/JWT.php';

class JwtManager
{
    private $token_key;

    private $token_encrypt = 'HS384';

    private $token_expire = 3600;

    /**
     * Create function
     *
     * @param  array  $data
     * @return void
     */
    public function create($data)
    {
        if (is_null($this->token_expire)) {
            return false;
        }
        if (is_null($this->token_key)) {
            return false;
        }
        $time = time();
        $token = [
            'iat' => $time,
            'exp' => $time + $this->token_expire,
            'aud' => $this->cliente(),
            'data' => base64_encode(json_encode($data)),
        ];

        return Firebase\JWT::encode($token, $this->token_key, $this->token_encrypt);
    }

    /**
     * check function
     *
     * @param  string  $token
     * @return bool
     */
    public function check($token)
    {
        if (is_null($this->token_expire)) {
            return false;
        }
        if (is_null($this->token_key)) {
            return false;
        }
        if (empty($token)) {
            return false;
        } else {
            Firebase\JWT::$leeway = 60;
            $decode = Firebase\JWT::decode($token, $this->token_key, [$this->token_encrypt]);
            if ($decode->aud == $this->cliente()) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function show($token)
    {
        if (is_null($this->token_expire)) {
            return false;
        }
        if (is_null($this->token_key)) {
            return false;
        }
        $decode = Firebase\JWT::decode(
            $token,
            $this->token_key,
            [$this->token_encrypt]
        );

        return json_decode(base64_decode($decode->data));
    }

    private function cliente()
    {
        $token_auth = '';
        if (! empty($_SERVER['HTTP_CLIENT_IP'])) {
            $token_auth = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (! empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $token_auth = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $token_auth = $_SERVER['REMOTE_ADDR'];
        }
        $token_auth .= @$_SERVER['HTTP_USER_AGENT'];
        $token_auth .= gethostname();

        return sha1($token_auth);
    }

    /**
     * setTokenKey function
     *
     * @param [type] $token_key
     * @return void
     */
    public function setTokenKey($token_key)
    {
        $this->token_key = $token_key;
    }

    /**
     * setTokenEncrypt function
     *
     * @param [type] $token_encrypt
     * @return void
     */
    public function setTokenEncrypt($token_encrypt)
    {
        $this->token_encrypt = $token_encrypt;
    }

    /**
     * setTokenExpire function
     *
     * @param [type] $token_expire
     * @return void
     */
    public function setTokenExpire($token_expire)
    {
        $this->token_expire = $token_expire;
    }
}
