<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "emprendedores".
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
 * @property EmprendedoresXProyectos[] $emprendedoresXProyectos
 * @property Proyectos[] $idProyectos
 * @property Proyecto[] $proyectos
 * @property Universidad[] $universidad
 */
class Emprendedor extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'emprendedores';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['universidad_otro', 'profesion', 'curp', 'rfc', 'tel_celular', 'tel_fijo', 'cp', 'direccion', 'colonia', 'facebook', 'twitter', 'pagina_web'], 'filter', 'filter' => 'strip_tags'],
            [['id_usuario'], 'required', 'except' => 'signup'],
            [['id_usuario', 'genero', 'id_nivel_educativo', 'id_estado', 'id_ciudad', 'estado_civil', 'id_estado_nacimiento', 'id_ciudad_nacimiento', 'id_universidad'], 'integer'],
            [['fecha_nacimiento'], 'safe'],
            //[['fecha_nacimiento'], 'date', 'format' => 'd-M-yyyy'],
            //['fecha_nacimiento', 'match', 'pattern' => '/^(0?[1-9]|[12][0-9]|3[01])[\/\-](0?[1-9]|1[012])[\/\-]\d{4}$/'],
            [['universidad_otro', 'profesion', 'curp', 'rfc', 'tel_celular', 'tel_fijo', 'cp', 'direccion', 'colonia', 'facebook', 'twitter', 'pagina_web'], 'app\validators\DelspacesValidator'],
            [['universidad_otro', 'profesion', 'colonia'], 'string', 'max' => 50],
            [['curp'], 'string', 'length' => 18],
            [['rfc'], 'string', 'length' => 13],
            [['curp', 'rfc'], 'filter', 'filter' => 'strtoupper'],
            [['tel_celular', 'tel_fijo'], 'string', 'max' => 10],
            [['cp'], 'string', 'length' => 5],
            [['direccion', 'facebook', 'twitter'], 'string', 'max' => 45],
            [['pagina_web'], 'string', 'max' => 100],
            [['curp', 'rfc'], 'filter', 'filter' => 'strtoupper'],
            [['tel_celular', 'tel_fijo'], 'match', 'pattern' => '/^\d{10}$/', 'message' => 'El {attribute} debería contener 10 dígitos'],
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
    public function getEmprendedoresXProyectos()
    {
        return $this->hasMany(EmprendedorXProyecto::className(), ['id_emprendedor' => 'id_usuario']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdProyectos()
    {
        return $this->hasMany(Proyectos::className(), ['id' => 'id_proyecto'])->viaTable('emprendedores_x_proyectos', ['id_emprendedor' => 'id_usuario']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProyectos()
    {
        return $this->hasMany(Proyecto::className(), ['id_emprendedor_creador' => 'id_usuario']);
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

    public function getPerfil()
    {
    }

    /**
     * Obtiene el proyecto que aplicó al concurso especificado
     * del cual es creador el emprendedor
     *
     * @return app\models\Proyecto
     */
    public function getProyectoFromConcurso($id_concurso)
    {
        return Proyecto::find()
            ->innerJoin('concursos_aplicados', 'concursos_aplicados.id_proyecto = proyectos.id')
            ->where('concursos_aplicados.id_concurso = '.$id_concurso)
            ->andWhere('id_emprendedor_creador = '.$this->id_usuario)
            ->one();
    }

    /**
     * Obtiene el proyecto que aplicó al concurso especificado
     * del cual es creador el emprendedor
     *
     * @return array app\models\Usuario
     */
    public static function findByName($nombre_completo, $omit_login = false)
    {
        return Usuario::findBySql('SELECT *
                FROM
                    usuarios
                WHERE
                    tipo = '.Usuario::$EMPRENDEDOR.' AND
                    activo = 1 AND (
                    '.($omit_login ? 'id != '.Yii::$app->user->id.' AND ' : '').'
                    (CONCAT(nombre, " ", appat, " ", apmat) LIKE "%'.$nombre_completo.'%") OR
                    email="'.$nombre_completo.'") ')
            ->all();
    }
}
