<?php

namespace app\models;

use Yii;
use app\helpers\Functions;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "concursos".
 *
 * @property integer $id
 * @property integer $id_institucion
 * @property string $nombre
 * @property string $descripcion
 * @property string $bases
 * @property string $premios
 * @property string $fecha_arranque
 * @property string $fecha_cierre
 * @property string $fecha_resultados
 * @property integer $max_socios
 * @property integer $socios_pueden_ver_proyecto *
 * @property integer $socios_pueden_editar_proyecto *
 * @property integer $creador_proyecto_restringido_a_id_estado_nacimiento *
 * @property integer $socios_proyecto_restringidos_a_id_estado_nacimiento *
 * @property double $calificacion_minima_proyectos
 * @property integer $no_ganadores
 * @property integer $edad_minima_emprendedores *
 * @property integer $edad_maxima_emprendedores *
 * @property integer $resultados_filtrado_visibles *
 * @property string $fecha_limite_envio_plan *
 * @property integer $id_concurso_anterior
 * @property integer $restringir_participantes
 * @property string $ids_participante
 * @property string $fechaCierre
 * @property int $evaluadores_x_proyecto
 *
 * @property Institucion $institucion
 * @property Proyectos[] $proyectos
 * @property Evaluaciones[] $evaluaciones
 * @property FiltrosConcurso $filtrosConcurso
 * @property Ganadores[] $ganadores
 * @property GruposEvaluadores[] $gruposEvaluadores
 * @property Rubrica[] $rubricas
 * @property Concurso $concursoAnterior
 * @property Etiqueta[] $etiquetas
 * @property FiltroParticipanteXConcurso[] $filtrosParticipanteXConcurso
 */
