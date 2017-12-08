<?php

namespace app\models;

use Yii;
use app\helpers\Functions;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "noticias".
 *
 * @property string $id
 * @property string $titulo
 * @property string $fecha
 * @property string $autor
 * @property string $resumen
 * @property string $contenido
 * @property integer $activo
 *
 * @property EtiquetasXNoticia[] $etiquetasXNoticia
 * @property Etiquetas[] $etiquetas
 */
class Noticia extends \yii\db\ActiveRecord
{
    public $portada = null;
    public $str_etiquetas = null;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'noticias';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['titulo', 'autor', 'resumen'], 'filter', 'filter' => 'strip_tags'],
            [['titulo', 'autor', 'resumen', 'contenido'], 'app\validators\DelspacesValidator'],
            [['fecha'], 'safe'],
            [['fecha'], 'date', 'format' => 'yyyy-M-d H:m:s'],
            [['contenido'], 'string'],
            [['activo'], 'integer'],
            [['titulo', 'resumen'], 'string', 'max' => 100],
            [['autor'], 'string', 'max' => 45],
            ['portada', 'file', 'extensions' => ['png', 'jpg', 'gif', 'jpeg'], 'maxSize' => 1024*1024*2],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'titulo' => 'Título',
            'fecha' => 'Fecha',
            'autor' => 'Autor',
            'resumen' => 'Resumen',
            'contenido' => 'Contenido',
            'activo' => 'Activo',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEtiquetasXNoticia()
    {
        return $this->hasMany(EtiquetaXNoticia::className(), ['id_noticia' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEtiquetas()
    {
        return $this->hasMany(Etiqueta::className(), ['id' => 'id_etiqueta'])->viaTable('etiquetas_x_noticias', ['id_noticia' => 'id']);
    }

    public function getStr_etiquetas()
    {
        $etiquetas = ArrayHelper::map($this->etiquetas, 'id', 'descripcion');

        return implode(', ', $etiquetas);
    }

    public static function getUploadDir()
    {
        return __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.
               Yii::$app->params['upload_dir'].DIRECTORY_SEPARATOR.
               'noticia'.DIRECTORY_SEPARATOR;
    }

    public function getPathPortada()
    {
        return self::getUploadDir().$this->id.'.jpg';
    }

    public function getBytePortada()
    {
        $pathPortada = $this->pathPortada;

        if (!file_exists($pathPortada)) {
            return 'http://placehold.it/350x150';
        }

        $type = pathinfo($pathPortada, PATHINFO_EXTENSION);
        $imagenByte = file_get_contents($pathPortada);
        $base64Imagen = 'data:image/' . $type . ';base64,' . base64_encode($imagenByte);

        return $base64Imagen;
    }

    public function subirPortada()
    {
        $pathPortada = $this->getPathPortada();

        if ($this->validate()) {
            $this->portada->saveAs($pathPortada);
        } else {
            throw new \Exception(Functions::errorsToString($this->errors));
        }
    }

    public function delete()
    {
        $pathPortada = $this->getPathPortada();

        if (file_exists($pathPortada)) {
            unlink($pathPortada);
        }

        return parent::delete();
    }

    public function beforeSave($query)
    {
        if (parent::beforeSave($query)) {
            if ($this->isNewRecord) {
                $this->autor = substr(Yii::$app->user->identity->nombre_completo, 0, 45);
                $this->fecha = date('Y-m-d H:i:s');
                $this->activo = 1;
            }
        }

        return true;
    }
}
