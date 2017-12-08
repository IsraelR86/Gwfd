<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * RecuperarPassForm is the model behind the recuperar pass form.
 */
class RecuperarPassForm extends Model
{
    public $email;
    public $captcha_code;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            ['captcha_code', 'captcha'],
            ['email', 'required'],
            ['email', 'email'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'email' => 'E-mail',
            'captcha_code' => 'Código de verificación',
        ];
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = Usuario::findByUsername($this->email);
        }

        return $this->_user;
    }
}
