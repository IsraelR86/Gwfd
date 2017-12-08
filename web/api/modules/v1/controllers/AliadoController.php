<?php

namespace app\api\modules\v1\controllers;

use Yii;
use app\models\Institucion;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

class AliadoController extends BaseController
{
    public $modelClass = 'app\models\Institucion';

    /**
     * Initializes the object.
     * This method is invoked at the end of the constructor after the object is initialized with the
     * given configuration.
     */
    public function init()
    {
        Yii::$classMap['app\models\Institucion'] = Yii::getAlias('@app') . '/../models/Institucion.php';
        Yii::$classMap['app\models\Usuario'] = Yii::getAlias('@app') . '/../models/Usuario.php';

        return parent::init();
    }

    public function actions()
    {
        $actions = parent::actions();

        unset($actions['index']);
        unset($actions['view']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);

        return $actions;
    }

    public function actionIndex()
    {
        $instituciones = Institucion::find()->orderBy('nombre')->all();

        $result = ArrayHelper::toArray($instituciones, [
                'app\models\Institucion' => [
                    'id',
                    'nombre',
                    'descipcion',
                    'logo' => function($model)
                    {
                        return $model->usuario->byteimagen;
                    },
                ]
            ]);

        return $result;
    }

    public function actionView($id)
    {
        $institucion = Institucion::findOne($id);

        if (empty($institucion)) {
            throw new \yii\web\NotFoundHttpException('Institución no disponible.');
        }

        $result = ArrayHelper::toArray($institucion, [
                'app\models\Institucion' => [
                    'id',
                    'nombre',
                    'descipcion',
                    'logo' => function($model)
                    {
                        return $model->usuario->byteimagen;
                    },
                ]
            ]);

        return $result;
    }
}
