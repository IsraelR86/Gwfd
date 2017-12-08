<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "preguntas_x_concurso".
 *
 * @property integer $id
 * @property integer $id_concurso
 * @property string $descripcion
 * @property string $ayuda
 *
 * @property Concursos $concurso
 * @property Concursos $tipoPreguntaConcurso
 */
class PreguntaXConcurso extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'preguntas_x_concurso';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['descripcion', 'ayuda'], 'filter', 'filter' => 'strip_tags'],
            [['id_concurso'], 'required'],
            [['id_concurso', 'id_tipo_pregunta_concurso'], 'integer'],
            [['descripcion', 'ayuda'], 'app\validators\DelspacesValidator'],
            [['descripcion'], 'string', 'max' => 300],
            [['ayuda'], 'string', 'max' => 1000]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_concurso' => 'Id Concurso',
            'id_tipo_pregunta_concurso' => 'Id Tipo Pregunta Concurso',
            'descripcion' => 'Descripcion',
            'ayuda' => 'Ayuda',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConcurso()
    {
        return $this->hasOne(Concursos::className(), ['id' => 'id_concurso']);
    }

    public function getTipoPreguntaConcurso()
    {
        return $this->hasOne(TipoPreguntaConcurso::className(), ['id' => 'id_tipo_pregunta_concurso']);
    }

    public function getTipoPregunta()
    {
        return $this->hasOne(TipoPreguntaConcurso::className(), ['id' => 'id_tipo_pregunta_concurso']);
    }

     public static function getRespEspecificaFromConcurso($preguntaEspecifica,$proyecto){
       return RespuestaConcurso::find()
       ->innerJoin('preguntas_x_concurso PXC','PXC.id = respuestas_concurso.id_pregunta AND PXC.id =' .$preguntaEspecifica. ' AND respuestas_concurso.id_proyecto= ' .$proyecto . ' AND respuestas_concurso.solo_concurso = 1')
       ->all();
     }

     public function getRespuestaEspecificaConcurso($id_preguntaEspecifica, $id_concurso,$id_proyecto)
     {
         return RespuestaConcurso::find()
             ->where('id_pregunta = :id_pregunta', [':id_pregunta' => $id_preguntaEspecifica])
             ->andWhere('id_proyecto = :id_proyecto', [':id_proyecto' => $id_proyecto])
             ->andWhere('id_concurso = :id_concurso', [':id_concurso' => $id_concurso])
             ->andWhere('solo_concurso = 1')
             ->one();
     }

     public function getRespuestaEspecificaConcursoToText($id_preguntaEspecifica,$id_concurso,$id_proyecto)
     {
         $respuesta = $this->getRespuestaEspecificaConcurso($id_preguntaEspecifica, $id_concurso,$id_proyecto);
         $texto = '';

         if (empty($respuesta)) {
             return $texto;
         }



         switch ($this->id_tipo_pregunta_concurso) {
             case 1: // Texto
                 $texto = $respuesta->getAttribute('respuesta_texto');
                 break;
             case 2: // Archivo
                 $texto = 'Documento';
               break;
         }

         return $texto;
     }

}
