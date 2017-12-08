<?php

namespace app\api\modules\v1\controllers;

use Yii;
use yii\rest\ActiveController;

class BaseController extends ActiveController
{
    public $modelClass = '';

    /**
     * Initializes the object.
     * This method is invoked at the end of the constructor after the object is initialized with the
     * given configuration.
     */
    public function init()
    {
        // Se mapea la clase a la dirección del archivo
        // debido a que el psr0 no carga automaticamente el archivo
        // porque la ruta no coincide con el namespace
        // Transforma 'app\models\Modelo' a '/../models/Modelo.php'
        Yii::$classMap[$this->modelClass] = Yii::getAlias('@app') .
            str_replace(['app\\', '\\'], ['/../','/'], $this->modelClass).'.php';

        Yii::$classMap['app\models\BaseModel'] = Yii::getAlias('@app') . '/../models/BaseModel.php';
        Yii::$classMap['app\helpers\MyConnection'] = Yii::getAlias('@app') . '/../helpers/MyConnection.php';
        Yii::$classMap['app\helpers\Functions'] = Yii::getAlias('@app') . '/../helpers/Functions.php';
        Yii::$classMap['app\validators\DelspacesValidator'] = Yii::getAlias('@app') . '/../validators/DelspacesValidator.php';

        return parent::init();
    }

    /**
     * It should return an array, with array keys being action IDs, and array values the corresponding
     * action class names or action configuration arrays.
     */
    public function actions()
    {
        $actions = parent::actions();

        return $actions;
    }

    /**
     * Returns the list of fields that should be returned by default by [[toArray()]] when no specific fields are specified.
     * The default implementation returns the names of the columns whose values have been populated into this record.
     *
     * @return array the list of field names or field definitions.
     */
    public function fields()
    {
        $fields = parent::fields();

        return $fields;
    }

    /**
     * Returns the list of additional fields that can be returned by [[toArray()]] in addition to those listed in [[fields()]].
     * The default implementation returns the names of the relations that have been populated into this record.
     *
     * @return array the list of expandable field names or field definitions. Please refer
     * to [[fields()]] on the format of the return value.
     */
    public function extraFields()
    {
        $extraFields = parent::extraFields();

        return $extraFields;
    }

    /**
     * Returns a list of behaviors that this component should behave as.
     *
     * @return array the behavior configurations.
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::className(),
        ];

        return $behaviors;
    }

    /**
     * Checks the privilege of the current user.
     *
     * This method should be overridden to check whether the current user has the privilege
     * to run the specified action against the specified data model.
     * If the user does not have access, a [[ForbiddenHttpException]] should be thrown.
     *
     * @param string $action the ID of the action to be executed
     * @param object $model the model to be accessed. If null, it means no specific model is being accessed.
     * @param array $params additional parameters
     * @throws ForbiddenHttpException if the user does not have access
     */
    public function checkAccess($action, $model = null, $params = [])
    {
        return parent::checkAccess($action, $model, $params);
    }
}
