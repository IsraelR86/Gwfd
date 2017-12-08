<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "badges_x_usuario".
 *
 * @property integer $id
 * @property integer $id_usuario
 * @property integer $id_badge
 */
class BadgeXUsuario extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'badges_x_usuario';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_usuario', 'id_badge'], 'required'],
            [['id_usuario', 'id_badge'], 'integer']
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
            'id_badge' => 'Id Badge',
        ];
    }
    
    public function getBadge()
    {
        return $this->hasOne(Badge::className(), ['id' => 'id_badge']);
    }
    
    public function getUsuario()
    {
        return $this->hasOne(Usuario::className(), ['id' => 'id_usuario']);
    }
}
