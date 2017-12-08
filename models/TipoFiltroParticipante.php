<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tipo_filtro_participante".
 *
 * @property integer $id
 * @property string $descripcion
 * @property string $columna_validar
 *
 * @property FiltroParticipanteXConcurso[] $filtrosParticipanteXConcurso
 * @property Concurso[] $Concursos
 */
class TipoFiltroParticipante extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tipo_filtro_participante';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['descripcion', 'columna_validar'], 'filter', 'filter' => 'strip_tags'],
            [['id'], 'required'],
            [['id'], 'integer'],
            [['descripcion', 'columna_validar'], 'app\validators\DelspacesValidator'],
            [['descripcion', 'columna_validar'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'descripcion' => 'Descripcion',
            'columna_validar' => 'Columna Validar',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFiltrosParticipanteXConcurso()
    {
        return $this->hasMany(FiltroParticipanteXConcurso::className(), ['id_tipo_filtro' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConcursos()
    {
        return $this->hasMany(Concurso::className(), ['id' => 'id_concurso'])->viaTable('filtro_participante_x_concurso', ['id_tipo_filtro_participante' => 'id']);
    }
}
