<?php

namespace app\api\modules\v1\controllers;

use Yii;

class SiteController extends BaseController
{
    /**
     * @inheritdoc
     */
    public function actions()
    {
        $actions = parent::actions();

        // Se elimina el método por default implementado por la clase
        // y se implementa uno personalizado
        unset($actions['index']);

        return $actions;
    }

    public function actionIndex()
    {
        return [
            'app' => Yii::$app->params['title'],
            'api' => 'rest',
            'version' => 'v1'
        ];
    }
    
    public function actionError()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $exception = Yii::$app->errorHandler->exception;
        if ($exception !== null) {
            return ['code' => $exception->getCode(), 'message' => $exception->getMessage()];
        }
    }

}
