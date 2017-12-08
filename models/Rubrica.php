<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "rubricas".
 *
 * @property integer $id
 * @property integer $id_concurso
 * @property integer $tipo
 * @property string $nombre
 * @property string $descripcion
 * @property integer $calificacion_minima
 * @property integer $calificacion_maxima
 *
 * @property Evaluacion[] $evaluaciones
 * @property Concurso $concurso
 * @property Pregunta[] $preguntas
 */
class Rubrica extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rubricas';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nombre', 'descripcion'], 'filter', 'filter' => 'strip_tags'],
            [['id_concurso'], 'required'],
            [['id_concurso', 'tipo', 'calificacion_minima', 'calificacion_maxima'], 'integer'],
            [['nombre', 'descripcion'], 'app\validators\DelspacesValidator'],
            [['nombre'], 'string', 'max' => 45],
            [['descripcion'], 'string', 'max' => 500]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_concurso' => 'Concurso',
            'tipo' => 'Tipo',
            'nombre' => 'Nombre',
            'descripcion' => 'Descripción',
            'calificacion_minima' => 'Calificación Mínima',
            'calificacion_maxima' => 'Calificación Máxima',
            'preguntas' => 'Preguntas',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvaluaciones()
    {
        return $this->hasMany(Evaluacion::className(), ['id_rubrica' => 'id']);
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
    public function getPreguntas()
    {
        return $this->hasMany(Pregunta::className(), ['id' => 'id_pregunta'])
                        ->viaTable('preguntas_x_rubricas', ['id_rubrica' => 'id']);
    }

    public function getPreguntasConcurso()
    {
        return $this->hasMany(PreguntaXConcurso::className(), ['id' => 'id_pregunta_concurso'])
                        ->viaTable('preguntas_concurso_x_rubrica', ['id_rubrica' => 'id']);
    }
    /**
     * Devuelve un true si la rubrica tiene una respuesta, de lo contrario false
     *
     * @return boolean
     */
    public function hasRespuesta($id_proyecto, $id_evaluador)
    {
        $query = 'SELECT
                COUNT(id) AS respuesta
            FROM
                evaluaciones
            WHERE
                id_rubrica = '.$this->id.' AND
                id_proyecto = :id_proyecto AND
                id_concurso = '.$this->id_concurso.' AND
                id_evaluador = :id_evaluador';

        $result = Yii::$app->db->createCommand($query, [
            ':id_proyecto' => $id_proyecto,
            ':id_evaluador' => $id_evaluador,
        ])->queryScalar();

        if ($result == 0) {
            return false;
        }

        return true;
    }

    /**
     * Devuelve la evaluacion en caso de existir
     *
     * @return app\models\Evaluacion
     */
    public function getEvaluacion($id_proyecto, $id_evaluador)
    {
        return Evaluaciones::find()
            ->where('id_rubrica = :id_rubrica', [':id_rubrica' => $this->id])
            ->andWhere('id_proyecto = :id_proyecto', [':id_proyecto' => $id_proyecto])
            ->andWhere('id_concurso = :id_concurso', [':id_concurso' => $this->id_concurso])
            ->andWhere('id_evaluador = :id_evaluador', [':id_evaluador' => $id_evaluador])
            ->one();
    }

    public function getRubricasXConcurso($id_concurso)
    {
        return Rubrica::find()
            ->where('id_concurso = :id_concurso', [':id_concurso' => $id_concurso])
            ->all();
    }
}
