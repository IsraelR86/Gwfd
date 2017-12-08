<?php

namespace app\api\modules\v1\controllers;

use Yii;
use app\models\Usuario;

class UsuarioController extends BaseController
{
    public $modelClass = 'app\models\Usuario';

    /**
     * Initializes the object.
     * This method is invoked at the end of the constructor after the object is initialized with the
     * given configuration.
     */
    public function init()
    {
        Yii::$classMap['app\models\TipoUsuario'] = Yii::getAlias('@app') . '/../models/TipoUsuario.php';

        return parent::init();
    }
}
