<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "etiquetas_x_noticias".
 *
 * @property string $id_noticia
 * @property integer $id_etiqueta
 *
 * @property Etiqueta $etiqueta
 * @property Noticia $noticia
 */
class EtiquetasXNoticia extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'etiquetas_x_noticias';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_noticia', 'id_etiqueta'], 'required'],
            [['id_noticia', 'id_etiqueta'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_noticia' => 'Noticia',
            'id_etiqueta' => 'Etiqueta',
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
    public function getNoticia()
    {
        return $this->hasOne(Noticia::className(), ['id' => 'id_noticia']);
    }
}