class Concurso extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'concursos';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nombre', 'descripcion', 'bases', 'premios', 'ids_participantes'], 'filter', 'filter' => 'strip_tags'],
            [['nombre', 'descripcion', 'fecha_arranque', 'fecha_cierre', 'id_institucion', 'calificacion_minima_proyectos'], 'required'],
            [['id_institucion', 'max_socios', 'id_concurso_anterior', 'restringir_participantes', 'calificacion_minima_proyectos', 'evaluadores_x_proyecto', 'no_ganadores'], 'integer'],
            [['nombre', 'descripcion', 'bases', 'premios'], 'app\validators\DelspacesValidator'],
            [['bases', 'premios'], 'string'],
            [['fecha_arranque', 'fecha_cierre', 'fecha_resultados'], 'safe'],
            [['fecha_arranque', 'fecha_cierre', 'fecha_resultados'], 'date', 'format' => 'yyyy-M-d'],
            [['calificacion_minima_proyectos'], 'number'],
            [['nombre'], 'string', 'max' => 100],
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
            'id_institucion' => 'Institución',
            'nombre' => 'Nombre',
            'descripcion' => 'Descripción',
            'bases' => 'Bases',
            'premios' => 'Premios',
            'fecha_arranque' => 'Fecha de Arranque',
            'fecha_cierre' => 'Fecha de Cierre',
            'fecha_resultados' => 'Fecha de Resultados',
            'max_socios' => 'Máximo de Socios',
            'calificacion_minima_proyectos' => 'Calificacion Mínima',
            'no_ganadores' => 'Número de ganadores',
            'evaluadores_x_proyecto' => 'Número de evaluadores por proyecto',
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
        $extraFields['byteImagen'] = 'byteImagen';
        $extraFields['fechaCierre'] = 'fechaCierre';
        $extraFields['fechaArranque'] = 'fechaArranque';
        $extraFields['preguntas'] = 'preguntas';
        $extraFields['status'] = 'status';
        $extraFields['noEvaluadores'] = 'noEvaluadores';
        $extraFields['proyectosRegistrados'] = 'proyectosRegistrados';
        $extraFields['proyectosCompletados'] = 'proyectosCompletados';
        $extraFields['superanEvaluacionATM'] = 'superanEvaluacionATM';
        $extraFields['posiblesPlagios'] = 'posiblesPlagios';
        $extraFields['rubricas'] = 'rubricas';
        $extraFields['countProyectosEvaluados'] = 'countProyectosEvaluados';
        $extraFields['countProyectosAEvaluador'] = 'countProyectosAEvaluador';

        return $extraFields;
    }

    public static function getUploadDir()
    {
        return __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.
               Yii::$app->params['upload_dir'].DIRECTORY_SEPARATOR.
               'concurso'.DIRECTORY_SEPARATOR;
    }

    public function getPathImagen()
    {
        return self::getUploadDir().$this->id.'.jpg';
    }

    public function getByteimagen()
    {
        $pathImagen = $this->pathImagen;

        if (!file_exists($pathImagen)) {
            return 'http://placehold.it/350x150';
        }

        $type = pathinfo($pathImagen, PATHINFO_EXTENSION);
        $imagenByte = file_get_contents($pathImagen);
        $base64Imagen = 'data:image/' . $type . ';base64,' . base64_encode($imagenByte);

        return $base64Imagen;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstitucion()
    {
        return $this->hasOne(Institucion::className(), ['id' => 'id_institucion']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProyectos()
    {
        return $this->hasMany(Proyecto::className(), ['id' => 'id_proyecto'])->viaTable('concursos_aplicados', ['id_concurso' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvaluaciones()
    {
        return $this->hasMany(Evaluaciones::className(), ['id_concurso' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPreguntas()
    {
        return $this->hasMany(PreguntaXConcurso::className(), ['id_concurso' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFiltrosConcurso()
    {
        return $this->hasMany(FiltroConcurso::className(), ['id_concurso' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGanadores()
    {
        return $this->hasMany(Ganadores::className(), ['id_concurso' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGruposEvaluadores()
    {
        return $this->hasMany(GrupoEvaluadores::className(), ['id_concurso' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRubricas()
    {
        return $this->hasMany(Rubrica::className(), ['id_concurso' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEstadoNacimiento()
    {
        return $this->hasOne(Estado::className(), ['id' => 'creador_proyecto_restringido_a_id_estado_nacimiento']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConcursoAnterior()
    {
        return $this->hasOne(Concurso::className(), ['id' => 'id_concurso_anterior']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEtiquetas()
    {
        return $this->hasMany(Etiqueta::className(), ['id' => 'id_etiqueta'])
                    ->viaTable('etiquetas_x_concursos', ['id_concurso' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFiltrosParticipanteXConcurso()
    {
        return $this->hasMany(FiltroParticipanteXConcurso::className(), ['id_concurso' => 'id']);
    }

    public function getFechaCierre()
    {
        return Functions::transformDate($this->fecha_cierre, 'd-m-Y');
    }

    public function getFechaArranque()
    {
        return Functions::transformDate($this->fecha_arranque, 'd-m-Y');
    }

    public function getNoEvaluadores()
    {
        return Yii::$app->db->createCommand('SELECT
                COUNT(grupos_ev_x_evaluadores.id_evaluador) AS no_evaluadores
            FROM
                grupos_evaluadores
            INNER JOIN grupos_ev_x_evaluadores ON
                grupos_ev_x_evaluadores.id_grupo_evaluadores = grupos_evaluadores.id
            WHERE
                grupos_evaluadores.id_concurso = '.$this->id)
            ->queryScalar();
    }

    public function getProyectosRegistrados()
    {
        return Yii::$app->db->createCommand('SELECT COUNT(id_proyecto) AS proyectosRegistrados
                FROM concursos_aplicados WHERE id_concurso = '.$this->id)
            ->queryScalar();
    }

    public function getProyectosCompletados()
    {
        $noProyectosCompletados = 0;

        $proyectos = Proyecto::find()
            ->innerJoin('concursos_aplicados', 'concursos_aplicados.id_proyecto = proyectos.id AND concursos_aplicados.id_concurso = '.$this->id)
            ->all();

        if (count($proyectos)) {
            foreach ($proyectos as $proyecto) {
                if ($proyecto->isCompletado()) {
                    $noProyectosCompletados++;
                }
            }
        }

        return $noProyectosCompletados;
    }

    public function getSuperanEvaluacionATM()
    {
        return Yii::$app->db->createCommand('SELECT COUNT(id_proyecto) AS proyectosRegistrados
                FROM concursos_aplicados WHERE id_concurso = '.$this->id.' AND paso_filtros = 1')
            ->queryScalar();
    }

    public function getPosiblesPlagios()
    {
        return 0;
    }

    /**
     * Revisa todas las reglas para poder participar en este concurso
     *
     */
    public function checkRulesToApply($Emprendedor)
    {
        $errors = [];

        $result = $this->checkEstadoNacimiento($Emprendedor);

        if (is_array($result)) {
            $errors = array_merge($errors, $result);
        }

        $result = $this->checkEdad($Emprendedor);

        if (is_array($result)) {
            $errors = array_merge($errors, $result);
        }

        if (!$this->isValidFechaRegistro()) {
            $errors['fecha_registro'] = 'No tiene permitido registrar sus datos, el periodo de registro para el concurso es del '.Functions::transformDate($this->fecha_arranque, 'd-m-Y').' al '.Functions::transformDate($this->fecha_cierre, 'd-m-Y');
        }

        return $errors;
    }

    /**
     * Revisa si la bandera creador_proyecto_restringido_a_id_estado_nacimiento esta activada
     * si es así, valida que el id_estado_nacimiento de Emprendedor cumpla con esta restricción
     *
     * @param Emprendedor $Emprendedor
     * @return boolean | array
     */
    public function checkEstadoNacimiento($Emprendedor)
    {
        if (!empty($this->creador_proyecto_restringido_a_id_estado_nacimiento)) {
            if ($this->creador_proyecto_restringido_a_id_estado_nacimiento != $Emprendedor->id_estado_nacimiento) {
                return ['creador_proyecto_restringido_a_id_estado_nacimiento' => 'Este concurso es para personas nacidas en el Estado de <strong>'.$this->estadoNacimiento->descripcion.'</strong>. Te invitamos a ver la convocatoria del concurso para demostrar tu residencia'];
            }
        }

        return true;
    }

    /**
     * Revisa si las banderas de edad_minima_emprendedores y edad_maxima_emprendedores esta activada
     * si es así, valida que la edad del emprendedor cumpla con estas restricciones
     *
     * @param Emprendedor $Emprendedor
     * @return boolean | array
     */
    public function checkEdad($Emprendedor)
    {
        $edad = $Emprendedor->edad;

        if (!empty($this->edad_minima_emprendedores) && !empty($this->edad_maxima_emprendedores)) {
            if ($edad < $this->edad_minima_emprendedores || $edad > $this->edad_maxima_emprendedores) {
                return ['edad_emprendedores' => 'No puede aplicar para este concurso porque no cumple con la edad mínima o máxima, la edad mínima es '.$this->edad_minima_emprendedores.' y la edad máxima es de '.$this->edad_maxima_emprendedores.', tu edad es de '.$edad];
            }
        } elseif (!empty($this->edad_minima_emprendedores)) {
            if ($edad < $this->edad_minima_emprendedores) {
                return ['edad_minima_emprendedores' => 'No puede aplicar para este concurso porque no cumple con la edad mínima, la edad mínima es '.$this->edad_minima_emprendedores.', tu edad es de '.$edad];
            }
        } elseif (!empty($this->edad_maxima_emprendedores)) {
            if ($edad > $this->edad_maxima_emprendedores) {
                return ['edad_maxima_emprendedores' => 'No puede aplicar para este concurso porque no cumple con la edad máxima, la edad máxima es de '.$this->edad_maxima_emprendedores.', tu edad es de '.$edad];
            }
        }

        return true;
    }

    /**
     * Valida que el número de integrantes registrador no supere el
     * número máximo de socios permitidos por el concurso
     *
     * @param int $noIntegrante
     */
    public function validMaxSocios($noIntegrante)
    {
        // Se le resta 1 porque se incluye al creador como integrante
        if (($noIntegrante) >= $this->max_socios) {
            return ['max_socios' => 'Se alcanzó el máximo de integrantes permitidos por el concurso'];
        }

        return true;
    }

    /**
     * Obtiene todos los proyectos con ponderacion total de las preguntas
     * mayor o igual a calificacion_minima_proyectos
     *
     * @return array app\models\Proyecto
     */
    public function getProyectosAprobadosFromCuestionario()
    {
        return Proyecto::find()
            ->select([
                '{{proyectos}}.*',
                'SUM({{respuestas}}.ponderacion) AS sumRespuestasPonderacion'
            ])
            ->joinWith('respuestas') // Relación en la clase Proyecto
            ->innerJoin('concursos_aplicados', 'concursos_aplicados.id_proyecto = proyectos.id AND '.
                                               'concursos_aplicados.id_concurso = '.$this->id)
            ->innerJoin('checklist_documentos', 'checklist_documentos.id_proyecto = proyectos.id AND '.
                                               'checklist_documentos.id_concurso = '.$this->id)
            ->where('concursos_aplicados.id_concurso = '.$this->id)
            ->andWhere('checklist_documentos.aplicacion_aprobada = 1') // Solo se toman los proyectos aprobados
            ->groupBy('{{proyectos}}.id')
            ->having('sumRespuestasPonderacion >= '.$this->calificacion_minima_proyectos) // la suma de la ponderación de las respuestas debe ser mayor que calificación mínima
            ->orderBy('sumRespuestasPonderacion DESC')
            ->all();
    }

    /**
     * Obtiene todos los proyectos con ponderacion total de las preguntas del concurso
     * mayor o igual a calificacion_minima_proyectos
     *
     * @return array app\models\Proyecto
     */
    public function getProyectosAprobadosFromCuestionarioConcurso()
    {
        return Proyecto::find()
            ->select([
                '{{proyectos}}.*',
                'SUM({{respuestas_concurso}}.ponderacion) AS sumRespuestasPonderacion'
            ])
            ->innerJoin('respuestas_concurso', 'respuestas_concurso.id_proyecto = proyectos.id AND '.
                                               'respuestas_concurso.id_concurso = '.$this->id)
            ->innerJoin('concursos_aplicados', 'concursos_aplicados.id_proyecto = proyectos.id AND '.
                                               'concursos_aplicados.id_concurso = '.$this->id)
            ->where('concursos_aplicados.id_concurso = '.$this->id.' AND (concursos_aplicados.paso_filtros = 1 OR concursos_aplicados.paso_filtros IS NULL)')
            ->groupBy('{{proyectos}}.id')
            ->having('sumRespuestasPonderacion >= '.$this->calificacion_minima_proyectos) // la suma de la ponderación de las respuestas debe ser mayor que calificación mínima
            ->orderBy('sumRespuestasPonderacion DESC')
            ->all();
    }

    /**
     * Obtiene todos los proyectos con ponderacion total de las preguntas
     * menor a calificacion_minima_proyectos requerida por el concurso
     *
     * @return array app\models\Proyecto
     */
    public function getProyectosNoAprobadosFromCuestionario()
    {
        return Proyecto::find()
            ->select([
                '{{proyectos}}.*',
                'SUM({{respuestas}}.ponderacion) AS sumRespuestasPonderacion'
            ])
            ->joinWith('respuestas') // Relación en la clase Proyecto
            ->innerJoin('concursos_aplicados', 'concursos_aplicados.id_proyecto = proyectos.id AND '.
                                               'concursos_aplicados.id_concurso = '.$this->id)
            ->where('concursos_aplicados.id_concurso = '.$this->id)
            ->groupBy('{{proyectos}}.id')
            ->having('sumRespuestasPonderacion < '.$this->calificacion_minima_proyectos)
            ->orHaving('sumRespuestasPonderacion IS NULL')
            ->orderBy('sumRespuestasPonderacion DESC')
            ->all();
    }

    /**
     * Obtiene todos los proyectos con ponderacion total de las preguntas del concurso
     * menor a calificacion_minima_proyectos requerida por el concurso
     *
     * @return array app\models\Proyecto
     */
    public function getProyectosNoAprobadosFromCuestionarioConcurso()
    {
        return Proyecto::find()
            ->select([
                '{{proyectos}}.*',
                'concursos_aplicados.paso_filtros',
                'SUM({{respuestas_concurso}}.ponderacion) AS sumRespuestasPonderacion'
            ])
            ->innerJoin('respuestas_concurso', 'respuestas_concurso.id_proyecto = proyectos.id AND '.
                                               'respuestas_concurso.id_concurso = '.$this->id)
            ->innerJoin('concursos_aplicados', 'concursos_aplicados.id_proyecto = proyectos.id AND '.
                                               'concursos_aplicados.id_concurso = '.$this->id)
           // Se pueden obtener los proyectos que alcanzarón la puntuación mínima pero que no fueron aprobados por la institución
            ->where('concursos_aplicados.id_concurso = '.$this->id)
            ->groupBy('{{proyectos}}.id')
            ->having('sumRespuestasPonderacion < '.$this->calificacion_minima_proyectos.' OR '.
                'sumRespuestasPonderacion IS NULL OR '.
                'concursos_aplicados.paso_filtros = 0')
            ->orderBy('sumRespuestasPonderacion DESC')
            ->all();
    }

    /**
     * Obtiene todos los proyectos rechazados
     *
     * @return array app\models\Proyecto
     */
    public function getProyectosRechazados()
    {
        return Proyecto::find()
            ->select([
                '{{proyectos}}.*',
                'SUM({{respuestas}}.ponderacion) AS sumRespuestasPonderacion'
            ])
            ->joinWith('respuestas') // Relación en la clase Proyecto
            ->innerJoin('concursos_aplicados', 'concursos_aplicados.id_proyecto = proyectos.id AND '.
                                               'concursos_aplicados.id_concurso = '.$this->id)
            ->innerJoin('checklist_documentos', 'checklist_documentos.id_proyecto = proyectos.id AND '.
                                               'checklist_documentos.id_concurso = '.$this->id)
            ->where('concursos_aplicados.id_concurso = '.$this->id)
            ->andWhere('checklist_documentos.aplicacion_aprobada <> 1') // Solo se toman los proyectos rechazados(2)/pendientes(0)
            ->groupBy('{{proyectos}}.id')
            ->all();
    }

    /**
     * Valida si la fecha actual esta entre las fechas fecha_arranque y fecha_cierre para permitir que el usuario registre sus datos
     */
    public function isValidFechaRegistro()
    {
        $fecha_actual = new \DateTime('NOW');
        $fecha_arranque = new \DateTime($this->fecha_arranque.' 00:00:00'); // Se agrega las horas a la fecha porque la comparación se hace con un datetime
        $fecha_cierre = new \DateTime($this->fecha_cierre.' 23:59:00');

        if ($fecha_actual<$fecha_arranque || $fecha_actual>$fecha_cierre) {
            return false;
        }

        return true;
    }

    /**
     * Valida si la fecha actual es menor que la fecha_limite_envio_plan
     */
    public function isValidFechaEnvioPlan()
    {
        $fecha_actual = new \DateTime('NOW');
        $fecha_limite_envio_plan = new \DateTime($this->fecha_limite_envio_plan.' 23:59:00'); // Se agrega las horas a la fecha porque la comparación se hace con un datetime

        if ($fecha_actual <= $fecha_limite_envio_plan) {
            return true;
        }

        return false;
    }

    /**
     * Obtiene el listado de concursos disponibles en la fecha actual,
     * limitado por la $page y $no_items
     *
     * @param int $page Página a mostrar
     * @param int $no_items Número de elementos a mostrar, default 5
     *
     * @return Concurso[]
     */
    public static function getAllAvailables($page, $no_items = 5, $filter_etiquetas = null)
    {
        return Concurso::find()
            ->select([
                '{{concursos}}.*',
                '(fecha_arranque<CURRENT_DATE()) AS post_arranque',
                '(fecha_cierre<CURRENT_DATE()) AS post_cierre'
            ])
            ->with(['institucion', 'etiquetas'])
            ->leftJoin('(SELECT DISTINCT id_concurso FROM etiquetas_x_concursos '.
                ($filter_etiquetas ? ' WHERE id_etiqueta IN ('.implode(',', $filter_etiquetas).') ' : '').
                ') AS etiquetas_x_concursos', 'etiquetas_x_concursos.id_concurso = concursos.id')
            ->where('CURRENT_DATE() BETWEEN fecha_arranque AND fecha_cierre')
            //->where('fecha_arranque <= CURDATE()')
            //->andWhere('fecha_cierre >= CURDATE()')
            ->andWhere('cancelado = 0')
            ->andFilterWhere(['id_etiqueta' => $filter_etiquetas])
            ->orderBy('post_arranque, post_cierre, fecha_arranque ASC, fecha_cierre ASC, fecha_resultados ASC')
            ->limit($no_items)
            ->offset($page*$no_items)
            ->all();
    }

    /**
     * Obtiene el listado de concursos disponibles en la fecha actual,
     * limitado por la $page y $no_items
     *
     * @param int $institucion ID de la institucion
     * @param int $page Página a mostrar
     * @param int $no_items Número de elementos a mostrar, default 5
     *
     * @return Concurso[]
     */
    public static function getAllByInstitucion($institucion, $page, $no_items = 5, $filterWhere = ['cancelado' => null], $filter_etiquetas = null)
    {
        return Concurso::find()
            ->select([
                '{{concursos}}.*',
                '(fecha_arranque<CURRENT_DATE()) AS post_arranque',
                '(fecha_cierre<CURRENT_DATE()) AS post_cierre'
            ])
            ->with(['institucion', 'etiquetas'])
            ->leftJoin('(SELECT DISTINCT id_concurso FROM etiquetas_x_concursos '.
                ($filter_etiquetas ? ' WHERE id_etiqueta IN ('.implode(',', $filter_etiquetas).') ' : '').
                ') AS etiquetas_x_concursos', 'etiquetas_x_concursos.id_concurso = concursos.id')
            ->where('id_institucion = '.$institucion)
            ->andFilterWhere($filterWhere)
            ->orderBy('post_arranque, post_cierre, fecha_arranque ASC, fecha_cierre ASC, fecha_resultados ASC')
            ->limit($no_items)
            ->offset($page*$no_items)
            ->all();
    }

    /**
     * Obtiene el listado de todos los concursos
     * limitado por la $page y $no_items
     *
     * @param int $page Página a mostrar
     * @param int $no_items Número de elementos a mostrar, default 5
     *
     * @return Concurso[]
     */
    public static function getAll($page, $no_items = 5)
    {
        return Concurso::find()
            ->select([
                '{{concursos}}.*',
                '(fecha_arranque<CURRENT_DATE()) AS post_arranque',
                '(fecha_cierre<CURRENT_DATE()) AS post_cierre'
            ])
            ->with(['institucion', 'etiquetas'])
            ->orderBy('post_arranque, post_cierre, fecha_arranque ASC, fecha_cierre ASC, fecha_resultados ASC')
            ->limit($no_items)
            ->offset($page*$no_items)
            ->all();
    }

    /**
     * Obtiene el concurso especificado por $id, incluye los datos de institucon y etiquetas
     *
     * @param int $id
     *
     * @return Concurso
     */
    public static function getById($id, $institucion = null)
    {
        return Concurso::find()
            ->where(['id' => $id])
            ->with([
                'institucion' => function ($query) {
                    $query->select(['nombre', 'pagina_web']);
                },
                'etiquetas'])
            ->andFilterWhere(['id_institucion' => $institucion])
            ->one();
    }

    /**
     * Obtiene el listado de concursos a los que ha aplicado un emprendedor
     *
     * @param int $emprendedor ID del emprendedor
     * @param int $page Página a mostrar
     * @param int $no_items Número de elementos a mostrar, default 5
     *
     * @return Concurso[]
     */
    public static function getByEmprendedor($emprendedor, $page, $no_items = 5)
    {
        return Concurso::findBySql('SELECT DISTINCT concursos.* FROM concursos
                INNER JOIN concursos_aplicados ON
                    concursos_aplicados.id_concurso = concursos.id
                INNER JOIN proyectos ON
                    proyectos.id = concursos_aplicados.id_proyecto AND
                    proyectos.id_emprendedor_creador = '.(int)$emprendedor.'
                ORDER BY fecha_arranque DESC, fecha_cierre DESC, fecha_resultados DESC LIMIT '.$no_items.' OFFSET '.($page*$no_items))
            ->all();
    }

    public function getFileBases()
    {
        // Se agrega guión bajo (_) al inicio del documento para evitar la colisión por nombre
        $files = glob(Yii::$app->basePath.DIRECTORY_SEPARATOR.
            Yii::$app->params['upload_dir'].DIRECTORY_SEPARATOR.
            'concurso'.DIRECTORY_SEPARATOR.
            '_'.$this->id . '_bases.*');

        if (count($files)) {
            return $files[0];
        }

        return null;
    }

    public function getNombreArchivoBases()
    {
        if ($this->fileBases != null) {
            $nombre = 'Bases - '.substr($this->nombre, 0, 35);
            $archivo = explode('.', $this->fileBases);

            return $nombre.'.'.$archivo[1];
        } else {
            return 'Archivo no encontrado';
        }
    }

    public function downloadBases()
    {
        $ruta = $this->fileBases;

        if ($ruta != null) {
            // http://johnculviner.com/jquery-file-download-plugin-for-ajax-like-feature-rich-file-downloads/
            // La Cookie fileDownload es necesario para el Plugin jQuery File Download
            header('Set-Cookie: fileDownload=true; path=/');
            header('Cache-Control: max-age=60, must-revalidate');
        }

        return $ruta;
    }

    public function getStatus()
    {
        $fecha_cierre = new \DateTime($this->fecha_cierre);
        $fecha_actual = new \DateTime( date('Y-m-d') );

        if ($fecha_actual <= $fecha_cierre) {
            return 'EN PROCESO';
        }

        return 'FINALIZADO';
    }

    public function getResultadosEvaluacionProyectos()
    {
        return Proyecto::find()
            ->select([
                '{{proyectos}}.*',
                'SUM(evaluaciones.calificacion) AS sumTotalEvaluaciones',
                '(SELECT SUM(ponderacion)
                FROM respuestas_concurso
                WHERE id_concurso = concursos_aplicados.id_concurso AND
                      id_proyecto = concursos_aplicados.id_proyecto) AS sumRespuestasPonderacion'
            ])
            ->innerJoin('concursos_aplicados', 'concursos_aplicados.id_proyecto = proyectos.id AND '.
                                               'concursos_aplicados.id_concurso = '.$this->id)
            ->innerJoin('rubricas', 'rubricas.id_concurso = concursos_aplicados.id_concurso')
            ->innerJoin('evaluaciones', 'evaluaciones.id_rubrica = rubricas.id AND
                                         evaluaciones.id_proyecto = proyectos.id')
            ->groupBy('{{proyectos}}.id')
            ->orderBy('sumTotalEvaluaciones DESC, sumRespuestasPonderacion DESC')
            ->all();
    }

    /**
     * Obtiene el listado de usuarios que son evaluadores del concurso
     *
     * @return Usuario[]
     */
    public function getEvaluadores()
    {
        return Usuario::find()
            ->innerJoin('evaluadores', ' usuarios.id = evaluadores.id_usuario AND usuarios.activo = 1')
            ->innerJoin('grupos_ev_x_evaluadores', 'grupos_ev_x_evaluadores.id_evaluador = evaluadores.id_usuario')
            ->innerJoin('grupos_evaluadores', 'grupos_evaluadores.id = grupos_ev_x_evaluadores.id_grupo_evaluadores AND
                                               grupos_evaluadores.id_concurso = '.$this->id)
            ->all();
    }

    public function getProyectosEvaluador($evaluador)
    {
        return Proyecto::find()
            ->innerJoin('grupos_ev_x_proyectos', 'proyectos.id = grupos_ev_x_proyectos.id_proyecto')
            ->innerJoin('grupos_evaluadores', 'grupos_ev_x_proyectos.id_grupo_evaluadores = grupos_evaluadores.id')
            ->innerJoin('grupos_ev_x_evaluadores', 'grupos_evaluadores.id = grupos_ev_x_evaluadores.id_grupo_evaluadores')
            //->where('grupos_evaluadores.id_concurso = 2')
            ->andWhere('grupos_ev_x_evaluadores.id_evaluador = '.$evaluador)
            ->orderBy('proyectos.nombre ASC')
            ->all();
    }

    /**
     * Ejecuta la evaluación automatica,
     * Evalua la calificacion del cuestionario
     * y aplica los filtros
     */
    public function evaluacionAutomatica()
    {
        // Primero se obtienen los proyectos que no alcanzarón el mínimo de puntuación
        // requerido por el concurso (concursos.calificacion_minima_proyectos)
        $proyectosNoAprobados = $this->getProyectosNoAprobadosFromCuestionarioConcurso();

        foreach ($proyectosNoAprobados as $proyecto) {
            $concursoAplicado = $proyecto->getConcursoAplicado($this->id);

            if ($concursoAplicado->filtros_no_pasados == '') {
                $concursoAplicado->filtros_no_pasados = $this->crearFiltroNoPasado(0, "La puntuación total del Cuestionario NO alcanzó la calificación mínima requerida por el Concurso");
            }

            $concursoAplicado->paso_filtros = 0;
            $concursoAplicado->calificacion = $proyecto->sumRespuestasPonderacion == null ? 0 : $proyecto->sumRespuestasPonderacion;

            $concursoAplicado->save();
        }

        // Obtenemos todos los proyectos que aprobaron el filtro de calificación mínima (concursos.calificacion_minima_proyectos)
        $proyectosAprobadosFromCuestionario = $this->getProyectosAprobadosFromCuestionarioConcurso();
        // Obtenemos todos los filtros que aplican al concurso
        $filtros = $this->filtrosConcurso;

        // Recorrer todos los proyectos que aprobaron el filtro de calificación mínima (concursos.calificacion_minima_proyectos)
        foreach ($proyectosAprobadosFromCuestionario as $proyecto) {
            // Variable bandera para determinar si fueron aprobados todos los filtros
            $pasoFiltros = true;
            $concursoAplicado = $proyecto->getConcursoAplicado($this->id);

            // Para cada proyecto, evaluamos todos los filtros
            if (count($filtros)) {
                foreach ($filtros as $filtro) {
                    if ($filtro->evaluar($proyecto) == false) {
                        // En caso de NO aprobar un filtro, el proyecto es descartado
                        $pasoFiltros = false;

                        $concursoAplicado->paso_filtros = 0;
                        $concursoAplicado->calificacion = $proyecto->sumRespuestasPonderacion;

                        if ($concursoAplicado->filtros_no_pasados == '') {
                            $concursoAplicado->filtros_no_pasados = $this->crearFiltroNoPasado($filtro->id, $filtro->comentarios);
                        }

                        // Solo basta con que NO pase un filtro para descartar al proyecto
                        break; // No es necesario continuar con los siguientes filtros
                    }
                }
            }

            // En caso de haber aprobado todos los filtros
            if ($pasoFiltros) {
                $concursoAplicado->paso_filtros = 1;
                $concursoAplicado->calificacion = $proyecto->sumRespuestasPonderacion;
                $concursoAplicado->filtros_no_pasados = '';
            }

            $concursoAplicado->save();
        }
    }

    public function crearFiltroNoPasado($id, $descripcion)
    {
        return json_encode( ["id"=> $id, "descripcion"=> $descripcion] , JSON_UNESCAPED_UNICODE);
    }

    /**
     * Asigna los proyectos aceptados a los grupos de evaluadores
     */
    public function asignarEvaluadores()
    {
        $transaction = Yii::$app->db->beginTransaction();
        // Cuando el evaluador aplica al concurso, éste es asignado a un grupo de evaluadores
        // de forma individual, por eso cuando evaluadores_x_proyecto = 1, no es necesario
        // formar los grupos de evaluadores, caso contrario, cuando evaluadores_x_proyecto > 1
        // se forman los grupos de evaluadores
        if ($this->evaluadores_x_proyecto > 1) {
            $this->formarGrupoEvaluadores();
        }

        $gruposEvaluadores = $this->gruposEvaluadores;
        $proyectosAprobados = $this->getProyectosAprobadosFromCuestionarioConcurso();

        // Al inicio, todos los proyectos estan pendientes de asignar
        $noProyectosPendientesAsignar = count($proyectosAprobados);

        // Solo asignamos los proyecto y evaluadores
        // cuando existan proyectos que fueron aprobados y
        // grupos evaluadores asingados al concurso
        // NOTA: Los grupos de evaluadores estan formados por solo un evaluador cuando evaluadores_x_proyecto = 1
        //       Cuando evaluadores_x_proyecto > 1 los grupos de evaluadores se forman en formarGrupoEvaluadores
        if (count($proyectosAprobados)!=0 && count($gruposEvaluadores)!=0) {
            // Cuantos proyectos le corresponde evaluar a cada grupo evaluador
            $noProyectosXGpoEvaluador = round(count($proyectosAprobados)/count($gruposEvaluadores));

            // Recorremos todos los grupos evaluadores
            foreach ($gruposEvaluadores as $gpoEvaluador) {
                // Eliminamos registros anteriores correspondientes al grupo evaluador
                // esto garantiza que el proceso se pueda ejecutar varias veces si afectar la integridad de los datos
                GruposEvXProyectos::deleteAll(['id_grupo_evaluadores' => $gpoEvaluador->id]);

                // Asignamos por cada grupo evaluador, el numero de proyectos correspondientes
                for ($i=0; $i<$noProyectosXGpoEvaluador; $i++) {
                    if ($noProyectosPendientesAsignar > 0) {
                        $proyecto = array_pop($proyectosAprobados);

                        if ($proyecto) {
                            $gpoEvaluadorXProyecto = new GruposEvXProyectos();

                            $gpoEvaluadorXProyecto->id_grupo_evaluadores = $gpoEvaluador->id;
                            $gpoEvaluadorXProyecto->id_proyecto = $proyecto->id;
                            $gpoEvaluadorXProyecto->fecha_alta = date('Y-m-d H:i:s');

                            $gpoEvaluadorXProyecto->save();
                        }

                        $noProyectosPendientesAsignar--;
                    }
                }
            }

            // Devido a que el resultado de la división entre el total de proyectos y el total de grupo evaluadores
            // puede ser un numero fraccionario, al redondearlo, la cantidad de proyectos asignados
            // puede ser menor que el total de proyectos aprobados
            // por eso se realiza esta validación y en caso de existir proyectos pendientes
            // se continua asignando proyectos a los grupos de evaluadors.

            // En caso de existir proyectos pendientes de asignar
            // los vamos asignando uno a uno a los grupos de evaluadores
            while($noProyectosPendientesAsignar > 0) {
                foreach ($gruposEvaluadores as $gpoEvaluador) {
                    $proyecto = array_pop($proyectosAprobados);

                    if ($proyecto) {
                        $gpoEvaluadorXProyecto = new GruposEvXProyectos();

                        $gpoEvaluadorXProyecto->id_grupo_evaluadores = $gpoEvaluador->id;
                        $gpoEvaluadorXProyecto->id_proyecto = $proyecto->id;
                        $gpoEvaluadorXProyecto->fecha_alta = date('Y-m-d H:i:s');

                        $gpoEvaluadorXProyecto->save();
                    }

                    $noProyectosPendientesAsignar--;
                }
            }
        }

        $transaction->commit();
    }


    public function formarGrupoEvaluadores()
    {
        $evaluadores = $this->getEvaluadores();
        $total_evaluadores = count($evaluadores);
        $total_grupos_evaluadores_a_formar = ceil($total_evaluadores/$this->evaluadores_x_proyecto); // Redondea hacia arriba
        $evaluador_actual = 0;

        if ($total_evaluadores == 0) {
            throw new \Exception('No se encontraron evaluadores para este concurso.');

        }

        // Se eliminan todas las asignaciones de grupos de evaluadores del concurso
        Yii::$app->db->createCommand('DELETE FROM grupos_ev_x_evaluadores WHERE id IN (
            SELECT tabla.id FROM (
                SELECT grupos_ev_x_evaluadores.id FROM grupos_ev_x_evaluadores
                INNER JOIN grupos_evaluadores ON
                    grupos_evaluadores.id = grupos_ev_x_evaluadores.id_grupo_evaluadores
                WHERE grupos_evaluadores.id_concurso = '.$this->id.'
            ) AS tabla
            )')->execute();

        Yii::$app->db->createCommand('DELETE FROM grupos_ev_x_proyectos WHERE id IN (
            SELECT tabla.id FROM (
                SELECT grupos_ev_x_proyectos.id FROM grupos_ev_x_proyectos
                INNER JOIN grupos_evaluadores ON
                    grupos_evaluadores.id = grupos_ev_x_proyectos.id_grupo_evaluadores
                WHERE grupos_evaluadores.id_concurso = '.$this->id.'
            ) AS tabla
            )')->execute();

        Yii::$app->db->createCommand('DELETE FROM grupos_evaluadores
            WHERE grupos_evaluadores.id_concurso = '.$this->id)->execute();

        // Formamos el $total_grupos_evaluadores_a_formar
        for ($i=0; $i <= $total_grupos_evaluadores_a_formar; $i++) {
            // Creamos el GrupoEvaluadores
            $grupoEvaluador = new GrupoEvaluadores();

            $grupoEvaluador->id_concurso = $this->id;
            $grupoEvaluador->nombre = 'Grupo Evaluador '.($i+1);
            $grupoEvaluador->fecha_alta = date('Y-m-d H:i:s');

            if (!$grupoEvaluador->save()) {
                throw new \Exception('GrupoEvaluadores - '.Functions::errorsToString($grupoEvaluador->errors));
            }

            // La cantidad de integrantes del grupo de evaluadores
            // es la cantidad de evaluadores que evaluarán el proyecto
            // lo determina la variable evaluadores_x_proyecto
            for ($j=0; $j < $this->evaluadores_x_proyecto; $j++) {
                $gpoEvaluadoresXEvaluador = new GruposEvXEvaluadores();
                $gpoEvaluadoresXEvaluador->id_grupo_evaluadores = $grupoEvaluador->id;
                $gpoEvaluadoresXEvaluador->id_evaluador = $evaluadores[$evaluador_actual]->id;
                $gpoEvaluadoresXEvaluador->fecha_alta = date('Y-m-d H:i:s');

                if (!$gpoEvaluadoresXEvaluador->save()) {
                    throw new \Exception('GruposEvXEvaluadores - '.Functions::errorsToString($gpoEvaluadoresXEvaluador->errors));
                }

                $evaluador_actual++;

                // Si se alcanzó el total de evaluadores
                // iniciamos nuevamente el ciclo con el primer evaluador
                if ($evaluador_actual == $total_evaluadores) {
                    $evaluador_actual = 0;
                }
            }
        }
    }

    public function getCountProyectosAEvaluador()
    {
        return Yii::$app->db->createCommand('
                SELECT
                    COUNT(DISTINCT(proyectos.id)) AS total_proyectos_a_evaluar
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
                    grupos_evaluadores.id_concurso = '.$this->id
            )->queryScalar();
    }

    public function getCountProyectosEvaluados()
    {
        return Yii::$app->db->createCommand('
                SELECT COUNT(DISTINCT(id_proyecto)) AS total_proyectos_evaluados
                FROM evaluaciones
                WHERE id_concurso = '.$this->id
            )->queryScalar();
    }
}
