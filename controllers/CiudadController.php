<?php

namespace app\controllers;

use Yii;
use app\models\Ciudad;
use yii\helpers\ArrayHelper;

class CiudadController extends \yii\web\Controller
{
    public function actionGetbyestado()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        // Permite Cross Domain Requests
        Yii::$app->response->headers->add('Access-Control-Allow-Origin', '*');
        
        $ciudades = Ciudad::find()
            ->select('id, descripcion')
            ->where('id_estado = :estado', [':estado' => Yii::$app->request->post('estado') ? Yii::$app->request->post('estado') : Yii::$app->request->get('estado')])
            ->orderBy('descripcion')
            ->all();
        
        //return ArrayHelper::map($ciudades, 'id', 'descripcion');
        return $ciudades;
    }

}
