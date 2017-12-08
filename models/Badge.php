<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "badges".
 *
 * @property integer $id
 * @property integer $tipo
 * @property string $descripcion
 * @property integer $activo
 * @property string $nota
 */
class Badge extends \yii\db\ActiveRecord
{
    public static $SOY_YO = 1; // Haz completado todos tus campos en tu perfil
    public static $LISTO_APLICAR = 2; // Haz completado todas las preguntas de un proyecto
    public static $CAMARA_ACCION = 3; // ¡Tu proyecto tiene vídeo!
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'badges';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['descripcion', 'nota'], 'filter', 'filter' => 'strip_tags'],
            [['tipo', 'activo'], 'integer'],
            [['descripcion'], 'required'],
            [['descripcion', 'nota'], 'app\validators\DelspacesValidator'],
            [['descripcion'], 'string', 'max' => 50],
            [['nota'], 'string', 'max' => 150]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tipo' => 'Tipo',
            'descripcion' => 'Descripcion',
            'activo' => 'Activo',
            'nota' => 'Nota',
        ];
    }

    public static function checkBadgeSoyYo($usuario)
    {
        return (
            !empty($usuario->email) &&
            !empty($usuario->nombre) &&
            !empty($usuario->appat) &&
            !empty($usuario->apmat) &&
            !empty($usuario->emprendedor->fecha_nacimiento) &&
            !empty($usuario->emprendedor->id_estado) &&
            !empty($usuario->emprendedor->id_ciudad) &&
            !empty($usuario->emprendedor->genero) &&
            !empty($usuario->emprendedor->estado_civil) &&
            !empty($usuario->emprendedor->id_nivel_educativo) &&
            !empty($usuario->emprendedor->id_universidad) &&
            (!empty($usuario->emprendedor->tel_celular) || !empty($usuario->emprendedor->tel_fijo))
        );
    }

    public static function checkBadgeListoAplicar($id_proyecto)
    {
        $total_preguntas = Yii::$app->db
            ->createCommand('SELECT COUNT(*) AS total_preguntas FROM preguntas')->queryScalar();

        $total_respuestas = Yii::$app->db
            ->createCommand('SELECT COUNT(*) AS total_respuestas FROM respuestas WHERE id_proyecto='.$id_proyecto)->queryScalar();

        return ($total_respuestas >= $total_preguntas);
    }
}
