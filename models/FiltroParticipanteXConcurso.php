<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "filtro_participante_x_concurso".
 *
 * @property integer $id_tipo_filtro_participante
 * @property integer $id_concurso
 * @property string $restricion
 *
 * @property Concursos $Concurso
 * @property TipoFiltroParticipante $idTipoFiltro
 */
class FiltroParticipanteXConcurso extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'filtro_participante_x_concurso';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['restricion'], 'filter', 'filter' => 'strip_tags'],
            [['id_tipo_filtro_participante', 'id_concurso'], 'required'],
            [['id_tipo_filtro_participante', 'id_concurso'], 'integer'],
            [['restricion'], 'app\validators\DelspacesValidator'],
            [['restricion'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_tipo_filtro_participante' => 'Tipo Filtro',
            'id_concurso' => 'Concurso',
            'restricion' => 'Restricion',
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
    public function getTipoFiltroParticipante()
    {
        return $this->hasOne(TipoFiltroParticipante::className(), ['id' => 'id_tipo_filtro']);
    }
}
