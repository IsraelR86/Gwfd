<?php

namespace app\api\modules\v1\controllers;

use Yii;
use app\models\Noticia;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

class NoticiaController extends BaseController
{
    public $modelClass = 'app\models\Noticia';

    /**
     * Initializes the object.
     * This method is invoked at the end of the constructor after the object is initialized with the
     * given configuration.
     */
    public function init()
    {
        Yii::$classMap['app\models\Noticia'] = Yii::getAlias('@app') . '/../models/Noticia.php';
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
        $limit = 15;
        $offset = $limit * Yii::$app->request->get('page', 0);
        $noticias = Noticia::find()
            ->orderBy('fecha')
            ->where(['activo' => '1'])
            ->limit($limit)
            ->offset($offset)
            ->all();

        $result = ArrayHelper::toArray($noticias, [
                'app\models\Noticia' => [
                    'id',
                    'titulo',
                    'fecha',
                    'autor',
                    'resumen',
                    'portada' => 'bytePortada',
                    'etiquetas',
                    /*'link' => function($model)
                    {
                        $link = str_replace('api/v1/', '', Url::toRoute(['noticia/view', 'id' => $model->id], true));

                        return str_replace('/noticias/', '/noticia/', $link);
                    },*/
                ]
            ]);

        return $result;
    }

    public function actionView($id)
    {
        $noticia = Noticia::findOne($id);

        if (empty($noticia)) {
            throw new \yii\web\NotFoundHttpException('Noticia no disponible.');
        }

        $result = ArrayHelper::toArray($noticia, [
                'app\models\Noticia' => [
                    'id',
                    'titulo',
                    'fecha',
                    'autor',
                    'resumen',
                    'contenido',
                    'portada' => 'bytePortada',
                    'etiquetas',
                    /*'link' => function($model)
                    {
                        $link = str_replace('api/v1/', '', Url::toRoute(['noticia/view', 'id' => $model->id], true));

                        return str_replace('/noticias/', '/noticia/', $link);
                    },*/
                ]
            ]);

        return $result;
    }
}
