<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * SingupForm is the model behind the login form.
 */
class SingupForm extends Model
{
    public $email;
    public $password;
    public $password_repeat;
    public $nombre_institucion;
    public $nombre;
    public $appat;
    public $apmat;
    public $captcha_code;
    public $acepto_politicas;

    private $_user = false;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            ['captcha_code', 'captcha'],
            [['email', 'password', 'nombre', 'appat', 'apmat', 'nombre_institucion'], 'filter', 'filter' => 'strip_tags'],
            [['email', 'password', 'nombre', 'appat', 'apmat', 'nombre_institucion'], 'app\validators\DelspacesValidator'],
            // email and password are both required
            [['email', 'password', 'nombre', 'appat', 'apmat'], 'required'],
            [['email', 'password', 'nombre', 'appat', 'apmat', 'nombre_institucion'], 'required', 'on' => 'institucion'],
            [['acepto_politicas'], 'required', 'requiredValue' => 1, 'message' => 'Debe aceptar las políticas de privacidad del sitio.'],
            ['acepto_politicas', 'integer'],
            // password is validated by validatePassword()
            ['password', 'string', 'min' => 6],
            ['password', 'compare'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'email' => 'Correo Electrónico',
            'password' => 'Contraseña',
            'password_repeat' => 'Confirmar Contraseña',
            'nombre' => 'Nombre',
            'nombre_institucion' => 'Nombre de la Institución',
            'appat' => 'Apellido Paterno',
            'apmat' => 'Apellido Materno',
            'captcha_code' => 'Código de verificación',
            'acepto_politicas' => 'Acepto las politicas de privacidad. '
        ];
    }
}
