<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "instituciones".
 *
 * @property integer $id
 * @property string $nombre
 * @property integer $id_estado
 * @property integer $id_ciudad
 * @property string $direccion
 * @property string $telefono
 * @property double $lat
 * @property double $lon
 * @property string $horario
 * @property string $pagina_web
 * @property string $facebook
 * @property string $twitter
 * @property string $descipcion
 *
 * @property Concursos[] $concursos
 * @property Ciudades $idCiudad
 * @property Estados $idEstado
 */
class Institucion extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'instituciones';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nombre', 'direccion', 'telefono', 'horario', 'pagina_web', 'facebook', 'twitter', 'descipcion'], 'filter', 'filter' => 'strip_tags'],
            [['nombre'], 'required'],
            [['nombre', 'direccion', 'telefono', 'horario', 'pagina_web', 'facebook', 'twitter', 'descipcion'], 'app\validators\DelspacesValidator'],
            [['id_estado', 'id_ciudad'], 'integer'],
            [['lat', 'lon'], 'number'],
            [['nombre'], 'string', 'max' => 50],
            [['direccion', 'horario', 'facebook', 'twitter'], 'string', 'max' => 45],
            [['telefono'], 'string', 'max' => 20],
            [['pagina_web'], 'string', 'max' => 100],
            [['descipcion'], 'string', 'max' => 600]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nombre' => 'Nombre',
            'id_estado' => 'Estado',
            'id_ciudad' => 'Ciudad',
            'direccion' => 'Dirección',
            'telefono' => 'Teléfono',
            'lat' => 'Latitutd',
            'lon' => 'Longitud',
            'horario' => 'Horario',
            'pagina_web' => 'Página Web',
            'facebook' => 'Facebook',
            'twitter' => 'Twitter',
            'descipcion' => 'Descipción',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConcursos()
    {
        return $this->hasMany(Concurso::className(), ['id_institucion' => 'id']);
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
    public function getEvaluadores()
    {
        return $this->hasMany(Evaluador::className(), ['id_usuario' => 'id_evaluador'])
            ->viaTable('evaluadores_x_instituciones', ['id_institucion' => 'id'])
            ->where('activo = 1');
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
    public function findEvaluadorByName($nombre_completo)
    {
        return Usuario::findBySql('SELECT *
                FROM
                    usuarios
                INNER JOIN evaluadores_x_instituciones ON
                    evaluadores_x_instituciones.id_evaluador = usuarios.id AND
                    evaluadores_x_instituciones.id_institucion = '.$this->id.'
                WHERE
                    activo = 1 AND
                    (CONCAT(nombre, " ", appat, " ", apmat) LIKE "%'.$nombre_completo.'%")')
            ->all();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function findEvaluadores($page, $no_items = 5)
    {
        return Usuario::findBySql('SELECT *
                FROM
                    usuarios
                INNER JOIN evaluadores_x_instituciones ON
                    evaluadores_x_instituciones.id_evaluador = usuarios.id AND
                    evaluadores_x_instituciones.id_institucion = '.$this->id.'
                WHERE
                    activo = 1
                LIMIT '.$no_items.' OFFSET '.($page*$no_items))
            ->all();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function estadisticasEvaluador($evaluador)
    {
        $estadisticas = Yii::$app->db->createCommand('
            SELECT
                COUNT(id_proyecto) AS proyectos_calificados,
                ROUND(AVG(calificacion)) AS promedio_calificaciones
            FROM
                (SELECT
                    evaluaciones.id_proyecto,
                    SUM(evaluaciones.calificacion) AS calificacion
                FROM
                    evaluaciones
                    INNER JOIN concursos
                        ON concursos.id = evaluaciones.id_concurso
                        AND concursos.id_institucion = '.$this->id.'
                WHERE
                    evaluaciones.id_evaluador = '.$evaluador.'
                GROUP BY evaluaciones.id_proyecto) AS calificaciones_evaluador ')->queryAll();

        if (count($estadisticas)) {
            $estadisticas = $estadisticas[0];
        }

        $estadisticas['concursos_activos'] = Evaluador::getConcursosActivosInstitucion($evaluador, $this->id);
        $estadisticas['concursos_pasados'] = Evaluador::getConcursosPasadosInstitucion($evaluador, $this->id);

        return $estadisticas;
    }
}
