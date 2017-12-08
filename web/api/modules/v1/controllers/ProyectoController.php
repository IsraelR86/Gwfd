<?php

namespace app\api\modules\v1\controllers;

use Yii;
use app\models\Proyecto;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

class ProyectoController extends BaseController
{
    public $modelClass = 'app\models\Proyecto';

    /**
     * Initializes the object.
     * This method is invoked at the end of the constructor after the object is initialized with the
     * given configuration.
     */
    public function init()
    {
        Yii::$classMap['app\models\Proyecto'] = Yii::getAlias('@app') . '/../models/Proyecto.php';
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
        $limit = 15;
        $offset = $limit * Yii::$app->request->get('page', 0);
        $proyectos = Proyecto::find()
            ->orderBy('nombre')
            ->limit($limit)
            ->offset($offset)
            ->all();

        $result = ArrayHelper::toArray($proyectos, [
                'app\models\Proyecto' => [
                    'id',
                    'proyecto' => 'nombre',
                    'contenido' => 'descripcion',
                    'video' => 'url_video',
                    'logo' => function($model)
                    {
                        return $model->bytelogo;
                    },
                    'imagen' => function($model)
                    {
                        return $model->byteimagen;
                    },
                ]
            ]);

        return $result;
    }

    public function actionView($id)
    {
        $proyecto = Proyecto::findOne($id);

        if (empty($proyecto)) {
            throw new \yii\web\NotFoundHttpException('Proyecto no disponible.');
        }

        $result = ArrayHelper::toArray($proyecto, [
                'app\models\Proyecto' => [
                    'id',
                    'proyecto' => 'nombre',
                    'contenido' => 'descripcion',
                    'video' => 'url_video',
                    'logo' => function($model)
                    {
                        return $model->bytelogo;
                    },
                    'imagen' => function($model)
                    {
                        return $model->byteimagen;
                    },
                ]
            ]);

        return $result;
    }
}
