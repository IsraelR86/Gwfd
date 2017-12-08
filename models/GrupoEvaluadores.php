<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "grupos_evaluadores".
 *
 * @property integer $id
 * @property integer $id_concurso
 * @property string $nombre
 * @property string $fecha_alta
 * @property string $fecha_inicio_proyectos_visibles
 * @property string $fecha_fin_proyectos_visibles
 *
 * @property GruposEvXEvaluadores[] $gruposEvXEvaluadores
 * @property GruposEvXProyectos[] $gruposEvXProyectos
 * @property Concursos $concurso
 */
class GrupoEvaluadores extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'grupos_evaluadores';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nombre'], 'filter', 'filter' => 'strip_tags'],
            [['nombre'], 'app\validators\DelspacesValidator'],
            [['id_concurso', 'nombre', 'fecha_alta'], 'required'],
            [['id_concurso'], 'integer'],
            [['fecha_alta', 'fecha_inicio_proyectos_visibles', 'fecha_fin_proyectos_visibles'], 'safe'],
            [['fecha_alta'], 'date', 'format' => 'yyyy-M-d H:m:s'],
            [['fecha_inicio_proyectos_visibles', 'fecha_fin_proyectos_visibles'], 'date', 'format' => 'yyyy-M-d'],
            //[['fecha_inicio_proyectos_visibles', 'fecha_fin_proyectos_visibles'], 'match', 'pattern' => '/^(0?[1-9]|[12][0-9]|3[01])[\/\-](0?[1-9]|1[012])[\/\-]\d{4}$/'],
            [['nombre'], 'string', 'max' => 45],
            //['fecha_inicio_proyectos_visibles', 'compare', 'compareAttribute' => 'fecha_fin_proyectos_visibles', 'operator' => '<'],
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
            'nombre' => 'Nombre',
            'fecha_alta' => 'Fecha de Alta',
            'fecha_inicio_proyectos_visibles' => 'Fecha de Inicio Proyectos Visibles',
            'fecha_fin_proyectos_visibles' => 'Fecha de Fin Proyectos Visibles',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGruposEvXEvaluadores()
    {
        return $this->hasMany(GruposEvXEvaluadores::className(), ['id_grupo_evaluadores' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvaluadores()
    {
        return $this->hasMany(Usuario::className(), ['id' => 'id_evaluador'])
            ->viaTable('grupos_ev_x_evaluadores', ['id_grupo_evaluadores' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGruposEvXProyectos()
    {
        return $this->hasMany(GruposEvXProyectos::className(), ['id_grupo_evaluadores' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProyectos()
    {
        return $this->hasMany(Proyecto::className(), ['id' => 'id_proyecto'])
            ->viaTable('grupos_ev_x_proyectos', ['id_grupo_evaluadores' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConcurso()
    {
        return $this->hasOne(Concurso::className(), ['id' => 'id_concurso']);
    }

    /**
     * Devuelve el proyecto que esta en primer lugar de la
     * lista de Resultado de Evaluaciones
     *
     * @return app\models\Proyecto
     */
    public function getPrimer_lugar()
    {
        $listProyectos = $this->getResultadoEvaluaciones();
        $primerLugar = null;

        if (count($listProyectos)) {
            $first = array_shift($listProyectos);

            if (!empty($first['calif_final'])) {
                $primerLugar = Proyecto::findOne($first['id_proyecto']);
            }
        }

        return $primerLugar;
    }

    /**
     * Devuelve array con las siguientes columnas
     * id_proyecto, id_evaluador, calificacion, tir
     *
     * @return array
     * @deprecated
     */
    public function getResumenEvaluaciones()
    {
        $query = 'SELECT
            evaluaciones.id_proyecto,
            evaluaciones.id_evaluador,
            SUM(evaluaciones.calificacion) AS calificacion,
            checklist_documentos.tir
        FROM
            evaluaciones
        INNER JOIN grupos_ev_x_proyectos ON
            evaluaciones.id_proyecto = grupos_ev_x_proyectos.id_proyecto AND
            grupos_ev_x_proyectos.id_grupo_evaluadores = '.$this->id.'
        INNER JOIN grupos_ev_x_evaluadores ON
            evaluaciones.id_evaluador = grupos_ev_x_evaluadores.id_evaluador AND
            grupos_ev_x_evaluadores.id_grupo_evaluadores = '.$this->id.'
        INNER JOIN checklist_documentos ON
            evaluaciones.id_proyecto = checklist_documentos.id_proyecto AND
            evaluaciones.id_concurso = checklist_documentos.id_concurso
        WHERE
            evaluaciones.id_concurso = '.$this->id_concurso.'
        GROUP BY
            evaluaciones.id_proyecto, evaluaciones.id_evaluador, checklist_documentos.tir
        ORDER BY
            evaluaciones.id_proyecto, evaluaciones.id_evaluador, calificacion, checklist_documentos.tir';

        $result = Yii::$app->db->createCommand($query)->queryAll();

        return $result;
    }

    /**
     * Devuelve matriz con formato de tabla, con los campos:
     * id_proyecto, evaluador1 ... evaluadorN, calif_final, tir
     *
     * @return array
     */
    public function getResultadoEvaluaciones()
    {
        $proyectos = $this->proyectos;
        $evaluadores = $this->evaluadores;
        $resultadoEvaluaciones = [];

        // Se hace un recorrido para cada proyecto perteneciente al grupo
        foreach ($proyectos as $proyecto) {
            $evaluacionFinal = 0;
            $chkDocumentos = $proyecto->getChecklistDocumentos($this->id_concurso);
            $resultadoEvaluaciones[$proyecto->id]['id_proyecto'] = $proyecto->id;

            // Se busca la evaluación de cada Evaluador perteneciente al grupo
            foreach ($evaluadores as $evaluador) {
                // Se realiza la consulta de la calificacion de forma individual
                // la suma de evaluaciones del Evaluador para cada Proyecto inscrito en el concurso
                $sumEvaluacion = $proyecto->getEvaluacionTotal($this->id_concurso, $evaluador->id);

                $resultadoEvaluaciones[$proyecto->id][$evaluador->id] = $sumEvaluacion ? $sumEvaluacion : 'Pendiente';
                $evaluacionFinal += (int) $sumEvaluacion;
            }

            // Se agrega la tir al final para poder hacer un order by
            $resultadoEvaluaciones[$proyecto->id]['tir'] = $chkDocumentos ? $chkDocumentos->tir : 0;
            $resultadoEvaluaciones[$proyecto->id]['calif_final'] = $evaluacionFinal;
        }

        // Se ordena primero por calif_final y despues por la tir, de mayor a menor
        // para que el primer lugar sea el primer elemento de la lista
        ArrayHelper::multisort($resultadoEvaluaciones, ['calif_final', 'tir'], [SORT_DESC, SORT_DESC], [SORT_NUMERIC, SORT_NUMERIC]);

        return $resultadoEvaluaciones;
    }

    /**
     * Valida si la fecha actual esta entre la fecha de inicio y fin de visibilidad
     */
    public function validFechaVisible()
    {
        $fechaActual = new \DateTime('NOW');;
        $fechaIni = new \DateTime($this->fecha_inicio_proyectos_visibles);
        $fechaFin = new \DateTime($this->fecha_fin_proyectos_visibles);

        if ($fechaActual<$fechaIni || $fechaActual>$fechaFin) {
            return false;
        }

        return true;
    }
    
    public static function getByConcursoAndEvaluador($concurso, $evaluador)
    {
        return GrupoEvaluadores::find()
            ->innerJoin('grupos_ev_x_evaluadores', 'grupos_evaluadores.id = grupos_ev_x_evaluadores.id_grupo_evaluadores')
            ->where('grupos_evaluadores.id_concurso = '.$concurso)
            ->andWhere('grupos_ev_x_evaluadores.id_evaluador = '.$evaluador)
            ->one();
    }
    
}
