<?php

namespace App\Lib;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Auth {
    private static $secret_key = 'tu_clave_secreta'; // Cambia esto por una clave secreta segura
    private static $encrypt = ['HS256'];
    private static $aud = null;
    private static $minutes = 259200;  // Aumente la expiración del token a 6 meses.

    private static function Aud() {
        $aud = '';

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $aud = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $aud = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $aud = $_SERVER['REMOTE_ADDR'];
        }

        $aud .= @$_SERVER['HTTP_USER_AGENT'];
        $aud .= gethostname();

        return sha1($aud);
    }

    public static function addToken($data) {
        $time = time();
        $token = [
            'exp' => $time + (60 * self::$minutes),
            'aud' => self::Aud(),
            'data' => $data
        ];

        return JWT::encode($token, self::$secret_key, self::$encrypt[0]);
    }

    public static function tokRecPass($data) {
        $time = time();
        $token = [
            'exp' => $time + (60 * 60 * 24),
            'aud' => self::Aud(),
            'data' => $data
        ];

        return JWT::encode($token, self::$secret_key, self::$encrypt[0]);
    }

    public static function TokReg($data) {
        $time = time();
        $token = [
            'exp' => $time + (60 * 60),
            'aud' => self::Aud(),
            'data' => $data
        ];

        return JWT::encode($token, self::$secret_key, self::$encrypt[0]);
    }

    public static function decData($token) {
        $data = self::decodeToken($token);
        if ($data) {
            return $data->data;
        } else {
            return null;
        }
    }

    public static function validateToken($token) {
        if (empty($token) || $token == NULL) {
            return false;
        }
        $decode = self::decodeToken($token);
        if ($decode === NULL) {
            return false;
        }
        if ($decode->exp <= time()) {
            return false;
        }
        return true;
    }

    private static function decodeToken($token) {
        try {
            return JWT::decode($token, new Key(self::$secret_key, self::$encrypt[0]));
        } catch (\Exception $e) {
            return null;
        }
    }

    public static function obtenerIP() {
        $ip = '';

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        return $ip;
    }
}
