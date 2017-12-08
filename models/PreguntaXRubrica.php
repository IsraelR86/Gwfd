<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "preguntas_x_rubricas".
 *
 * @property integer $id_pregunta
 * @property integer $id_rubrica
 *
 * @property Pregunta $pregunta
 * @property Rubrica $rubrica
 */
class PreguntaXRubrica extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'preguntas_x_rubricas';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_pregunta', 'id_rubrica'], 'required'],
            [['id_pregunta', 'id_rubrica'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_pregunta' => 'Id Pregunta',
            'id_rubrica' => 'Id Rubrica',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPregunta()
    {
        return $this->hasOne(Pregunta::className(), ['id' => 'id_pregunta']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRubrica()
    {
        return $this->hasOne(Rubrica::className(), ['id' => 'id_rubrica']);
    }
}
