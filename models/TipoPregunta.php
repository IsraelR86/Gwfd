<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tipo_pregunta".
 *
 * @property integer $id
 * @property string $descripcion
 * @property string $columna_respuesta 
 *
 * @property Pregunta[] $preguntas
 * @property TipoFiltro[] $tipoFiltros
 */
class TipoPregunta extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tipo_pregunta';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['descripcion', 'columna_respuesta'], 'filter', 'filter' => 'strip_tags'],
            [['descripcion', 'columna_respuesta'], 'app\validators\DelspacesValidator'],
            [['descripcion'], 'string', 'max' => 20],
            [['columna_respuesta'], 'string', 'max' => 45],
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
            'columna_respuesta' => 'Columna Respuesta',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPreguntas()
    {
        return $this->hasMany(Pregunta::className(), ['tipo_pregunta' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipoFiltros()
    {
        return $this->hasMany(TipoFiltro::className(), ['tipo_pregunta' => 'id']);
    }
}
