<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "emprendedores_x_proyectos".
 *
 * @property integer $id_emprendedor
 * @property integer $id_proyecto
 * @property string $fecha_alta
 *
 * @property Emprendedores $emprendedor
 * @property Proyectos $proyecto
 */
class EmprendedorXProyecto extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'emprendedores_x_proyectos';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_emprendedor', 'id_proyecto'], 'required'],
            [['id_emprendedor', 'id_proyecto'], 'integer'],
            [['fecha_alta'], 'safe'],
            [['fecha_alta'], 'date', 'format' => 'yyyy-M-d H:m:s']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_emprendedor' => 'Emprendedor',
            'id_proyecto' => 'Proyecto',
            'fecha_alta' => 'Fecha de Registro',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmprendedor()
    {
        return $this->hasOne(Emprendedor::className(), ['id_usuario' => 'id_emprendedor']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProyecto()
    {
        return $this->hasOne(Proyecto::className(), ['id' => 'id_proyecto']);
    }
    
    /**
     * Establece el campo fecha_alta con la fecha actual
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->fecha_alta = date('Y-m-d H:i:s');
            return true;
        } else {
            return false;
        }
    }
}
