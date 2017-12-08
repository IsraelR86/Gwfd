<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "universidades".
 *
 * @property integer $id
 * @property string $nombre
 * @property integer $activo
 *
 * @property Emprendedor[] $emprendedores
 */
class Universidad extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'universidades';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nombre'], 'filter', 'filter' => 'strip_tags'],
            [['id', 'nombre'], 'required'],
            [['id', 'activo'], 'integer'],
            [['nombre'], 'app\validators\DelspacesValidator'],
            [['nombre'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nombre' => 'Nombre',
            'activo' => 'Activo',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmprendedores()
    {
        return $this->hasMany(Emprendedor::className(), ['id_universidad' => 'id']);
    }
}
