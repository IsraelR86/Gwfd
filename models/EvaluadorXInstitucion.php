<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "evaluadores_x_instituciones".
 *
 * @property integer $id_institucion
 * @property integer $id_evaluador
 *
 * @property Evaluadores $idEvaluador
 * @property Instituciones $idInstitucion
 */
class EvaluadorXInstitucion extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'evaluadores_x_instituciones';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_institucion', 'id_evaluador'], 'required'],
            [['id_institucion', 'id_evaluador'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_institucion' => 'Id Institucion',
            'id_evaluador' => 'Id Evaluador',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdEvaluador()
    {
        return $this->hasOne(Evaluadores::className(), ['id_usuario' => 'id_evaluador']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdInstitucion()
    {
        return $this->hasOne(Instituciones::className(), ['id' => 'id_institucion']);
    }
}
