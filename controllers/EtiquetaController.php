<?php

namespace app\controllers;

use Yii;
use app\models\Etiqueta;

use yii\helpers\ArrayHelper;

class EtiquetaController extends \yii\web\Controller
{
    public function actionGetall()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $etiquetas = Etiqueta::find()->all();
        
        return $etiquetas;
    }

}
