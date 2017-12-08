<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "etiquetas_x_concursos".
 *
 * @property integer $id_etiqueta
 * @property integer $id_concurso
 *
 * @property Concursos $concurso
 * @property Etiquetas $etiqueta
 */
class EtiquetasXConcurso extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'etiquetas_x_concursos';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_etiqueta', 'id_concurso'], 'required'],
            [['id_etiqueta', 'id_concurso'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_etiqueta' => 'Id Etiqueta',
            'id_concurso' => 'Id Concurso',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConcurso()
    {
        return $this->hasOne(Concurso::className(), ['id' => 'id_concurso']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEtiqueta()
    {
        return $this->hasOne(Etiqueta::className(), ['id' => 'id_etiqueta']);
    }
}
