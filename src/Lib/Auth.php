<?php

namespace App\Lib;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Auth {
    // Clave secreta para firmar los tokens JWT
    private static $secret_key = 'mi_clave_secreta_segura_123!@#'; 
    // Algoritmo de encriptación
    private static $encrypt = ['HS256'];
    // Identificador único del usuario
    private static $aud = null;
    // Duración del token en minutos (6 meses)
    private static $minutes = 259200;

    /**
     * Genera una identificación única para el usuario.
     * 
     * @return string
     */
    private static function Aud() {
        $aud = '';

        // Obtener la IP del cliente
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $aud = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $aud = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $aud = $_SERVER['REMOTE_ADDR'];
        }

        // Añadir información del agente de usuario y el nombre del host
        $aud .= @$_SERVER['HTTP_USER_AGENT'];
        $aud .= gethostname();

        // Crear un hash SHA-1 de la información combinada
        return sha1($aud);
    }

    /**
     * Crea un token con duración predeterminada y datos suministrados.
     * 
     * @param array $data
     * @return string
     */
    public static function addToken($data) {
        $time = time();
        // Información del token
        $token = [
            'exp' => $time + (60 * self::$minutes), // Tiempo de expiración
            'aud' => self::Aud(), // Identificación única del usuario
            'data' => $data // Datos del usuario (incluye id, nombre y apellido)
        ];

        // Codificar el token usando la clave secreta y el algoritmo de encriptación
        return JWT::encode($token, self::$secret_key, self::$encrypt[0]);
    }

    /**
     * Crea un token para recuperación de contraseña con duración de 24 horas.
     * 
     * @param array $data
     * @return string
     */
    public static function tokRecPass($data) {
        $time = time();
        // Información del token
        $token = [
            'exp' => $time + (60 * 60 * 24), // Tiempo de expiración (24 horas)
            'aud' => self::Aud(), // Identificación única del usuario
            'data' => $data // Datos del usuario
        ];

        // Codificar el token usando la clave secreta y el algoritmo de encriptación
        return JWT::encode($token, self::$secret_key, self::$encrypt[0]);
    }

    /**
     * Crea un token para registro con duración de 1 hora.
     * 
     * @param array $data
     * @return string
     */
    public static function TokReg($data) {
        $time = time();
        // Información del token
        $token = [
            'exp' => $time + (60 * 60), // Tiempo de expiración (1 hora)
            'aud' => self::Aud(), // Identificación única del usuario
            'data' => $data // Datos del usuario
        ];

        // Codificar el token usando la clave secreta y el algoritmo de encriptación
        return JWT::encode($token, self::$secret_key, self::$encrypt[0]);
    }

    /**
     * Decodifica y obtiene los datos del token.
     * 
     * @param string $token
     * @return mixed
     */
    public static function decData($token) {
        // Decodificar el token
        $data = self::decodeToken($token);
        if ($data) {
            return $data->data; // Retornar los datos del usuario
        } else {
            return null;
        }
    }

    /**
     * Valida si el token es válido.
     * 
     * @param string $token
     * @return bool
     */
    public static function validateToken($token) {
        if (empty($token) || $token == NULL) {
            return false;
        }
        // Decodificar el token
        $decode = self::decodeToken($token);
        if ($decode === NULL) {
            return false;
        }
        // Verificar si el token ha expirado
        if ($decode->exp <= time()) {
            return false;
        }
        return true;
    }

    /**
     * Decodifica un token JWT.
     * 
     * @param string $token
     * @return mixed
     */
    private static function decodeToken($token) {
        try {
            // Decodificar el token usando la clave secreta y el algoritmo de encriptación
            return JWT::decode($token, new Key(self::$secret_key, self::$encrypt[0]));
        } catch (\Exception $e) {
            return null;
        }
    }

}
