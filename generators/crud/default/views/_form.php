<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

/* @var $model \yii\db\ActiveRecord */
$model = new $generator->modelClass();
$safeAttributes = $model->safeAttributes();
if (empty($safeAttributes)) {
    $safeAttributes = $model->attributes();
}

echo "<?php\n";
?>

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model <?= ltrim($generator->modelClass, '\\') ?> */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-form">

    <?= "<?php " ?>$form = ActiveForm::begin(['id' => 'form<?= (StringHelper::basename($generator->modelClass)) ?>']); ?>
    
    <?= "<?= " ?>$form->errorSummary($model, ['class' => 'alert alert-danger']) ?>

<?php foreach ($generator->getColumnNames() as $attribute) {
    if (in_array($attribute, $safeAttributes)) {
        echo "    <?= " . $generator->generateActiveField($attribute) . " ?>\n\n";
    }
} ?>
    <div class="form-group">
        <?= "<?= " ?>Html::submitButton('<span class="glyphicon glyphicon-floppy-save"></span> '.($model->isNewRecord ? 'Guardar' : 'Actualizar'), ['class' => 'btn ' . ($model->isNewRecord ? 'btn-success' : 'btn-primary')]) ?> &nbsp; 
        <?= "<?= " ?>Html::a('<span class="glyphicon glyphicon-arrow-left"></span> Cancelar', ['index'], ['class' => 'btn btn-danger']) ?>
    </div>

    <?= "<?php " ?>ActiveForm::end(); ?>

</div>
