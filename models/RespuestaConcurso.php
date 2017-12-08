<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "respuestas".
 *
 * @property integer $id
 * @property integer $id_concurso
 * @property integer $id_proyecto
 * @property integer $id_pregunta
 * @property double $respuesta_numerica
 * @property string $respuesta_texto
 * @property string $respuesta_opcion
 * @property string $respuesta_geografica
 * @property double $ponderacion
 * @property integer $solo_concurso
 *
 * @property Concurso $concurso
 * @property Pregunta $pregunta
 * @property Proyecto $proyecto
 */
class RespuestaConcurso extends \yii\db\ActiveRecord
{
    public $archivo = '';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'respuestas_concurso';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['respuesta_texto', 'respuesta_opcion', 'respuesta_geografica'], 'filter', 'filter' => 'strip_tags'],
            [['respuesta_texto', 'respuesta_opcion', 'respuesta_geografica'], 'app\validators\DelspacesValidator'],
            [['id_proyecto', 'id_pregunta'], 'required'],
            [['id_proyecto', 'id_pregunta', 'id_concurso', 'solo_concurso'], 'integer'],
            [['respuesta_numerica', 'ponderacion'], 'number'],
            [['respuesta_texto', 'respuesta_opcion', 'respuesta_geografica'], 'string'],
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
            'id_proyecto' => 'Proyecto',
            'id_pregunta' => 'Pregunta',
            'respuesta_numerica' => 'Respuesta Númerica',
            'respuesta_texto' => 'Respuesta Texto',
            'respuesta_opcion' => 'Respuesta Opción',
            'respuesta_geografica' => 'Respuesta Geográfica',
            'ponderacion' => 'Ponderación',
            'solo_concurso' => 'Solo concurso',
        ];
    }

    public static function getDirArchivo()
    {
        return __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.
               Yii::$app->params['upload_dir'].DIRECTORY_SEPARATOR.
               'respuestaarchivo'.DIRECTORY_SEPARATOR;
    }

    public function getArchivo()
    {
        $archivos = glob(RespuestaConcurso::getDirArchivo().
            $this->id_concurso.'_'.$this->id_proyecto.DIRECTORY_SEPARATOR.
            $this->id.'.*');

        if (count($archivos)) {
            return $archivos[0];
        }

        return null;
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
    public function getPregunta()
    {
        return $this->hasOne(Pregunta::className(), ['id' => 'id_pregunta']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProyecto()
    {
        return $this->hasOne(Proyecto::className(), ['id' => 'id_proyecto']);
    }

    /**
     *
     */
    public static function getByProyecto($id_proyecto)
    {
        return Respuesta::find()
            ->where(['id_proyecto' => $id_proyecto])
            ->all();
    }

    /**
     *
     */
    public static function copyRespuestas($concurso, $proyecto)
    {
        $sql = 'INSERT INTO respuestas_concurso
            (id_concurso,
             id_proyecto,
             id_pregunta,
             respuesta_numerica,
             respuesta_texto,
             respuesta_opcion,
             respuesta_geografica,
             ponderacion)
                SELECT
                    "'.(int)$concurso.'" AS id_concurso,
                    id_proyecto,
                    id_pregunta,
                    respuesta_numerica,
                    respuesta_texto,
                    respuesta_opcion,
                    respuesta_geografica,
                    ponderacion
                FROM respuestas
                WHERE id_proyecto = '.(int)$proyecto;

        $query = Yii::$app->db->createCommand($sql)->execute();
    }

    public function getNombreArchivo()
    {
        if ($this->getArchivo() != null) {
            $nombre = 'Archivo';
            $ruta = explode(DIRECTORY_SEPARATOR, $this->getArchivo());
            $archivo = $ruta[count($ruta)-1]; // Se obtiene el nombre del archivo
            $archivo = explode('.', $archivo); // Se obtiene la extensión del archivo

            return $nombre.'.'.$archivo[1];
        } else {
            return 'Archivo no encontrado';
        }
    }

    public function downloadArchivoRespuesta()
    {
        $ruta = $this->getArchivo();

        if ($ruta != null) {
            // http://johnculviner.com/jquery-file-download-plugin-for-ajax-like-feature-rich-file-downloads/
            // La Cookie fileDownload es necesario para el Plugin jQuery File Download
            header('Set-Cookie: fileDownload=true; path=/');
            header('Cache-Control: max-age=60, must-revalidate');
        }

        return $ruta;
    }

}
