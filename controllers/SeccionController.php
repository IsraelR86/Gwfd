<?php

namespace app\controllers;

use Yii;
use app\models\Seccion;
use app\models\Etiqueta;
use app\models\PreguntaXConcurso;
use yii\helpers\ArrayHelper;

class SeccionController extends \yii\web\Controller
{
    public function actionGetall()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $secciones = ArrayHelper::toArray(Seccion::getAll(), [
                        'app\models\Seccion' => [
                            'id',
                            'descripcion',
                            'preguntas',
                        ],
                        'app\models\Pregunta' => [
                            'id',
                            'descripcion',
                            'tipoPregunta',
                            'opcionesMultiple',
                            'ayuda',
                            'pagina',
                        ],
                        'app\models\TipoPregunta' => [
                            'id',
                            'descripcion',
                            'tipoFiltros',
                        ],
                        'app\models\OpcionMultiple' => [
                            'id',
                            'descripcion',
                        ],
                        'app\models\TipoFiltro' => [
                            'id',
                            'descripcion',
                        ],
                    ]);

        if (Yii::$app->request->get('preguntaGroupByPagina')) {
            foreach ($secciones as $index => &$seccion) {
                $cache_preguntas = $seccion['preguntas'];
                unset($seccion['preguntas']);

                $paginas = [];
                foreach ($cache_preguntas as $index => $pregunta) {
                    $paginas[$pregunta['pagina']]['preguntas'][] = $pregunta;
                }

                $seccion['paginas'] = array_values($paginas);
            }
        }

        if (Yii::$app->request->get('include_preguntas_concurso')) {
          $preguntas_concurso = ArrayHelper::toArray(PreguntaXConcurso::find()
                                      ->where(['id_concurso' => Yii::$app->request->get('id_concurso')])
                                      ->all(), [
                                        'app\models\PreguntaXConcurso' => [
                                            'id',
                                            'descripcion',
                                            'tipoPregunta',
                                            'ayuda',
                                        ],
                                        'app\models\TipoPregunta' => [
                                            'id',
                                            'descripcion',
                                        ],
                                      ]);
          array_push($secciones, [
            'id' => count($secciones) + 1,
            'descripcion' => 'Preguntas especificias del concurso',
            'preguntas' => $preguntas_concurso,
          ]);
        }

        return [
            'secciones' => $secciones,
            'etiquetas' => Etiqueta::find()->all(),
        ];
    }

}
