<?php
namespace app\validators;

use yii\validators\Validator;

class DelspacesValidator extends Validator
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        Validator::$builtInValidators['delspaces'] = 'app\validators\DelspacesValidator';
    }

    /**
     * Elimina los espacios excedentes en toda la cadena
     *
     * @param mixed $model
     * @param string $attribute
     */
    public function validateAttribute($model, $attribute)
    {
        // Elimina espacios al inicio y final de la cadena
        $model->$attribute = trim($model->$attribute);
        // Elimina dobles espacios o más dentro de la cadena
        $model->$attribute = preg_replace("'\s+'", ' ', $model->$attribute);
    }
}