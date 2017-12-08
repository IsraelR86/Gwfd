<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "concursos_aplicados".
 *
 * @property integer $id_concurso
 * @property integer $id_proyecto
 * @property string $fecha_alta
 * @property string $clave
 * @property integer $paso_filtros
 * @property integer $calificacion
 * @property string $filtros_no_pasados
 *
 * @property Concurso $concurso
 * @property Proyecto $proyecto
 */
class ConcursoAplicado extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'concursos_aplicados';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['clave', 'filtros_no_pasados'], 'filter', 'filter' => 'strip_tags'],
            [['clave', 'filtros_no_pasados'], 'app\validators\DelspacesValidator'],
            [['id_concurso', 'id_proyecto', 'fecha_alta', 'clave'], 'required'],
            [['id_concurso', 'id_proyecto', 'paso_filtros', 'calificacion'], 'integer'],
            [['fecha_alta'], 'safe'],
            [['fecha_alta'], 'date', 'format' => 'yyyy-M-d H:m:s'],
            [['clave'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_concurso' => 'Concurso',
            'id_proyecto' => 'Proyecto',
            'fecha_alta' => 'Fecha de Registro',
            'clave' => 'No. de Registro',
            'paso_filtros' => '¿Pasó todos los filtros?',
            'calificacion' => 'Calificación automática obtenida',
            'filtros_no_pasados' => 'Filtros no pasados',
        ];
    }
    
    /**
     * @inheritdoc
     *
     */
    public function extraFields()
    {
        $extraFields = parent::extraFields();
        
        // Se agregan estos campos para poder ser exportados por toArray
        $extraFields['concurso'] = 'concurso';
        $extraFields['proyecto'] = 'proyecto';
        
        return $extraFields;
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
    public function getProyecto()
    {
        return $this->hasOne(Proyecto::className(), ['id' => 'id_proyecto']);
    }

    /**
     * Genera la clave para el proyecto inscrito en el concurso actual
     * formato de la clave YYYYMMDD-N
     */
    public function createClave()
    {
        $query = 'SELECT
                CONCAT(DATE_FORMAT(NOW(), \'%Y%m%d\'),\'-\',(COUNT(*) + 1)) AS clave
            FROM
                concursos_aplicados
            WHERE
                id_concurso = '.$this->id_concurso.' AND
                DATE_FORMAT(fecha_alta, \'%Y-%m-%d\') = CURDATE()';

        $result = Yii::$app->db->createCommand($query)->queryOne();

        $this->clave = $result['clave'];

        return $result['clave'];
    }

    /**
     * Obtiene el ConcursoAplicado con base en id_proyecto e id_concurso
     */
    public static function getConcursoAplicado($id_proyecto, $id_concurso)
    {
        $result = ConcursoAplicado::find()
            ->where('id_proyecto = :id_proyecto', [':id_proyecto' => $id_proyecto])
            ->andWhere('id_concurso = :id_concurso', [':id_concurso' => $id_concurso])
            ->one();

        return $result;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChecklistDocumentos()
    {
        return $this->hasOne(ChecklistDocumentos::className(), ['id_proyecto' => 'id_proyecto', 'id_concurso' => 'id_concurso']);
    }

    /**
     * Obtiene todos los datos necesarios para imprimir la gráfica de los resultados del cuestionario
     */
    public function getDatosGraficaCuestionario()
    {
        $datos = [];
        $proyecto = $this->proyecto;

        $datos['maximo'] = ArrayHelper::getColumn(Seccion::getMaxBySeccion($this->id_concurso), function ($element) { return (int)$element['maximo']; });
        $datos['minimo'] = ArrayHelper::getColumn(Seccion::getMinBySeccion($this->id_concurso), function ($element) { return (int)$element['minimo']; });
        $datos['promedio'] = ArrayHelper::getColumn(Seccion::getAvgBySeccion($this->id_concurso), function ($element) { return (int)$element['promedio']; });
        $datos['cuestionario'] = ArrayHelper::getColumn($proyecto->getPonderacionRespuestasByCategoria(), function ($element) { return (int)$element['ponderacion_total']; });
        $datos['total'] = $proyecto->getPonderacionTotalRespuestas();
        $datos['desc_secciones'] = ArrayHelper::getColumn(Seccion::find()->orderBy('id ASC')->all(), 'descripcion');

        if (count($datos['desc_secciones'])) {
            for($i=1; $i<=count($datos['desc_secciones']); $i++) {
                $datos['secciones'][] = 'Sección '.$i;
            }
        }

        return $datos;
    }
    
    /**
     * Obtiene el listado de concursos a los que ha aplicado un emprendedor
     * 
     * @param int $emprendedor ID del emprendedor
     * @param int $page Página a mostrar
     * @param int $no_items Número de elementos a mostrar, default 5
     * 
     * @return ConcursoAplicado[]
     */
    public static function getByEmprendedor($emprendedor, $page, $no_items = 5) 
    {
        return ConcursoAplicado::find()
            ->innerJoin('concursos', 'concursos_aplicados.id_concurso = concursos.id')
            ->innerJoin('proyectos', 'proyectos.id = concursos_aplicados.id_proyecto AND 
                    proyectos.id_emprendedor_creador = '.(int)$emprendedor)
            ->orderBy('concursos_aplicados.fecha_alta DESC, concursos.fecha_arranque ASC')
            ->limit($no_items)
            ->offset($page*$no_items)
            ->all();
    }
    
    /**
     * 
     */
    public static function getByAplicacion($concurso, $proyecto, $emprendedor) 
    {
        return ConcursoAplicado::find()
            ->innerJoin('proyectos', 'proyectos.id = concursos_aplicados.id_proyecto AND 
                    proyectos.id_emprendedor_creador = '.(int)$emprendedor)
            ->where('id_proyecto = '.(int)$proyecto)
            ->andWhere('id_concurso = '.(int)$concurso)
            ->all();
    }
    
    /**
     * 
     */
    public function abandonar()
    {
        // Eliminamos todas las respuestas a las preguntas
        RespuestaConcurso::deleteAll(['id_concurso' => $this->id_concurso, 'id_proyecto' => $this->id_proyecto]);
        
        // Eliminamos la aplicación
        $this->delete();
    }
    
    /**
     * 
     */
    public function getPuntajeByRubrica($evaluador = false)
    {
        $query = 'SELECT 
                rubricas.id,
                rubricas.nombre,
                rubricas.descripcion,
                SUM(rubricas.calificacion_maxima) AS calificacion_maxima,
                SUM(evaluaciones.calificacion) AS puntaje,
                GROUP_CONCAT(evaluaciones.comentarios SEPARATOR ", ") AS comentarios
            FROM 
                rubricas 
            LEFT JOIN evaluaciones ON
                evaluaciones.id_rubrica = rubricas.id AND
                evaluaciones.id_concurso = rubricas.id_concurso AND
                evaluaciones.id_proyecto = '.$this->id_proyecto.'
                '.($evaluador ? ' AND evaluaciones.id_evaluador = '.$evaluador : '').'
            WHERE 
                rubricas.id_concurso = '.$this->id_concurso.'
            GROUP BY
                rubricas.id,
                rubricas.nombre,
                rubricas.descripcion,
                rubricas.calificacion_maxima';
        
        return Yii::$app->db->createCommand($query)->queryAll();
    }
    
    /**
     * 
     */
    public function getPuntajeTotal()
    {
        $query = 'SELECT 
                SUM(evaluaciones.calificacion) AS puntaje,
                SUM(rubricas.calificacion_maxima) AS calificacion_maxima
            FROM 
                rubricas 
            INNER JOIN evaluaciones ON
                evaluaciones.id_rubrica = rubricas.id AND
                evaluaciones.id_concurso = rubricas.id_concurso AND
                evaluaciones.id_proyecto = '.$this->id_proyecto.'
            WHERE 
                rubricas.id_concurso = '.$this->id_concurso;
        
        return Yii::$app->db->createCommand($query)->queryOne();
    }
    
}
