<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "etiquetas".
 *
 * @property integer $id
 * @property string $descripcion
 * @property integer $activo
 *
 * @property Concurso[] $concursos
 * @property Proyecto[] $proyectos
 */
class Etiqueta extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'etiquetas';
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
            [['activo'], 'integer'],
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
            'descripcion' => 'Descripción',
            'activo' => 'Activo',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConcursos()
    {
        return $this->hasMany(Concurso::className(), ['id' => 'id_concurso'])
                    ->viaTable('etiquetas_x_concursos', ['id_etiqueta' => 'id']);
    }
    
    /**
    * @return \yii\db\ActiveQuery
    */
   public function getProyectos()
   {
       return $this->hasMany(Proyecto::className(), ['id' => 'id_proyecto'])
                   ->viaTable('etiquetas_x_proyectos', ['id_etiqueta' => 'id']);
   }
}
