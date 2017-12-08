<?php

namespace app\controllers;

use Yii;
use app\models\FiltroParticipanteXConcurso;
use app\helpers\Functions;
use yii\helpers\ArrayHelper;

class FiltroparticipantexconcursoController extends \yii\web\Controller
{
    public function actionSetfiltros()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $transaction = Yii::$app->db->beginTransaction();
        $filtros = Yii::$app->request->post('filtros');
        $result = ['error' => false, 'message' => 'Datos guardados exitosamente'];
        
        try {
            foreach($filtros as $filtro) {
                $filtroParticipante = FiltroParticipanteXConcurso::find()
                                    ->where(['id_tipo_filtro_participante' => $filtro['id_tipo_filtro_participante']])
                                    ->one();
                                    
                // Permite Actualizar/Insertar el filtro
                if ($filtroParticipante == null) {
                    $filtroParticipante = new FiltroParticipanteXConcurso();
                }
                
                $filtroParticipante->id_concurso = Yii::$app->request->post('id_concurso');
                $filtroParticipante->id_tipo_filtro_participante = $filtro['id_tipo_filtro_participante'];
                $filtroParticipante->restricion = $filtro['restricion'];
                
                $filtroParticipante->save();
                
                if ($filtroParticipante->errors) {
                    throw new \Exception(Functions::errorsToString($filtroParticipante->errors));
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
        $filtros = FiltroParticipanteXConcurso::find()
                        ->where(['id_concurso' => Yii::$app->request->post('concurso')])
                        ->all();
        
        return $filtros;
    }

}
