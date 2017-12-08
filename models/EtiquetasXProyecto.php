<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "etiquetas_x_proyectos".
 *
 * @property integer $id_etiqueta
 * @property integer $id_proyecto
 *
 * @property Etiquetas $Etiqueta
 * @property Proyectos $Proyecto
 */
class EtiquetasXProyecto extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'etiquetas_x_proyectos';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_etiqueta', 'id_proyecto'], 'required'],
            [['id_etiqueta', 'id_proyecto'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_etiqueta' => 'Etiqueta',
            'id_proyecto' => 'Proyecto',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEtiqueta()
    {
        return $this->hasOne(Etiqueta::className(), ['id' => 'id_etiqueta']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProyecto()
    {
        return $this->hasOne(Proyecto::className(), ['id' => 'id_proyecto']);
    }
}
