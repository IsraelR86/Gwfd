<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "ganadores".
 *
 * @property integer $id_concurso
 * @property integer $id_proyecto
 *
 * @property Concursos $idConcurso
 * @property Proyectos $idProyecto
 */
class Ganadores extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ganadores';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_concurso', 'id_proyecto'], 'required'],
            [['id_concurso', 'id_proyecto'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_concurso' => 'Id Concurso',
            'id_proyecto' => 'Id Proyecto',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdConcurso()
    {
        return $this->hasOne(Concurso::className(), ['id' => 'id_concurso']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdProyecto()
    {
        return $this->hasOne(Proyectos::className(), ['id' => 'id_proyecto']);
    }
}
