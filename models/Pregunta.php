<?php

namespace app\models;

use Yii;
use app\models\Seccion;
use app\models\Rubrica;
use app\models\PreguntaXRubrica;
use app\models\Pregunta;

/**
 * This is the model class for table "preguntas".
 *
 * @property integer $id
 * @property integer $id_seccion
 * @property integer $pagina
 * @property string $descripcion
 * @property string $ayuda
 * @property integer $tipo_pregunta
 * @property integer $ponderacion
 *
 * @property FiltroConcurso[] $filtrosConcursos
 * @property OpcionMultiple[] $opcionesMultiple
 * @property Seccion $seccion
 * @property TipoPregunta $tipoPregunta
 * @property Respuesta $respuesta
 */
class Pregunta extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'preguntas';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['descripcion', 'ayuda'], 'filter', 'filter' => 'strip_tags'],
            [['descripcion', 'ayuda'], 'app\validators\DelspacesValidator'],
            [['id_seccion', 'tipo_pregunta'], 'required'],
            [['id_seccion', 'tipo_pregunta', 'ponderacion', 'pagina'], 'integer'],
            [['descripcion'], 'string', 'max' => 300],
            [['ayuda'], 'string', 'max' => 1000]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_seccion' => 'Sección',
            'descripcion' => 'Descripción',
            'ayuda' => 'Ayuda',
            'tipo_pregunta' => 'Tipo de Pregunta',
            'ponderacion' => 'Ponderación',
            'pagina' => 'Página',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFiltrosConcursos()
    {
        return $this->hasMany(FiltrosConcurso::className(), ['id_pregunta' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOpcionesMultiple()
    {
        return $this->hasMany(OpcionMultiple::className(), ['id_pregunta' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSeccion()
    {
        return $this->hasOne(Seccion::className(), ['id' => 'id_seccion']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipoPregunta()
    {
        return $this->hasOne(TipoPregunta::className(), ['id' => 'tipo_pregunta']);
    }

    /**
     * Obtiene la Respuesta de la instancia de proyecto especificado
     *
     * @param integer id_proyecto
     * @return app\models\Respuesta
     */
    public function getRespuesta($id_proyecto)
    {
        return Respuesta::find()
            ->where('id_pregunta = :id_pregunta', [':id_pregunta' => $this->id])
            ->andWhere('id_proyecto = :id_proyecto', [':id_proyecto' => $id_proyecto])
            ->one();
    }

    public function getRespuestaConcurso($id_proyecto, $id_concurso)
    {
        return RespuestaConcurso::find()
            ->where('id_pregunta = :id_pregunta', [':id_pregunta' => $this->id])
            ->andWhere('id_proyecto = :id_proyecto', [':id_proyecto' => $id_proyecto])
            ->andWhere('id_concurso = :id_concurso', [':id_concurso' => $id_concurso])
            ->one();
    }

    public function getRespuestaConcursoToText($id_proyecto, $id_concurso)
    {
        $respuesta = $this->getRespuestaConcurso($id_proyecto, $id_concurso);
        $texto = '';

        if (empty($respuesta)) {
            return $texto;
        }

        switch ($this->tipo_pregunta) {
            case 1: // Texto
            case 2: // Numérica
            case 5: // Hipervínculo
                $texto = $respuesta->getAttribute($this->tipoPregunta->columna_respuesta);
                break;

            case 3: // Opción Múltiple
                $res = json_decode($respuesta->getAttribute($this->tipoPregunta->columna_respuesta));

                if (empty($res)) {
                    break;
                }

                $opciones = OpcionMultiple::find()
                    ->where('id IN ('.implode(',', $res).')')
                    ->all();

                foreach ($opciones as $opcion) {
                    $texto .= $opcion->descripcion.', ';
                }

                $texto = substr($texto, 0, strlen($texto)-2);

                break;

            case 4: // Opción Única
                $opcion = OpcionMultiple::findOne($respuesta->getAttribute($this->tipoPregunta->columna_respuesta));
                $texto = $opcion->descripcion;

                break;

            case 6: // Punto Radial Geográfico
            case 7: // Polígono Geográfico
            case 8: // Punto Geográfico
                $texto = count(json_decode($respuesta->respuesta_geografica)) .' Puntos geográficos';

                break;
        }

        return $texto;
    }

    /**
     * Devuelve el Total de preguntas y
     * el avance de las respuestas
     *
     * @return array total_preguntas, total_respuestas
     */
    public static function getAvanceCuestionario($id_proyecto)
    {
        $sql = 'SELECT
            COUNT(preguntas.id) AS total_preguntas,
            COUNT(respuestas.id_pregunta) AS total_respuestas
        FROM preguntas
        LEFT JOIN respuestas ON
            preguntas.id = respuestas.id_pregunta AND
            respuestas.id_proyecto = '.$id_proyecto;

        $result = Yii::$app->db->createCommand($sql)->queryOne();

        return $result;
    }

    /**
     * Calcula la ponderación de la respuesta
     *
     * @param int|Pregunta $pregunta
     * @param int|string $respuesta
     * @return int Ponderación
     */
    public static function calcPonderacionRespuesta($pregunta, $respuesta)
    {
        $ponderacion = 0;

        if ($pregunta instanceof Pregunta) {
            $objPregunta = $pregunta;
        } else {
            $objPregunta = Pregunta::findOne($pregunta);
        }

        switch ($pregunta->tipo_pregunta) {
            case 1: // Texto
            case 2: // Numérica
            case 5: // Hipervínculo
                $ponderacion = $objPregunta->ponderacion;
                break;

            case 3: // Opción Múltiple
                $respuestas = json_decode($respuesta);

                if (empty($respuesta)) {
                    break;
                }

                $opciones = OpcionMultiple::find()
                    ->where('id IN ('.implode(',', $respuestas).')')
                    ->all();

                foreach ($opciones as $opcion) {
                    $ponderacion += $objPregunta->ponderacion * $opcion->ponderacion;
                }

                break;

            case 4: // Opción Única
                $opcion = OpcionMultiple::findOne($respuesta);
                $ponderacion = $objPregunta->ponderacion * $opcion->ponderacion;

                break;
        }

        return $ponderacion;
    }

    public function getPonderacionRespuesta($respuesta)
    {
        return self::calcPonderacionRespuesta($this, $respuesta);
    }
    public function getPreguntasXseccion($id_seccion,$preguntas)
    {

         $sql = "SELECT * FROM preguntas WHERE  ( ";
        $i=1;
        foreach($preguntas as $pregunta):
            $sql.=' id = '.$pregunta->id;
            if($i<count($preguntas))
            $sql.=' or ';
            $i++;
            endforeach;
        $sql.= " ) and id_seccion = ".$id_seccion;

        return Pregunta::findBySQL($sql)->all();
    }
    public function getRubricas($id_concurso)
    {
       return Rubrica::findBySQL("SELECT * FROM rubricas WHERE id_concurso = ".$id_concurso)->all();
    }

    public function getSql($id_rubrica)
    {
        $preguntas_por_rubrica = PreguntaXRubrica::findBySQL("SELECT * FROM preguntas_x_rubricas WHERE id_rubrica = ".$id_rubrica)->all();
        $sql = "SELECT * FROM preguntas WHERE ";
        $i=1;
        foreach($preguntas_por_rubrica as $pregunta):
            $sql.=' id = '.$pregunta->id_pregunta;
            if($i<count($preguntas_por_rubrica))
            $sql.=' or ';
            $i++;
            endforeach;

        return $sql;
    }
    public function getPreguntas($sql)
    {
        return Pregunta::findBySQL($sql)->all();
    }
    public function getSecciones($sql)
    {
        $secciones = Pregunta::findBySQL($sql.' GROUP BY id_seccion')->all();

        $sql = "SELECT * FROM seccion WHERE ";
        $i=1;

        foreach($secciones as $seccion):
            $sql.=' id = '.$seccion->id_seccion;
            if($i<count($secciones))
            $sql.=' or ';
            $i++;
            endforeach;
        $secciones  = Seccion::findBySQL($sql)->all();
        return $secciones;
        }

    public static function getFromRubricaSeccion($rubrica, $seccion)
    {
        return Pregunta::find()
            ->innerJoin('seccion', ' seccion.id = preguntas.id_seccion AND seccion.id = '.$seccion)
            ->innerJoin('preguntas_x_rubricas', 'preguntas_x_rubricas.id_pregunta = preguntas.id AND preguntas_x_rubricas.id_rubrica = '.$rubrica)
            ->all();
    }

}
