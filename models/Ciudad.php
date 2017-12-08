<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "ciudades".
 *
 * @property integer $id
 * @property integer $id_estado
 * @property string $descripcion
 *
 * @property Estado $estado
 * @property Emprendedor[] $emprendedores
 */
class Ciudad extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ciudades';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['descripcion'], 'filter', 'filter' => 'strip_tags'],
            [['id', 'id_estado', 'descripcion'], 'required'],
            [['id', 'id_estado'], 'integer'],
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
            'id_estado' => 'Estado',
            'descripcion' => 'Descripción',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEstado()
    {
        return $this->hasOne(Estado::className(), ['id' => 'id_estado']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmprendedores()
    {
        return $this->hasMany(Emprendedor::className(), ['id_ciudad' => 'id']);
    }
}
