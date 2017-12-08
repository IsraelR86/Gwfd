<?php

namespace app\helpers;

use Yii;

class Security
{
    /**
     * Crea una contraseña aleatoria
     *
     * @param int $longitud
     * @return string Contraseña generada
     */
    public static function createPassword($longitud = 8)
    {
        $cadena = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
        $longitudCadena = strlen($cadena);
        $pass = '';

        for($i=1; $i<=$longitud; $i++) {
            $pos = rand(0, $longitudCadena-1);
            $pass .= substr($cadena, $pos, 1);
        }

        return $pass;
    }
    
    /**
     * Crea token aleatorio unico
     *
     * @return string Token generado
     */
    public static function createToken()
    {
        return self::encode(uniqid(rand(), true));
    }

    /**
     * Encripta la cadena con MD5
     * 
     * @param string $cadena
     * @return string Cadena encriptada
     */
    public static function encode($cadena)
    {
        return md5($cadena);
    }
}