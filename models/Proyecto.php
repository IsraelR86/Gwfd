<?php

namespace app\models;

use Yii;
use yii\db\Query;

/**
 * This is the model class for table "proyectos".
 *
 * @property integer $id
 * @property integer $id_emprendedor_creador
 * @property string $nombre
 * @property string $descripcion
 * @property string $url_video
 * @property string $imagen
 * @property string $logo
 * @property string $integrantes
 *
 * @property Concurso[] $concursosAplicados
 * @property EmprendedorXProyecto[] $emprendedoresXProyecto
 * @property Emprendedor[] $emprendedores
 * @property Evaluaciones[] $evaluaciones
 * @property Ganadores[] $ganadores
 * @property GruposEvXProyectos[] $gruposEvXProyectos
 * @property Emprendedor $emprendedorCreador
 * @property Respuestas[] $respuestas
 */
class Proyecto extends \yii\db\ActiveRecord
{
    /**
     * Almacena la ponderación total obtenida a partir de
     * las respuestas a las preguntas del cuestionario
     */
    public $sumRespuestasPonderacion;

    /**
     * Almacena la puntuación total obtenida a partir de
     * las evaluaciones de los evaluadores del concurso
     */
    public $sumTotalEvaluaciones;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'proyectos';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nombre', 'descripcion', 'url_video', 'imagen'], 'filter', 'filter' => 'strip_tags'],
            [['nombre', 'descripcion', 'url_video', 'imagen'], 'app\validators\DelspacesValidator'],
            [['nombre', 'descripcion', 'id_emprendedor_creador'], 'required'],
            [['id_emprendedor_creador', 'integrantes'], 'integer'],
            [['nombre'], 'string', 'max' => 250],
            [['descripcion'], 'string', 'max' => 300],
            [['etiquetas_array'], 'string'],
            [['url_video', 'imagen', 'logo'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_emprendedor_creador' => 'Emprendedor Creador',
            'nombre' => 'Nombre',
            'descripcion' => 'Descripción',
            'url_video' => 'URL Video',
            'imagen' => 'Imágen',
            'logo' => 'Logo',
            'integrantes' => 'Integrantes',
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
        $extraFields['byteimagen'] = 'byteimagen';
        $extraFields['bytelogo'] = 'bytelogo';
        $extraFields['emprendedoresXProyecto'] = 'emprendedoresXProyecto';
        $extraFields['etiquetas'] = 'etiquetas';
        $extraFields['noParticipacion'] = 'noParticipacion';
        $extraFields['porcentajeCompletado'] = 'porcentajeCompletado';
        $extraFields['nombreEmprendedorCreador'] = 'nombreEmprendedorCreador';

        return $extraFields;
    }

    public static function getUploadDir()
    {
        return __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.
               Yii::$app->params['upload_dir'].DIRECTORY_SEPARATOR.
               'proyecto'.DIRECTORY_SEPARATOR;
    }

    public function getPathImagen()
    {
        return self::getUploadDir().$this->id.'_imagen.jpg';
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

    public function getPathLogo()
    {
        return self::getUploadDir().$this->id.'_logo.jpg';
    }

    public function getBytelogo()
    {
        $pathLogo = $this->pathLogo;

        if (!file_exists($pathLogo)) {
            return 'http://placehold.it/100x100';
        }

        $type = pathinfo($pathLogo, PATHINFO_EXTENSION);
        $logoByte = file_get_contents($pathLogo);
        $base64Logo = 'data:image/' . $type . ';base64,' . base64_encode($logoByte);

        return $base64Logo;
    }

    public function getNoParticipacion()
    {
        $query = 'SELECT COUNT(id_concurso) AS no_participacion FROM concursos_aplicados WHERE id_proyecto = '.$this->id;

        $result = Yii::$app->db->createCommand($query)->queryScalar();

        return $result;
    }

    public function getPorcentajeCompletado()
    {
        $query = 'SELECT
                seccion.id AS seccion_id,
                COUNT(preguntas.id) AS no_preguntas,
                COUNT(respuestas.id_pregunta) AS no_respuestas
            FROM
                seccion
            INNER JOIN preguntas ON
                preguntas.id_seccion = seccion.id
            LEFT JOIN respuestas ON
                respuestas.id_pregunta = preguntas.id AND
                respuestas.id_proyecto = '.$this->id.'
            GROUP BY seccion.id';
        $completados = 0;
        $noCompletados = 0;

        // Primero verificamos si la sección general esta completa
        // Los datos obligatorios son:
        // nombre, descripcion, url_video, imagen, logo
        if (empty($this->nombre) || empty($this->descripcion) || empty($this->url_video) || !file_exists($this->pathImagen) || !file_exists($this->pathLogo)) {
            $noCompletados++;
        } else {
            $completados++;
        }

        $result = Yii::$app->db->createCommand($query)->queryAll();

        // Ahora revisamos las secciones para determinar si todas sus preguntas estan respondidadas
        foreach ($result as $seccion) {
            if ($seccion['no_preguntas'] == $seccion['no_respuestas']) {
                $completados++;
            } else {
                $noCompletados++;
            }
        }

        return round(($completados / ($completados+$noCompletados))*100);
    }

    public function isCompletado()
    {
        $query = 'SELECT
                seccion.id AS seccion_id,
                COUNT(preguntas.id) AS no_preguntas,
                COUNT(respuestas.id_pregunta) AS no_respuestas
            FROM
                seccion
            INNER JOIN preguntas ON
                preguntas.id_seccion = seccion.id
            LEFT JOIN respuestas ON
                respuestas.id_pregunta = preguntas.id AND
                respuestas.id_proyecto = '.$this->id.'
            GROUP BY seccion.id';

        // Primero verificamos si la sección general esta completa
        // Los datos obligatorios son:
        // nombre, descripcion, url_video, imagen, logo
        
        if (empty($this->nombre) || empty($this->descripcion) || empty($this->url_video) || !file_exists($this->pathImagen) || !file_exists($this->pathLogo)) {
            return false;
        }

        $result = Yii::$app->db->createCommand($query)->queryAll();

        // Ahora revisamos las secciones para determinar si todas sus preguntas estan respondidadas
        foreach ($result as $seccion) {
            if ($seccion['no_preguntas'] == $seccion['no_respuestas']) {
                return false;
            }
        }

        return $result;
    }
    public function isSemicompletado()
    {
        $query = 'SELECT
                seccion.id AS seccion_id,
                COUNT(preguntas.id) AS no_preguntas,
                COUNT(respuestas.id_pregunta) AS no_respuestas
            FROM
                seccion
            INNER JOIN preguntas ON
                preguntas.id_seccion = seccion.id
            LEFT JOIN respuestas ON
                respuestas.id_pregunta = preguntas.id AND
                respuestas.id_proyecto = '.$this->id.'
            GROUP BY seccion.id';

        // Primero verificamos si la sección general esta completa
        // Los datos obligatorios son:
        // nombre, descripcion, url_video, imagen, logo
        
        if (empty($this->nombre) || empty($this->descripcion)) {
            return false;
        }

        $result = Yii::$app->db->createCommand($query)->queryAll();

        // Ahora revisamos las secciones para determinar si todas sus preguntas estan respondidadas
        foreach ($result as $seccion) {
            if ($seccion['no_preguntas'] != $seccion['no_respuestas']) {
                return false;
            }
        }

        return $result;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConcursosAplicados()
    {
        //return $this->hasMany(Concurso::className(), ['id' => 'id_concurso'])->viaTable('concursos_aplicados', ['id_proyecto' => 'id']);
        return $this->hasMany(ConcursoAplicado::className(), ['id_proyecto' => 'id']);
    }

    /**
     * @return app\models\ConcursoAplicado
     */
    public function getConcursoAplicado($id_concurso)
    {
        return ConcursoAplicado::find()
            ->where('id_proyecto = :id_proyecto', [':id_proyecto' => $this->id])
            ->andWhere('id_concurso = :id_concurso', [':id_concurso' => $id_concurso])
            ->one();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmprendedoresXProyecto()
    {
        return $this->hasMany(EmprendedorXProyecto::className(), ['id_proyecto' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmprendedores()
    {
        return $this->hasMany(Emprendedor::className(), ['id_usuario' => 'id_emprendedor'])
            ->viaTable('emprendedores_x_proyectos', ['id_proyecto' => 'id']);
        //->where('tipo_usuario = 6')
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvaluaciones()
    {
        return $this->hasMany(Evaluaciones::className(), ['id_proyecto' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGanadores()
    {
        return $this->hasMany(Ganadores::className(), ['id_proyecto' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGruposEvXProyectos()
    {
        return $this->hasMany(GruposEvXProyectos::className(), ['id_proyecto' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmprendedorCreador()
    {
        return $this->hasOne(Emprendedor::className(), ['id_usuario' => 'id_emprendedor_creador']);
    }

    public function getNombreEmprendedorCreador()
    {
        $emprendedorCreador = $this->emprendedorCreador;

        if ($emprendedorCreador) {
            return $emprendedorCreador->usuario->nombre_completo;
        }

        return '';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRespuestas()
    {
        return $this->hasMany(Respuesta::className(), ['id_proyecto' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEtiquetas()
    {
        return $this->hasMany(Etiqueta::className(), ['id' => 'id_etiqueta'])
            ->viaTable('etiquetas_x_proyectos', ['id_proyecto' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    /*public function getPreguntas()
    {
        return $this->hasMany(Pregunta::className(), ['id' => 'id_pregunta'])
            ->viaTable('respuestas', ['id_proyecto' => 'id']);
    }*/

    /**
     * Valida que el integrante con el email proporcionado
     * forma parte del equipo del proyecto
     */
    public function validIntegrante($emailIntegrante)
    {
        $query = 'SELECT
            COUNT(usuarios.id) AS integranteValido
        FROM
            usuarios
        INNER JOIN emprendedores_x_proyectos ON
            usuarios.id = emprendedores_x_proyectos.id_emprendedor
        WHERE
            usuarios.tipo = 6 AND
            emprendedores_x_proyectos.id_proyecto = '.$this->id.' AND
            usuarios.email = "'.$emailIntegrante.'"';

        $result = Yii::$app->db->createCommand($query)->queryScalar();

        if ($result == 1) {
            return true;
        }

        return false;
    }

    /**
     * Obtiene el registro en la tabla "checklist_documentos" relacionado con el proyecto y
     * el id_concurso proporcionado
     *
     * @param integer id_concurso
     * @return app\models\ChecklistDocumentos
     */
    public function getChecklistDocumentos($id_concurso)
    {
        return ChecklistDocumentos::getChecklistDocumentos($this->id, $id_concurso);
    }

    /**
     * Obtiene todos los proyectos que no esten registrados en un grupo y que pertenescan a un concurso especificado
     *
     * @return array app\models\Proyecto
     */
    public static function getProyectosSinGrupo($id_concurso)
    {
        $proyectosEnGrupoQuery = (new Query())->select('id_proyecto')
            ->from('grupos_ev_x_proyectos')
            ->innerJoin('grupos_evaluadores', 'grupos_evaluadores.id = grupos_ev_x_proyectos.id_grupo_evaluadores AND '.
                                              'grupos_evaluadores.id_concurso = '.$id_concurso);
        //'SELECT id_proyecto FROM grupos_ev_x_proyectos'
        return Proyecto::find()
            ->innerJoin('concursos_aplicados', 'concursos_aplicados.id_proyecto = proyectos.id AND '.
                                               'concursos_aplicados.id_concurso = '.(int)$id_concurso)
            ->where(['not in', 'proyectos.id', $proyectosEnGrupoQuery])
            ->andWhere('concursos_aplicados.paso_filtros = 1') // Solo se toman los proyectos aprobados
            ->all();
    }

    /**
     * Devuelve la calificación total asignada al proyecto para un determinado concurso y evaluador
     */
    public function getEvaluacionTotal($id_concurso, $id_evaluador)
    {
        $query = 'SELECT
            SUM(calificacion) AS calificacion_total
        FROM
            evaluaciones
        WHERE
            id_proyecto = :id_proyecto AND
            id_evaluador = :id_evaluador AND
            id_concurso = :id_concurso';

        $result = Yii::$app->db->createCommand($query)
            ->bindValue(':id_proyecto', $this->id)
            ->bindValue(':id_evaluador', $id_evaluador)
            ->bindValue(':id_concurso', $id_concurso)
            ->queryScalar();

        return $result;
    }

    /**
     * Valida que el Evaluador tiene permitido evaluar al Proyecto
     * es decir, que el Evaluador pertenezca al mismo grupo de evaluadores al cual pertenece el Proyecto
     */
    public function validEvaluador($id_evaluador)
    {
        $query = 'SELECT
            COUNT(*) AS total
        FROM
            grupos_ev_x_proyectos
        INNER JOIN grupos_ev_x_evaluadores ON
            grupos_ev_x_proyectos.id_grupo_evaluadores = grupos_ev_x_evaluadores.id_grupo_evaluadores
        WHERE
            grupos_ev_x_proyectos.id_proyecto = '.$this->id.' AND
            grupos_ev_x_evaluadores.id_evaluador = '.$id_evaluador;

        $result = Yii::$app->db->createCommand($query)->queryScalar();

        if ($result == 0) {
            return false;
        }

        return true;
    }

    public function getComentarioEvaluador($id_concurso, $id_evaluador)
    {
        return ComentarioEvaluador::find()
            ->where('id_proyecto = '.$this->id)
            ->andwhere('id_concurso = :id_concurso', [':id_concurso' => $id_concurso])
            ->andwhere('id_evaluador = :id_evaluador', [':id_evaluador' => $id_evaluador])
            ->one();
    }

    /**
     * Obtiene la ponderación total obtenida con las respuestas de
     * las preguntas en el cuestonario
     *
     * @return int
     */
    public function getPonderacionTotalRespuestas()
    {
        $query = 'SELECT SUM(ponderacion) AS ponderacion_total
            FROM respuestas
            WHERE id_proyecto = '.$this->id;

        $result = Yii::$app->db->createCommand($query)->queryScalar();

        return $result;
    }

    /**
     * Obtiene la ponderación total obtenida por cada categoría de preguntas del cuestonario
     *
     * @return array
     */
    public function getPonderacionRespuestasByCategoria()
    {
        $query = 'SELECT
                seccion.id,
                SUM(respuestas.ponderacion) AS ponderacion_total
            FROM seccion
            LEFT JOIN preguntas ON
                seccion.id = preguntas.id_seccion
            LEFT JOIN respuestas ON
                preguntas.id = respuestas.id_pregunta AND
                respuestas.id_proyecto = '.$this->id.'
            GROUP BY seccion.id
            ORDER BY seccion.id ASC';

        $result = Yii::$app->db->createCommand($query)->queryAll();

        return $result;
    }

    public function getPlanNegocios()
    {
        // Se agrega guión bajo (_) al inicio del documento para evitar la colisión por nombre
        $files = glob(Yii::$app->basePath.DIRECTORY_SEPARATOR.
            'uploads'.DIRECTORY_SEPARATOR.
            'plan_negocio'.DIRECTORY_SEPARATOR.
            '_'.$this->id . '.*');

        if (count($files)) {
            return $files[0];
        }

        return null;
    }

    public function downloadPlanNegocios()
    {
        $ruta = $this->planNegocios;

        // http://johnculviner.com/jquery-file-download-plugin-for-ajax-like-feature-rich-file-downloads/
        // La Cookie fileDownload es necesario para el Plugin jQuery File Download
        header('Set-Cookie: fileDownload=true; path=/');
        header('Cache-Control: max-age=60, must-revalidate');
        //header("Content-type: text/csv");
        //header('Content-Disposition: attachment; filename="Plan de Negocios"');

        return $ruta;
    }

    public function getNombrePlanNegocios()
    {
        if ($this->planNegocios != null) {
            $nombre = 'Plan de Negocios - '.substr($this->nombre, 0, 25);
            $archivo = explode('.', $this->planNegocios);

            return $nombre.'.'.$archivo[1];
        } else {
            return 'Archivo no encontrado';
        }
    }

    public function deletePlanNegocios()
    {
        $ruta = $this->planNegocios;

        if ($ruta != null) {
            unlink($ruta);
        }
    }

    /**
     * Obtiene el listado de todos los proyectos de un determinado emprendedor
     *
     * @param int $emprendedor ID del emprendedor
     * @param int $page Página a mostrar
     * @param int $no_items Número de elementos a mostrar, default 5
     *
     * @return Proyecto[]
     */
    public static function getAllByEmprendedor($emprendedor, $page, $no_items = 5)
    {
        $query = Proyecto::find()
            ->where(['id_emprendedor_creador' => $emprendedor]);

        if (!empty($page)) {
            $query->offset($page*$no_items);
        }

        if (!empty($no_items)) {
            $query->limit($no_items);
        }

        return $query->orderby('id DESC')->all();
    }

    /**
     * Obtiene el listado de todos los proyectos del usuario, ya sea como creador o integrante
     *
     * @param int $usuario ID del usuario
     * @param int $page Página a mostrar
     * @param int $no_items Número de elementos a mostrar, default 5
     *
     * @return Proyecto[]
     */
    public static function getAllByCreadorOrIntegrante($usuario, $page, $no_items = 5)
    {
        return Proyecto::find()
            ->where(['id_emprendedor_creador' => $usuario])
            ->orWhere('id IN (
                SELECT
                    id_proyecto
                FROM emprendedores_x_proyectos
                WHERE id_emprendedor = '.$usuario.
            ')')
            ->limit($no_items)
            ->offset($page*$no_items)
            ->orderby('id DESC')
            ->all();
    }

    /**
     * Verifica si el usuario es creador o integrante del proyecto
     *
     * @param  int  $usuario ID del usuario
     * @return boolean
     */
    public function isCreadorOrIntegrante($usuario)
    {
        $result = Yii::$app->db->createCommand('SELECT COUNT(*) AS no_proyectos FROM proyectos
            WHERE (id_emprendedor_creador = '.$usuario.' OR
                id IN (SELECT id_proyecto
                FROM emprendedores_x_proyectos
                WHERE id_emprendedor = 2)
            ) AND id = '.$this->id)->queryScalar();

        return $result == 1 ? true : false;
    }

    /**
     * Obtiene el listado de todos los proyectos de un determinado emprendedor
     *
     * @param int $emprendedor ID del emprendedor
     * @param int $proyecto ID del proyecto
     *
     * @return Proyecto
     */
    public static function getByEmprendedor($emprendedor, $proyecto)
    {
        return Proyecto::find()
            ->where(['id_emprendedor_creador' => $emprendedor])
            ->andWhere(['id' => $proyecto])
            ->one();
    }

    /**
     *
     */
    public function getPreguntasRespondidas($seccion = null)
    {
        return Pregunta::find()
            ->innerJoin('respuestas', 'respuestas.id_pregunta = preguntas.id AND respuestas.id_proyecto = '.$this->id)
            ->andFilterWhere(['id_seccion' => $seccion])
            ->all();
    }

    /**
     *
     */
    public function getRespuestasBySeccion($seccion)
    {
        return Respuesta::find()
            ->innerJoin('preguntas', 'preguntas.id = respuestas.id_pregunta AND preguntas.id_seccion = '.$seccion)
            ->where(['id_proyecto' => $this->id])
            ->all();
    }
}
