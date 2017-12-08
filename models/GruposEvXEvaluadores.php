<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "grupos_ev_x_evaluadores".
 *
 * @property integer $id
 * @property integer $id_grupo_evaluadores
 * @property integer $id_evaluador
 * @property string $fecha_alta
 *
 * @property GrupoEvaluadores $grupoEvaluadores
 * @property Usuario $evaluador
 */
class GruposEvXEvaluadores extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
     
    public static function tableName()
    {
        return 'grupos_ev_x_evaluadores';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_grupo_evaluadores', 'id_evaluador', 'fecha_alta'], 'required'],
            [['id_grupo_evaluadores', 'id_evaluador'], 'integer'],
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
            'id' => 'ID',
            'id_grupo_evaluadores' => 'Grupo de Evaluadores',
            'id_evaluador' => 'Evaluador',
            'fecha_alta' => 'Fecha de Alta',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGrupoEvaluadores()
    {
        return $this->hasOne(GrupoEvaluadores::className(), ['id' => 'id_grupo_evaluadores']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvaluador()
    {
        return $this->hasOne(Usuario::className(), ['id' => 'id_evaluador']);
    }
    
}
