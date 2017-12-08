<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "log_eventos".
 *
 * @property string $id
 * @property integer $id_usuario
 * @property integer $evento
 * @property string $fecha_hora
 */
class LogEvento extends \yii\db\ActiveRecord
{
    public static $INICIAR_SESSION = 1;
    public static $CERRAR_SESSION = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'log_eventos';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_usuario', 'evento'], 'required'],
            [['id_usuario', 'evento'], 'integer'],
            [['fecha_hora'], 'safe'],
            [['fecha_hora'], 'date', 'format' => 'yyyy-M-d H:m:s']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_usuario' => 'Id Usuario',
            'evento' => 'Evento',
            'fecha_hora' => 'Fecha Hora',
        ];
    }

    public static function register($user, $event)
    {
        $log = new LogEvento();
        $log->id_usuario = $user;
        $log->evento = $event;
        $log->fecha_hora = date('Y-m-d H:i:s');
        $log->save();
    }
}
