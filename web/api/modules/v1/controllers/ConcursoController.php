<?php

namespace app\api\modules\v1\controllers;

use Yii;
use app\models\Concurso;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

class ConcursoController extends BaseController
{
    public $modelClass = 'app\models\Concurso';

    /**
     * Initializes the object.
     * This method is invoked at the end of the constructor after the object is initialized with the
     * given configuration.
     */
    public function init()
    {
        Yii::$classMap['app\models\Institucion'] = Yii::getAlias('@app') . '/../models/Institucion.php';
        Yii::$classMap['app\models\Etiqueta'] = Yii::getAlias('@app') . '/../models/Etiqueta.php';

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
        $institucion = Yii::$app->request->get('institucion');
        $etiquetas = Yii::$app->request->get('etiquetas');
        $page = Yii::$app->request->get('page', 0);
        $concursos = [];

        if ($etiquetas) {
            $etiquetas = explode(',', $etiquetas);
        }

        if ($institucion) {
            $concursos = Concurso::getAllByInstitucion($institucion, $page, 10, ['cancelado' => '0'], $etiquetas);
        } else {
            $concursos = Concurso::getAllAvailables($page, 10, $etiquetas);
        }

        $result = ArrayHelper::toArray($concursos, [
                'app\models\Concurso' => [
                    'id',
                    'concurso' => 'nombre',
                    'contenido' => 'descripcion',
                    'premios',
                    'fecha_arranque',
                    'fecha_cierre',
                    'fecha_resultados',
                    'link' => function ($model)
                    {
                        $link = str_replace('api/v1/', '', Url::toRoute(['concurso/view', 'id' => $model->id], true));

                        return str_replace('/concursos/', '/concurso/', $link);
                    },
                    'fuente'  => function ($model)
                    {
                        return $model->byteImagen;
                    },
                    'institucion',
                    'etiquetas',
                ],
                'app\models\Institucion' => [
                    'id',
                    'nombre',
                ],
                'app\models\Etiqueta' => [
                    'id',
                    'descripcion',
                ],
            ]);

        return $result;
    }

    public function actionView($id)
    {
        $concurso = Concurso::findOne($id);

        if (empty($concurso)) {
            throw new \yii\web\NotFoundHttpException('Concurso no disponible.');
        }

        $result = ArrayHelper::toArray($concurso, [
                'app\models\Concurso' => [
                    'id',
                    'concurso' => 'nombre',
                    'contenido' => 'descripcion',
                    'premios',
                    'fecha_arranque',
                    'fecha_cierre',
                    'fecha_resultados',
                    'link' => function ($model)
                    {
                        $link = str_replace('api/v1/', '', Url::toRoute(['concurso/view', 'id' => $model->id], true));

                        return str_replace('/concursos/', '/concurso/', $link);
                    },
                    'fuente'  => function ($model)
                    {
                        return $model->byteImagen;
                    },
                    'institucion',
                    'etiquetas',
                ],
                'app\models\Institucion' => [
                    'id',
                    'nombre',
                ],
                'app\models\Etiqueta' => [
                    'id',
                    'descripcion',
                ],
            ]);

        return $result;
    }
}
