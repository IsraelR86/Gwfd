<?php

namespace app\models;

use Yii;
use app\helpers\Functions;

/**
 * This is the model class for table "evaluadores".
 *
 * @property integer $id_usuario
 * @property string $fecha_nacimiento
 * @property integer $genero
 * @property integer $id_nivel_educativo
 * @property string $universidad_otro
 * @property string $profesion
 * @property string $curp
 * @property string $rfc
 * @property string $tel_celular
 * @property string $tel_fijo
 * @property integer $id_estado
 * @property integer $id_ciudad
 * @property string $cp
 * @property string $direccion
 * @property integer $estado_civil
 * @property string $colonia
 * @property integer $id_estado_nacimiento
 * @property integer $id_ciudad_nacimiento
 * @property integer $id_universidad
 * @property string $facebook
 * @property string $twitter
 * @property string $pagina_web
 *
 * @property Ciudad $ciudad
 * @property Estado $estado
 * @property Ciudad $ciudadNacimiento
 * @property Estado $estadoNacimiento
 * @property Usuario $usuario
 * @property Proyectos[] $idProyectos
 * @property Proyecto[] $proyectos
 * @property Universidad[] $universidad
 */
class Evaluador extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'evaluadores';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['universidad_otro', 'profesion', 'curp', 'rfc', 'tel_celular', 'tel_fijo', 'cp', 'direccion', 'colonia', 'facebook', 'twitter', 'pagina_web'], 'filter', 'filter' => 'strip_tags'],
            [['id_usuario'], 'required'],
            [['id_usuario', 'genero', 'id_nivel_educativo', 'id_estado', 'id_ciudad', 'estado_civil', 'id_estado_nacimiento', 'id_ciudad_nacimiento', 'id_universidad'], 'integer'],
            [['fecha_nacimiento'], 'safe'],
            //[['fecha_nacimiento'], 'date', 'format' => 'd-M-yyyy'],
            //['fecha_nacimiento', 'match', 'pattern' => '/^(0?[1-9]|[12][0-9]|3[01])[\/\-](0?[1-9]|1[012])[\/\-]\d{4}$/'],
            [['universidad_otro', 'profesion', 'curp', 'rfc', 'tel_celular', 'tel_fijo', 'cp', 'direccion', 'colonia', 'facebook', 'twitter', 'pagina_web'], 'app\validators\DelspacesValidator'],
            [['universidad_otro', 'profesion', 'colonia'], 'string', 'max' => 50],
            //[['curp'], 'string', 'length' => 18],
            //[['rfc'], 'string', 'length' => 13],
            [['curp', 'rfc'], 'filter', 'filter' => 'strtoupper'],
            [['tel_celular', 'tel_fijo'], 'string', 'max' => 15],
            [['cp'], 'string', 'max' => 6],
            [['direccion', 'facebook', 'twitter'], 'string', 'max' => 45],
            [['pagina_web'], 'string', 'max' => 100],
            //[['tel_celular', 'tel_fijo'], 'match', 'pattern' => '/^\d{10}$/', 'message' => 'El {attribute} debería contener 10 dígitos'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_usuario' => 'Usuario',
            'fecha_nacimiento' => 'Fecha de Nacimiento',
            'genero' => 'Género',
            'id_nivel_educativo' => 'Nivel Educativo',
            'universidad_otro' => 'Otra Universidad',
            'profesion' => 'Ocupación',
            'curp' => 'CURP',
            'rfc' => 'RFC',
            'tel_celular' => 'Celular',
            'tel_fijo' => 'Teléfono',
            'id_estado' => 'Estado',
            'id_ciudad' => 'Municipio',
            'cp' => 'Código Postal',
            'direccion' => 'Calle y No',
            'estado_civil' => 'Estado Civil',
            'colonia' => 'Colonia',
            'id_estado_nacimiento' => 'Estado de Nacimiento',
            'id_ciudad_nacimiento' => 'Municipio de Nacimiento',
            'id_universidad' => 'Universidad',
            'facebook' => 'Facebook',
            'twitter' => 'Twitter',
            'pagina_web' => 'Página Web',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCiudad()
    {
        return $this->hasOne(Ciudad::className(), ['id' => 'id_ciudad']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEstado()
    {
        return $this->hasOne(Estado::className(), ['id' => 'id_estado']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCiudadNacimiento()
    {
        return $this->hasOne(Ciudad::className(), ['id' => 'id_ciudad_nacimiento']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEstadoNacimiento()
    {
        return $this->hasOne(Estado::className(), ['id' => 'id_estado_nacimiento']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsuario()
    {
        return $this->hasOne(Usuario::className(), ['id' => 'id_usuario']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUniversidad()
    {
        return $this->hasOne(Universidad::className(), ['id' => 'id_universidad']);
    }

    /**
     * Devuelve la edad basandose en fecha_nacimiento y la fecha actual
     */
    public function getEdad()
    {
        $dateDiff = time() - strtotime($this->fecha_nacimiento);
        $edad = floor($dateDiff / 31556926); // 31556926 Segundos de un año

        return $edad;
    }

    public static function getEvaluacionesByProyectos($concurso, $evaluador, $proyecto=false)
    {
        return Yii::$app->db->createCommand('
            SELECT
                proyectos.id,
                proyectos.nombre,
                SUM(evaluaciones.calificacion) AS puntaje,
                SUM(rubricas.calificacion_maxima) AS calificacion_maxima
            FROM
                evaluaciones
            INNER JOIN proyectos
                ON evaluaciones.id_proyecto = proyectos.id
            INNER JOIN rubricas
                ON rubricas.id = evaluaciones.id_rubrica
            WHERE
                evaluaciones.id_concurso = '.$concurso.' AND
                evaluaciones.id_evaluador = '.$evaluador.'
                '.($proyecto ? ' AND evaluaciones.id_proyecto = '.$proyecto : '').'
            GROUP BY proyectos.id
        ')->queryAll();
    }

    public static function getConcursosActivosInstitucion($evaluador, $institucion)
    {
        $sql = 'SELECT
            concursos.id,
            concursos.nombre
        FROM
            concursos
            INNER JOIN evaluadores_x_instituciones ON
                evaluadores_x_instituciones.id_institucion = concursos.id_institucion
        WHERE concursos.id_institucion = '.(int)$institucion.'
            AND concursos.cancelado = 0
            AND CURDATE() > concursos.fecha_cierre
            AND (fecha_resultados IS NULL OR fecha_resultados < CURDATE())
            AND evaluadores_x_instituciones.id_evaluador = '.(int)$evaluador;

        return Yii::$app->db->createCommand($sql)->queryAll();
    }

    public static function getConcursosPasadosInstitucion($evaluador, $institucion)
    {
        $sql = 'SELECT
            concursos.id,
            concursos.nombre
        FROM
            concursos
            INNER JOIN evaluadores_x_instituciones ON
                evaluadores_x_instituciones.id_institucion = concursos.id_institucion
        WHERE concursos.id_institucion = '.(int)$institucion.'
            AND concursos.cancelado = 0
            AND concursos.fecha_cierre > CURDATE()
            AND evaluadores_x_instituciones.id_evaluador = '.(int)$evaluador;

        return Yii::$app->db->createCommand($sql)->queryAll();
    }

    public function getEvaluacionesByEv($evaluador)
    {
        return GruposEvXEvaluadores::find()
            ->with(
                'grupoEvaluadores.gruposEvXProyectos.proyecto.evaluaciones.idRubrica',
                'grupoEvaluadores.concurso'
            )
            ->where('id_evaluador = '.$evaluador)
            ->all();
    }

    public function aplicaConcurso($id_concurso)
    {
        $concurso = Concurso::findOne(['id' => $id_concurso]);

        $evaInst = EvaluadorXInstitucion::findOne(['id_institucion' =>$concurso->id_institucion, 'id_evaluador' => $this->id_usuario]);

        if (!$evaInst) {
            $evaInst = new EvaluadorXInstitucion();
            $evaInst->id_evaluador = $this->id_usuario;
            $evaInst->id_institucion = $concurso->id_institucion;

            if (!$evaInst->save()) {
                return Functions::errorsToString($evaInst->errors);
            }
        }

        $grupoEvaluador = new GrupoEvaluadores();

        $grupoEvaluador->id_concurso = $id_concurso;
        $grupoEvaluador->nombre = Yii::$app->user->identity->nombre_completo;
        $grupoEvaluador->fecha_alta = date('Y-m-d H:i:s');

        if (!$grupoEvaluador->save()) {
            return Functions::errorsToString($grupoEvaluador->errors);
        }

        $gpoEvaluadoresXEvaluador = new GruposEvXEvaluadores();
        $gpoEvaluadoresXEvaluador->id_grupo_evaluadores = $grupoEvaluador->id;
        $gpoEvaluadoresXEvaluador->id_evaluador = Yii::$app->user->id;
        $gpoEvaluadoresXEvaluador->fecha_alta = date('Y-m-d H:i:s');

        if (!$gpoEvaluadoresXEvaluador->save()) {
            return Functions::errorsToString($gpoEvaluadoresXEvaluador->errors);
        }

        return true;
    }

    /**
     * Obtiene el id de los proyectos pendientes por evaluar
     */
    public function proyectosPendientes()
    {
        $sql = 'SELECT grupos_ev_x_proyectos.id_proyecto FROM
                grupos_ev_x_evaluadores
            INNER JOIN grupos_evaluadores ON
                grupos_ev_x_evaluadores.id_grupo_evaluadores = grupos_evaluadores.id
            INNER JOIN grupos_ev_x_proyectos ON
                grupos_evaluadores.id = grupos_ev_x_proyectos.id_grupo_evaluadores
            WHERE
                grupos_ev_x_evaluadores.id_evaluador = '.$this->id_usuario.' AND
                grupos_ev_x_proyectos.id_proyecto NOT IN (
                    SELECT id_proyecto
                    FROM evaluaciones
                    WHERE id_evaluador = '.$this->id_usuario.'
                )';

        return Yii::$app->db->createCommand($sql)->queryAll();
    }

    public function getConcursosActivos()
    {
        return Concurso::find()
            ->innerJoin('evaluadores_x_instituciones', 'concursos.id_institucion = evaluadores_x_instituciones.id_institucion')
            ->where('cancelado != 1')
            ->andWhere('CURDATE() > fecha_cierre')
            ->andWhere('(fecha_resultados IS NULL OR fecha_resultados > CURDATE())')
            ->andWhere('evaluadores_x_instituciones.id_evaluador = '.$this->id_usuario)
            ->orderBy('fecha_resultados ASC')
            ->all();
    }

    public static function getCountProyectosAEvaluar($concurso, $evaluador)
    {
        return Yii::$app->db->createCommand(
                'SELECT
                    COUNT(grupos_ev_x_proyectos.id_proyecto) AS total_proyectos
                FROM grupos_evaluadores
                INNER JOIN grupos_ev_x_evaluadores ON
                    grupos_ev_x_evaluadores.id_grupo_evaluadores = grupos_evaluadores.id
                INNER JOIN grupos_ev_x_proyectos ON
                    grupos_ev_x_proyectos.id_grupo_evaluadores = grupos_evaluadores.id
                INNER JOIN usuarios ON
                    usuarios.id = grupos_ev_x_evaluadores.id_evaluador
                INNER JOIN proyectos ON
                    proyectos.id = grupos_ev_x_proyectos.id_proyecto
                WHERE
                    grupos_evaluadores.id_concurso = '.$concurso.' AND
                    grupos_ev_x_evaluadores.id_evaluador = '.$evaluador
            )
            ->queryScalar();
    }

}
