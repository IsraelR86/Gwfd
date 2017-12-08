<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "seccion".
 *
 * @property integer $id
 * @property string $descripcion
 *
 * @property Pregunta[] $preguntas
 */
class Seccion extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'seccion';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['descripcion'], 'filter', 'filter' => 'strip_tags'],
            [['descripcion'], 'app\validators\DelspacesValidator'],
            [['descripcion'], 'required'],
            [['descripcion'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'descripcion' => 'Descripción',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPreguntas()
    {
        return $this->hasMany(Pregunta::className(), ['id_seccion' => 'id']);
    }
    
    /**
     * Obtiene el listado de todas las secciones, incluyend sus correspondientes preguntas
     * 
     * @return Seccion[]
     */
    public static function getAll() 
    {
        return Seccion::find()
            ->with(['preguntas' => function($query) {
                $query->with(['tipoPregunta' => function($q) {
                        $q->with('tipoFiltros');
                    }
                    , 'opcionesMultiple']);
            }])
            ->all();
    }

    /**
     * Obtiene el listado de valores mínimos obtenidos es los cuestionarios
     * de un determinado concurso agrupados por sección
     *
     * @param int $id_concurso
     * @return array Arreglo associativo con los valores de id_seccion y minimo
     */
    public static function getMinBySeccion($id_concurso)
    {
        $query = 'SELECT id_seccion, MIN(ponderacion_total) AS minimo
            FROM (
                SELECT
                    concursos_aplicados.id_proyecto,
                    seccion.id AS id_seccion,
                    SUM(respuestas.ponderacion) AS ponderacion_total
                FROM seccion
                LEFT JOIN concursos_aplicados ON
                    concursos_aplicados.id_concurso = '.$id_concurso.'
                LEFT JOIN preguntas ON
                    seccion.id = preguntas.id_seccion
                LEFT JOIN respuestas ON
                    preguntas.id = respuestas.id_pregunta AND
                    respuestas.id_proyecto = concursos_aplicados.id_proyecto
                GROUP BY concursos_aplicados.id_proyecto, seccion.id
                ORDER BY concursos_aplicados.id_proyecto, seccion.id ASC
                ) AS resultadosBySecciones
            GROUP BY id_seccion
            ORDER BY id_seccion ASC';

        $result = Yii::$app->db->createCommand($query)->queryAll();

        return $result;
    }

    /**
     * Obtiene el listado de valores máximos obtenidos es los cuestionarios
     * de un determinado concurso agrupados por sección
     *
     * @param int $id_concurso
     * @return array Arreglo associativo con los valores de id_seccion y maximo
     */
    public static function getMaxBySeccion($id_concurso)
    {
        $query = 'SELECT id_seccion, MAX(ponderacion_total) AS maximo
            FROM (
                SELECT
                    concursos_aplicados.id_proyecto,
                    seccion.id AS id_seccion,
                    SUM(respuestas.ponderacion) AS ponderacion_total
                FROM seccion
                LEFT JOIN concursos_aplicados ON
                    concursos_aplicados.id_concurso = '.$id_concurso.'
                LEFT JOIN preguntas ON
                    seccion.id = preguntas.id_seccion
                LEFT JOIN respuestas ON
                    preguntas.id = respuestas.id_pregunta AND
                    respuestas.id_proyecto = concursos_aplicados.id_proyecto
                GROUP BY concursos_aplicados.id_proyecto, seccion.id
                ORDER BY concursos_aplicados.id_proyecto, seccion.id ASC
                ) AS resultadosBySecciones
            GROUP BY id_seccion
            ORDER BY id_seccion ASC';

        $result = Yii::$app->db->createCommand($query)->queryAll();

        return $result;
    }

    /**
     * Obtiene el listado de valores promedios obtenidos es los cuestionarios
     * de un determinado concurso agrupados por sección
     *
     * @param int $id_concurso
     * @return array Arreglo associativo con los valores de id_seccion y promedio
     */
    public static function getAvgBySeccion($id_concurso)
    {
        $query = 'SELECT id_seccion, ROUND(AVG(ponderacion_total)) AS promedio
            FROM (
                SELECT
                    concursos_aplicados.id_proyecto,
                    seccion.id AS id_seccion,
                    SUM(respuestas.ponderacion) AS ponderacion_total
                FROM seccion
                LEFT JOIN concursos_aplicados ON
                    concursos_aplicados.id_concurso = '.$id_concurso.'
                LEFT JOIN preguntas ON
                    seccion.id = preguntas.id_seccion
                LEFT JOIN respuestas ON
                    preguntas.id = respuestas.id_pregunta AND
                    respuestas.id_proyecto = concursos_aplicados.id_proyecto
                GROUP BY concursos_aplicados.id_proyecto, seccion.id
                ORDER BY concursos_aplicados.id_proyecto, seccion.id ASC
                ) AS resultadosBySecciones
            GROUP BY id_seccion
            ORDER BY id_seccion ASC';

        $result = Yii::$app->db->createCommand($query)->queryAll();

        return $result;
    }
    
    public static function getFromRubrica($rubrica) 
    {
        return Yii::$app->db->createCommand('
            SELECT
                seccion.id,
                seccion.descripcion
            FROM seccion
            INNER JOIN preguntas ON 
                preguntas.id_seccion = seccion.id
            INNER JOIN preguntas_x_rubricas ON
                preguntas.id = preguntas_x_rubricas.id_pregunta
            WHERE 
                preguntas_x_rubricas.id_rubrica = '.$rubrica.'
            GROUP BY 
                seccion.id,
                seccion.descripcion
            ORDER BY 
                seccion.id
        ')->queryAll();
    }
}
