<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "estados".
 *
 * @property integer $id
 * @property string $descripcion
 *
 * @property Ciudad[] $ciudades
 * @property Emprendedor[] $emprendedores
 */
class Estado extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'estados';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['descripcion'], 'filter', 'filter' => 'strip_tags'],
            [['descripcion'], 'required'],
            [['descripcion'], 'app\validators\DelspacesValidator'],
            [['descripcion'], 'string', 'max' => 19]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'descripcion' => 'Descripción',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCiudades()
    {
        return $this->hasMany(Ciudad::className(), ['id_estado' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmprendedores()
    {
        return $this->hasMany(Emprendedor::className(), ['id_estado' => 'id']);
    }
}
