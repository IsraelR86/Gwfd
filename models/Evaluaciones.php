<?php

namespace app\models;

use Yii;
/**
 * This is the model class for table "evaluaciones".
 *
 * @property integer $id
 * @property integer $id_rubrica
 * @property integer $id_proyecto
 * @property integer $id_concurso
 * @property integer $id_evaluador
 * @property integer $calificacion
 * @property string $comentarios
 * @property string $fecha
 *
 * @property Concursos $idConcurso
 * @property Usuarios $idEvaluador
 * @property Proyectos $idProyecto
 * @property Rubricas $idRubrica
 */
class Evaluaciones extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'evaluaciones';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['comentarios'], 'filter', 'filter' => 'strip_tags'],
            [['id_rubrica', 'id_proyecto', 'id_concurso', 'id_evaluador', 'calificacion'], 'required'],
            [['id_rubrica', 'id_proyecto', 'id_concurso', 'id_evaluador', 'calificacion'], 'integer'],
            [['comentarios'], 'string'],
            [['comentarios'], 'app\validators\DelspacesValidator'],
            [['fecha'], 'safe'],
            [['fecha'], 'date', 'format' => 'yyyy-M-d H:m:s']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'id_rubrica' => Yii::t('app', 'Id Rubrica'),
            'id_proyecto' => Yii::t('app', 'Id Proyecto'),
            'id_concurso' => Yii::t('app', 'Id Concurso'),
            'id_evaluador' => Yii::t('app', 'Id Evaluador'),
            'calificacion' => Yii::t('app', 'Calificacion'),
            'comentarios' => Yii::t('app', 'Comentarios'),
            'fecha' => Yii::t('app', 'Fecha'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdConcurso()
    {
        return $this->hasOne(Concursos::className(), ['id' => 'id_concurso']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdEvaluador()
    {
        return $this->hasOne(Usuarios::className(), ['id' => 'id_evaluador']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdProyecto()
    {
        return $this->hasOne(Proyectos::className(), ['id' => 'id_proyecto']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdRubrica()
    {
        return $this->hasOne(Rubrica::className(), ['id' => 'id_rubrica']);
    }

    public static function getRubricasEvaluadas($proyecto, $concurso, $evaluador)
    {
        //Devuelve el número de rúbricas calificadas por un evaluador, concurso y proyecto dado
        return Evaluaciones::find()
            ->where('id_proyecto = :id_proyecto', [':id_proyecto' => $proyecto])
            ->andWhere('id_concurso = :id_concurso', [':id_concurso' => $concurso])
            ->andWhere('id_evaluador = :id_evaluador', [':id_evaluador' => $evaluador])
            ->all();
    }

}
