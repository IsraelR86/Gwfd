<?php

namespace app\controllers;

use Yii;
use app\models\FiltroConcurso;
use app\helpers\Functions;
use yii\helpers\ArrayHelper;

class FiltroconcursoController extends \yii\web\Controller
{
    public function actionSetfiltros()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $transaction = Yii::$app->db->beginTransaction();
        $filtros = Yii::$app->request->post('filtros');
        $result = ['error' => false, 'message' => 'Datos guardados exitosamente'];
        
        try {
            foreach($filtros as $filtro) {
                $filtroConcurso = FiltroConcurso::find()
                                    ->where(['id_pregunta' => $filtro['id_pregunta']])
                                    ->one();
                                    
                // Permite Actualizar/Insertar el filtro
                if ($filtroConcurso == null) {
                    $filtroConcurso = new FiltroConcurso();
                }
                
                $filtroConcurso->id_concurso = Yii::$app->request->post('id_concurso');
                $filtroConcurso->id_pregunta = $filtro['id_pregunta'];
                $filtroConcurso->tipo_filtro = $filtro['tipo_filtro'];
                $filtroConcurso->minimo = $filtro['minimo'];
                $filtroConcurso->maximo = $filtro['maximo'];
                $filtroConcurso->arreglo_opcion = $filtro['arreglo_opcion'];
                $filtroConcurso->validar_copia = $filtro['validar_copia'];
                $filtroConcurso->comentarios = $filtro['comentarios'];
                
                $filtroConcurso->save();
                
                if ($filtroConcurso->errors) {
                    throw new \Exception(Functions::errorsToString($filtroConcurso->errors));
                }
            }
            
            $transaction->commit();
        } catch(\Exception $e) {
            $transaction->rollBack();
            
            $result = ['error' => true, 'message' => $e->getMessage()];
        }
        
        return $result;
    }
    
    public function actionGetfiltros()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $filtros = FiltroConcurso::find()
                        ->where(['id_concurso' => Yii::$app->request->post('concurso')])
                        ->all();
        
        return $filtros;
    }

}
