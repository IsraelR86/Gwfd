<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tipo_pregunta_concurso".
 *
 * @property integer $id
 * @property string $descripcion
 *
 * @property PreguntaXConcurso[] $preguntasXConcursos
 */
class TipoPreguntaConcurso extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tipo_pregunta_concurso';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['descripcion'], 'filter', 'filter' => 'strip_tags'],
            [['descripcion'], 'app\validators\DelspacesValidator'],
            [['descripcion'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'descripcion' => 'Descripcion',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPreguntasXConcursos()
    {
        return $this->hasMany(PreguntaXConcurso::className(), ['id_tipo_pregunta_concurso' => 'id']);
    }
}
