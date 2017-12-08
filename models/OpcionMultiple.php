<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "opciones_multiples".
 *
 * @property integer $id
 * @property integer $id_pregunta
 * @property string $descripcion
 * @property integer $ponderacion
 *
 * @property Pregunta $pregunta
 */
class OpcionMultiple extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'opciones_multiples';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['descripcion'], 'filter', 'filter' => 'strip_tags'],
            [['descripcion'], 'app\validators\DelspacesValidator'],
            [['id_pregunta', 'descripcion'], 'required'],
            [['id_pregunta', 'ponderacion'], 'integer'],
            [['descripcion'], 'string', 'max' => 200]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_pregunta' => 'Pregunta',
            'descripcion' => 'Descripción',
            'ponderacion' => 'Ponderación',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPregunta()
    {
        return $this->hasOne(Pregunta::className(), ['id' => 'id_pregunta']);
    }
}
