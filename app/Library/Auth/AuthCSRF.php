<?php

namespace App\Library\Auth;

use App\Exceptions\AuthException;

class AuthCSRF
{
    public function __construct() {}

    /**
     * initialize function
     *
     * @changed [2023-12-00]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     *
     * @return bool
     */
    public static function Valid()
    {
        try {
            $headers = getallheaders();
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method');
            header('Access-Control-Allow-Methods: POST');
            header('Allow: POST');
            header('Content-type: application/json; charset=utf-8');

            $method = $_SERVER['REQUEST_METHOD'];
            if ($method == 'OPTIONS') {
                exit();
            }

            $authorization = (isset($headers['Authorization'])) ? $headers['Authorization'] : null;
            if (! $authorization) {
                throw new AuthException('La autenticación requiere de Authorization Token', 404);
            }

            if (preg_match('/^bearer/i', $authorization)) {
                $token = trim(preg_replace('/^bearer/i', '', $authorization));

                if (Session::get('CSRF_TOKEN') !== $token) {
                    throw new AuthException('No es correcto el usuario para su ingreso.', 501);
                }
            } else {
                throw new AuthException('No es correcto el Bearer Token', 501);
            }

            return true;
        } catch (AuthException $err) {
            echo json_encode([
                'success' => false,
                'msj' => 'Error usuario no autorizado, el token ya ha caducado para hacer la solicitud. Recarga la página y vuelve a intentar el envío.',
                'errors' => $err->getMessage(),
            ]);
            exit();
        }
    }

    public static function Init()
    {
        Session::unsetData('CSRF_TOKEN');
        View::setViewParam('csrf_token', self::Change());
    }

    public static function getCSRF()
    {
        $csrf_token = Session::get('CSRF_TOKEN');

        return $csrf_token;
    }

    public static function Change()
    {
        $pass = '';
        $seed = str_split('abcdefghijklmnopqrstuvwxyz1234567890');
        shuffle($seed);
        foreach (array_rand($seed, 36) as $k) {
            $pass .= $seed[$k];
        }
        Session::set('CSRF_TOKEN', $pass);

        return $pass;
    }
}
