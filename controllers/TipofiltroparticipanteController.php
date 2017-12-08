<?php

namespace app\controllers;

use Yii;
use app\models\TipoFiltroParticipante;
use app\models\Estado;
use app\helpers\Functions;
use yii\helpers\ArrayHelper;

class TipofiltroparticipanteController extends \yii\web\Controller
{
    public function actionGetall()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $filtros = TipoFiltroParticipante::find()->all();
        $arrayFiltros = [];
        
        // Se hardcodea las opciones de respuesta de los tipos de filtros 
        // porque no se tiene otra forma de conocer las posibles respuestas
        foreach ($filtros as $filtro) {
            $opciones = null;
            
            switch($filtro->id) {
                // 2 Genero
                case 2:
                    $opciones = Functions::arrayToObject(Yii::$app->params['genero'], 'id', 'descripcion');
                    break;
                // 3 Nivel Educativo
                case 3:
                    $opciones = Functions::arrayToObject(Yii::$app->params['nivel_educativo'], 'id', 'descripcion');
                    break;
                // 4 Estado Nacimiento
                // 5 Estado Residencia
                case 4:
                case 5:
                    $opciones = Estado::find()->asArray()->all();
                    break;
                // 6 Estado Civil
                case 6:
                    $opciones = Functions::arrayToObject(Yii::$app->params['estado_civil'], 'id', 'descripcion');
                    break;
            }
            
            $arrayFiltros[] = [
                'id' => $filtro->id,
                'descripcion' => $filtro->descripcion,
                'opciones' => $opciones
            ];
        }
        
        return ['filtros' => $arrayFiltros];
    }

}
