<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Noticia */
/* @var $form yii\bootstrap\ActiveForm */

$this->registerJsFile(Url::to('@web/js/plugins/tinymce/tinymce.min.js'), ['depends' => [\app\assets\AppAsset::className()]]);
$this->registerJsFile(Url::to('@web/js/noticia.js'), ['depends' => [\app\assets\AppAsset::className()]]);
$this->registerCss('
.select2-container--default .select2-selection--multiple,
.select2-container--default.select2-container--focus .select2-selection--multiple,
.select2-container--default .select2-selection--single {
    border: 1px solid #777 !important;
}
');
?>

<div class="noticia-form">

    <?php $form = ActiveForm::begin([
        'id' => 'formNoticia',
        'layout' => 'horizontal',
        'options' => [
            'enctype' => 'multipart/form-data',
        ],
        'fieldConfig' => [
            'horizontalCssClasses' => [
                'label' => 'col-md-2',
                'wrapper' => 'col-md-10',
            ],
        ],
    ]); ?>

    <?= $form->errorSummary($model, ['class' => 'alert alert-danger']) ?>

    <?= $form->field($model, 'titulo')->textInput(['maxlength' => 100]) ?>

    <?= $form->field($model, 'portada')->input('file', ['accept' => 'image/*']) ?>

    <?= $form->field($model, 'resumen')->textarea(['rows' => 3]) ?>

    <?= $form->field($model, 'contenido')->textarea(['rows' => 6, 'class' => 'form-control tinymce']) ?>

    <?php
    if (!$model->isNewRecord) {
        echo $form->field($model, 'activo')->dropDownList([0=>'No', 1=>'Si'], ['prompt' => 'Todos']);
    } ?>

    <?php
    echo $form->field($model, 'etiquetas', [
        'inputOptions' => [
            'class' => 'select2 form-control',
            'style' => 'width: 100%',
            'multiple' => true,
        ]])->dropDownList($etiquetas, ['prompt' => 'Todos']);
    ?>

    <div class="form-group">
        <div class="col-md-12 text-center">
            <?= Html::submitButton('<span class="glyphicon glyphicon-floppy-save"></span> '.($model->isNewRecord ? 'Guardar' : 'Actualizar'), ['class' => 'btn btn-danger']) ?> &nbsp;
            <?= Html::a('<span class="glyphicon glyphicon-arrow-left"></span> Cancelar', ['index'], ['class' => 'btn btn-default']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
