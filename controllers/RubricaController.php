<?php

namespace app\controllers;

use Yii;
use app\models\Rubrica;
use app\models\PreguntaXRubrica;
use app\models\Seccion;
use app\models\Pregunta;
use app\models\Evaluaciones;
use app\helpers\Functions;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\PreguntasConcursoXRubrica;
use app\models\RespuestaConcurso;

class RubricaController extends \yii\web\Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['getbyconcurso', 'setpreguntas', 'getrubricas', 'getrubricasevaluar', 'setevaluacion','downloaddocumento'],
                        'allow' => true,
                        'roles' => ['@']
                    ],
                ],
            ],
        ];
    }

    public function actionGetbyconcurso()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $rubricas = ArrayHelper::toArray(Rubrica::find()->where(['id_concurso' => Yii::$app->request->post('id')])->all(), [
                        'app\models\Rubrica' => [
                            'id',
                            'nombre',
                            'descripcion',
                            'preguntas',
                            'preguntasConcurso'
                        ],
                        'app\models\Pregunta' => [
                            'id'
                        ],

                        'app\models\PreguntaXConcurso' => [
                            'id'
                        ]
                    ]);

        return $rubricas;
    }

    public function actionSetpreguntas()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $transaction = Yii::$app->db->beginTransaction();
        $preguntas = Yii::$app->request->post('preguntas');
        $preguntasConcurso = Yii::$app->request->post('preguntasConcurso');
        $result = ['error' => false, 'message' => 'Datos guardados exitosamente'];

        try {
            PreguntaXRubrica::deleteAll(['id_rubrica' => Yii::$app->request->post('id')]);

            if(!empty($preguntas))
            {
              foreach($preguntas as $pregunta) {
                  $preguntaRubrica = new PreguntaXRubrica();
                  $preguntaRubrica->id_rubrica = Yii::$app->request->post('id');
                  $preguntaRubrica->id_pregunta = $pregunta['id'];

                  $preguntaRubrica->save();

                  if ($preguntaRubrica->errors) {
                      throw new \Exception(Functions::errorsToString($filtroConcurso->errors));
                  }
              }
            }

            PreguntasConcursoXRubrica::deleteAll(['id_rubrica' => Yii::$app->request->post('id')]);

            if(!empty($preguntasConcurso))
            {
              foreach($preguntasConcurso as $preguntaConcurso) {
                  $preguntaRubricaConcurso = new PreguntasConcursoXRubrica();
                  $preguntaRubricaConcurso->id_rubrica = Yii::$app->request->post('id');
                  $preguntaRubricaConcurso->id_pregunta_concurso = $preguntaConcurso['id'];

                  $preguntaRubricaConcurso->save();

                  if ($preguntaRubricaConcurso->errors) {
                      throw new \Exception(Functions::errorsToString($filtroConcurso->errors));
                  }
              }
            }

            $transaction->commit();
        } catch(\Exception $e) {
            $transaction->rollBack();

            $result = ['error' => true, 'message' => $e->getMessage()];
        }

        return $result;
    }

    public function actionGetrubricas()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $id_concurso = Yii::$app->request->post('id_concurso');


        $rubricas = ArrayHelper::toArray(Rubrica::getRubricasXConcurso($id_concurso),[
                        'app\models\Rubrica' => [
                            'id',
                            'nombre',
                            'descripcion'
                        ]
                    ]);

        //$rubricas = Rubrica::getRubricasXConcurso($id_concurso);

        return $rubricas;
    }

    public function actionGetrubricasevaluar()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $result = [
            'rubricas' => []
        ];

        $rubricas = Rubrica::find()
            ->where(['id_concurso' => Yii::$app->request->post('concurso')])
            ->orderBy('id ASC')
            ->all();

        if (count($rubricas)) {
            $countRubrica = 0;

            foreach ($rubricas as $rubrica) {
                $evaluacion = $rubrica->getEvaluacion(Yii::$app->request->post('proyecto'), Yii::$app->user->id);

                $result['rubricas'][$countRubrica] = [
                    'id' => $rubrica->id,
                    'nombre' => $rubrica->nombre,
                    'descripcion' => $rubrica->descripcion,
                    'calificacion_minima' => $rubrica->calificacion_minima,
                    'calificacion_maxima' => $rubrica->calificacion_maxima,
                    'opciones_calificacion' => [],
                    'secciones' => [],
                    'calificacion' => $evaluacion ? $evaluacion->calificacion : null,
                    'comentarios' => $evaluacion ? $evaluacion->comentarios : null,
                ];

                for ($i=$rubrica->calificacion_minima; $i<=$rubrica->calificacion_maxima; $i++) {
                    $result['rubricas'][$countRubrica]['opciones_calificacion'][] = ['calificacion' => $i];
                }

                $seccionesPreguntas = Seccion::getFromRubrica($rubrica->id);
                if (count($seccionesPreguntas)) {
                    foreach ($seccionesPreguntas as $seccion) {
                      //Se comento esta parte mientras evaluan los de FESE
                      //  $result['rubricas'][$countRubrica]['secciones'][$seccion['id']] = [
                        //    'id' => $seccion['id'],
                          //  'descripcion' => $seccion['descripcion'],
                          //  'preguntas' => [],
                      //  ];

                        $preguntas = Pregunta::getFromRubricaSeccion($rubrica->id, $seccion['id']);

                        foreach ($preguntas as $pregunta) {
                            $respuesta = $pregunta->getRespuestaConcurso(Yii::$app->request->post('proyecto'), Yii::$app->request->post('concurso'));


                          //  $result['rubricas'][$countRubrica]['secciones'][$seccion['id']]['preguntas'][] = [
                            //    'id' => $pregunta->id,
                              //  'descripcion' => $pregunta->descripcion,
                            //    'tipo_pregunta' => $pregunta->tipo_pregunta,
                            //    'respuesta' => $pregunta->getRespuestaConcursoToText(Yii::$app->request->post('proyecto'), Yii::$app->request->post('concurso'),Yii::$app->request->post('proyecto')),
                            //    'respuesta_geografica' => $respuesta->respuesta_geografica,
                          //  ];
                        }
                    }

                    ArrayHelper::multisort($seccionesPreguntas, ['id'], [SORT_DESC]);
                    $idSeccionPreguntasEspecificas = $seccionesPreguntas[0]['id'];
                    $idSeccionPreguntasEspecificas++;

                  $result['rubricas'][$countRubrica]['secciones'][$idSeccionPreguntasEspecificas] = [
                        'id' => $idSeccionPreguntasEspecificas,
                        //'descripcion' => "preguntas Especificas",
                        'descripcion' => "PLAN DE NEGOCIO",
                        'preguntas' => [],
                    ];


                    $preguntasEspecificas = PreguntasConcursoXRubrica::getEspecificasFromRubrica($rubrica->id,Yii::$app->request->post('concurso'));

                    foreach ($preguntasEspecificas as $preguntaEspecifica) {
                     $respuestaEspecifica = $preguntaEspecifica->getRespEspecificaFromConcurso($preguntaEspecifica->id,Yii::$app->request->post('proyecto'));

                     $result['rubricas'][$countRubrica]['secciones'][$idSeccionPreguntasEspecificas]['preguntas'][] = [
                         'id' => $preguntaEspecifica->id,
                         'descripcion' => $preguntaEspecifica->descripcion,
                         'id_tipo_pregunta_concurso' => $preguntaEspecifica->id_tipo_pregunta_concurso,
                         'respuesta' => $preguntaEspecifica->getRespuestaEspecificaConcursoToText($preguntaEspecifica->id,Yii::$app->request->post('concurso'),Yii::$app->request->post('proyecto')),
                         'respuesta_geografica' => $respuesta->respuesta_geografica,
                     ];
                    }
                }

                $countRubrica++;
            }
        }

        return $result;
    }

    public function actionSetevaluacion()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $result = ['error' => false, 'mensaje' => ''];

        $evaluacion = Evaluaciones::find()
            ->where('id_rubrica = :id_rubrica', [':id_rubrica' => Yii::$app->request->post('rubrica')])
            ->andWhere('id_proyecto = :id_proyecto', [':id_proyecto' => Yii::$app->request->post('proyecto')])
            ->andWhere('id_concurso = :id_concurso', [':id_concurso' => Yii::$app->request->post('concurso')])
            ->andWhere('id_evaluador = :id_evaluador', [':id_evaluador' => Yii::$app->user->id])
            ->one();

        if (empty($evaluacion)) {
            $evaluacion = new Evaluaciones();
            $evaluacion->id_rubrica = Yii::$app->request->post('rubrica');
            $evaluacion->id_proyecto = Yii::$app->request->post('proyecto');
            $evaluacion->id_concurso = Yii::$app->request->post('concurso');
            $evaluacion->id_evaluador = Yii::$app->user->id;
        }

        $evaluacion->calificacion = Yii::$app->request->post('calificacion');
        $evaluacion->comentarios = Yii::$app->request->post('comentarios');
        $evaluacion->fecha = date('Y-m-d H:m:s');

        if (!$evaluacion->save()) {
            $result['error'] = true;
            $result['mensaje'] = $evaluacion->errors;
        }

        return $result;
    }

    public function actionDownloaddocumento()
    {
      $respuestaC = RespuestaConcurso::findOne(['id_concurso' => Yii::$app->request->post('concurso'),'id_proyecto' => Yii::$app->request->post('proyecto'), 'id_pregunta' => Yii::$app->request->post('pregunta')]);

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $headers = Yii::$app->response->headers;

        if ($respuestaC == null) {
            Yii::$app->response->statusCode = 404;
            Yii::$app->response->data = 'Archivo no encontrado';
            $headers->add('Content-Type', 'text/xml; charset=utf-8');
            return Yii::$app->response;
        }

        if ($respuestaC->downloadArchivoRespuesta() == null) {
            Yii::$app->response->statusCode = 404;
            Yii::$app->response->content = 'Archivo no encontrado';
            $headers->add('Content-Type', 'text/xml; charset=utf-8');
            return Yii::$app->response;
        }

        return Yii::$app->response->sendFile($respuestaC->downloadArchivoRespuesta(), $respuestaC->getNombreArchivo());
    }

}
