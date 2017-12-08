<?php

namespace app\api\modules\v1;

/**
 * api module definition class
 */
class Api extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    //public $controllerNamespace = 'app\modules\rest\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
        //\Yii::configure($this, require(__DIR__ . '/../../config/config.php'));
    }
}
