<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "preguntas_concurso_x_rubrica".
 *
 * @property integer $id_rubrica
 * @property integer $id_pregunta_concurso
 *
 * @property Rubricas $idRubrica
 * @property PreguntasXConcurso $idPreguntaConcurso
 */
class PreguntasConcursoXRubrica extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'preguntas_concurso_x_rubrica';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_rubrica', 'id_pregunta_concurso'], 'required'],
            [['id_rubrica', 'id_pregunta_concurso'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_rubrica' => 'Id Rubrica',
            'id_pregunta_concurso' => 'Id Pregunta Concurso',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdRubrica()
    {
        return $this->hasOne(Rubricas::className(), ['id' => 'id_rubrica']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdPreguntaConcurso()
    {
        return $this->hasOne(PreguntasXConcurso::className(), ['id' => 'id_pregunta_concurso']);
    }

    public static function getEspecificasFromRubrica($rubrica,$concurso) {
       return PreguntaXConcurso::find()
       ->innerJoin('preguntas_concurso_x_rubrica PCXR', 'PCXR.id_pregunta_concurso = preguntas_x_concurso.id AND preguntas_x_concurso.id_concurso = '.$concurso)
       ->innerJoin('rubricas R', 'R.id = PCXR.id_rubrica AND PCXR.id_rubrica = '.$rubrica)
       ->orderBy('R.id')
       ->all();
     }

  

}
