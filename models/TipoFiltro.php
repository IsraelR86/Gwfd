<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tipo_filtro".
 *
 * @property integer $id
 * @property integer $tipo_pregunta
 * @property string $descripcion
 *
 * @property FiltroConcurso[] $filtrosConcurso
 * @property TipoPregunta $tipoPregunta
 */
class TipoFiltro extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tipo_filtro';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['descripcion'], 'filter', 'filter' => 'strip_tags'],
            [['tipo_pregunta', 'descripcion'], 'required'],
            [['tipo_pregunta'], 'integer'],
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
            'tipo_pregunta' => 'Tipo de Pregunta',
            'descripcion' => 'Descripción',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFiltrosConcurso()
    {
        return $this->hasMany(FiltroConcurso::className(), ['tipo_filtro' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipoPregunta()
    {
        return $this->hasOne(TipoPregunta::className(), ['id' => 'tipo_pregunta']);
    }
}
